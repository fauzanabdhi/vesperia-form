<?php

namespace App\Actions;

use App\Models\FieldOption;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;
use Illuminate\Support\Facades\DB;
use JsonException;
use RuntimeException;

class ImportFormFeed
{
    public function handle(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("Form feed cannot be read: {$path}");
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read form feed: {$path}");
        }

        try {
            $feed = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException(
                "Form feed contains invalid JSON: {$exception->getMessage()}",
                previous: $exception,
            );
        }

        if (! is_array($feed) || ! array_is_list($feed) || $feed === []) {
            throw new RuntimeException('Form feed must be a non-empty JSON array of sections.');
        }

        return DB::transaction(function () use ($feed, $contents): array {
            $form = Form::firstOrNew([
                'external_id' => 'operational-risk-report',
            ]);

            $form->forceFill([
                'name' => 'Operational Risk Report',
                'definition_json' => $feed,
                'source_checksum' => hash('sha256', $contents),
                'imported_at' => now(),
            ])->save();

            $sectionExternalIds = [];
            $sectionCount = 0;
            $fieldCount = 0;
            $optionCount = 0;

            foreach ($feed as $sectionPosition => $sectionData) {
                $sectionExternalId = $this->requiredString($sectionData, 'id');
                $sectionExternalIds[] = $sectionExternalId;

                $section = FormSection::firstOrNew([
                    'external_id' => $sectionExternalId,
                ]);

                $section->forceFill([
                    'form_id' => $form->id,
                    'name' => $this->requiredString($sectionData, 'name'),
                    'position' => $sectionPosition,
                    'definition_json' => $sectionData,
                ])->save();

                $sectionCount++;

                $payloads = $sectionData['payloads'] ?? [];

                if (! is_array($payloads)) {
                    throw new RuntimeException(
                        "Section {$sectionExternalId} has an invalid payloads value."
                    );
                }

                $fieldExternalIds = [];

                foreach ($payloads as $fieldPosition => $fieldData) {
                    $fieldExternalId = $this->requiredString($fieldData, 'id');
                    $fieldExternalIds[] = $fieldExternalId;

                    $field = FormField::firstOrNew([
                        'external_id' => $fieldExternalId,
                    ]);

                    $field->forceFill([
                        'section_id' => $section->id,
                        'label' => $this->requiredString($fieldData, 'label'),
                        'type' => $this->requiredString($fieldData, 'type'),
                        'sub_type' => $this->nullableString($fieldData, 'sub_type'),
                        'description' => $this->nullableString($fieldData, 'description'),
                        'is_orm_only' => ($fieldData['orm_only'] ?? 'no') === 'yes',
                        'position' => $fieldPosition,
                        'definition_json' => $fieldData,
                    ])->save();

                    $fieldCount++;

                    $options = $fieldData['options'] ?? [];

                    if (! is_array($options)) {
                        throw new RuntimeException(
                            "Field {$fieldExternalId} has an invalid options value."
                        );
                    }

                    $optionExternalIds = [];

                    foreach ($options as $optionPosition => $optionData) {
                        $optionExternalId = $this->requiredString($optionData, 'id');
                        $optionExternalIds[] = $optionExternalId;

                        $option = FieldOption::firstOrNew([
                            'external_id' => $optionExternalId,
                        ]);

                        $option->forceFill([
                            'field_id' => $field->id,
                            'label' => $this->requiredString($optionData, 'label'),
                            'value' => $this->nullableString($optionData, 'value'),
                            'position' => $optionPosition,
                            'definition_json' => $optionData,
                        ])->save();

                        $optionCount++;
                    }

                    $optionQuery = FieldOption::query()
                        ->where('field_id', $field->id);

                    $optionExternalIds === []
                        ? $optionQuery->delete()
                        : $optionQuery->whereNotIn('external_id', $optionExternalIds)->delete();
                }

                $fieldQuery = FormField::query()
                    ->where('section_id', $section->id);

                $fieldExternalIds === []
                    ? $fieldQuery->delete()
                    : $fieldQuery->whereNotIn('external_id', $fieldExternalIds)->delete();
            }

            $sectionQuery = FormSection::query()
                ->where('form_id', $form->id);

            $sectionQuery
                ->whereNotIn('external_id', $sectionExternalIds)
                ->delete();

            return [
                'form' => $form,
                'sections' => $sectionCount,
                'fields' => $fieldCount,
                'options' => $optionCount,
            ];
        });
    }

    private function requiredString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || trim($value) === '') {
            throw new RuntimeException("Required string key [{$key}] is missing or invalid.");
        }

        return $value;
    }

    private function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }
}
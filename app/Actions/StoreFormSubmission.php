<?php

namespace App\Actions;

use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StoreFormSubmission
{
    public function handle(Form $form, array $answers): FormSubmission
    {
        $fields = FormField::query()
            ->whereHas('section', function ($query) use ($form): void {
                $query->where('form_id', $form->id);
            })
            ->with('options')
            ->get()
            ->keyBy('external_id');

        $errors = [];

        foreach ($answers as $fieldExternalId => $answer) {
            $field = $fields->get($fieldExternalId);

            if (! $field) {
                $errors["answers.{$fieldExternalId}"][] =
                    'The selected field does not belong to this form.';

                continue;
            }

            match ($field->type) {
                'radio_button' => $this->validateRadio($field, $answer, $errors),
                'checkbox' => $this->validateCheckbox($field, $answer, $errors),
                'text', 'long_text' => $this->validateText($field, $answer, $errors),
                default => $errors["answers.{$fieldExternalId}"][] =
                    "Unsupported field type [{$field->type}].",
            };
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return FormSubmission::create([
            'form_id' => $form->id,
            'payload' => [
                'answers' => $answers,
            ],
            'submitted_at' => now(),
        ]);
    }

    private function validateRadio(
        FormField $field,
        mixed $answer,
        array &$errors,
    ): void {
        $key = "answers.{$field->external_id}";

        if (! is_string($answer)) {
            $errors[$key][] = 'A radio-button answer must be one option ID.';

            return;
        }

        if (! $field->options->contains('external_id', $answer)) {
            $errors[$key][] = 'The selected option does not belong to this field.';
        }
    }

    private function validateCheckbox(
        FormField $field,
        mixed $answer,
        array &$errors,
    ): void {
        $key = "answers.{$field->external_id}";

        if (! is_array($answer)) {
            $errors[$key][] = 'A checkbox answer must be an array of option IDs.';

            return;
        }

        foreach ($answer as $optionExternalId) {
            if (
                ! is_string($optionExternalId)
                || ! $field->options->contains('external_id', $optionExternalId)
            ) {
                $errors[$key][] =
                    'One or more selected options do not belong to this field.';

                return;
            }
        }
    }

    private function validateText(
        FormField $field,
        mixed $answer,
        array &$errors,
    ): void {
        $key = "answers.{$field->external_id}";

        if (! is_string($answer)) {
            $errors[$key][] = 'The answer must be text.';

            return;
        }

        $rules = match ($field->sub_type) {
            'date' => ['date_format:Y-m-d'],
            'amount' => ['numeric'],
            default => [],
        };

        if ($rules === []) {
            return;
        }

        $validator = Validator::make(
            ['value' => $answer],
            ['value' => $rules],
        );

        if ($validator->fails()) {
            $errors[$key][] = match ($field->sub_type) {
                'date' => 'The answer must use the YYYY-MM-DD date format.',
                'amount' => 'The answer must be a numeric amount.',
                default => 'The answer is invalid.',
            };
        }
    }
}

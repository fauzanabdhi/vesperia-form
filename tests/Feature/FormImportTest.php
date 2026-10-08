<?php

namespace Tests\Feature;

use App\Actions\ImportFormFeed;
use App\Models\FieldOption;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_is_imported_without_duplicates_when_reimported(): void
    {
        $importer = app(ImportFormFeed::class);

        $importer->handle(base_path('submission.json'));

        $firstCounts = [
            'forms' => Form::count(),
            'sections' => FormSection::count(),
            'fields' => FormField::count(),
            'options' => FieldOption::count(),
        ];

        $importer->handle(base_path('submission.json'));

        $this->assertSame([
            'forms' => 1,
            'sections' => 2,
            'fields' => 11,
            'options' => 40,
        ], $firstCounts);

        $this->assertSame($firstCounts['forms'], Form::count());
        $this->assertSame($firstCounts['sections'], FormSection::count());
        $this->assertSame($firstCounts['fields'], FormField::count());
        $this->assertSame($firstCounts['options'], FieldOption::count());
    }
}

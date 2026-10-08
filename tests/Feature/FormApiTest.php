<?php

namespace Tests\Feature;

use App\Actions\ImportFormFeed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_schema_is_available_through_the_api(): void
    {
        app(ImportFormFeed::class)->handle(base_path('submission.json'));

        $response = $this->getJson(
            '/api/v1/forms/operational-risk-report'
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.id', 'operational-risk-report')
            ->assertJsonPath('data.name', 'Operational Risk Report')
            ->assertJsonCount(2, 'data.sections')
            ->assertJsonPath(
                'data.sections.0.id',
                '1609227754-u4t8-cck1-w18p7azbr'
            )
            ->assertJsonPath(
                'data.sections.0.fields.0.label',
                'Bulan Pelaporan'
            );
    }

    public function test_unknown_form_returns_not_found(): void
    {
        $this->getJson('/api/v1/forms/unknown-form')
            ->assertNotFound();
    }
}

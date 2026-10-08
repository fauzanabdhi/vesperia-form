<?php

namespace Tests\Feature;

use App\Actions\ImportFormFeed;
use App\Models\FormSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ImportFormFeed::class)->handle(base_path('submission.json'));
    }

    public function test_valid_submission_is_stored(): void
    {
        $response = $this->postJson(
            '/api/v1/forms/operational-risk-report/submissions',
            [
                'answers' => [
                    '1617779234-f0oy-phln-ppl0u1qx5' => '1617779275-lt0k-zexz-uol8cts7s',
                    '1617779372-kgyo-zi4q-a3cxrkbox' => '2019-01-11',
                    '1617779535-70rw-phgu-z775zzpti' => [
                        '1617779587-p21t-cv59-eoh4ses9b',
                        '1617779627-v64i-uu1g-oo8n3j93v',
                    ],
                ],
            ],
        );

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'submitted_at'],
            ]);

        $this->assertSame(1, FormSubmission::count());
    }

    public function test_option_from_another_field_is_rejected(): void
    {
        $response = $this->postJson(
            '/api/v1/forms/operational-risk-report/submissions',
            [
                'answers' => [
                    // This option belongs to the checkbox field, not the month radio field.
                    '1617779234-f0oy-phln-ppl0u1qx5' => '1617779587-p21t-cv59-eoh4ses9b',
                ],
            ],
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'answers.1617779234-f0oy-phln-ppl0u1qx5',
            ]);

        $this->assertSame(0, FormSubmission::count());
    }

    public function test_invalid_date_is_rejected(): void
    {
        $response = $this->postJson(
            '/api/v1/forms/operational-risk-report/submissions',
            [
                'answers' => [
                    '1617779372-kgyo-zi4q-a3cxrkbox' => 'not-a-date',
                ],
            ],
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'answers.1617779372-kgyo-zi4q-a3cxrkbox',
            ]);
    }
}

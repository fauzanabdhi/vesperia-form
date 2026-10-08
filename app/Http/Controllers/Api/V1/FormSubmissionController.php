<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\StoreFormSubmission;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormSubmissionRequest;
use App\Models\Form;
use Illuminate\Http\JsonResponse;

class FormSubmissionController extends Controller
{
    public function store(
        StoreFormSubmissionRequest $request,
        Form $form,
        StoreFormSubmission $submissionStore,
    ): JsonResponse {
        $submission = $submissionStore->handle(
            $form,
            $request->validated('answers'),
        );

        return response()->json([
            'data' => [
                'id' => $submission->id,
                'submitted_at' => $submission->submitted_at->toISOString(),
            ],
        ], 201);
    }
}

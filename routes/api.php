<?php

use App\Http\Controllers\Api\V1\FormController;
use App\Http\Controllers\Api\V1\FormSubmissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/forms/{form:external_id}', [FormController::class, 'show']);

    Route::post(
        '/forms/{form:external_id}/submissions',
        [FormSubmissionController::class, 'store'],
    );
});
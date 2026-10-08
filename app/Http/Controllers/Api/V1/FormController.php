<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FormResource;
use App\Models\Form;

class FormController extends Controller
{
    public function show(Form $form): FormResource
    {
        $form->load([
            'sections' => fn ($query) => $query->orderBy('position'),
            'sections.fields' => fn ($query) => $query->orderBy('position'),
            'sections.fields.options' => fn ($query) => $query->orderBy('position'),
        ]);

        return new FormResource($form);
    }
}
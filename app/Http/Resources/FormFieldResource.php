<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->external_id,
            'label' => $this->label,
            'type' => $this->type,
            'sub_type' => $this->sub_type,
            'description' => $this->description,
            'options' => FieldOptionResource::collection(
                $this->whenLoaded('options')
            ),
        ];
    }
}
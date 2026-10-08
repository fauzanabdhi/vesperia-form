<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
            'is_orm_only' => 'boolean',
        ];
    }
}

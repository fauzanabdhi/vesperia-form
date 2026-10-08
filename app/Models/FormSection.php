<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSection extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldOption extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
        ];
    }
}

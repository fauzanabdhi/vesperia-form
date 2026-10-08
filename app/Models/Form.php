<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
            'imported_at' => 'datetime',
        ];
    }
}

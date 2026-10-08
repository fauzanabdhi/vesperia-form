<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldOption extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
        ];
    }

    protected $fillable = [
        'field_id',
        'external_id',
        'label',
        'value',
        'position',
        'definition_json',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }
}

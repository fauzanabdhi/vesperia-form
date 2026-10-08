<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormField extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
            'is_orm_only' => 'boolean',
        ];
    }

    protected $fillable = [
    'section_id',
    'external_id',
    'label',
    'type',
    'sub_type',
    'description',
    'is_orm_only',
    'position',
    'definition_json',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'section_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(FieldOption::class, 'field_id');
    }
}

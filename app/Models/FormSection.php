<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
        ];
    }

    protected $fillable = [
    'form_id',
    'external_id',
    'name',
    'position',
    'definition_json',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class, 'section_id');
    }
}

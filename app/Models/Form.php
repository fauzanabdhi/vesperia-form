<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected function casts(): array
    {
        return [
            'definition_json' => 'array',
            'imported_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'external_id',
        'name',
        'definition_json',
        'source_checksum',
        'imported_at',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(FormSection::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}

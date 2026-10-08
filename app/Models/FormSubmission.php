<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    protected $fillable = [
        'form_id',
        'payload',
        'submitted_at',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}

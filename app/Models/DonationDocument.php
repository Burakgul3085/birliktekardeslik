<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationDocument extends Model
{
    protected $fillable = [
        'donation_id',
        'document_template_id',
        'type',
        'verification_code',
        'pdf_path',
        'generated_at',
        'meta',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'meta' => 'array',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return DocumentTemplate::TYPES[$this->type] ?? $this->type;
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('crm.document.verify', $this->verification_code);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentFieldOverride extends Model
{
    protected $fillable = [
        'donation_document_id',
        'field_key',
        'x',
        'y',
        'width',
        'height',
        'font_family',
        'font_size',
        'color',
        'align',
        'vertical_align',
        'text_override',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(DonationDocument::class, 'donation_document_id');
    }

    /**
     * @return array<string, mixed>
     */
    public function toRenderPatch(): array
    {
        $patch = [];

        foreach (['x', 'y', 'width', 'height', 'font_family', 'font_size', 'color', 'align', 'vertical_align'] as $key) {
            if ($this->{$key} !== null) {
                $patch[$key] = $this->{$key};
            }
        }

        return $patch;
    }
}

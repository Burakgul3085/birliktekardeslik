<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivitySectionSetting extends Model
{
    protected $fillable = [
        'badge_text',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->where('is_active', true)->latest('id')->first() ?? new self([
            'badge_text' => 'Birlikte Kardeşlik Derneği',
            'title' => 'Faaliyetlerimiz',
            'description' => 'Afrika’da açlık ve susuzlukla mücadele için yürüttüğümüz gıda, temiz su ve acil yardım faaliyetleri.',
            'is_active' => true,
        ]);
    }
}

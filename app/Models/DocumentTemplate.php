<?php

namespace App\Models;

use App\Support\Crm\TemplateEngine\TemplateFieldDefaults;
use App\Support\Crm\TemplateEngine\TemplateFieldSynchronizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    public const TYPE_RECEIPT = 'receipt';

    public const TYPE_DONATION_POSTER = 'donation_poster';

    public const TYPE_THANKS_POSTER = 'thanks_poster';

    /** @deprecated Artık üretilmiyor */
    public const TYPE_THANKS_LETTER = 'thanks_letter';

    /** @deprecated Artık üretilmiyor */
    public const TYPE_CERTIFICATE = 'certificate';

    public const ACTIVE_TYPES = [
        self::TYPE_RECEIPT => 'Makbuz',
    ];

    public const TYPES = [
        ...self::ACTIVE_TYPES,
        self::TYPE_DONATION_POSTER => 'Bağış Afişi',
        self::TYPE_THANKS_POSTER => 'Teşekkür Afişi',
        self::TYPE_THANKS_LETTER => 'Teşekkür Belgesi',
        self::TYPE_CERTIFICATE => 'Sertifika',
    ];

    public const GENERATABLE_TYPES = [
        self::TYPE_RECEIPT,
    ];

    protected $fillable = [
        'name',
        'type',
        'blade_view',
        'background_image',
        'canvas_width',
        'canvas_height',
        'settings',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(DonationDocument::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(DocumentTemplateField::class)->orderBy('sort_order');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function usesTemplateEngine(): bool
    {
        return in_array($this->type, [self::TYPE_DONATION_POSTER, self::TYPE_THANKS_POSTER], true)
            && filled($this->background_image);
    }

    /** @deprecated */
    public function usesImageEngine(): bool
    {
        return $this->usesTemplateEngine();
    }

    public function usesOverlay(): bool
    {
        return filled($this->background_image);
    }

    public function requiresBackground(): bool
    {
        return in_array($this->type, [self::TYPE_DONATION_POSTER, self::TYPE_THANKS_POSTER], true);
    }

    public function resolvedOrientation(): string
    {
        return match ($this->type) {
            self::TYPE_DONATION_POSTER, self::TYPE_THANKS_POSTER => 'portrait',
            default => $this->settings['orientation'] ?? 'portrait',
        };
    }

    /**
     * @return array{width: int, height: int}
     */
    public function canvasSize(): array
    {
        if ($this->canvas_width && $this->canvas_height) {
            return [
                'width' => (int) $this->canvas_width,
                'height' => (int) $this->canvas_height,
            ];
        }

        if ($this->background_image) {
            $path = storage_path('app/public/' . $this->background_image);
            if (is_file($path)) {
                $size = getimagesize($path);
                if ($size !== false) {
                    return ['width' => $size[0], 'height' => $size[1]];
                }
            }
        }

        return ['width' => 2480, 'height' => 3508];
    }

    public function syncCanvasDimensions(): void
    {
        if (! $this->background_image) {
            return;
        }

        $path = storage_path('app/public/' . $this->background_image);

        if (! is_file($path)) {
            return;
        }

        $size = getimagesize($path);

        if ($size === false) {
            return;
        }

        $this->canvas_width = $size[0];
        $this->canvas_height = $size[1];
    }

    /** @deprecated */
    public function syncCanvasFromBackground(bool $forceResetFields = false): void
    {
        $this->syncCanvasDimensions();
        $this->saveQuietly();

        app(TemplateFieldSynchronizer::class)->ensureFields($this, $forceResetFields);
    }

    /** @deprecated */
    public function resolvedTemplateFields(): array
    {
        return $this->fields->map(fn (DocumentTemplateField $field) => $field->toRenderDefinition())->all();
    }
}

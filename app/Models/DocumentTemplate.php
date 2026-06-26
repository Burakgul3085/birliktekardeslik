<?php

namespace App\Models;

use App\Support\Crm\TemplateEngine\TemplateFieldDefaults;
use App\Support\Crm\TemplateEngine\TemplateFieldNormalizer;
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
        self::TYPE_DONATION_POSTER => 'Bağış Afişi',
        self::TYPE_THANKS_POSTER => 'Teşekkür Afişi',
    ];

    public const TYPES = [
        ...self::ACTIVE_TYPES,
        self::TYPE_THANKS_LETTER => 'Teşekkür Belgesi',
        self::TYPE_CERTIFICATE => 'Sertifika',
    ];

    public const GENERATABLE_TYPES = [
        self::TYPE_RECEIPT,
        self::TYPE_DONATION_POSTER,
        self::TYPE_THANKS_POSTER,
    ];

    protected $fillable = [
        'name',
        'type',
        'blade_view',
        'background_image',
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

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
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

    public function usesImageEngine(): bool
    {
        return in_array($this->type, [self::TYPE_DONATION_POSTER, self::TYPE_THANKS_POSTER], true)
            && filled($this->background_image);
    }

    /**
     * @return array{width: int, height: int}
     */
    public function canvasSize(): array
    {
        $canvas = $this->settings['canvas'] ?? null;

        if (is_array($canvas) && ! empty($canvas['width']) && ! empty($canvas['height'])) {
            return [
                'width' => (int) $canvas['width'],
                'height' => (int) $canvas['height'],
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

    /**
     * @return array<int, array<string, mixed>>
     */
    public function resolvedTemplateFields(): array
    {
        ['width' => $width, 'height' => $height] = $this->canvasSize();
        $settings = $this->settings ?? [];
        $saved = $settings['fields'] ?? [];
        $version = (int) ($settings['fields_version'] ?? 0);

        if (empty($saved) || $version < TemplateFieldDefaults::FIELDS_VERSION) {
            return TemplateFieldDefaults::forType($this->type, $width, $height);
        }

        return TemplateFieldNormalizer::normalizeAll(
            array_map(
                fn (array $field): array => TemplateFieldDefaults::applyCanvasSize($field, $width, $height),
                $saved,
            ),
        );
    }

    public function syncCanvasFromBackground(bool $forceResetFields = false): void
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

        $settings = $this->settings ?? [];
        $settings['canvas'] = ['width' => $size[0], 'height' => $size[1]];

        $shouldInitFields = $forceResetFields
            || empty($settings['fields'])
            || (int) ($settings['fields_version'] ?? 0) < TemplateFieldDefaults::FIELDS_VERSION;

        if ($shouldInitFields && in_array($this->type, [self::TYPE_DONATION_POSTER, self::TYPE_THANKS_POSTER], true)) {
            $settings['fields'] = TemplateFieldDefaults::forType($this->type, $size[0], $size[1]);
            $settings['fields_version'] = TemplateFieldDefaults::FIELDS_VERSION;
        }

        $this->settings = $settings;
    }
}

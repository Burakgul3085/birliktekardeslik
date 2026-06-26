<?php

namespace App\Support\Crm\TemplateEngine;

class FontRegistry
{
    public const FAMILIES = [
        'DejaVuSans' => 'DejaVuSans.ttf',
        'DejaVuSans-Bold' => 'DejaVuSans-Bold.ttf',
        'DejaVuSerif' => 'DejaVuSerif.ttf',
        'DejaVuSerif-Bold' => 'DejaVuSerif-Bold.ttf',
    ];

    public static function path(string $family): string
    {
        $file = self::FAMILIES[$family] ?? self::FAMILIES['DejaVuSans'];

        return resource_path('fonts/' . $file);
    }

    public static function options(): array
    {
        return [
            'DejaVuSans' => 'DejaVu Sans',
            'DejaVuSans-Bold' => 'DejaVu Sans Kalın',
            'DejaVuSerif' => 'DejaVu Serif',
            'DejaVuSerif-Bold' => 'DejaVu Serif Kalın',
        ];
    }
}

<?php

namespace App\Support\Crm;

class PosterLayout
{
    public static function donationNameFontSize(string $name): float
    {
        $length = mb_strlen($name);

        return match (true) {
            $length > 40 => 17.0,
            $length > 30 => 19.0,
            $length > 22 => 21.0,
            default => 24.0,
        };
    }

    public static function donationDescriptionFontSize(string $text): float
    {
        $length = mb_strlen($text);

        return match (true) {
            $length > 240 => 7.5,
            $length > 180 => 8.0,
            $length > 130 => 8.5,
            $length > 90 => 9.0,
            $length > 55 => 9.5,
            default => 10.0,
        };
    }

    public static function thanksBodyFontSize(string $text): float
    {
        $length = mb_strlen($text);

        return match (true) {
            $length > 200 => 9.0,
            $length > 150 => 9.5,
            default => 10.0,
        };
    }
}

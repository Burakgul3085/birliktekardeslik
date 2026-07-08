<?php

namespace App\Support;

class TestimonialDisplayName
{
    public static function make(string $name, bool $anonymous = false): string
    {
        $normalized = trim(preg_replace('/\s+/u', ' ', $name) ?? '');

        if ($normalized === '') {
            return '';
        }

        if (! $anonymous) {
            return $normalized;
        }

        $parts = preg_split('/\s+/u', $normalized) ?: [];

        if (count($parts) === 1) {
            return $parts[0];
        }

        $first = $parts[0];
        $last = $parts[count($parts) - 1];
        $initial = mb_strtoupper(mb_substr($last, 0, 1));

        return trim($first.' '.$initial.'.');
    }
}

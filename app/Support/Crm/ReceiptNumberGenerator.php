<?php

namespace App\Support\Crm;

use App\Models\Donation;

class ReceiptNumberGenerator
{
    public static function next(): string
    {
        $year = now()->format('Y');
        $prefix = 'MKB-' . $year . '-';

        $lastNumber = Donation::query()
            ->where('receipt_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('receipt_number');

        $sequence = 1;

        if (is_string($lastNumber) && preg_match('/-(\d+)$/', $lastNumber, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }
}

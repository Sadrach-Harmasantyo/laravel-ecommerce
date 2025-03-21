<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format a number as Indonesian Rupiah
     *
     * @param float $amount
     * @return string
     */
    public static function formatRupiah($amount)
    {
        return 'Rp' . number_format($amount, 0, ',', '.');
    }
}
<?php

namespace App\Helpers;

class OrderHelper
{
    /**
     * Terjemahkan status pesanan ke Bahasa Indonesia
     */
    public static function translateOrderStatus(string $status): string
    {
        return match($status) {
            'new' => 'Baru',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    }

    /**
     * Terjemahkan status pembayaran ke Bahasa Indonesia
     */
    public static function translatePaymentStatus(string $status): string
    {
        return match($status) {
            'pending' => 'Menunggu',
            'paid' => 'Dibayar',
            'failed' => 'Gagal',
            'refunded' => 'Dikembalikan',
            default => ucfirst($status),
        };
    }
}
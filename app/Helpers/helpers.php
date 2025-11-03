<?php

use App\Models\SystemSetting;

if (!function_exists('setting')) {
    /**
     * Get system setting value
     */
    function setting(string $key, $default = null)
    {
        $setting = SystemSetting::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'number' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Rupiah currency
     */
    function format_rupiah($amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('day_in_indonesian')) {
    /**
     * Get Indonesian day name
     */
    function day_in_indonesian(string $day): string
    {
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        return $days[strtolower($day)] ?? $day;
    }
}

if (!function_exists('generate_invoice_number')) {
    /**
     * Generate unique invoice number
     */
    function generate_invoice_number(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
        return "INV-{$date}-{$random}";
    }
}

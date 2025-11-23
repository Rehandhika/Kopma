<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use firstOrCreate to avoid duplicate entries
        StoreSetting::firstOrCreate(
            ['id' => 1], // Only one store settings record should exist
            [
                'is_open' => false,
                'status_reason' => 'Tidak ada pengurus yang bertugas',
                'auto_status' => true,
                'manual_mode' => false,
                'manual_is_open' => false,
                'manual_open_override' => false,
                'operating_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
                    'tuesday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
                    'wednesday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
                    'thursday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
                    'friday' => ['open' => null, 'close' => null, 'is_open' => false],
                    'saturday' => ['open' => null, 'close' => null, 'is_open' => false],
                    'sunday' => ['open' => null, 'close' => null, 'is_open' => false],
                ],
                'contact_phone' => null,
                'contact_email' => null,
                'contact_address' => null,
                'contact_whatsapp' => null,
                'about_text' => null,
            ]
        );
    }
}

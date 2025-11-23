<?php

namespace App\Livewire\Public;

use App\Models\StoreSetting;
use Livewire\Component;

class About extends Component
{
    public $storeSetting;
    public $operatingDays = [];

    public function mount()
    {
        // Load store settings
        $this->storeSetting = StoreSetting::first();
        
        // If no settings exist, create default
        if (!$this->storeSetting) {
            $this->storeSetting = new StoreSetting([
                'contact_phone' => '-',
                'contact_email' => '-',
                'contact_whatsapp' => '-',
                'contact_address' => '-',
                'about_text' => 'Informasi tentang koperasi akan segera tersedia.',
                'operating_hours' => $this->getDefaultOperatingHours(),
            ]);
        }
        
        // Format operating hours for display
        $this->formatOperatingHours();
    }

    protected function getDefaultOperatingHours()
    {
        return [
            'monday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
            'tuesday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
            'wednesday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
            'thursday' => ['open' => '08:00', 'close' => '16:00', 'is_open' => true],
            'friday' => ['open' => null, 'close' => null, 'is_open' => false],
            'saturday' => ['open' => null, 'close' => null, 'is_open' => false],
            'sunday' => ['open' => null, 'close' => null, 'is_open' => false],
        ];
    }

    protected function formatOperatingHours()
    {
        $dayNames = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        $operatingHours = $this->storeSetting->operating_hours ?? $this->getDefaultOperatingHours();

        foreach ($dayNames as $key => $name) {
            $hours = $operatingHours[$key] ?? ['is_open' => false];
            
            $this->operatingDays[] = [
                'name' => $name,
                'is_open' => $hours['is_open'] ?? false,
                'open' => $hours['open'] ?? null,
                'close' => $hours['close'] ?? null,
            ];
        }
    }

    public function render()
    {
        return view('livewire.public.about')
            ->layout('layouts.public');
    }
}

<div class="space-y-6">
    <x-layout.page-header 
        title="Pengaturan Sistem"
        description="Kelola pengaturan waktu dan mode maintenance"
    />

    {{-- Custom DateTime Warning Banner --}}
    @if($systemInfo['custom_datetime_active'])
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-amber-800">Mode Waktu Custom Aktif</p>
                        <p class="text-sm text-amber-700">Waktu sistem: <strong>{{ $systemInfo['server_time'] }}</strong> (Nyata: {{ $systemInfo['real_time'] }})</p>
                    </div>
                </div>
                <button wire:click="resetToRealTime" class="px-3 py-1.5 text-sm font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-md">
                    Reset
                </button>
            </div>
        </div>
    @endif

    {{-- System Info --}}
    <x-ui.card>
        <div class="p-4 bg-gray-50 rounded-lg">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div><span class="text-gray-500">PHP:</span> <span class="font-medium">{{ $systemInfo['php_version'] }}</span></div>
                <div><span class="text-gray-500">Laravel:</span> <span class="font-medium">{{ $systemInfo['laravel_version'] }}</span></div>
                <div><span class="text-gray-500">Zona Waktu:</span> <span class="font-medium">{{ $systemInfo['timezone'] }}</span></div>
                <div>
                    <span class="text-gray-500">Waktu:</span> 
                    <span class="font-medium {{ $systemInfo['custom_datetime_active'] ? 'text-amber-600' : '' }}">{{ $systemInfo['server_time'] }}</span>
                </div>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card>
        <form wire:submit="save" class="space-y-6">
            {{-- Maintenance Mode --}}
            <x-layout.form-section 
                title="Mode Maintenance"
                description="Aktifkan untuk mencegah akses pengguna saat pemeliharaan"
            >
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $maintenance_mode ? 'bg-red-100' : 'bg-gray-200' }} flex items-center justify-center">
                            <svg class="w-5 h-5 {{ $maintenance_mode ? 'text-red-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium {{ $maintenance_mode ? 'text-red-700' : 'text-gray-700' }}">
                                {{ $maintenance_mode ? 'Maintenance Aktif' : 'Sistem Normal' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $maintenance_mode ? 'Pengguna tidak dapat mengakses sistem' : 'Sistem dapat diakses normal' }}
                            </p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="maintenance_mode" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                    </label>
                </div>
            </x-layout.form-section>

            {{-- Custom DateTime --}}
            <x-layout.form-section 
                title="Waktu Custom"
                description="Atur waktu sistem manual untuk audit/development"
            >
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Aktifkan Waktu Custom</p>
                            <p class="text-xs text-gray-500">Mempengaruhi absensi, laporan, dan status toko</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="use_custom_datetime" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>

                    @if($use_custom_datetime)
                        <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                    <input type="date" wire:model.live="custom_date" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
                                    <input type="time" wire:model.live="custom_time" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                            @if($custom_date && $custom_time)
                                <p class="text-sm text-purple-700">
                                    Preview: <span class="font-mono font-medium">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i', $custom_date . ' ' . $custom_time)->format($datetime_format) }}</span>
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </x-layout.form-section>

            {{-- DateTime Settings --}}
            <x-layout.form-section 
                title="Format Waktu & Tanggal"
                description="Konfigurasi tampilan waktu di seluruh sistem"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.select label="Zona Waktu" name="timezone" wire:model.live="timezone" :options="$timezoneOptions" />
                    <x-ui.select label="Bahasa" name="locale" wire:model.live="locale" :options="$localeOptions" />
                    <x-ui.select label="Format Tanggal" name="date_format" wire:model.live="date_format" :options="$dateFormatOptions" />
                    <x-ui.select label="Format Waktu" name="time_format" wire:model.live="time_format" :options="$timeFormatOptions" />
                    <x-ui.select label="Format Tanggal & Waktu" name="datetime_format" wire:model.live="datetime_format" :options="$datetimeFormatOptions" />
                    <x-ui.select label="Hari Pertama Minggu" name="first_day_of_week" wire:model="first_day_of_week" :options="$firstDayOfWeekOptions" />
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div><span class="text-blue-600">Tanggal:</span> <span class="font-medium" wire:poll.5s>{{ now()->setTimezone($timezone)->format($date_format) }}</span></div>
                        <div><span class="text-blue-600">Waktu:</span> <span class="font-medium" wire:poll.5s>{{ now()->setTimezone($timezone)->format($time_format) }}</span></div>
                        <div><span class="text-blue-600">Lengkap:</span> <span class="font-medium" wire:poll.5s>{{ now()->setTimezone($timezone)->format($datetime_format) }}</span></div>
                    </div>
                </div>
            </x-layout.form-section>

            {{-- Actions --}}
            <div class="flex justify-between pt-4 border-t border-gray-200">
                <button type="button" wire:click="clearCache" wire:confirm="Bersihkan cache?" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Bersihkan Cache
                </button>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </x-ui.card>
</div>

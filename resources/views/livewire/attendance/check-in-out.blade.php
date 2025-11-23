<div class="max-w-2xl mx-auto">
    <x-ui.card title="Absensi Hari Ini">
        @if($currentSchedule)
            {{-- Active Schedule Info --}}
            <div class="mb-6">
                <div class="bg-info-50 border-l-4 border-info-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <x-ui.icon name="clock" class="h-5 w-5 text-info-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-info-800">
                                Jadwal Aktif: {{ $currentSchedule->day_label }}, {{ $currentSchedule->date->format('d M Y') }}
                            </h3>
                            <div class="mt-2 text-sm text-info-700 space-y-1">
                                <p>Sesi: {{ $currentSchedule->session_label }}</p>
                                <p>Waktu: {{ $currentSchedule->time_start->format('H:i') }} - {{ $currentSchedule->time_end->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Check-in/Check-out Section --}}
            <x-layout.grid cols="2" gap="6" class="mb-6">
                {{-- Check-in Card --}}
                <x-ui.card padding="true" shadow="sm">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Check-in</h4>
                    @if($checkInTime)
                        <div class="text-center">
                            <div class="text-2xl font-bold text-success-600">{{ $checkInTime }}</div>
                            <p class="text-sm text-gray-500">Sudah check-in</p>
                        </div>
                    @else
                        <div class="text-center">
                            <x-ui.button 
                                variant="success" 
                                wire:click="checkIn"
                                :loading="$wire->loading"
                            >
                                <x-ui.icon name="check-circle" class="w-5 h-5 mr-2" />
                                <span wire:loading.remove>Check-in</span>
                                <span wire:loading>Memproses...</span>
                            </x-ui.button>
                        </div>
                    @endif
                </x-ui.card>

                {{-- Check-out Card --}}
                <x-ui.card padding="true" shadow="sm">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Check-out</h4>
                    @if($checkOutTime)
                        <div class="text-center">
                            <div class="text-2xl font-bold text-info-600">{{ $checkOutTime }}</div>
                            <p class="text-sm text-gray-500">Sudah check-out</p>
                        </div>
                    @elseif($checkInTime)
                        <div class="text-center">
                            <x-ui.button 
                                variant="info" 
                                wire:click="checkOut"
                                :loading="$wire->loading"
                            >
                                <x-ui.icon name="logout" class="w-5 h-5 mr-2" />
                                <span wire:loading.remove>Check-out</span>
                                <span wire:loading>Memproses...</span>
                            </x-ui.button>
                        </div>
                    @else
                        <div class="text-center text-gray-500">
                            <p class="text-sm">Check-in terlebih dahulu</p>
                        </div>
                    @endif
                </x-ui.card>
            </x-layout.grid>

            {{-- Notes Section --}}
            <x-layout.form-section title="Catatan">
                <x-ui.textarea
                    name="notes"
                    placeholder="Tambahkan catatan jika diperlukan..."
                    rows="3"
                    wire:model="notes"
                />
                <div class="mt-2">
                    <x-ui.button 
                        variant="white" 
                        wire:click="updateNotes"
                        :loading="$wire->loading"
                    >
                        <span wire:loading.remove>Simpan Catatan</span>
                        <span wire:loading>Menyimpan...</span>
                    </x-ui.button>
                </div>
            </x-layout.form-section>

        @else
            <x-layout.empty-state
                icon="calendar"
                title="Tidak ada jadwal aktif"
                description="Saat ini tidak ada jadwal kerja yang aktif untuk Anda."
            />
        @endif
    </x-ui.card>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="mt-4">
            <x-ui.alert variant="success" dismissible="true">
                {{ session('success') }}
            </x-ui.alert>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4">
            <x-ui.alert variant="danger" dismissible="true">
                {{ session('error') }}
            </x-ui.alert>
        </div>
    @endif
</div>

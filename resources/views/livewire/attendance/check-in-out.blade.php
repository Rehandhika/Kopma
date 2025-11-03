<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Absensi Hari Ini
            </h3>

            @if($currentSchedule)
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Jadwal Aktif: {{ $currentSchedule->day_label }}, {{ $currentSchedule->date->format('d M Y') }}
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Sesi: {{ $currentSchedule->session_label }}</p>
                                <p>Waktu: {{ $currentSchedule->time_start->format('H:i') }} - {{ $currentSchedule->time_end->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check-in/Check-out Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Check-in -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Check-in</h4>
                        @if($checkInTime)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $checkInTime }}</div>
                                <p class="text-sm text-gray-500">Sudah check-in</p>
                            </div>
                        @else
                            <div class="text-center">
                                <button
                                    wire:click="checkIn"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    wire:loading.attr="disabled"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span wire:loading.remove>Check-in</span>
                                    <span wire:loading>Memproses...</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Check-out -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Check-out</h4>
                        @if($checkOutTime)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $checkOutTime }}</div>
                                <p class="text-sm text-gray-500">Sudah check-out</p>
                            </div>
                        @elseif($checkInTime)
                            <div class="text-center">
                                <button
                                    wire:click="checkOut"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    wire:loading.attr="disabled"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span wire:loading.remove>Check-out</span>
                                    <span wire:loading>Memproses...</span>
                                </button>
                            </div>
                        @else
                            <div class="text-center text-gray-500">
                                <p class="text-sm">Check-in terlebih dahulu</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Catatan (Opsional)
                    </label>
                    <div class="mt-1">
                        <textarea
                            id="notes"
                            name="notes"
                            rows="3"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            placeholder="Tambahkan catatan jika diperlukan..."
                            wire:model="notes"
                        ></textarea>
                    </div>
                    <div class="mt-2">
                        <button
                            wire:click="updateNotes"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove>Simpan Catatan</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </div>

            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal aktif</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Saat ini tidak ada jadwal kerja yang aktif untuk Anda.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</div>

<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Ketersediaan</h1>
                <p class="text-gray-600 mt-1">Atur jadwal ketersediaan Anda untuk minggu ini</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($status === 'submitted')
                    <div class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-check-circle mr-1"></i>
                        Terkirim
                    </div>
                @elseif($status === 'draft')
                    <div class="bg-yellow-50 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-edit mr-1"></i>
                        Draft
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Week Selection -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar-week mr-2 text-blue-600"></i>
                Pilih Minggu
            </h2>
            @if(!$isCurrentWeek)
                <button 
                    wire:click="$set('selectedWeekOffset', 0)"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors"
                >
                    <i class="fas fa-calendar-day mr-1"></i>
                    Minggu Ini
                </button>
            @endif
        </div>
        
        <div class="flex items-center space-x-4">
            <button 
                wire:click="$set('selectedWeekOffset', $selectedWeekOffset - 1)"
                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                @if($selectedWeekOffset <= -4) disabled @endif
            >
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="flex-1 text-center">
                <select 
                    wire:model.live="selectedWeekOffset"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed"
                    @if(!$canEdit) disabled @endif
                >
                    <option value="0">Minggu Ini</option>
                    <option value="1">Minggu Depan</option>
                    <option value="2">2 Minggu Depan</option>
                    <option value="3">3 Minggu Depan</option>
                    <option value="4">4 Minggu Depan</option>
                </select>
                <p class="text-sm text-gray-600 mt-2">{{ $weekRange }}</p>
            </div>
            
            <button 
                wire:click="$set('selectedWeekOffset', $selectedWeekOffset + 1)"
                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                @if($selectedWeekOffset >= 4) disabled @endif
            >
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-calendar-check text-blue-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Sesi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSessions }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-clock text-green-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Jam</p>
                    <p class="text-2xl font-bold text-green-600">{{ $totalHours }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-calendar-day text-purple-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">Hari Tersedia</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $availableDays }}/7</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-percentage text-yellow-600 text-lg"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-600">Coverage</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ round(($totalSessions / 21) * 100) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if($canEdit)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                Aksi Cepat
            </h3>
            <div class="flex flex-wrap gap-3">
                <button 
                    wire:click="selectAll"
                    type="button"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition-all duration-150 shadow-sm hover:shadow"
                >
                    <i class="fas fa-check-double mr-2"></i>
                    Pilih Semua
                </button>
                <button 
                    wire:click="clearAll"
                    type="button"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 active:bg-red-800 transition-all duration-150 shadow-sm hover:shadow"
                >
                    <i class="fas fa-times-circle mr-2"></i>
                    Hapus Semua
                </button>
                
                @foreach($sessions as $sessionKey => $sessionName)
                    <button 
                        wire:click="setSessionAvailability({{ $sessionKey }}, true)"
                        type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-all duration-150 shadow-sm hover:shadow"
                    >
                        <i class="fas fa-clock mr-2"></i>
                        {{ $sessionName }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

        <!-- Availability Grid -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
            Ketersediaan per Hari & Sesi
        </h3>
        
        <div class="overflow-x-auto -mx-6 px-6">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700 whitespace-nowrap">Hari</th>
                        @foreach($sessions as $sessionKey => $sessionName)
                            <th class="text-center py-3 px-4 font-semibold text-gray-700 whitespace-nowrap">
                                <div class="flex flex-col items-center">
                                    <span class="font-semibold">Sesi {{ $sessionKey }}</span>
                                    <span class="text-xs font-normal text-gray-500 mt-1">
                                        @if($sessionKey == 1) 08:00 - 12:00
                                        @elseif($sessionKey == 2) 13:00 - 17:00
                                        @elseif($sessionKey == 3) 17:00 - 21:00
                                        @endif
                                    </span>
                                </div>
                            </th>
                        @endforeach
                        <th class="text-center py-3 px-4 font-semibold text-gray-700 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($days as $dayKey => $dayName)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-4 font-semibold text-gray-900 whitespace-nowrap">
                                {{ $dayName }}
                            </td>
                            
                            @foreach($sessions as $sessionKey => $sessionName)
                                <td class="py-4 px-4 text-center">
                                    @if($canEdit)
                                        <button 
                                            wire:click="toggleAvailability('{{ $dayKey }}', {{ $sessionKey }})"
                                            type="button"
                                            class="w-6 h-6 rounded-full border-2 transition-colors inline-flex items-center justify-center
                                                {{ isset($availability[$dayKey][$sessionKey]) && $availability[$dayKey][$sessionKey] 
                                                    ? 'bg-green-500 border-green-500 hover:bg-green-600 hover:border-green-600' 
                                                    : 'bg-white border-gray-300 hover:border-gray-400 hover:bg-gray-50' }}"
                                        >
                                            @if(isset($availability[$dayKey][$sessionKey]) && $availability[$dayKey][$sessionKey])
                                                <i class="fas fa-check text-white text-xs"></i>
                                            @endif
                                        </button>
                                    @else
                                        <div class="w-6 h-6 rounded-full border-2 mx-auto inline-flex items-center justify-center
                                            {{ isset($availability[$dayKey][$sessionKey]) && $availability[$dayKey][$sessionKey] 
                                                ? 'bg-green-500 border-green-500' 
                                                : 'bg-gray-100 border-gray-300' }}">
                                            @if(isset($availability[$dayKey][$sessionKey]) && $availability[$dayKey][$sessionKey])
                                                <i class="fas fa-check text-white text-xs"></i>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            
                            <td class="py-4 px-4 text-center">
                                @if($canEdit)
                                    <div class="flex justify-center items-center space-x-2">
                                        <button 
                                            wire:click="setDayAvailability('{{ $dayKey }}', true)"
                                            type="button"
                                            class="text-green-600 hover:text-green-700 transition-colors"
                                            title="Pilih semua sesi"
                                        >
                                            <i class="fas fa-check-circle text-lg"></i>
                                        </button>
                                        <button 
                                            wire:click="setDayAvailability('{{ $dayKey }}', false)"
                                            type="button"
                                            class="text-red-600 hover:text-red-700 transition-colors"
                                            title="Hapus semua sesi"
                                        >
                                            <i class="fas fa-times-circle text-lg"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notes Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
            Catatan Tambahan
        </h3>
        <textarea 
            wire:model="notes"
            placeholder="Tambahkan catatan atau preferensi khusus..."
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed"
            rows="3"
            @if(!$canEdit) disabled @endif
        ></textarea>
        <div class="mt-2 text-right">
            <span class="text-sm text-gray-500">{{ strlen($notes) }}/500 karakter</span>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex-1">
                @if($status === 'submitted')
                    <p class="text-sm font-medium text-green-600 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        Ketersediaan Anda telah dikirim untuk minggu ini
                    </p>
                @elseif($totalSessions === 0)
                    <p class="text-sm font-medium text-yellow-600 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Pilih minimal satu sesi untuk menyimpan ketersediaan
                    </p>
                @endif
            </div>
            
            @if($canEdit)
                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="saveAsDraft"
                        type="button"
                        class="px-5 py-2.5 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 active:bg-gray-800 transition-all duration-150 shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="saveAsDraft">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Draft
                        </span>
                        <span wire:loading wire:target="saveAsDraft">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Menyimpan...
                        </span>
                    </button>
                    
                    <button 
                        wire:click="submitAvailability"
                        type="button"
                        class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-all duration-150 shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        @if($totalSessions === 0) disabled @endif
                    >
                        <span wire:loading.remove wire:target="submitAvailability">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Ketersediaan
                        </span>
                        <span wire:loading wire:target="submitAvailability">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Mengirim...
                        </span>
                    </button>
                </div>
            @else
                <div class="text-sm font-medium text-gray-500 flex items-center bg-gray-50 px-4 py-2 rounded-lg">
                    <i class="fas fa-lock mr-2"></i>
                    Ketersediaan untuk minggu ini tidak dapat diubah
                </div>
            @endif
        </div>
    </div>
</div>

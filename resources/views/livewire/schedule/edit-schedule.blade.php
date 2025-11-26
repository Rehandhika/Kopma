<div class="space-y-6" x-data="{ 
    showDeleteConfirm: false, 
    deleteAction: null,
    showClearConfirm: false,
    clearSlotData: null,
    showRemoveConfirm: false,
    removeData: null
}" 
@confirm-remove.window="removeData = $event.detail; showRemoveConfirm = true">
    <!-- Enhanced Header with Status Badge -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <h2 class="text-3xl font-bold">Edit Jadwal</h2>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium">
                        {{ ucfirst($schedule->status) }}
                    </span>
                </div>
                <p class="text-blue-100 mt-2 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>
                        {{ \Carbon\Carbon::parse($schedule->week_start_date)->locale('id')->isoFormat('D MMMM') }} - 
                        {{ \Carbon\Carbon::parse($schedule->week_start_date)->addDays(3)->locale('id')->isoFormat('D MMMM YYYY') }}
                    </span>
                </p>
            </div>
            <div class="flex items-center space-x-3">
                @if($hasUnsavedChanges)
                    <div class="flex items-center space-x-2 px-4 py-2 bg-yellow-500 rounded-lg shadow-lg animate-pulse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-semibold">{{ $this->getChangeCount() }} perubahan belum disimpan</span>
                    </div>
                @endif
                <button 
                    wire:click="discardChanges" 
                    @if(!$hasUnsavedChanges) disabled @endif
                    class="group px-5 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg font-medium transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed border border-white/20 hover:scale-105 active:scale-95 hover:shadow-lg"
                    x-data
                    x-tooltip="'Batalkan semua perubahan yang belum disimpan'"
                >
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Batal</span>
                    </div>
                </button>
                <button 
                    wire:click="saveChanges" 
                    @if(!$this->canSave()) disabled @endif
                    class="group px-5 py-2.5 bg-white text-blue-600 hover:bg-blue-50 rounded-lg font-semibold transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 active:scale-95 hover:shadow-xl relative overflow-hidden"
                    x-data
                    x-tooltip="'Simpan semua perubahan ke database'"
                >
                    <!-- Ripple effect background -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                    <div class="flex items-center space-x-2 relative z-10">
                        <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Panel -->
    <x-schedule.statistics-panel 
        :statistics="$statistics"
        :show="$showStatistics"
        wire:click="toggleStatistics"
    />

    <!-- Conflict Panel -->
    @if(!empty($conflicts['all']))
        <x-schedule.conflict-indicator 
            :conflicts="$conflicts"
            :show="$showConflicts"
            wire:click="toggleConflicts"
        />
    @endif

    <!-- Enhanced Schedule Grid -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-40 border-r border-gray-200">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                <span>Sesi</span>
                            </div>
                        </th>
                        @foreach($this->getScheduleDates() as $dateInfo)
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 last:border-r-0">
                                <div class="flex flex-col items-center space-y-1">
                                    <div class="text-blue-600">{{ $dateInfo['day_name'] }}</div>
                                    <div class="text-gray-900 font-bold text-sm">{{ $dateInfo['formatted'] }}</div>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach([1, 2, 3] as $session)
                        @php
                            $sessionInfo = $this->getSessionTime($session);
                            $sessionColors = [
                                1 => 'bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500',
                                2 => 'bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500',
                                3 => 'bg-gradient-to-r from-purple-50 to-purple-100 border-l-4 border-purple-500',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-5 whitespace-nowrap text-sm font-semibold text-gray-900 {{ $sessionColors[$session] }} border-r border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center font-bold text-lg">
                                        {{ $session }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $sessionInfo['label'] }}</div>
                                        <div class="text-xs text-gray-600 flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            <span>{{ $sessionInfo['start'] }} - {{ $sessionInfo['end'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @foreach($this->getScheduleDates() as $dateInfo)
                                @php
                                    $date = $dateInfo['date'];
                                    $slotAssignments = $this->getSlotAssignments($date, $session);
                                    $userCount = count($slotAssignments);
                                    $slotStatus = $this->getSlotStatus($date, $session);
                                @endphp
                                <td class="px-4 py-4 text-sm border-r border-gray-200 last:border-r-0">
                                    <x-schedule.slot-card
                                        :date="$date"
                                        :session="$session"
                                        :assignments="$slotAssignments"
                                        :status="$slotStatus"
                                        :userCount="$userCount"
                                        :isFull="$this->isSlotFull($date, $session)"
                                        wire:key="slot-{{ $date }}-{{ $session }}"
                                    >
                                        <!-- Enhanced Slot Actions with Icons -->
                                        <div class="flex gap-2">
                                            <button 
                                                wire:click="openUserSelector('{{ $date }}', {{ $session }})"
                                                class="group flex-1 flex items-center justify-center space-x-1.5 text-xs bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-3 py-2 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md font-medium hover:scale-105 active:scale-95"
                                                @if($this->isSlotFull($date, $session)) disabled @endif
                                                x-data
                                                x-tooltip="'Tambah user ke slot'"
                                            >
                                                <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                <span>Tambah</span>
                                            </button>
                                            @if($userCount > 0)
                                                <button 
                                                    @click="clearSlotData = { date: '{{ $date }}', session: {{ $session }} }; showClearConfirm = true"
                                                    class="group flex items-center justify-center text-xs bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg px-3 py-2 hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-sm hover:shadow-md hover:scale-105 active:scale-95"
                                                    x-data
                                                    x-tooltip="'Kosongkan slot'"
                                                >
                                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </x-schedule.slot-card>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



    <!-- Enhanced User Selector Modal -->
    @if($showUserSelector && $selectedSlot)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="$wire.closeUserSelector()">
            <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[85vh] overflow-hidden"
                 x-transition:enter="transition ease-out duration-300 delay-100"
                 x-transition:enter-start="opacity-0 transform scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90"
                 @click.stop>
                
                <!-- Enhanced Header with Gradient -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div class="text-white">
                                <h3 class="text-xl font-bold">Tambah User ke Slot</h3>
                                <p class="text-sm text-blue-100 flex items-center space-x-2 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ $this->getFormattedDate($selectedSlot['date']) }} - {{ $this->getSessionTime($selectedSlot['session'])['label'] }}</span>
                                </p>
                            </div>
                        </div>
                        <button 
                            wire:click="closeUserSelector" 
                            class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors duration-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-5">
                    <!-- Enhanced Search -->
                    <div class="mb-5">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="searchTerm"
                                placeholder="Cari nama atau email user..."
                                class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            >
                        </div>
                    </div>

                    <!-- Enhanced Available Users List -->
                    <div class="max-h-96 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                        @forelse($availableUsers as $user)
                            <div class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 group hover:shadow-md hover:scale-[1.02] cursor-pointer"
                                 x-data
                                 x-tooltip="'Klik untuk menambahkan {{ $user->name }} ke slot'">
                                <div class="flex items-center space-x-4 flex-1">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleUserSelection({{ $user->id }})"
                                        @if(in_array($user->id, $selectedUserIds)) checked @endif
                                        class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    >
                                    
                                    @php
                                        $nameParts = explode(' ', $user->name);
                                        $initials = '';
                                        foreach ($nameParts as $part) {
                                            if (!empty($part)) {
                                                $initials .= strtoupper(substr($part, 0, 1));
                                                if (strlen($initials) >= 2) break;
                                            }
                                        }
                                        $colorIndex = $user->id % 8;
                                        $colors = [
                                            'bg-gradient-to-br from-blue-400 to-blue-600',
                                            'bg-gradient-to-br from-green-400 to-green-600',
                                            'bg-gradient-to-br from-purple-400 to-purple-600',
                                            'bg-gradient-to-br from-pink-400 to-pink-600',
                                            'bg-gradient-to-br from-yellow-400 to-yellow-600',
                                            'bg-gradient-to-br from-red-400 to-red-600',
                                            'bg-gradient-to-br from-indigo-400 to-indigo-600',
                                            'bg-gradient-to-br from-teal-400 to-teal-600',
                                        ];
                                    @endphp
                                    
                                    <div class="w-12 h-12 rounded-xl {{ $colors[$colorIndex] }} flex items-center justify-center text-white font-bold text-lg shadow-md">
                                        {{ $initials }}
                                    </div>
                                    
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900 text-base">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 flex items-center space-x-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button 
                                    wire:click="addUserToSlot('{{ $selectedSlot['date'] }}', {{ $selectedSlot['session'] }}, {{ $user->id }})"
                                    class="group flex items-center space-x-2 text-sm bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg px-4 py-2 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-sm hover:shadow-md font-medium hover:scale-105 active:scale-95"
                                >
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Tambah</span>
                                </button>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-12">
                                <div class="text-5xl mb-3">🔍</div>
                                <div class="font-semibold text-gray-700 mb-1">Tidak ada user tersedia</div>
                                <div class="text-sm text-gray-500">Semua user sudah ditugaskan atau tidak tersedia</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enhanced Footer -->
                <div class="px-6 py-4 border-t-2 border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        @if(count($selectedUserIds) > 0)
                            <div class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg font-semibold text-sm">
                                {{ count($selectedUserIds) }} user dipilih
                            </div>
                        @else
                            <div class="text-sm text-gray-600">
                                Pilih user untuk ditambahkan
                            </div>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <button 
                            wire:click="closeUserSelector"
                            class="px-5 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-all duration-200 hover:scale-105 active:scale-95"
                        >
                            Batal
                        </button>
                        @if(count($selectedUserIds) > 0)
                            <button 
                                wire:click="bulkAddUsers('{{ $selectedSlot['date'] }}', {{ $selectedSlot['session'] }}, @js($selectedUserIds)); closeUserSelector()"
                                class="group flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 font-semibold transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                            >
                                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>Tambah {{ count($selectedUserIds) }} User</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Remove User Confirmation Dialog -->
    <div x-show="showRemoveConfirm" 
         x-cloak
         class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="showRemoveConfirm = false; removeData = null">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             @click.stop>
            
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Konfirmasi Hapus User</h3>
                        <p class="text-sm text-red-100 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-5">
                <div class="flex items-start space-x-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-900 font-medium mb-2">
                            Apakah Anda yakin ingin menghapus user ini dari slot?
                        </p>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">User yang akan dihapus:</div>
                            <div class="font-semibold text-gray-900" x-text="removeData?.userName || 'Unknown'"></div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start space-x-2">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-yellow-800">
                        User akan menerima notifikasi tentang penghapusan assignment ini.
                    </p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button 
                    @click="showRemoveConfirm = false; removeData = null"
                    class="px-5 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-all duration-200 hover:scale-105 active:scale-95"
                >
                    Batal
                </button>
                <button 
                    @click="if(removeData) { $wire.removeUserFromSlot(removeData.assignmentId); showRemoveConfirm = false; removeData = null; }"
                    class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 font-semibold transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 active:scale-95 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Ya, Hapus User</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Clear Slot Confirmation Dialog -->
    <div x-show="showClearConfirm" 
         x-cloak
         class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="showClearConfirm = false; clearSlotData = null">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             @click.stop>
            
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg animate-pulse">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="text-white">
                        <h3 class="text-xl font-bold">Kosongkan Slot</h3>
                        <p class="text-sm text-orange-100 mt-0.5">Hapus semua user dari slot ini</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-5">
                <div class="flex items-start space-x-3 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-900 font-medium mb-2">
                            Apakah Anda yakin ingin mengosongkan slot ini?
                        </p>
                        <p class="text-sm text-gray-600">
                            Semua user yang ada di slot akan dihapus dari jadwal.
                        </p>
                    </div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 flex items-start space-x-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-red-800 mb-1">Peringatan:</p>
                        <p class="text-sm text-red-700">
                            Semua user yang terhapus akan menerima notifikasi. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button 
                    @click="showClearConfirm = false; clearSlotData = null"
                    class="px-5 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-all duration-200 hover:scale-105 active:scale-95"
                >
                    Batal
                </button>
                <button 
                    @click="if(clearSlotData) { $wire.clearSlot(clearSlotData.date, clearSlotData.session, 'Slot cleared by admin'); showClearConfirm = false; clearSlotData = null; }"
                    class="px-5 py-2.5 bg-gradient-to-r from-orange-600 to-red-700 text-white rounded-lg hover:from-orange-700 hover:to-red-800 font-semibold transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 active:scale-95 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Ya, Kosongkan Slot</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Loading Overlay with Animations and Progress -->
    <div 
        x-data="{ show: false }"
        x-show="$store.loading.active || show"
        x-cloak
        @loading.window="show = true"
        @loaded.window="show = false"
        wire:loading.class="!block"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 hidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center space-y-4 max-w-sm mx-4 animate-bounce-in"
            x-transition:enter="transition ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
        >
            <!-- Animated spinner with multiple rings -->
            <div class="relative w-16 h-16">
                <svg class="animate-spin h-16 w-16 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-4 h-4 bg-blue-600 rounded-full animate-pulse"></div>
                </div>
                <!-- Outer ring -->
                <svg class="absolute inset-0 animate-spin-slow h-16 w-16 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-20" cx="12" cy="12" r="11" stroke="currentColor" stroke-width="2"></circle>
                </svg>
            </div>
            
            <!-- Loading message with dynamic text -->
            <div class="text-center">
                <div class="text-gray-900 font-semibold text-lg mb-1" x-text="$store.loading.message || 'Memproses...'"></div>
                <div class="text-gray-500 text-sm">Mohon tunggu sebentar</div>
            </div>
            
            <!-- Progress bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div 
                    class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300 ease-out"
                    :style="`width: ${$store.loading.progress}%`"
                ></div>
            </div>
            
            <!-- Progress dots animation -->
            <div class="flex space-x-2">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
            </div>
        </div>
    </div>

    <!-- Enhanced Toast Notification System -->
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            getConfig() {
                const configs = {
                    success: {
                        border: 'border-green-500',
                        bg: 'bg-green-100',
                        text: 'text-green-600',
                        title: 'Berhasil!',
                        icon: 'M5 13l4 4L19 7'
                    },
                    error: {
                        border: 'border-red-500',
                        bg: 'bg-red-100',
                        text: 'text-red-600',
                        title: 'Error!',
                        icon: 'M6 18L18 6M6 6l12 12'
                    },
                    warning: {
                        border: 'border-yellow-500',
                        bg: 'bg-yellow-100',
                        text: 'text-yellow-600',
                        title: 'Peringatan!',
                        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
                    },
                    info: {
                        border: 'border-blue-500',
                        bg: 'bg-blue-100',
                        text: 'text-blue-600',
                        title: 'Info',
                        icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                    }
                };
                return configs[this.type] || configs.success;
            }
         }"
         @notify.window="
            show = true; 
            message = $event.detail.message || 'Operasi berhasil'; 
            type = $event.detail.type || 'success';
            setTimeout(() => show = false, $event.detail.duration || 3000)
         "
         x-init="
            $watch('$store.toast.show', value => {
                if (value) {
                    show = true;
                    message = $store.toast.message;
                    type = $store.toast.type;
                    setTimeout(() => {
                        show = false;
                        $store.toast.show = false;
                    }, $store.toast.duration);
                }
            })
         "
         x-show="show"
         x-cloak
         class="fixed top-4 right-4 z-[60] max-w-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full scale-90"
         x-transition:enter-end="opacity-100 transform translate-x-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0 scale-100"
         x-transition:leave-end="opacity-0 transform translate-x-full scale-90">
        <div class="bg-white rounded-xl shadow-2xl border-l-4 p-4 flex items-start space-x-3 animate-bounce-in"
             :class="getConfig().border">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                     :class="getConfig().bg">
                    <svg class="w-6 h-6" :class="getConfig().text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getConfig().icon" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="text-sm font-semibold text-gray-900 mb-1" x-text="getConfig().title"></h4>
                <p class="text-sm text-gray-600 break-words" x-text="message"></p>
            </div>
            <button @click="show = false" 
                    class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-all duration-200 hover:scale-110 active:scale-95 p-1 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Global Confirmation Dialog (Alpine Store Based) -->
    <div x-data
         x-show="$store.confirm.show"
         x-cloak
         class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[70] p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="$store.confirm.close()"
         @keydown.escape.window="$store.confirm.close()">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 transform scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             @click.stop>
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="text-white">
                        <h3 class="text-xl font-bold" x-text="$store.confirm.title"></h3>
                        <p class="text-sm text-red-100 mt-0.5">Tindakan ini memerlukan konfirmasi</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-5">
                <p class="text-gray-700 text-base" x-text="$store.confirm.message"></p>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button 
                    @click="$store.confirm.cancel()"
                    class="px-5 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 font-medium transition-all duration-200 hover:scale-105 active:scale-95"
                    x-text="$store.confirm.cancelText"
                >
                </button>
                <button 
                    @click="$store.confirm.confirm()"
                    class="px-5 py-2.5 text-white rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg hover:scale-105 active:scale-95"
                    :class="$store.confirm.confirmClass"
                    x-text="$store.confirm.confirmText"
                >
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Enhanced Alpine.js interactions for Schedule Edit
    document.addEventListener('alpine:init', () => {
        // Enhanced tooltip directive with better positioning, animations, and multi-line support
        Alpine.directive('tooltip', (el, { expression }, { evaluate }) => {
            const content = evaluate(expression);
            
            let tooltip = null;
            let timeout = null;
            
            const showTooltip = () => {
                // Clear any existing timeout
                if (timeout) clearTimeout(timeout);
                
                // Delay tooltip appearance slightly for better UX
                timeout = setTimeout(() => {
                    tooltip = document.createElement('div');
                    tooltip.className = 'fixed z-[100] px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-xl pointer-events-none transition-all duration-200 opacity-0 max-w-xs';
                    tooltip.style.whiteSpace = 'normal';
                    tooltip.textContent = content;
                    
                    // Add arrow indicator
                    const arrow = document.createElement('div');
                    arrow.className = 'absolute w-2 h-2 bg-gray-900 transform rotate-45';
                    tooltip.appendChild(arrow);
                    
                    document.body.appendChild(tooltip);
                    
                    // Calculate optimal position
                    const rect = el.getBoundingClientRect();
                    const tooltipRect = tooltip.getBoundingClientRect();
                    
                    // Default: position above element
                    let top = rect.top - tooltipRect.height - 10;
                    let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                    let arrowPosition = 'bottom';
                    
                    // Check if tooltip goes off screen vertically
                    if (top < 10) {
                        // Position below if not enough space above
                        top = rect.bottom + 10;
                        arrowPosition = 'top';
                    }
                    
                    // Ensure tooltip stays within viewport horizontally
                    const padding = 10;
                    if (left < padding) {
                        left = padding;
                    } else if (left + tooltipRect.width > window.innerWidth - padding) {
                        left = window.innerWidth - tooltipRect.width - padding;
                    }
                    
                    // Position arrow
                    if (arrowPosition === 'bottom') {
                        arrow.style.bottom = '-4px';
                        arrow.style.left = '50%';
                        arrow.style.transform = 'translateX(-50%) rotate(45deg)';
                    } else {
                        arrow.style.top = '-4px';
                        arrow.style.left = '50%';
                        arrow.style.transform = 'translateX(-50%) rotate(45deg)';
                    }
                    
                    tooltip.style.top = `${top}px`;
                    tooltip.style.left = `${left}px`;
                    
                    // Fade in with scale animation
                    requestAnimationFrame(() => {
                        tooltip.classList.remove('opacity-0');
                        tooltip.classList.add('opacity-100', 'scale-100');
                        tooltip.style.transform = 'scale(1)';
                    });
                    
                    el._tooltip = tooltip;
                }, 300); // 300ms delay before showing
            };
            
            const hideTooltip = () => {
                if (timeout) {
                    clearTimeout(timeout);
                    timeout = null;
                }
                
                if (el._tooltip) {
                    el._tooltip.classList.remove('opacity-100');
                    el._tooltip.classList.add('opacity-0');
                    el._tooltip.style.transform = 'scale(0.95)';
                    
                    setTimeout(() => {
                        if (el._tooltip) {
                            el._tooltip.remove();
                            delete el._tooltip;
                        }
                    }, 200);
                }
            };
            
            el.addEventListener('mouseenter', showTooltip);
            el.addEventListener('mouseleave', hideTooltip);
            el.addEventListener('click', hideTooltip); // Hide on click
            el.addEventListener('focus', showTooltip); // Show on focus for accessibility
            el.addEventListener('blur', hideTooltip); // Hide on blur
        });
        
        // Loading state management with progress tracking
        Alpine.store('loading', {
            active: false,
            message: 'Memproses...',
            progress: 0,
            
            show(message = 'Memproses...') {
                this.message = message;
                this.active = true;
                this.progress = 0;
                this.startProgress();
            },
            
            hide() {
                this.active = false;
                this.progress = 100;
            },
            
            startProgress() {
                // Simulate progress for better UX
                const interval = setInterval(() => {
                    if (this.progress < 90 && this.active) {
                        this.progress += Math.random() * 10;
                    } else {
                        clearInterval(interval);
                    }
                }, 200);
            },
            
            setMessage(message) {
                this.message = message;
            }
        });
        
        // Toast notification system
        Alpine.store('toast', {
            show: false,
            message: '',
            type: 'success', // success, error, warning, info
            duration: 3000,
            
            success(message, duration = 3000) {
                this.display(message, 'success', duration);
            },
            
            error(message, duration = 4000) {
                this.display(message, 'error', duration);
            },
            
            warning(message, duration = 3500) {
                this.display(message, 'warning', duration);
            },
            
            info(message, duration = 3000) {
                this.display(message, 'info', duration);
            },
            
            display(message, type, duration) {
                this.message = message;
                this.type = type;
                this.duration = duration;
                this.show = true;
                
                setTimeout(() => {
                    this.show = false;
                }, duration);
            }
        });
        
        // Confirmation dialog store with enhanced features
        Alpine.store('confirm', {
            show: false,
            title: '',
            message: '',
            confirmText: 'Ya',
            cancelText: 'Batal',
            confirmClass: 'bg-red-600 hover:bg-red-700',
            icon: 'warning',
            onConfirm: null,
            onCancel: null,
            
            ask(options) {
                this.title = options.title || 'Konfirmasi';
                this.message = options.message || 'Apakah Anda yakin?';
                this.confirmText = options.confirmText || 'Ya';
                this.cancelText = options.cancelText || 'Batal';
                this.confirmClass = options.confirmClass || 'bg-red-600 hover:bg-red-700';
                this.icon = options.icon || 'warning';
                this.onConfirm = options.onConfirm || null;
                this.onCancel = options.onCancel || null;
                this.show = true;
            },
            
            confirm() {
                if (this.onConfirm) {
                    this.onConfirm();
                }
                this.close();
            },
            
            cancel() {
                if (this.onCancel) {
                    this.onCancel();
                }
                this.close();
            },
            
            close() {
                this.show = false;
                this.onConfirm = null;
                this.onCancel = null;
            }
        });
    });
    
    // Livewire loading interceptor with visual feedback
    document.addEventListener('livewire:init', () => {
        let loadingTimeout;
        
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            // Show loading indicator after 200ms (avoid flash for quick operations)
            loadingTimeout = setTimeout(() => {
                Alpine.store('loading').show();
            }, 200);
            
            succeed(({ snapshot, effect }) => {
                clearTimeout(loadingTimeout);
                Alpine.store('loading').hide();
            });
            
            fail(({ snapshot, effect }) => {
                clearTimeout(loadingTimeout);
                Alpine.store('loading').hide();
                Alpine.store('toast').error('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
        
        // Listen for Livewire events
        Livewire.on('notify', (event) => {
            const data = event[0] || event;
            const type = data.type || 'success';
            const message = data.message || 'Operasi berhasil';
            Alpine.store('toast')[type](message);
        });
        
        Livewire.on('confirm', (event) => {
            const data = event[0] || event;
            Alpine.store('confirm').ask(data);
        });
    });
    
    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Enhanced keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // ESC to close modals and dialogs
        if (e.key === 'Escape') {
            // Close confirmation dialogs
            if (Alpine.store('confirm').show) {
                Alpine.store('confirm').close();
                return;
            }
            
            // Dispatch event to close other modals
            window.dispatchEvent(new CustomEvent('close-modals'));
            
            // Close Livewire modals
            window.dispatchEvent(new CustomEvent('close-modal'));
        }
        
        // Ctrl/Cmd + S to save changes
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const saveButton = document.querySelector('[wire\\:click="saveChanges"]');
            if (saveButton && !saveButton.disabled) {
                saveButton.click();
                Alpine.store('toast').info('Menyimpan perubahan...');
            }
        }
        
        // Ctrl/Cmd + Z to discard changes (with confirmation)
        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
            e.preventDefault();
            const discardButton = document.querySelector('[wire\\:click="discardChanges"]');
            if (discardButton && !discardButton.disabled) {
                Alpine.store('confirm').ask({
                    title: 'Batalkan Perubahan?',
                    message: 'Semua perubahan yang belum disimpan akan hilang.',
                    confirmText: 'Ya, Batalkan',
                    cancelText: 'Tidak',
                    confirmClass: 'bg-red-600 hover:bg-red-700',
                    onConfirm: () => {
                        discardButton.click();
                    }
                });
            }
        }
    });
    
    // Add visual feedback for button clicks
    document.addEventListener('click', (e) => {
        const button = e.target.closest('button');
        if (button && !button.disabled) {
            button.style.transform = 'scale(0.95)';
            setTimeout(() => {
                button.style.transform = '';
            }, 100);
        }
    });
    
    // Auto-hide alerts after some time
    setTimeout(() => {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            if (!alert.classList.contains('permanent')) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s, transform 0.5s';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
        });
    }, 1000);
</script>

<style>
    /* Custom scrollbar styles with smooth transitions */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
        transition: background 0.3s ease;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Enhanced smooth transitions for all interactive elements */
    button, a, input, select, textarea {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    
    button:active {
        transform: scale(0.95);
    }
    
    /* Pulse animation for unsaved changes badge */
    @keyframes pulse-slow {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.02);
        }
    }
    
    .animate-pulse-slow {
        animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Fade in animation for list items */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }
    
    /* Slide in from right animation */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .animate-slide-in-right {
        animation: slideInRight 0.3s ease-out;
    }
    
    /* Shimmer loading effect */
    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }
    
    .animate-shimmer {
        animation: shimmer 2s infinite linear;
        background: linear-gradient(to right, #f1f5f9 4%, #e2e8f0 25%, #f1f5f9 36%);
        background-size: 1000px 100%;
    }
    
    /* Bounce animation for notifications */
    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3) translateY(-20px);
        }
        50% {
            opacity: 1;
            transform: scale(1.05) translateY(0);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
        }
    }
    
    .animate-bounce-in {
        animation: bounceIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    
    /* Shake animation for errors */
    @keyframes shake {
        0%, 100% {
            transform: translateX(0);
        }
        10%, 30%, 50%, 70%, 90% {
            transform: translateX(-5px);
        }
        20%, 40%, 60%, 80% {
            transform: translateX(5px);
        }
    }
    
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
    
    /* Glow effect for focused elements */
    @keyframes glow {
        0%, 100% {
            box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
        }
        50% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.8);
        }
    }
    
    .animate-glow {
        animation: glow 2s ease-in-out infinite;
    }
    
    /* Hide scrollbar for Chrome, Safari and Opera */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    /* Hide scrollbar for IE, Edge and Firefox */
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Smooth hover effects for cards */
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Loading spinner animation */
    @keyframes spin-slow {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin-slow {
        animation: spin-slow 2s linear infinite;
    }
    
    /* Gradient animation for backgrounds */
    @keyframes gradient-shift {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
    
    .animate-gradient {
        background-size: 200% 200%;
        animation: gradient-shift 3s ease infinite;
    }
    
    /* Focus visible styles for accessibility */
    *:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
        border-radius: 4px;
    }
    
    /* Smooth transitions for Alpine.js x-show */
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

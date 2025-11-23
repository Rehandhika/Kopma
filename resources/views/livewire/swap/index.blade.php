<div class="space-y-6">
    <!-- Page Header -->
    <x-layout.page-header title="Tukar Shift">
        <x-slot:actions>
            <x-ui.button 
                variant="primary" 
                :href="route('swap.create')" 
                icon="arrow-path">
                Buat Permintaan
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    <!-- Tabs -->
    <x-ui.card :padding="false">
        <x-data.tabs>
            <x-data.tab 
                name="my-requests" 
                :active="$tab === 'my-requests'" 
                wire:click="$set('tab', 'my-requests')">
                Permintaan Saya
            </x-data.tab>
            <x-data.tab 
                name="received" 
                :active="$tab === 'received'" 
                wire:click="$set('tab', 'received')">
                Permintaan Masuk
            </x-data.tab>
        </x-data.tabs>

        <!-- Swap List -->
        <div class="p-6">
            <div class="space-y-4">
                @forelse($swaps as $swap)
                    <x-ui.card shadow="sm" class="hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-3">
                                    <x-ui.badge 
                                        :variant="$swap->status === 'accepted' ? 'success' : ($swap->status === 'pending' ? 'warning' : 'danger')">
                                        {{ ucfirst($swap->status) }}
                                    </x-ui.badge>
                                    <span class="text-sm text-gray-500">
                                        {{ $swap->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <div class="text-xs font-medium text-gray-500 uppercase mb-1">Dari</div>
                                        <div class="font-semibold text-gray-900">{{ $swap->requester->name }}</div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            {{ $swap->originalSchedule->date->format('d/m/Y') }} - 
                                            Sesi {{ $swap->originalSchedule->session }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-gray-500 uppercase mb-1">Ke</div>
                                        <div class="font-semibold text-gray-900">{{ $swap->target->name }}</div>
                                        @if($swap->targetSchedule)
                                            <div class="text-sm text-gray-600 mt-1">
                                                {{ $swap->targetSchedule->date->format('d/m/Y') }} - 
                                                Sesi {{ $swap->targetSchedule->session }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($swap->reason)
                                    <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-3">
                                        <span class="font-medium">Alasan:</span> {{ $swap->reason }}
                                    </div>
                                @endif
                            </div>

                            @if($tab === 'received' && $swap->status === 'pending')
                                <div class="flex space-x-2 ml-4">
                                    <x-ui.button 
                                        wire:click="acceptRequest({{ $swap->id }})" 
                                        variant="success" 
                                        size="sm">
                                        Terima
                                    </x-ui.button>
                                    <x-ui.button 
                                        wire:click="rejectRequest({{ $swap->id }})" 
                                        variant="danger" 
                                        size="sm">
                                        Tolak
                                    </x-ui.button>
                                </div>
                            @elseif($tab === 'my-requests' && $swap->status === 'pending')
                                <x-ui.button 
                                    wire:click="cancelRequest({{ $swap->id }})" 
                                    wire:confirm="Batalkan permintaan?"
                                    variant="white" 
                                    size="sm" 
                                    class="ml-4">
                                    Batalkan
                                </x-ui.button>
                            @endif
                        </div>
                    </x-ui.card>
                @empty
                    <x-layout.empty-state 
                        icon="arrow-path" 
                        title="Tidak ada permintaan tukar shift"
                        description="Belum ada permintaan tukar shift pada tab ini" />
                @endforelse
            </div>
        </div>
    </x-ui.card>

    <!-- Pagination -->
    <div>
        {{ $swaps->links() }}
    </div>
</div>

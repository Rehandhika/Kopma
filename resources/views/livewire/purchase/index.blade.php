<div class="space-y-6">
    <!-- Header -->
    <x-layout.page-header 
        title="Daftar Pembelian"
        description="Kelola pembelian dari supplier"
    />

    <!-- Stats -->
    <x-layout.grid cols="4" gap="4">
        <x-layout.stat-card
            label="Total Pembelian"
            :value="$stats['total']"
            icon="shopping-cart"
            iconColor="bg-primary-100"
            iconTextColor="text-primary-600"
        />
        <x-layout.stat-card
            label="Belum Bayar"
            :value="$stats['pending']"
            icon="clock"
            iconColor="bg-warning-100"
            iconTextColor="text-warning-600"
        />
        <x-layout.stat-card
            label="Bayar Sebagian"
            :value="$stats['approved']"
            icon="credit-card"
            iconColor="bg-info-100"
            iconTextColor="text-info-600"
        />
        <x-layout.stat-card
            label="Lunas"
            :value="$stats['received']"
            icon="check-circle"
            iconColor="bg-success-100"
            iconTextColor="text-success-600"
        />
    </x-layout.grid>

    <!-- Filters -->
    <x-ui.card padding="true">
        <x-layout.grid cols="2" gap="4">
            <x-ui.input 
                type="text" 
                wire:model.live="search" 
                placeholder="Cari invoice atau supplier..." 
                icon="search"
            />
            <x-ui.select wire:model.live="statusFilter">
                <option value="">Semua Status</option>
                <option value="unpaid">Belum Bayar</option>
                <option value="partial">Bayar Sebagian</option>
                <option value="paid">Lunas</option>
            </x-ui.select>
        </x-layout.grid>
    </x-ui.card>

    <!-- Purchase List -->
    <x-data.table 
        :headers="['No. Invoice', 'Tanggal', 'Supplier', 'Total', 'Status Pembayaran', 'Dibuat Oleh', 'Aksi']"
        striped="true"
        hoverable="true"
    >
        @forelse($purchases as $purchase)
            <x-data.table-row>
                <x-data.table-cell class="font-medium">{{ $purchase->invoice_number }}</x-data.table-cell>
                <x-data.table-cell>{{ $purchase->date->format('d/m/Y') }}</x-data.table-cell>
                <x-data.table-cell>{{ $purchase->supplier_name ?? '-' }}</x-data.table-cell>
                <x-data.table-cell class="font-medium">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</x-data.table-cell>
                <x-data.table-cell>
                    @if($purchase->payment_status === 'paid')
                        <x-ui.badge variant="success">Lunas</x-ui.badge>
                    @elseif($purchase->payment_status === 'partial')
                        <x-ui.badge variant="info">Sebagian</x-ui.badge>
                    @else
                        <x-ui.badge variant="warning">Belum Bayar</x-ui.badge>
                    @endif
                </x-data.table-cell>
                <x-data.table-cell>{{ $purchase->user->name ?? '-' }}</x-data.table-cell>
                <x-data.table-cell>
                    <div class="flex items-center space-x-2">
                        @if($purchase->payment_status === 'unpaid')
                            <x-ui.button 
                                variant="ghost" 
                                size="sm"
                                wire:click="approvePurchase({{ $purchase->id }})"
                                wire:confirm="Tandai pembelian ini sebagai lunas?"
                            >
                                Tandai Lunas
                            </x-ui.button>
                        @endif
                    </div>
                </x-data.table-cell>
            </x-data.table-row>
        @empty
            <x-data.table-row>
                <x-data.table-cell colspan="7">
                    <x-layout.empty-state
                        icon="shopping-cart"
                        title="Tidak ada pembelian"
                        description="Belum ada data pembelian yang tercatat"
                    />
                </x-data.table-cell>
            </x-data.table-row>
        @endforelse
    </x-data.table>

    <!-- Pagination -->
    <div>
        {{ $purchases->links() }}
    </div>
</div>

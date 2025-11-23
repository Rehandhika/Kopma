<div class="p-6">
    <x-layout.page-header 
        title="Daftar Penjualan" 
        description="Riwayat transaksi penjualan"
    >
        <x-slot name="actions">
            <x-ui.button variant="primary" icon="plus">
                Transaksi Baru
            </x-ui.button>
        </x-slot>
    </x-layout.page-header>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-ui.input 
                type="date" 
                label="Tanggal Mulai"
                placeholder="Pilih tanggal"
            />
            <x-ui.input 
                type="date" 
                label="Tanggal Akhir"
                placeholder="Pilih tanggal"
            />
            <x-ui.select 
                label="Status"
                :options="[
                    '' => 'Semua Status',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan'
                ]"
            />
        </div>
    </x-ui.card>

    {{-- Sales Table --}}
    <x-ui.card>
        <x-data.table 
            :headers="['No. Transaksi', 'Tanggal', 'Kasir', 'Total', 'Metode', 'Status', 'Aksi']"
            striped="true"
            hoverable="true"
        >
            {{-- Example rows - replace with actual data --}}
            <x-data.table-row>
                <x-data.table-cell>TRX-001</x-data.table-cell>
                <x-data.table-cell>23 Nov 2025 14:30</x-data.table-cell>
                <x-data.table-cell>John Doe</x-data.table-cell>
                <x-data.table-cell>Rp 150.000</x-data.table-cell>
                <x-data.table-cell>
                    <x-ui.badge variant="info" size="sm">Cash</x-ui.badge>
                </x-data.table-cell>
                <x-data.table-cell>
                    <x-ui.badge variant="success" size="sm">Selesai</x-ui.badge>
                </x-data.table-cell>
                <x-data.table-cell>
                    <x-ui.button variant="ghost" size="sm" icon="eye">
                        Detail
                    </x-ui.button>
                </x-data.table-cell>
            </x-data.table-row>
        </x-data.table>

        {{-- Empty State --}}
        {{-- Uncomment when no data
        <x-layout.empty-state 
            icon="shopping-bag" 
            title="Belum ada transaksi"
            description="Transaksi penjualan akan muncul di sini"
        />
        --}}
    </x-ui.card>

    {{-- Pagination --}}
    <div class="mt-6">
        {{-- Add pagination component when data is available --}}
    </div>
</div>

<div class="p-6">
    {{-- Header --}}
    <x-layout.page-header 
        title="Laporan Penjualan"
        description="Analisis dan statistik penjualan"
    >
        <x-slot:actions>
            <x-ui.button variant="white" icon="download">
                Export Excel
            </x-ui.button>
            <x-ui.button variant="primary" icon="printer">
                Cetak
            </x-ui.button>
        </x-slot:actions>
    </x-layout.page-header>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <x-layout.grid cols="4">
            <x-ui.input 
                label="Dari Tanggal"
                name="dateFrom"
                type="date"
                wire:model.live="dateFrom"
            />
            <x-ui.input 
                label="Sampai Tanggal"
                name="dateTo"
                type="date"
                wire:model.live="dateTo"
            />
            <x-ui.select 
                label="Kasir"
                name="cashierFilter"
                wire:model.live="cashierFilter"
            >
                <option value="all">Semua Kasir</option>
                @foreach($cashiers as $cashier)
                    <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                @endforeach
            </x-ui.select>
            <x-ui.select 
                label="Metode Pembayaran"
                name="paymentMethodFilter"
                wire:model.live="paymentMethodFilter"
            >
                <option value="all">Semua Metode</option>
                <option value="cash">Cash</option>
                <option value="transfer">Transfer</option>
                <option value="qris">QRIS</option>
            </x-ui.select>
        </x-layout.grid>
    </x-ui.card>

    {{-- Stats Cards --}}
    <x-layout.grid cols="3" class="mb-6">
        <x-layout.stat-card 
            label="Total Transaksi"
            :value="number_format($stats['total_sales'])"
            subtitle="Transaksi"
            icon="clipboard-list"
            iconColor="bg-info-100"
            iconTextColor="text-info-600"
        />
        <x-layout.stat-card 
            label="Total Pendapatan"
            :value="'Rp ' . number_format($stats['total_revenue'], 0, ',', '.')"
            subtitle="Pendapatan"
            icon="currency-dollar"
            iconColor="bg-success-100"
            iconTextColor="text-success-600"
        />
        <x-layout.stat-card 
            label="Rata-rata Transaksi"
            :value="'Rp ' . number_format($stats['average_transaction'], 0, ',', '.')"
            subtitle="Per Transaksi"
            icon="chart-bar"
            iconColor="bg-secondary-100"
            iconTextColor="text-secondary-600"
        />
    </x-layout.grid>

    {{-- Payment Methods --}}
    <x-ui.card title="Metode Pembayaran" class="mb-6">
        <x-layout.grid cols="3">
            <x-layout.stat-card 
                label="Cash"
                :value="$stats['cash_transactions']"
                icon="cash"
                iconColor="bg-success-100"
                iconTextColor="text-success-600"
            />
            <x-layout.stat-card 
                label="Transfer"
                :value="$stats['transfer_transactions']"
                icon="credit-card"
                iconColor="bg-info-100"
                iconTextColor="text-info-600"
            />
            <x-layout.stat-card 
                label="QRIS"
                :value="$stats['qris_transactions']"
                icon="qrcode"
                iconColor="bg-secondary-100"
                iconTextColor="text-secondary-600"
            />
        </x-layout.grid>
    </x-ui.card>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Sales Chart --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trend Penjualan</h3>
            <canvas id="salesChart" height="200"></canvas>
        </div>

        {{-- Revenue Chart --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Trend Pendapatan</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>

    {{-- Top Products --}}
    <x-ui.card title="Produk Terlaris" class="mb-6">
        <x-data.table :headers="['Produk', 'Terjual', 'Pendapatan']">
            @forelse($topProducts as $index => $product)
                <x-data.table-row>
                    <x-data.table-cell>
                        <div class="flex items-center">
                            <span class="w-8 h-8 bg-info-100 text-info-600 rounded-full flex items-center justify-center font-semibold text-sm mr-3">
                                {{ $index + 1 }}
                            </span>
                            <span class="font-medium text-gray-900">{{ $product->name }}</span>
                        </div>
                    </x-data.table-cell>
                    <x-data.table-cell class="text-center font-semibold">
                        {{ number_format($product->total_quantity) }} unit
                    </x-data.table-cell>
                    <x-data.table-cell class="text-right font-semibold text-success-600">
                        Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell colspan="3">
                        <x-layout.empty-state 
                            icon="shopping-bag"
                            title="Tidak ada data produk"
                            description="Belum ada produk yang terjual dalam periode ini"
                        />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>
    </x-ui.card>

    {{-- Sales Table --}}
    <x-ui.card title="Detail Transaksi">
        <x-data.table :headers="['Invoice', 'Tanggal', 'Kasir', 'Item', 'Metode', 'Total']">
            @forelse($sales as $sale)
                <x-data.table-row>
                    <x-data.table-cell>
                        <span class="font-mono text-sm">{{ $sale->invoice_number }}</span>
                    </x-data.table-cell>
                    <x-data.table-cell>
                        {{ $sale->created_at->format('d/m/Y H:i') }}
                    </x-data.table-cell>
                    <x-data.table-cell>
                        {{ $sale->cashier->name }}
                    </x-data.table-cell>
                    <x-data.table-cell class="text-center">
                        {{ $sale->items->count() }} item
                    </x-data.table-cell>
                    <x-data.table-cell class="text-center">
                        <x-ui.badge 
                            :variant="match($sale->payment_method) {
                                'cash' => 'success',
                                'transfer' => 'info',
                                'qris' => 'secondary',
                                default => 'gray'
                            }"
                        >
                            {{ strtoupper($sale->payment_method) }}
                        </x-ui.badge>
                    </x-data.table-cell>
                    <x-data.table-cell class="text-right font-semibold">
                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                    </x-data.table-cell>
                </x-data.table-row>
            @empty
                <x-data.table-row>
                    <x-data.table-cell colspan="6">
                        <x-layout.empty-state 
                            icon="clipboard-list"
                            title="Tidak ada transaksi"
                            description="Ubah filter atau periode waktu untuk melihat data"
                        />
                    </x-data.table-cell>
                </x-data.table-row>
            @endforelse
        </x-data.table>

        <x-slot:footer>
            {{ $sales->links() }}
        </x-slot:footer>
    </x-ui.card>

    {{-- Loading State --}}
    <div wire:loading class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-40">
        <div class="bg-white rounded-lg p-6 shadow-xl flex flex-col items-center">
            <x-ui.spinner size="lg" />
            <p class="mt-4 text-gray-700 font-medium">Memuat data...</p>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('livewire:navigated', () => {
        const chartData = @js($chartData);
        
        // Sales Chart
        createLineChart('salesChart', {
            labels: chartData.labels,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: chartData.counts,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
            }]
        });

        // Revenue Chart
        createLineChart('revenueChart', {
            labels: chartData.labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: chartData.totals,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.3,
            }]
        });
    });
    </script>
    @endpush
</div>

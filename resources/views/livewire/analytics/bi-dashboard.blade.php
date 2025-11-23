<div class="space-y-6">
    <!-- Header Controls -->
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Business Intelligence Dashboard</h2>
            <div class="flex items-center space-x-4">
                <!-- Period Selector -->
                <x-ui.select wire:model.live="selectedPeriod" name="selectedPeriod">
                    <option value="today">Hari Ini</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="quarter">Kuartal Ini</option>
                    <option value="year">Tahun Ini</option>
                </x-ui.select>

                <!-- Auto Refresh Toggle -->
                <div class="flex items-center space-x-2">
                    <x-ui.button 
                        wire:click="toggleAutoRefresh" 
                        :variant="$autoRefresh ? 'success' : 'ghost'"
                        size="sm"
                    >
                        <x-ui.icon name="refresh" class="w-4 h-4 mr-1" />
                        {{ $autoRefresh ? 'Auto Refresh ON' : 'Auto Refresh OFF' }}
                    </x-ui.button>
                    
                    @if($autoRefresh)
                        <x-ui.select wire:model.live="refreshInterval" name="refreshInterval" class="text-sm">
                            <option value="15">15s</option>
                            <option value="30">30s</option>
                            <option value="60">1m</option>
                            <option value="300">5m</option>
                        </x-ui.select>
                    @endif
                </div>

                <!-- Actions -->
                <x-ui.button wire:click="exportReport" variant="white" size="sm">
                    <x-ui.icon name="download" class="w-4 h-4 mr-2" />
                    Export
                </x-ui.button>
                
                <x-ui.button wire:click="scheduleReport" variant="primary" size="sm">
                    <x-ui.icon name="clock" class="w-4 h-4 mr-2" />
                    Jadwalkan
                </x-ui.button>
            </div>
        </div>

        <!-- Date Range Display -->
        <div class="text-sm text-gray-600">
            Periode: {{ $startDate->locale('id')->isoFormat('D MMMM YYYY') }} - {{ $endDate->locale('id')->isoFormat('D MMMM YYYY') }}
        </div>
    </x-ui.card>

    <!-- KPI Cards -->
    <x-layout.grid cols="4">
        <!-- Attendance KPIs -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Kehadiran</h3>
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <x-ui.icon name="users" class="w-4 h-4 text-blue-600" />
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Tingkat Kehadiran</span>
                    <span class="text-lg font-bold text-gray-900">{{ $getKPIDisplay('attendance_metrics', 'attendance_rate')['formatted_value'] }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Tingkat Ketepatan</span>
                    <span class="text-lg font-bold text-gray-900">{{ $getKPIDisplay('attendance_metrics', 'punctuality_rate')['formatted_value'] }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Hadir</span>
                    <span class="text-lg font-bold text-green-600">{{ $kpis['attendance_metrics']['total_present'] ?? 0 }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Terlambat</span>
                    <span class="text-lg font-bold text-yellow-600">{{ $kpis['attendance_metrics']['total_late'] ?? 0 }}</span>
                </div>
            </div>
        </x-ui.card>

        <!-- Sales KPIs -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Penjualan</h3>
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <x-ui.icon name="currency-dollar" class="w-4 h-4 text-green-600" />
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Pendapatan</span>
                    <span class="text-lg font-bold text-gray-900">{{ $getKPIDisplay('sales_metrics', 'total_revenue')['formatted_value'] }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Transaksi</span>
                    <span class="text-lg font-bold text-gray-900">{{ $kpis['sales_metrics']['total_transactions'] ?? 0 }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Rata-rata Transaksi</span>
                    <span class="text-lg font-bold text-gray-900">{{ $getKPIDisplay('sales_metrics', 'average_transaction')['formatted_value'] }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pertumbuhan</span>
                    <span class="text-lg font-bold {{ $kpis['sales_metrics']['growth_rate'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $kpis['sales_metrics']['growth_rate'] ?? 0 }}%
                    </span>
                </div>
            </div>
        </x-ui.card>

        <!-- Operational KPIs -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Operasional</h3>
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <x-ui.icon name="chart-bar" class="w-4 h-4 text-purple-600" />
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Pengguna</span>
                    <span class="text-lg font-bold text-gray-900">{{ $kpis['operational_metrics']['total_users'] ?? 0 }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pengguna Aktif</span>
                    <span class="text-lg font-bold text-green-600">{{ $kpis['operational_metrics']['active_users'] ?? 0 }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Efisiensi Jadwal</span>
                    <span class="text-lg font-bold text-gray-900">{{ $getKPIDisplay('operational_metrics', 'schedule_efficiency')['formatted_value'] }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Request Pending</span>
                    <span class="text-lg font-bold text-yellow-600">{{ ($kpis['operational_metrics']['pending_swap_requests'] ?? 0) + ($kpis['operational_metrics']['pending_leave_requests'] ?? 0) }}</span>
                </div>
            </div>
        </x-ui.card>

        <!-- Financial KPIs -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Keuangan</h3>
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                    <x-ui.icon name="calculator" class="w-4 h-4 text-yellow-600" />
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Laba Bersih</span>
                    <span class="text-lg font-bold {{ ($kpis['financial_metrics']['net_profit'] ?? 0) > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $getKPIDisplay('financial_metrics', 'net_profit')['formatted_value'] }}
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Margin Laba</span>
                    <span class="text-lg font-bold text-gray-900">{{ $getKPIDisplay('financial_metrics', 'profit_margin')['formatted_value'] }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Penalti</span>
                    <span class="text-lg font-bold text-red-600">{{ $getKPIDisplay('financial_metrics', 'total_penalties')['formatted_value'] }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pendapatan/Hari</span>
                    <span class="text-lg font-bold text-gray-900">{{ $getKPIDisplay('financial_metrics', 'revenue_per_day')['formatted_value'] }}</span>
                </div>
            </div>
        </x-ui.card>
    </x-layout.grid>

    <!-- Real-time Metrics -->
    <x-ui.card title="Metrik Real-time">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-16 h-16 transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"></circle>
                        <circle cx="32" cy="32" r="28" stroke="#10b981" stroke-width="4" fill="none"
                                stroke-dasharray="{{ ($realTimeMetrics['online_users'] / 50) * 176 }} 176"
                                stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute">
                        <span class="text-lg font-bold">{{ $realTimeMetrics['online_users'] }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-2">Online</p>
            </div>

            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-16 h-16 transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"></circle>
                        <circle cx="32" cy="32" r="28" stroke="#3b82f6" stroke-width="4" fill="none"
                                stroke-dasharray="{{ ($realTimeMetrics['active_sessions'] / 100) * 176 }} 176"
                                stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute">
                        <span class="text-lg font-bold">{{ $realTimeMetrics['active_sessions'] }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-2">Sesi Aktif</p>
            </div>

            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-16 h-16 transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"></circle>
                        <circle cx="32" cy="32" r="28" stroke="#8b5cf6" stroke-width="4" fill="none"
                                stroke-dasharray="{{ ($realTimeMetrics['today_attendance']['total'] / 50) * 176 }} 176"
                                stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute">
                        <span class="text-lg font-bold">{{ $realTimeMetrics['today_attendance']['total'] }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-2">Absen Hari Ini</p>
            </div>

            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-16 h-16 transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"></circle>
                        <circle cx="32" cy="32" r="28" stroke="#f59e0b" stroke-width="4" fill="none"
                                stroke-dasharray="{{ ($realTimeMetrics['today_sales']['total'] / 100) * 176 }} 176"
                                stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute">
                        <span class="text-lg font-bold">{{ $realTimeMetrics['today_sales']['total'] }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-2">Transaksi Hari Ini</p>
            </div>

            <div class="text-center">
                <div class="relative inline-flex items-center justify-center">
                    <svg class="w-16 h-16 transform -rotate-90">
                        <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"></circle>
                        <circle cx="32" cy="32" r="28" stroke="#ef4444" stroke-width="4" fill="none"
                                stroke-dasharray="{{ ($realTimeMetrics['system_health']['cpu_usage'] / 100) * 176 }} 176"
                                stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute">
                        <span class="text-lg font-bold">{{ $realTimeMetrics['system_health']['cpu_usage'] }}%</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mt-2">CPU Usage</p>
            </div>
        </div>
    </x-ui.card>

    <!-- Charts Section -->
    <x-layout.grid cols="2">
        <!-- Attendance Trend Chart -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Trend Kehadiran</h3>
                <x-ui.select wire:model.live="chartType" name="chartType" class="text-sm">
                    <option value="line">Line</option>
                    <option value="bar">Bar</option>
                    <option value="area">Area</option>
                </x-ui.select>
            </div>
            <div class="h-64">
                <canvas id="attendance-chart"></canvas>
            </div>
        </x-ui.card>

        <!-- Sales Trend Chart -->
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Trend Penjualan</h3>
                <x-ui.button 
                    wire:click="toggleComparison" 
                    :variant="$compareWithPrevious ? 'primary' : 'ghost'"
                    size="sm"
                >
                    {{ $compareWithPrevious ? 'Bandingkan: ON' : 'Bandingkan: OFF' }}
                </x-ui.button>
            </div>
            <div class="h-64">
                <canvas id="sales-chart"></canvas>
            </div>
        </x-ui.card>
    </x-layout.grid>

    <!-- Predictions Section -->
    <x-ui.card title="Prediksi & Analitik">
        <x-layout.grid cols="4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Prediksi Kehadiran</span>
                    <x-ui.badge variant="info" size="sm">
                        {{ $predictions['attendance_forecast']['confidence'] }}% confidence
                    </x-ui.badge>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $predictions['attendance_forecast']['prediction'] }}</div>
                <div class="text-sm text-gray-600">orang/hari</div>
                <div class="text-xs {{ $predictions['attendance_forecast']['trend'] === 'improving' ? 'text-green-600' : 'text-red-600' }} mt-1">
                    Trend: {{ $predictions['attendance_forecast']['trend'] }}
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Prediksi Penjualan</span>
                    <x-ui.badge variant="success" size="sm">
                        {{ $predictions['sales_forecast']['confidence'] }}% confidence
                    </x-ui.badge>
                </div>
                <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($predictions['sales_forecast']['prediction'], 0, ',', '.') }}</div>
                <div class="text-sm text-gray-600">per hari</div>
                <div class="text-xs {{ $predictions['sales_forecast']['trend'] === 'growing' ? 'text-green-600' : 'text-red-600' }} mt-1">
                    Trend: {{ $predictions['sales_forecast']['trend'] }}
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Kebutuhan Staff</span>
                    <x-ui.badge variant="secondary" size="sm">
                        {{ $predictions['staffing_needs']['utilization_rate'] }}% utilized
                    </x-ui.badge>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $predictions['staffing_needs']['required_staff'] }}</div>
                <div class="text-sm text-gray-600">staff dibutuhkan</div>
                <div class="text-xs {{ $predictions['staffing_needs']['recommendation'] === 'optimal' ? 'text-green-600' : 'text-yellow-600' }} mt-1">
                    {{ $predictions['staffing_needs']['recommendation'] }}
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Prediksi Revenue</span>
                    <x-ui.badge variant="warning" size="sm">
                        {{ $predictions['revenue_forecast']['confidence'] }}% confidence
                    </x-ui.badge>
                </div>
                <div class="text-2xl font-bold text-gray-900">Rp {{ number_format($predictions['revenue_forecast']['prediction'], 0, ',', '.') }}</div>
                <div class="text-sm text-gray-600">per bulan</div>
                <div class="text-xs {{ $predictions['revenue_forecast']['growth_rate'] > 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                    +{{ $predictions['revenue_forecast']['growth_rate'] }}% growth
                </div>
            </div>
        </x-layout.grid>
    </x-ui.card>
</div>

<!-- Chart JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Chart.js
    Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
    
    // Attendance Chart
    const attendanceCtx = document.getElementById('attendance-chart');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: '{{ $chartType }}',
            data: {
                labels: @json(array_keys($trends['attendance']['data'] ?? [])),
                datasets: [{
                    label: 'Tingkat Kehadiran (%)',
                    data: @json(array_values($trends['attendance']['data'] ?? [])),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    // Sales Chart
    const salesCtx = document.getElementById('sales-chart');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: '{{ $chartType }}',
            data: {
                labels: @json(array_keys($trends['sales']['data'] ?? [])),
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: @json(array_values($trends['sales']['data'] ?? [])),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Auto refresh functionality
    @this.on('startAutoRefresh', (interval) => {
        setInterval(() => {
            @this.call('refreshData');
        }, interval * 1000);
    });

    @this.on('stopAutoRefresh', () => {
        // Stop auto refresh logic
    });

    @this.on('updateRefreshInterval', (interval) => {
        // Update refresh interval
    });

    // Handle drill down
    @this.on('drillDownData', (data) => {
        console.log('Drill down data:', data);
        // Show modal or update view with detailed data
    });

    @this.on('dataRefreshed', () => {
        console.log('Dashboard data refreshed');
    });

    @this.on('reportExported', (data) => {
        // Trigger download
        const link = document.createElement('a');
        link.href = data.url;
        link.download = data.filename;
        link.click();
    });

    @this.on('reportScheduled', (message) => {
        // Show success notification
        alert(message);
    });
});
</script>

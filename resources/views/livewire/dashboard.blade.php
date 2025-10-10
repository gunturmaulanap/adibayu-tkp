<div>
    <!-- Filter Date Range -->
    <div class="mb-6 bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Filter Periode</h2>
            <div class="text-sm text-gray-500">
                <span class="font-medium">Data dari:</span>
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Mulai</label>
                <input type="date" wire:model.live="startDate" max="{{ now()->format('Y-m-d') }}"
                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Akhir</label>
                <input type="date" wire:model.live="endDate" max="{{ now()->format('Y-m-d') }}"
                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Quick Filter Buttons -->
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="setDateRange('{{ now()->startOfMonth()->format('Y-m-d') }}', '{{ now()->endOfMonth()->format('Y-m-d') }}')"
                class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                Bulan Ini
            </button>
            <button
                wire:click="setDateRange('{{ now()->subMonth()->startOfMonth()->format('Y-m-d') }}', '{{ now()->subMonth()->endOfMonth()->format('Y-m-d') }}')"
                class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                Bulan Lalu
            </button>
            <button
                wire:click="setDateRange('{{ now()->startOfYear()->format('Y-m-d') }}', '{{ now()->endOfYear()->format('Y-m-d') }}')"
                class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                Tahun Ini
            </button>
            <button
                wire:click="setDateRange('{{ now()->subDays(7)->format('Y-m-d') }}', '{{ now()->format('Y-m-d') }}')"
                class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                7 Hari Terakhir
            </button>
            <button
                wire:click="setDateRange('{{ now()->subDays(30)->format('Y-m-d') }}', '{{ now()->format('Y-m-d') }}')"
                class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                30 Hari Terakhir
            </button>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading.delay class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-700 font-medium">Memuat data...</span>
        </div>
    </div>

    <!-- Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
        wire:key="widgets-{{ $startDate }}-{{ $endDate }}">
        <!-- Total Transaksi -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium opacity-90">Jumlah Transaksi</p>

            </div>
            <h3 class="text-4xl font-bold">{{ number_format($widgets['total_transactions']) }}</h3>
            <p class="text-sm opacity-75 mt-2">Transaksi</p>
        </div>

        <!-- Total Penjualan -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium opacity-90">Total Penjualan</p>

            </div>
            <h3 class="text-3xl font-bold">Rp {{ number_format($widgets['total_sales'], 0, ',', '.') }}</h3>
            <p class="text-sm opacity-75 mt-2">Total Nilai Penjualan</p>
        </div>

        <!-- Total Pembayaran Diterima -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium opacity-90">Pembayaran Diterima</p>

            </div>
            <h3 class="text-3xl font-bold">Rp {{ number_format($widgets['total_received'], 0, ',', '.') }}</h3>
            <p class="text-sm opacity-75 mt-2">Sudah Dibayar</p>
        </div>

        <!-- Sisa Pembayaran -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-medium opacity-90">Sisa Pembayaran</p>

            </div>
            <h3 class="text-3xl font-bold">Rp {{ number_format($widgets['total_remaining'], 0, ',', '.') }}</h3>
            <p class="text-sm opacity-75 mt-2">Belum Dibayar</p>
        </div>
    </div>

    <!-- Secondary Widget -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6"
        wire:key="secondary-widgets-{{ $startDate }}-{{ $endDate }}">
        <!-- Total Qty -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Item Terjual</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($widgets['total_qty']) }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Pieces</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Persentase Pembayaran</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $widgets['total_sales'] > 0 ? number_format(($widgets['total_received'] / $widgets['total_sales']) * 100, 1) : 0 }}%
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Sudah Terbayar</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Transaction -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2">Rp
                        {{ $widgets['total_transactions'] > 0 ? number_format($widgets['total_sales'] / $widgets['total_transactions'], 0, ',', '.') : 0 }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Per Transaksi</p>
                </div>
                <div class="bg-cyan-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 gap-6 mb-6">
        <!-- Monthly Sales Chart with Payment Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Penjualan & Pembayaran per Bulan
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Overview penjualan, pembayaran yang diterima, dan sisa pembayaran setiap bulan
                        @if ($monthlySales['count'] > 0)
                            ({{ $monthlySales['count'] }} bulan)
                        @endif
                    </p>
                </div>
                <div class="flex gap-4 text-xs">
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        <span class="text-gray-600">Penjualan</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span class="text-gray-600">Pembayaran</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 bg-orange-500 rounded"></div>
                        <span class="text-gray-600">Sisa</span>
                    </div>
                </div>
            </div>

            @if ($monthlySales['count'] > 0)
                <div style="height: 400px;" wire:ignore>
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            @else
                <div class="flex items-center justify-center h-64 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Tidak ada data penjualan pada periode ini</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Item Sales Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Item Terjual (Qty)</h3>
            <div style="height: 400px;" wire:ignore>
                <canvas id="itemSalesChart"></canvas>
            </div>
        </div>

        <!-- Payment Status Pie Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Pembayaran</h3>
            <div style="height: 400px;" wire:ignore>
                <canvas id="paymentStatusChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let monthlySalesChart = null;
            let itemSalesChart = null;
            let paymentStatusChart = null;
            let chartsData = {
                monthlySales: @json($monthlySales),
                itemSales: @json($itemSales),
                widgets: @json($widgets)
            };
            let chartsInitialized = false;
            let initRetryCount = 0;
            const MAX_RETRY = 50; // Maximum 50 retries (2.5 seconds)

            const centerTextPlugin = {
                id: 'centerText',
                afterDatasetsDraw: function(chart) {
                    if (chart.config.type === 'doughnut' && chart.canvas.id === 'paymentStatusChart') {
                        const ctx = chart.ctx;
                        const width = chart.width;
                        const height = chart.height;

                        ctx.restore();

                        const data = chart.data.datasets[0].data;
                        // Ensure data are numbers, not strings
                        const received = parseFloat(data[0]) || 0;
                        const remaining = parseFloat(data[1]) || 0;
                        const total = received + remaining;
                        const percentage = total > 0 ? ((received / total) * 100).toFixed(1) : 0;

                        // console.log('CenterText Plugin - Data:', data, 'Received:', received, 'Remaining:', remaining,
                        //     'Total:', total, 'Percentage:', percentage);

                        ctx.font = 'bold 32px sans-serif';
                        ctx.fillStyle = '#1f2937';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(percentage + '%', width / 2, height / 2 - 10);

                        ctx.font = '14px sans-serif';
                        ctx.fillStyle = '#6b7280';
                        ctx.fillText('Terbayar', width / 2, height / 2 + 20);

                        ctx.save();
                    }
                }
            };

            function initCharts() {
                console.log('Initializing charts with data:', chartsData);

                const monthlySalesCtx = document.getElementById('monthlySalesChart');
                if (monthlySalesCtx) {
                    if (monthlySalesChart) {
                        monthlySalesChart.destroy();
                        monthlySalesChart = null;
                    }

                    const labels = chartsData.monthlySales.labels;
                    const salesData = chartsData.monthlySales.sales;
                    const receivedData = chartsData.monthlySales.received;
                    const remainingData = chartsData.monthlySales.remaining;
                    const dataCount = labels.length;

                    // Only create chart if there's data
                    if (dataCount > 0) {
                        let maxBarThickness = 60;

                        if (dataCount <= 3) {
                            maxBarThickness = 100;
                        } else if (dataCount <= 6) {
                            maxBarThickness = 80;
                        } else if (dataCount <= 12) {
                            maxBarThickness = 60;
                        } else {
                            maxBarThickness = 40;
                        }

                        monthlySalesChart = new Chart(monthlySalesCtx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Total Penjualan',
                                        data: salesData,
                                        backgroundColor: 'rgba(59, 130, 246, 0.85)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 0,
                                        borderRadius: 6,
                                        barThickness: 'flex',
                                        maxBarThickness: maxBarThickness
                                    },
                                    {
                                        label: 'Pembayaran Diterima',
                                        data: receivedData,
                                        backgroundColor: 'rgba(34, 197, 94, 0.85)',
                                        borderColor: 'rgb(34, 197, 94)',
                                        borderWidth: 0,
                                        borderRadius: 6,
                                        barThickness: 'flex',
                                        maxBarThickness: maxBarThickness
                                    },
                                    {
                                        label: 'Sisa Pembayaran',
                                        data: remainingData,
                                        backgroundColor: 'rgba(249, 115, 22, 0.85)',
                                        borderColor: 'rgb(249, 115, 22)',
                                        borderWidth: 0,
                                        borderRadius: 6,
                                        barThickness: 'flex',
                                        maxBarThickness: maxBarThickness
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'nearest',
                                    intersect: true,
                                    axis: 'x'
                                },
                                scales: {
                                    x: {
                                        stacked: false,
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                size: dataCount > 31 ? 9 : 11
                                            },
                                            maxRotation: dataCount > 12 ? 45 : 0,
                                            minRotation: dataCount > 12 ? 45 : 0
                                        }
                                    },
                                    y: {
                                        stacked: false,
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                if (value >= 1000000) {
                                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                                } else if (value >= 1000) {
                                                    return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                                }
                                                return 'Rp ' + value;
                                            },
                                            font: {
                                                size: 11
                                            }
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.05)'
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            padding: 15,
                                            usePointStyle: true,
                                            pointStyle: 'circle',
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    tooltip: {
                                        enabled: true,
                                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                        padding: 12,
                                        titleFont: {
                                            size: 13,
                                            weight: 'bold'
                                        },
                                        bodyFont: {
                                            size: 12
                                        },
                                        borderColor: 'rgba(255, 255, 255, 0.1)',
                                        borderWidth: 1,
                                        displayColors: true,
                                        callbacks: {
                                            title: function(context) {
                                                return context[0].label;
                                            },
                                            label: function(context) {
                                                return context.dataset.label + ': Rp ' +
                                                    context.parsed.y.toLocaleString('id-ID');
                                            },
                                            footer: function(context) {
                                                // Calculate total for this month
                                                const dataIndex = context[0].dataIndex;

                                                return [
                                                    '─────────────────',
                                                    'Total Penjualan: Rp ' + salesData[dataIndex]
                                                    .toLocaleString(
                                                        'id-ID'),
                                                    'Pembayaran Diterima: Rp ' + receivedData[dataIndex]
                                                    .toLocaleString('id-ID'),
                                                    'Sisa Pembayaran: Rp ' + remainingData[dataIndex]
                                                    .toLocaleString('id-ID')
                                                ];
                                            }
                                        },
                                        footerFont: {
                                            size: 11,
                                            weight: 'normal'
                                        },
                                        footerColor: 'rgba(255, 255, 255, 0.8)'
                                    }
                                }
                            }
                        });
                    } // End of if (dataCount > 0)
                }

                const itemSalesCtx = document.getElementById('itemSalesChart');
                if (itemSalesCtx) {
                    if (itemSalesChart) {
                        itemSalesChart.destroy();
                    }
                    itemSalesChart = new Chart(itemSalesCtx, {
                        type: 'bar',
                        data: {
                            labels: chartsData.itemSales.labels,
                            datasets: [{
                                label: 'Quantity',
                                data: chartsData.itemSales.data,
                                backgroundColor: [
                                    'rgba(168, 85, 247, 0.7)',
                                    'rgba(236, 72, 153, 0.7)',
                                    'rgba(59, 130, 246, 0.7)',
                                    'rgba(34, 197, 94, 0.7)',
                                    'rgba(249, 115, 22, 0.7)',
                                    'rgba(239, 68, 68, 0.7)',
                                    'rgba(14, 165, 233, 0.7)',
                                    'rgba(168, 162, 158, 0.7)',
                                    'rgba(251, 191, 36, 0.7)',
                                    'rgba(99, 102, 241, 0.7)'
                                ],
                                borderWidth: 0,
                                borderRadius: 8
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'nearest',
                                intersect: true,
                                axis: 'y'
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                    padding: 12,
                                    titleFont: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 12
                                    },
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    callbacks: {
                                        title: function(context) {
                                            return context[0].label;
                                        },
                                        label: function(context) {
                                            return 'Terjual: ' + context.parsed.x + ' pcs';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Payment Status Pie Chart
                const paymentStatusCtx = document.getElementById('paymentStatusChart');
                if (paymentStatusCtx) {
                    if (paymentStatusChart) {
                        paymentStatusChart.destroy();
                    }

                    // Ensure values are numbers
                    const totalReceived = parseFloat(chartsData.widgets.total_received) || 0;
                    const totalRemaining = parseFloat(chartsData.widgets.total_remaining) || 0;

                    console.log('Payment Status Chart - Received:', totalReceived, 'Remaining:', totalRemaining);

                    paymentStatusChart = new Chart(paymentStatusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Sudah Dibayar', 'Belum Dibayar'],
                            datasets: [{
                                data: [totalReceived, totalRemaining],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.85)',
                                    'rgba(249, 115, 22, 0.85)'
                                ],
                                borderWidth: 2,
                                borderColor: '#fff',
                                hoverOffset: 15,
                                hoverBorderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true,
                                        pointStyle: 'circle',
                                        font: {
                                            size: 13
                                        }
                                    }
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                    padding: 12,
                                    titleFont: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 12
                                    },
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            const value = parseFloat(context.parsed) || 0;
                                            const data = context.dataset.data.map(v => parseFloat(v) || 0);
                                            const total = data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return [
                                                context.label,
                                                'Rp ' + value.toLocaleString('id-ID'),
                                                percentage + '% dari total'
                                            ];
                                        }
                                    }
                                }
                            }
                        },
                        plugins: [centerTextPlugin]
                    });
                }
            }

            function safeInitCharts() {
                const monthlySalesCtx = document.getElementById('monthlySalesChart');
                const itemSalesCtx = document.getElementById('itemSalesChart');
                const paymentStatusCtx = document.getElementById('paymentStatusChart');


                if (!monthlySalesCtx && !itemSalesCtx && !paymentStatusCtx) {

                    if (initRetryCount === 0) {
                        // console.log('No chart canvas elements found. Skipping chart initialization (not on dashboard page).');
                    }
                    return;
                }

                initRetryCount++;

                if (initRetryCount > MAX_RETRY) {
                    console.error('Failed to initialize charts after', MAX_RETRY, 'retries. Canvas elements not found.');
                    return;
                }

                if (typeof Chart === 'undefined') {
                    console.log('Chart.js not loaded yet, retrying... (', initRetryCount, '/', MAX_RETRY, ')');
                    setTimeout(safeInitCharts, 50);
                    return;
                }

                console.log('All requirements met, initializing charts...');
                initRetryCount = 0; // Reset counter on success
                initCharts();
                chartsInitialized = true;
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', safeInitCharts);
            } else {
                const isDashboardPage = document.getElementById('monthlySalesChart') ||
                    document.getElementById('itemSalesChart') ||
                    document.getElementById('paymentStatusChart');

                if (isDashboardPage) {
                    setTimeout(safeInitCharts, 100);
                }
            }

            document.addEventListener('livewire:navigated', function() {
                const isDashboardPage = document.getElementById('monthlySalesChart') ||
                    document.getElementById('itemSalesChart') ||
                    document.getElementById('paymentStatusChart');

                if (isDashboardPage) {
                    setTimeout(safeInitCharts, 150);
                }
            });

            document.addEventListener('livewire:init', () => {
                Livewire.on('chartsDataUpdated', (data) => {
                    console.log('Charts data updated received:', data);
                    chartsData = data[0];
                    // console.log('Updated chartsData:', chartsData);
                    // console.log('Widgets data:', chartsData.widgets);
                    initRetryCount = 0;
                    initCharts();
                });
            });
        </script>
    @endpush
</div>

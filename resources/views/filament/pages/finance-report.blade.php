<x-filament-panels::page>

    {{-- ===================================================
    STAT CARDS — 6 cards, 3 columns
    =================================================== --}}
    @php
        $stats = $this->getStats();
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

        {{-- Revenue --}}
        <div
            class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <x-heroicon-o-banknotes class="h-5 w-5 text-emerald-500" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">
                Rp {{ number_format((float) $stats['total_revenue'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-gray-400">{{ \Carbon\Carbon::parse($this->dateFrom)->format('d M') }} –
                {{ \Carbon\Carbon::parse($this->dateTo)->format('d M Y') }}</div>
        </div>

        {{-- Expenses --}}
        <div
            class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <x-heroicon-o-arrow-trending-down class="h-5 w-5 text-rose-500" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Expenses</span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">
                Rp {{ number_format((float) $stats['total_expense'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-gray-400">Periode yang dipilih</div>
        </div>

        {{-- Net Income --}}
        <div
            class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <x-heroicon-o-calculator
                    class="h-5 w-5 {{ $stats['net_income'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Income</span>
            </div>
            <div class="mt-2 text-2xl font-bold {{ $stats['net_income'] >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                Rp {{ number_format((float) $stats['net_income'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-gray-400">Revenue – Expenses</div>
        </div>

        {{-- Paid Transactions --}}
        <div
            class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <x-heroicon-o-check-circle class="h-5 w-5 text-emerald-500" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Paid Transactions</span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">
                {{ number_format($stats['total_paid']) }}
            </div>
            <div class="mt-1 text-xs text-gray-400">Dalam periode ini</div>
        </div>

        {{-- Pending --}}
        <div
            class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <x-heroicon-o-clock class="h-5 w-5 text-amber-500" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Payments</span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">
                {{ number_format($stats['total_pending']) }}
            </div>
            <div class="mt-1 text-xs text-amber-400">Menunggu konfirmasi</div>
        </div>

        {{-- Unpaid Invoices --}}
        <div
            class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-x-2">
                <x-heroicon-o-document-currency-dollar class="h-5 w-5 text-amber-500" />
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Unpaid Invoices</span>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">
                {{ number_format($stats['total_invoice_unpaid']) }}
            </div>
            <div class="mt-1 text-xs text-amber-400">Belum dibayar / Jatuh tempo</div>
        </div>

    </div>

    {{-- ===================================================
    MONTHLY REVENUE & EXPENSE CHART — menggunakan Filament Chart widget embed
    =================================================== --}}
    @php
        $monthly = $this->getMonthlyRevenue();
        $months = collect($monthly)->pluck('month')->toJson();
        $revenues = collect($monthly)->pluck('revenue')->toJson();
        $expenses = collect($monthly)->pluck('expense')->toJson();
    @endphp

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-3">

        {{-- Line Chart: Revenue vs Expense 6 bulan --}}
        <div
            class="xl:col-span-2 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Revenue vs Expenses — 6 Bulan
                Terakhir</h3>
            <canvas id="revenueChart" height="120"></canvas>
        </div>

        {{-- Expense by Category --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Expenses by Category</h3>
            @php $byCategory = $this->getExpenseByCategory(); @endphp
            @if(count($byCategory) > 0)
                <canvas id="categoryChart" height="200"></canvas>
            @else
                <div class="flex h-40 items-center justify-center text-sm text-gray-400">
                    Belum ada data expense dalam periode ini.
                </div>
            @endif
        </div>

    </div>

    {{-- ===================================================
    RECENT PAID TRANSACTIONS TABLE
    =================================================== --}}
    <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-white/10">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Recent Paid Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-left dark:border-white/5 dark:bg-gray-800/50">
                        <th class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Student</th>
                        <th class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Department</th>
                        <th class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Amount</th>
                        <th class="px-6 py-3 font-medium text-gray-500 dark:text-gray-400">Paid At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($this->getRecentTransactions() as $trx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                            <td class="px-6 py-3 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $trx->code }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $trx->user?->name ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $trx->department?->name ?? '-' }}</td>
                            <td class="px-6 py-3 font-semibold text-emerald-500">
                                Rp {{ number_format((float) $trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">
                                {{ $trx->paid_at?->format('d M Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">
                                Belum ada transaksi lunas dalam periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================================================
    CHART.JS — loaded via CDN, standalone (bukan Filament chart widget)
    Aman karena tidak mengganggu Livewire cycle
    =================================================== --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
                const labelColor = isDark ? '#94a3b8' : '#64748b';

                // ── Revenue vs Expense Line Chart ──────────────
                const revenueCtx = document.getElementById('revenueChart');
                if (revenueCtx) {
                    new Chart(revenueCtx, {
                        type: 'line',
                        data: {
                            labels: {!! $months !!},
                            datasets: [
                                {
                                    label: 'Revenue',
                                    data: {!! $revenues !!},
                                    borderColor: '#10B981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#10B981',
                                },
                                {
                                    label: 'Expenses',
                                    data: {!! $expenses !!},
                                    borderColor: '#F43F5E',
                                    backgroundColor: 'rgba(244, 63, 94, 0.08)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    pointBackgroundColor: '#F43F5E',
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: { color: labelColor, font: { size: 12 } }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                                    },
                                },
                            },
                            scales: {
                                x: { ticks: { color: labelColor }, grid: { color: gridColor } },
                                y: {
                                    ticks: {
                                        color: labelColor,
                                        callback: v => 'Rp ' + (v / 1000000).toFixed(1) + 'jt',
                                    },
                                    grid: { color: gridColor },
                                },
                            },
                        },
                    });
                }

                // ── Category Donut Chart ───────────────────────
                @php $byCategoryJson = json_encode($byCategory); @endphp
                const catData = {!! $byCategoryJson !!};
                const categoryCtx = document.getElementById('categoryChart');
                if (categoryCtx && catData.length > 0) {
                    new Chart(categoryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: catData.map(d => d.category),
                            datasets: [{
                                data: catData.map(d => d.total),
                                backgroundColor: [
                                    '#10B981', '#F59E0B', '#3B82F6',
                                    '#8B5CF6', '#F43F5E', '#64748B', '#06B6D4',
                                ],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: labelColor, font: { size: 11 }, padding: 12 },
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => ' Rp ' + ctx.parsed.toLocaleString('id-ID'),
                                    },
                                },
                            },
                        },
                    });
                }
            });
        </script>
    @endpush

</x-filament-panels::page>
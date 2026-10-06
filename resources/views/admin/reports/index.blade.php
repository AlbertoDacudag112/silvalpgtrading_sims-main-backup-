<x-app-layout title="Reports" header="Reports">

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
        </div>
        <button type="submit" class="bg-maroon-600 hover:bg-maroon-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Run Report</button>
        <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}"
            class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
            </svg>
            Export to PDF
        </a>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-stat-card label="Total Sales" value="₱{{ number_format($totalSales, 2) }}" accent="green">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m0-8c-1.11 0-2.08.402-2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-stat-card>
        <x-stat-card label="Total Expenses" value="₱{{ number_format($totalExpenses, 2) }}" accent="yellow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l-1 11H6L5 9z" />
            </svg>
        </x-stat-card>
        <x-stat-card label="Net Income" value="₱{{ number_format($netIncome, 2) }}" accent="{{ $netIncome >= 0 ? 'green' : 'red' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
        </x-stat-card>
    </div>

    @if(empty($salesByDay) && empty($expensesByDay) && empty($paymentMethodSplit) && empty($expensesByCategory))
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400 text-sm">
        No sales or expenses recorded between {{ \Carbon\Carbon::parse($from)->format('M j, Y') }} and {{ \Carbon\Carbon::parse($to)->format('M j, Y') }}.
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-900 mb-4">Sales vs Expenses</h2>
            <canvas id="salesChart" height="110"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-900 mb-4">Payment Method Split</h2>
            <canvas id="paymentChart" height="180"></canvas>
        </div>
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-900 mb-4">Expenses by Category</h2>
            <canvas id="categoryChart" height="90"></canvas>
        </div>
    </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js failed to load — check network access to cdnjs.cloudflare.com.');
                return;
            }

            const maroon = '#7a1f24';
            const green = '#16a34a';
            const amber = '#b45309';

            const salesByDay = @json($salesByDay);
            const expensesByDay = @json($expensesByDay);
            const dateSet = [...new Set([...salesByDay.map(d => d.date), ...expensesByDay.map(d => d.date)])].sort();

            const salesCanvas = document.getElementById('salesChart');
            if (salesCanvas && dateSet.length > 0) {
                new Chart(salesCanvas, {
                    type: 'line',
                    data: {
                        labels: dateSet,
                        datasets: [{
                                label: 'Sales',
                                data: dateSet.map(d => Number((salesByDay.find(s => s.date === d) || {}).total || 0)),
                                borderColor: maroon,
                                backgroundColor: maroon,
                                tension: 0.3,
                            },
                            {
                                label: 'Expenses',
                                data: dateSet.map(d => Number((expensesByDay.find(s => s.date === d) || {}).total || 0)),
                                borderColor: amber,
                                backgroundColor: amber,
                                tension: 0.3,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    },
                });
            }

            const paymentSplit = @json($paymentMethodSplit);
            const paymentCanvas = document.getElementById('paymentChart');
            if (paymentCanvas && paymentSplit.length > 0) {
                new Chart(paymentCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: paymentSplit.map(p => p.payment_method.toUpperCase()),
                        datasets: [{
                            data: paymentSplit.map(p => Number(p.total)),
                            backgroundColor: [maroon, green]
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    },
                });
            }

            const byCategory = @json($expensesByCategory);
            const categoryCanvas = document.getElementById('categoryChart');
            if (categoryCanvas && byCategory.length > 0) {
                new Chart(categoryCanvas, {
                    type: 'bar',
                    data: {
                        labels: byCategory.map(c => c.category),
                        datasets: [{
                            label: 'Amount',
                            data: byCategory.map(c => Number(c.total)),
                            backgroundColor: maroon
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    },
                });
            }
        });
    </script>

</x-app-layout>
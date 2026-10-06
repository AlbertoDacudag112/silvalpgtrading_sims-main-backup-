<x-app-layout title="Dashboard" header="Cashier Dashboard">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        <x-stat-card label="Today's Sales" value="₱{{ number_format($todaySales, 2) }}" accent="maroon">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m0-8c-1.11 0-2.08.402-2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-stat-card>
        <x-stat-card label="Pending / In-Transit" value="{{ $pendingOrders }}" accent="yellow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-stat-card>
        <x-stat-card label="Products Needing Restock" value="{{ $lowStockProducts->count() }}" accent="{{ $lowStockProducts->isNotEmpty() ? 'red' : 'green' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </x-stat-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-1 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h2 class="font-semibold text-gray-900 mb-4">Inventory Status</h2>
            <div class="space-y-4">
                @foreach($products as $product)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-800">{{ $product->name }}</span>
                        <span class="text-gray-500">{{ $product->current_stock }}/{{ $product->max_capacity }}</span>
                    </div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $product->stock_status === 'green' ? 'bg-status-success' : ($product->stock_status === 'yellow' ? 'bg-status-warning' : 'bg-status-danger') }}"
                            style="width: {{ max($product->stock_percent, 4) }}%"></div>
                    </div>
                    <x-badge :color="$product->stock_status" :label="$product->stock_label" class="mt-1.5" />
                </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-900">Recent Orders</h2>
                <a href="{{ route('cashier.orders.index') }}" class="text-xs text-maroon-600 hover:underline">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-100">
                            <th class="py-2 pr-3 font-medium">Order</th>
                            <th class="py-2 pr-3 font-medium">Customer</th>
                            <th class="py-2 pr-3 font-medium">Total</th>
                            <th class="py-2 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-2.5 pr-3"><a href="{{ route('cashier.orders.show', $order) }}" class="text-maroon-600 font-medium hover:underline">{{ $order->order_code }}</a></td>
                            <td class="py-2.5 pr-3 text-gray-700">{{ $order->customer->name }}</td>
                            <td class="py-2.5 pr-3 text-gray-700">₱{{ number_format($order->grand_total, 2) }}</td>
                            <td class="py-2.5"><x-badge :color="$order->status_color" :label="$order->status_label" /></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-400">No orders yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('cashier.orders.create') }}" class="inline-flex items-center gap-2 bg-maroon-600 hover:bg-maroon-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Take New Order
        </a>
    </div>

</x-app-layout>

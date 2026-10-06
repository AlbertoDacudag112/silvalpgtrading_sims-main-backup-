<x-app-layout title="Inventory" header="Inventory">

    @php $low = $products->filter(fn($p) => $p->stock_status !== 'green'); @endphp

    @if($low->isNotEmpty())
    <div class="mb-5 bg-status-warning-bg border border-yellow-200 text-status-warning text-sm font-medium px-4 py-3 rounded-lg">
        ⚠ {{ $low->count() }} product(s) at or below the reorder level — restock soon.
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($products as $product)
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-400">Gas ₱{{ number_format($product->gas_price, 2) }} · Cylinder fee ₱{{ number_format($product->cylinder_fee, 2) }}</p>
                </div>
                <x-badge :color="$product->stock_status" :label="$product->stock_label" />
            </div>

            <div class="flex justify-between text-sm text-gray-600 mb-1 mt-3">
                <span>{{ $product->current_stock }} / {{ $product->max_capacity }} cylinders</span>
                <span class="text-gray-400">reorder at {{ $product->reorder_level }}</span>
            </div>
            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full {{ $product->stock_status === 'green' ? 'bg-status-success' : ($product->stock_status === 'yellow' ? 'bg-status-warning' : 'bg-status-danger') }}"
                     style="width: {{ max($product->stock_percent, 4) }}%"></div>
            </div>

            <form method="POST" action="{{ route('cashier.inventory.restock', $product) }}" class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                @csrf
                <input type="number" name="quantity" min="1" placeholder="Qty received" required
                       class="w-32 rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-2 rounded-lg">Record Restock</button>
            </form>
        </div>
        @endforeach
    </div>

</x-app-layout>

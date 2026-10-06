<x-app-layout title="Inventory" header="Inventory">

    @php $low = $products->filter(fn($p) => $p->stock_status !== 'green'); @endphp

    @if($low->isNotEmpty())
    <div class="mb-5 bg-status-warning-bg border border-yellow-200 text-status-warning text-sm font-medium px-4 py-3 rounded-lg">
        ⚠ {{ $low->count() }} product(s) at or below the {{ $products->first()->reorder_level ?? 20 }}-cylinder reorder level — restock soon.
    </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.inventory.create') }}" class="inline-flex items-center gap-2 bg-maroon-600 hover:bg-maroon-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Add Product
        </a>
    </div>

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

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.inventory.restock', $product) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="number" name="quantity" min="1" placeholder="Qty" required
                           class="w-20 rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-2 rounded-lg">Restock</button>
                </form>
                <a href="{{ route('admin.inventory.logs', $product) }}" class="text-xs text-maroon-600 hover:underline">View logs →</a>
            </div>
        </div>
        @endforeach
    </div>

</x-app-layout>

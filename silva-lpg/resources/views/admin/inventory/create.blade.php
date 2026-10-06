<x-app-layout title="Add Product" header="Add Product">

    <div class="max-w-lg bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.inventory.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Product name</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. LPG Cylinder 11kg"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Gas price (₱)</label>
                    <input type="number" step="0.01" min="0" name="gas_price" value="{{ old('gas_price') }}" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Cylinder fee (₱)</label>
                    <input type="number" step="0.01" min="0" name="cylinder_fee" value="{{ old('cylinder_fee', 1500) }}" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                    <p class="text-[11px] text-gray-400 mt-1">Charged only when customer has no cylinder to exchange.</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Starting stock</label>
                    <input type="number" min="0" name="current_stock" value="{{ old('current_stock', 0) }}" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Max capacity</label>
                    <input type="number" min="1" name="max_capacity" value="{{ old('max_capacity', 50) }}" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Reorder level</label>
                    <input type="number" min="0" name="reorder_level" value="{{ old('reorder_level', 20) }}" required
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit" class="bg-maroon-600 hover:bg-maroon-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow-sm">
                    Save Product
                </button>
                <a href="{{ route('admin.inventory.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-2 py-2.5">Cancel</a>
            </div>
        </form>
    </div>

</x-app-layout>

<x-app-layout title="Order {{ $order->order_code }}" header="Order {{ $order->order_code }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Items to deliver</h2>
                    <x-badge :color="$order->is_delayed ? 'yellow' : $order->status_color" :label="$order->is_delayed ? 'Delayed' : $order->status_label" />
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-100">
                            <th class="py-2 pr-3 font-medium">Product</th>
                            <th class="py-2 pr-3 font-medium">Qty</th>
                            <th class="py-2 font-medium text-right">Line total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr class="border-b border-gray-50">
                            <td class="py-2.5 pr-3 text-gray-800">{{ $item->product->name ?? 'Deleted product' }}</td>
                            <td class="py-2.5 pr-3 text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-2.5 text-right text-gray-800 font-medium">₱{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between text-base font-semibold text-gray-900">
                    <span>Grand total ({{ strtoupper($order->payment_method) }} — already paid)</span>
                    <span>₱{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>

            @if($order->status === 'out_for_delivery')
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">Complete Delivery</h2>
                <p class="text-xs text-gray-500 mb-4">Upload a photo proof (e.g. handed-off cylinder / signed receipt) to mark this order as delivered.</p>
                <form method="POST" action="{{ route('delivery.orders.complete', $order) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Proof photo</label>
                        <input type="file" name="proof_image" accept="image/*" required
                               class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-maroon-50 file:text-maroon-700 file:text-xs file:font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Remarks (optional)</label>
                        <input type="text" name="remarks" class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                    </div>
                    <button type="submit" class="w-full bg-status-success text-white text-sm font-medium px-4 py-2.5 rounded-lg hover:opacity-90">
                        Mark as Delivered
                    </button>
                </form>
            </div>
            @elseif($order->deliveryProof)
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">Proof of Delivery</h2>
                <img src="{{ asset('storage/' . $order->deliveryProof->proof_image_path) }}" alt="Delivery proof" class="rounded-lg border border-gray-200 max-h-80 object-cover">
                <p class="text-xs text-gray-400 mt-2">Completed {{ $order->deliveryProof->completed_at->format('M j, Y g:i A') }}</p>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm h-fit">
            <h2 class="font-semibold text-gray-900 mb-3">Customer</h2>
            <p class="text-sm text-gray-800">{{ $order->customer->name }}</p>
            <p class="text-sm text-gray-500">{{ $order->customer->phone }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $order->delivery_address }}</p>
            <div class="text-xs text-gray-400 mt-3 pt-3 border-t border-gray-100">Order date: {{ $order->order_date->format('M j, Y') }}</div>
        </div>
    </div>

</x-app-layout>

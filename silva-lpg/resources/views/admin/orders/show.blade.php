<x-app-layout title="Order {{ $order->order_code }}" header="Order {{ $order->order_code }}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900">Items</h2>
                    <x-badge :color="$order->is_delayed ? 'yellow' : $order->status_color" :label="$order->is_delayed ? 'Delayed' : $order->status_label" />
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-100">
                            <th class="py-2 pr-3 font-medium">Product</th>
                            <th class="py-2 pr-3 font-medium">Qty</th>
                            <th class="py-2 pr-3 font-medium">Cylinder</th>
                            <th class="py-2 pr-3 font-medium">Gas price</th>
                            <th class="py-2 pr-3 font-medium">Cyl. fee</th>
                            <th class="py-2 font-medium text-right">Line total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr class="border-b border-gray-50">
                            <td class="py-2.5 pr-3 text-gray-800">{{ $item->product->name ?? 'Deleted product' }}</td>
                            <td class="py-2.5 pr-3 text-gray-600">{{ $item->quantity }}</td>
                            <td class="py-2.5 pr-3 text-gray-600">{{ $item->has_own_cylinder ? 'Own (exchange)' : 'Provided by shop' }}</td>
                            <td class="py-2.5 pr-3 text-gray-600">₱{{ number_format($item->unit_gas_price, 2) }}</td>
                            <td class="py-2.5 pr-3 text-gray-600">₱{{ number_format($item->unit_cylinder_fee, 2) }}</td>
                            <td class="py-2.5 text-right text-gray-800 font-medium">₱{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 pt-4 border-t border-gray-100 space-y-1 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₱{{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-gray-600"><span>Cylinder fees</span><span>₱{{ number_format($order->cylinder_fee_total, 2) }}</span></div>
                    <div class="flex justify-between text-base font-semibold text-gray-900"><span>Grand total</span><span>₱{{ number_format($order->grand_total, 2) }}</span></div>
                </div>
            </div>

            @if($order->deliveryProof)
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">Proof of Delivery</h2>
                <img src="{{ asset('storage/' . $order->deliveryProof->proof_image_path) }}" alt="Delivery proof" class="rounded-lg border border-gray-200 max-h-80 object-cover">
                @if($order->deliveryProof->remarks)
                    <p class="text-sm text-gray-600 mt-3">"{{ $order->deliveryProof->remarks }}"</p>
                @endif
                <p class="text-xs text-gray-400 mt-2">Completed {{ $order->deliveryProof->completed_at->format('M j, Y g:i A') }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">Customer</h2>
                <p class="text-sm text-gray-800">{{ $order->customer->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->customer->phone }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $order->delivery_address }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-500">
                    Payment: <span class="capitalize font-medium text-gray-700">{{ $order->payment_method }}</span>
                    — <span class="text-status-success font-medium">Paid in full</span>
                </div>
                <div class="text-xs text-gray-400 mt-1">Order date: {{ $order->order_date->format('M j, Y') }}</div>
                @if($order->notes)
                    <p class="text-xs text-gray-500 mt-2 italic">{{ $order->notes }}</p>
                @endif
            </div>

            @if(in_array($order->status, ['pending']))
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">Assign Delivery</h2>
                <form method="POST" action="{{ route('admin.orders.assign', $order) }}" class="space-y-3">
                    @csrf
                    <select name="delivery_staff_id" required class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                        <option value="">Select delivery staff…</option>
                        @foreach($deliveryStaff as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-maroon-600 hover:bg-maroon-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
                        Assign &amp; Mark Out for Delivery
                    </button>
                </form>
            </div>
            @endif

            @if(!in_array($order->status, ['completed', 'cancelled']))
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-900 mb-3">Cancel Order</h2>
                <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
                      onsubmit="return confirm('Cancel this order and restock the items?');" class="space-y-3">
                    @csrf
                    <input type="text" name="reason" placeholder="Reason (optional)" class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon-500 focus:ring-maroon-500">
                    <button type="submit" class="w-full bg-status-danger-bg hover:bg-red-200 text-status-danger text-sm font-medium px-4 py-2.5 rounded-lg">
                        Cancel Order
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

</x-app-layout>

<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\DeliveryProof;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /** Delivery staff only ever see orders assigned to them. */
    public function index(Request $request)
    {
        $orders = Order::with('customer', 'deliveryProof')
            ->where('assigned_delivery_id', $request->user()->id)
            ->latest('order_date')
            ->paginate(15);

        return view('delivery.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->assigned_delivery_id === $request->user()->id, 403);
        $order->load('items.product', 'customer', 'deliveryProof');
        return view('delivery.orders.show', compact('order'));
    }

    /**
     * Validate / complete a delivery: requires a proof-of-completion image upload,
     * per the delivery staff's sole responsibility in the business process.
     */
    public function complete(Request $request, Order $order)
    {
        abort_unless($order->assigned_delivery_id === $request->user()->id, 403);

        if ($order->status !== 'out_for_delivery') {
            return back()->with('error', 'Only orders that are out for delivery can be marked complete.');
        }

        $validated = $request->validate([
            'proof_image' => 'required|image|max:4096',
            'remarks' => 'nullable|string|max:500',
        ]);

        $path = $request->file('proof_image')->store('delivery-proofs', 'public');

        DeliveryProof::create([
            'order_id' => $order->id,
            'delivery_staff_id' => $request->user()->id,
            'proof_image_path' => $path,
            'remarks' => $validated['remarks'] ?? null,
            'completed_at' => now(),
        ]);

        $order->update([
            'status' => 'completed',
            'delivered_at' => now(),
        ]);

        return redirect()->route('delivery.orders.index')
            ->with('success', "Order {$order->order_code} marked as delivered.");
    }
}

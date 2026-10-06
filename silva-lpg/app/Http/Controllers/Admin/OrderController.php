<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'deliveryStaff']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($search = $request->get('search')) {
            $query->whereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"))
                ->orWhere('order_code', 'like', "%{$search}%");
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'delivery_address' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,gcash',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.has_own_cylinder' => 'nullable|boolean',
        ]);

        $order = $this->orderService->createOrder(
            customerData: [
                'name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'],
                'address' => $validated['delivery_address'],
            ],
            items: $validated['items'],
            deliveryAddress: $validated['delivery_address'],
            paymentMethod: $validated['payment_method'],
            createdBy: $request->user()->id,
            notes: $validated['notes'] ?? null,
        );

        return redirect()->route('admin.orders.show', $order)
            ->with('success', "Order {$order->order_code} created successfully.");
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'customer', 'deliveryStaff', 'deliveryProof', 'creator']);
        $deliveryStaff = User::where('role', 'delivery')->where('is_active', true)->get();
        return view('admin.orders.show', compact('order', 'deliveryStaff'));
    }

    public function assign(Request $request, Order $order)
    {
        $validated = $request->validate(['delivery_staff_id' => 'required|exists:users,id']);
        $this->orderService->assignDelivery($order, $validated['delivery_staff_id']);
        return back()->with('success', 'Order assigned to delivery staff.');
    }

    public function cancel(Request $request, Order $order)
    {
        $validated = $request->validate(['reason' => 'nullable|string|max:255']);
        $this->orderService->cancelOrder($order, $request->user()->id, $validated['reason'] ?? null);
        return back()->with('success', 'Order cancelled and stock restored.');
    }
}

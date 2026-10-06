<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index()
    {
        $products = Product::withCount('orderItems')->orderBy('name')->get();
        return view('admin.inventory.index', compact('products'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'gas_price' => 'required|numeric|min:0',
            'cylinder_fee' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'max_capacity' => 'required|integer|min:1',
            'reorder_level' => 'required|integer|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('admin.inventory.index')->with('success', 'Product added.');
    }

    public function restock(Request $request, Product $product)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        $this->orderService->restock($product, $validated['quantity'], $request->user()->id);
        return back()->with('success', "Restocked {$product->name} by {$validated['quantity']} units.");
    }

    public function logs(Product $product)
    {
        $logs = $product->inventoryLogs()->with('creator', 'order')->latest()->paginate(20);
        return view('admin.inventory.logs', compact('product', 'logs'));
    }
}

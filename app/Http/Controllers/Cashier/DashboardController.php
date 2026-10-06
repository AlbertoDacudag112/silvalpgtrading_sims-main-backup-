<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Order::whereDate('order_date', today())
            ->where('status', '!=', 'cancelled')->sum('grand_total');

        $pendingOrders = Order::whereIn('status', ['pending', 'out_for_delivery'])->count();

        $products = Product::orderBy('current_stock')->get();
        $lowStockProducts = $products->filter(fn($p) => $p->stock_status !== 'green');

        $recentOrders = Order::with('customer')->latest()->take(8)->get();

        return view('cashier.dashboard', compact('todaySales', 'pendingOrders', 'products', 'lowStockProducts', 'recentOrders'));
    }
}

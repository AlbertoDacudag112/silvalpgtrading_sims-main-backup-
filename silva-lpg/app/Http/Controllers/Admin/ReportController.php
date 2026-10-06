<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from', now()->subDays(29)->toDateString());
        $to = $request->get('to', now()->toDateString());

        $salesByDay = Order::select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(grand_total) as total'))
            ->where('status', '!=', 'cancelled')
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('date')->orderBy('date')->get();

        $expensesByDay = Expense::select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('date')->orderBy('date')->get();

        $expensesByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('category')->get();

        $paymentMethodSplit = Order::select('payment_method', DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as count'))
            ->where('status', '!=', 'cancelled')
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('payment_method')->get();

        $totalSales = $salesByDay->sum('total');
        $totalExpenses = $expensesByDay->sum('total');
        $netIncome = $totalSales - $totalExpenses;

        return view('admin.reports.index', compact(
            'from', 'to', 'salesByDay', 'expensesByDay', 'expensesByCategory',
            'paymentMethodSplit', 'totalSales', 'totalExpenses', 'netIncome'
        ));
    }
}

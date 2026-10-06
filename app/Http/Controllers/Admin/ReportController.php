<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.index', $this->reportData($request));
    }

    public function export(Request $request)
    {
        $data = $this->reportData($request);

        $pdf = Pdf::loadView('admin.reports.pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download("silva-lpg-report-{$data['from']}-to-{$data['to']}.pdf");
    }

    private function reportData(Request $request): array
    {
        $from = $request->get('from', now()->subDays(29)->toDateString());
        $to = $request->get('to', now()->toDateString());

        $salesByDay = Order::select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(grand_total) as total'))
            ->where('status', '!=', 'cancelled')
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('date')->orderBy('date')->get()
            ->map(fn($row) => ['date' => (string) $row->date, 'total' => (float) $row->total])
            ->values()->all();

        $expensesByDay = Expense::select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('date')->orderBy('date')->get()
            ->map(fn($row) => ['date' => (string) $row->date, 'total' => (float) $row->total])
            ->values()->all();

        $expensesByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('expense_date', [$from, $to])
            ->groupBy('category')->get()
            ->map(fn($row) => ['category' => (string) $row->category, 'total' => (float) $row->total])
            ->values()->all();

        $paymentMethodSplit = Order::select('payment_method', DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as count'))
            ->where('status', '!=', 'cancelled')
            ->whereBetween('order_date', [$from, $to])
            ->groupBy('payment_method')->get()
            ->map(fn($row) => ['payment_method' => (string) $row->payment_method, 'total' => (float) $row->total, 'count' => (int) $row->count])
            ->values()->all();

        $totalSales = array_sum(array_column($salesByDay, 'total'));
        $totalExpenses = array_sum(array_column($expensesByDay, 'total'));
        $netIncome = $totalSales - $totalExpenses;

        return compact(
            'from',
            'to',
            'salesByDay',
            'expensesByDay',
            'expensesByCategory',
            'paymentMethodSplit',
            'totalSales',
            'totalExpenses',
            'netIncome'
        );
    }
}

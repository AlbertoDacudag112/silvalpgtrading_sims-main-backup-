<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1f2937; font-size: 11px; }
        h1 { font-size: 18px; color: #7a1f24; margin-bottom: 2px; }
        .subtitle { color: #6b7280; margin-bottom: 18px; font-size: 11px; }
        .summary { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .summary td { width: 33.33%; padding: 10px; border: 1px solid #e5e7eb; text-align: center; }
        .summary .label { display: block; font-size: 9px; color: #6b7280; text-transform: uppercase; margin-bottom: 4px; }
        .summary .value { display: block; font-size: 15px; font-weight: bold; color: #111827; }
        h2 { font-size: 13px; color: #7a1f24; border-bottom: 2px solid #7a1f24; padding-bottom: 4px; margin-top: 22px; margin-bottom: 8px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data th { background: #7a1f24; color: #ffffff; text-align: left; padding: 6px 8px; font-size: 10px; }
        table.data td { padding: 6px 8px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
        table.data tr:nth-child(even) td { background: #faf7f7; }
        .empty { color: #9ca3af; font-style: italic; padding: 10px 0; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Silva LPG Trading — Sales &amp; Expense Report</h1>
    <p class="subtitle">
        {{ \Carbon\Carbon::parse($from)->format('F j, Y') }} to {{ \Carbon\Carbon::parse($to)->format('F j, Y') }}
        &middot; Generated {{ now()->format('F j, Y g:i A') }}
    </p>

    <table class="summary">
        <tr>
            <td><span class="label">Total Sales</span><span class="value">₱{{ number_format($totalSales, 2) }}</span></td>
            <td><span class="label">Total Expenses</span><span class="value">₱{{ number_format($totalExpenses, 2) }}</span></td>
            <td><span class="label">Net Income</span><span class="value">₱{{ number_format($netIncome, 2) }}</span></td>
        </tr>
    </table>

    <h2>Sales by Day</h2>
    @if(count($salesByDay))
    <table class="data">
        <thead><tr><th>Date</th><th>Sales</th></tr></thead>
        <tbody>
        @foreach($salesByDay as $row)
            <tr><td>{{ \Carbon\Carbon::parse($row['date'])->format('M j, Y') }}</td><td>₱{{ number_format($row['total'], 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">No sales recorded in this range.</p>
    @endif

    <h2>Expenses by Day</h2>
    @if(count($expensesByDay))
    <table class="data">
        <thead><tr><th>Date</th><th>Expenses</th></tr></thead>
        <tbody>
        @foreach($expensesByDay as $row)
            <tr><td>{{ \Carbon\Carbon::parse($row['date'])->format('M j, Y') }}</td><td>₱{{ number_format($row['total'], 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">No expenses recorded in this range.</p>
    @endif

    <h2>Expenses by Category</h2>
    @if(count($expensesByCategory))
    <table class="data">
        <thead><tr><th>Category</th><th>Amount</th></tr></thead>
        <tbody>
        @foreach($expensesByCategory as $row)
            <tr><td>{{ $row['category'] }}</td><td>₱{{ number_format($row['total'], 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">No expenses recorded in this range.</p>
    @endif

    <h2>Payment Method Split</h2>
    @if(count($paymentMethodSplit))
    <table class="data">
        <thead><tr><th>Method</th><th>Orders</th><th>Total</th></tr></thead>
        <tbody>
        @foreach($paymentMethodSplit as $row)
            <tr><td>{{ strtoupper($row['payment_method']) }}</td><td>{{ $row['count'] }}</td><td>₱{{ number_format($row['total'], 2) }}</td></tr>
        @endforeach
        </tbody>
    </table>
    @else
    <p class="empty">No paid orders recorded in this range.</p>
    @endif

    <p class="footer">Silva LPG Trading — Order, Sales, Expense, and Inventory Management System</p>
</body>
</html>
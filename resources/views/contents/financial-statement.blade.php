@extends('layouts.app')

@section('title', 'Financial Statement')

@section('content')
<style>
    .fs-header {
        margin-bottom: 2rem;
    }
    .fs-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    /* Summary Cards Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .summary-label {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .summary-label i {
        font-size: 1rem;
    }

    .summary-card.revenue .summary-label i { color: #16a34a; }
    .summary-card.expense .summary-label i { color: #dc2626; }
    .summary-card.net .summary-label i { color: #03255b; }
    .summary-card.liability .summary-label i { color: #d97706; }
    .summary-card.outstanding .summary-label i { color: #2563eb; }

    .summary-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
    }

    .summary-card.revenue .summary-value { color: #16a34a; }
    .summary-card.expense .summary-value { color: #dc2626; }
    .summary-card.net.positive .summary-value { color: #16a34a; }
    .summary-card.net.negative .summary-value { color: #dc2626; }

    /* Section Cards */
    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .section-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .section-body {
        padding: 1.5rem;
    }

    /* Breakdown Table */
    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
    }

    .breakdown-table tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .breakdown-table tr:last-child {
        border-bottom: none;
    }

    .breakdown-table td {
        padding: 0.75rem 0;
        font-size: 0.9rem;
    }

    .breakdown-table td:first-child {
        color: #475569;
    }

    .breakdown-table td:last-child {
        text-align: right;
        font-weight: 600;
        color: #1e293b;
    }

    .breakdown-table tr.total {
        border-top: 2px solid #e2e8f0;
        font-weight: 700;
    }

    .breakdown-table tr.total td:last-child {
        font-size: 1.1rem;
    }

    .amount-positive { color: #16a34a !important; }
    .amount-negative { color: #dc2626 !important; }

    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .kpi-card {
        background: #f8fafc;
        border-radius: 10px;
        padding: 1.25rem;
        text-align: center;
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
    }

    .kpi-label {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    /* Monthly Trends Chart */
    .trends-chart {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        height: 200px;
        gap: 0.5rem;
        padding: 1rem 0;
        border-bottom: 2px solid #e2e8f0;
    }

    .trend-bar-group {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }

    .trend-bars {
        display: flex;
        gap: 3px;
        align-items: flex-end;
        height: 150px;
    }

    .trend-bar {
        width: 12px;
        border-radius: 3px 3px 0 0;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 2px;
    }

    .trend-bar:hover {
        opacity: 0.8;
        transform: scaleY(1.02);
    }

    .trend-bar.income { background: linear-gradient(180deg, #22c55e, #16a34a); }
    .trend-bar.expense { background: linear-gradient(180deg, #ef4444, #dc2626); }

    .trend-label {
        font-size: 0.7rem;
        color: #64748b;
        font-weight: 600;
    }

    .trend-net {
        font-size: 0.65rem;
        font-weight: 700;
    }

    .trend-net.positive { color: #16a34a; }
    .trend-net.negative { color: #dc2626; }

    .trends-legend {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 1rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: #64748b;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
    }

    .legend-color.income { background: #22c55e; }
    .legend-color.expense { background: #ef4444; }

    /* Filters */
    .filters-card {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
    }

    .filter-select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        min-width: 150px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    /* Receivables Table */
    .receivables-table {
        width: 100%;
        border-collapse: collapse;
    }

    .receivables-table thead {
        background: #f8fafc;
    }

    .receivables-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .receivables-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    .receivables-table tr:hover {
        background: #f8fafc;
    }

    .badge-overdue {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #fee2e2;
        color: #dc2626;
    }

    .row-gap { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .row-gap > div { flex: 1; min-width: 300px; }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="fs-header d-flex justify-content-between align-items-center">
        <h1 class="fs-title"><i class="bi bi-bar-chart-line me-2"></i>Financial Statement</h1>
        <a href="{{ route('financial-statement.export', ['date_filter' => $dateFilter]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-file-pdf"></i> PDF
        </a>
    </div>

    <!-- Date Filter -->
    <form method="GET" action="{{ route('financial-statement') }}" class="filters-card">
        <div class="filter-group">
            <label class="filter-label">Period:</label>
            <select name="date_filter" class="filter-select" onchange="this.form.submit()">
                <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_quarter" {{ $dateFilter == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                <option value="this_year" {{ $dateFilter == 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="all" {{ $dateFilter == 'all' ? 'selected' : '' }}>All Time</option>
            </select>
            @if($dateFrom && $dateTo)
                <span class="text-muted ms-2">{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</span>
            @endif
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="summary-grid">
        <div class="summary-card revenue">
            <div class="summary-label"><i class="bi bi-graph-up-arrow"></i> Total Revenue</div>
            <div class="summary-value">₱{{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="summary-card expense">
            <div class="summary-label"><i class="bi bi-graph-down-arrow"></i> Total Expenses</div>
            <div class="summary-value">₱{{ number_format($totalExpensesAndRefunds, 2) }}</div>
        </div>
        <div class="summary-card net {{ $netIncome >= 0 ? 'positive' : 'negative' }}">
            <div class="summary-label"><i class="bi bi-cash-stack"></i> Net Income</div>
            <div class="summary-value">{{ $netIncome >= 0 ? '' : '-' }}₱{{ number_format(abs($netIncome), 2) }}</div>
        </div>
        <div class="summary-card liability">
            <div class="summary-label"><i class="bi bi-shield-check"></i> Security Deposits Held</div>
            <div class="summary-value">₱{{ number_format($securityDepositsHeld, 2) }}</div>
        </div>
        <div class="summary-card outstanding">
            <div class="summary-label"><i class="bi bi-hourglass-split"></i> Outstanding Balance</div>
            <div class="summary-value">₱{{ number_format($outstandingBalance, 2) }}</div>
        </div>
    </div>

    <!-- Income & Expense Breakdown -->
    <div class="row-gap">
        <div>
            <div class="section-card">
                <div class="section-header">
                    <i class="bi bi-arrow-up-circle text-success"></i>
                    <h3 class="section-title">Income Breakdown</h3>
                </div>
                <div class="section-body">
                    <table class="breakdown-table">
                        <tr>
                            <td>Rent Income</td>
                            <td class="amount-positive">₱{{ number_format($rentIncome, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Electricity Fees</td>
                            <td class="amount-positive">₱{{ number_format($electricityIncome, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Forfeited Deposits</td>
                            <td class="amount-positive">₱{{ number_format($forfeitedDeposits, 2) }}</td>
                        </tr>
                        <tr class="total">
                            <td>Total Revenue</td>
                            <td class="amount-positive">₱{{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <div class="section-card">
                <div class="section-header">
                    <i class="bi bi-arrow-down-circle text-danger"></i>
                    <h3 class="section-title">Expense Breakdown</h3>
                </div>
                <div class="section-body">
                    <table class="breakdown-table">
                        @foreach($expensesByCategory as $category => $amount)
                        <tr>
                            <td>{{ $category }}</td>
                            <td class="amount-negative">₱{{ number_format($amount, 2) }}</td>
                        </tr>
                        @endforeach
                        @if(count($expensesByCategory) == 0)
                        <tr>
                            <td colspan="2" class="text-muted text-center">No expenses recorded</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Security Deposit Refunds</td>
                            <td class="amount-negative">₱{{ number_format($totalRefunds, 2) }}</td>
                        </tr>
                        <tr class="total">
                            <td>Total Expenses</td>
                            <td class="amount-negative">₱{{ number_format($totalExpensesAndRefunds, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="section-card">
        <div class="section-header">
            <i class="bi bi-speedometer2 text-primary"></i>
            <h3 class="section-title">Key Performance Indicators</h3>
        </div>
        <div class="section-body">
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($collectionRate, 1) }}%</div>
                    <div class="kpi-label">Collection Rate</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ number_format($occupancyRate, 1) }}%</div>
                    <div class="kpi-label">Occupancy Rate</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">{{ $occupiedRooms }}/{{ $totalRooms }}</div>
                    <div class="kpi-label">Rooms Occupied</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">₱{{ number_format($totalCollected, 2) }}</div>
                    <div class="kpi-label">Total Collected</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="section-card">
        <div class="section-header">
            <i class="bi bi-graph-up text-info"></i>
            <h3 class="section-title">Monthly Trends (Last 12 Months)</h3>
        </div>
        <div class="section-body">
            <div class="trends-chart">
                @foreach($monthlyTrends as $trend)
                <div class="trend-bar-group">
                    <div class="trend-bars">
                        @php
                            $maxVal = max(max(array_column($monthlyTrends, 'income')), max(array_column($monthlyTrends, 'expenses')), 1);
                            $incomeHeight = ($trend['income'] / $maxVal) * 100;
                            $expenseHeight = ($trend['expenses'] / $maxVal) * 100;
                        @endphp
                        <div class="trend-bar income" style="height: {{ $incomeHeight }}%;" title="Income: ₱{{ number_format($trend['income'], 2) }}"></div>
                        <div class="trend-bar expense" style="height: {{ $expenseHeight }}%;" title="Expenses: ₱{{ number_format($trend['expenses'], 2) }}"></div>
                    </div>
                    <div class="trend-label">{{ substr($trend['month'], 0, 3) }}</div>
                    <div class="trend-net {{ $trend['net'] >= 0 ? 'positive' : 'negative' }}">
                        {{ $trend['net'] >= 0 ? '+' : '' }}₱{{ number_format(abs($trend['net']), 0) }}
                    </div>
                </div>
                @endforeach
            </div>
            <div class="trends-legend">
                <span class="legend-item"><span class="legend-color income"></span> Income</span>
                <span class="legend-item"><span class="legend-color expense"></span> Expenses</span>
            </div>
        </div>
    </div>

    <!-- Outstanding Receivables -->
    <div class="section-card">
        <div class="section-header">
            <i class="bi bi-exclamation-triangle text-warning"></i>
            <h3 class="section-title">Outstanding Receivables</h3>
        </div>
        <div class="section-body p-0">
            <table class="receivables-table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Room</th>
                        <th>Invoice</th>
                        <th>Total Due</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Days Overdue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receivables as $invoice)
                    <tr>
                        <td>{{ optional(optional($invoice->booking)->tenant)->name ?? '—' }}</td>
                        <td>{{ optional(optional($invoice->booking)->room)->room_num ?? '—' }}</td>
                        <td>#{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>₱{{ number_format($invoice->total_due, 2) }}</td>
                        <td>₱{{ number_format($invoice->paid ?? 0, 2) }}</td>
                        <td class="amount-negative">₱{{ number_format($invoice->remaining_balance, 2) }}</td>
                        <td>
                            @if($invoice->days_overdue > 0)
                                <span class="badge-overdue">{{ $invoice->days_overdue }} days</span>
                            @else
                                <span class="text-muted">Not due</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No outstanding receivables</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

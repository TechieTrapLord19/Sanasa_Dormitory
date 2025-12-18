<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Statement - Sanasa Dormitory</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background: white;
        }

        .page {
            padding: 15mm 20mm;
        }

        /* ============ HEADER ============ */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-bottom: 3px solid #03255b;
            padding-bottom: 15px;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            text-align: right;
        }

        .company-name {
            font-size: 28px;
            font-weight: 800;
            color: #03255b;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .company-tagline {
            font-size: 11px;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 700;
            color: #03255b;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 6px;
            display: inline-block;
        }

        .report-meta {
            margin-top: 8px;
            font-size: 10px;
            color: #64748b;
        }

        /* ============ SECTIONS ============ */
        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #03255b;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ============ SUMMARY GRID ============ */
        .summary-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 20px;
        }

        .summary-row {
            display: table-row;
        }

        .summary-card {
            display: table-cell;
            padding: 12px 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .summary-card:first-child {
            border-radius: 6px 0 0 6px;
        }

        .summary-card:last-child {
            border-radius: 0 6px 6px 0;
        }

        .summary-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .summary-value.positive { color: #16a34a; }
        .summary-value.negative { color: #dc2626; }

        /* ============ TWO COLUMN ============ */
        .two-col {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .col {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }

        .col:last-child {
            padding-right: 0;
            padding-left: 10px;
        }

        .col-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }

        .col-title {
            font-size: 10px;
            font-weight: 700;
            color: #03255b;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .item-row {
            display: table;
            width: 100%;
            padding: 6px 0;
            border-bottom: 1px dotted #e2e8f0;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-label {
            display: table-cell;
            width: 65%;
            color: #475569;
        }

        .item-value {
            display: table-cell;
            width: 35%;
            text-align: right;
            font-weight: 600;
            color: #1e293b;
        }

        .item-row.total {
            border-top: 2px solid #1e293b;
            margin-top: 8px;
            padding-top: 8px;
        }

        .item-row.total .item-label,
        .item-row.total .item-value {
            font-weight: 700;
            font-size: 12px;
        }

        /* ============ KPI GRID ============ */
        .kpi-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .kpi-card {
            display: table-cell;
            text-align: center;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .kpi-value {
            font-size: 20px;
            font-weight: 700;
            color: #03255b;
        }

        .kpi-label {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }

        /* ============ TABLE ============ */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table thead {
            background: #03255b;
        }

        table.data-table th {
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }

        table.data-table td {
            padding: 8px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        table.data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        /* ============ FOOTER ============ */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e2e8f0;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
        }

        .footer-text {
            font-size: 9px;
            color: #64748b;
        }

        .signature-line {
            border-top: 1px solid #1e293b;
            width: 150px;
            margin-bottom: 5px;
            margin-top: 25px;
        }

        .signature-label {
            font-size: 9px;
            color: #64748b;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">SANASA DORMITORY</div>
                <div class="company-tagline">Student Accommodation & Housing Management</div>
            </div>
            <div class="header-right">
                <div class="report-title">FINANCIAL STATEMENT</div>
                <div class="report-meta">
                    <strong>Period:</strong> {{ $periodLabel }}<br>
                    <strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}<br>
                    <strong>Generated By:</strong> {{ $generatedBy }}
                </div>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="section">
            <div class="section-title">Financial Summary</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-card">
                        <div class="summary-label">Total Revenue</div>
                        <div class="summary-value positive">P{{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Total Expenses</div>
                        <div class="summary-value negative">P{{ number_format($totalExpensesAndRefunds, 2) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Net Income</div>
                        <div class="summary-value {{ $netIncome >= 0 ? 'positive' : 'negative' }}">
                            {{ $netIncome >= 0 ? '' : '-' }}P{{ number_format(abs($netIncome), 2) }}
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Deposits Held</div>
                        <div class="summary-value">P{{ number_format($securityDepositsHeld, 2) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Outstanding</div>
                        <div class="summary-value">P{{ number_format($outstandingBalance, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INCOME & EXPENSE BREAKDOWN -->
        <div class="two-col">
            <div class="col">
                <div class="col-box">
                    <div class="col-title">Income Breakdown</div>
                    <div class="item-row">
                        <span class="item-label">Rent Income</span>
                        <span class="item-value">P{{ number_format($rentIncome, 2) }}</span>
                    </div>
                    <div class="item-row">
                        <span class="item-label">Electricity Fees</span>
                        <span class="item-value">P{{ number_format($electricityIncome, 2) }}</span>
                    </div>
                    <div class="item-row">
                        <span class="item-label">Forfeited Deposits</span>
                        <span class="item-value">P{{ number_format($forfeitedDeposits, 2) }}</span>
                    </div>
                    <div class="item-row total">
                        <span class="item-label">Total Revenue</span>
                        <span class="item-value">P{{ number_format($totalRevenue, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="col-box">
                    <div class="col-title">Expense Breakdown</div>
                    @foreach($expensesByCategory as $category => $amount)
                    <div class="item-row">
                        <span class="item-label">{{ $category }}</span>
                        <span class="item-value">P{{ number_format($amount, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="item-row">
                        <span class="item-label">Security Deposit Refunds</span>
                        <span class="item-value">P{{ number_format($totalRefunds, 2) }}</span>
                    </div>
                    <div class="item-row total">
                        <span class="item-label">Total Expenses</span>
                        <span class="item-value">P{{ number_format($totalExpensesAndRefunds, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPIs -->
        <div class="section">
            <div class="section-title">Key Performance Indicators</div>
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
                    <div class="kpi-value">P{{ number_format($totalCollected, 2) }}</div>
                    <div class="kpi-label">Total Collected</div>
                </div>
            </div>
        </div>

        <!-- RECEIVABLES -->
        @if(count($receivables) > 0)
        <div class="section">
            <div class="section-title">Outstanding Receivables</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Room</th>
                        <th>Invoice</th>
                        <th class="text-right">Total Due</th>
                        <th class="text-right">Balance</th>
                        <th class="text-right">Days Overdue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receivables as $invoice)
                    <tr>
                        <td>{{ optional(optional($invoice->booking)->tenant)->name ?? '—' }}</td>
                        <td>{{ optional(optional($invoice->booking)->room)->room_num ?? '—' }}</td>
                        <td>#{{ str_pad($invoice->invoice_id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-right">P{{ number_format($invoice->total_due, 2) }}</td>
                        <td class="text-right font-bold">P{{ number_format($invoice->remaining_balance, 2) }}</td>
                        <td class="text-right">{{ $invoice->days_overdue > 0 ? $invoice->days_overdue . ' days' : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-left">
                <div class="footer-text">
                    <strong>Sanasa Dormitory Management System</strong><br>
                    This is a computer-generated report.<br>
                    © {{ now()->year }} All Rights Reserved
                </div>
            </div>
            <div class="footer-right">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature / Date</div>
            </div>
        </div>
    </div>
</body>
</html>

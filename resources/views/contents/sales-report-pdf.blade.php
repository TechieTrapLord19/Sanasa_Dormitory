<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report - Sanasa Dormitory</title>
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
            position: relative;
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

        /* ============ SUMMARY CARDS ============ */
        .summary-section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #03255b;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e2e8f0;
        }

        .summary-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .summary-row {
            display: table-row;
        }

        .summary-card {
            display: table-cell;
            padding: 12px 15px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            text-align: center;
            vertical-align: top;
        }

        .summary-card:first-child {
            border-radius: 8px 0 0 8px;
        }

        .summary-card:last-child {
            border-radius: 0 8px 8px 0;
        }

        .summary-card.highlight {
            background: linear-gradient(135deg, #03255b 0%, #1e40af 100%);
            color: white;
        }

        .summary-card.highlight .summary-label {
            color: rgba(255, 255, 255, 0.8);
        }

        .summary-card.highlight .summary-value {
            color: white;
        }

        .summary-card.success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-color: #10b981;
        }

        .summary-card.success .summary-value {
            color: #059669;
        }

        .summary-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 800;
            color: #03255b;
            line-height: 1.2;
        }

        .summary-subtext {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 3px;
        }

        /* ============ BREAKDOWN SECTION ============ */
        .breakdown-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .breakdown-col {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
            vertical-align: top;
        }

        .breakdown-col:last-child {
            padding-right: 0;
            padding-left: 15px;
        }

        .breakdown-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }

        .breakdown-title {
            font-size: 11px;
            font-weight: 700;
            color: #03255b;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }

        .breakdown-item {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px dotted #e2e8f0;
        }

        .breakdown-item:last-child {
            border-bottom: none;
        }

        .breakdown-label {
            display: table-cell;
            width: 60%;
            font-weight: 500;
            color: #475569;
        }

        .breakdown-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-weight: 700;
            color: #03255b;
        }

        .breakdown-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #03255b;
            font-size: 13px;
        }

        .breakdown-total .breakdown-label {
            font-weight: 700;
            color: #03255b;
        }

        .breakdown-total .breakdown-value {
            font-size: 14px;
        }

        /* ============ TRANSACTIONS TABLE ============ */
        .transactions-section {
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: linear-gradient(135deg, #03255b 0%, #1e40af 100%);
        }

        th {
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-radius: 6px 0 0 0;
        }

        th:last-child {
            border-radius: 0 6px 0 0;
            text-align: right;
        }

        td {
            padding: 10px 8px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .td-amount {
            text-align: right;
            font-weight: 700;
            color: #03255b;
            font-size: 11px;
        }

        .td-date {
            color: #64748b;
            font-weight: 500;
        }

        .td-tenant {
            font-weight: 600;
            color: #1e293b;
        }

        .td-room {
            background: #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 9px;
            display: inline-block;
        }

        .td-type {
            font-size: 9px;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        .type-rent {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .type-deposit {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .type-deduction {
            background: #fef3c7;
            color: #d97706;
        }

        .type-refund {
            background: #dcfce7;
            color: #16a34a;
        }

        .td-method {
            font-size: 9px;
            color: #64748b;
        }

        /* Total Row */
        .total-row {
            background: linear-gradient(135deg, #03255b 0%, #1e40af 100%) !important;
        }

        .total-row td {
            color: white;
            font-weight: 700;
            padding: 12px 8px;
            border-bottom: none;
        }

        .total-row td:last-child {
            font-size: 14px;
        }

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
            vertical-align: top;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .footer-text {
            font-size: 9px;
            color: #64748b;
            line-height: 1.6;
        }

        .signature-area {
            margin-top: 30px;
        }

        .signature-line {
            border-top: 1px solid #1e293b;
            width: 180px;
            margin-bottom: 5px;
        }

        .signature-label {
            font-size: 9px;
            color: #64748b;
        }

        /* ============ PAGE BREAK ============ */
        .page-break {
            page-break-after: always;
        }

        /* ============ EMPTY STATE ============ */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }

        /* ============ NOTES ============ */
        .notes-box {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 12px 15px;
            margin-top: 15px;
        }

        .notes-title {
            font-size: 10px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 5px;
        }

        .notes-text {
            font-size: 9px;
            color: #78350f;
            line-height: 1.5;
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
                <div class="report-title">SALES REPORT</div>
                <div class="report-meta">
                    <strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}<br>
                    <strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="summary-section">
            <div class="section-title">Financial Summary</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-card highlight">
                        <div class="summary-label">Total Revenue</div>
                        <div class="summary-value">P{{ number_format($totalSales, 2) }}</div>
                        <div class="summary-subtext">Rent/Utility + Deductions</div>
                    </div>
                    <div class="summary-card success">
                        <div class="summary-label">Security Deposits</div>
                        <div class="summary-value">P{{ number_format($totalSecurityDeposits ?? 0, 2) }}</div>
                        <div class="summary-subtext">Tenant Funds (Liability)</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Total Transactions</div>
                        <div class="summary-value">{{ number_format($totalTransactions) }}</div>
                        <div class="summary-subtext">Payment Records</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Average Transaction</div>
                        <div class="summary-value">P{{ $totalTransactions > 0 ? number_format(($totalSales + ($totalSecurityDeposits ?? 0)) / $totalTransactions, 2) : '0.00' }}</div>
                        <div class="summary-subtext">Per Payment</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BREAKDOWN SECTION -->
        <div class="breakdown-grid">
            <div class="breakdown-col">
                <div class="breakdown-box">
                    <div class="breakdown-title">Revenue by Payment Type</div>
                    @if($salesByType->isEmpty())
                        <div class="empty-state">No data available</div>
                    @else
                        @php
                            $grandTotal = $salesByType->sum();
                        @endphp
                        @foreach($salesByType as $type => $amount)
                            <div class="breakdown-item">
                                <span class="breakdown-label">{{ $type }}</span>
                                <span class="breakdown-value">P{{ number_format($amount, 2) }}</span>
                            </div>
                        @endforeach
                        <div class="breakdown-item breakdown-total">
                            <span class="breakdown-label">GRAND TOTAL</span>
                            <span class="breakdown-value">P{{ number_format($grandTotal, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="breakdown-col">
                <div class="breakdown-box">
                    <div class="breakdown-title">Payment Methods Used</div>
                    @php
                        $paymentMethods = $payments->groupBy('payment_method')->map(function($group) {
                            return [
                                'count' => $group->count(),
                                'total' => $group->sum('amount')
                            ];
                        });
                    @endphp
                    @if($paymentMethods->isEmpty())
                        <div class="empty-state">No data available</div>
                    @else
                        @foreach($paymentMethods as $method => $data)
                            <div class="breakdown-item">
                                <span class="breakdown-label">{{ $method }} ({{ $data['count'] }})</span>
                                <span class="breakdown-value">P{{ number_format($data['total'], 2) }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="notes-box">
            <div class="notes-title">Important Notes</div>
            <div class="notes-text">
                * Total Revenue includes Rent/Utility payments and Deposit Deductions (actual income)<br>
                * Security Deposits are tenant funds held as liability, not counted as revenue<br>
                * Deposit Deductions become revenue when used to cover unpaid invoices
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="page">
        <!-- TRANSACTIONS TABLE -->
        <div class="transactions-section">
            <div class="section-title">Payment Transaction Details</div>

            @if($payments->isEmpty())
                <div class="empty-state">
                    <p>No payment transactions found for the selected period.</p>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 8%;">ID</th>
                            <th style="width: 22%;">Tenant</th>
                            <th style="width: 8%;">Room</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 12%;">Method</th>
                            <th style="width: 15%;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $runningTotal = 0; @endphp
                        @foreach($payments as $payment)
                            @php $runningTotal += $payment->amount; @endphp
                            <tr>
                                <td class="td-date">{{ $payment->date_received->format('M d, Y') }}</td>
                                <td>#{{ $payment->payment_id }}</td>
                                <td class="td-tenant">
                                    @if($payment->invoice && $payment->invoice->booking)
                                        {{ Str::limit($payment->invoice->booking->tenant_summary, 25) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($payment->invoice && $payment->invoice->booking && $payment->invoice->booking->room)
                                        <span class="td-room">{{ $payment->invoice->booking->room->room_num }}</span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeClass = match($payment->payment_type) {
                                            'Rent/Utility' => 'type-rent',
                                            'Security Deposit' => 'type-deposit',
                                            'Deposit Deduction' => 'type-deduction',
                                            'Deposit Refund' => 'type-refund',
                                            default => 'type-rent'
                                        };
                                    @endphp
                                    <span class="td-type {{ $typeClass }}">{{ $payment->payment_type }}</span>
                                </td>
                                <td class="td-method">{{ $payment->payment_method }}</td>
                                <td class="td-amount">P{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="6" style="text-align: right;">TOTAL ({{ $payments->count() }} transactions):</td>
                            <td style="text-align: right;">P{{ number_format($runningTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>

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
                <div class="signature-area">
                    <div class="signature-line"></div>
                    <div class="signature-label">Authorized Signature / Date</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

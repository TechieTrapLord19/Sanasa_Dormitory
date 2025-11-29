<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #03255b;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #03255b;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-section {
            margin-bottom: 30px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 15px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
            width: 33.33%;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #03255b;
        }
        .breakdown-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #03255b;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0;
        }
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .breakdown-label {
            font-weight: 600;
        }
        .breakdown-value {
            font-weight: bold;
            color: #03255b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #03255b;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SANASA DORMITORY</h1>
        <h2 style="margin: 10px 0; color: #03255b;">Sales Report</h2>
        <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="summary-section">
        <h3 class="section-title">Sales Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Revenue (Rent/Utility)</div>
                    <div class="summary-value">₱{{ number_format($totalSales, 2) }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Security Deposits</div>
                    <div class="summary-value" style="color: #3b82f6;">₱{{ number_format($totalSecurityDeposits ?? 0, 2) }}</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Total Transactions</div>
                    <div class="summary-value">{{ number_format($totalTransactions) }}</div>
                </div>
            </div>
        </div>
        <p style="font-size: 10px; color: #666; margin-top: 10px;">
            <em>Note: Security Deposits are tenant funds held as liability, not revenue.</em>
        </p>
    </div>

    <div class="breakdown-section">
        <h3 class="section-title">Sales by Payment Type</h3>
        @if($salesByType->isEmpty())
            <p style="color: #666; text-align: center; padding: 20px;">No sales data for selected period</p>
        @else
            @foreach($salesByType as $type => $amount)
                <div class="breakdown-item">
                    <span class="breakdown-label">{{ $type }}</span>
                    <span class="breakdown-value">₱{{ number_format($amount, 2) }}</span>
                </div>
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <h3 class="section-title">Payment Transactions</h3>
    @if($payments->isEmpty())
        <p style="color: #666; text-align: center; padding: 20px;">No payment transactions found for the selected period.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Booking</th>
                    <th>Tenant(s)</th>
                    <th>Room</th>
                    <th>Payment Type</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->date_received->format('M d, Y') }}</td>
                        <td>#{{ $payment->booking_id ?? 'N/A' }}</td>
                        <td>
                            @if($payment->invoice && $payment->invoice->booking)
                                {{ $payment->invoice->booking->tenant_summary }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($payment->invoice && $payment->invoice->booking && $payment->invoice->booking->room)
                                {{ $payment->invoice->booking->room->room_num }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $payment->payment_type }}</td>
                        <td style="text-align: right; font-weight: bold;">₱{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="background-color: #03255b; color: white; font-weight: bold;">
                    <td colspan="5" style="text-align: right; padding: 12px;">TOTAL:</td>
                    <td style="text-align: right; padding: 12px;">₱{{ number_format($totalSales, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
        <p>© {{ now()->year }} Sanasa Dormitory Management System</p>
    </div>
</body>
</html>

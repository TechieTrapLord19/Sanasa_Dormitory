<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Financial Summary Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; background: white; }
        .page { padding: 15mm 20mm; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 20px; border-bottom: 3px solid #03255b; padding-bottom: 12px; }
        .header-left { display: table-cell; width: 65%; vertical-align: middle; }
        .header-right { display: table-cell; width: 35%; vertical-align: middle; text-align: right; }
        .company-name { font-size: 26px; font-weight: 800; color: #03255b; letter-spacing: -0.5px; margin-bottom: 3px; }
        .company-tagline { font-size: 10px; color: #64748b; letter-spacing: 0.3px; }
        .report-title { font-size: 15px; font-weight: 700; color: #03255b; background: #f1f5f9; padding: 6px 14px; border-radius: 4px; display: inline-block; }
        .report-meta { margin-top: 6px; font-size: 9px; color: #64748b; }

        /* Summary Cards */
        .summary-section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: 700; color: #03255b; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0; }
        .card-grid { width: 100%; border-spacing: 8px; }
        .summary-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; text-align: center; }
        .card-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; font-weight: 600; display: block; }
        .card-value { font-size: 20px; font-weight: 800; color: #03255b; line-height: 1.2; display: block; margin-bottom: 2px; }
        .card-subtext { font-size: 8px; color: #94a3b8; display: block; }

        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; background: white; }
        .data-table thead { background: #f7fafc; }
        .data-table th { padding: 10px 12px; text-align: left; font-weight: 600; color: #03255b; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 2px solid #03255b; }
        .data-table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        .data-table tbody tr:hover { background: #f8fafc; }
        .data-table tbody tr.total-row { background: #f1f5f9; font-weight: 700; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 9px; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">SANASA HOMESTAY</div>
                <div class="company-tagline">Student Accommodation & Housing Management</div>
            </div>
            <div class="header-right">
                <div class="report-title">FINANCIAL SUMMARY</div>
                <div class="report-meta">
                    @if($date_from && $date_to)
                    <strong>Period:</strong> {{ \Carbon\Carbon::parse($date_from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($date_to)->format('M d, Y') }}<br>
                    @else
                    <strong>Period:</strong> All Time<br>
                    @endif
                    <strong>Generated:</strong> {{ $generated_at->format('M d, Y h:i A') }}<br>
                    <strong>By:</strong> {{ $generated_by->full_name }} ({{ ucfirst($generated_by->role) }})
                </div>
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="summary-section">
            <div class="section-title">Financial Overview</div>
            <table class="card-grid">
                <tr>
                    <td class="summary-card">
                        <span class="card-label">Total Billed</span>
                        <span class="card-value">P{{ number_format($invoices_total, 2) }}</span>
                        <span class="card-subtext">All invoices generated</span>
                    </td>
                    <td class="summary-card">
                        <span class="card-label">Total Collected</span>
                        <span class="card-value" style="color: #059669;">P{{ number_format($payments_total, 2) }}</span>
                        <span class="card-subtext">Payments received</span>
                    </td>
                    <td class="summary-card">
                        <span class="card-label">Outstanding Balance</span>
                        <span class="card-value" style="color: #dc2626;">P{{ number_format($outstanding, 2) }}</span>
                        <span class="card-subtext">Amount pending</span>
                    </td>
                    <td class="summary-card">
                        <span class="card-label">Invoice Status</span>
                        <span class="card-value" style="font-size: 16px;">{{ $paid_count }} / {{ $partial_count }} / {{ $unpaid_count }}</span>
                        <span class="card-subtext">Paid / Partial / Unpaid</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Payment Methods -->
        <div class="summary-section">
            <div class="section-title">Payment Methods Breakdown</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th class="text-right">Total Amount</th>
                        <th class="text-right">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = $payments_by_method->sum('total'); @endphp
                    @forelse($payments_by_method as $row)
                        <tr>
                            <td class="font-bold">{{ $row->payment_method }}</td>
                            <td class="text-right">P{{ number_format($row->total, 2) }}</td>
                            <td class="text-right">{{ $total > 0 ? number_format(($row->total / $total) * 100, 1) : 0 }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align: center; color: #a0aec0; padding: 20px;">No payment data available</td></tr>
                    @endforelse
                    @if($payments_by_method->count() > 0)
                        <tr class="total-row">
                            <td>GRAND TOTAL</td>
                            <td class="text-right">P{{ number_format($total, 2) }}</td>
                            <td class="text-right">100%</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sanasa Homestay Management System</strong></p>
            <p>This is a system-generated report. No signature required.</p>
        </div>
    </div>
</body>
</html>

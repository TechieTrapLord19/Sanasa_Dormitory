<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment History Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 10px; color: #1e293b; line-height: 1.4; background: white; }
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
        .card-grid { width: 100%; border-spacing: 8px; }
        .summary-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; text-align: center; }
        .card-label { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; font-weight: 600; display: block; }
        .card-value { font-size: 18px; font-weight: 800; color: #03255b; line-height: 1.2; display: block; margin-bottom: 2px; }
        .card-subtext { font-size: 8px; color: #94a3b8; display: block; }

        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; background: white; font-size: 9px; }
        .data-table thead { background: #f7fafc; }
        .data-table th { padding: 8px 6px; text-align: left; font-weight: 600; color: #03255b; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 2px solid #03255b; }
        .data-table td { padding: 8px 6px; border-bottom: 1px solid #e2e8f0; }
        .data-table tbody tr:hover { background: #f8fafc; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; color: #03255b; }
        .badge { padding: 2px 6px; border-radius: 10px; font-size: 8px; font-weight: 600; display: inline-block; }
        .badge-cash { background: #d1fae5; color: #065f46; }
        .badge-gcash { background: #dbeafe; color: #1e40af; }
        .badge-bank { background: #e0e7ff; color: #4338ca; }

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
                <div class="report-title">PAYMENT HISTORY</div>
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

        <!-- Summary Cards -->
        @php
            $total = $payments->sum('amount');
            $cash = $payments->where('payment_method', 'Cash')->sum('amount');
            $gcash = $payments->where('payment_method', 'GCash')->sum('amount');
            $bank = $payments->where('payment_method', 'Bank Transfer')->sum('amount');
        @endphp

        <div class="summary-section">
            <table class="card-grid">
                <tr>
                    <td class="summary-card">
                        <span class="card-label">Total Payments</span>
                        <span class="card-value">{{ $payments->count() }}</span>
                        <span class="card-subtext">Transaction Records</span>
                    </td>
                    <td class="summary-card">
                        <span class="card-label">Total Amount</span>
                        <span class="card-value" style="color: #059669;">P{{ number_format($total, 2) }}</span>
                        <span class="card-subtext">All Methods</span>
                    </td>
                    <td class="summary-card">
                        <span class="card-label">Cash</span>
                        <span class="card-value">P{{ number_format($cash, 2) }}</span>
                        <span class="card-subtext">{{ $payments->where('payment_method', 'Cash')->count() }} transactions</span>
                    </td>
                    <td class="summary-card">
                        <span class="card-label">GCash</span>
                        <span class="card-value">P{{ number_format($gcash, 2) }}</span>
                        <span class="card-subtext">{{ $payments->where('payment_method', 'GCash')->count() }} transactions</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Transaction Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">ID</th>
                    <th style="width: 8%;">Invoice</th>
                    <th style="width: 18%;">Tenant</th>
                    <th style="width: 13%;">Date & Time</th>
                    <th class="text-right" style="width: 11%;">Amount</th>
                    <th style="width: 10%;">Method</th>
                    <th style="width: 12%;">Reference</th>
                    <th style="width: 16%;">Collected By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td class="font-bold">#{{ $p->payment_id }}</td>
                        <td>#{{ str_pad($p->invoice_id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ optional(optional($p->booking)->tenant)->full_name ?? 'N/A' }}</td>
                        <td>{{ $p->date_received ? $p->date_received->format('M d, Y h:i A') : 'N/A' }}</td>
                        <td class="text-right font-bold">P{{ number_format($p->amount, 2) }}</td>
                        <td>
                            @if($p->payment_method === 'Cash')
                                <span class="badge badge-cash">{{ $p->payment_method }}</span>
                            @elseif($p->payment_method === 'GCash')
                                <span class="badge badge-gcash">{{ $p->payment_method }}</span>
                            @else
                                <span class="badge badge-bank">{{ $p->payment_method }}</span>
                            @endif
                        </td>
                        <td>{{ $p->reference_number ?? '—' }}</td>
                        <td>{{ optional($p->collectedBy)->full_name ?? 'System' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align: center; color: #a0aec0; padding: 20px;">No payment records found</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Sanasa Homestay Management System</strong></p>
            <p>This is a system-generated report. No signature required.</p>
        </div>
    </div>
</body>
</html>

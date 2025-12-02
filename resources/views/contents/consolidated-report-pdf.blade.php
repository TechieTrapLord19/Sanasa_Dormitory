<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consolidated Monthly Report - Sanasa Dormitory</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            background: white;
        }

        .page {
            padding: 12mm 15mm;
            position: relative;
        }

        /* ============ HEADER ============ */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 3px solid #03255b;
            padding-bottom: 12px;
        }

        .header-left {
            display: table-cell;
            width: 65%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 35%;
            vertical-align: middle;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: 800;
            color: #03255b;
            letter-spacing: -0.5px;
            margin-bottom: 3px;
        }

        .company-tagline {
            font-size: 10px;
            color: #64748b;
            letter-spacing: 0.3px;
        }

        .report-title {
            font-size: 14px;
            font-weight: 700;
            color: #03255b;
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 5px;
            display: inline-block;
        }

        .report-meta {
            margin-top: 6px;
            font-size: 9px;
            color: #64748b;
        }

        /* ============ SECTION STYLING ============ */
        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #03255b;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
        }

        .section-icon {
            margin-right: 6px;
        }

        /* ============ SUMMARY CARDS ============ */
        .summary-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 15px;
        }

        .summary-row {
            display: table-row;
        }

        .summary-card {
            display: table-cell;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
            vertical-align: top;
        }

        .summary-card:first-child {
            border-radius: 6px 0 0 6px;
        }

        .summary-card:last-child {
            border-radius: 0 6px 6px 0;
        }

        .summary-card.primary {
            background: #03255b;
            color: white;
            border-color: #03255b;
        }

        .summary-card.primary .summary-label {
            color: #bfdbfe;
        }

        .summary-card.primary .summary-value {
            color: white;
        }

        .summary-card.primary .summary-subtext {
            color: #93c5fd;
        }

        .summary-card.success {
            background: #dcfce7;
            border-color: #10b981;
        }

        .summary-card.success .summary-value {
            color: #059669;
        }

        .summary-card.warning {
            background: #fef3c7;
            border-color: #f59e0b;
        }

        .summary-card.warning .summary-value {
            color: #d97706;
        }

        .summary-card.danger {
            background: #fee2e2;
            border-color: #ef4444;
        }

        .summary-card.danger .summary-value {
            color: #dc2626;
        }

        .summary-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 3px;
            font-weight: 600;
        }
        .summary-value {
            font-size: 16px;
            font-weight: 800;
            color: #03255b;
            line-height: 1.2;
        }

        .summary-subtext {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ============ TWO COLUMN LAYOUT ============ */
        .two-col {
            display: table;
            width: 100%;
            margin-bottom: 15px;
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

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }

        .info-box-title {
            font-size: 10px;
            font-weight: 700;
            color: #03255b;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row {
            display: table;
            width: 100%;
            padding: 5px 0;
            border-bottom: 1px dotted #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            display: table-cell;
            width: 55%;
            font-weight: 500;
            color: #475569;
            font-size: 9px;
        }

        .info-value {
            display: table-cell;
            width: 45%;
            text-align: right;
            font-weight: 700;
            color: #03255b;
            font-size: 9px;
        }

        /* ============ TABLES ============ */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 9px;
        }

        thead {
            background: #03255b;
        }

        th {
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        th:first-child {
            border-radius: 5px 0 0 0;
        }

        th:last-child {
            border-radius: 0 5px 0 0;
        }

        td {
            padding: 7px 6px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-primary {
            color: #03255b;
        }

        .text-success {
            color: #059669;
        }

        .text-warning {
            color: #d97706;
        }

        .text-danger {
            color: #dc2626;
        }

        .text-muted {
            color: #64748b;
        }

        /* ============ STATUS BADGES ============ */
        .badge {
            font-size: 7px;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-available {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-occupied {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-maintenance {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-completed {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-in-progress {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-canceled {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-held {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .badge-refunded {
            background: #dcfce7;
            color: #16a34a;
        }

        /* ============ ROOM GRID ============ */
        .room-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .room-grid-row {
            display: table-row;
        }

        .room-cell {
            display: table-cell;
            width: 12.5%;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        .room-box {
            padding: 6px 4px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
        }

        .room-available {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #86efac;
        }

        .room-occupied {
            background: #dbeafe;
            color: #2563eb;
            border: 1px solid #93c5fd;
        }

        .room-pending {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fcd34d;
        }

        .room-maintenance {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
        }

        /* ============ PAGE BREAK ============ */
        .page-break {
            page-break-after: always;
        }

        /* ============ FOOTER ============ */
        .footer {
            margin-top: 25px;
            padding-top: 12px;
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
            font-size: 8px;
            color: #64748b;
            line-height: 1.5;
        }

        .signature-area {
            margin-top: 25px;
        }

        .signature-line {
            border-top: 1px solid #1e293b;
            width: 150px;
            margin-bottom: 4px;
        }

        .signature-label {
            font-size: 8px;
            color: #64748b;
        }

        /* ============ EMPTY STATE ============ */
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-style: italic;
            font-size: 9px;
        }

        /* ============ TOTAL ROW ============ */
        .total-row {
            background: #03255b !important;
        }

        .total-row td {
            color: white;
            font-weight: 700;
            padding: 10px 6px;
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- ==================== PAGE 1: EXECUTIVE SUMMARY ==================== -->
    <div class="page">
        <!-- HEADER -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">SANASA DORMITORY</div>
                <div class="company-tagline">Student Accommodation & Housing Management</div>
            </div>
            <div class="header-right">
                <div class="report-title">CONSOLIDATED MONTHLY REPORT</div>
                <div class="report-meta">
                    <strong>Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}<br>
                    <strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        <!-- EXECUTIVE SUMMARY -->
        <div class="section">
            <div class="section-title">Executive Summary</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-card primary">
                        <div class="summary-label">Total Revenue</div>
                        <div class="summary-value">P{{ number_format($totalRevenue, 2) }}</div>
                        <div class="summary-subtext">Rent/Utilities + Deductions</div>
                    </div>
                    <div class="summary-card success">
                        <div class="summary-label">Occupancy Rate</div>
                        <div class="summary-value">{{ $occupancyRate }}%</div>
                        <div class="summary-subtext">{{ $roomStats['occupied'] }}/{{ $roomStats['total'] }} Rooms</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Active Tenants</div>
                        <div class="summary-value">{{ $activeTenants }}</div>
                        <div class="summary-subtext">Currently Registered</div>
                    </div>
                    <div class="summary-card warning">
                        <div class="summary-label">Pending Maintenance</div>
                        <div class="summary-value">{{ $maintenanceStats['pending'] + $maintenanceStats['in_progress'] }}</div>
                        <div class="summary-subtext">Issues to Address</div>
                    </div>
                    <div class="summary-card danger">
                        <div class="summary-label">Outstanding Balance</div>
                        <div class="summary-value">P{{ number_format($outstandingBalance, 2) }}</div>
                        <div class="summary-subtext">Unpaid Invoices</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FINANCIAL OVERVIEW -->
        <div class="section">
            <div class="section-title">Financial Overview</div>
            <div class="two-col">
                <div class="col">
                    <div class="info-box">
                        <div class="info-box-title">Revenue by Payment Type</div>
                        @if($revenueByType->isEmpty())
                            <div class="empty-state">No data available</div>
                        @else
                            @foreach($revenueByType as $type => $amount)
                                <div class="info-row">
                                    <span class="info-label">{{ $type }}</span>
                                    <span class="info-value">P{{ number_format($amount, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="info-row" style="border-top: 2px solid #03255b; margin-top: 8px; padding-top: 8px;">
                                <span class="info-label font-bold">TOTAL</span>
                                <span class="info-value">P{{ number_format($revenueByType->sum(), 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="info-box">
                        <div class="info-box-title">Payment Methods</div>
                        @if($paymentsByMethod->isEmpty())
                            <div class="empty-state">No data available</div>
                        @else
                            @foreach($paymentsByMethod as $method)
                                <div class="info-row">
                                    <span class="info-label">{{ $method->payment_method }} ({{ $method->count }})</span>
                                    <span class="info-value">P{{ number_format($method->total, 2) }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ROOM OCCUPANCY STATUS -->
        <div class="section">
            <div class="section-title">Room Occupancy Status</div>
            <div class="summary-grid" style="margin-bottom: 12px;">
                <div class="summary-row">
                    <div class="summary-card success">
                        <div class="summary-label">Available</div>
                        <div class="summary-value">{{ $roomStats['available'] }}</div>
                    </div>
                    <div class="summary-card" style="background: #dbeafe; border-color: #3b82f6;">
                        <div class="summary-label">Occupied</div>
                        <div class="summary-value text-primary">{{ $roomStats['occupied'] }}</div>
                    </div>
                    <div class="summary-card warning">
                        <div class="summary-label">Pending</div>
                        <div class="summary-value">{{ $roomStats['pending'] }}</div>
                    </div>
                    <div class="summary-card danger">
                        <div class="summary-label">Maintenance</div>
                        <div class="summary-value">{{ $roomStats['maintenance'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Room Visual Grid -->
            <div class="info-box">
                <div class="info-box-title">Room Status Overview</div>
                @php
                    $roomChunks = $rooms->chunk(8);
                @endphp
                <div class="room-grid">
                    @foreach($roomChunks as $chunk)
                        <div class="room-grid-row">
                            @foreach($chunk as $room)
                                <div class="room-cell">
                                    <div class="room-box room-{{ $room->status }}">
                                        {{ $room->room_num }}
                                    </div>
                                </div>
                            @endforeach
                            @for($i = $chunk->count(); $i < 8; $i++)
                                <div class="room-cell"></div>
                            @endfor
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 10px; font-size: 8px; color: #64748b;">
                    <span style="display: inline-block; margin-right: 12px;"><span class="badge badge-available">●</span> Available</span>
                    <span style="display: inline-block; margin-right: 12px;"><span class="badge badge-occupied">●</span> Occupied</span>
                    <span style="display: inline-block; margin-right: 12px;"><span class="badge badge-pending">●</span> Pending</span>
                    <span style="display: inline-block;"><span class="badge badge-maintenance">●</span> Maintenance</span>
                </div>
            </div>
        </div>

        <!-- TENANT ACTIVITY -->
        <div class="section">
            <div class="section-title">Tenant Activity Summary</div>
            <div class="two-col">
                <div class="col">
                    <div class="info-box">
                        <div class="info-box-title">Tenant Statistics</div>
                        <div class="info-row">
                            <span class="info-label">Active Tenants</span>
                            <span class="info-value text-success">{{ $activeTenants }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Inactive Tenants</span>
                            <span class="info-value text-muted">{{ $inactiveTenants }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">New Bookings (This Period)</span>
                            <span class="info-value">{{ $newBookingsInPeriod->count() }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Checkouts (This Period)</span>
                            <span class="info-value">{{ $checkoutsInPeriod->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="info-box">
                        <div class="info-box-title">Security Deposit Summary</div>
                        <div class="info-row">
                            <span class="info-label">Total Deposits Held</span>
                            <span class="info-value">P{{ number_format($depositStats['total_held'], 2) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Refunded</span>
                            <span class="info-value text-success">P{{ number_format($depositStats['total_refunded'], 2) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Total Deducted</span>
                            <span class="info-value text-warning">P{{ number_format($depositStats['total_deducted'], 2) }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Pending / Held / Refunded</span>
                            <span class="info-value">{{ $depositStats['pending_count'] }} / {{ $depositStats['held_count'] }} / {{ $depositStats['refunded_count'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAINTENANCE SUMMARY -->
        <div class="section">
            <div class="section-title">Maintenance Summary</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-card">
                        <div class="summary-label">Total Issues</div>
                        <div class="summary-value">{{ $maintenanceStats['total'] }}</div>
                    </div>
                    <div class="summary-card warning">
                        <div class="summary-label">Pending</div>
                        <div class="summary-value">{{ $maintenanceStats['pending'] }}</div>
                    </div>
                    <div class="summary-card" style="background: #dbeafe; border-color: #3b82f6;">
                        <div class="summary-label">In Progress</div>
                        <div class="summary-value text-primary">{{ $maintenanceStats['in_progress'] }}</div>
                    </div>
                    <div class="summary-card success">
                        <div class="summary-label">Completed</div>
                        <div class="summary-value">{{ $maintenanceStats['completed'] }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Canceled</div>
                        <div class="summary-value text-muted">{{ $maintenanceStats['canceled'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 2: PAYMENT TRANSACTIONS ==================== -->
    <div class="page">
        <div class="header">
            <div class="header-left">
                <div class="company-name">SANASA DORMITORY</div>
                <div class="company-tagline">Consolidated Monthly Report</div>
            </div>
            <div class="header-right">
                <div class="report-title">PAYMENT TRANSACTIONS</div>
                <div class="report-meta">
                    Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Payment Transaction Details</div>
            @if($payments->isEmpty())
                <div class="empty-state">No payment transactions found for the selected period.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">Date</th>
                            <th style="width: 6%;">ID</th>
                            <th style="width: 22%;">Tenant</th>
                            <th style="width: 8%;">Room</th>
                            <th style="width: 15%;">Type</th>
                            <th style="width: 12%;">Method</th>
                            <th style="width: 15%;" class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $runningTotal = 0; @endphp
                        @foreach($payments->take(30) as $payment)
                            @php $runningTotal += $payment->amount; @endphp
                            <tr>
                                <td class="text-muted">{{ $payment->date_received->format('M d, Y') }}</td>
                                <td>#{{ $payment->payment_id }}</td>
                                <td class="font-bold">
                                    @if($payment->invoice && $payment->invoice->booking && $payment->invoice->booking->tenant)
                                        {{ Str::limit($payment->invoice->booking->tenant->full_name, 25) }}
                                    @elseif($payment->booking && $payment->booking->tenant)
                                        {{ Str::limit($payment->booking->tenant->full_name, 25) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($payment->invoice && $payment->invoice->booking && $payment->invoice->booking->room)
                                        {{ $payment->invoice->booking->room->room_num }}
                                    @elseif($payment->booking && $payment->booking->room)
                                        {{ $payment->booking->room->room_num }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeClass = match($payment->payment_type) {
                                            'Rent/Utility' => 'badge-occupied',
                                            'Security Deposit' => 'badge-held',
                                            'Deposit Deduction' => 'badge-pending',
                                            'Deposit Refund' => 'badge-refunded',
                                            default => 'badge-occupied'
                                        };
                                    @endphp
                                    <span class="badge {{ $typeClass }}">{{ $payment->payment_type }}</span>
                                </td>
                                <td class="text-muted">{{ $payment->payment_method }}</td>
                                <td class="text-right font-bold text-primary">P{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        @if($payments->count() > 30)
                            <tr>
                                <td colspan="7" class="text-center text-muted" style="font-style: italic;">
                                    ... and {{ $payments->count() - 30 }} more transactions (P{{ number_format($payments->skip(30)->sum('amount'), 2) }})
                                </td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td colspan="6" class="text-right">TOTAL ({{ $payments->count() }} transactions):</td>
                            <td class="text-right">P{{ number_format($payments->sum('amount'), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ==================== PAGE 3: BOOKINGS & MAINTENANCE ==================== -->
    <div class="page">
        <div class="header">
            <div class="header-left">
                <div class="company-name">SANASA DORMITORY</div>
                <div class="company-tagline">Consolidated Monthly Report</div>
            </div>
            <div class="header-right">
                <div class="report-title">BOOKINGS & MAINTENANCE</div>
                <div class="report-meta">
                    Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </div>
            </div>
        </div>

        <!-- NEW BOOKINGS -->
        <div class="section">
            <div class="section-title">New Bookings This Period ({{ $newBookingsInPeriod->count() }})</div>
            @if($newBookingsInPeriod->isEmpty())
                <div class="empty-state">No new bookings during this period.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">Check-in</th>
                            <th style="width: 15%;">Check-out</th>
                            <th style="width: 30%;">Tenant</th>
                            <th style="width: 10%;">Room</th>
                            <th style="width: 15%;">Rate</th>
                            <th style="width: 15%;" class="text-right">Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($newBookingsInPeriod->take(15) as $booking)
                            <tr>
                                <td>{{ $booking->checkin_date->format('M d, Y') }}</td>
                                <td>{{ $booking->checkout_date ? $booking->checkout_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="font-bold">{{ $booking->tenant ? $booking->tenant->full_name : 'N/A' }}</td>
                                <td>{{ $booking->room ? $booking->room->room_num : 'N/A' }}</td>
                                <td>{{ $booking->rate ? $booking->rate->duration_type : 'N/A' }}</td>
                                <td class="text-right font-bold text-primary">P{{ number_format($booking->total_calculated_fee ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                        @if($newBookingsInPeriod->count() > 15)
                            <tr>
                                <td colspan="6" class="text-center text-muted" style="font-style: italic;">
                                    ... and {{ $newBookingsInPeriod->count() - 15 }} more bookings
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @endif
        </div>

        <!-- CHECKOUTS -->
        <div class="section">
            <div class="section-title">Completed Checkouts This Period ({{ $checkoutsInPeriod->count() }})</div>
            @if($checkoutsInPeriod->isEmpty())
                <div class="empty-state">No checkouts during this period.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 20%;">Checkout Date</th>
                            <th style="width: 40%;">Tenant</th>
                            <th style="width: 20%;">Room</th>
                            <th style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checkoutsInPeriod->take(10) as $booking)
                            <tr>
                                <td>{{ $booking->checkout_date->format('M d, Y') }}</td>
                                <td class="font-bold">{{ $booking->tenant ? $booking->tenant->full_name : 'N/A' }}</td>
                                <td>{{ $booking->room ? $booking->room->room_num : 'N/A' }}</td>
                                <td><span class="badge badge-completed">Completed</span></td>
                            </tr>
                        @endforeach
                        @if($checkoutsInPeriod->count() > 10)
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="font-style: italic;">
                                    ... and {{ $checkoutsInPeriod->count() - 10 }} more checkouts
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @endif
        </div>

        <!-- MAINTENANCE LOGS -->
        <div class="section">
            <div class="section-title">Maintenance Logs This Period ({{ $maintenanceLogs->count() }})</div>
            @if($maintenanceLogs->isEmpty())
                <div class="empty-state">No maintenance issues reported during this period.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">Reported</th>
                            <th style="width: 20%;">Asset/Location</th>
                            <th style="width: 35%;">Description</th>
                            <th style="width: 13%;">Status</th>
                            <th style="width: 20%;">Reported By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maintenanceLogs->take(15) as $log)
                            <tr>
                                <td>{{ $log->date_reported->format('M d, Y') }}</td>
                                <td>
                                    @if($log->asset)
                                        {{ $log->asset->name }}
                                        @if($log->asset->room)
                                            <br><small class="text-muted">Room {{ $log->asset->room->room_num }}</small>
                                        @endif
                                    @else
                                        General Issue
                                    @endif
                                </td>
                                <td>{{ Str::limit($log->description, 60) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($log->status) {
                                            'Pending' => 'badge-pending',
                                            'In Progress' => 'badge-in-progress',
                                            'Completed' => 'badge-completed',
                                            'Canceled' => 'badge-canceled',
                                            default => 'badge-pending'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $log->status }}</span>
                                </td>
                                <td>
                                    @if($log->loggedBy)
                                        {{ $log->loggedBy->first_name }} {{ $log->loggedBy->last_name }}
                                    @else
                                        Unknown
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($maintenanceLogs->count() > 15)
                            <tr>
                                <td colspan="5" class="text-center text-muted" style="font-style: italic;">
                                    ... and {{ $maintenanceLogs->count() - 15 }} more maintenance logs
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            @endif
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-left">
                <div class="footer-text">
                    <strong>Sanasa Dormitory Management System</strong><br>
                    This is a computer-generated consolidated monthly report.<br>
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

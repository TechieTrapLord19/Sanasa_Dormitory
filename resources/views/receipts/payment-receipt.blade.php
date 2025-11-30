<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $receiptNumber }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 10px;
            color: #333;
        }

        .receipt-container {
            max-width: 80mm;
            margin: 0 auto;
            background: white;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #03255b;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .company-logo {
            width: 100%;
            height: 70px;
            margin: 0 auto 6px;
            display: block;
        }

        .receipt-header h1 {
            color: #03255b;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .unofficial-notice {
            font-size: 9px;
            color: #e53e3e;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .receipt-number {
            font-size: 11px;
            color: #666;
            font-weight: 600;
        }

        .receipt-section {
            margin-bottom: 10px;
        }

        .receipt-section-title {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .receipt-section-content {
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px dotted #ddd;
            font-size: 11px;
        }

        .receipt-row:last-child {
            border-bottom: none;
        }

        .receipt-label {
            font-weight: 600;
            color: #555;
            width: 45%;
        }

        .receipt-value {
            color: #333;
            width: 55%;
            text-align: right;
        }

        .amount-highlight {
            font-size: 16px;
            font-weight: bold;
            color: #03255b;
            text-align: center;
            padding: 8px;
            background-color: #f0f4f8;
            border-radius: 4px;
            margin: 10px 0;
        }

        .receipt-footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .signature-line {
            margin-top: 20px;
            border-top: 1px solid #333;
            width: 150px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 3px;
        }

        .thank-you {
            margin-top: 8px;
            font-style: italic;
            color: #666;
            font-size: 10px;
        }

        .print-actions {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 12px;
        }

        .btn-primary {
            background-color: #03255b;
            color: white;
        }

        .btn-primary:hover {
            background-color: #021d47;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                padding: 10mm;
                max-width: 80mm;
            }

            .print-actions {
                display: none;
            }

            @page {
                size: 80mm auto;
                margin: 5mm;
            }
        }

        .info-line {
            font-size: 11px;
            padding: 2px 0;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <img src="{{ asset('images/Logo1.png') }}" alt="Sanasa Dormitory Logo" class="company-logo">
            <div class="unofficial-notice">*** UNOFFICIAL RECEIPT ***</div>
            <h1>PAYMENT RECEIPT</h1>
            <div class="receipt-number">Receipt No: {{ $receiptNumber }}</div>
        </div>

        <div class="receipt-section">
            <div class="receipt-row">
                <span class="receipt-label">Payment Date:</span>
                <span class="receipt-value">{{ $payment->date_received->format('F d, Y') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Payment Time:</span>
                <span class="receipt-value">{{ $payment->created_at->format('h:i A') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Payment ID:</span>
                <span class="receipt-value">#{{ $payment->payment_id }}</span>
            </div>
        </div>

        <div class="receipt-section">
            <div class="receipt-section-title">Tenant & Booking</div>
            <div class="receipt-section-content">
                @php
                    $displayTenants = isset($occupants) && $occupants->count() > 0 ? $occupants : collect([$tenant]);
                @endphp
                @foreach($displayTenants as $person)
                    <div class="info-line"><strong>{{ $person->full_name }}</strong></div>
                    @if(!empty($person->contact_num))
                        <div class="info-line">{{ $person->contact_num }}</div>
                    @endif
                @endforeach
                <div class="info-line">Room: {{ $room->room_num }} | Booking #{{ $booking->booking_id }}</div>
            </div>
        </div>

        <div class="receipt-section">
            <div class="receipt-section-title">Check-In & Check-Out</div>
            <div class="receipt-section-content">
                <div class="receipt-row">
                    <span class="receipt-label">Check-In Date:</span>
                    <span class="receipt-value">{{ $booking->checkin_date->format('M d, Y') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Check-Out Date:</span>
                    <span class="receipt-value">{{ $booking->checkout_date->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="receipt-section">
            <div class="receipt-section-title">Payment Details</div>
            <div class="receipt-section-content">
                <div class="receipt-row">
                    <span class="receipt-label">Payment Type:</span>
                    <span class="receipt-value">{{ $paymentType ?? 'Rent/Utility' }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Payment Method:</span>
                    <span class="receipt-value">{{ $payment->payment_method }}</span>
                </div>
                @if($payment->payment_method === 'GCash' && $payment->reference_number)
                <div class="receipt-row">
                    <span class="receipt-label">Reference Number:</span>
                    <span class="receipt-value">{{ $payment->reference_number }}</span>
                </div>
                @endif
                @if($payment->invoice)
                <div class="receipt-row">
                    <span class="receipt-label">Invoice ID:</span>
                    <span class="receipt-value">#{{ $payment->invoice->invoice_id }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-label">Invoice Date:</span>
                    <span class="receipt-value">{{ $payment->invoice->date_generated->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($invoice && str_contains($paymentType, 'Monthly'))
        <div class="receipt-section">
            <div class="receipt-section-title">Breakdown</div>
            <div class="receipt-section-content">
                @if($invoice->rent_subtotal > 0)
                <div class="receipt-row">
                    <span class="receipt-label">Rent:</span>
                    <span class="receipt-value">₱{{ number_format($invoice->rent_subtotal, 2) }}</span>
                </div>
                @endif
                @if($invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0)
                    @foreach($invoice->invoiceUtilities as $utility)
                    <div class="receipt-row">
                        <span class="receipt-label">{{ $utility->utility_name }}:</span>
                        <span class="receipt-value">₱{{ number_format($utility->amount, 2) }}</span>
                    </div>
                    @endforeach
                @endif
                @if($invoice->utility_electricity_fee > 0 && !str_contains($paymentType, 'Security Deposit'))
                <div class="receipt-row">
                    <span class="receipt-label">Electricity:</span>
                    <span class="receipt-value">₱{{ number_format($invoice->utility_electricity_fee, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="amount-highlight">
            Amount Paid: ₱{{ number_format($payment->amount, 2) }}
        </div>

        <div class="receipt-section">
            <div class="receipt-row">
                <span class="receipt-label">Collected By:</span>
                <span class="receipt-value">{{ $collectedBy ? $collectedBy->first_name . ' ' . $collectedBy->last_name : 'N/A' }}</span>
            </div>
        </div>

        <div class="receipt-footer">
            <div class="signature-line">
                <div style="text-align: center; font-size: 9px; color: #666;">Authorized Signature</div>
            </div>
            <div class="thank-you">
                Thank you for your payment!
            </div>
        </div>

        <div class="print-actions">
            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
            <a href="{{ route('invoices') }}" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">Close</a>
        </div>
    </div>
</body>
</html>

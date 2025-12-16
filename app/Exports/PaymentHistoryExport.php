<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $query = Payment::with(['invoice', 'booking.tenant', 'collectedBy']);

        if ($this->dateFrom && $this->dateTo) {
            $query->whereBetween('date_received', [$this->dateFrom, $this->dateTo]);
        }

        return $query->orderByDesc('date_received')
            ->limit(1000)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Payment ID',
            'Invoice',
            'Tenant',
            'Date Received',
            'Amount',
            'Method',
            'Reference',
            'Collected By',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->payment_id,
            $payment->invoice_id,
            optional(optional($payment->booking)->tenant)->full_name ?? 'N/A',
            $payment->date_received ? $payment->date_received->format('Y-m-d H:i') : '',
            (float) $payment->amount,
            $payment->payment_method,
            $payment->reference_number,
            optional($payment->collectedBy)->full_name ?? 'N/A',
        ];
    }
}

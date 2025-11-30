@extends('layouts.app')

@section('title', 'Security Deposit Details')

@section('content')
<style>
    .deposit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .deposit-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }

    /* Status Badge */
    .status-badge-large {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-badge-large.pending { background-color: #fef3c7; color: #92400e; }
    .status-badge-large.held { background-color: #dbeafe; color: #1e40af; }
    .status-badge-large.partially-refunded { background-color: #fce7f3; color: #9d174d; }
    .status-badge-large.refunded { background-color: #d1fae5; color: #065f46; }
    .status-badge-large.forfeited { background-color: #fee2e2; color: #991b1b; }

    /* Cards Layout */
    .deposit-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .deposit-grid {
            grid-template-columns: 1fr;
        }

        .deposit-sidebar {
            position: static;
        }
    }

    .deposit-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        border: 1px solid #e5e5e5;
    }

    .deposit-main {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .deposit-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        position: sticky;
        top: 1rem;
    }

    .card-header {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Amount Summary */
    .amount-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .amount-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .amount-item {
        padding: 1.25rem 1rem;
        background: #f8fafc;
        border-radius: 8px;
        text-align: center;
    }

    .amount-item.highlight {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border: 2px solid #10b981;
    }

    .amount-item .label {
        font-size: 0.7rem;
        color: #64748b;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .amount-item .value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    .amount-item.highlight .value {
        color: #059669;
    }

    .amount-item.deducted .value {
        color: #dc2626;
    }

    /* Booking Info */
    .info-table {
        width: 100%;
    }

    .info-table tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .info-table tr:last-child {
        border-bottom: none;
    }

    .info-table td {
        padding: 0.875rem 0;
        font-size: 0.9rem;
    }

    .info-table td:first-child {
        width: 130px;
        font-weight: 500;
        color: #64748b;
    }

    .info-table td:last-child {
        color: #1e293b;
    }

    .info-row {
        display: flex;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        width: 120px;
        font-weight: 500;
        color: #64748b;
        font-size: 0.875rem;
    }

    .info-value {
        flex: 1;
        color: #1e293b;
        font-size: 0.875rem;
    }

    /* Deductions List */
    .deductions-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .deduction-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.75rem;
        background: #fef2f2;
        border-radius: 6px;
        margin-bottom: 0.5rem;
    }

    .deduction-item:last-child {
        margin-bottom: 0;
    }

    .deduction-info {
        flex: 1;
    }

    .deduction-category {
        font-weight: 600;
        color: #991b1b;
        font-size: 0.875rem;
    }

    .deduction-desc {
        font-size: 0.75rem;
        color: #7f1d1d;
        margin-top: 0.25rem;
    }

    .deduction-date {
        font-size: 0.7rem;
        color: #b91c1c;
    }

    .deduction-amount {
        font-weight: 700;
        color: #dc2626;
        font-size: 0.875rem;
    }

    .no-deductions {
        text-align: center;
        padding: 2rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn-action {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
    }

    .btn-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-deduct {
        background-color: #fef3c7;
        color: #92400e;
    }

    .btn-deduct:hover:not(:disabled) {
        background-color: #fde68a;
    }

    .btn-refund {
        background-color: #d1fae5;
        color: #065f46;
    }

    .btn-refund:hover:not(:disabled) {
        background-color: #a7f3d0;
    }

    .btn-forfeit {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .btn-forfeit:hover:not(:disabled) {
        background-color: #fecaca;
    }



    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0;
    }

    .modal-title {
        font-weight: 600;
        color: #1e293b;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }

    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0.75rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .btn-modal-primary {
        background-color: #03255b;
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        color: white;
    }

    .btn-modal-primary:hover {
        background-color: #021d47;
        color: white;
    }

    /* Notes Section */
    .notes-section {
        margin-top: 1rem;
        padding: 1rem;
        background: #fffbeb;
        border-radius: 8px;
        border-left: 4px solid #f59e0b;
    }

    .notes-section h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 0.5rem;
    }

    .notes-section p {
        font-size: 0.875rem;
        color: #78350f;
        margin: 0;
        white-space: pre-wrap;
    }

    /* Alert Messages */
    .alert {
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .alert-success {
        background-color: #d1fae5;
        border: 1px solid #10b981;
        color: #065f46;
    }

    .alert-error {
        background-color: #fee2e2;
        border: 1px solid #ef4444;
        color: #991b1b;
    }
</style>

<div class="deposit-header">
    <h1 class="deposit-title">
        <i class="bi bi-shield-check"></i>
        Security Deposit #{{ $securityDeposit->security_deposit_id }}
    </h1>
    <a href="{{ route('security-deposits.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('success') }}', 'success', 4000);
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('error') }}', 'error', 5000);
        });
    </script>
@endif

<div class="deposit-grid">
    <!-- Main Content -->
    <div class="deposit-main">
        <!-- Amount Summary Card -->
        <div class="deposit-card">
            <div class="card-header">
                <h3><i class="bi bi-cash-stack"></i> Deposit Summary</h3>
                @php
                    $statusClass = match($securityDeposit->status) {
                        'Pending' => 'pending',
                        'Held' => 'held',
                        'Partially Refunded' => 'partially-refunded',
                        'Refunded' => 'refunded',
                        'Forfeited' => 'forfeited',
                        default => 'pending'
                    };
                @endphp
                <span class="status-badge-large {{ $statusClass }}">{{ $securityDeposit->status }}</span>
            </div>
            <div class="card-body">
                <div class="amount-grid">
                    <div class="amount-item">
                        <div class="label">Required</div>
                        <div class="value">₱{{ number_format($securityDeposit->amount_required, 2) }}</div>
                    </div>
                    <div class="amount-item">
                        <div class="label">Paid</div>
                        <div class="value">₱{{ number_format($securityDeposit->amount_paid, 2) }}</div>
                    </div>
                    <div class="amount-item deducted">
                        <div class="label">Deducted</div>
                        <div class="value">-₱{{ number_format($securityDeposit->amount_deducted, 2) }}</div>
                    </div>
                    <div class="amount-item highlight">
                        <div class="label">Refundable Balance</div>
                        <div class="value">₱{{ number_format($securityDeposit->calculateRefundable(), 2) }}</div>
                    </div>
                </div>

                @if($securityDeposit->amount_refunded > 0)
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">Amount Refunded: </small>
                        <strong class="text-success">₱{{ number_format($securityDeposit->amount_refunded, 2) }}</strong>
                        @if($securityDeposit->refunded_at)
                            <small class="text-muted ms-2">on {{ $securityDeposit->refunded_at->format('M d, Y h:i A') }}</small>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Booking Information Card -->
        <div class="deposit-card">
            <div class="card-header">
                <h3><i class="bi bi-calendar-check"></i> Booking Information</h3>
            </div>
            <div class="card-body">
                <table class="info-table">
                    <tr>
                        <td>Booking ID</td>
                        <td>
                            <a href="{{ route('bookings.show', $securityDeposit->booking_id) }}" style="color: #03255b; font-weight: 600;">
                                #{{ $securityDeposit->booking_id }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Tenant</td>
                        <td>
                            <strong>{{ $securityDeposit->booking->tenant->first_name ?? 'N/A' }} {{ $securityDeposit->booking->tenant->last_name ?? '' }}</strong>
                            @if($securityDeposit->booking->secondaryTenant)
                                <br><small class="text-muted">& {{ $securityDeposit->booking->secondaryTenant->first_name }} {{ $securityDeposit->booking->secondaryTenant->last_name }}</small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Room</td>
                        <td>Room {{ $securityDeposit->booking->room->room_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Check-in</td>
                        <td>{{ $securityDeposit->booking->checkin_date?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Check-out</td>
                        <td>{{ $securityDeposit->booking->checkout_date?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Booking Status</td>
                        <td>
                            <span class="badge bg-{{ $securityDeposit->booking->status === 'Active' ? 'success' : ($securityDeposit->booking->status === 'Completed' ? 'secondary' : 'warning') }}">
                                {{ $securityDeposit->booking->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Deductions History Card -->
        <div class="deposit-card">
            <div class="card-header">
                <h3><i class="bi bi-list-check"></i> Deductions History</h3>
            </div>
            <div class="card-body">
                @php $deductions = $securityDeposit->getDeductionsArray(); @endphp
                @if(count($deductions) > 0)
                    <div class="deductions-list">
                        @foreach($deductions as $deduction)
                            <div class="deduction-item">
                                <div class="deduction-info">
                                    <div class="deduction-category">{{ $deduction['category'] }}</div>
                                    @if(!empty($deduction['description']))
                                        <div class="deduction-desc">{{ $deduction['description'] }}</div>
                                    @endif
                                    <div class="deduction-date">{{ \Carbon\Carbon::parse($deduction['date'])->format('M d, Y h:i A') }}</div>
                                </div>
                                <div class="deduction-amount">-₱{{ number_format($deduction['amount'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-deductions">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        No deductions have been applied.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar - Actions -->
    <div class="deposit-sidebar">
        <!-- Actions Card -->
        <div class="deposit-card">
            <div class="card-header">
                <h3><i class="bi bi-gear"></i> Actions</h3>
            </div>
            <div class="card-body">
                @php
                    $isClosed = in_array($securityDeposit->status, ['Refunded', 'Forfeited']);
                    $hasRefundable = $securityDeposit->calculateRefundable() > 0;
                @endphp

                <div class="action-buttons">
                    <button type="button" class="btn-action btn-deduct"
                            data-bs-toggle="modal" data-bs-target="#deductionModal"
                            {{ $isClosed || !$hasRefundable ? 'disabled' : '' }}>
                        <i class="bi bi-dash-circle"></i> Apply Deduction
                    </button>

                    <button type="button" class="btn-action btn-refund"
                            data-bs-toggle="modal" data-bs-target="#refundModal"
                            {{ $isClosed || !$hasRefundable ? 'disabled' : '' }}>
                        <i class="bi bi-arrow-return-left"></i> Process Refund
                    </button>

                    <button type="button" class="btn-action btn-forfeit"
                            data-bs-toggle="modal" data-bs-target="#forfeitModal"
                            {{ $isClosed || !$hasRefundable ? 'disabled' : '' }}>
                        <i class="bi bi-x-circle"></i> Forfeit Deposit
                    </button>
                </div>

                @if($isClosed)
                    <div class="mt-3 p-2 bg-light rounded text-center">
                        <small class="text-muted">This deposit has been finalized.</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Notes Card -->
        @if($securityDeposit->notes)
            <div class="deposit-card">
                <div class="card-header">
                    <h3><i class="bi bi-sticky"></i> Notes</h3>
                </div>
                <div class="card-body">
                    <p style="white-space: pre-wrap; margin: 0; font-size: 0.875rem; color: #475569;">{{ $securityDeposit->notes }}</p>
                </div>
            </div>
        @endif

        <!-- Processed By Card -->
        @if($securityDeposit->processedBy)
            <div class="deposit-card">
                <div class="card-header">
                    <h3><i class="bi bi-person-check"></i> Processed By</h3>
                </div>
                <div class="card-body">
                    <p style="margin: 0; font-size: 0.875rem;">
                        {{ $securityDeposit->processedBy->name }}
                        @if($securityDeposit->refunded_at)
                            <br><small class="text-muted">{{ $securityDeposit->refunded_at->format('M d, Y h:i A') }}</small>
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Deduction Modal -->
<div class="modal fade" id="deductionModal" tabindex="-1" aria-labelledby="deductionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('security-deposits.deduction', $securityDeposit) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="deductionModalLabel">
                        <i class="bi bi-dash-circle me-2"></i>Apply Deduction
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Deduction Category</label>
                        <select name="category" id="deductionCategory" class="form-select" required>
                            <option value="">Select category...</option>
                            @foreach(\App\Models\SecurityDeposit::getDeductionCategories() as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Invoice Selection (shown only for Unpaid Rent/Utilities) -->
                    <div class="mb-3" id="invoiceSelectionGroup" style="display: none;">
                        <label class="form-label">Apply to Invoice</label>
                        <select name="invoice_id" id="invoiceSelect" class="form-select">
                            <option value="">Select invoice to apply payment...</option>
                            @if(isset($outstandingInvoices) && $outstandingInvoices->count() > 0)
                                @foreach($outstandingInvoices as $invoice)
                                    <option value="{{ $invoice->invoice_id }}" data-balance="{{ $invoice->remaining_balance }}">
                                        Invoice #{{ $invoice->invoice_id }} - {{ $invoice->date_generated->format('M Y') }}
                                        (Balance: ₱{{ number_format($invoice->remaining_balance, 2) }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No outstanding invoices</option>
                            @endif
                        </select>
                        <small class="text-muted">The deduction amount will be applied as a payment to the selected invoice.</small>
                    </div>

                    <!-- Revenue Info (shown for Damages/Cleaning/Other) -->
                    <div class="alert alert-info mb-3" id="revenueInfo" style="display: none;">
                        <i class="bi bi-info-circle me-2"></i>
                        This deduction will be recorded as <strong>revenue</strong> in the sales report.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount" id="deductionAmount" class="form-control"
                                   step="0.01" min="0.01" max="{{ $securityDeposit->calculateRefundable() }}" required>
                        </div>
                        <small class="text-muted">Maximum: ₱{{ number_format($securityDeposit->calculateRefundable(), 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Describe the reason for this deduction..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-modal-primary">Apply Deduction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('deductionCategory');
    const invoiceGroup = document.getElementById('invoiceSelectionGroup');
    const revenueInfo = document.getElementById('revenueInfo');
    const invoiceSelect = document.getElementById('invoiceSelect');
    const amountInput = document.getElementById('deductionAmount');

    categorySelect.addEventListener('change', function() {
        const category = this.value;
        const isUnpaid = (category === 'Unpaid Rent/Utilities' || category === 'Unpaid Electricity');

        invoiceGroup.style.display = isUnpaid ? 'block' : 'none';
        revenueInfo.style.display = (!isUnpaid && category) ? 'block' : 'none';

        if (!isUnpaid) {
            invoiceSelect.value = '';
        }
    });

    // Auto-fill amount when invoice is selected
    invoiceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.balance) {
            const balance = parseFloat(selectedOption.dataset.balance);
            const maxDeduction = {{ $securityDeposit->calculateRefundable() }};
            amountInput.value = Math.min(balance, maxDeduction).toFixed(2);
        }
    });
});
</script>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('security-deposits.refund', $securityDeposit) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">
                        <i class="bi bi-arrow-return-left me-2"></i>Process Refund
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Refundable amount: <strong>₱{{ number_format($securityDeposit->calculateRefundable(), 2) }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Refund Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="refund_amount" class="form-control"
                                   step="0.01" min="0" max="{{ $securityDeposit->calculateRefundable() }}"
                                   value="{{ $securityDeposit->calculateRefundable() }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Refund Method</label>
                        <select name="refund_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="GCash">GCash</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Forfeit Modal -->
<div class="modal fade" id="forfeitModal" tabindex="-1" aria-labelledby="forfeitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('security-deposits.forfeit', $securityDeposit) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="forfeitModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Forfeit Deposit
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will forfeit the entire remaining balance of
                        <strong>₱{{ number_format($securityDeposit->calculateRefundable(), 2) }}</strong>.
                        This action cannot be undone.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Forfeiture</label>
                        <textarea name="reason" class="form-control" rows="3"
                                  placeholder="Explain why the deposit is being forfeited..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirm Forfeiture</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


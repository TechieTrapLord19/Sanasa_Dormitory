@extends('layouts.app')

@section('title', 'Tenant Details')

@section('content')
<style>
    .tenant-details-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        flex-shrink: 0;
        gap: 1rem;
    }

    .details-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .status-badge.active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.inactive {
        background-color: #e5e7eb;
        color: #4b5563;
    }

    .info-section {
        margin-bottom: 1.5rem;
        flex-shrink: 0;
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .info-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.75rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .info-value {
        font-size: 0.95rem;
        color: #2d3748;
        font-weight: 500;
    }

    .info-value-view {
        display: block;
    }

    .info-value-edit {
        display: none;
    }

    .edit-mode .info-value-view {
        display: none;
    }

    .edit-mode .info-value-edit {
        display: block;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-save {
        background-color: #10b981;
        color: white;
    }

    .btn-save:hover {
        background-color: #059669;
    }

    .btn-cancel {
        background-color: #6b7280;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #4b5563;
    }

    .btn-archive {
        background-color: #fef3c7;
        color: #92400e;
    }

    .btn-archive:hover {
        background-color: #fde68a;
    }

    .btn-archive:disabled {
        background-color: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-archive:disabled:hover {
        background-color: #e5e7eb;
    }

    .btn-activate {
        background-color: #d1fae5;
        color: #065f46;
    }

    .btn-activate:hover {
        background-color: #a7f3d0;
    }

    .edit-mode .btn-edit {
        display: none;
    }

    .btn-save, .btn-cancel {
        display: none;
    }

    .edit-mode .btn-save,
    .edit-mode .btn-cancel {
        display: inline-block;
    }

    /* Payment Method Badges */
    .payment-method-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-method-badge.cash {
        background-color: #d1fae5;
        color: #065f46;
    }

    .payment-method-badge.gcash {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .payment-method-badge.bank {
        background-color: #e0e7ff;
        color: #4338ca;
    }

    .payment-method-badge.check {
        background-color: #fef3c7;
        color: #92400e;
    }

    .payment-method-badge.other {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    /* Payment Type Badges */
    .payment-type-badge {
        display: inline-block;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .payment-type-badge.rent {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .payment-type-badge.utility {
        background-color: #fef3c7;
        color: #92400e;
    }

    .payment-type-badge.deposit {
        background-color: #d1fae5;
        color: #065f46;
    }

    .payment-type-badge.other {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .btn-receipt {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.75rem;
        background-color: #03255b;
        color: white;
        border: none;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-receipt:hover {
        background-color: #021d47;
        color: white;
    }

    .bookings-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        margin-top: 1.5rem;
    }

    .bookings-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bookings-table thead {
        background-color: #f7fafc;
    }

    .bookings-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .bookings-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .bookings-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .bookings-table tbody tr:last-child td {
        border-bottom: none;
    }

    .btn-view {
        background-color: #e0f2fe;
        color: #0369a1;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-block;
    }

    .btn-view:hover {
        background-color: #bae6fd;
        color: #0369a1;
    }

    .form-control, .form-select {
        font-size: 0.95rem;
        padding: 0.5rem;
    }
</style>

<div class="tenant-details-container" id="tenantContainer">
    <div class="details-header">
        <div>
            <h1 class="details-title">Tenant Details</h1>
            <span class="status-badge {{ $tenant->status }}" id="statusBadge">{{ ucfirst($tenant->status) }}</span>
        </div>
        <div class="action-buttons">
            <a href="{{ route('tenants') }}" class="btn-action btn-edit">
                <i class="bi bi-arrow-left"></i> Back to Tenants
            </a>
            <button type="button" class="btn-action btn-edit" id="editBtn" onclick="toggleEditMode()">
                <i class="bi bi-pencil-square"></i> Edit
            </button>
            <button type="button" class="btn-action btn-save" id="saveBtn" onclick="saveTenant()">
                <i class="bi bi-check-circle"></i> Save
            </button>
            <button type="button" class="btn-action btn-cancel" id="cancelBtn" onclick="cancelEdit()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
            @if($tenant->status === 'active')
                @php
                    $hasActiveBooking = $tenant->bookings()->where('status', 'Active')->exists();
                @endphp
                <form action="{{ route('tenants.archive', $tenant->tenant_id) }}" method="POST" style="display: inline;" id="archiveForm">
                    @csrf
                    <button type="button"
                            class="btn-action btn-archive"
                            @if($hasActiveBooking) disabled @endif
                            title="{{ $hasActiveBooking ? 'Cannot archive tenant while they have an active booking' : 'Archive this tenant' }}"
                            onclick="{{ $hasActiveBooking ? '' : 'confirmAction(\'Are you sure you want to archive this tenant?\', function() { document.getElementById(\'archiveForm\').submit(); }, { title: \'Archive Tenant\', confirmText: \'Yes, Archive\', type: \'warning\' })' }}">
                        <i class="bi bi-archive"></i> Archive
                    </button>
                </form>

            @else
                <form action="{{ route('tenants.activate', $tenant->tenant_id) }}" method="POST" style="display: inline;" id="activateForm">
                    @csrf
                    <button type="button" class="btn-action btn-activate" onclick="confirmAction('Are you sure you want to activate this tenant?', function() { document.getElementById('activateForm').submit(); }, { title: 'Activate Tenant', confirmText: 'Yes, Activate', type: 'info' })">
                        <i class="bi bi-check-circle"></i> Activate
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading">Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tenants.update', $tenant->tenant_id) }}" method="POST" id="tenantForm">
        @csrf
        @method('PUT')

        <!-- Tenant Information -->
        <div class="info-section">
            <h2 class="info-section-title">Personal Information</h2>
            <!-- Three Column Layout -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                <!-- Column 1: Name Fields (Stacked) -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="info-item">
                        <span class="info-label">First Name</span>
                        <span class="info-value info-value-view"><strong>{{ $tenant->first_name }}</strong></span>
                        <input type="text" class="form-control info-value-edit @error('first_name') is-invalid @enderror"
                               name="first_name" value="{{ old('first_name', $tenant->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="info-item">
                        <span class="info-label">Middle Name</span>
                        <span class="info-value info-value-view">{{ $tenant->middle_name ?? 'N/A' }}</span>
                        <input type="text" class="form-control info-value-edit @error('middle_name') is-invalid @enderror"
                               name="middle_name" value="{{ old('middle_name', $tenant->middle_name) }}">
                        @error('middle_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Name</span>
                        <span class="info-value info-value-view"><strong>{{ $tenant->last_name }}</strong></span>
                        <input type="text" class="form-control info-value-edit @error('last_name') is-invalid @enderror"
                               name="last_name" value="{{ old('last_name', $tenant->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Column 2: Contact Info (Stacked) -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="info-item">
                        <span class="info-label">Contact Number</span>
                        <span class="info-value info-value-view">{{ $tenant->contact_num ?? 'N/A' }}</span>
                        <input type="text" class="form-control info-value-edit @error('contact_num') is-invalid @enderror"
                               name="contact_num" value="{{ old('contact_num', $tenant->contact_num) }}">
                        @error('contact_num')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value info-value-view">{{ $tenant->email ?? 'N/A' }}</span>
                        <input type="email" class="form-control info-value-edit @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email', $tenant->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="info-item">
                        <span class="info-label">Age</span>
                        <span class="info-value info-value-view">{{ $tenant->age ? $tenant->age . ' years old' : 'N/A' }}</span>
                        <div class="info-value-edit" style="padding: 0.5rem; background-color: #f8fafc; border-radius: 4px; color: #64748b;">
                            {{ $tenant->age ? $tenant->age . ' years old' : 'N/A' }} <small>(calculated from birth date)</small>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Additional Info (Stacked) -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="info-item">
                        <span class="info-label">Emergency Contact</span>
                        <span class="info-value info-value-view">{{ $tenant->emer_contact_num ?? 'N/A' }}</span>
                        <input type="text" class="form-control info-value-edit @error('emer_contact_num') is-invalid @enderror"
                               name="emer_contact_num" value="{{ old('emer_contact_num', $tenant->emer_contact_num) }}">
                        @error('emer_contact_num')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="info-item">
                        <span class="info-label">Birth Date</span>
                        <span class="info-value info-value-view">{{ $tenant->birth_date ? $tenant->birth_date->format('M d, Y') : 'N/A' }}</span>
                        <input type="date" class="form-control info-value-edit @error('birth_date') is-invalid @enderror"
                               name="birth_date" value="{{ old('birth_date', $tenant->birth_date ? $tenant->birth_date->format('Y-m-d') : '') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="info-item">
                        <span class="info-label">ID Document</span>
                        <span class="info-value info-value-view">{{ $tenant->id_document ?? 'N/A' }}</span>
                        <input type="text" class="form-control info-value-edit @error('id_document') is-invalid @enderror"
                               name="id_document" value="{{ old('id_document', $tenant->id_document) }}">
                        @error('id_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address (Full Width) -->
            <div class="info-item" style="margin-top: 1rem;">
                <span class="info-label">Address</span>
                <span class="info-value info-value-view">{{ $tenant->address ?? 'N/A' }}</span>
                <textarea class="form-control info-value-edit @error('address') is-invalid @enderror"
                          name="address" rows="2">{{ old('address', $tenant->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Status Field (Hidden in view, shown in edit mode) -->
        <div class="info-section" id="statusSection" style="display: none;">
            <h2 class="info-section-title">Account Status</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <select class="form-select @error('status') is-invalid @enderror"
                            name="status" required>
                        <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </form>

    <!-- Payment History -->
    <div class="info-section">
        <h2 class="info-section-title">
            <i class="bi bi-credit-card me-2"></i>Payment History
            <span style="font-weight: 400; font-size: 0.85rem; color: #64748b;">
                ({{ $payments->count() }} {{ Str::plural('payment', $payments->count()) }} · Total: ₱{{ number_format($totalPaid, 2) }})
            </span>
        </h2>
        @if($payments->count() > 0)
            <div class="bookings-table-container">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Room</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Collected By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            @php
                                $methodClass = match(strtolower($payment->payment_method ?? '')) {
                                    'cash' => 'cash',
                                    'gcash' => 'gcash',
                                    'bank transfer', 'bank' => 'bank',
                                    'check', 'cheque' => 'check',
                                    default => 'other'
                                };
                                $typeClass = match(true) {
                                    str_contains(strtolower($payment->payment_type ?? ''), 'rent') => 'rent',
                                    str_contains(strtolower($payment->payment_type ?? ''), 'electric') || str_contains(strtolower($payment->payment_type ?? ''), 'utility') => 'utility',
                                    str_contains(strtolower($payment->payment_type ?? ''), 'deposit') => 'deposit',
                                    default => 'other'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $payment->date_received ? $payment->date_received->format('M d, Y') : 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $payment->created_at->format('g:i A') }}</small>
                                </td>
                                <td>
                                    <span class="payment-type-badge {{ $typeClass }}">{{ $payment->payment_type }}</span>
                                </td>
                                <td>
                                    @if($payment->booking && $payment->booking->room)
                                        Room {{ $payment->booking->room->room_num }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td style="font-weight: 600; color: #059669;">₱{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="payment-method-badge {{ $methodClass }}">
                                        @if($methodClass === 'cash')
                                            <i class="bi bi-cash"></i>
                                        @elseif($methodClass === 'gcash')
                                            <i class="bi bi-phone"></i>
                                        @elseif($methodClass === 'bank')
                                            <i class="bi bi-bank"></i>
                                        @elseif($methodClass === 'check')
                                            <i class="bi bi-file-text"></i>
                                        @else
                                            <i class="bi bi-credit-card"></i>
                                        @endif
                                        {{ $payment->payment_method }}
                                    </span>
                                </td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                <td>{{ $payment->collectedBy->full_name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('payments.receipt', $payment->payment_id) }}" class="btn-receipt" target="_blank">
                                            <i class="bi bi-printer"></i> Receipt
                                        </a>
                                        @if($payment->booking)
                                            <a href="{{ route('bookings.show', $payment->booking->booking_id) }}" class="btn-view">View</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 2rem; color: #64748b;">
                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <p class="mb-0">No payment records found for this tenant.</p>
            </div>
        @endif
    </div>

    <!-- Booking History -->
    <div class="info-section">
        <h2 class="info-section-title">Booking History ({{ $tenant->bookings_count ?? 0 }})</h2>
        @if($tenant->bookings->count() > 0)
            <div class="bookings-table-container">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                            <th>Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenant->bookings as $booking)
                            <tr>
                                <td>
                                    <strong>Room {{ $booking->room->room_num ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ $booking->checkin_date->format('M d, Y') }}</td>
                                <td>{{ $booking->checkout_date->format('M d, Y') }}</td>
                                <td>{{ $booking->rate->duration_type ?? 'N/A' }} &middot; ₱{{ number_format($booking->rate->base_price ?? 0, 2) }}</td>
                                <td>
                                    <span class="status-badge {{ str_replace(' ', '-', $booking->effective_status) }}">
                                        {{ $booking->effective_status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking->booking_id) }}" class="btn-view">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">No bookings found for this tenant.</p>
        @endif
    </div>
</div>

<script>
let isEditMode = false;
const originalValues = {};

function toggleEditMode() {
    const container = document.getElementById('tenantContainer');
    const statusSection = document.getElementById('statusSection');
    isEditMode = !isEditMode;

    if (isEditMode) {
        container.classList.add('edit-mode');
        // Show status section in edit mode
        if (statusSection) statusSection.style.display = 'block';
        // Store original values
        document.querySelectorAll('.info-value-edit').forEach(input => {
            originalValues[input.name] = input.value;
        });
        // Also store status select
        const statusSelect = document.querySelector('[name="status"]');
        if (statusSelect) originalValues['status'] = statusSelect.value;
        // Hide archive/activate forms
        const archiveForm = document.getElementById('archiveForm');
        const activateForm = document.getElementById('activateForm');
        if (archiveForm) archiveForm.style.display = 'none';
        if (activateForm) activateForm.style.display = 'none';
    } else {
        container.classList.remove('edit-mode');
        // Hide status section in view mode
        if (statusSection) statusSection.style.display = 'none';
        // Restore original values
        Object.keys(originalValues).forEach(name => {
            const input = document.querySelector(`[name="${name}"]`);
            if (input) input.value = originalValues[name];
        });
        // Show archive/activate forms
        const archiveForm = document.getElementById('archiveForm');
        const activateForm = document.getElementById('activateForm');
        if (archiveForm) archiveForm.style.display = 'inline';
        if (activateForm) activateForm.style.display = 'inline';
    }
}

function cancelEdit() {
    toggleEditMode();
}

function saveTenant() {
    document.getElementById('tenantForm').submit();
}

// Update status badge when status changes
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const statusBadge = document.getElementById('statusBadge');
            if (statusBadge) {
                statusBadge.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                statusBadge.className = 'status-badge ' + this.value;
            }
        });
    }
});
</script>
@endsection


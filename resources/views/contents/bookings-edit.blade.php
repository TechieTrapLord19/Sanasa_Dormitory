@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
@php
    $originalStayLength = $booking->checkin_date->diffInDays($booking->checkout_date);
    $defaultStayLength = (int) old('stay_length', max(1, $originalStayLength));
@endphp
<style>
    .booking-form-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }

    .step-indicator::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #e2e8f0;
        z-index: 0;
    }

    .step {
        flex: 1;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e2e8f0;
        color: #4a5568;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin: 0 auto 0.5rem;
        transition: all 0.3s ease;
    }

    .step.active .step-number {
        background-color: #03255b;
        color: white;
    }

    .step.completed .step-number {
        background-color: #10b981;
        color: white;
    }

    .step-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4a5568;
    }

    .step.active .step-title {
        color: #03255b;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control, .form-select {
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        width: 100%;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .duration-presets {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .duration-button {
        border: 1px solid #cbd5e0;
        background-color: #f7fafc;
        color: #1a202c;
        padding: 0.45rem 0.9rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .duration-button.active {
        background-color: #03255b;
        border-color: #021d47;
        color: white;
    }

    .duration-button:hover {
        border-color: #03255b;
        color: #03255b;
    }

    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .room-card {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .room-card:hover {
        border-color: #03255b;
        background-color: #f7fafc;
    }

    .room-card.selected {
        border-color: #03255b;
        background-color: #e0f2fe;
    }

    .room-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #03255b;
        margin-bottom: 0.25rem;
    }

    .room-floor {
        font-size: 0.75rem;
        color: #718096;
    }

    .btn-primary-custom, .btn-secondary-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary-custom i, .btn-secondary-custom i {
        font-size: 1rem;
    }

    .btn-primary-custom {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-primary-custom:hover {
        background-color: #021d47;
    }

    .btn-secondary-custom {
        background-color: #e2e8f0;
        color: #4a5568;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .btn-secondary-custom:hover {
        background-color: #cbd5e0;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .summary-box {
        background-color: #f7fafc;
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .summary-row.total {
        font-weight: 700;
        font-size: 1.125rem;
        color: #03255b;
        border-top: 2px solid #e2e8f0;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }

    .summary-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1f2937;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
    }

    .charges-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .charges-list li {
        display: flex;
        justify-content: space-between;
        padding: 0.35rem 0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.9rem;
    }

    .charges-list li:last-child {
        border-bottom: none;
    }

    .italic-note {
        font-style: italic;
        color: #4a5568;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .info-box {
        background-color: #e0f2fe;
        border-left: 4px solid #0369a1;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }

    .info-box p {
        margin: 0.25rem 0;
        font-size: 0.9rem;
    }

    .info-box strong {
        color: #03255b;
    }

    .tenant-dropdown-wrapper {
        position: relative;
        width: 100%;
    }

    .tenant-dropdown-btn {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        background-color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }

    .tenant-dropdown-btn:hover {
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .tenant-search-input-inline {
        flex: 1;
        border: none;
        outline: none;
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
        background: transparent;
    }

    .tenant-search-input-inline::placeholder {
        color: #a0aec0;
    }

    .tenant-search-input-inline:focus {
        outline: none;
    }

    .tenant-dropdown-btn i {
        font-size: 0.875rem;
        transition: transform 0.2s ease;
        color: #718096;
        margin-left: 0.5rem;
        flex-shrink: 0;
    }

    .tenant-dropdown-btn.open i {
        transform: rotate(180deg);
    }

    .tenant-dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 6px 6px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .tenant-dropdown-menu.show {
        display: block;
    }

    .tenant-list-container {
        max-height: 250px;
        overflow-y: auto;
    }

    .tenant-dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 1rem;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }

    .tenant-dropdown-item:last-child {
        border-bottom: none;
    }

    .tenant-dropdown-item:hover {
        background-color: #f7fafc;
    }

    .tenant-dropdown-item.selected {
        background-color: #e0f2fe;
    }

    .tenant-dropdown-item .tenant-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #03255b;
        flex-shrink: 0;
    }

    .tenant-dropdown-item .tenant-label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
        color: #2d3748;
        flex: 1;
        font-size: 0.9rem;
    }

    .tenant-dropdown-item input:checked + .tenant-label {
        color: #03255b;
        font-weight: 600;
    }

    .tenant-no-results {
        padding: 1rem;
        text-align: center;
        color: #718096;
        font-size: 0.875rem;
    }
</style>

<div class="booking-form-container">
    <h1 class="mb-4" style="color: #03255b; font-size: 2rem; font-weight: 700;">Edit Booking</h1>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-title">Schedule & Room</div>
        </div>
        <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-title">Booking Info</div>
        </div>
        <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-title">Review & Update</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading">Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form id="editBookingForm" action="{{ route('bookings.update', $booking->booking_id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Step 1: Schedule & Room -->
        <div class="step-content active" data-step="1">
            <h3 class="mb-4" style="color: #2d3748;">Adjust Schedule & Select Room</h3>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Check-in Date <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('checkin_date') is-invalid @enderror"
                               name="checkin_date"
                               id="checkin_date"
                               value="{{ old('checkin_date', $booking->checkin_date->format('Y-m-d')) }}"
                               required>
                        @error('checkin_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Check-out Date</label>
                        <input type="text"
                               class="form-control"
                               id="checkout_display"
                               readonly>
                        <input type="hidden" name="checkout_date" id="checkout_date" value="{{ old('checkout_date', $booking->checkout_date->format('Y-m-d')) }}">

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Stay Length <span class="text-danger">*</span></label>
                        <input type="number"
                               id="custom_stay_length"
                               name="custom_stay_length"
                               min="1"
                               class="form-control @error('stay_length') is-invalid @enderror"
                               placeholder="Enter days"
                               value="{{ old('stay_length', $defaultStayLength) }}">
                        <input type="hidden" name="stay_length" id="stay_length" value="{{ old('stay_length', $defaultStayLength) }}" required>
                        @error('stay_length')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label d-block mb-2">Quick Select</label>
                <div class="duration-presets">
                    <button type="button" class="duration-button" data-days="1">1 Day</button>
                    <button type="button" class="duration-button" data-days="7">7 Days</button>
                    <button type="button" class="duration-button" data-days="30">30 Days</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Room <span class="text-danger">*</span></label>
                <div id="roomsContainer" class="rooms-grid">
                    <p class="text-muted">Loading rooms...</p>
                </div>
                <input type="hidden" name="room_id" id="selected_room_id" value="{{ old('room_id', $booking->room_id) }}" required>
                @error('room_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Step 2: Booking Info -->
        <div class="step-content" data-step="2">
            <h3 class="mb-4" style="color: #2d3748;">Update Tenant Information</h3>

            <div class="form-group">
                <label class="form-label">Select Tenant(s) <span class="text-danger">*</span> <small class="text-muted">(Maximum: 2)</small></label>

                <div class="tenant-dropdown-wrapper">
                    <div class="tenant-dropdown-btn" id="tenantDropdownBtn">
                        <input type="text"
                               class="tenant-search-input-inline"
                               id="tenantSearchInput"
                               placeholder="Search or select tenants..."
                               autocomplete="off">
                        <i class="bi bi-chevron-down"></i>
                    </div>

                    <div class="tenant-dropdown-menu" id="tenantDropdownMenu">
                        <div class="tenant-list-container" id="tenantListContainer">
                            @foreach($tenants as $tenant)
                                @php
                                    $isCurrentTenant = (int)$tenant->tenant_id === (int)$booking->tenant_id ||
                                                      (int)$tenant->tenant_id === (int)$booking->secondary_tenant_id;
                                @endphp
                                <div class="tenant-dropdown-item {{ $isCurrentTenant ? 'selected' : '' }}"
                                     data-debug="tenant_id={{ $tenant->tenant_id }}, booking_tenant={{ $booking->tenant_id }}, secondary={{ $booking->secondary_tenant_id }}, match={{ $isCurrentTenant ? 'yes' : 'no' }}">
                                    <input type="checkbox"
                                           class="tenant-checkbox"
                                           name="tenant_ids[]"
                                           value="{{ $tenant->tenant_id }}"
                                           id="tenant_{{ $tenant->tenant_id }}"
                                           data-name="{{ $tenant->full_name }}"
                                           {{ $isCurrentTenant ? 'checked' : '' }}>
                                    <label class="tenant-label" for="tenant_{{ $tenant->tenant_id }}">
                                        {{ $tenant->full_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @error('tenant_ids')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('tenant_ids.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="info-box mt-3">
                <p><strong>Current Room:</strong> {{ $booking->room->room_num }} (Floor {{ $booking->room->floor }})</p>
                <p><strong>Current Rate:</strong> {{ $booking->rate->duration_type }} - ₱{{ number_format($booking->rate->base_price, 2) }}</p>
                <p><strong>Status:</strong> <span style="color: #10b981; font-weight: 600;">{{ $booking->status }}</span></p>
            </div>

        </div>

        <!-- Step 3: Review & Update -->
        <div class="step-content" data-step="3">
            <h3 class="mb-4" style="color: #2d3748;">Review Changes & Confirm</h3>

            <div class="summary-box">
                <div class="summary-row">
                    <span>Room:</span>
                    <span id="summary_room">{{ $booking->room->room_num }}</span>
                </div>
                <div class="summary-row">
                    <span>Tenant(s):</span>
                    <span id="summary_tenant">-</span>
                </div>
                <div class="summary-row">
                    <span>Check-in:</span>
                    <span id="summary_checkin">{{ $booking->checkin_date->format('M d, Y') }}</span>
                </div>
                <div class="summary-row">
                    <span>Check-out:</span>
                    <span id="summary_checkout">{{ $booking->checkout_date->format('M d, Y') }}</span>
                </div>
                <div class="summary-row">
                    <span>Total Nights:</span>
                    <span id="summary_nights">{{ $defaultStayLength }}</span>
                </div>
                <div class="summary-row">
                    <span>Rate Plan:</span>
                    <span id="summary_rate_plan">{{ $booking->rate->duration_type }}</span>
                </div>
                <div class="summary-section-title">Updated Charges</div>
                <ul class="charges-list" id="charges_list">
                    <li><span>Rate Total</span><span id="summary_rate_amount">₱0.00</span></li>
                    <li id="summary_deposit_row" style="display:none;"><span>Security Deposit</span><span id="summary_deposit_amount">₱0.00</span></li>
                    <!-- Utility rows will be dynamically added here -->
                </ul>
                <div class="italic-note" id="summary_inclusion_note" style="display:none;"></div>
                <div class="summary-row total">
                    <span>Total Due After Update:</span>
                    <span id="summary_total_due">₱0.00</span>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn-secondary-custom" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                <i class="bi bi-arrow-left"></i> Previous
            </button>
            <div style="margin-left: auto;">
                <a href="{{ route('bookings.show', $booking->booking_id) }}" class="btn-secondary-custom">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="button" class="btn-secondary-custom" id="nextBtn" onclick="changeStep(1)">
                    Next <i class="bi bi-arrow-right"></i>
                </button>
                <button type="submit" class="btn-primary-custom" id="submitBtn" style="display: none;">
                    <i class="bi bi-check-circle"></i> Update Booking
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;
const currentRoomId = {{ $booking->room_id }};

const ratesByDuration = {!! json_encode($ratesByDuration->mapWithKeys(function($rate) {
    return [$rate->duration_type => [
        'rate_id' => $rate->rate_id,
        'duration_type' => $rate->duration_type,
        'base_price' => $rate->base_price,
        'description' => $rate->description,
        'utilities' => $rate->utilities->map(function($utility) {
            return [
                'name' => $utility->name,
                'price' => $utility->price,
            ];
        })->values()->toArray(),
    ]];
})->toArray()) !!};

const monthlySecurityDeposit = {{ \App\Http\Controllers\BookingController::MONTHLY_SECURITY_DEPOSIT }};
let missingRateAlertShown = false;

function changeStep(direction) {
    if (direction > 0 && !validateStep(currentStep)) {
        return;
    }

    document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');

    currentStep += direction;

    document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.add('active');
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');

    for (let i = 1; i < currentStep; i++) {
        document.querySelector(`.step[data-step="${i}"]`).classList.add('completed');
    }

    document.getElementById('prevBtn').style.display = currentStep > 1 ? 'inline-block' : 'none';
    document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'inline-block' : 'none';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';

    if (currentStep === 3) {
        updateSummary();
    }
}

function validateStep(step) {
    if (step === 1) {
        const checkin = document.getElementById('checkin_date').value;
        const checkout = document.getElementById('checkout_date').value;
        const roomId = document.getElementById('selected_room_id').value;
        const stayLength = document.getElementById('stay_length').value;

        if (!checkin) {
            alert('Please select a check-in date.');
            return false;
        }

        if (!stayLength || parseInt(stayLength, 10) < 1) {
            alert('Please set the stay length.');
            return false;
        }

        if (!roomId || roomId === '0' || roomId === '') {
            alert('Please select a room.');
            return false;
        }

        if (new Date(checkout) <= new Date(checkin)) {
            alert('Check-out date must be after check-in date.');
            return false;
        }

        return true;
    }

    if (step === 2) {
        const tenantCount = getSelectedTenantCount();

        if (tenantCount === 0) {
            alert('Please select at least one tenant.');
            return false;
        }

        if (tenantCount > roomCapacityLimit) {
            alert(`Maximum ${roomCapacityLimit} tenants allowed per booking.`);
            return false;
        }

        return true;
    }

    return true;
}

function updateSummary() {
    const checkin = document.getElementById('checkin_date').value;
    const checkout = document.getElementById('checkout_date').value;
    const stayLength = parseInt(document.getElementById('stay_length').value || '0', 10);
    const roomCard = document.querySelector('.room-card.selected');
    const tenantNames = getSelectedTenantNames();

    document.getElementById('summary_room').textContent = roomCard ? roomCard.querySelector('.room-number').textContent : '{{ $booking->room->room_num }}';
    document.getElementById('summary_tenant').textContent = tenantNames.length ? tenantNames.join(' & ') : '-';
    document.getElementById('summary_checkin').textContent = checkin ? new Date(checkin).toLocaleDateString() : '-';
    document.getElementById('summary_checkout').textContent = checkout ? new Date(checkout).toLocaleDateString() : '-';
    document.getElementById('summary_nights').textContent = stayLength ? `${stayLength} night(s)` : '-';

    if (stayLength) {
        const pricing = calculatePricingSummary(stayLength);
        if (pricing) {
            const duration = determineRateDuration(stayLength);
            document.getElementById('summary_rate_plan').textContent = duration;
            document.getElementById('summary_rate_amount').textContent = '₱' + pricing.rateTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('summary_total_due').textContent = '₱' + pricing.totalDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            updateChargesDisplay(pricing);
        }
    }
}

function updateChargesDisplay(pricing) {
    const chargesList = document.getElementById('charges_list');
    const depositRow = document.getElementById('summary_deposit_row');
    const inclusionNote = document.getElementById('summary_inclusion_note');

    // Remove existing utility rows (keep Rate Total and Security Deposit)
    const existingUtilityRows = chargesList.querySelectorAll('.utility-row');
    existingUtilityRows.forEach(row => row.remove());

    // Display Security Deposit
    if (pricing.securityDeposit > 0) {
        depositRow.style.display = '';
        document.getElementById('summary_deposit_amount').textContent = '₱' + pricing.securityDeposit.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        depositRow.style.display = 'none';
    }

    // Dynamically add utility rows for ALL utilities
    if (pricing.utilityFees) {
        Object.keys(pricing.utilityFees).forEach(utilityName => {
            const utilityFee = pricing.utilityFees[utilityName];
            if (utilityFee > 0) {
                const li = document.createElement('li');
                li.className = 'utility-row';
                li.innerHTML = `<span>${utilityName}</span><span>₱${utilityFee.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`;
                // Insert before Security Deposit if it exists, or after Rate Total
                const depositRowElement = chargesList.querySelector('#summary_deposit_row');
                if (depositRowElement && depositRowElement.style.display !== 'none') {
                    chargesList.insertBefore(li, depositRowElement.nextSibling);
                } else {
                    // Insert after Rate Total
                    const rateTotalRow = chargesList.querySelector('li:first-child');
                    chargesList.insertBefore(li, rateTotalRow.nextSibling);
                }
            }
        });
    }

    if (pricing.inclusionNote) {
        inclusionNote.style.display = '';
        inclusionNote.textContent = pricing.inclusionNote;
    } else {
        inclusionNote.style.display = 'none';
    }
}

function determineRateDuration(days) {
    if (days >= 30) return 'Monthly';
    if (days >= 7) return 'Weekly';
    return 'Daily';
}

function calculatePricingSummary(days) {
    const duration = determineRateDuration(days);
    const rate = ratesByDuration[duration];

    if (!rate) {
        if (!missingRateAlertShown) {
            alert(`No ${duration.toLowerCase()} rate configured.`);
            missingRateAlertShown = true;
        }
        return null;
    }

    let rateTotal = 0, securityDeposit = 0, inclusionNote = '';
    const utilityFees = {}; // Store all utilities dynamically

    // Get utilities from rate
    const utilities = {};
    if (rate.utilities) {
        rate.utilities.forEach(utility => {
            utilities[utility.name] = utility.price;
        });
    }

    if (duration === 'Monthly') {
        // Calculate full months and remaining days
        const fullMonths = Math.floor(days / 30);
        const remainingDays = days % 30;

        // Calculate rate total: full months at monthly rate
        rateTotal = rate.base_price * fullMonths;

        // IMPORTANT: Utilities are charged ONLY for full months, NOT for partial months
        // Calculate fees for ALL utilities
        Object.keys(utilities).forEach(utilityName => {
            if (fullMonths > 0) {
                utilityFees[utilityName] = utilities[utilityName] * fullMonths;
            } else if (days > 0 && fullMonths === 0) {
                // For stays less than 30 days, charge 1 month
                utilityFees[utilityName] = utilities[utilityName];
            } else {
                utilityFees[utilityName] = 0;
            }
        });

        // For remaining days, use daily rate base_price (rent only)
        // NOTE: If daily rate includes utilities in base_price, extract rent-only portion
        // Utilities are NOT included in rateTotal
        if (remainingDays > 0) {
            const dailyRate = ratesByDuration['Daily'];
            if (dailyRate) {
                // Calculate daily rent-only amount
                // If daily rate has utilities, subtract them from base_price to get rent-only
                let dailyRentOnly = dailyRate.base_price;
                if (dailyRate.utilities && dailyRate.utilities.length > 0) {
                    const dailyUtilities = {};
                    dailyRate.utilities.forEach(utility => {
                        dailyUtilities[utility.name] = utility.price;
                    });
                    // Subtract ALL utilities from daily rate to get rent-only
                    Object.keys(dailyUtilities).forEach(utilityName => {
                        dailyRentOnly -= (dailyUtilities[utilityName] / 30); // Convert monthly utility to daily
                    });
                }
                rateTotal += dailyRentOnly * remainingDays;
            } else {
                // Fallback: prorate monthly rate (rent only)
                const dailyPrice = rate.base_price / 30;
                rateTotal += dailyPrice * remainingDays;
            }
        }

        // Ensure at least 1 month if days > 0 but less than 30
        if (days > 0 && fullMonths === 0) {
            rateTotal = rate.base_price;
        }

        securityDeposit = monthlySecurityDeposit;
        inclusionNote = 'Security deposit is a separate one-time payment (not included in invoice). Utilities are itemized separately for monthly stays.';
    } else if (duration === 'Weekly') {
        const weeks = Math.max(1, Math.ceil(days / 7));
        rateTotal = rate.base_price * weeks;
        inclusionNote = 'Water and Wi-Fi are included in the weekly package.';
    } else {
        rateTotal = rate.base_price * Math.max(1, days);
        inclusionNote = 'Water and Wi-Fi are included in the daily package.';
    }

    // Calculate total due
    const utilitiesTotal = Object.values(utilityFees).reduce((sum, fee) => sum + fee, 0);
    const totalDue = rateTotal + securityDeposit + utilitiesTotal;

    return { rateTotal, securityDeposit, utilityFees, totalDue, inclusionNote };
}

function loadRooms() {
    const checkin = document.getElementById('checkin_date').value;
    const checkout = document.getElementById('checkout_date').value;

    if (!checkin || !checkout) {
        document.getElementById('roomsContainer').innerHTML = '<p class="text-muted">Please select check-in date and stay length to see available rooms.</p>';
        return;
    }

    if (new Date(checkout) <= new Date(checkin)) {
        document.getElementById('roomsContainer').innerHTML = '<p class="text-danger">Check-out date must be after check-in date.</p>';
        return;
    }

    // Preserve the currently selected room ID before reloading
    const roomIdInput = document.getElementById('selected_room_id');
    const currentlySelectedRoomId = roomIdInput ? parseInt(roomIdInput.value) || currentRoomId : currentRoomId;

    document.getElementById('roomsContainer').innerHTML = '<p class="text-muted">Loading available rooms...</p>';

    fetch(`{{ route('bookings.check-availability') }}?checkin_date=${checkin}&checkout_date=${checkout}&exclude_booking_id={{ $booking->booking_id }}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('roomsContainer');
            if (data.available_rooms && data.available_rooms.length > 0) {
                // Check if current room is in available rooms
                const currentRoomInAvailable = data.available_rooms.find(r => r.room_id == currentRoomId);

                // Build HTML for available rooms, marking the selected one
                let html = data.available_rooms.map(room => {
                    const isSelected = room.room_id == currentlySelectedRoomId;
                    return `
                        <div class="room-card ${isSelected ? 'selected' : ''}"
                             data-room-id="${room.room_id}"
                             onclick="selectRoom(${room.room_id})">
                            <div class="room-number">${room.room_num}</div>
                            <div class="room-floor">Floor ${room.floor}</div>
                            ${room.room_id == currentRoomId ? '<small style="color: #f59e0b;">Current</small>' : ''}
                        </div>
                    `;
                }).join('');

                // If current room is not in available rooms, add it at the beginning
                if (!currentRoomInAvailable) {
                    const isCurrentSelected = currentRoomId == currentlySelectedRoomId;
                    html = `
                        <div class="room-card ${isCurrentSelected ? 'selected' : ''}"
                             data-room-id="${currentRoomId}"
                             onclick="selectRoom(${currentRoomId})">
                            <div class="room-number">{{ $booking->room->room_num }}</div>
                            <div class="room-floor">Floor {{ $booking->room->floor }}</div>
                            <small style="color: #f59e0b;">Current</small>
                        </div>
                    ` + html;
                }

                // Set the room_id input to the preserved selection (or current room if nothing was selected)
                if (roomIdInput) {
                    if (!roomIdInput.value || roomIdInput.value === '0') {
                        roomIdInput.value = currentlySelectedRoomId;
                    }
                }

                container.innerHTML = html;
            } else {
                // No available rooms, show current room only
                const isCurrentSelected = currentRoomId == currentlySelectedRoomId;
                container.innerHTML = `
                    <div class="room-card ${isCurrentSelected ? 'selected' : ''}"
                         data-room-id="${currentRoomId}"
                         onclick="selectRoom(${currentRoomId})">
                        <div class="room-number">{{ $booking->room->room_num }}</div>
                        <div class="room-floor">Floor {{ $booking->room->floor }}</div>
                        <small style="color: #f59e0b;">Current</small>
                    </div>
                    <p class="text-muted mt-2">No other rooms available for these dates.</p>
                `;
                if (roomIdInput) {
                    roomIdInput.value = currentRoomId;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('roomsContainer').innerHTML = '<p class="text-danger">Error loading rooms. Please try again.</p>';
        });
}

function selectRoom(roomId) {
    document.querySelectorAll('.room-card').forEach(card => {
        card.classList.remove('selected');
    });

    const card = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
    if (card) {
        card.classList.add('selected');
        document.getElementById('selected_room_id').value = roomId;

        if (currentStep === 3) {
            updateSummary();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Set up event listeners first
    const checkinInput = document.getElementById('checkin_date');
    const stayLengthInput = document.getElementById('custom_stay_length');

    if (checkinInput) {
        checkinInput.addEventListener('change', calculateCheckoutDate);
    }

    if (stayLengthInput) {
        stayLengthInput.addEventListener('input', handleCustomStayLength);
    }

    // Initialize duration buttons
    initializeDurationButtons();

    // Calculate checkout date and set initial values
    calculateCheckoutDate();

    // Highlight initial duration button
    highlightInitialDuration();

    // Update summary
    updateSummary();

    // Load rooms
    loadRooms();

    // Form submission validation
    const editForm = document.getElementById('editBookingForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            // Ensure checkout_date is calculated before submission
            calculateCheckoutDate();

            const checkinDate = document.getElementById('checkin_date').value;
            const stayLength = document.getElementById('stay_length').value;
            const roomId = document.getElementById('selected_room_id').value;
            const checkoutDate = document.getElementById('checkout_date').value;

            console.log('Form submission validation:', {
                checkinDate: checkinDate,
                stayLength: stayLength,
                roomId: roomId,
                checkoutDate: checkoutDate,
            });

            if (!checkinDate) {
                e.preventDefault();
                alert('Please select a check-in date.');
                currentStep = 1;
                changeStep(0);
                return false;
            }

            if (!stayLength || parseInt(stayLength, 10) < 1) {
                e.preventDefault();
                alert('Please set the stay length.');
                currentStep = 1;
                changeStep(0);
                return false;
            }

            if (!roomId || roomId === '0' || roomId === '') {
                e.preventDefault();
                alert('Please select a room.');
                currentStep = 1;
                changeStep(0);
                return false;
            }

            if (!checkoutDate) {
                e.preventDefault();
                alert('Check-out date is missing. Please check your dates.');
                currentStep = 1;
                changeStep(0);
                return false;
            }

            if (new Date(checkoutDate) <= new Date(checkinDate)) {
                e.preventDefault();
                alert('Check-out date must be after check-in date.');
                currentStep = 1;
                changeStep(0);
                return false;
            }

            console.log('All validations passed, form will submit');
            return true;
        });
    }
});

function initializeDurationButtons() {
    document.querySelectorAll('.duration-button').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const days = parseInt(button.dataset.days, 10);
            setStayLength(days);
            highlightDurationButton(button);
        });
    });
}

function highlightInitialDuration() {
    const stayLength = parseInt(document.getElementById('stay_length').value || '0', 10);
    const matching = Array.from(document.querySelectorAll('.duration-button')).find(btn => parseInt(btn.dataset.days, 10) === stayLength);
    if (matching) {
        highlightDurationButton(matching);
    } else if (stayLength) {
        document.getElementById('custom_stay_length').value = stayLength;
    }
}

function highlightDurationButton(activeButton) {
    document.querySelectorAll('.duration-button').forEach(btn => btn.classList.remove('active'));
    if (activeButton) activeButton.classList.add('active');
}

function setStayLength(days) {
    if (!days || days < 1) {
        console.error('Invalid stay length:', days);
        return;
    }

    console.log('Setting stay length to:', days);
    document.getElementById('stay_length').value = days;
    document.getElementById('custom_stay_length').value = days;
    calculateCheckoutDate();

    if (currentStep === 3) {
        updateSummary();
    }
}

function calculateCheckoutDate() {
    const checkinValue = document.getElementById('checkin_date').value;
    const stayLength = parseInt(document.getElementById('stay_length').value || '0', 10);
    const checkoutDateInput = document.getElementById('checkout_date');
    const checkoutDisplay = document.getElementById('checkout_display');

    if (!checkinValue || stayLength <= 0) {
        // If no checkin or stay length, try to use existing checkout date from booking
        const existingCheckout = checkoutDateInput.value;
        if (existingCheckout) {
            try {
                const checkoutDate = new Date(existingCheckout + 'T00:00:00');
                if (!isNaN(checkoutDate.getTime())) {
                    checkoutDisplay.value = checkoutDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                }
            } catch (e) {
                console.error('Error parsing existing checkout date:', e);
                checkoutDisplay.value = '';
            }
        } else {
            checkoutDisplay.value = '';
        }
        return;
    }

    try {
        const checkinDate = new Date(checkinValue + 'T00:00:00'); // Add time to avoid timezone issues
        if (isNaN(checkinDate.getTime())) {
            console.error('Invalid check-in date:', checkinValue);
            return;
        }

        const checkoutDate = new Date(checkinDate);
        checkoutDate.setDate(checkoutDate.getDate() + stayLength);

        // Format as YYYY-MM-DD for hidden input
        const year = checkoutDate.getFullYear();
        const month = String(checkoutDate.getMonth() + 1).padStart(2, '0');
        const day = String(checkoutDate.getDate()).padStart(2, '0');
        const isoDate = `${year}-${month}-${day}`;

        checkoutDateInput.value = isoDate;
        checkoutDisplay.value = checkoutDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

        console.log('Checkout date calculated:', {
            checkin: checkinValue,
            stayLength: stayLength,
            checkout: isoDate
        });

        // Only reload rooms if we have valid dates
        if (checkinValue && stayLength > 0) {
            loadRooms();
        }
    } catch (e) {
        console.error('Error calculating checkout date:', e);
    }
}

function handleCustomStayLength(event) {
    const value = parseInt(event.target.value, 10);
    if (!isNaN(value) && value > 0) {
        setStayLength(value);
        highlightDurationButton(null);
    }
}


// Tenant dropdown functionality
let roomCapacityLimit = 2;

function getSelectedTenantNames() {
    const checkboxes = document.querySelectorAll('.tenant-checkbox:checked');
    return Array.from(checkboxes)
        .map(cb => cb.getAttribute('data-name'))
        .filter(name => name);
}

function getSelectedTenantCount() {
    return document.querySelectorAll('.tenant-checkbox:checked').length;
}

function enforceTenantSelectionLimit({ notify = true } = {}) {
    const checkboxes = document.querySelectorAll('.tenant-checkbox:checked');
    let removed = false;

    if (checkboxes.length > roomCapacityLimit) {
        // Uncheck all except the first two
        for (let i = roomCapacityLimit; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
            checkboxes[i].closest('.tenant-dropdown-item')?.classList.remove('selected');
            removed = true;
        }
        if (notify) {
            alert(`Maximum ${roomCapacityLimit} tenants allowed per booking.`);
        }
    }
    return removed;
}

function updateTenantDropdownText() {
    const selectedTenants = getSelectedTenantNames();
    const searchInput = document.getElementById('tenantSearchInput');

    if (selectedTenants.length === 0) {
        searchInput.placeholder = 'Search or select tenants...';
        searchInput.value = '';
    } else {
        searchInput.value = selectedTenants.join(' & ');
        searchInput.placeholder = 'Search or select tenants...';
    }

    if (currentStep === 3) {
        updateSummary();
    }
}

// Initialize tenant dropdown on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('tenantDropdownBtn');
    const dropdownMenu = document.getElementById('tenantDropdownMenu');
    const searchInput = document.getElementById('tenantSearchInput');
    const listContainer = document.getElementById('tenantListContainer');

    if (dropdownBtn && searchInput) {
        // Toggle dropdown
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
            dropdownBtn.classList.toggle('open');
            if (dropdownMenu.classList.contains('show')) {
                searchInput.focus();
            }
        });

        // Prevent dropdown from closing when clicking inside
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                dropdownBtn.classList.remove('open');
            }
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = listContainer.querySelectorAll('.tenant-dropdown-item');
            let hasResults = false;

            items.forEach(item => {
                const label = item.querySelector('.tenant-label');
                const tenantName = label ? label.textContent.toLowerCase() : '';

                if (tenantName.includes(searchTerm)) {
                    item.style.display = '';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show "no results" message if needed
            let noResultsMsg = listContainer.querySelector('.tenant-no-results');
            if (!hasResults) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'tenant-no-results';
                    noResultsMsg.textContent = 'No tenants found';
                    listContainer.appendChild(noResultsMsg);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        });
    }

    // Tenant checkbox listeners
    const tenantCheckboxes = document.querySelectorAll('.tenant-checkbox');
    tenantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const item = this.closest('.tenant-dropdown-item');
            if (this.checked) {
                item?.classList.add('selected');
                const removed = enforceTenantSelectionLimit({ notify: true });
                if (removed) {
                    updateTenantDropdownText();
                }
            } else {
                item?.classList.remove('selected');
            }
            updateTenantDropdownText();
        });
    });

    // Update dropdown text on load
    updateTenantDropdownText();
    enforceTenantSelectionLimit({ notify: false });
});

</script>
@endsection

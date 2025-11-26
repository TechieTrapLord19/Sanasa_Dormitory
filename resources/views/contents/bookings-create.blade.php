@extends('layouts.app')

@section('title', 'Create New Booking')

@section('content')
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
        margin-bottom:1px;
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

    .custom-duration-input {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .custom-duration-input input[type="number"] {
        width: 120px;
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

    .room-card.unavailable {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #fee2e2;
        border-color: #dc2626;
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

    .add-tenant-btn {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        margin-top: 0.5rem;
    }

    .add-tenant-btn:hover {
        background-color: #bae6fd;
    }

    .error-text {
        color: #dc2626;
        font-size: 0.8rem;
        margin-top: 0.25rem;
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
    <h1 class="mb-4" style="color: #03255b; font-size: 2rem; font-weight: 700;">Create New Booking</h1>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-title">Schedule & Room</div>
        </div>
        <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-title">Tenant</div>
        </div>
        <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-title">Review & Confirm</div>
        </div>
    </div>

    <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST" novalidate>
        @csrf

        <!-- Step 1: Schedule & Room -->
                <div class="step-content active" data-step="1">
            <h3 class="mb-4" style="color: #2d3748;">Select Stay & Room</h3>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Check-in Date <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('checkin_date') is-invalid @enderror"
                               name="checkin_date"
                               id="checkin_date"
                               value="{{ old('checkin_date') }}"
                               required
                               min="{{ date('Y-m-d') }}">
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
                               value=""
                               readonly>
                        <input type="hidden" name="checkout_date" id="checkout_date" value="{{ old('checkout_date') }}">
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
                               placeholder="Enter days">
                        <input type="hidden" name="stay_length" id="stay_length" value="{{ old('stay_length') }}" required>
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
                <label class="form-label">Available Rooms <span class="text-danger">*</span></label>
                <div id="roomsContainer" class="rooms-grid">
                    <p class="text-muted">Please select check-in date and stay length to see available rooms.</p>
                </div>
            <input type="hidden" name="room_id" id="selected_room_id" value="{{ old('room_id') }}" required>                @error('room_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Step 2: Tenant -->
        <div class="step-content" data-step="2">
            <h3 class="mb-3" style="color: #2d3748;">Select Tenant(s)</h3>

            <div class="form-group">
                <label class="form-label">Tenant(s) <span class="text-danger">*</span></label>
                <div class="tenant-dropdown-wrapper">
                    <div class="tenant-dropdown-btn" id="tenantDropdownBtn">
                        <input type="text"
                               id="tenantSearchInput"
                               class="tenant-search-input-inline"
                               placeholder="Search or select tenants..."
                               autocomplete="off">
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div id="tenantDropdownMenu" class="tenant-dropdown-menu" style="display: none;">
                        <div id="tenantListContainer" class="tenant-list-container">
                            @if($tenants->isEmpty())
                                <p class="text-muted p-3">No available tenants</p>
                            @else
                                @foreach($tenants as $tenant)
                                    <div class="tenant-dropdown-item" data-tenant-name="{{ strtolower($tenant->full_name) }}">
                                        <input type="checkbox"
                                               class="tenant-checkbox"
                                               name="tenant_ids[]"
                                               value="{{ $tenant->tenant_id }}"
                                               id="tenant_{{ $tenant->tenant_id }}"
                                               {{ in_array($tenant->tenant_id, old('tenant_ids', [])) ? 'checked' : '' }}>
                                        <label for="tenant_{{ $tenant->tenant_id }}" class="tenant-label">
                                            {{ $tenant->full_name }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">Select up to 2 tenants per booking (limited by room capacity).</small>
                @error('tenant_ids')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                <button type="button" class="add-tenant-btn" id="openTenantModal" data-bs-toggle="modal" data-bs-target="#tenantModal">
                    + Add New Tenant
                </button>
            </div>
            <input type="hidden" name="rate_id" id="rate_id" value="{{ old('rate_id') }}" required>
        </div>
        <!-- Step 3: Review & Confirm -->
        <div class="step-content" data-step="3">
            <h3 class="mb-3" style="color: #2d3748;">Review & Confirm</h3>

            <div class="summary-box">
                <div class="summary-row">
                    <span>Room:</span>
                    <span id="summary_room">-</span>
                </div>
                <div class="summary-row">
                    <span>Check-in Date:</span>
                    <span id="summary_checkin">-</span>
                </div>
                <div class="summary-row">
                    <span>Check-out Date:</span>
                    <span id="summary_checkout">-</span>
                </div>
                <div class="summary-row">
                    <span>Total Nights:</span>
                    <span id="summary_nights">-</span>
                </div>
                <div class="summary-row">
                    <span>Tenant(s):</span>
                    <span id="summary_tenant">-</span>
                </div>
                <div class="summary-row">
                    <span>Rate:</span>
                    <span id="summary_rate">-</span>
                </div>
                <div class="summary-section-title">Charges & Inclusions</div>
                <ul class="charges-list" id="charges_list">
                    <li><span>Rate Total</span><span id="summary_rate_amount">₱0.00</span></li>
                    <li id="summary_deposit_row" style="display:none;"><span>Security Deposit</span><span id="summary_deposit_amount">₱0.00</span></li>
                    <!-- Utility rows will be dynamically added here -->
                </ul>
                <div class="italic-note" id="summary_inclusion_note" style="display:none;"></div>
                <div class="summary-row total">
                    <span>Total Due on Arrival:</span>
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
                <button type="button" class="btn-secondary-custom" id="nextBtn" onclick="changeStep(1)">
                    Next <i class="bi bi-arrow-right"></i>
                </button>
                <button type="submit" class="btn-primary-custom" id="submitBtn" style="display: none;">
                    <i class="bi bi-check-circle"></i> Confirm Booking
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Tenant Modal -->
<div class="modal fade" id="tenantModal" tabindex="-1" aria-labelledby="tenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tenantModalLabel">Add New Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tenantForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_num" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_num" name="contact_num" placeholder="0912-345-6789">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emer_contact_num" class="form-label">Emergency Contact Number</label>
                            <input type="text" class="form-control" id="emer_contact_num" name="emer_contact_num" placeholder="0917-969-4567">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Birth Date</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="Street, City, Province"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_document" class="form-label">ID Document</label>
                            <input type="text" class="form-control" id="id_document" name="id_document" placeholder="e.g., Driver's License, Passport">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div id="tenantFormError" class="error-text" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Save Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;
const ratesByDuration = {!! json_encode($ratesByDuration->mapWithKeys(function($rate) {
    $utilities = $rate->utilities->keyBy('name');
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
let roomCapacityLimit = 2;

function getTenantSelectElement() {
    return document.querySelectorAll('.tenant-checkbox');
}

function getSelectedTenantNames() {
    const checkboxes = document.querySelectorAll('.tenant-checkbox:checked');
    return Array.from(checkboxes)
        .map(checkbox => {
            const label = document.querySelector(`label[for="${checkbox.id}"]`);
            return label ? label.textContent.trim() : '';
        })
        .filter(name => name.length > 0);
}

function getSelectedTenantCount() {
    return document.querySelectorAll('.tenant-checkbox:checked').length;
}

function enforceTenantSelectionLimit({ notify = true } = {}) {
    const checkboxes = document.querySelectorAll('.tenant-checkbox:checked');
    let removed = false;

    if (checkboxes.length > roomCapacityLimit) {
        // Uncheck the last selected ones
        for (let i = roomCapacityLimit; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
            checkboxes[i].parentElement.classList.remove('selected');
        }
        removed = true;
        if (notify) {
            alert(`You can select up to ${roomCapacityLimit} tenant(s) for the selected room.`);
        }
    }
    return removed;
}

// STEP NAVIGATION
function changeStep(direction) {
    if (direction > 0 && currentStep < totalSteps) {
        if (!validateStep(currentStep)) {
            console.log('Validation failed for step', currentStep);
            return;
        }
    }

    if (direction > 0 && currentStep >= totalSteps) {
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
        const stayLength = document.getElementById('stay_length').value;
        const roomId = document.getElementById('selected_room_id').value;

        if (!checkin) {
            alert('Please select a check-in date.');
            return false;
        }

        if (!stayLength || parseInt(stayLength, 10) < 1) {
            alert('Please choose the stay length.');
            return false;
        }

        if (!roomId) {
            alert('Please select a room.');
            return false;
        }

        return true;
    }

    if (step === 2) {
        // Check if at least one tenant checkbox is selected
        const tenantCheckboxes = document.querySelectorAll('.tenant-checkbox:checked');

        console.log('Tenant checkboxes checked:', tenantCheckboxes.length);

        if (tenantCheckboxes.length === 0) {
            alert('Please select at least one tenant.');
            return false;
        }

        return true;
    }

    return true;
}

// STAY LENGTH & DURATION LOGIC
function determineRateDuration(days) {
    if (days >= 30) {
        return 'Monthly';
    }
    if (days >= 7) {
        return 'Weekly';
    }
    return 'Daily';
}

function setStayLength(days) {
    console.log('Setting stay length to:', days);
    document.getElementById('stay_length').value = days;
    document.getElementById('custom_stay_length').value = days;

    calculateCheckoutDate();
    updateRateSelection(days);

    if (currentStep === 3) {
        updateSummary();
    }
}

function calculateCheckoutDate() {
    const checkinValue = document.getElementById('checkin_date').value;
    const stayLength = parseInt(document.getElementById('stay_length').value || '0', 10);

    if (!checkinValue || stayLength <= 0) {
        document.getElementById('checkout_date').value = '';
        document.getElementById('checkout_display').value = '';
        return;
    }

    const checkinDate = new Date(checkinValue);
    const checkoutDate = new Date(checkinDate);
    checkoutDate.setDate(checkoutDate.getDate() + stayLength);

    const isoDate = checkoutDate.toISOString().split('T')[0];
    document.getElementById('checkout_date').value = isoDate;
    document.getElementById('checkout_display').value = checkoutDate.toLocaleDateString();

    checkAvailability();
}

function updateRateSelection(days) {
    if (!days || days < 1) {
        document.getElementById('rate_id').value = '';
        return;
    }

    const duration = determineRateDuration(days);
    const rate = ratesByDuration[duration];

    if (!rate) {
        if (!missingRateAlertShown) {
            alert(`No ${duration.toLowerCase()} rate configured. Please create one before proceeding.`);
            missingRateAlertShown = true;
        }
        document.getElementById('rate_id').value = '';
        return;
    }

    console.log(`Days: ${days}, Duration: ${duration}, Rate ID: ${rate.rate_id}`);
    document.getElementById('rate_id').value = rate.rate_id;
}

// DURATION BUTTONS
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

function highlightDurationButton(activeButton) {
    document.querySelectorAll('.duration-button').forEach(btn => {
        btn.classList.remove('active');
    });
    if (activeButton) {
        activeButton.classList.add('active');
    }
}

function handleCustomStayLength(event) {
    const value = parseInt(event.target.value, 10);
    if (!isNaN(value) && value > 0) {
        setStayLength(value);
        highlightDurationButton(null);
    }
}

// SUMMARY & PRICING
function updateSummary() {
    const roomSelect = document.querySelector('.room-card.selected');
    const checkin = document.getElementById('checkin_date').value;
    const checkout = document.getElementById('checkout_date').value;
    const stayLength = parseInt(document.getElementById('stay_length').value || '0', 10);
    const { rate, duration } = determineRateForSummary(stayLength);

    document.getElementById('summary_room').textContent = roomSelect ? roomSelect.querySelector('.room-number').textContent : '-';
    document.getElementById('summary_checkin').textContent = checkin ? new Date(checkin).toLocaleDateString() : '-';
    document.getElementById('summary_checkout').textContent = checkout ? new Date(checkout).toLocaleDateString() : '-';
    document.getElementById('summary_nights').textContent = stayLength ? `${stayLength} night(s)` : '-';
    const tenantNames = getSelectedTenantNames();
    document.getElementById('summary_tenant').textContent = tenantNames.length ? tenantNames.join(' & ') : '-';
    document.getElementById('summary_rate').textContent = duration && rate ? `${duration} - ₱${Number(rate.base_price).toLocaleString('en-US', { minimumFractionDigits: 2 })}` : '-';

    if (checkin && checkout && stayLength && rate) {
        const pricing = calculatePricingSummary(stayLength, rate);

        // Rate Total shows ONLY the rent amount (no utilities)
        document.getElementById('summary_rate_amount').textContent = '₱' + pricing.rateTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Total due includes security deposit (but it's paid separately)
        document.getElementById('summary_total_due').textContent = '₱' + pricing.totalDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

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
                    // Insert before the total row (which is the last item, but we need to insert before Security Deposit if it exists, or at the end)
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
}

function determineRateForSummary(days) {
    if (!days) {
        return { rate: null, duration: null };
    }
    const duration = determineRateDuration(days);
    const rate = ratesByDuration[duration] || null;
    return { rate, duration };
}

function calculatePricingSummary(days, rate) {
    const duration = determineRateDuration(days);
    let rateTotal = 0;
    let inclusionNote = '';
    let securityDeposit = 0;
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
        inclusionNote = 'Security deposit is a separate invoice. Utilities are itemized separately for monthly stays.';
    } else if (duration === 'Weekly') {
        const weeks = Math.max(1, Math.ceil(days / 7));
        rateTotal = rate.base_price * weeks;
        inclusionNote = 'Water, Wi-Fi and Electricity are included in the weekly package.';
    } else {
        rateTotal = rate.base_price * Math.max(1, days);
        inclusionNote = 'Water, Wi-Fi and Electricity are included in the daily package.';
    }

    // Calculate total due
    const utilitiesTotal = Object.values(utilityFees).reduce((sum, fee) => sum + fee, 0);
    const totalDue = rateTotal + securityDeposit + utilitiesTotal;

    return { rateTotal, securityDeposit, utilityFees, totalDue, inclusionNote };
}

// ROOM AVAILABILITY
function checkAvailability() {
    const checkin = document.getElementById('checkin_date').value;
    const checkout = document.getElementById('checkout_date').value;

    if (!checkin || !checkout) {
        return;
    }

    if (new Date(checkout) <= new Date(checkin)) {
        document.getElementById('roomsContainer').innerHTML = '<p class="text-danger">Check-out date must be after check-in date.</p>';
        return;
    }

    document.getElementById('roomsContainer').innerHTML = '<p class="text-muted">Loading available rooms...</p>';

    fetch(`{{ route('bookings.check-availability') }}?checkin_date=${checkin}&checkout_date=${checkout}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('roomsContainer');
            if (data.available_rooms && data.available_rooms.length > 0) {
                container.innerHTML = data.available_rooms.map(room => {
                    const capacity = room.capacity !== undefined && room.capacity !== null ? room.capacity : 2;
                    return `
                    <div class="room-card" data-room-id="${room.room_id}" data-capacity="${capacity}" onclick="selectRoom(${room.room_id})">
                        <div class="room-number">${room.room_num}</div>
                        <div class="room-floor">Floor ${room.floor} • Capacity: ${capacity}</div>
                    </div>`;
                }).join('');
            } else {
                container.innerHTML = '<p class="text-muted">No rooms available for the selected dates.</p>';
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
    if (card && !card.classList.contains('unavailable')) {
        card.classList.add('selected');
        document.getElementById('selected_room_id').value = roomId;
        const capacity = parseInt(card.getAttribute('data-capacity'), 10);
        roomCapacityLimit = isNaN(capacity) ? 2 : capacity;
        const removed = enforceTenantSelectionLimit({ notify: false });
        if (removed) {
            alert(`Some tenants were unselected because the selected room allows up to ${roomCapacityLimit} tenant(s).`);
        }
        if (currentStep === 3) {
            updateSummary();
        }
    }
}

// FORM SUBMISSION
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const checkinDate = document.getElementById('checkin_date').value;
    const stayLength = document.getElementById('stay_length').value;
    const roomId = document.getElementById('selected_room_id').value;
    const tenantCheckboxes = document.querySelectorAll('.tenant-checkbox:checked');
    const rateId = document.getElementById('rate_id').value;

    console.log('Form submission validation:', {
        checkinDate: !!checkinDate,
        stayLength: !!stayLength,
        roomId: !!roomId,
        tenantsSelected: tenantCheckboxes.length,
        rateId: !!rateId,
    });

    if (!checkinDate || !stayLength || !roomId || tenantCheckboxes.length === 0 || !rateId) {
        e.preventDefault();
        alert('Please complete all required fields before confirming.');
        return false;
    }

    console.log('All validations passed, form will submit');
    return true;
});

// DOM INITIALIZATION
document.addEventListener('DOMContentLoaded', function() {
    initializeDurationButtons();

    document.getElementById('checkin_date').addEventListener('change', calculateCheckoutDate);
    document.getElementById('custom_stay_length').addEventListener('input', handleCustomStayLength);

    // Tenant dropdown and search functionality
    const dropdownBtn = document.getElementById('tenantDropdownBtn');
    const dropdownMenu = document.getElementById('tenantDropdownMenu');
    const searchInput = document.getElementById('tenantSearchInput');
    const listContainer = document.getElementById('tenantListContainer');

    if (dropdownBtn && searchInput) {
        // Open/close dropdown on input click
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdownMenu.style.display !== 'none';
            dropdownMenu.style.display = isOpen ? 'none' : 'block';
            dropdownBtn.classList.toggle('open');
            if (dropdownMenu.style.display === 'block') {
                searchInput.focus();
            }
        });

        // Live search on input
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tenantItems = listContainer.querySelectorAll('.tenant-dropdown-item');
            let visibleCount = 0;

            tenantItems.forEach(item => {
                const tenantName = item.getAttribute('data-tenant-name');
                if (tenantName.includes(searchTerm)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show "no results" message
            let noResultsMsg = listContainer.querySelector('.tenant-no-results');
            if (visibleCount === 0 && searchTerm.length > 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'tenant-no-results';
                    noResultsMsg.textContent = 'No tenants found';
                    listContainer.appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = '';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
                dropdownBtn.classList.remove('open');
                searchInput.value = '';
                const tenantItems = listContainer.querySelectorAll('.tenant-dropdown-item');
                tenantItems.forEach(item => item.style.display = '');
                const noResultsMsg = listContainer.querySelector('.tenant-no-results');
                if (noResultsMsg) noResultsMsg.style.display = 'none';
            }
        });
    }

    // Tenant checkbox listeners
    const tenantCheckboxes = document.querySelectorAll('.tenant-checkbox');
    tenantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update visual feedback
            const item = this.parentElement;
            if (this.checked) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }

            updateTenantDropdownText();
            const removed = enforceTenantSelectionLimit();
            if (removed && currentStep !== 3) {
                // Alert already shown when notify true
            }
            if (currentStep === 3) {
                updateSummary();
            }
        });
    });

    // Update dropdown text on load
    updateTenantDropdownText();
    enforceTenantSelectionLimit({ notify: false });

    // Tenant modal
    const tenantModalElement = document.getElementById('tenantModal');
    const tenantModal = tenantModalElement ? new bootstrap.Modal(tenantModalElement) : null;
    const tenantForm = document.getElementById('tenantForm');
    const tenantFormError = document.getElementById('tenantFormError');

    if (tenantModalElement) {
        tenantModalElement.addEventListener('show.bs.modal', function () {
            tenantForm.reset();
            tenantFormError.style.display = 'none';
        });

        tenantModalElement.addEventListener('hidden.bs.modal', function () {
            tenantForm.reset();
            tenantFormError.style.display = 'none';
        });
    }

    tenantForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        tenantFormError.style.display = 'none';

        const formData = new FormData(tenantForm);

        try {
            const response = await fetch('{{ route('tenants.store') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData,
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => null);
                let message = errorData?.message || 'Failed to save tenant.';
                if (errorData?.errors) {
                    const firstError = Object.values(errorData.errors).flat()[0];
                    if (firstError) message = firstError;
                }
                tenantFormError.textContent = message;
                tenantFormError.style.display = 'block';
                return;
            }

            const tenant = await response.json();
            if (tenant && tenant.tenant_id) {
                const listContainer = document.getElementById('tenantListContainer');
                const newCheckboxItem = document.createElement('div');
                newCheckboxItem.className = 'tenant-dropdown-item selected';
                newCheckboxItem.setAttribute('data-tenant-name', (tenant.full_name || `${tenant.first_name} ${tenant.last_name}`).toLowerCase());
                newCheckboxItem.innerHTML = `
                    <input type="checkbox"
                           class="tenant-checkbox"
                           name="tenant_ids[]"
                           value="${tenant.tenant_id}"
                           id="tenant_${tenant.tenant_id}"
                           checked>
                    <label for="tenant_${tenant.tenant_id}" class="tenant-label">
                        ${tenant.full_name || `${tenant.first_name} ${tenant.last_name}`}
                    </label>
                `;
                listContainer.appendChild(newCheckboxItem);

                // Add event listener to new checkbox
                newCheckboxItem.querySelector('.tenant-checkbox').addEventListener('change', function() {
                    const item = this.parentElement;
                    if (this.checked) {
                        item.classList.add('selected');
                    } else {
                        item.classList.remove('selected');
                    }
                    updateTenantDropdownText();
                    const removed = enforceTenantSelectionLimit();
                    if (currentStep === 3) {
                        updateSummary();
                    }
                });

                enforceTenantSelectionLimit();
                updateTenantDropdownText();
                if (currentStep === 3) {
                    updateSummary();
                }
            }
            if (tenantModal) tenantModal.hide();
        } catch (error) {
            console.error(error);
            tenantFormError.textContent = 'Something went wrong while saving.';
            tenantFormError.style.display = 'block';
        }
    });
});

function updateTenantDropdownText() {
    const selectedTenants = getSelectedTenantNames();
    const searchInput = document.getElementById('tenantSearchInput');

    if (selectedTenants.length === 0) {
        searchInput.placeholder = 'Search or select tenants...';
    } else {
        searchInput.placeholder = `${selectedTenants.length} tenant(s) selected - Search to add more`;
    }
}
</script>
@endsection


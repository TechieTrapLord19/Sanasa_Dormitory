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
</style>

<div class="booking-form-container">
    <h1 class="mb-4" style="color: #03255b; font-size: 2rem; font-weight: 700;">Create New Booking</h1>

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-title">Room & Dates</div>
        </div>
        <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-title">Tenant & Rate</div>
        </div>
        <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-title">Review & Confirm</div>
        </div>
    </div>

    <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST">
        @csrf

        <!-- Step 1: Room & Dates -->
        <div class="step-content active" data-step="1">
            <h3 class="mb-3" style="color: #2d3748;">Select Room & Dates</h3>
            
            <div class="row">
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Check-out Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control @error('checkout_date') is-invalid @enderror" 
                               name="checkout_date" 
                               id="checkout_date" 
                               value="{{ old('checkout_date') }}" 
                               required>
                        @error('checkout_date')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Available Rooms</label>
                <div id="roomsContainer" class="rooms-grid">
                    <p class="text-muted">Please select check-in and check-out dates to see available rooms.</p>
                </div>
                <input type="hidden" name="room_id" id="selected_room_id" value="{{ old('room_id') }}" required>
                @error('room_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Step 2: Tenant & Rate -->
        <div class="step-content" data-step="2">
            <h3 class="mb-3" style="color: #2d3748;">Select Tenant & Rate</h3>
            
            <div class="form-group">
                <label class="form-label">Tenant <span class="text-danger">*</span></label>
                <select class="form-select @error('tenant_id') is-invalid @enderror" 
                        name="tenant_id" 
                        id="tenant_id" 
                        required>
                    <option value="">Select a tenant...</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->tenant_id }}" {{ old('tenant_id') == $tenant->tenant_id ? 'selected' : '' }}>
                            {{ $tenant->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('tenant_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                <button type="button" class="add-tenant-btn" onclick="window.open('{{ route('tenants') }}', '_blank')">
                    + Add New Tenant
                </button>
            </div>

            <div class="form-group">
                <label class="form-label">Rate <span class="text-danger">*</span></label>
                <select class="form-select @error('rate_id') is-invalid @enderror" 
                        name="rate_id" 
                        id="rate_id" 
                        required>
                    <option value="">Select a rate...</option>
                    @foreach($rates as $rate)
                        <option value="{{ $rate->rate_id }}" 
                                data-duration="{{ $rate->duration_type }}" 
                                data-price="{{ $rate->base_price }}"
                                {{ old('rate_id') == $rate->rate_id ? 'selected' : '' }}>
                            {{ $rate->duration_type }} - ₱{{ number_format($rate->base_price, 2) }}
                            @if($rate->inclusion)
                                ({{ $rate->inclusion }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('rate_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
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
                    <span>Tenant:</span>
                    <span id="summary_tenant">-</span>
                </div>
                <div class="summary-row">
                    <span>Rate:</span>
                    <span id="summary_rate">-</span>
                </div>
                <div class="summary-row">
                    <span>Calculated Total:</span>
                    <span id="summary_total">₱0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Initial Payment Due:</span>
                    <span id="summary_initial">₱0.00</span>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn-secondary-custom" id="prevBtn" onclick="changeStep(-1)" style="display: none;">Previous</button>
            <div style="margin-left: auto;">
                <button type="button" class="btn-secondary-custom" id="nextBtn" onclick="changeStep(1)">Next</button>
                <button type="submit" class="btn-primary-custom" id="submitBtn" style="display: none;">Confirm Booking</button>
            </div>
        </div>
    </form>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;

function changeStep(direction) {
    // Validate current step before proceeding
    if (direction > 0 && !validateStep(currentStep)) {
        return;
    }

    // Hide current step
    document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');
    
    // Update step
    currentStep += direction;
    
    // Show new step
    document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.add('active');
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
    
    // Mark previous steps as completed
    for (let i = 1; i < currentStep; i++) {
        document.querySelector(`.step[data-step="${i}"]`).classList.add('completed');
    }
    
    // Update buttons
    document.getElementById('prevBtn').style.display = currentStep > 1 ? 'inline-block' : 'none';
    document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'inline-block' : 'none';
    document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'inline-block' : 'none';
    
    // Update summary if on step 3
    if (currentStep === 3) {
        updateSummary();
    }
}

function validateStep(step) {
    if (step === 1) {
        const checkin = document.getElementById('checkin_date').value;
        const checkout = document.getElementById('checkout_date').value;
        const roomId = document.getElementById('selected_room_id').value;
        
        if (!checkin || !checkout) {
            alert('Please select both check-in and check-out dates.');
            return false;
        }
        
        if (new Date(checkout) <= new Date(checkin)) {
            alert('Check-out date must be after check-in date.');
            return false;
        }
        
        if (!roomId) {
            alert('Please select a room.');
            return false;
        }
        
        return true;
    } else if (step === 2) {
        const tenantId = document.getElementById('tenant_id').value;
        const rateId = document.getElementById('rate_id').value;
        
        if (!tenantId) {
            alert('Please select a tenant.');
            return false;
        }
        
        if (!rateId) {
            alert('Please select a rate.');
            return false;
        }
        
        return true;
    }
    
    return true;
}

function updateSummary() {
    const roomSelect = document.querySelector('.room-card.selected');
    const checkin = document.getElementById('checkin_date').value;
    const checkout = document.getElementById('checkout_date').value;
    const tenantSelect = document.getElementById('tenant_id');
    const rateSelect = document.getElementById('rate_id');
    
    document.getElementById('summary_room').textContent = roomSelect ? roomSelect.querySelector('.room-number').textContent : '-';
    document.getElementById('summary_checkin').textContent = checkin ? new Date(checkin).toLocaleDateString() : '-';
    document.getElementById('summary_checkout').textContent = checkout ? new Date(checkout).toLocaleDateString() : '-';
    document.getElementById('summary_tenant').textContent = tenantSelect.options[tenantSelect.selectedIndex].text || '-';
    document.getElementById('summary_rate').textContent = rateSelect.options[rateSelect.selectedIndex].text || '-';
    
    // Calculate totals
    if (checkin && checkout && rateSelect.value) {
        const rateOption = rateSelect.options[rateSelect.selectedIndex];
        const price = parseFloat(rateOption.dataset.price);
        const duration = rateOption.dataset.duration;
        
        const checkinDate = new Date(checkin);
        const checkoutDate = new Date(checkout);
        const days = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));
        
        let total = 0;
        if (duration === 'Daily') {
            total = price * days;
        } else if (duration === 'Weekly') {
            total = price * Math.ceil(days / 7);
        } else if (duration === 'Monthly') {
            const months = Math.max(1, Math.ceil(days / 30));
            total = price * months;
        }
        
        document.getElementById('summary_total').textContent = '₱' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Initial payment (deposit for monthly, full for others)
        const initialPayment = duration === 'Monthly' ? price : total;
        document.getElementById('summary_initial').textContent = '₱' + initialPayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
}

// Check availability when dates change
document.getElementById('checkin_date').addEventListener('change', checkAvailability);
document.getElementById('checkout_date').addEventListener('change', checkAvailability);

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
    
    // Show loading
    document.getElementById('roomsContainer').innerHTML = '<p class="text-muted">Loading available rooms...</p>';
    
    // Fetch available rooms
    fetch(`{{ route('bookings.check-availability') }}?checkin_date=${checkin}&checkout_date=${checkout}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('roomsContainer');
            if (data.available_rooms && data.available_rooms.length > 0) {
                container.innerHTML = data.available_rooms.map(room => `
                    <div class="room-card" data-room-id="${room.room_id}" onclick="selectRoom(${room.room_id})">
                        <div class="room-number">${room.room_num}</div>
                        <div class="room-floor">Floor ${room.floor}</div>
                    </div>
                `).join('');
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
    // Remove previous selection
    document.querySelectorAll('.room-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Select new room
    const card = document.querySelector(`.room-card[data-room-id="${roomId}"]`);
    if (card && !card.classList.contains('unavailable')) {
        card.classList.add('selected');
        document.getElementById('selected_room_id').value = roomId;
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // If dates are pre-filled, check availability
    if (document.getElementById('checkin_date').value && document.getElementById('checkout_date').value) {
        checkAvailability();
    }
});
</script>
@endsection


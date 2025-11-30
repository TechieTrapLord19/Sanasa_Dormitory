@extends('layouts.app')

@section('title', 'Electric Readings')

@section('content')
<style>
    .readings-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .readings-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    /* Filter Styles */
    .readings-filters {
        background-color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        margin: 0;
        white-space: nowrap;
    }

    .filter-btn {
        border: 1px solid #cbd5e1;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
        background-color: white;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .filter-btn:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    .filter-btn.active {
        background: #03255b;
        color: white;
        border-color: #03255b;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    /* Table Styles */
    .readings-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .readings-table {
        width: 100%;
        border-collapse: collapse;
    }

    .readings-table thead {
        background-color: #f7fafc;
    }

    .readings-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .readings-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .readings-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .readings-table tbody tr:last-child td {
        border-bottom: none;
    }

    .reading-row.hidden {
        display: none;
    }

    .room-number {
        font-weight: 600;
        color: #0f172a;
    }

    .room-number.clickable {
        color: #03255b;
        cursor: pointer;
        text-decoration: underline;
        transition: color 0.2s ease;
    }

    .room-number.clickable:hover {
        color: #021d47;
    }

    .last-reading {
        color: #64748b;
        font-size: 0.85rem;
    }

    .last-reading-value {
        font-weight: 600;
        color: #1f2937;
    }

    .reading-input {
        width: 140px;
        padding: 0.5rem;
        border: 1px solid #d0d7e2;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .reading-input:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .date-input {
        width: 150px;
        padding: 0.5rem;
        border: 1px solid #d0d7e2;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .date-input:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .btn-save-row {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        background-color: #03255b;
        color: white;
        transition: background-color 0.2s ease;
    }

    .btn-save-row:hover {
        background-color: #021d47;
    }

    .btn-save-all {
        padding: 0.75rem 1.5rem;
        border: none;
        background-color: #03255b;
        color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-save-all:hover {
        background-color: #021d47;
    }

    .btn-save-all[style*="background-color: #8b5cf6"]:hover {
        background-color: #7c3aed !important;
    }

    .save-all-section {
        padding: 1rem 1.5rem;
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .no-reading {
        color: #94a3b8;
        font-style: italic;
    }
    .reading-input.invalid {
        border-color: #dc2626;
        background-color: #fef2f2;
    }
    .reading-error {
        color: #dc2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    @media (max-width: 768px) {
        .readings-table {
            font-size: 0.8rem;
        }
        .readings-table th,
        .readings-table td {
            padding: 0.75rem 0.5rem;
        }
        .reading-input {
            width: 100px;
        }
        .date-input {
            width: 120px;
        }
    }
</style>

<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following errors:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="readings-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8 d-flex justify-content-start">
                <h1 class="readings-title">Electric Meter Readings</h1>
            </div>
        </div>
    </div>

    <!-- Electricity Rate and Filters Card -->
    <div class="readings-filters">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0;">
            <div>
                <strong>Electricity Rate per kWh</strong>
                <p class="text-muted mb-0 small">Set the price per kWh for electricity billing.</p>
            </div>
            <form method="POST" action="{{ route('electric-readings.rate') }}" id="rateForm" style="display:flex;align-items:center;gap:0.5rem;">
                @csrf
                <label for="kwh_price" style="margin:0;font-weight:600;color:#2d3748;">Price/ kWh (₱):</label>
                <input id="kwh_price" type="number" name="electricity_rate_per_kwh" step="0.01" min="0" placeholder="Enter price per kWh" value="{{ $electricityRate ?? '' }}" style="width:120px;padding:0.4rem;border:1px solid #d0d7e2;border-radius:6px;" required>
                <button type="submit" class="btn-save-all" style="background-color: #03255b;">
                    <i class="bi bi-save"></i> Save Rate
                </button>
            </form>
        </div>
        <div class="filter-group">
            <label class="filter-label">Filter by Floor:</label>
            <button type="button" class="filter-btn active" data-filter="floor" data-value="all">All</button>
            @foreach($floors as $floor)
                <button type="button" class="filter-btn" data-filter="floor" data-value="{{ $floor }}">Floor {{ $floor }}</button>
            @endforeach
        </div>
    </div>

    <!-- Readings Table -->
    <div class="readings-table-container">
        <form method="POST" action="{{ route('electric-readings.store') }}" id="readingsForm">
            @csrf
            <table class="readings-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Last Reading</th>
                        <th>New Meter Reading (kWh)</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roomsWithReadings as $item)
                        @php
                            $room = $item['room'];
                            $latestReading = $item['latestReading'];
                        @endphp
                        <tr class="reading-row" data-floor="{{ $room->floor }}">
                            <td>
                                @if($room->activeBooking)
                                    <a href="{{ route('bookings.show', $room->activeBooking->booking_id) }}"
                                       class="room-number clickable"
                                       title="View booking details">
                                        Room {{ $room->room_num }}
                                    </a>
                                @else
                                    <span class="room-number">Room {{ $room->room_num }}</span>
                                @endif
                            </td>
                            <td>
                                @if($latestReading)
                                    <span class="last-reading">
                                        <span class="last-reading-value">{{ number_format($latestReading->meter_value_kwh, 2) }} kWh</span>
                                        <br>
                                        <small>on {{ $latestReading->reading_date->format('M d, Y') }}</small>
                                    </span>
                                @else
                                    <span class="no-reading">No previous reading</span>
                                @endif
                            </td>
                            <td>
                                <input type="number"
                                       class="reading-input meter-input"
                                       name="readings[{{ $room->room_id }}][meter_value_kwh]"
                                       data-last-reading="{{ $latestReading ? $latestReading->meter_value_kwh : 0 }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="e.g. 1300.00">
                                <input type="hidden" name="readings[{{ $room->room_id }}][room_id]" value="{{ $room->room_id }}">
                                <div class="reading-error" style="display: none; color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;"></div>
                                <div class="reading-preview" style="font-size:0.85rem;color:#475569;margin-top:0.25rem;display:none;">
                                    Usage: <span class="preview-usage">0.00</span> kWh — Cost: ₱<span class="preview-cost">0.00</span>
                                </div>
                            </td>
                            <td>
                                <input type="date"
                                       class="date-input"
                                       name="readings[{{ $room->room_id }}][reading_date]"
                                       value="{{ date('Y-m-d') }}"
                                       required>
                            </td>
                            <td>
                                <button type="button" class="btn-save-row" onclick="saveRow(this)">
                                    Save
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="save-all-section">
                <div>
                    <strong>Save all readings at once</strong>
                    <p class="text-muted mb-0 small">Only rooms with readings entered will be saved.</p>
                </div>
                <button type="submit" class="btn-save-all">
                    <i class="bi bi-save"></i> Save All Readings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Handle rate form submission separately
document.addEventListener('DOMContentLoaded', function() {
    const rateForm = document.getElementById('rateForm');
    if (rateForm) {
        rateForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (data.success) {
                    // Show success message and reload
                    showToast(data.message || 'Electricity rate saved successfully!', 'success');
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to save electricity rate');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error: ' + (error.message || 'Failed to save electricity rate. Please try again.'), 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }

    const kwhPriceInput = document.getElementById('kwh_price');

    // Update previews when rate changes
    if (kwhPriceInput) {
        kwhPriceInput.addEventListener('input', function() {
            // Update all previews when rate changes
            document.querySelectorAll('.meter-input').forEach(input => {
                updatePreview(input);
            });
        });

        kwhPriceInput.addEventListener('change', function() {
            // Update all previews when rate changes
            document.querySelectorAll('.meter-input').forEach(input => {
                updatePreview(input);
            });
        });
    }

    // Set default date to today for all date inputs
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('.date-input').forEach(input => {
        if (!input.value) {
            input.value = today;
        }
    });

    // Add "Select All" date functionality
    const firstDateInput = document.querySelector('.date-input');
    if (firstDateInput) {
        firstDateInput.addEventListener('change', function() {
            const selectedDate = this.value;
            document.querySelectorAll('.date-input').forEach(input => {
                input.value = selectedDate;
            });
        });
    }

    // Validate meter inputs - input is new cumulative meter value; compute usage and preview cost
    document.querySelectorAll('.meter-input').forEach(input => {
        input.addEventListener('blur', function() {
            validateMeter(this);
        });
        input.addEventListener('input', function() {
            // Clear error on input
            const errorDiv = this.parentElement.querySelector('.reading-error');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            this.classList.remove('invalid');
            // Update preview
            updatePreview(this);
        });
        // Initialize preview on load
        updatePreview(input);
    });

    function validateMeter(input) {
        const meter = parseFloat(input.value);
        const lastReading = parseFloat(input.getAttribute('data-last-reading')) || 0;
        const errorDiv = input.parentElement.querySelector('.reading-error');

        if (!input.value) {
            // empty is allowed; treated as not-entered
            return;
        }

        if (isNaN(meter) || meter < 0) {
            input.classList.add('invalid');
            if (errorDiv) {
                errorDiv.textContent = `Please enter a valid meter reading.`;
                errorDiv.style.display = 'block';
            }
            return;
        }

        const usage = meter - lastReading;

        if (usage < 0) {
            // new reading lower than last — possible meter reset; warn but allow
            input.classList.add('invalid');
            if (errorDiv) {
                errorDiv.textContent = `New reading is less than last reading. Please confirm.`;
                errorDiv.style.display = 'block';
            }
        } else if (usage > 10000) {
            input.classList.add('invalid');
            if (errorDiv) {
                errorDiv.textContent = `Warning: Usage (${usage.toFixed(2)} kWh) is unusually large.`;
                errorDiv.style.display = 'block';
            }
        } else {
            input.classList.remove('invalid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
        }
        updatePreview(input);
    }

    function updatePreview(input) {
        const meter = parseFloat(input.value);
        const lastReading = parseFloat(input.getAttribute('data-last-reading')) || 0;
        const preview = input.parentElement.querySelector('.reading-preview');
        const usageSpan = input.parentElement.querySelector('.preview-usage');
        const costSpan = input.parentElement.querySelector('.preview-cost');
        const kwhPriceInput = document.getElementById('kwh_price');
        const defaultPrice = kwhPriceInput && kwhPriceInput.value ? parseFloat(kwhPriceInput.value) : 0;

        if (!preview) return;

        if (isNaN(meter) || meter === null || input.value === '') {
            preview.style.display = 'none';
            return;
        }

        if (defaultPrice === 0) {
            preview.style.display = 'none';
            return;
        }

        const usage = Math.max(0, meter - lastReading);
        const cost = usage * defaultPrice;

        usageSpan.textContent = usage.toFixed(2);
        costSpan.textContent = cost.toFixed(2);
        preview.style.display = 'block';
    }

    // Floor filter functionality
    const floorButtons = document.querySelectorAll('[data-filter="floor"]');
    const readingRows = document.querySelectorAll('.reading-row');
    let currentFloor = 'all';

    function filterReadings() {
        readingRows.forEach(row => {
            const rowFloor = row.getAttribute('data-floor');
            const floorMatch = currentFloor === 'all' || rowFloor == currentFloor;

            if (floorMatch) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }

    // Floor filter buttons
    floorButtons.forEach(button => {
        button.addEventListener('click', function() {
            floorButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentFloor = this.getAttribute('data-value');
            filterReadings();
        });
    });
});

function saveRow(button) {
    const row = button.closest('tr');
    const roomId = row.querySelector('input[type="hidden"]').value;
    const meterInput = row.querySelector('input[name*="[meter_value_kwh]"]');
    const meterValue = meterInput.value;
    const readingDate = row.querySelector('input[name*="[reading_date]"]').value;
    const lastReading = parseFloat(meterInput.getAttribute('data-last-reading')) || 0;

    if (!meterValue) {
        showToast('Please enter the new meter reading.', 'warning');
        return;
    }

    if (!readingDate) {
        showToast('Please select a date.', 'warning');
        return;
    }
    const newMeterValue = parseFloat(meterValue);
    if (isNaN(newMeterValue) || newMeterValue < 0) {
        showToast('Please enter a valid meter reading.', 'warning');
        return;
    }

    // Function to actually submit the reading
    const doSave = () => {
        // Create form data for single row
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('room_id', roomId);
        formData.append('reading_date', readingDate);
        formData.append('meter_value_kwh', newMeterValue);

        // Disable button during submission
        button.disabled = true;
        button.textContent = 'Saving...';

        fetch('{{ route("electric-readings.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to show updated readings
                window.location.reload();
            } else {
                showToast('Error: ' + (data.message || 'Failed to save reading'), 'error');
                button.disabled = false;
                button.textContent = 'Save';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while saving. Please try again.', 'error');
            button.disabled = false;
            button.textContent = 'Save';
        });
    };

    const usage = newMeterValue - lastReading;
    if (usage < 0) {
        confirmAction('New reading is less than the last recorded reading. This may indicate a meter reset. Proceed anyway?', doSave, {
            title: 'Confirm Reading',
            confirmText: 'Yes, Save',
            type: 'warning'
        });
    } else {
        doSave();
    }
}

// Handle form submission for bulk save
document.getElementById('readingsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const readings = [];
    let hasReadings = false;

    // Collect all readings and validate
    const invalidReadings = [];
    document.querySelectorAll('input[name*="[meter_value_kwh]"]').forEach(input => {
        const name = input.name;
        const match = name.match(/readings\[(\d+)\]/);
        if (match) {
            const roomId = match[1];
            const meterValueRaw = input.value;
            const dateInput = document.querySelector(`input[name="readings[${roomId}][reading_date]"]`);
            const lastReading = parseFloat(input.getAttribute('data-last-reading')) || 0;
            const meterValue = parseFloat(meterValueRaw);

            if (meterValueRaw && !isNaN(meterValue)) {
                const usageCalc = meterValue - lastReading;

                if (usageCalc < 0) {
                    const roomNum = input.closest('tr').querySelector('.room-number').textContent.trim();
                    invalidReadings.push({ room: roomNum, lastReading: lastReading, newReading: meterValue });
                }

                readings.push({
                    room_id: roomId,
                    reading_date: dateInput.value,
                    meter_value_kwh: meterValue
                });
                hasReadings = true;
            }
        }
    });

    // Check if we have at least one reading or a rate to save
    const kwhPriceInput = document.getElementById('kwh_price');
    const hasRate = kwhPriceInput && kwhPriceInput.value && parseFloat(kwhPriceInput.value) > 0;

    if (!hasReadings && !hasRate) {
        showToast('Please enter at least one reading or set a price/kWh rate before saving.', 'warning');
        return;
    }

    // Function to actually submit the readings
    const submitReadings = () => {
        // Prepare form data
        const submitData = new FormData();
        submitData.append('_token', formData.get('_token'));

        // Add electricity rate
        if (kwhPriceInput && kwhPriceInput.value) {
            submitData.append('electricity_rate_per_kwh', kwhPriceInput.value);
        }

        // Add readings array in the correct format (send cumulative meter values)
        if (readings.length > 0) {
            readings.forEach((reading, index) => {
                submitData.append(`readings[${index}][room_id]`, reading.room_id);
                submitData.append(`readings[${index}][reading_date]`, reading.reading_date);
                submitData.append(`readings[${index}][meter_value_kwh]`, reading.meter_value_kwh);
            });
        }

        const submitButton = document.querySelector('.btn-save-all');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

        fetch(document.getElementById('readingsForm').action, {
            method: 'POST',
            body: submitData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            // Check if response is successful (200-299)
            if (response.ok) {
                // Try to parse as JSON
                try {
                    const data = await response.json();
                    if (data.success) {
                        // Success - reload page
                        window.location.reload();
                        return;
                    } else {
                        throw new Error(data.message || 'Failed to save readings');
                    }
                } catch (e) {
                    // If JSON parsing fails but status is ok, assume success and reload
                    window.location.reload();
                    return;
                }
            }

            // If not ok, try to get error message
            try {
                const data = await response.json();
                throw new Error(data.message || 'Failed to save readings');
            } catch (e) {
                if (e.message) {
                    throw e;
                }
                throw new Error('Failed to save readings. Status: ' + response.status);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error: ' + (error.message || 'An error occurred while saving. Please try again.'), 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-save"></i> Save All Readings';
        });
    };

    // Warn about readings that are lower than last reading
    if (invalidReadings.length > 0) {
        let warningMessage = 'The following rooms have readings lower than their last reading:<br><br>';
        invalidReadings.forEach(item => {
            warningMessage += `<strong>${item.room}</strong>: ${item.newReading.toFixed(2)} kWh (last: ${item.lastReading.toFixed(2)} kWh)<br>`;
        });
        warningMessage += '<br>This may indicate meter resets or data entry errors.';

        confirmAction(warningMessage, submitReadings, {
            title: 'Confirm Meter Readings',
            confirmText: 'Yes, Save Anyway',
            type: 'warning'
        });
    } else {
        submitReadings();
    }
});
</script>
@endsection


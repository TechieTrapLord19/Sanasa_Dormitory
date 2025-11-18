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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        background-color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
    }

    .filter-btn.active {
        background-color: #03255b;
        color: white;
        border-color: #03255b;
    }

    /* Table Styles */
    .readings-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
        background-color: #f7fafc;
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

    .last-reading {
        color: #64748b;
        font-size: 0.85rem;
    }

    .last-reading-value {
        font-weight: 600;
        color: #1f2937;
    }

    .reading-input {
        width: 120px;
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
        background-color: #10b981;
        color: white;
        transition: background-color 0.2s ease;
    }

    .btn-save-row:hover {
        background-color: #059669;
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
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    <div class="readings-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="readings-title">Electric Meter Readings</h1>
    </div>

    <!-- Filters -->
    <div class="readings-filters">
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
                        <th>New Reading (kWh)</th>
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
                                <span class="room-number">Room {{ $room->room_num }}</span>
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
                                       class="reading-input"
                                       name="readings[{{ $room->room_id }}][meter_value_kwh]"
                                       data-last-reading="{{ $latestReading ? $latestReading->meter_value_kwh : 0 }}"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                                <input type="hidden" name="readings[{{ $room->room_id }}][room_id]" value="{{ $room->room_id }}">
                                <div class="reading-error" style="display: none; color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;"></div>
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
document.addEventListener('DOMContentLoaded', function() {
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

    // Validate reading inputs - check if new reading is less than last reading
    document.querySelectorAll('.reading-input').forEach(input => {
        input.addEventListener('blur', function() {
            validateReading(this);
        });
        input.addEventListener('input', function() {
            // Clear error on input
            const errorDiv = this.parentElement.querySelector('.reading-error');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
            this.classList.remove('invalid');
        });
    });

    function validateReading(input) {
        const newReading = parseFloat(input.value);
        const lastReading = parseFloat(input.getAttribute('data-last-reading')) || 0;
        const errorDiv = input.parentElement.querySelector('.reading-error');

        if (!input.value || newReading <= 0) {
            return; // Empty or invalid, will be caught by required validation
        }

        if (lastReading > 0 && newReading < lastReading) {
            input.classList.add('invalid');
            if (errorDiv) {
                errorDiv.textContent = `Warning: New reading (${newReading.toFixed(2)}) is lower than last reading (${lastReading.toFixed(2)}). This may indicate a meter reset or error.`;
                errorDiv.style.display = 'block';
            }
        } else {
            input.classList.remove('invalid');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
        }
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

    if (!meterValue || meterValue <= 0) {
        alert('Please enter a valid meter reading.');
        return;
    }

    if (!readingDate) {
        alert('Please select a date.');
        return;
    }

    // Validate that new reading is not lower than last reading
    const newReading = parseFloat(meterValue);
    if (lastReading > 0 && newReading < lastReading) {
        const confirmMessage = `Warning: The new reading (${newReading.toFixed(2)} kWh) is lower than the last reading (${lastReading.toFixed(2)} kWh).\n\nThis may indicate:\n- A meter reset/replacement\n- An error in data entry\n\nDo you want to proceed anyway?`;
        if (!confirm(confirmMessage)) {
            return;
        }
    }

    // Create form data for single row
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('room_id', roomId);
    formData.append('reading_date', readingDate);
    formData.append('meter_value_kwh', meterValue);

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
            alert('Error: ' + (data.message || 'Failed to save reading'));
            button.disabled = false;
            button.textContent = 'Save';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving. Please try again.');
        button.disabled = false;
        button.textContent = 'Save';
    });
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
            const meterValue = input.value;
            const dateInput = document.querySelector(`input[name="readings[${roomId}][reading_date]"]`);
            const lastReading = parseFloat(input.getAttribute('data-last-reading')) || 0;
            const newReading = parseFloat(meterValue);

            if (meterValue && meterValue > 0) {
                // Check if reading is lower than last reading
                if (lastReading > 0 && newReading < lastReading) {
                    const roomNum = input.closest('tr').querySelector('.room-number').textContent.trim();
                    invalidReadings.push({
                        room: roomNum,
                        lastReading: lastReading,
                        newReading: newReading
                    });
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

    if (!hasReadings) {
        alert('Please enter at least one reading before saving.');
        return;
    }

    // Warn about readings that are lower than last reading
    if (invalidReadings.length > 0) {
        let warningMessage = 'Warning: The following rooms have readings lower than their last reading:\n\n';
        invalidReadings.forEach(item => {
            warningMessage += `${item.room}: ${item.newReading.toFixed(2)} kWh (last: ${item.lastReading.toFixed(2)} kWh)\n`;
        });
        warningMessage += '\nThis may indicate meter resets or data entry errors.\n\nDo you want to proceed anyway?';

        if (!confirm(warningMessage)) {
            return;
        }
    }

    // Prepare form data
    const submitData = new FormData();
    submitData.append('_token', formData.get('_token'));

    // Add readings array in the correct format
    readings.forEach((reading, index) => {
        submitData.append(`readings[${index}][room_id]`, reading.room_id);
        submitData.append(`readings[${index}][reading_date]`, reading.reading_date);
        submitData.append(`readings[${index}][meter_value_kwh]`, reading.meter_value_kwh);
    });

    const submitButton = this.querySelector('.btn-save-all');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    fetch(this.action, {
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
        alert('Error: ' + (error.message || 'An error occurred while saving. Please try again.'));
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="bi bi-save"></i> Save All Readings';
    });
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Rates Management')

@section('content')
<style>
    .rates-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .rates-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .create-rate-btn {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.3s ease;
    }
    .create-rate-btn:hover {
        background-color: #021d47;
        color: white;
    }
    .create-rate-btn-icon {
        width: 24px;
        height: 24px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    /* Filter Styles */
    .rates-filters {
        background-color: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        margin: 0;
    }

    .filter-input {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
        min-width: 200px;
    }

    .filter-input:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .filter-select {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
        cursor: pointer;
        min-width: 150px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    /* Table Styles */
    .rates-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .rates-table {
        width: 100%;
        border-collapse: collapse;
    }

    .rates-table thead {
        background-color: #f7fafc;
    }

    .rates-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .rates-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .rates-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .rates-table tbody tr:last-child td {
        border-bottom: none;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-edit, .btn-delete {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-delete {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background-color: #fecaca;
    }

    .room-rates-header {
        background-color: #f7fafc;
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: #2d3748;
        border-bottom: 2px solid #e2e8f0;
    }

    .room-rates-title {
        font-size: 1rem;
        margin: 0;
    }

    .price-display {
        font-weight: 600;
        color: #03255b;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
    }
</style>

<div class="rates-header">
    <div class="row align-items-center">
        <!-- Left: Title -->
        <div class="col-md-8 d-flex justify-content-start">
            <h1 class="rates-title">Rates Management</h1>
        </div>

        <!-- Right: Create Button -->
        <div class="col-md-4 d-flex justify-content-end">
            <button class="create-rate-btn" data-bs-toggle="modal" data-bs-target="#createRateModal">
                <span class="create-rate-btn-icon">+</span>
                <span>Create New Rate</span>
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="rates-filters">
    <div class="filter-group">
        <p class="filter-label mb-0">Room or Rate Name:</p>
        <input type="text" class="filter-input" id="rateNameFilter" placeholder="Search by room or rate name...">
    </div>
    <div class="filter-group mt-3">
        <p class="filter-label mb-0">Filter by Floor:</p>
        <select class="filter-select" id="floorFilter">
            <option value="all">All Floors</option>
            @foreach($floors as $floor)
                <option value="{{ $floor }}">Floor {{ $floor }}</option>
            @endforeach
        </select>
    </div>
</div>

<!-- General Rates Section -->
<div class="section-title">General Rates</div>
<div class="rates-table-container">
    <table class="rates-table">
        <thead>
            <tr>
                <th>Rate Name</th>
                <th>Duration</th>
                <th>Base Price</th>
                <th>Inclusions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="generalRatesTable">
            @forelse($rates as $rate)
                <tr>
                    <td>{{ $rate->duration_type }} Rate</td>
                    <td>Per {{ $rate->duration_type === 'Daily' ? 'Day' : ($rate->duration_type === 'Weekly' ? 'Week' : 'Month') }}</td>
                    <td class="price-display">₱{{ number_format($rate->base_price, 2) }}</td>
                    <td>{{ $rate->inclusion }}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" onclick="editRate({{ $rate->rate_id }})">Edit</button>
                            <button class="btn-delete" onclick="deleteRate({{ $rate->rate_id }})">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No rates found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Active Room Rates Section -->
<div class="section-title">Active Room Rates</div>
@forelse($occupiedRooms as $room)
    <div class="rates-table-container">
        <div class="room-rates-header">
            <h5 class="room-rates-title mb-0">Room {{ $room->room_num }}</h5>
        </div>
        <table class="rates-table">
            <thead>
                <tr>
                    <th>Rate Name</th>
                    <th>Duration</th>
                    <th>Base Price</th>
                    <th>Inclusions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Show all available rates for this occupied room
                    // In the future, when bookings are fully implemented, 
                    // this will show only the rate assigned to the booking
                    $roomRates = $rates;
                @endphp
                @forelse($roomRates as $rate)
                    <tr>
                        <td>{{ $rate->duration_type }} Rate</td>
                        <td>Per {{ $rate->duration_type === 'Daily' ? 'Day' : ($rate->duration_type === 'Weekly' ? 'Week' : 'Month') }}</td>
                        <td class="price-display">₱{{ number_format($rate->base_price, 2) }}</td>
                        <td>{{ $rate->inclusion }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-edit" onclick="editRoomRate({{ $room->room_id }}, {{ $rate->rate_id }})">Edit</button>
                                <button class="btn-delete" onclick="deleteRoomRate({{ $room->room_id }}, {{ $rate->rate_id }})">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No rates assigned to this room</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@empty
    <div class="rates-table-container">
        <div class="text-center text-muted py-4">No occupied rooms at the moment. Active room rates will appear here when rooms are booked.</div>
    </div>
@endforelse

<!-- Create Rate Modal -->
<div class="modal fade" id="createRateModal" tabindex="-1" aria-labelledby="createRateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRateModalLabel">Create New Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('rates.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="duration_type" class="form-label">Duration Type</label>
                        <select class="form-select @error('duration_type') is-invalid @enderror"
                                id="duration_type" name="duration_type" required>
                            <option value="">Select duration type...</option>
                            <option value="Daily">Daily</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Monthly">Monthly</option>
                        </select>
                        @error('duration_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="base_price" class="form-label">Base Price</label>
                        <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror"
                               id="base_price" name="base_price" required>
                        @error('base_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="inclusion" class="form-label">Inclusions</label>
                        <textarea class="form-control @error('inclusion') is-invalid @enderror"
                                  id="inclusion" name="inclusion" rows="3" required></textarea>
                        @error('inclusion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rateNameFilter = document.getElementById('rateNameFilter');
    const floorFilter = document.getElementById('floorFilter');
    const generalRatesTable = document.getElementById('generalRatesTable');
    const roomTables = document.querySelectorAll('.rates-table-container');

    function filterRates() {
        const rateName = rateNameFilter.value.toLowerCase();
        const floor = floorFilter.value;

        // Filter general rates
        const generalRows = generalRatesTable.querySelectorAll('tr');
        generalRows.forEach(row => {
            if (row.querySelector('td')) {
                const rateNameText = row.querySelector('td:first-child').textContent.toLowerCase();
                const matchesName = !rateName || rateNameText.includes(rateName);

                if (matchesName) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });

        // Filter active room rates by room number, rate name, and floor
        roomTables.forEach(table => {
            const roomHeader = table.querySelector('.room-rates-header');
            if (roomHeader) {
                // Check floor filter first
                const roomNum = roomHeader.textContent.match(/Room (\d+)/);
                let matchesFloor = true;
                if (roomNum) {
                    const roomFloor = Math.floor(parseInt(roomNum[1]) / 100);
                    matchesFloor = floor === 'all' || roomFloor.toString() === floor;
                }

                // Check rate name filter
                const roomText = roomHeader.textContent.toLowerCase();
                const matchesRoom = !rateName || roomText.includes(rateName);

                const rows = table.querySelectorAll('tbody tr');
                let hasVisibleRows = false;

                rows.forEach(row => {
                    if (row.querySelector('td')) {
                        const rateNameText = row.querySelector('td:first-child').textContent.toLowerCase();
                        const matchesRate = !rateName || rateNameText.includes(rateName);

                        if (matchesRoom || matchesRate) {
                            row.style.display = '';
                            hasVisibleRows = true;
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });

                // Show/hide the entire table container based on both filters
                table.style.display = (matchesFloor && hasVisibleRows) ? '' : 'none';
            }
        });
    }

    rateNameFilter.addEventListener('input', filterRates);
    floorFilter.addEventListener('change', filterRates);
});

function editRate(rateId) {
    // TODO: Implement edit functionality
    alert('Edit rate ' + rateId);
}

function deleteRate(rateId) {
    if (confirm('Are you sure you want to delete this rate?')) {
        // TODO: Implement delete functionality
        alert('Delete rate ' + rateId);
    }
}

function editRoomRate(roomId, rateId) {
    // TODO: Implement edit room rate functionality
    alert('Edit room ' + roomId + ' rate ' + rateId);
}

function deleteRoomRate(roomId, rateId) {
    if (confirm('Are you sure you want to delete this room rate?')) {
        // TODO: Implement delete room rate functionality
        alert('Delete room ' + roomId + ' rate ' + rateId);
    }
}
</script>
@endsection

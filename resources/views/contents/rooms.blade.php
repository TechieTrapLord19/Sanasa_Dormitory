@extends('layouts.app')

@section('title', 'Rooms')

@section('content')

<style>
    .room-header {
        background-color: white;
    }
    .room-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .room-status-container {
        display: flex;
        flex-direction: column;
        align-items: end;
    }
    .room-status-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.80rem;
    }
    /* Status Dots */
    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-dot.available {
        background-color: #10b981;
    }

    .status-dot.occupied {
        background-color: #ef4444;
    }

    .status-dot.maintenance {
        background-color: #f59e0b;
    }

    .status-dot.pending {
        background-color: #fbbf24;
}

    .modal-footer .btn-primary:hover {
        background-color: #021d47 !important;
        border-color: #021d47 !important;
    }

    .create-room-btn {
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
    .create-room-btn:hover {
        background-color: #021d47;
        color: white;
    }
    .create-room-btn-icon {
        width: 24px;
        height: 24px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    /* Room Card Styles */
    .room-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .room-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        background: white;
        height: 100%;
        cursor: pointer;
    }

    .room-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .room-card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .room-card-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 1rem;
    }

    .room-status-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .room-status-badge.available {
        background-color: #d1fae5;
        color: #065f46;
    }

    .room-status-badge.occupied {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .room-status-badge.maintenance {
        background-color: #fef3c7;
        color: #92400e;
    }
    .room-status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .room-info-item {
        font-size: 0.875rem;
        color: #4a5568;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .room-info-item:last-of-type {
        margin-bottom: 0;
    }

    .room-info-label {
        font-weight: 600;
        color: #2d3748;
    }

    .room-card-actions {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .room-card:hover .room-card-actions {
        opacity: 1;
    }

    .room-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background-color: #f7fafc;
        color: #4a5568;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .room-action-btn:hover {
        background-color: #edf2f7;
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .room-card-content {
        flex-grow: 1;
    }

    /* Filter Styles */
    .room-filters {
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
        min-width: 130px;
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

    .room-item.hidden {
        display: none;
    }
    .tenant-names {
    font-size: 0.85rem;
    line-height: 1.4;
    }
</style>

<div class="room-header">
    <div class="row align-items-center">
        <!-- Left: Title -->
        <div class="col-md-8 d-flex justify-content-start">
            <h1 class="room-title">Room Management</h1>
        </div>

        <!-- Right: Create Button (aligned to the end) - Only for owners -->
        @if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
        <div class="col-md-4 d-flex justify-content-end">
            <button class="create-room-btn" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                <i class="bi bi-plus-circle"></i>
                <span>Create New Room</span>
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="room-filters mt-4">
    <div class="row align-items-center">
        <!-- Left: Filters (two groups stacked) -->
        <div class="col-md-8">
            <div class="filter-group">
                <p class="filter-label mb-0">Filter by Floor:</p>
                <button class="filter-btn active" data-filter="floor" data-value="all">All</button>
                @foreach($floors as $floor)
                    <button class="filter-btn" data-filter="floor" data-value="{{ $floor }}">Floor {{ $floor }}</button>
                @endforeach
            </div>
            <div class="filter-group mt-3">
                <p class="filter-label mb-0">Filter by Status:</p>
                <button class="filter-btn active" data-filter="status" data-value="all">All</button>
                <button class="filter-btn" data-filter="status" data-value="available">Available</button>
                <button class="filter-btn" data-filter="status" data-value="pending">Pending</button>
                <button class="filter-btn" data-filter="status" data-value="occupied">Occupied</button>
                <button class="filter-btn" data-filter="status" data-value="maintenance">Maintenance</button>
            </div>
        </div>

        <!-- Right: Room counts aligned to end -->
        <div class="col-md-4 d-flex justify-content-end">
            <div class="room-status-container">
                <div class="room-status-item">
                    <span class="status-dot available"></span>
                    <span>Available <strong>{{ $roomCounts['available'] ?? 0 }}/{{ $totalRooms ?? 0 }}</strong></span>
                </div>
                <div class="room-status-item">
                    <span class="status-dot pending"></span>
                    <span>Pending <strong>{{ $roomCounts['pending'] ?? 0 }}/{{ $totalRooms ?? 0 }}</strong></span>
                </div>
                <div class="room-status-item">
                    <span class="status-dot occupied"></span>
                    <span>Occupied <strong>{{ $roomCounts['occupied'] ?? 0 }}/{{ $totalRooms ?? 0 }}</strong></span>
                </div>
                <div class="room-status-item">
                    <span class="status-dot maintenance"></span>
                    <span>Maintenance <strong>{{ $roomCounts['maintenance'] ?? 0 }}/{{ $totalRooms ?? 0 }}</strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row p-0 container-fluid" id="roomsContainer">
    @foreach($rooms as $room)
        <div class="col-md-3 mb-4 room-item" data-status="{{ $room->status }}" data-floor="{{ $room->floor }}">
            <a href="{{ route('rooms.show', $room->room_id) }}" class="room-card-link">
                <div class="room-card">
                    <div class="room-card-body">
                        <div class="room-card-content">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="room-card-title mb-0">
                                    Room {{ $room->room_num }}
                                </h5>
                                <span class="room-status-badge mb-0 {{ $room->status }}">
                                    {{ ucfirst($room->status) }}
                                </span>
                            </div>
                            <div class="room-info-item">
                                <span class="room-info-label">Tenant(s):</span>
                                <span class="tenant-names">
                                    @if($room->activeBooking)
                                        @php
                                            $tenants = [];
                                            if ($room->activeBooking->tenant) {
                                                $firstName = explode(' ', $room->activeBooking->tenant->full_name)[0];
                                                $lastName = explode(' ', $room->activeBooking->tenant->full_name);
                                                $tenants[] = $firstName . ' ' . end($lastName);
                                            }
                                            if ($room->activeBooking->secondaryTenant) {
                                                $firstName = explode(' ', $room->activeBooking->secondaryTenant->full_name)[0];
                                                $lastName = explode(' ', $room->activeBooking->secondaryTenant->full_name);
                                                $tenants[] = $firstName . ' ' . end($lastName);
                                            }
                                        @endphp
                                        {!! implode('<br>', $tenants) !!}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>

                        <div class="room-info-item">
                            <span class="room-info-label">Rate:</span>
                            <span>
                                @if($room->activeBooking && $room->activeBooking->rate)
                                    @php
                                        $rate = $room->activeBooking->rate;
                                        $rateLabel = $rate->rate_name ?? $rate->duration_type;
                                    @endphp
                                    {{ $rateLabel }} - ₱{{ number_format($rate->base_price, 2) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>

                        <div class="room-info-item">
                            <span class="room-info-label">Floor:</span>
                            <span>{{ $room->floor }}</span>
                        </div>

                        <div class="room-info-item">
                            <span class="room-info-label">Capacity:</span>
                            <span>{{ $room->capacity }}</span>
                        </div>


                    </div>

                    <div class="room-card-actions" onclick="event.stopPropagation();">
                            <button class="room-action-btn" type="button" title="More options" onclick="event.stopPropagation();">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                                </svg>
                            </button>
                    </div>
                </div>
            </div>
            </a>
        </div>
    @endforeach
</div>
<!-- Create Room Modal -->
<div class="modal fade" id="createRoomModal" tabindex="-1" aria-labelledby="createRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoomModalLabel">Create New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="room_num" class="form-label">Room Number</label>
                        <input type="text" class="form-control @error('room_num') is-invalid @enderror"
                               id="room_num" name="room_num" required>
                        @error('room_num')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="floor" class="form-label">Floor</label>
                        <input type="text" class="form-control @error('floor') is-invalid @enderror"
                               id="floor" name="floor" required>
                        @error('floor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                               id="capacity" name="capacity" required value="{{ old('capacity', 2) }}">
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="">Select status...</option>
                            <option value="available" selected>Available</option>
                            <option value="pending">Pending</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-plus-circle"></i> Create Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusButtons = document.querySelectorAll('[data-filter="status"]');
    const floorButtons = document.querySelectorAll('[data-filter="floor"]');
    const roomItems = document.querySelectorAll('.room-item');

    let currentStatus = 'all';
    let currentFloor = 'all';

    function filterRooms() {
        roomItems.forEach(item => {
            const itemStatus = item.getAttribute('data-status');
            const itemFloor = item.getAttribute('data-floor');

            const statusMatch = currentStatus === 'all' || itemStatus === currentStatus;
            const floorMatch = currentFloor === 'all' || itemFloor === currentFloor;

            if (statusMatch && floorMatch) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    // Status filter buttons
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            statusButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentStatus = this.getAttribute('data-value');
            filterRooms();
        });
    });

    // Floor filter buttons
    floorButtons.forEach(button => {
        button.addEventListener('click', function() {
            floorButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentFloor = this.getAttribute('data-value');
            filterRooms();
        });
    });
});
</script>
@endsection

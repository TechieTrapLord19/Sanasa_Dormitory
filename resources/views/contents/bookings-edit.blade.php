@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
<style>
    .booking-form-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 2rem;
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
        text-decoration: none;
        display: inline-block;
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
</style>

<div class="booking-form-container">
    <h1 class="mb-4" style="color: #03255b; font-size: 2rem; font-weight: 700;">Edit Booking</h1>

    <form action="{{ route('bookings.update', $booking->booking_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
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
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Check-out Date <span class="text-danger">*</span></label>
                    <input type="date" 
                           class="form-control @error('checkout_date') is-invalid @enderror" 
                           name="checkout_date" 
                           id="checkout_date" 
                           value="{{ old('checkout_date', $booking->checkout_date->format('Y-m-d')) }}" 
                           required>
                    @error('checkout_date')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
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

        <div class="form-group">
            <label class="form-label">Current Booking Info</label>
            <div class="p-3 bg-light rounded">
                <p class="mb-1"><strong>Tenant:</strong> {{ $booking->tenant->full_name }}</p>
                <p class="mb-1"><strong>Rate:</strong> {{ $booking->rate->duration_type }} - ₱{{ number_format($booking->rate->base_price, 2) }}</p>
                <p class="mb-0"><strong>Status:</strong> {{ $booking->status }}</p>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('bookings.show', $booking->booking_id) }}" class="btn-secondary-custom">Cancel</a>
            <button type="submit" class="btn-primary-custom">Update Booking</button>
        </div>
    </form>
</div>

<script>
// Check availability when dates change
document.getElementById('checkin_date').addEventListener('change', checkAvailability);
document.getElementById('checkout_date').addEventListener('change', checkAvailability);

function checkAvailability() {
    const checkin = document.getElementById('checkin_date').value;
    const checkout = document.getElementById('checkout_date').value;
    const currentRoomId = {{ $booking->room_id }};
    
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
                let html = data.available_rooms.map(room => {
                    const isSelected = room.room_id == currentRoomId;
                    return `
                        <div class="room-card ${isSelected ? 'selected' : ''}" 
                             data-room-id="${room.room_id}" 
                             onclick="selectRoom(${room.room_id})">
                            <div class="room-number">${room.room_num}</div>
                            <div class="room-floor">Floor ${room.floor}</div>
                        </div>
                    `;
                }).join('');
                
                // Also show current room if it's not in available list (might be unavailable)
                const currentRoomInList = data.available_rooms.find(r => r.room_id == currentRoomId);
                if (!currentRoomInList) {
                    html = `
                        <div class="room-card selected" 
                             data-room-id="${currentRoomId}" 
                             onclick="selectRoom(${currentRoomId})">
                            <div class="room-number">{{ $booking->room->room_num }}</div>
                            <div class="room-floor">Floor {{ $booking->room->floor }}</div>
                            <div class="text-warning small mt-1">Current Room</div>
                        </div>
                    ` + html;
                }
                
                container.innerHTML = html;
                
                // Set selected room
                if (currentRoomId) {
                    document.getElementById('selected_room_id').value = currentRoomId;
                }
            } else {
                // Show current room even if no others available
                container.innerHTML = `
                    <div class="room-card selected" 
                         data-room-id="${currentRoomId}" 
                         onclick="selectRoom(${currentRoomId})">
                        <div class="room-number">{{ $booking->room->room_num }}</div>
                        <div class="room-floor">Floor {{ $booking->room->floor }}</div>
                        <div class="text-warning small mt-1">Current Room</div>
                    </div>
                    <p class="text-muted">No other rooms available for the selected dates.</p>
                `;
                document.getElementById('selected_room_id').value = currentRoomId;
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    checkAvailability();
});
</script>
@endsection


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Rate;
use App\Models\Invoice;
use App\Models\ElectricReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Payment;
use App\Traits\LogsActivity;

class BookingController extends Controller
{
    use LogsActivity;
    public const MONTHLY_SECURITY_DEPOSIT = 5000.00;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)

    {

        $query = Booking::with(['tenant', 'room', 'rate']);

        // Filter by status tab - we'll filter after loading based on effective status
        $statusFilter = $request->get('status', 'Upcoming');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tenant', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('room', function($q) use ($search) {
                $q->where('room_num', 'like', "%{$search}%");
            });
        }

        $allBookings = $query->with('invoices.payments')->orderBy('checkin_date', 'desc')->get();

        // Filter by effective status
        $filteredBookings = $allBookings->filter(function($booking) use ($statusFilter) {
            $effectiveStatus = $booking->effective_status;
            
            switch ($statusFilter) {
                case 'Active':
                    return $effectiveStatus === 'Active';
                case 'Completed':
                    return $effectiveStatus === 'Completed';
                case 'Canceled':
                    return $effectiveStatus === 'Canceled';
                case 'Pending Payment':
                    return $effectiveStatus === 'Pending Payment';
                case 'Partial Payment':
                    return $effectiveStatus === 'Partial Payment';
                case 'Paid':
                    return $effectiveStatus === 'Paid';
                case 'Upcoming':
                default:
                    return in_array($effectiveStatus, ['Pending Payment', 'Partial Payment', 'Paid', 'Active']) 
                           && $booking->checkin_date >= now()->toDateString();
            }
        })->values();

        // Pagination
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }
        
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $filteredBookings->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $filteredBookings->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Calculate status counts based on effective status
        $statusCounts = [
            'Upcoming' => 0,
            'Pending Payment' => 0,
            'Partial Payment' => 0,
            'Paid' => 0,
            'Active' => 0,
            'Completed' => 0,
            'Canceled' => 0,
        ];

        foreach ($allBookings as $booking) {
            $effectiveStatus = $booking->effective_status;
            if (isset($statusCounts[$effectiveStatus])) {
                $statusCounts[$effectiveStatus]++;
            }
            
            // Count upcoming (bookings with checkin_date >= today and not completed/canceled)
            if (in_array($effectiveStatus, ['Pending Payment', 'Partial Payment', 'Paid', 'Active']) 
                && $booking->checkin_date >= now()->toDateString()) {
                $statusCounts['Upcoming']++;
            }
        }

        $searchTerm = $request->input('search', '');
        
        return view('contents.bookings', compact('bookings', 'statusCounts', 'statusFilter', 'searchTerm', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tenants = Tenant::where('status', 'active')->orderBy('last_name')->get();
        $rooms = Room::all();
        $ratesByDuration = Rate::whereIn('duration_type', ['Daily', 'Weekly', 'Monthly'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('duration_type')
            ->keyBy('duration_type');

        // If dates are provided, filter available rooms
        $availableRooms = collect();
        if ($request->filled('checkin_date') && $request->filled('checkout_date')) {
            $checkinDate = $request->checkin_date;
            $checkoutDate = $request->checkout_date;

            $availableRooms = $this->getAvailableRooms($checkinDate, $checkoutDate);
        }

        return view('contents.bookings-create', compact('tenants', 'rooms', 'availableRooms', 'ratesByDuration'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validatedData = $request->validate([
        'room_id' => 'required|exists:rooms,room_id',
        'tenant_id' => 'required|exists:tenants,tenant_id',
        'rate_id' => 'required|exists:rates,rate_id',
        'checkin_date' => 'required|date|after_or_equal:today',
        'stay_length' => 'required|integer|min:1',
    ]);

    $checkinDate = Carbon::parse($validatedData['checkin_date']);
    $stayLength = (int) $validatedData['stay_length'];
    $checkoutDate = (clone $checkinDate)->addDays($stayLength);

    $rate = Rate::findOrFail($validatedData['rate_id']);
    $durationType = $rate->duration_type;

    // Check availability
    if (Booking::hasOverlap($validatedData['room_id'], $validatedData['checkin_date'], $checkoutDate->toDateString())) {
        return back()->withErrors(['room_id' => 'This room is not available for the selected dates.'])->withInput();
    }

    // Calculate total fee based on rate duration type
    $totalFee = $this->calculateTotalFee($rate, $stayLength);

    DB::beginTransaction();
    try {
        // Create booking with 'Pending Payment' status
        $booking = Booking::create([
            'room_id' => $validatedData['room_id'],
            'tenant_id' => $validatedData['tenant_id'],
            'rate_id' => $rate->rate_id,
            'recorded_by_user_id' => Auth::id(),
            'checkin_date' => $validatedData['checkin_date'],
            'checkout_date' => $checkoutDate->toDateString(),
            'total_calculated_fee' => $totalFee,
            'status' => 'Pending Payment', // Start as Pending Payment
        ]);

        // Update room status to occupied
        $room = Room::findOrFail($validatedData['room_id']);
        $room->update(['status' => 'occupied']);

        // Create invoices based on booking type
        $months = max(1, (int) ceil($stayLength / 30));
        $weeks = max(1, (int) ceil($stayLength / 7));
        $days = max(1, $stayLength);

        if ($durationType === 'Monthly') {
            // Monthly: Create TWO separate invoices
            
            // Invoice 1: Rent + Utilities (Water + WiFi)
            $rentSubtotal = $rate->base_price * $months;
            $utilityWaterFee = 350.00 * $months;
            $utilityWifiFee = 260.00 * $months;
            $rentAndUtilitiesTotal = $rentSubtotal + $utilityWaterFee + $utilityWifiFee;

            Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => $rentSubtotal,
                'utility_water_fee' => $utilityWaterFee,
                'utility_wifi_fee' => $utilityWifiFee,
                'utility_electricity_fee' => 0.00, // No electricity on initial invoice
                'total_due' => $rentAndUtilitiesTotal,
                'is_paid' => false,
            ]);

            // Invoice 2: Security Deposit
            Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => 0,
                'utility_water_fee' => 0,
                'utility_wifi_fee' => 0,
                'utility_electricity_fee' => self::MONTHLY_SECURITY_DEPOSIT,
                'total_due' => self::MONTHLY_SECURITY_DEPOSIT,
                'is_paid' => false,
            ]);

        } else {
            // Daily/Weekly: One invoice with rent only (utilities included)
            if ($durationType === 'Weekly') {
                $rentSubtotal = $rate->base_price * $weeks;
            } else { // Daily
                $rentSubtotal = $rate->base_price * $days;
            }

            Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => $rentSubtotal,
                'utility_water_fee' => 0,
                'utility_wifi_fee' => 0,
                'utility_electricity_fee' => 0,
                'total_due' => $rentSubtotal,
                'is_paid' => false,
            ]);
        }

        DB::commit();

        $booking->load('tenant', 'room');
        $tenant = $booking->tenant;
        $room = $booking->room;
        $this->logActivity(
            'Created Booking',
            "Created booking #{$booking->booking_id} for tenant {$tenant->full_name} in room {$room->room_num} (Check-in: {$booking->checkin_date}, Check-out: {$booking->checkout_date})",
            $booking
        );

        return redirect()->route('invoices')
                        ->with('success', 'Booking created successfully! Invoices have been generated. Record payments to enable check-in.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Failed to create booking: ' . $e->getMessage()])->withInput();
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['tenant', 'room', 'rate', 'recordedBy', 'invoices.payments', 'refunds.payment', 'refunds.refundedBy'])
                          ->findOrFail($id);

        $stayLengthDays = max(1, $booking->checkin_date->diffInDays($booking->checkout_date));
        $chargeSummary = $this->buildChargeSummary($booking->rate, $stayLengthDays);

        // Get all payments for this booking (directly from payments table)
        $allPayments = Payment::where('booking_id', $booking->booking_id)
            ->with('refunds')
            ->get();

        // Get the two most recent readings for electricity invoice generation
        $electricReadings = ElectricReading::where('room_id', $booking->room_id)
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_id', 'desc')
            ->take(2)
            ->get();

        $lastReading = $electricReadings->count() >= 2 ? $electricReadings->last() : null;
        $currentReading = $electricReadings->count() >= 1 ? $electricReadings->first() : null;

        return view('contents.bookings-show', compact('booking', 'chargeSummary', 'stayLengthDays', 'allPayments', 'lastReading', 'currentReading'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $booking = Booking::with(['tenant', 'room', 'rate'])->findOrFail($id);
        $tenants = Tenant::where('status', 'active')->orderBy('last_name')->get();
        $rooms = Room::all();
        $ratesByDuration = Rate::whereIn('duration_type', ['Daily', 'Weekly', 'Monthly'])
            ->orderByDesc('updated_at')
            ->get()
            ->unique('duration_type')
            ->keyBy('duration_type');

        return view('contents.bookings-edit', compact('booking', 'tenants', 'rooms', 'ratesByDuration'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                'room_id' => 'required|exists:rooms,room_id',
                'checkin_date' => 'required|date',
                'stay_length' => 'required|integer|min:1',
            ], [
                'room_id.required' => 'Please select a room.',
                'room_id.exists' => 'The selected room does not exist.',
                'checkin_date.required' => 'Check-in date is required.',
                'checkin_date.date' => 'Check-in date must be a valid date.',
                'stay_length.required' => 'Stay length is required.',
                'stay_length.integer' => 'Stay length must be a whole number.',
                'stay_length.min' => 'Stay length must be at least 1 day.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $booking = Booking::findOrFail($id);
        $stayLength = (int) $validatedData['stay_length'];
        $checkinDate = Carbon::parse($validatedData['checkin_date']);
        $checkoutDate = (clone $checkinDate)->addDays($stayLength);

        // Store original values for comparison
        $originalRoomId = $booking->room_id;
        $originalCheckoutDate = $booking->checkout_date;
        $roomChanged = $validatedData['room_id'] != $originalRoomId;
        
        // Calculate extension days if checkout date is extended
        $extensionDays = 0;
        if ($checkoutDate->gt($originalCheckoutDate)) {
            $extensionDays = (int) $originalCheckoutDate->diffInDays($checkoutDate);
        }

        // Determine rate based on new stay length
        [$rate, $durationType] = $this->determineRateForStayLength($stayLength);

        if (!$rate) {
            return back()->withErrors(['stay_length' => 'No rate is configured for the selected stay length.'])->withInput();
        }

        // Check availability for new room (excluding current booking)
        if ($roomChanged) {
            if (Booking::hasOverlap($validatedData['room_id'], $validatedData['checkin_date'], $checkoutDate->toDateString(), $id)) {
                return back()->withErrors(['room_id' => 'This room is not available for the selected dates.'])->withInput();
            }
        }

        // Recalculate total fee
        $totalFee = $this->calculateTotalFee($rate, $stayLength);

        DB::beginTransaction();
        try {
            // Handle room changes
            if ($roomChanged) {
                $oldRoom = Room::findOrFail($originalRoomId);
                $newRoom = Room::findOrFail($validatedData['room_id']);

                // Update old room: check if it has other Active bookings (excluding the current booking being moved)
                $hasOtherActiveBookings = Booking::where('room_id', $originalRoomId)
                    ->where('booking_id', '!=', $booking->booking_id)
                    ->where('status', 'Active')
                    ->exists();

                // Only update old room status if it's not in maintenance
                if ($oldRoom->status !== 'maintenance') {
                    if ($hasOtherActiveBookings) {
                        // Has other active bookings, keep it occupied
                        $oldRoom->update(['status' => 'occupied']);
                    } else {
                        // No other active bookings, set to available (since we're moving this booking away)
                        $oldRoom->update(['status' => 'available']);
                    }
                }

                // Update new room based on booking status
                if ($newRoom->status !== 'maintenance') {
                    if ($booking->status === 'Active') {
                        // If booking is Active, new room should be occupied
                        $newRoom->update(['status' => 'occupied']);
                    } else {
                        // If booking is not Active, check if new room has Active bookings
                        $hasActiveBookings = Booking::where('room_id', $validatedData['room_id'])
                            ->where('status', 'Active')
                            ->exists();

                        if ($hasActiveBookings) {
                            $newRoom->update(['status' => 'occupied']);
                        } else {
                            $newRoom->update(['status' => 'available']);
                        }
                    }
                }
            }

            // Update booking (security_deposit_due is not stored in bookings table, it's in invoices)
            $booking->update([
                'room_id' => $validatedData['room_id'],
                'checkin_date' => $validatedData['checkin_date'],
                'checkout_date' => $checkoutDate->toDateString(),
                'rate_id' => $rate->rate_id,
                'total_calculated_fee' => $totalFee,
            ]);

            // If booking is extended and is Active, generate renewal invoice
            if ($extensionDays > 0 && $booking->status === 'Active') {
                // Calculate rent and utilities for extension period
                $rentSubtotal = 0;
                $utilityWaterFee = 0;
                $utilityWifiFee = 0;

                if ($durationType === 'Monthly') {
                    // If extension is less than 30 days, use daily rate instead
                    if ($extensionDays < 30) {
                        // Get daily rate
                        $dailyRate = Rate::where('duration_type', 'Daily')->first();
                        if ($dailyRate) {
                            // Daily rate includes all utilities
                            $rentSubtotal = $dailyRate->base_price * $extensionDays;
                            $utilityWaterFee = 0; // Included in daily rate
                            $utilityWifiFee = 0; // Included in daily rate
                        } else {
                            // Fallback: calculate daily rate from monthly (5000/30 = 166.67 per day)
                            $dailyPrice = $rate->base_price / 30;
                            $rentSubtotal = $dailyPrice * $extensionDays;
                            // For partial months, prorate utilities
                            $utilityWaterFee = (350.00 / 30) * $extensionDays;
                            $utilityWifiFee = (260.00 / 30) * $extensionDays;
                        }
                    } else {
                        // Extension is 30+ days, calculate months needed
                        $months = max(1, (int) ceil($extensionDays / 30));
                        $rentSubtotal = $rate->base_price * $months;
                        $utilityWaterFee = 350.00 * $months;
                        $utilityWifiFee = 260.00 * $months;
                    }
                } elseif ($durationType === 'Weekly') {
                    // If extension is less than 7 days, use daily rate instead
                    if ($extensionDays < 7) {
                        // Get daily rate
                        $dailyRate = Rate::where('duration_type', 'Daily')->first();
                        if ($dailyRate) {
                            // Daily rate includes all utilities
                            $rentSubtotal = $dailyRate->base_price * $extensionDays;
                            $utilityWaterFee = 0; // Included in daily rate
                            $utilityWifiFee = 0; // Included in daily rate
                        } else {
                            // Fallback: calculate daily rate from weekly (1750/7 = 250 per day)
                            $dailyPrice = $rate->base_price / 7;
                            $rentSubtotal = $dailyPrice * $extensionDays;
                            $utilityWaterFee = 0; // Included in weekly rate
                            $utilityWifiFee = 0; // Included in weekly rate
                        }
                    } else {
                        // Extension is 7+ days, calculate weeks needed
                        $weeks = max(1, (int) ceil($extensionDays / 7));
                        $rentSubtotal = $rate->base_price * $weeks;
                        $utilityWaterFee = 0; // Included in weekly rate
                        $utilityWifiFee = 0; // Included in weekly rate
                    }
                } else { // Daily
                    $rentSubtotal = $rate->base_price * $extensionDays;
                    $utilityWaterFee = 0; // Included in daily rate
                    $utilityWifiFee = 0; // Included in daily rate
                }

                // Create invoice for extension
                Invoice::create([
                    'booking_id' => $booking->booking_id,
                    'date_generated' => now()->toDateString(),
                    'rent_subtotal' => $rentSubtotal,
                    'utility_water_fee' => $utilityWaterFee,
                    'utility_wifi_fee' => $utilityWifiFee,
                    'utility_electricity_fee' => 0.00, // Electricity is separate
                    'total_due' => $rentSubtotal + $utilityWaterFee + $utilityWifiFee,
                    'is_paid' => false,
                ]);
            }

            DB::commit();

            $tenant = $booking->tenant;
            $room = $booking->room;
            $description = "Updated booking #{$booking->booking_id} for tenant {$tenant->full_name}";
            if ($roomChanged) {
                $oldRoom = Room::find($originalRoomId);
                $newRoom = Room::find($validatedData['room_id']);
                $description .= " - Room changed from {$oldRoom->room_num} to {$newRoom->room_num}";
            }
            if ($extensionDays > 0 && $booking->status === 'Active') {
                $description .= " - Extended by {$extensionDays} days";
            }
            $this->logActivity('Updated Booking', $description, $booking);

            $successMessage = 'Booking updated successfully!';
            if ($roomChanged) {
                $successMessage .= ' Room changed from ' . Room::find($originalRoomId)->room_num . ' to ' . Room::find($validatedData['room_id'])->room_num . '.';
            }
            if ($extensionDays > 0 && $booking->status === 'Active') {
                $successMessage .= ' Extension invoice generated for ' . $extensionDays . ' days.';
            }

            return redirect()->route('bookings.show', $booking->booking_id)
                            ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update booking: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:1000',
        ]);

        $booking = Booking::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update booking status and cancellation reason
            $booking->update([
                'status' => 'Canceled',
                'cancellation_reason' => $request->cancellation_reason,
            ]);

            // Update room status back to available
            $room = $booking->room;
            $room->update(['status' => 'available']);

            DB::commit();

            $tenant = $booking->tenant;
            $room = $booking->room;
            $this->logActivity(
                'Canceled Booking',
                "Canceled booking #{$booking->booking_id} for tenant {$tenant->full_name} in room {$room->room_num}. Reason: {$request->cancellation_reason}",
                $booking
            );

            return redirect()->route('bookings.index')
                            ->with('success', 'Booking canceled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to cancel booking: ' . $e->getMessage()]);
        }
    }

    /**
     * Check in a tenant
     */
public function checkin(string $id)
{
    $booking = Booking::with('invoices.payments')->findOrFail($id);
    
    // Allow check-in if booking is not already Active, Completed, or Canceled
    if (in_array($booking->status, ['Active', 'Completed', 'Canceled'])) {
        return back()->withErrors(['error' => 'Cannot check in. Booking is already ' . $booking->status . '.']);
    }

    // Separate invoices into Rent+Utilities and Security Deposit
    $rentUtilitiesInvoices = $booking->invoices->filter(function($invoice) {
        return $invoice->rent_subtotal > 0 || $invoice->utility_water_fee > 0 || $invoice->utility_wifi_fee > 0;
    });
    
    $securityDepositInvoice = $booking->invoices->first(function($invoice) {
        return $invoice->rent_subtotal == 0 && 
               $invoice->utility_water_fee == 0 && 
               $invoice->utility_wifi_fee == 0 && 
               $invoice->utility_electricity_fee > 0;
    });

    // Rule 1: Rent + Utilities MUST be fully paid (aggregate ALL invoices, including extensions)
    if ($rentUtilitiesInvoices->isEmpty()) {
        return back()->withErrors(['error' => 'Cannot check in. Rent + Utilities invoice not found.']);
    }
    
    // Sum up total due and payments across ALL rent/utilities invoices
    $rentUtilitiesDue = $rentUtilitiesInvoices->sum('total_due');
    $rentUtilitiesPaid = $rentUtilitiesInvoices->sum(function($invoice) {
        return $invoice->payments->sum('amount');
    });
    
    if ($rentUtilitiesPaid < $rentUtilitiesDue) {
        return back()->withErrors(['error' => 'Cannot check in. Rent + Utilities must be fully paid. Current payment: ₱' . number_format($rentUtilitiesPaid, 2) . ' / ₱' . number_format($rentUtilitiesDue, 2)]);
    }

    // Rule 2: Security Deposit must be at least HALF paid (only if security deposit invoice exists)
    // Note: Daily/Weekly bookings don't have security deposit invoices, so this check is optional
    if ($securityDepositInvoice) {
        $securityDepositDue = $securityDepositInvoice->total_due;
        $securityDepositPaid = $securityDepositInvoice->payments->sum('amount');
        $requiredMinimum = $securityDepositDue / 2; // Half of security deposit
        
        if ($securityDepositPaid < $requiredMinimum) {
            return back()->withErrors(['error' => 'Cannot check in. Security Deposit must be at least half paid (₱' . number_format($requiredMinimum, 2) . '). Current payment: ₱' . number_format($securityDepositPaid, 2) . ' / ₱' . number_format($securityDepositDue, 2)]);
        }
    }
    // If no security deposit invoice exists (Daily/Weekly bookings), allow check-in if rent is fully paid

    DB::beginTransaction();
    try {
        // Manually set booking to Active (check-in is always manual)
        $booking->update(['status' => 'Active']);
        $booking->room->update(['status' => 'occupied']);

        DB::commit();

        $tenant = $booking->tenant;
        $room = $booking->room;
        $this->logActivity(
            'Checked In Tenant',
            "Checked in tenant {$tenant->full_name} for booking #{$booking->booking_id} in room {$room->room_num}",
            $booking
        );

        return redirect()->route('bookings.show', $booking->booking_id)
                        ->with('success', 'Tenant checked in successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'Failed to check in tenant: ' . $e->getMessage()]);
    }
}
    /**
     * Generate electricity invoice separately
     */
    public function generateElectricityInvoice(string $id, Request $request)
    {
        $booking = Booking::with(['rate', 'room'])->findOrFail($id);

        if ($booking->status !== 'Active') {
            return back()->withErrors(['error' => 'Only active bookings can generate electricity invoices.']);
        }

        // Get the two most recent readings for this room
        $electricReadings = ElectricReading::where('room_id', $booking->room_id)
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_id', 'desc')
            ->take(2)
            ->get();

        if ($electricReadings->count() < 2) {
            return back()->withErrors(['error' => 'Cannot generate electricity invoice. At least two meter readings are required. Please record readings on the Electric Readings page first.']);
        }

        $currentReading = $electricReadings->first();
        $lastReading = $electricReadings->last();

        // Check if current reading is already billed
        if ($currentReading->is_billed) {
            return back()->withErrors(['error' => 'The current meter reading has already been billed. Please record a new reading first.']);
        }

        $validated = $request->validate([
            'electricity_rate_per_kwh' => ['required', 'numeric', 'min:0'],
        ], [
            'electricity_rate_per_kwh.required' => 'Electricity rate per kWh is required.',
            'electricity_rate_per_kwh.numeric' => 'Electricity rate must be a number.',
            'electricity_rate_per_kwh.min' => 'Electricity rate must be at least 0.',
        ]);

        DB::beginTransaction();
        try {
            // Calculate electricity usage from the readings
            $lastReadingValue = (float) $lastReading->meter_value_kwh;
            $currentReadingValue = (float) $currentReading->meter_value_kwh;
            $kwhUsed = max(0, $currentReadingValue - $lastReadingValue);
            $ratePerKwh = (float) $validated['electricity_rate_per_kwh'];
            $electricityFee = $kwhUsed * $ratePerKwh;

            // Mark the current reading as billed (update existing record)
            $currentReading->update(['is_billed' => true]);

            // Create separate electricity invoice
            Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => 0,
                'utility_water_fee' => 0,
                'utility_wifi_fee' => 0,
                'utility_electricity_fee' => $electricityFee,
                'total_due' => $electricityFee,
                'is_paid' => false,
            ]);

            DB::commit();

            $tenant = $booking->tenant;
            $room = $booking->room;
            $this->logActivity(
                'Generated Electricity Invoice',
                "Generated electricity invoice for booking #{$booking->booking_id} (Tenant: {$tenant->full_name}, Room: {$room->room_num}, Amount: ₱" . number_format($electricityFee, 2) . ")",
                $booking
            );

            return redirect()->route('bookings.show', $booking->booking_id)
                ->with('success', 'Electricity invoice generated successfully! Amount: ₱' . number_format($electricityFee, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to generate electricity invoice: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Generate renewal invoice for an active booking (without electricity)
     */
    public function generateRenewalInvoice(string $id, Request $request)
    {
        $booking = Booking::with(['rate', 'room'])->findOrFail($id);

        if ($booking->status !== 'Active') {
            return back()->withErrors(['error' => 'Only active bookings can be renewed.']);
        }

        $durationType = $booking->rate->duration_type;
        $validated = $request->validate([
            'extension_days' => ['required', 'integer', 'min:1'],
        ], [
            'extension_days.required' => 'Extension days is required.',
            'extension_days.integer' => 'Extension days must be a whole number.',
            'extension_days.min' => 'Extension days must be at least 1.',
        ]);

        DB::beginTransaction();
        try {
            $requestedExtensionDays = (int) $validated['extension_days'];
            $actualExtensionDays = $requestedExtensionDays;
            $daysPastDue = 0;

            // Calculate days past due (if checkout_date has passed)
            $today = now()->toDateString();
            $checkoutDate = $booking->checkout_date->toDateString();
            
            if ($checkoutDate < $today) {
                $daysPastDue = max(0, (int) Carbon::parse($checkoutDate)->diffInDays(Carbon::parse($today)));
                
                // If past due, check if within 3-day grace period
                if ($daysPastDue <= 3) {
                    // Within grace period: days past due are consumed (deducted from extension days)
                    $actualExtensionDays = max(1, $requestedExtensionDays - $daysPastDue);
                } else {
                    // Past 3-day grace period: cannot renew
                    DB::rollBack();
                    return back()->withErrors(['error' => 'Cannot renew. Booking is past the 3-day grace period. Please contact the tenant to settle outstanding payments.'])->withInput();
                }
            }

            // Calculate rent and utilities for next period
            $rentSubtotal = 0;
            $utilityWaterFee = 0;
            $utilityWifiFee = 0;

            if ($durationType === 'Monthly') {
                // If extension is less than 30 days, use daily rate instead
                if ($actualExtensionDays < 30) {
                    // Get daily rate
                    $dailyRate = Rate::where('duration_type', 'Daily')->first();
                    if ($dailyRate) {
                        // Daily rate includes all utilities
                        $rentSubtotal = $dailyRate->base_price * $actualExtensionDays;
                        $utilityWaterFee = 0; // Included in daily rate
                        $utilityWifiFee = 0; // Included in daily rate
                    } else {
                        // Fallback: calculate daily rate from monthly (5000/30 = 166.67 per day)
                        $dailyPrice = $booking->rate->base_price / 30;
                        $rentSubtotal = $dailyPrice * $actualExtensionDays;
                        // For partial months, prorate utilities
                        $utilityWaterFee = (350.00 / 30) * $actualExtensionDays;
                        $utilityWifiFee = (260.00 / 30) * $actualExtensionDays;
                    }
                } else {
                    // Extension is 30+ days, calculate months needed
                    $months = max(1, (int) ceil($actualExtensionDays / 30));
                    $rentSubtotal = $booking->rate->base_price * $months;
                    $utilityWaterFee = 350.00 * $months;
                    $utilityWifiFee = 260.00 * $months;
                }
            } elseif ($durationType === 'Weekly') {
                // If extension is less than 7 days, use daily rate instead
                if ($actualExtensionDays < 7) {
                    // Get daily rate
                    $dailyRate = Rate::where('duration_type', 'Daily')->first();
                    if ($dailyRate) {
                        // Daily rate includes all utilities
                        $rentSubtotal = $dailyRate->base_price * $actualExtensionDays;
                        $utilityWaterFee = 0; // Included in daily rate
                        $utilityWifiFee = 0; // Included in daily rate
                    } else {
                        // Fallback: calculate daily rate from weekly (1750/7 = 250 per day)
                        $dailyPrice = $booking->rate->base_price / 7;
                        $rentSubtotal = $dailyPrice * $actualExtensionDays;
                        $utilityWaterFee = 0; // Included in weekly rate
                        $utilityWifiFee = 0; // Included in weekly rate
                    }
                } else {
                    // Extension is 7+ days, calculate weeks needed
                    $weeks = max(1, (int) ceil($actualExtensionDays / 7));
                    $rentSubtotal = $booking->rate->base_price * $weeks;
                    $utilityWaterFee = 0; // Included in weekly rate
                    $utilityWifiFee = 0; // Included in weekly rate
                }
            } else { // Daily
                $rentSubtotal = $booking->rate->base_price * $actualExtensionDays;
                $utilityWaterFee = 0; // Included in daily rate
                $utilityWifiFee = 0; // Included in daily rate
            }

            // Create renewal invoice (rent + utilities only, NO electricity)
            $totalDue = $rentSubtotal + $utilityWaterFee + $utilityWifiFee;

            Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => $rentSubtotal,
                'utility_water_fee' => $utilityWaterFee,
                'utility_wifi_fee' => $utilityWifiFee,
                'utility_electricity_fee' => 0.00, // Electricity is separate
                'total_due' => $totalDue,
                'is_paid' => false,
            ]);

            // Extend booking checkout_date by actual extension days
            $newCheckoutDate = (clone $booking->checkout_date)->addDays($actualExtensionDays);
            $booking->update([
                'checkout_date' => $newCheckoutDate->toDateString(),
            ]);

            DB::commit();

            $tenant = $booking->tenant;
            $room = $booking->room;
            $description = "Generated renewal invoice for booking #{$booking->booking_id} (Tenant: {$tenant->full_name}, Room: {$room->room_num}, Extended by {$actualExtensionDays} days";
            if ($daysPastDue > 0) {
                $description .= ", {$daysPastDue} days past due deducted";
            }
            $description .= ", Amount: ₱" . number_format($totalDue, 2) . ")";
            $this->logActivity('Generated Renewal Invoice', $description, $booking);

            $successMessage = 'Renewal invoice generated successfully! Booking extended by ' . $actualExtensionDays . ' days.';
            if ($daysPastDue > 0) {
                $successMessage .= ' (' . $daysPastDue . ' days past due were deducted from the extension period.)';
            }

            return redirect()->route('bookings.show', $booking->booking_id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to generate renewal invoice: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Check out a tenant
     */
    public function checkout(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'Active') {
            return back()->withErrors(['error' => 'Only active bookings can be checked out.']);
        }

        // Check if all invoices are paid
        $unpaidInvoices = $booking->invoices()->where('is_paid', false)->count();
        if ($unpaidInvoices > 0) {
            return back()->withErrors(['error' => 'Cannot check out. There are unpaid invoices.']);
        }

        DB::beginTransaction();
        try {
            $booking->update(['status' => 'Completed']);
            $booking->room->update(['status' => 'maintenance']); // Using 'maintenance' as closest to 'Cleaning'

            DB::commit();

            $tenant = $booking->tenant;
            $room = $booking->room;
            $this->logActivity(
                'Checked Out Tenant',
                "Checked out tenant {$tenant->full_name} from booking #{$booking->booking_id} in room {$room->room_num}",
                $booking
            );

            return redirect()->route('bookings.show', $booking->booking_id)
                            ->with('success', 'Tenant checked out successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to check out tenant: ' . $e->getMessage()]);
        }
    }

    /**
     * Get available rooms for a date range
     */
    public function getAvailableRooms($checkinDate, $checkoutDate, $excludeBookingId = null)
    {
        $allRooms = Room::all();
        $availableRooms = collect();

        foreach ($allRooms as $room) {
            // Check if room is available (not in maintenance)
            if ($room->status === 'maintenance') {
                continue;
            }

            // Check for overlapping bookings (excluding the specified booking if provided)
            if (!Booking::hasOverlap($room->room_id, $checkinDate, $checkoutDate, $excludeBookingId)) {
                $availableRooms->push($room);
            }
        }

        return $availableRooms;
    }

    /**
     * Calculate total fee based on rate and dates
     */
    private function calculateTotalFee($rate, int $stayLengthInDays)
    {
        $days = max(1, $stayLengthInDays);

        switch ($rate->duration_type) {
            case 'Weekly':
                $weeks = (int) ceil($days / 7);
                return $rate->base_price * $weeks;
            case 'Monthly':
                $months = (int) ceil($days / 30);
                return $rate->base_price * max(1, $months);
            case 'Daily':
            default:
                return $rate->base_price * $days;
        }
    }

    /**
     * Build a detailed charge summary for a booking
     */
    private function buildChargeSummary($rate, int $stayLengthInDays): array
    {
        $days = max(1, $stayLengthInDays);
        $duration = $rate->duration_type;
        $rateTotal = 0;
        $securityDeposit = 0.00;
        $waterFee = 0.00;
        $wifiFee = 0.00;
        $note = null;
        $units = 1;

        if ($duration === 'Monthly') {
            $units = max(1, (int) ceil($days / 30));
            $rateTotal = $rate->base_price * $units;
            $securityDeposit = self::MONTHLY_SECURITY_DEPOSIT;
            $waterFee = 350.00 * $units;
            $wifiFee = 260.00 * $units;
            $note = 'Security deposit and utilities are itemized separately for monthly stays.';
        } elseif ($duration === 'Weekly') {
            $units = max(1, (int) ceil($days / 7));
            $rateTotal = $rate->base_price * $units;
            $note = 'Water and Wi-Fi are included in the weekly package.';
        } else {
            $units = $days;
            $rateTotal = $rate->base_price * $units;
            $note = 'Water and Wi-Fi are included in the daily package.';
        }

        $totalDue = $rateTotal + $securityDeposit + $waterFee + $wifiFee;

        return [
            'duration_type' => $duration,
            'units' => $units,
            'rate_total' => $rateTotal,
            'security_deposit' => $securityDeposit,
            'water_fee' => $waterFee,
            'wifi_fee' => $wifiFee,
            'total_due' => $totalDue,
            'note' => $note,
        ];
    }

    /**
     * Determine the appropriate rate model based on stay length
     */
    private function determineRateForStayLength(int $stayLengthInDays): array
    {
        if ($stayLengthInDays >= 30) {
            $rate = Rate::where('duration_type', 'Monthly')->first();
            return [$rate, 'Monthly'];
        }

        if ($stayLengthInDays >= 7) {
            $rate = Rate::where('duration_type', 'Weekly')->first();
            return [$rate, 'Weekly'];
        }

        $rate = Rate::where('duration_type', 'Daily')->first();
        return [$rate, 'Daily'];
    }

    /**
     * API endpoint to check room availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
            'exclude_booking_id' => 'nullable|exists:bookings,booking_id',
        ]);

        $excludeBookingId = $request->input('exclude_booking_id');
        $availableRooms = $this->getAvailableRooms($request->checkin_date, $request->checkout_date, $excludeBookingId);

        return response()->json([
            'available_rooms' => $availableRooms->map(function($room) {
                return [
                    'room_id' => $room->room_id,
                    'room_num' => $room->room_num,
                    'floor' => $room->floor,
                    'capacity' => $room->capacity,
                ];
            })->values()
        ]);
    }
}
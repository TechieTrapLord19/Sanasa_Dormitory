<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Rate;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)

    {

        $query = Booking::with(['tenant', 'room', 'rate']);

        // Filter by status tab
        $statusFilter = $request->get('status', 'Upcoming');
        switch ($statusFilter) {
            case 'Active':
                $query->where('status', 'Active');
                break;
            case 'Completed':
                $query->where('status', 'Completed');
                break;
            case 'Canceled':
                $query->where('status', 'Canceled');
                break;
            case 'Upcoming':
            default:
                $query->whereIn('status', ['Reserved', 'Active'])
                      ->where('checkin_date', '>=', now()->toDateString());
                break;
        }

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

        $bookings = $query->orderBy('checkin_date', 'desc')->get();

        // Get counts for tabs
        $statusCounts = [
            'Upcoming' => Booking::whereIn('status', ['Reserved', 'Active'])
                                 ->where('checkin_date', '>=', now()->toDateString())
                                 ->count(),
            'Active' => Booking::where('status', 'Active')->count(),
            'Completed' => Booking::where('status', 'Completed')->count(),
            'Canceled' => Booking::where('status', 'Canceled')->count(),
        ];

        return view('contents.bookings', compact('bookings', 'statusCounts', 'statusFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tenants = Tenant::where('status', 'active')->orderBy('last_name')->get();
        $rates = Rate::all();
        $rooms = Room::all();

        // If dates are provided, filter available rooms
        $availableRooms = collect();
        if ($request->filled('checkin_date') && $request->filled('checkout_date')) {
            $checkinDate = $request->checkin_date;
            $checkoutDate = $request->checkout_date;

            $availableRooms = $this->getAvailableRooms($checkinDate, $checkoutDate);
        }

        return view('contents.bookings-create', compact('tenants', 'rates', 'rooms', 'availableRooms'));
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
            'checkout_date' => 'required|date|after:checkin_date',
        ]);

        // Check availability
        if (Booking::hasOverlap($validatedData['room_id'], $validatedData['checkin_date'], $validatedData['checkout_date'])) {
            return back()->withErrors(['room_id' => 'This room is not available for the selected dates.'])->withInput();
        }

        // Get rate to calculate total fee
        $rate = Rate::findOrFail($validatedData['rate_id']);
        $checkinDate = Carbon::parse($validatedData['checkin_date']);
        $checkoutDate = Carbon::parse($validatedData['checkout_date']);

        // Calculate total fee based on rate duration type
        $totalFee = $this->calculateTotalFee($rate, $checkinDate, $checkoutDate);

        DB::beginTransaction();
        try {
            // Create booking
            $booking = Booking::create([
                'room_id' => $validatedData['room_id'],
                'tenant_id' => $validatedData['tenant_id'],
                'rate_id' => $validatedData['rate_id'],
                'recorded_by_user_id' => Auth::id(),
                'checkin_date' => $validatedData['checkin_date'],
                'checkout_date' => $validatedData['checkout_date'],
                'total_calculated_fee' => $totalFee,
                'status' => 'Reserved',
            ]);

            // Update room status
            $room = Room::findOrFail($validatedData['room_id']);
            $room->update(['status' => 'occupied']); // Using 'occupied' as closest match to 'Reserved'

            // Create initial invoice (Deposit for monthly, Upfront for daily/weekly)
            $invoiceType = $rate->duration_type === 'Monthly' ? 'Deposit' : 'Upfront Payment';
            $initialAmount = $this->calculateInitialPayment($rate, $totalFee);

            $invoice = Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => $initialAmount,
                'utility_water_fee' => 0,
                'utility_wifi_fee' => 0,
                'utility_electricity_fee' => 0,
                'total_due' => $initialAmount,
                'is_paid' => false,
            ]);

            DB::commit();

            return redirect()->route('bookings.show', $booking->booking_id)
                            ->with('success', 'Booking created successfully! Please add the initial payment.');
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
        $booking = Booking::with(['tenant', 'room', 'rate', 'invoices.payments', 'recordedBy'])
                          ->findOrFail($id);

        // Get all invoices with their payment totals
        $invoices = $booking->invoices()->with('payments')->get();

        // Calculate totals
        $totalInvoiced = $invoices->sum('total_due');
        $totalPaid = $invoices->sum(function($invoice) {
            return $invoice->payments->sum('amount');
        });
        $totalBalance = $totalInvoiced - $totalPaid;

        return view('contents.bookings-show', compact('booking', 'invoices', 'totalInvoiced', 'totalPaid', 'totalBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $booking = Booking::with(['tenant', 'room', 'rate'])->findOrFail($id);
        $tenants = Tenant::where('status', 'active')->orderBy('last_name')->get();
        $rates = Rate::all();
        $rooms = Room::all();

        return view('contents.bookings-edit', compact('booking', 'tenants', 'rates', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,room_id',
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
        ]);

        // Check availability (excluding current booking)
        if (Booking::hasOverlap($validatedData['room_id'], $validatedData['checkin_date'], $validatedData['checkout_date'], $booking->booking_id)) {
            return back()->withErrors(['room_id' => 'This room is not available for the selected dates.'])->withInput();
        }

        // Recalculate total fee if dates or rate changed
        $rate = $booking->rate;
        $checkinDate = Carbon::parse($validatedData['checkin_date']);
        $checkoutDate = Carbon::parse($validatedData['checkout_date']);
        $totalFee = $this->calculateTotalFee($rate, $checkinDate, $checkoutDate);

        DB::beginTransaction();
        try {
            // Update booking
            $booking->update([
                'room_id' => $validatedData['room_id'],
                'checkin_date' => $validatedData['checkin_date'],
                'checkout_date' => $validatedData['checkout_date'],
                'total_calculated_fee' => $totalFee,
            ]);

            DB::commit();

            return redirect()->route('bookings.show', $booking->booking_id)
                            ->with('success', 'Booking updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update booking: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update(['status' => 'Canceled']);

            // Update room status back to available
            $room = $booking->room;
            $room->update(['status' => 'available']);

            // Note: Deposit refund logic would go here if needed

            DB::commit();

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
        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'Reserved') {
            return back()->withErrors(['error' => 'Only reserved bookings can be checked in.']);
        }

        DB::beginTransaction();
        try {
            $booking->update(['status' => 'Active']);
            $booking->room->update(['status' => 'occupied']);

            DB::commit();

            return redirect()->route('bookings.show', $booking->booking_id)
                            ->with('success', 'Tenant checked in successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to check in tenant: ' . $e->getMessage()]);
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
    public function getAvailableRooms($checkinDate, $checkoutDate)
    {
        $allRooms = Room::all();
        $availableRooms = collect();

        foreach ($allRooms as $room) {
            // Check if room is available (not in maintenance)
            if ($room->status === 'maintenance') {
                continue;
            }

            // Check for overlapping bookings
            if (!Booking::hasOverlap($room->room_id, $checkinDate, $checkoutDate)) {
                $availableRooms->push($room);
            }
        }

        return $availableRooms;
    }

    /**
     * Calculate total fee based on rate and dates
     */
    private function calculateTotalFee($rate, $checkinDate, $checkoutDate)
    {
        $days = $checkinDate->diffInDays($checkoutDate);

        switch ($rate->duration_type) {
            case 'Daily':
                return $rate->base_price * $days;
            case 'Weekly':
                $weeks = ceil($days / 7);
                return $rate->base_price * $weeks;
            case 'Monthly':
                $months = $checkinDate->diffInMonths($checkoutDate);
                if ($checkinDate->day > $checkoutDate->day) {
                    $months++;
                }
                return $rate->base_price * max(1, $months);
            default:
                return $rate->base_price * $days;
        }
    }

    /**
     * Calculate initial payment (deposit or upfront)
     */
    private function calculateInitialPayment($rate, $totalFee)
    {
        if ($rate->duration_type === 'Monthly') {
            // Deposit is typically one month's rent
            return $rate->base_price;
        } else {
            // For daily/weekly, upfront payment is the full amount
            return $totalFee;
        }
    }

    /**
     * API endpoint to check room availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'checkin_date' => 'required|date',
            'checkout_date' => 'required|date|after:checkin_date',
        ]);

        $availableRooms = $this->getAvailableRooms($request->checkin_date, $request->checkout_date);

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
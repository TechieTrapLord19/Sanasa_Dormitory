<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Rate;
use App\Models\Invoice;
use App\Models\InvoiceUtility;
use App\Models\ElectricReading;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Payment;
use App\Traits\LogsActivity;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    use LogsActivity;
    public const MONTHLY_SECURITY_DEPOSIT = 5000.00;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)

    {

        $query = Booking::with(['tenant', 'secondaryTenant', 'room', 'rate']);

        // Filter by status tab - we'll filter after loading based on effective status
        $statusFilter = $request->get('status', 'All');

        // Map 'Paid' to 'Paid Payment' for backward compatibility
        if ($statusFilter === 'Paid') {
            $statusFilter = 'Paid Payment';
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($subQuery) use ($search) {
                $subQuery->whereHas('tenant', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('secondaryTenant', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('room', function($q) use ($search) {
                $q->where('room_num', 'like', "%{$search}%");
                });
            });
        }

        $allBookings = $query->with('invoices.payments')
            ->orderBy('created_at', 'desc')
            ->orderBy('booking_id', 'desc')
            ->get();

        // Filter by effective status
        $filteredBookings = $allBookings->filter(function($booking) use ($statusFilter) {
            $effectiveStatus = $booking->effective_status;

            switch ($statusFilter) {
                case 'All':
                    return true; // Show all bookings
                case 'Pending Payment':
                    return $effectiveStatus === 'Pending Payment';
                case 'Partial Payment':
                    return $effectiveStatus === 'Partial Payment';
                case 'Paid Payment':
                    return $effectiveStatus === 'Paid Payment';
                case 'Active':
                    return $effectiveStatus === 'Active';
                case 'Completed':
                    return $effectiveStatus === 'Completed';
                case 'Canceled':
                    return $effectiveStatus === 'Canceled';
                default:
                    return true; // Show all by default
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
            'All' => $allBookings->count(),
            'Pending Payment' => 0,
            'Partial Payment' => 0,
            'Paid Payment' => 0,
            'Active' => 0,
            'Completed' => 0,
            'Canceled' => 0,
        ];

        foreach ($allBookings as $booking) {
            $effectiveStatus = $booking->effective_status;

            // Count by effective status
            if (isset($statusCounts[$effectiveStatus])) {
                $statusCounts[$effectiveStatus]++;
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
        $activeBookings = Booking::whereIn('status', ['Pending Payment', 'Active'])
            ->get(['tenant_id', 'secondary_tenant_id']);

        $engagedTenantIds = $activeBookings->pluck('tenant_id')
            ->merge($activeBookings->pluck('secondary_tenant_id'))
            ->filter()
            ->unique();

        $tenants = Tenant::where('status', 'active')
            ->when($engagedTenantIds->isNotEmpty(), function ($query) use ($engagedTenantIds) {
                $query->whereNotIn('tenant_id', $engagedTenantIds);
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        $rooms = Room::all();
        $ratesByDuration = Rate::whereIn('duration_type', ['Daily', 'Weekly', 'Monthly'])
            ->with('utilities')
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
        'tenant_ids' => ['required', 'array', 'min:1'],
        'tenant_ids.*' => [
            'distinct',
            Rule::exists('tenants', 'tenant_id')->where(function ($query) {
                $query->where('status', 'active');
            }),
        ],
        'rate_id' => 'required|exists:rates,rate_id',
        'checkin_date' => 'required|date|after_or_equal:today',
        'stay_length' => 'required|integer|min:1',
    ], [
        'tenant_ids.required' => 'Please select at least one tenant.',
        'tenant_ids.array' => 'Invalid tenant selection.',
        'tenant_ids.min' => 'Please select at least one tenant.',
        'tenant_ids.*.distinct' => 'Duplicate tenants are not allowed.',
    ]);

    $checkinDate = Carbon::parse($validatedData['checkin_date']);
    $stayLength = (int) $validatedData['stay_length'];
    $checkoutDate = (clone $checkinDate)->addDays($stayLength);

    $tenantIds = collect($validatedData['tenant_ids'] ?? [])
        ->filter()
        ->unique()
        ->values();

    if ($tenantIds->isEmpty()) {
        return back()->withErrors(['tenant_ids' => 'Please select at least one tenant.'])->withInput();
    }

    $room = Room::findOrFail($validatedData['room_id']);

    if ($tenantIds->count() > $room->capacity) {
        return back()->withErrors([
            'tenant_ids' => "Room {$room->room_num} can only accommodate {$room->capacity} tenant(s).",
        ])->withInput();
    }

    $conflictingTenants = Booking::conflictingTenantNames($tenantIds->all());
    if (!empty($conflictingTenants)) {
        return back()->withErrors([
            'tenant_ids' => 'The following tenants already have an active or pending booking: ' . implode(', ', $conflictingTenants),
        ])->withInput();
    }

    $primaryTenantId = (int) $tenantIds->shift();
    $secondaryTenantId = $tenantIds->isNotEmpty() ? (int) $tenantIds->shift() : null;

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
            'tenant_id' => $primaryTenantId,
            'secondary_tenant_id' => $secondaryTenantId,
            'rate_id' => $rate->rate_id,
            'recorded_by_user_id' => Auth::id(),
            'checkin_date' => $validatedData['checkin_date'],
            'checkout_date' => $checkoutDate->toDateString(),
            'total_calculated_fee' => $totalFee,
            'status' => 'Pending Payment', // Start as Pending Payment
        ]);
        $room->update(['status' => 'pending']);

        // Create invoices based on booking type
        $months = max(1, (int) ceil($stayLength / 30));
        $weeks = max(1, (int) ceil($stayLength / 7));
        $days = max(1, $stayLength);

        if ($durationType === 'Monthly') {
            // Monthly: Create TWO separate invoices

            // Load rate with utilities
            $rate->load('utilities');

            // Calculate full months and remaining days
            $fullMonths = (int) floor($stayLength / 30);
            $remainingDays = $stayLength - ($fullMonths * 30);

            // ============================================
            // RENT SUBTOTAL: ONLY RENT, NO UTILITIES
            // ============================================
            // Full months: monthly rate base_price (rent only)
            $rentSubtotal = $rate->base_price * $fullMonths;

            // Remaining days: daily rate base_price (rent only)
            // NOTE: If daily rate includes utilities in base_price, we need to extract rent-only portion
            // Utilities are NOT included in rent_subtotal
            if ($remainingDays > 0) {
                $dailyRate = Rate::where('duration_type', 'Daily')->with('utilities')->first();
                if ($dailyRate) {
                    // Calculate daily rent-only amount
                    // If daily rate has utilities, subtract them from base_price to get rent-only
                    $dailyRentOnly = $dailyRate->base_price;
                    $dailyUtilities = $dailyRate->utilities->keyBy('name');
                    // Subtract ALL utilities from daily rate to get rent-only
                    foreach ($dailyUtilities as $utility) {
                        $dailyRentOnly -= ($utility->price / 30); // Convert monthly utility to daily
                    }
                    $rentSubtotal += $dailyRentOnly * $remainingDays;
                } else {
                    // Fallback: prorate monthly rate (rent only)
                    $dailyPrice = $rate->base_price / 30;
                    $rentSubtotal += $dailyPrice * $remainingDays;
                }
            }

            // ============================================
            // UTILITIES: CALCULATED SEPARATELY FOR ALL UTILITIES
            // ============================================
            // IMPORTANT: Utilities are charged ONLY for full months, NOT for remaining days
            // Utilities are NOT added to rent_subtotal, they are separate line items
            $utilitiesTotal = 0.00;
            $utilityFees = []; // Store utility fees for invoice_utilities table

            // Loop through ALL utilities from the rate
            foreach ($rate->utilities as $utility) {
                $utilityFee = 0.00;
                if ($fullMonths > 0) {
                    $utilityFee = $utility->price * $fullMonths; // Only full months
                }

                // Ensure at least 1 month if stayLength > 0 but less than 30
                if ($stayLength > 0 && $fullMonths == 0) {
                    // For stays less than 30 days, charge 1 month
                    $utilityFee = $utility->price;
                }

                if ($utilityFee > 0) {
                    $utilitiesTotal += $utilityFee;
                    $utilityFees[] = [
                        'utility_name' => $utility->name,
                        'amount' => $utilityFee,
                    ];
                }
            }

            // Ensure at least 1 month if stayLength > 0 but less than 30
            if ($stayLength > 0 && $fullMonths == 0) {
                $rentSubtotal = $rate->base_price;
            }

            $rentAndUtilitiesTotal = $rentSubtotal + $utilitiesTotal;

            // Create invoice
            $invoice = Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => $rentSubtotal,
                'utility_electricity_fee' => 0.00, // No electricity on initial invoice
                'total_due' => $rentAndUtilitiesTotal,
                'is_paid' => false,
            ]);

            // Store utilities in invoice_utilities table
            foreach ($utilityFees as $utilityFee) {
                InvoiceUtility::create([
                    'invoice_id' => $invoice->invoice_id,
                    'utility_name' => $utilityFee['utility_name'],
                    'amount' => $utilityFee['amount'],
                ]);
            }

            // Invoice 2: Security Deposit
            Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => 0,
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
                'utility_electricity_fee' => 0,
                'total_due' => $rentSubtotal,
                'is_paid' => false,
            ]);
        }

        DB::commit();

        $booking->load('tenant', 'secondaryTenant', 'room');
        $tenantSummary = $booking->tenant_summary;
        $room = $booking->room;
        $this->logActivity(
            'Created Booking',
            "Created booking #{$booking->booking_id} for tenant(s) {$tenantSummary} in room {$room->room_num} (Check-in: {$booking->checkin_date}, Check-out: {$booking->checkout_date})",
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
        $booking = Booking::with(['tenant', 'secondaryTenant', 'room', 'rate', 'recordedBy', 'invoices.payments', 'invoices.invoiceUtilities', 'refunds.payment', 'refunds.refundedBy'])
                          ->findOrFail($id);

        $stayLengthDays = max(1, $booking->checkin_date->diffInDays($booking->checkout_date));

        // Determine appropriate rate based on current stay length (not original booking rate)
        // This handles cases where Daily/Weekly bookings are extended to Monthly
        if ($stayLengthDays >= 30) {
            $rate = Rate::where('duration_type', 'Monthly')->with('utilities')->first();
            if (!$rate) {
                // Fallback to original booking rate if Monthly rate doesn't exist
                $rate = Rate::with('utilities')->findOrFail($booking->rate_id);
            }
        } elseif ($stayLengthDays >= 7) {
            $rate = Rate::where('duration_type', 'Weekly')->with('utilities')->first();
            if (!$rate) {
                // Fallback to original booking rate if Weekly rate doesn't exist
                $rate = Rate::with('utilities')->findOrFail($booking->rate_id);
            }
        } else {
            $rate = Rate::where('duration_type', 'Daily')->with('utilities')->first();
            if (!$rate) {
                // Fallback to original booking rate if Daily rate doesn't exist
                $rate = Rate::with('utilities')->findOrFail($booking->rate_id);
            }
        }

        $chargeSummary = $this->buildChargeSummary($rate, $stayLengthDays);

        // Calculate actual total due from unpaid invoices (not theoretical)
        $unpaidInvoices = $booking->invoices->filter(function($inv) {
            $paymentsSum = $inv->payments->sum('amount');
            return $paymentsSum < $inv->total_due;
        });
        $actualTotalDue = $unpaidInvoices->sum('total_due') - $unpaidInvoices->sum(function($inv) {
            return $inv->payments->sum('amount');
        });

        // Check if security deposit is already paid
        $securityDepositInvoice = $booking->invoices->first(function($inv) {
            $hasUtilities = $inv->invoiceUtilities && $inv->invoiceUtilities->count() > 0;
            return $inv->rent_subtotal == 0 &&
                   !$hasUtilities &&
                   $inv->utility_electricity_fee > 0;
        });

        $securityDepositPaid = 0;
        if ($securityDepositInvoice) {
            $securityDepositPaid = $securityDepositInvoice->payments->sum('amount');
            // If security deposit is fully paid, remove it from chargeSummary
            if ($securityDepositPaid >= $securityDepositInvoice->total_due) {
                $chargeSummary['security_deposit'] = 0;
            } else {
                // Partially paid, show remaining amount
                $chargeSummary['security_deposit'] = $securityDepositInvoice->total_due - $securityDepositPaid;
            }
        }

        // Calculate actual breakdown from unpaid invoices
        // Show full amounts from unpaid invoices (total due already accounts for payments)
        $actualRentTotal = 0;
        $actualUtilitiesBreakdown = [];

        foreach ($unpaidInvoices as $invoice) {
            $invoicePayments = $invoice->payments->sum('amount');
            $invoiceRemaining = $invoice->total_due - $invoicePayments;

            // Only include invoices that have remaining balance
            if ($invoiceRemaining > 0) {
                // Add rent (full amount from invoice, payments are accounted in total due)
                $actualRentTotal += $invoice->rent_subtotal;

                // Add utilities from invoice_utilities
                if ($invoice->invoiceUtilities) {
                    foreach ($invoice->invoiceUtilities as $invoiceUtility) {
                        if (!isset($actualUtilitiesBreakdown[$invoiceUtility->utility_name])) {
                            $actualUtilitiesBreakdown[$invoiceUtility->utility_name] = 0;
                        }
                        $actualUtilitiesBreakdown[$invoiceUtility->utility_name] += $invoiceUtility->amount;
                    }
                }
            }
        }

        // Update chargeSummary with actual values from invoices
        $chargeSummary['rate_total'] = $actualRentTotal;

        // Convert utilities breakdown to array format
        $chargeSummary['utilities'] = [];
        foreach ($actualUtilitiesBreakdown as $utilityName => $amount) {
            $chargeSummary['utilities'][] = [
                'name' => $utilityName,
                'amount' => $amount,
            ];
        }

        // Update chargeSummary with actual total due
        $chargeSummary['total_due'] = max(0, $actualTotalDue);

        // Pass flag to view to determine if electricity button should show
        $isMonthlyStay = $stayLengthDays >= 30 || ($securityDepositInvoice !== null);

        // Get all payments for this booking (directly from payments table)
        $allPayments = Payment::where('booking_id', $booking->booking_id)
            ->with('refunds')
            ->get();

        // Get the most recent reading for electricity invoice generation
        // This will be used as the "last reading" to calculate usage from
        $lastReading = ElectricReading::where('room_id', $booking->room_id)
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_id', 'desc')
            ->first();

        // Get the electricity rate from session
        $electricityRate = session('electricity_rate_per_kwh', null);

        return view('contents.bookings-show', compact('booking', 'chargeSummary', 'stayLengthDays', 'allPayments', 'lastReading', 'isMonthlyStay', 'unpaidInvoices', 'electricityRate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $booking = Booking::with(['tenant', 'secondaryTenant', 'room', 'rate'])->findOrFail($id);
        $tenants = Tenant::where('status', 'active')->orderBy('last_name')->get();
        $rooms = Room::all();
        $ratesByDuration = Rate::whereIn('duration_type', ['Daily', 'Weekly', 'Monthly'])
            ->with('utilities')
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

                if ($newRoom->status !== 'maintenance') {
                    if ($booking->status === 'Active') {
                        $newRoom->update(['status' => 'occupied']);
                    } elseif ($booking->status === 'Pending Payment') {
                        $newRoom->update(['status' => 'pending']);
                    } else {
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

            $tenantSummary = $booking->tenant_summary;
            $room = $booking->room;
            $description = "Updated booking #{$booking->booking_id} for tenant(s) {$tenantSummary}";
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

            $tenantSummary = $booking->tenant_summary;
            $room = $booking->room;
            $this->logActivity(
                'Canceled Booking',
                "Canceled booking #{$booking->booking_id} for tenant(s) {$tenantSummary} in room {$room->room_num}. Reason: {$request->cancellation_reason}",
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
    $booking = Booking::with([
        'tenant',
        'secondaryTenant',
        'room',
        'invoices.payments',
        'invoices.invoiceUtilities'
    ])->findOrFail($id);

    // Allow check-in if booking is not already Active, Completed, or Canceled
    if (in_array($booking->status, ['Active', 'Completed', 'Canceled'])) {
        return back()->withErrors(['error' => 'Cannot check in. Booking is already ' . $booking->status . '.']);
    }

    // Separate invoices into Rent+Utilities and Security Deposit
    $rentUtilitiesInvoices = $booking->invoices->filter(function($invoice) {
        // Check if invoice has rent or utilities (from invoice_utilities table)
        $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
        return $invoice->rent_subtotal > 0 || $hasUtilities;
    });

    $securityDepositInvoice = $booking->invoices->first(function($invoice) {
        // Security deposit invoice: no rent, no utilities, but has electricity fee (used for security deposit)
        $hasUtilities = $invoice->invoiceUtilities && $invoice->invoiceUtilities->count() > 0;
        return $invoice->rent_subtotal == 0 &&
               !$hasUtilities &&
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

        $room = $booking->room;
        $this->logActivity(
            'Checked In Tenant',
            "Checked in tenant(s) {$booking->tenant_summary} for booking #{$booking->booking_id} in room {$room->room_num}",
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
        $booking = Booking::with(['tenant', 'secondaryTenant', 'rate', 'room'])->findOrFail($id);

        if ($booking->status !== 'Active') {
            return back()->withErrors(['error' => 'Only active bookings can generate electricity invoices.']);
        }

        // Get the most recent reading for this room (this will be used as the base for calculation)
        $lastReading = ElectricReading::where('room_id', $booking->room_id)
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_id', 'desc')
            ->first();

        $validated = $request->validate([
            'electricity_rate_per_kwh' => ['required', 'numeric', 'min:0'],
            'new_meter_value_kwh' => ['required', 'numeric', 'min:0'],
        ], [
            'electricity_rate_per_kwh.required' => 'Electricity rate per kWh is required.',
            'electricity_rate_per_kwh.numeric' => 'Electricity rate must be a number.',
            'electricity_rate_per_kwh.min' => 'Electricity rate must be at least 0.',
            'new_meter_value_kwh.required' => 'New meter reading is required.',
            'new_meter_value_kwh.numeric' => 'New meter reading must be a number.',
            'new_meter_value_kwh.min' => 'New meter reading must be at least 0.',
        ]);

        DB::beginTransaction();
        try {
            // Calculate electricity usage: new meter reading - last reading
            $newMeterValue = (float) $validated['new_meter_value_kwh'];
            $lastReadingValue = $lastReading ? (float) $lastReading->meter_value_kwh : 0.0;
            $kwhUsed = max(0, $newMeterValue - $lastReadingValue);

            // Create a new ElectricReading and mark it billed (using today's date)
            ElectricReading::create([
                'room_id' => $booking->room_id,
                'reading_date' => now()->toDateString(),
                'meter_value_kwh' => $newMeterValue,
                'is_billed' => true,
            ]);

            $ratePerKwh = (float) $validated['electricity_rate_per_kwh'];
            $electricityFee = $kwhUsed * $ratePerKwh;

            // Create separate electricity invoice
            Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => 0,
                'utility_electricity_fee' => $electricityFee,
                'total_due' => $electricityFee,
                'is_paid' => false,
            ]);

            DB::commit();

            $room = $booking->room;
            $this->logActivity(
                'Generated Electricity Invoice',
                "Generated electricity invoice for booking #{$booking->booking_id} (Tenant(s): {$booking->tenant_summary}, Room: {$room->room_num}, Amount: ₱" . number_format($electricityFee, 2) . ")",
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
        $booking = Booking::with(['tenant', 'secondaryTenant', 'rate', 'room', 'invoices.invoiceUtilities'])->findOrFail($id);

        if ($booking->status !== 'Active') {
            return back()->withErrors(['error' => 'Only active bookings can be renewed.']);
        }

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

            // Load rates based on extension period (not original booking rate)
            $monthlyRate = Rate::where('duration_type', 'Monthly')->with('utilities')->first();
            $dailyRate = Rate::where('duration_type', 'Daily')->with('utilities')->first();

            // Calculate rent and utilities for next period
            $rentSubtotal = 0;
            $utilitiesTotal = 0.00;
            $utilityFees = []; // Store utility fees for invoice_utilities table

            if ($actualExtensionDays >= 30) {
                // Extension is 30+ days: Split into full months + remaining days
                $fullMonths = (int) floor($actualExtensionDays / 30);
                $remainingDays = $actualExtensionDays - ($fullMonths * 30);

                if ($monthlyRate) {
                    // Full months: Monthly rate (rent + utilities)
                    $rentSubtotal = $monthlyRate->base_price * $fullMonths;

                    // Calculate utilities for full months
                    foreach ($monthlyRate->utilities as $utility) {
                        $utilityFee = $utility->price * $fullMonths;
                        if ($utilityFee > 0) {
                            $utilitiesTotal += $utilityFee;
                            $utilityFees[] = [
                                'utility_name' => $utility->name,
                                'amount' => $utilityFee,
                            ];
                        }
                    }
                } else {
                    // Fallback: if no monthly rate, use daily rate for all days
                    if ($dailyRate) {
                        $rentSubtotal = $dailyRate->base_price * $actualExtensionDays;
                    }
                }

                // Remaining days: Daily rate (rent only, utilities included in daily rate base_price)
                if ($remainingDays > 0 && $dailyRate) {
                    // Calculate daily rent-only amount
                    // If daily rate has utilities, subtract them from base_price to get rent-only
                    $dailyRentOnly = $dailyRate->base_price;
                    if ($dailyRate->utilities && $dailyRate->utilities->count() > 0) {
                        // Subtract ALL utilities from daily rate to get rent-only
                        foreach ($dailyRate->utilities as $utility) {
                            $dailyRentOnly -= ($utility->price / 30); // Convert monthly utility to daily
                        }
                    }
                    $rentSubtotal += $dailyRentOnly * $remainingDays;
                }
            } else {
                // Extension is less than 30 days: Use Daily rate for entire period
                if ($dailyRate) {
                    $rentSubtotal = $dailyRate->base_price * $actualExtensionDays;
                    // No separate utilities for daily rate (included in base_price)
                } else {
                    // Fallback: if no daily rate exists, cannot calculate
                    DB::rollBack();
                    return back()->withErrors(['error' => 'Daily rate not found. Please configure rates in the system.'])->withInput();
                }
            }

            // Create renewal invoice (rent + utilities only, NO electricity)
            $totalDue = $rentSubtotal + $utilitiesTotal;

            $invoice = Invoice::create([
                'booking_id' => $booking->booking_id,
                'date_generated' => now()->toDateString(),
                'rent_subtotal' => $rentSubtotal,
                'utility_electricity_fee' => 0.00, // Electricity is separate
                'total_due' => $totalDue,
                'is_paid' => false,
            ]);

            // Store utilities in invoice_utilities table
            foreach ($utilityFees as $utilityFee) {
                InvoiceUtility::create([
                    'invoice_id' => $invoice->invoice_id,
                    'utility_name' => $utilityFee['utility_name'],
                    'amount' => $utilityFee['amount'],
                ]);
            }

            // Check if security deposit invoice is needed
            // If extension is 30+ days (Monthly) AND original booking was Daily/Weekly (no security deposit exists)
            if ($actualExtensionDays >= 30) {
                // Check if booking already has a security deposit invoice
                $hasSecurityDeposit = $booking->invoices->first(function($inv) {
                    // Security deposit invoice: no rent, no utilities, but has electricity fee (used for security deposit)
                    $hasUtilities = $inv->invoiceUtilities && $inv->invoiceUtilities->count() > 0;
                    return $inv->rent_subtotal == 0 &&
                           !$hasUtilities &&
                           $inv->utility_electricity_fee > 0;
                });

                // If no security deposit invoice exists, create one
                if (!$hasSecurityDeposit) {
                    Invoice::create([
                        'booking_id' => $booking->booking_id,
                        'date_generated' => now()->toDateString(),
                        'rent_subtotal' => 0,
                        'utility_electricity_fee' => self::MONTHLY_SECURITY_DEPOSIT,
                        'total_due' => self::MONTHLY_SECURITY_DEPOSIT,
                        'is_paid' => false,
                    ]);
                }
            }

            // Extend booking checkout_date by actual extension days
            $newCheckoutDate = (clone $booking->checkout_date)->addDays($actualExtensionDays);
            $booking->update([
                'checkout_date' => $newCheckoutDate->toDateString(),
            ]);

            DB::commit();

            $room = $booking->room;
            $description = "Generated renewal invoice for booking #{$booking->booking_id} (Tenant(s): {$booking->tenant_summary}, Room: {$room->room_num}, Extended by {$actualExtensionDays} days";
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

            $room = $booking->room;
            $this->logActivity(
                'Checked Out Tenant',
            "Checked out tenant(s) {$booking->tenant_summary} from booking #{$booking->booking_id} in room {$room->room_num}",
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
        $utilities = []; // Array to store all utilities dynamically
        $note = null;
        $units = 1;

        // Load rate with utilities
        $rate->load('utilities');

        if ($duration === 'Monthly') {
            // Calculate full months and remaining days
            $fullMonths = (int) floor($days / 30);
            $remainingDays = $days - ($fullMonths * 30);

            // ============================================
            // RATE TOTAL: ONLY RENT, NO UTILITIES
            // ============================================
            // Full months: monthly rate base_price (rent only)
            $rateTotal = $rate->base_price * $fullMonths;

            // Remaining days: daily rate base_price (rent only)
            // NOTE: If daily rate includes utilities in base_price, we need to extract rent-only portion
            // Utilities are NOT included in rate_total
            if ($remainingDays > 0) {
                $dailyRate = Rate::where('duration_type', 'Daily')->with('utilities')->first();
                if ($dailyRate) {
                    // Calculate daily rent-only amount
                    // If daily rate has utilities, subtract them from base_price to get rent-only
                    $dailyRentOnly = $dailyRate->base_price;
                    $dailyUtilities = $dailyRate->utilities->keyBy('name');
                    // Subtract ALL utilities from daily rate to get rent-only
                    foreach ($dailyUtilities as $utility) {
                        $dailyRentOnly -= ($utility->price / 30); // Convert monthly utility to daily
                    }
                    $rateTotal += $dailyRentOnly * $remainingDays;
                } else {
                    // Fallback: prorate monthly rate (rent only)
                    $dailyPrice = $rate->base_price / 30;
                    $rateTotal += $dailyPrice * $remainingDays;
                }
            }

            // ============================================
            // UTILITIES: CALCULATED SEPARATELY FOR ALL UTILITIES
            // ============================================
            // IMPORTANT: Utilities are charged ONLY for full months, NOT for remaining days
            // Utilities are NOT added to rate_total, they are separate line items
            $utilitiesTotal = 0.00;

            // Loop through ALL utilities from the rate
            foreach ($rate->utilities as $utility) {
                $utilityFee = 0.00;
                if ($fullMonths > 0) {
                    $utilityFee = $utility->price * $fullMonths; // Only full months
                }

                // Ensure at least 1 month if days > 0 but less than 30
                if ($days > 0 && $fullMonths == 0) {
                    // For stays less than 30 days, charge 1 month
                    $utilityFee = $utility->price;
                }

                if ($utilityFee > 0) {
                    $utilitiesTotal += $utilityFee;
                    $utilities[] = [
                        'name' => $utility->name,
                        'amount' => $utilityFee,
                    ];
                }
            }

            // Ensure at least 1 month if days > 0 but less than 30
            if ($days > 0 && $fullMonths == 0) {
                $rateTotal = $rate->base_price;
            }

            $units = $fullMonths > 0 ? $fullMonths : 1;
            if ($remainingDays > 0) {
                $units .= " month(s) + {$remainingDays} day(s)";
            } else {
                $units .= " month(s)";
            }

            $securityDeposit = self::MONTHLY_SECURITY_DEPOSIT;
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

        $totalDue = $rateTotal + $securityDeposit + array_sum(array_column($utilities, 'amount'));

        return [
            'duration_type' => $duration,
            'units' => $units,
            'rate_total' => $rateTotal,
            'security_deposit' => $securityDeposit,
            'utilities' => $utilities, // All utilities dynamically
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

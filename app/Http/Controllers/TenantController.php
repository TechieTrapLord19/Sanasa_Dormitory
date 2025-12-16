<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Traits\LogsActivity;

class TenantController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tenant::query();

        // Search by name or contact
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_num', 'like', "%{$search}%")
                  ->orWhere('emer_contact_num', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $tenants = $query->with(['currentBooking.room'])
                         ->withCount('bookings')
                         ->orderBy('created_at', 'desc')
                         ->orderBy('tenant_id', 'desc')
                         ->paginate($perPage)
                         ->withQueryString();

        // Get counts for status indicators
        $statusCounts = [
            'active' => Tenant::where('status', 'active')->count(),
            'inactive' => Tenant::where('status', 'inactive')->count(),
            'total' => Tenant::count(),
        ];

        $activeStatus = $request->input('status', 'all');
        $searchTerm = $request->input('search', '');

        return view('contents.tenants', compact('tenants', 'statusCounts', 'activeStatus', 'searchTerm', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Calculate the maximum birth date (must be at least 12 years old)
        $maxBirthDate = now()->subYears(12)->format('Y-m-d');

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'birth_date' => ['required', 'date', 'before_or_equal:' . $maxBirthDate],
            'id_document' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'contact_num' => ['required', 'string', 'max:20'],
            'emer_contact_num' => ['required', 'string', 'max:20'],
            'emer_contact_name' => ['required', 'string', 'max:255'],
            'status' => 'required|in:active,inactive',
        ], [
            'birth_date.required' => 'Birth date is required.',
            'birth_date.before_or_equal' => 'Tenant must be at least 12 years old.',
            'id_document.required' => 'ID document image is required for verification.',
            'id_document.image' => 'ID document must be an image file.',
            'id_document.mimes' => 'ID document must be a JPEG, PNG, JPG, or GIF file.',
            'id_document.max' => 'ID document image must not exceed 5MB.',
            'contact_num.required' => 'Contact number is required.',
            'emer_contact_num.required' => 'Emergency contact number is required.',
            'emer_contact_name.required' => 'Emergency contact name is required.',
        ]);

        // Handle ID document image upload
        if ($request->hasFile('id_document')) {
            $image = $request->file('id_document');
            $filename = 'id_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/id_documents'), $filename);
            $validatedData['id_document'] = 'uploads/id_documents/' . $filename;
        }

        $tenant = Tenant::create($validatedData);

        $this->logActivity(
            'Created Tenant',
            "Created tenant {$tenant->full_name} (Email: {$tenant->email}, Status: {$tenant->status})",
            $tenant
        );

        if ($request->expectsJson()) {
            $tenant->refresh();
            $tenant->append('full_name');

            return response()->json([
                'tenant_id' => $tenant->tenant_id,
                'first_name' => $tenant->first_name,
                'last_name' => $tenant->last_name,
                'full_name' => $tenant->full_name,
                'email' => $tenant->email,
                'contact_num' => $tenant->contact_num,
                'status' => $tenant->status,
            ], 201);
        }

        return redirect()->route('tenants')->with('success', 'Tenant created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant = Tenant::with([
                'bookings.room',
                'bookings.rate',
                'bookings.invoices.payments',
                'payments.booking.room',
                'payments.invoice',
                'payments.collectedBy'
            ])
            ->withCount('bookings')
            ->findOrFail($id);

        // Get payments sorted by date (newest first)
        $payments = $tenant->payments()->with(['booking.room', 'invoice', 'collectedBy'])
                           ->orderBy('date_received', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->get();

        // Calculate total paid
        $totalPaid = $payments->sum('amount');

        return view('contents.tenants-show', compact('tenant', 'payments', 'totalPaid'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('contents.tenants-edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Calculate the maximum birth date (must be at least 12 years old)
        $maxBirthDate = now()->subYears(12)->format('Y-m-d');

        $tenant = Tenant::findOrFail($id);

        // ID document is optional on update if tenant already has one
        $idDocumentRules = $tenant->id_document
            ? ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120']
            : ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'];

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'birth_date' => ['required', 'date', 'before_or_equal:' . $maxBirthDate],
            'id_document' => $idDocumentRules,
            'contact_num' => ['required', 'string', 'max:20'],
            'emer_contact_num' => ['required', 'string', 'max:20'],
            'emer_contact_name' => ['required', 'string', 'max:255'],
            'status' => 'required|in:active,inactive',
        ], [
            'birth_date.required' => 'Birth date is required.',
            'birth_date.before_or_equal' => 'Tenant must be at least 12 years old.',
            'id_document.required' => 'ID document image is required for verification.',
            'id_document.image' => 'ID document must be an image file.',
            'id_document.mimes' => 'ID document must be a JPEG, PNG, JPG, or GIF file.',
            'id_document.max' => 'ID document image must not exceed 5MB.',
            'contact_num.required' => 'Contact number is required.',
            'emer_contact_num.required' => 'Emergency contact number is required.',
            'emer_contact_name.required' => 'Emergency contact name is required.',
        ]);

        // Handle ID document image upload
        if ($request->hasFile('id_document')) {
            // Delete old image if exists
            if ($tenant->id_document && file_exists(public_path($tenant->id_document))) {
                unlink(public_path($tenant->id_document));
            }

            $image = $request->file('id_document');
            $filename = 'id_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/id_documents'), $filename);
            $validatedData['id_document'] = 'uploads/id_documents/' . $filename;
        } else {
            // Keep existing id_document if no new file uploaded
            unset($validatedData['id_document']);
        }

        $oldStatus = $tenant->status;

        // Check if tenant has active booking before allowing status change to inactive
        if ($validatedData['status'] === 'inactive' && $oldStatus === 'active') {
            $hasActiveBooking = $tenant->bookings()
                ->where('status', 'Active')
                ->exists();

            if ($hasActiveBooking) {
                return redirect()->back()
                    ->withErrors(['status' => 'Cannot set tenant to inactive while they have an active booking. Please check out the tenant first.'])
                    ->withInput();
            }
        }

        $tenant->update($validatedData);

        $description = "Updated tenant {$tenant->full_name}";
        if ($oldStatus !== $tenant->status) {
            $description .= " - Status changed from {$oldStatus} to {$tenant->status}";
        }
        $this->logActivity('Updated Tenant', $description, $tenant);

        return redirect()->route('tenants.show', $tenant->tenant_id)
                        ->with('success', 'Tenant updated successfully!')
                        ->withInput();
    }

    /**
     * Archive a tenant (set status to inactive)
     */
    public function archive(string $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Check if tenant has active booking
        $hasActiveBooking = $tenant->bookings()
            ->where('status', 'Active')
            ->exists();

        if ($hasActiveBooking) {
            return redirect()->back()
                ->with('error', 'Cannot archive tenant while they have an active booking. Please check out the tenant first.');
        }

        $tenant->update(['status' => 'inactive']);

        $this->logActivity(
            'Archived Tenant',
            "Archived tenant {$tenant->full_name}",
            $tenant
        );

        return redirect()->route('tenants')
                        ->with('success', 'Tenant archived successfully!');
    }

    /**
     * Activate a tenant (set status to active)
     */
    public function activate(string $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => 'active']);

        $this->logActivity(
            'Activated Tenant',
            "Activated tenant {$tenant->full_name}",
            $tenant
        );

        return redirect()->route('tenants')
                        ->with('success', 'Tenant activated successfully!');
    }
}

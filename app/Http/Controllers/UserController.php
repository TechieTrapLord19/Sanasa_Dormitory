<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Traits\ChecksRole;

class UserController extends Controller
{
    use ChecksRole;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->requireOwner();

        $query = User::query();

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $users = $query->orderBy('last_name')
                      ->orderBy('first_name')
                      ->paginate($perPage)
                      ->withQueryString();

        // Get counts for role indicators
        $roleCounts = [
            'owner' => User::where('role', 'owner')->count(),
            'caretaker' => User::where('role', 'caretaker')->count(),
            'total' => User::count(),
        ];

        $selectedRole = $request->input('role', 'all');
        $searchTerm = $request->input('search', '');

        return view('contents.user-management', compact('users', 'roleCounts', 'selectedRole', 'searchTerm', 'perPage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->requireOwner();

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:owner,caretaker',
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Role is required.',
            'role.in' => 'Role must be either owner or caretaker.',
        ]);

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'] ?? null,
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'birth_date' => $validatedData['birth_date'] ?? null,
            'address' => $validatedData['address'] ?? null,
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('user-management')
                        ->with('success', 'User created successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->requireOwner();

        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'password' => 'nullable|confirmed|min:8',
            'role' => 'required|in:owner,caretaker',
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'role.required' => 'Role is required.',
            'role.in' => 'Role must be either owner or caretaker.',
        ]);

        $updateData = [
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'] ?? null,
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'birth_date' => $validatedData['birth_date'] ?? null,
            'address' => $validatedData['address'] ?? null,
            'role' => $validatedData['role'],
        ];

        // Only update password if provided
        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($updateData);

        return redirect()->route('user-management')
                        ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->requireOwner();

        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // Prevent deleting the currently logged-in user
        if ($user->user_id === $currentUser->user_id) {
            return redirect()->route('user-management')
                            ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()->route('user-management')
                        ->with('success', 'User deleted successfully!');
    }
}

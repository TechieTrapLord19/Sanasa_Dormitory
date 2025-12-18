<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class ExpenseController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::with('recordedBy');

        // Sorting
        $sortBy = $request->input('sort_by', 'expense_date');
        $sortDir = $request->input('sort_dir', 'desc');
        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Date filter handling
        $dateFilter = $request->input('date_filter', 'this_month');
        $dateFrom = '';
        $dateTo = '';

        switch ($dateFilter) {
            case 'today':
                $dateFrom = now()->toDateString();
                $dateTo = now()->toDateString();
                break;
            case 'this_week':
                $dateFrom = now()->startOfWeek()->toDateString();
                $dateTo = now()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $dateFrom = now()->startOfMonth()->toDateString();
                $dateTo = now()->endOfMonth()->toDateString();
                break;
            case 'this_quarter':
                $dateFrom = now()->firstOfQuarter()->toDateString();
                $dateTo = now()->lastOfQuarter()->toDateString();
                break;
            case 'this_year':
                $dateFrom = now()->startOfYear()->toDateString();
                $dateTo = now()->endOfYear()->toDateString();
                break;
            case 'custom':
                $dateFrom = $request->input('date_from', '');
                $dateTo = $request->input('date_to', '');
                break;
            case 'all':
            default:
                // No date filtering
                break;
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('expense_date', [$dateFrom, $dateTo]);
        }

        // Apply sorting
        $allowedSortColumns = ['expense_id', 'category', 'amount', 'expense_date', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns, true)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('expense_date', 'desc');
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $expenses = $query->paginate($perPage)->withQueryString();

        // Calculate totals for current filter
        $totalsQuery = Expense::query();
        if ($request->filled('category')) {
            $totalsQuery->where('category', $request->category);
        }
        if ($dateFrom && $dateTo) {
            $totalsQuery->whereBetween('expense_date', [$dateFrom, $dateTo]);
        }

        $totalExpenses = $totalsQuery->sum('amount');
        $expensesByCategory = Expense::query()
            ->when($dateFrom && $dateTo, fn($q) => $q->whereBetween('expense_date', [$dateFrom, $dateTo]))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // Get distinct asset names for suggestions
        $assetNames = Asset::distinct()->orderBy('name')->pluck('name')->toArray();

        return view('contents.expenses', [
            'expenses' => $expenses,
            'categories' => Expense::getCategories(),
            'assetNames' => $assetNames,
            'totalExpenses' => $totalExpenses,
            'expensesByCategory' => $expensesByCategory,
            'dateFilter' => $dateFilter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'asset_type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense = Expense::create([
            ...$validated,
            'recorded_by_user_id' => Auth::id(),
        ]);

        // If category is Asset, automatically create a new asset in inventory
        if ($validated['category'] === 'Asset' && !empty($validated['asset_type'])) {
            $asset = Asset::create([
                'name' => $validated['asset_type'],
                'room_id' => null, // Storage
                'condition' => 'Good',
                'date_acquired' => $validated['expense_date'],
            ]);

            $this->logActivity(
                'Added Asset from Expense',
                "Added new asset '{$validated['asset_type']}' to Storage from expense record",
                $asset
            );
        }

        $this->logActivity(
            'Recorded Expense',
            "Recorded expense of ₱" . number_format($expense->amount, 2) . " for {$expense->category}" . ($expense->asset_type ? ": {$expense->asset_type}" : ""),
            $expense
        );

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully.' . ($validated['category'] === 'Asset' ? ' Asset added to inventory.' : ''));
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'asset_type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldAmount = $expense->amount;
        $expense->update($validated);

        $this->logActivity(
            'Updated Expense',
            "Updated expense #{$expense->expense_id} - Amount: ₱" . number_format($oldAmount, 2) . " → ₱" . number_format($expense->amount, 2),
            $expense
        );

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense.
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        
        $this->logActivity(
            'Deleted Expense',
            "Deleted expense #{$expense->expense_id} - ₱" . number_format($expense->amount, 2) . " for {$expense->category}",
            $expense
        );

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}

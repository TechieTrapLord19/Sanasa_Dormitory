@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
<style>
    .expenses-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .expenses-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .add-expense-btn {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
        cursor: pointer;
    }

    .add-expense-btn:hover {
        background-color: #021d47;
        color: white;
    }

    /* Summary Cards */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .summary-card-label {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .summary-card-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ef4444;
    }

    .summary-card-value.positive { color: #22c55e; }

    /* Filter Styles */
    .expenses-filters {
        background-color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
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

    .filter-select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
        min-width: 150px;
    }

    .filter-select:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .filter-btn-clear {
        background-color: white;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 0.45rem 1rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .filter-btn-clear:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Table Styles */
    .expenses-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .expenses-table {
        width: 100%;
        border-collapse: collapse;
    }

    .expenses-table thead {
        background-color: #f7fafc;
    }

    .expenses-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .expenses-table td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.875rem;
        color: #4a5568;
    }

    .expenses-table tr:hover {
        background-color: #f8fafc;
    }

    .amount-col {
        font-weight: 600;
        color: #ef4444;
    }

    .category-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background-color: #fee2e2;
        color: #dc2626;
    }


    .btn-action {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background-color: #e0e7ff;
        color: #4f46e5;
    }

    .btn-edit:hover {
        background-color: #c7d2fe;
    }

    .btn-delete {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background-color: #fecaca;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }

    .modal-title {
        font-weight: 700;
        color: #03255b;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .modal-footer {
        border-top: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }

    .btn-primary {
        background-color: #03255b;
        border-color: #03255b;
    }

    .btn-primary:hover {
        background-color: #021d47;
        border-color: #021d47;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background-color: white;
        border-top: 1px solid #e2e8f0;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="expenses-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="expenses-title"><i class="bi bi-wallet2 me-2"></i>Expenses</h1>
        <button class="add-expense-btn" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="bi bi-plus-lg"></i> Add Expense
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-label">Total Expenses</div>
            <div class="summary-card-value">₱{{ number_format($totalExpenses, 2) }}</div>
        </div>
        @foreach($expensesByCategory as $cat => $total)
        <div class="summary-card">
            <div class="summary-card-label">{{ $cat }}</div>
            <div class="summary-card-value">₱{{ number_format($total, 2) }}</div>
        </div>
        @endforeach
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('expenses.index') }}" class="expenses-filters">
        <div class="filter-group">
            <label class="filter-label">Category:</label>
            <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <label class="filter-label ms-3">Date:</label>
            <select name="date_filter" class="filter-select" onchange="this.form.submit()">
                <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_quarter" {{ $dateFilter == 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                <option value="this_year" {{ $dateFilter == 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="all" {{ $dateFilter == 'all' ? 'selected' : '' }}>All Time</option>
            </select>

            <a href="{{ route('expenses.index') }}" class="filter-btn-clear ms-2">Clear</a>
        </div>
    </form>

    <!-- Table -->
    <div class="expenses-table-container">
        <table class="expenses-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Receipt #</th>
                    <th>Recorded By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                    <td>
                        {{ $expense->category }}{{ $expense->asset_type ? ': ' . $expense->asset_type : '' }}
                    </td>
                    <td>{{ $expense->description ?? '—' }}</td>
                    <td class="amount-col">₱{{ number_format($expense->amount, 2) }}</td>
                    <td>{{ $expense->receipt_number ?? '—' }}</td>
                    <td>{{ $expense->recordedBy->full_name ?? '—' }}</td>
                    <td>
                        <button class="btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editExpenseModal"
                            data-id="{{ $expense->expense_id }}"
                            data-category="{{ $expense->category }}"
                            data-asset-type="{{ $expense->asset_type }}"
                            data-description="{{ $expense->description }}"
                            data-amount="{{ $expense->amount }}"
                            data-date="{{ $expense->expense_date->format('Y-m-d') }}"
                            data-receipt="{{ $expense->receipt_number }}"
                            data-notes="{{ $expense->notes }}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-wallet2"></i>
                        <div>No expenses found</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($expenses->hasPages())
        <div class="pagination-wrapper">
            <div>Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} expenses</div>
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" id="addCategory" class="form-select" required onchange="toggleAssetType('add')">
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="addAssetTypeWrapper" style="display: none;">
                        <label class="form-label">Asset Type <span class="text-danger">*</span></label>
                        <input type="text" name="asset_type" id="addAssetType" class="form-control" list="assetNamesList" placeholder="Select or type new asset name">
                        <small class="text-muted">Select from existing assets or type a new one</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="What was this expense for?"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" name="receipt_number" class="form-control" placeholder="Optional reference">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expense Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editExpenseForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category" id="editCategory" class="form-select" required onchange="toggleAssetType('edit')">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="editAssetTypeWrapper" style="display: none;">
                        <label class="form-label">Asset Type <span class="text-danger">*</span></label>
                        <input type="text" name="asset_type" id="editAssetType" class="form-control" list="assetNamesList" placeholder="Select or type new asset name">
                        <small class="text-muted">Select from existing assets or type a new one</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="editAmount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" id="editDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Receipt Number</label>
                        <input type="text" name="receipt_number" id="editReceipt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Datalist for asset name suggestions -->
<datalist id="assetNamesList">
    @foreach($assetNames as $assetName)
        <option value="{{ $assetName }}">
    @endforeach
</datalist>

<script>
    // Toggle asset type field based on category
    function toggleAssetType(prefix) {
        const category = document.getElementById(prefix + 'Category').value;
        const wrapper = document.getElementById(prefix + 'AssetTypeWrapper');
        const input = document.getElementById(prefix + 'AssetType');
        
        if (category === 'Asset') {
            wrapper.style.display = 'block';
            input.required = true;
        } else {
            wrapper.style.display = 'none';
            input.required = false;
            input.value = '';
        }
    }

    // Edit modal population
    document.getElementById('editExpenseModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        
        document.getElementById('editExpenseForm').action = '/expenses/' + id;
        document.getElementById('editCategory').value = button.getAttribute('data-category');
        document.getElementById('editAssetType').value = button.getAttribute('data-asset-type') || '';
        document.getElementById('editDescription').value = button.getAttribute('data-description') || '';
        document.getElementById('editAmount').value = button.getAttribute('data-amount');
        document.getElementById('editDate').value = button.getAttribute('data-date');
        document.getElementById('editReceipt').value = button.getAttribute('data-receipt') || '';
        document.getElementById('editNotes').value = button.getAttribute('data-notes') || '';
        
        // Toggle asset type visibility
        toggleAssetType('edit');
    });
</script>
@endsection

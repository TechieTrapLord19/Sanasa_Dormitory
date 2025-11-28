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

    .modal-footer .btn-primary:hover {
        background-color: #021d47 !important;
        border-color: #021d47 !important;
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
        justify-content: center;
        align-items: center;
    }

    .btn-edit, .btn-archive, .btn-activate {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-edit i, .btn-archive i, .btn-activate i,
    .action-buttons button i {
        font-size: 1rem;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-archive {
        background-color: #fef3c7;
        color: #92400e;
    }

    .btn-archive:hover {
        background-color: #fde68a;
    }

    .btn-activate {
        background-color: #d1fae5;
        color: #065f46;
    }

    .btn-activate:hover {
        background-color: #a7f3d0;
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

    /* Column width adjustments */
    .rates-table th:nth-child(5),
    .rates-table td:nth-child(5) {
        max-width: 250px;
        word-wrap: break-word;
    }

    .rates-table th:nth-child(4),
    .rates-table td:nth-child(4) {
        min-width: 180px;
    }

.rates-table th:nth-child(6),
.rates-table td:nth-child(6) {
    text-align: center;
}

.rates-table th:nth-child(7),
.rates-table td:nth-child(7) {
    text-align: center;
    width: 1%;
    white-space: nowrap;
    padding: 1rem 0.75rem;
}

.status-badge {
    display: inline-block;
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.active {
    background-color: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background-color: #e5e7eb;
    color: #4b5563;
}
</style>

<div class="rates-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="rates-title">Rates Management</h1>
    @if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
    <button class="create-rate-btn" data-bs-toggle="modal" data-bs-target="#createRateModal">
        <i class="bi bi-plus-circle"></i>
        <span>Create New Rate</span>
    </button>
    @endif
</div>

<!-- Filters -->
<div class="rates-filters">
    <div class="filter-group">
        <p class="filter-label mb-0">Rate Name:</p>
        <input type="text" class="filter-input" id="rateNameFilter" placeholder="Search by rate name...">
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
                <th>Utilities</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="generalRatesTable">
            @forelse($rates as $rate)
                <tr>
                    <td>{{ $rate->rate_name ?? $rate->duration_type . ' Rate' }}</td>
                    <td>Per {{ $rate->duration_type === 'Daily' ? 'Day' : ($rate->duration_type === 'Weekly' ? 'Week' : 'Month') }}</td>
                    <td class="price-display">₱{{ number_format($rate->base_price, 2) }}</td>
                    <td>
                        @if($rate->utilities && $rate->utilities->count() > 0)
                            <ul style="margin: 0; padding-left: 1.25rem; list-style-type: disc;">
                                @foreach($rate->utilities as $utility)
                                    <li>{{ $utility->name }} - ₱{{ number_format($utility->price, 2) }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">None</span>
                        @endif
                    </td>
                    <td>{{ $rate->description }}</td>
                    <td>
                        @if($rate->status === 'active')
                            <span class="status-badge active">Active</span>
                        @else
                            <span class="status-badge inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if(auth()->check() && strtolower(auth()->user()->role) === 'owner')
                            <button class="btn-edit" onclick="editRate({{ $rate->rate_id }})">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            @if($rate->status === 'active')
                                <button class="btn-archive" onclick="archiveRate({{ $rate->rate_id }})">
                                    <i class="bi bi-archive"></i> Archive
                                </button>
                            @else
                                <button class="btn-activate" onclick="archiveRate({{ $rate->rate_id }})">
                                    <i class="bi bi-arrow-counterclockwise"></i> Activate
                                </button>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No rates found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>



<!-- Create Rate Modal -->
<div class="modal fade" id="createRateModal" tabindex="-1" aria-labelledby="createRateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRateModalLabel">Create New Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('rates.store') }}" method="POST" id="createRateForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rate_name" class="form-label">Rate Name <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control @error('rate_name') is-invalid @enderror"
                               id="rate_name" name="rate_name" placeholder="e.g., 6-Month Package, Annual Plan">
                        @error('rate_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
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
                        <label class="form-label">Utilities <span class="text-muted">(Optional - Electricity not included)</span></label>
                        <div class="border rounded p-3 bg-light">
                            <div class="mb-3">
                                <h6 class="mb-2">Predefined Utilities:</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input utility-checkbox" type="checkbox" value="Water" id="utility_water" data-utility-name="Water">
                                    <label class="form-check-label" for="utility_water">Water</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm d-inline-block ms-2" style="width: 120px;"
                                           id="utility_water_price" name="utilities[water][price]" placeholder="Price" disabled>
                                    <input type="hidden" name="utilities[water][name]" value="Water">
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input utility-checkbox" type="checkbox" value="Wi-Fi" id="utility_wifi" data-utility-name="Wi-Fi">
                                    <label class="form-check-label" for="utility_wifi">Wi-Fi</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm d-inline-block ms-2" style="width: 120px;"
                                           id="utility_wifi_price" name="utilities[wifi][price]" placeholder="Price" disabled>
                                    <input type="hidden" name="utilities[wifi][name]" value="Wi-Fi">
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input utility-checkbox" type="checkbox" value="Garbage" id="utility_garbage" data-utility-name="Garbage">
                                    <label class="form-check-label" for="utility_garbage">Garbage</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm d-inline-block ms-2" style="width: 120px;"
                                           id="utility_garbage_price" name="utilities[garbage][price]" placeholder="Price" disabled>
                                    <input type="hidden" name="utilities[garbage][name]" value="Garbage">
                                </div>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-2">Custom Utilities:</h6>
                                <div id="customUtilitiesContainer"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addCustomUtilityBtn">
                                    <i class="bi bi-plus-circle"></i> Add Custom Utility
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3" required></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-plus-circle"></i> Create Rate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rate Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1" aria-labelledby="editRateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRateModalLabel">Edit Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_rate_name" class="form-label">Rate Name <span class="text-muted">(Optional)</span></label>
                        <input type="text" class="form-control"
                               id="edit_rate_name" name="rate_name" placeholder="e.g., 6-Month Package, Annual Plan">
                    </div>
                    <div class="mb-3">
                        <label for="edit_duration_type" class="form-label">Duration Type</label>
                        <select class="form-select" id="edit_duration_type" name="duration_type" required>
                            <option value="">Select duration type...</option>
                            <option value="Daily">Daily</option>
                            <option value="Weekly">Weekly</option>
                            <option value="Monthly">Monthly</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_base_price" class="form-label">Base Price</label>
                        <input type="number" step="0.01" class="form-control"
                               id="edit_base_price" name="base_price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Utilities <span class="text-muted">(Optional - Electricity not included)</span></label>
                        <div class="border rounded p-3 bg-light">
                            <div class="mb-3">
                                <h6 class="mb-2">Predefined Utilities:</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input edit-utility-checkbox" type="checkbox" value="Water" id="edit_utility_water" data-utility-name="Water">
                                    <label class="form-check-label" for="edit_utility_water">Water</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm d-inline-block ms-2" style="width: 120px;"
                                           id="edit_utility_water_price" placeholder="Price" disabled>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input edit-utility-checkbox" type="checkbox" value="Wi-Fi" id="edit_utility_wifi" data-utility-name="Wi-Fi">
                                    <label class="form-check-label" for="edit_utility_wifi">Wi-Fi</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm d-inline-block ms-2" style="width: 120px;"
                                           id="edit_utility_wifi_price" placeholder="Price" disabled>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input edit-utility-checkbox" type="checkbox" value="Garbage" id="edit_utility_garbage" data-utility-name="Garbage">
                                    <label class="form-check-label" for="edit_utility_garbage">Garbage</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm d-inline-block ms-2" style="width: 120px;"
                                           id="edit_utility_garbage_price" placeholder="Price" disabled>
                                </div>
                            </div>
                            <div class="mb-3">
                                <h6 class="mb-2">Custom Utilities:</h6>
                                <div id="editCustomUtilitiesContainer"></div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addEditCustomUtilityBtn">
                                    <i class="bi bi-plus-circle"></i> Add Custom Utility
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-check-circle"></i> Update Rate
                    </button>
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

    // Utilities management
    let customUtilityIndex = 0;
    let editCustomUtilityIndex = 0;

    // Handle predefined utility checkboxes (Create modal)
    document.querySelectorAll('.utility-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const priceInput = document.getElementById(this.id + '_price');
            if (priceInput) {
                if (this.checked) {
                    priceInput.disabled = false;
                    priceInput.required = true;
                } else {
                    priceInput.disabled = true;
                    priceInput.required = false;
                    priceInput.value = '';
                }
            }
        });
    });

    // Handle predefined utility checkboxes (Edit modal)
    document.querySelectorAll('.edit-utility-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const priceInput = document.getElementById(this.id + '_price');
            if (priceInput) {
                if (this.checked) {
                    priceInput.disabled = false;
                    priceInput.required = true;
                } else {
                    priceInput.disabled = true;
                    priceInput.required = false;
                    priceInput.value = '';
                }
            }
        });
    });

    // Add custom utility (Create modal)
    const addCustomUtilityBtn = document.getElementById('addCustomUtilityBtn');
    const customUtilitiesContainer = document.getElementById('customUtilitiesContainer');

    if (addCustomUtilityBtn) {
        addCustomUtilityBtn.addEventListener('click', function() {
            const utilityDiv = document.createElement('div');
            utilityDiv.className = 'custom-utility-item mb-2 d-flex align-items-center gap-2';
            utilityDiv.innerHTML = `
                <input type="text" class="form-control form-control-sm" style="width: 150px;"
                       name="utilities[custom][${customUtilityIndex}][name]"
                       placeholder="Utility name" required>
                <input type="number" step="0.01" class="form-control form-control-sm" style="width: 120px;"
                       name="utilities[custom][${customUtilityIndex}][price]"
                       placeholder="Price" required>
                <button type="button" class="btn btn-sm btn-outline-danger remove-custom-utility">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            customUtilitiesContainer.appendChild(utilityDiv);
            customUtilityIndex++;

            // Add remove functionality
            utilityDiv.querySelector('.remove-custom-utility').addEventListener('click', function() {
                utilityDiv.remove();
            });
        });
    }

    // Add custom utility (Edit modal)
    const addEditCustomUtilityBtn = document.getElementById('addEditCustomUtilityBtn');
    const editCustomUtilitiesContainer = document.getElementById('editCustomUtilitiesContainer');

    if (addEditCustomUtilityBtn) {
        addEditCustomUtilityBtn.addEventListener('click', function() {
            const utilityDiv = document.createElement('div');
            utilityDiv.className = 'edit-custom-utility-item mb-2 d-flex align-items-center gap-2';
            utilityDiv.innerHTML = `
                <input type="text" class="form-control form-control-sm" style="width: 150px;"
                       placeholder="Utility name" required>
                <input type="number" step="0.01" class="form-control form-control-sm" style="width: 120px;"
                       placeholder="Price" required>
                <button type="button" class="btn btn-sm btn-outline-danger remove-edit-custom-utility">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            editCustomUtilitiesContainer.appendChild(utilityDiv);
            editCustomUtilityIndex++;

            // Add remove functionality
            utilityDiv.querySelector('.remove-edit-custom-utility').addEventListener('click', function() {
                utilityDiv.remove();
            });
        });
    }

    // Process form before submission to format utilities array correctly (Create)
    const createRateForm = document.getElementById('createRateForm');
    if (createRateForm) {
        createRateForm.addEventListener('submit', function(e) {
            const utilities = [];

            // Process predefined utilities
            document.querySelectorAll('.utility-checkbox:checked').forEach(checkbox => {
                const priceInput = document.getElementById(checkbox.id + '_price');
                if (priceInput && priceInput.value) {
                    utilities.push({
                        name: checkbox.dataset.utilityName,
                        price: priceInput.value
                    });
                }
            });

            // Process custom utilities
            document.querySelectorAll('.custom-utility-item').forEach(item => {
                const nameInput = item.querySelector('input[placeholder="Utility name"]');
                const priceInput = item.querySelector('input[placeholder="Price"]');
                if (nameInput && nameInput.value && priceInput && priceInput.value) {
                    utilities.push({
                        name: nameInput.value,
                        price: priceInput.value
                    });
                }
            });

            // Remove old utility inputs and add hidden inputs with correct format
            document.querySelectorAll('input[name^="utilities["]').forEach(input => {
                if (!input.classList.contains('utility-hidden')) {
                    input.remove();
                }
            });

            // Add utilities as hidden inputs in correct format
            utilities.forEach((utility, index) => {
                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = `utilities[${index}][name]`;
                nameInput.value = utility.name;
                nameInput.classList.add('utility-hidden');

                const priceInput = document.createElement('input');
                priceInput.type = 'hidden';
                priceInput.name = `utilities[${index}][price]`;
                priceInput.value = utility.price;
                priceInput.classList.add('utility-hidden');

                createRateForm.appendChild(nameInput);
                createRateForm.appendChild(priceInput);
            });
        });
    }

    // Process form before submission to format utilities array correctly (Edit)
    const editRateForm = document.getElementById('editRateForm');
    if (editRateForm) {
        editRateForm.addEventListener('submit', function(e) {
            const utilities = [];

            // Process predefined utilities
            document.querySelectorAll('.edit-utility-checkbox:checked').forEach(checkbox => {
                const priceInput = document.getElementById(checkbox.id + '_price');
                if (priceInput && priceInput.value) {
                    utilities.push({
                        name: checkbox.dataset.utilityName,
                        price: priceInput.value
                    });
                }
            });

            // Process custom utilities
            document.querySelectorAll('.edit-custom-utility-item').forEach(item => {
                const nameInput = item.querySelector('input[placeholder="Utility name"]');
                const priceInput = item.querySelector('input[placeholder="Price"]');
                if (nameInput && nameInput.value && priceInput && priceInput.value) {
                    utilities.push({
                        name: nameInput.value,
                        price: priceInput.value
                    });
                }
            });

            // Remove old utility hidden inputs
            document.querySelectorAll('#editRateForm input.utility-hidden').forEach(input => {
                input.remove();
            });

            // Add utilities as hidden inputs in correct format
            utilities.forEach((utility, index) => {
                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = `utilities[${index}][name]`;
                nameInput.value = utility.name;
                nameInput.classList.add('utility-hidden');

                const priceInput = document.createElement('input');
                priceInput.type = 'hidden';
                priceInput.name = `utilities[${index}][price]`;
                priceInput.value = utility.price;
                priceInput.classList.add('utility-hidden');

                editRateForm.appendChild(nameInput);
                editRateForm.appendChild(priceInput);
            });
        });
    }

    // Reset form when modal is closed (Create)
    const createRateModal = document.getElementById('createRateModal');
    if (createRateModal) {
        createRateModal.addEventListener('hidden.bs.modal', function() {
            if (createRateForm) {
                createRateForm.reset();
                customUtilitiesContainer.innerHTML = '';
                customUtilityIndex = 0;
                document.querySelectorAll('.utility-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                    const priceInput = document.getElementById(checkbox.id + '_price');
                    if (priceInput) {
                        priceInput.disabled = true;
                        priceInput.required = false;
                        priceInput.value = '';
                    }
                });
            }
        });
    }

    // Reset form when modal is closed (Edit)
    const editRateModal = document.getElementById('editRateModal');
    if (editRateModal) {
        editRateModal.addEventListener('hidden.bs.modal', function() {
            if (editRateForm) {
                editRateForm.reset();
                editCustomUtilitiesContainer.innerHTML = '';
                editCustomUtilityIndex = 0;
                document.querySelectorAll('.edit-utility-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                    const priceInput = document.getElementById(checkbox.id + '_price');
                    if (priceInput) {
                        priceInput.disabled = true;
                        priceInput.required = false;
                        priceInput.value = '';
                    }
                });
            }
        });
    }
});

async function editRate(rateId) {
    try {
        const response = await fetch(`/rates/${rateId}/edit`);
        if (!response.ok) {
            throw new Error('Failed to fetch rate data');
        }
        const rate = await response.json();

        // Populate form fields
        document.getElementById('edit_rate_name').value = rate.rate_name || '';
        document.getElementById('edit_duration_type').value = rate.duration_type;
        document.getElementById('edit_base_price').value = rate.base_price;
        document.getElementById('edit_description').value = rate.description || '';

        // Set form action
        document.getElementById('editRateForm').action = `/rates/${rateId}`;

        // Clear existing utilities
        document.querySelectorAll('.edit-utility-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            const priceInput = document.getElementById(checkbox.id + '_price');
            if (priceInput) {
                priceInput.disabled = true;
                priceInput.value = '';
            }
        });
        document.getElementById('editCustomUtilitiesContainer').innerHTML = '';
        editCustomUtilityIndex = 0;

        // Populate utilities
        if (rate.utilities && rate.utilities.length > 0) {
            rate.utilities.forEach(utility => {
                // Map utility names to checkbox IDs
                let checkboxId = null;
                if (utility.name === 'Water') {
                    checkboxId = 'edit_utility_water';
                } else if (utility.name === 'Wi-Fi') {
                    checkboxId = 'edit_utility_wifi';
                } else if (utility.name === 'Garbage') {
                    checkboxId = 'edit_utility_garbage';
                }

                const checkbox = checkboxId ? document.getElementById(checkboxId) : null;

                if (checkbox) {
                    // Predefined utility
                    checkbox.checked = true;
                    const priceInput = document.getElementById(checkboxId + '_price');
                    if (priceInput) {
                        priceInput.disabled = false;
                        priceInput.value = utility.price;
                    }
                } else {
                    // Custom utility
                    const utilityDiv = document.createElement('div');
                    utilityDiv.className = 'edit-custom-utility-item mb-2 d-flex align-items-center gap-2';
                    const utilityName = utility.name.replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    utilityDiv.innerHTML = `
                        <input type="text" class="form-control form-control-sm" style="width: 150px;"
                               placeholder="Utility name" value="${utilityName}" required>
                        <input type="number" step="0.01" class="form-control form-control-sm" style="width: 120px;"
                               placeholder="Price" value="${utility.price}" required>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-edit-custom-utility">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    `;
                    document.getElementById('editCustomUtilitiesContainer').appendChild(utilityDiv);
                    editCustomUtilityIndex++;

                    // Add remove functionality
                    utilityDiv.querySelector('.remove-edit-custom-utility').addEventListener('click', function() {
                        utilityDiv.remove();
                    });
                }
            });
        }

        // Show modal
        const editModal = new bootstrap.Modal(document.getElementById('editRateModal'));
        editModal.show();
    } catch (error) {
        console.error('Error fetching rate:', error);
        alert('Failed to load rate data. Please try again.');
    }
}

function archiveRate(rateId) {
    if (confirm('Are you sure you want to archive/restore this rate?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/rates/${rateId}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
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

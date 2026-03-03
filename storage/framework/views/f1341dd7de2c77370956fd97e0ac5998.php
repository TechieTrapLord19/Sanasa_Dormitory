<?php $__env->startSection('title', 'Asset Inventory'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .assets-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .assets-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .add-asset-btn {
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
    }

    .add-asset-btn:hover {
        background-color: #021d47;
        color: white;
    }

    .modal-footer .btn-primary:hover {
        background-color: #021d47 !important;
        border-color: #021d47 !important;
    }

    /* Filter Styles */
    .assets-filters {
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

    .filter-input,
    .filter-select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
        min-width: 150px;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .filter-btn {
        padding: 0.45rem 1.5rem;
        border: none;
        background-color: #03255b;
        color: white;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    .filter-btn:hover {
        background-color: #021d47;
    }

    .filter-btn-clear {
        background-color: white;
        color: #475569;
        border: 1px solid #cbd5e1;
        box-shadow: none;
    }

    .filter-btn-clear:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Table Styles */
    .assets-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .assets-table {
        width: 100%;
        border-collapse: collapse;
    }

    .assets-table thead {
        background-color: #f7fafc;
    }

    .assets-table th {
        padding: 1rem;
        text-align: center;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .assets-table th:first-child {
        text-align: left;
    }

    .assets-table th:last-child {
        text-align: center;
        width: 1%;
        white-space: nowrap;
    }
    .assets-table th.sortable {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }
    .assets-table th.sortable:hover {
        background: #e2e8f0;
        color: #03255b;
    }
    .assets-table th.sortable .sort-icon {
        margin-left: 0.3rem;
        font-size: 0.7rem;
        opacity: 0.4;
    }
    .assets-table th.sortable.active .sort-icon {
        opacity: 1;
        color: #03255b;
    }

    .assets-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
    }

    .assets-table td:first-child {
        text-align: left;
    }

    .assets-table td:last-child {
        text-align: center;
        width: 1%;
        white-space: nowrap;
    }

    .assets-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .assets-table tbody tr:last-child td {
        border-bottom: none;
    }

    .condition-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .condition-badge.Good {
        background-color: #d1fae5;
        color: #065f46;
    }

    .condition-badge.Needs-Repair {
        background-color: #fef3c7;
        color: #92400e;
    }

    .condition-badge.Broken {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .condition-badge.Missing {
        background-color: #e5e7eb;
        color: #4b5563;
    }

    .btn-edit-asset {
        padding: 0.375rem 0.875rem;
        border: 1px solid #e2e8f0;
        background-color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .btn-edit-asset:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        color: #03255b;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-wrapper .form-select {
        width: auto;
        border-radius: 999px;
        min-width: 70px;
    }

    .pagination-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pagination-center {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pagination-right {
        display: flex;
        align-items: center;
    }

    /* Fix pagination styling */
    .pagination-wrapper .pagination {
        margin: 0;
        display: flex;
        list-style: none;
        gap: 0.25rem;
    }

    .pagination-wrapper .pagination .page-item {
        margin: 0;
    }

    .pagination-wrapper .pagination .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #475569;
        text-decoration: none;
        background-color: white;
        transition: all 0.2s ease;
    }

    .pagination-wrapper .pagination .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #03255b;
    }

    .pagination-wrapper .pagination .page-item.active .page-link {
        background-color: #03255b;
        border-color: #03255b;
        color: white;
        font-weight: 600;
    }

    .pagination-wrapper .pagination .page-item.disabled .page-link {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-wrapper .pagination .page-link:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    /* Hide the large chevron icons if they exist */
    .pagination-wrapper svg {
        display: none !important;
    }

    /* Hide the "Showing X to Y" text from Laravel pagination since we display it manually */
    .pagination-wrapper nav > div:first-child {
        display: none !important;
    }

    .pagination-wrapper nav > div:last-child > div:first-child {
        display: none !important;
    }

    /* Show only the pagination controls (ul.pagination) */
    .pagination-wrapper nav > div:last-child > div:last-child {
        display: block !important;
    }

    /* Style our custom "Showing X to Y" text */
    .pagination-center .small {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .pagination-center .fw-semibold {
        font-weight: 600;
        color: #0f172a;
    }
</style>

<div class="container-fluid">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following errors:
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="assets-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="assets-title">Asset Inventory</h1>
        <button type="button" class="add-asset-btn" data-bs-toggle="modal" data-bs-target="#addAssetModal">
            <i class="bi bi-plus-circle"></i> Add New Asset
        </button>
    </div>

    <!-- Filters -->
    <div class="assets-filters">
        <form method="GET" action="<?php echo e(route('asset-inventory')); ?>" id="filterForm" class="d-flex flex-wrap align-items-end gap-3">
            <div class="filter-group">
                <label class="filter-label">Asset Type:</label>
                <select name="asset_type" class="filter-select">
                    <option value="">All Asset Types</option>
                    <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assetType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($assetType); ?>" <?php echo e($selectedAssetType == $assetType ? 'selected' : ''); ?>>
                            <?php echo e($assetType); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Condition:</label>
                <select name="condition" class="filter-select">
                    <option value="">All Conditions</option>
                    <option value="Good" <?php echo e($selectedCondition == 'Good' ? 'selected' : ''); ?>>Good</option>
                    <option value="Needs Repair" <?php echo e($selectedCondition == 'Needs Repair' ? 'selected' : ''); ?>>Needs Repair</option>
                    <option value="Broken" <?php echo e($selectedCondition == 'Broken' ? 'selected' : ''); ?>>Broken</option>
                    <option value="Missing" <?php echo e($selectedCondition == 'Missing' ? 'selected' : ''); ?>>Missing</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Location:</label>
                <select name="location" class="filter-select">
                    <option value="all" <?php echo e($selectedLocation == 'all' || $selectedLocation == '' ? 'selected' : ''); ?>>All Locations</option>
                    <option value="storage" <?php echo e($selectedLocation == 'storage' ? 'selected' : ''); ?>>Storage</option>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($room->room_id); ?>" <?php echo e($selectedLocation == $room->room_id ? 'selected' : ''); ?>>
                            Room <?php echo e($room->room_num); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Search:</label>
                <input type="text"
                       name="search"
                       class="filter-input"
                       placeholder="Search by asset name..."
                       value="<?php echo e($searchTerm); ?>">
            </div>

            <div class="filter-group">
                <button type="submit" class="filter-btn">Apply Filters</button>
                <a href="<?php echo e(route('asset-inventory')); ?>" class="filter-btn filter-btn-clear" style="text-decoration: none; display: inline-block;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Assets Table -->
    <div class="assets-table-container">
        <?php if($assets->count() > 0): ?>
            <table class="assets-table">
                <thead>
                    <tr>
                        <th class="sortable <?php echo e($sortBy === 'name' ? 'active' : ''); ?>" onclick="sortTable('name')" style="text-align: left;">
                            Asset Name
                            <?php if($sortBy === 'name'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th class="sortable <?php echo e($sortBy === 'room_id' ? 'active' : ''); ?>" onclick="sortTable('room_id')">
                            Location
                            <?php if($sortBy === 'room_id'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th class="sortable <?php echo e($sortBy === 'condition' ? 'active' : ''); ?>" onclick="sortTable('condition')">
                            Condition
                            <?php if($sortBy === 'condition'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th class="sortable <?php echo e($sortBy === 'purchase_date' ? 'active' : ''); ?>" onclick="sortTable('purchase_date')">
                            Date Acquired
                            <?php if($sortBy === 'purchase_date'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($asset->name); ?></strong></td>
                            <td><?php echo e($asset->location); ?></td>
                            <td>
                                <span class="condition-badge <?php echo e(str_replace(' ', '-', $asset->condition)); ?>">
                                    <?php echo e($asset->condition); ?>

                                </span>
                            </td>
                            <td>
                                <?php echo e($asset->date_acquired ? $asset->date_acquired->format('M d, Y') : 'N/A'); ?>

                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button"
                                            class="btn-edit-asset"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAssetModal<?php echo e($asset->asset_id); ?>">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button"
                                            class="btn-edit-asset"
                                            data-bs-toggle="modal"
                                            data-bs-target="#logRepairModal<?php echo e($asset->asset_id); ?>">
                                        <i class="bi bi-tools"></i> Log Repair
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-left">
                    <form method="GET" action="<?php echo e(route('asset-inventory')); ?>" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="asset_type" value="<?php echo e($selectedAssetType); ?>">
                        <input type="hidden" name="condition" value="<?php echo e($selectedCondition); ?>">
                        <input type="hidden" name="location" value="<?php echo e($selectedLocation); ?>">
                        <input type="hidden" name="search" value="<?php echo e($searchTerm); ?>">
                        <label for="perPage" class="text-muted small mb-0">Rows per page</label>
                        <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                            <?php $__currentLoopData = [5, 10, 15, 20]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($option); ?>" <?php echo e((int) $perPage === $option ? 'selected' : ''); ?>>
                                    <?php echo e($option); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </form>
                </div>
                <div class="pagination-center">
                    <p class="small text-muted mb-0">
                        Showing
                        <span class="fw-semibold"><?php echo e($assets->firstItem() ?? 0); ?></span>
                        to
                        <span class="fw-semibold"><?php echo e($assets->lastItem() ?? 0); ?></span>
                        of
                        <span class="fw-semibold"><?php echo e($assets->total()); ?></span>
                        results
                    </p>
                </div>
                <div class="pagination-right">
                    <?php echo e($assets->appends(['asset_type' => $selectedAssetType, 'condition' => $selectedCondition, 'location' => $selectedLocation, 'search' => $searchTerm, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->links()); ?>

                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>No assets found</h3>
                <p>There are no assets matching your filters.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add New Asset Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-labelledby="addAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAssetModalLabel">Add New Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('assets.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="asset_name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="asset_name"
                               name="name"
                               value="<?php echo e(old('name')); ?>"
                               required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="asset_location" class="form-label">Location <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="asset_location"
                                name="room_id">
                            <option value="">Storage</option>
                            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($room->room_id); ?>" <?php echo e(old('room_id') == $room->room_id ? 'selected' : ''); ?>>
                                    Room <?php echo e($room->room_num); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Select "Storage" or a specific room</small>
                        <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="asset_condition" class="form-label">Condition <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="asset_condition"
                                name="condition"
                                required>
                            <option value="">Select condition...</option>
                            <option value="Good" <?php echo e(old('condition', 'Good') === 'Good' ? 'selected' : ''); ?>>Good</option>
                            <option value="Needs Repair" <?php echo e(old('condition') === 'Needs Repair' ? 'selected' : ''); ?>>Needs Repair</option>
                            <option value="Broken" <?php echo e(old('condition') === 'Broken' ? 'selected' : ''); ?>>Broken</option>
                            <option value="Missing" <?php echo e(old('condition') === 'Missing' ? 'selected' : ''); ?>>Missing</option>
                        </select>
                        <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="asset_date_acquired" class="form-label">Date Acquired</label>
                        <input type="date"
                               class="form-control <?php $__errorArgs = ['date_acquired'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="asset_date_acquired"
                               name="date_acquired"
                               value="<?php echo e(old('date_acquired', date('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['date_acquired'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-plus-circle"></i> Add Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Modals -->
<?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editAssetModal<?php echo e($asset->asset_id); ?>" tabindex="-1" aria-labelledby="editAssetModalLabel<?php echo e($asset->asset_id); ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssetModalLabel<?php echo e($asset->asset_id); ?>">Edit Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('assets.update', $asset->asset_id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_asset_name<?php echo e($asset->asset_id); ?>" class="form-label">Asset Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="edit_asset_name<?php echo e($asset->asset_id); ?>"
                               name="name"
                               value="<?php echo e(old('name', $asset->name)); ?>"
                               required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_asset_location<?php echo e($asset->asset_id); ?>" class="form-label">Location <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="edit_asset_location<?php echo e($asset->asset_id); ?>"
                                name="room_id">
                            <option value="" <?php echo e(!$asset->room_id ? 'selected' : ''); ?>>Storage</option>
                            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($room->room_id); ?>" <?php echo e(old('room_id', $asset->room_id) == $room->room_id ? 'selected' : ''); ?>>
                                    Room <?php echo e($room->room_num); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Change location to move asset between rooms or storage</small>
                        <?php $__errorArgs = ['room_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_asset_condition<?php echo e($asset->asset_id); ?>" class="form-label">Condition <span class="text-danger">*</span></label>
                        <select class="form-select <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="edit_asset_condition<?php echo e($asset->asset_id); ?>"
                                name="condition"
                                required>
                            <option value="">Select condition...</option>
                            <option value="Good" <?php echo e(old('condition', $asset->condition) === 'Good' ? 'selected' : ''); ?>>Good</option>
                            <option value="Needs Repair" <?php echo e(old('condition', $asset->condition) === 'Needs Repair' ? 'selected' : ''); ?>>Needs Repair</option>
                            <option value="Broken" <?php echo e(old('condition', $asset->condition) === 'Broken' ? 'selected' : ''); ?>>Broken</option>
                            <option value="Missing" <?php echo e(old('condition', $asset->condition) === 'Missing' ? 'selected' : ''); ?>>Missing</option>
                        </select>
                        <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_asset_date_acquired<?php echo e($asset->asset_id); ?>" class="form-label">Date Acquired</label>
                        <input type="date"
                               class="form-control <?php $__errorArgs = ['date_acquired'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="edit_asset_date_acquired<?php echo e($asset->asset_id); ?>"
                               name="date_acquired"
                               value="<?php echo e(old('date_acquired', $asset->date_acquired ? $asset->date_acquired->format('Y-m-d') : '')); ?>">
                        <?php $__errorArgs = ['date_acquired'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Log Repair Modals -->
<?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="logRepairModal<?php echo e($asset->asset_id); ?>" tabindex="-1" aria-labelledby="logRepairModalLabel<?php echo e($asset->asset_id); ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logRepairModalLabel<?php echo e($asset->asset_id); ?>">Log Repair for <?php echo e($asset->name); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('maintenance-logs.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="asset_id" value="<?php echo e($asset->asset_id); ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Asset:</label>
                        <input type="text"
                               class="form-control"
                               value="<?php echo e($asset->name); ?> - <?php echo e($asset->location); ?>"
                               readonly
                               style="background-color: #f8fafc;">
                    </div>

                    <div class="mb-3">
                        <label for="repair_description<?php echo e($asset->asset_id); ?>" class="form-label">Issue Description <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  id="repair_description<?php echo e($asset->asset_id); ?>"
                                  name="description"
                                  rows="4"
                                  required><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label for="repair_date_reported<?php echo e($asset->asset_id); ?>" class="form-label">Date Reported</label>
                        <input type="date"
                               class="form-control <?php $__errorArgs = ['date_reported'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="repair_date_reported<?php echo e($asset->asset_id); ?>"
                               name="date_reported"
                               value="<?php echo e(old('date_reported', date('Y-m-d'))); ?>">
                        <small class="text-muted">Defaults to today if not specified</small>
                        <?php $__errorArgs = ['date_reported'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-tools"></i> Log Repair
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script>
// Sorting function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort_by');
    const currentDir = url.searchParams.get('sort_dir') || 'asc';

    if (currentSort === column) {
        url.searchParams.set('sort_dir', currentDir === 'asc' ? 'desc' : 'asc');
    } else {
        url.searchParams.set('sort_by', column);
        url.searchParams.set('sort_dir', 'asc');
    }

    window.location.href = url.toString();
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/contents/asset-inventory.blade.php ENDPATH**/ ?>
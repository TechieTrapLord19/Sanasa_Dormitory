<?php $__env->startSection('title', 'Activity Logs'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .logs-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .logs-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    /* Filter Styles */
    .logs-filters {
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
        padding: 0.5rem 1.5rem;
        border: none;
        background-color: #03255b;
        color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background-color: #021d47;
    }

    .filter-btn-clear {
        background-color: #e2e8f0;
        color: #4a5568;
    }

    .filter-btn-clear:hover {
        background-color: #cbd5e0;
    }

    /* Table Styles */
    .logs-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .logs-table {
        width: 100%;
        border-collapse: collapse;
    }

    .logs-table thead {
        background-color: #f7fafc;
    }

    .logs-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .logs-table th.sortable {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }
    .logs-table th.sortable:hover {
        background: #e2e8f0;
        color: #03255b;
    }
    .logs-table th.sortable .sort-icon {
        margin-left: 0.3rem;
        font-size: 0.7rem;
        opacity: 0.4;
    }
    .logs-table th.sortable.active .sort-icon {
        opacity: 1;
        color: #03255b;
    }

    .logs-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .logs-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .logs-table tbody tr.clickable {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .logs-table tbody tr.clickable:hover {
        background-color: #edf2f7;
    }

    .logs-table tbody tr:last-child td {
        border-bottom: none;
    }

    .action-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-badge.created {
        background-color: #d1fae5;
        color: #065f46;
    }

    .action-badge.updated {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .action-badge.deleted,
    .action-badge.canceled,
    .action-badge.archived {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .action-badge.checked {
        background-color: #fef3c7;
        color: #92400e;
    }

    .action-badge.payment,
    .action-badge.generated {
        background-color: #e0e7ff;
        color: #3730a3;
    }

    .action-badge.login-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .action-badge.login-failed,
    .action-badge.login-blocked,
    .action-badge.account-locked {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .action-badge.logout {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .action-badge.login-2fa {
        background-color: #fef3c7;
        color: #92400e;
    }

    .description-text {
        max-width: 500px;
        word-wrap: break-word;
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
        display: none !important; /* Hide mobile pagination */
    }

    .pagination-wrapper nav > div:last-child > div:first-child {
        display: none !important; /* Hide the "Showing X to Y" text div */
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
    <div class="logs-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="logs-title">Activity Logs</h1>
    </div>

    <!-- Filters -->
    <div class="logs-filters">
        <form method="GET" action="<?php echo e(route('activity-logs')); ?>" id="filterForm" class="d-flex flex-wrap align-items-end gap-3">
            <?php if(auth()->check() && strtolower(auth()->user()->role) !== 'caretaker'): ?>
            <div class="filter-group">
                <label class="filter-label">User:</label>
                <select name="user_id" class="filter-select">
                    <option value="">All Users</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($user->user_id); ?>" <?php echo e($selectedUserId == $user->user_id ? 'selected' : ''); ?>>
                            <?php echo e($user->last_name); ?>, <?php echo e($user->first_name); ?> (<?php echo e(ucfirst($user->role)); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="filter-group">
                <label class="filter-label">Action:</label>
                <select name="action" class="filter-select">
                    <option value="">All Actions</option>
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($action); ?>" <?php echo e($selectedAction == $action ? 'selected' : ''); ?>>
                            <?php echo e($action); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Date From:</label>
                <input type="date" name="date_from" class="filter-input" value="<?php echo e($dateFrom); ?>">
            </div>

            <div class="filter-group">
                <label class="filter-label">Date To:</label>
                <input type="date" name="date_to" class="filter-input" value="<?php echo e($dateTo); ?>">
            </div>

            <div class="filter-group">
                <a href="<?php echo e(route('activity-logs')); ?>" class="filter-btn filter-btn-clear" style="text-decoration: none; display: inline-block;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="logs-table-container">
        <?php if($logs->count() > 0): ?>
            <table class="logs-table">
                <thead>
                    <tr>
                        <th class="sortable <?php echo e($sortBy === 'created_at' ? 'active' : ''); ?>" onclick="sortTable('created_at')">
                            Date & Time
                            <?php if($sortBy === 'created_at'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th class="sortable <?php echo e($sortBy === 'user_id' ? 'active' : ''); ?>" onclick="sortTable('user_id')">
                            Name
                            <?php if($sortBy === 'user_id'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th class="sortable <?php echo e($sortBy === 'action' ? 'active' : ''); ?>" onclick="sortTable('action')">
                            Action
                            <?php if($sortBy === 'action'): ?>
                                <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            <?php endif; ?>
                        </th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr <?php if($log->resource_url): ?> class="clickable" onclick="window.location.href='<?php echo e($log->resource_url); ?>'" <?php endif; ?>>
                            <td>
                                <div><?php echo e($log->created_at->format('M d, Y')); ?></div>
                                <div style="font-size: 0.75rem; color: #718096;"><?php echo e($log->created_at->format('h:i A')); ?></div>
                            </td>
                            <td>
                                <strong><?php echo e($log->caretaker_name); ?></strong>
                            </td>
                            <td>
                                <?php
                                    $actionClass = 'created';
                                    $actionLower = strtolower($log->action);
                                    if (str_contains($actionLower, 'login failed')) {
                                        $actionClass = 'login-failed';
                                    } elseif (str_contains($actionLower, 'login blocked')) {
                                        $actionClass = 'login-blocked';
                                    } elseif (str_contains($actionLower, 'account locked')) {
                                        $actionClass = 'account-locked';
                                    } elseif (str_contains($actionLower, 'login 2fa')) {
                                        $actionClass = 'login-2fa';
                                    } elseif (str_contains($actionLower, 'login success') || str_contains($actionLower, 'login success (2fa)')) {
                                        $actionClass = 'login-success';
                                    } elseif (str_contains($actionLower, 'logout')) {
                                        $actionClass = 'logout';
                                    } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'changed')) {
                                        $actionClass = 'updated';
                                    } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'cancel') || str_contains($actionLower, 'archive')) {
                                        $actionClass = 'deleted';
                                    } elseif (str_contains($actionLower, 'check')) {
                                        $actionClass = 'checked';
                                    } elseif (str_contains($actionLower, 'payment') || str_contains($actionLower, 'generate')) {
                                        $actionClass = 'payment';
                                    }
                                ?>
                                <span class="action-badge <?php echo e($actionClass); ?>"><?php echo e($log->action); ?></span>
                            </td>
                            <td>
                                <div class="description-text"><?php echo e($log->description); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-left">
                    <form method="GET" action="<?php echo e(route('activity-logs')); ?>" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="user_id" value="<?php echo e($selectedUserId); ?>">
                        <input type="hidden" name="action" value="<?php echo e($selectedAction); ?>">
                        <input type="hidden" name="date_from" value="<?php echo e($dateFrom); ?>">
                        <input type="hidden" name="date_to" value="<?php echo e($dateTo); ?>">
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
                        <span class="fw-semibold"><?php echo e($logs->firstItem() ?? 0); ?></span>
                        to
                        <span class="fw-semibold"><?php echo e($logs->lastItem() ?? 0); ?></span>
                        of
                        <span class="fw-semibold"><?php echo e($logs->total()); ?></span>
                        results
                    </p>
                </div>
                <div class="pagination-right">
                    <?php echo e($logs->appends(['user_id' => $selectedUserId, 'action' => $selectedAction, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->links()); ?>

                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>No activity logs found</h3>
                <p>There are no activity logs matching your filters.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Sorting function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort_by');
    const currentDir = url.searchParams.get('sort_dir') || 'desc';

    if (currentSort === column) {
        url.searchParams.set('sort_dir', currentDir === 'asc' ? 'desc' : 'asc');
    } else {
        url.searchParams.set('sort_by', column);
        url.searchParams.set('sort_dir', 'desc');
    }

    window.location.href = url.toString();
}

// Auto-apply filters on change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        // Auto-submit on select change
        filterForm.querySelectorAll('select').forEach(function(select) {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });

        // Auto-submit on date change
        filterForm.querySelectorAll('input[type="date"]').forEach(function(input) {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    }
});
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/contents/payments.blade.php ENDPATH**/ ?>
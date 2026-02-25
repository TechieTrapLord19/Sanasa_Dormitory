<?php if($paginator->hasPages()): ?>
    <?php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $visiblePages = 4;
        
        // Calculate start and end pages for sliding window
        $halfWindow = floor($visiblePages / 2);
        $startPage = max(1, $currentPage - $halfWindow);
        $endPage = min($lastPage, $startPage + $visiblePages - 1);
        
        // Adjust start if we're near the end
        if ($endPage - $startPage < $visiblePages - 1) {
            $startPage = max(1, $endPage - $visiblePages + 1);
        }
    ?>
    <nav class="custom-pagination" aria-label="Pagination">
        <ul class="pagination-list">
            
            <?php if($paginator->onFirstPage()): ?>
                <li class="pagination-item disabled">
                    <span class="pagination-link pagination-prev" aria-disabled="true">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            <?php else: ?>
                <li class="pagination-item">
                    <a class="pagination-link pagination-prev" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if($startPage > 1): ?>
                <li class="pagination-item">
                    <a class="pagination-link" href="<?php echo e($paginator->url(1)); ?>">1</a>
                </li>
                <?php if($startPage > 2): ?>
                    <li class="pagination-item disabled">
                        <span class="pagination-link pagination-dots">...</span>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php for($page = $startPage; $page <= $endPage; $page++): ?>
                <?php if($page == $currentPage): ?>
                    <li class="pagination-item active">
                        <span class="pagination-link"><?php echo e($page); ?></span>
                    </li>
                <?php else: ?>
                    <li class="pagination-item">
                        <a class="pagination-link" href="<?php echo e($paginator->url($page)); ?>"><?php echo e($page); ?></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>

            
            <?php if($endPage < $lastPage): ?>
                <?php if($endPage < $lastPage - 1): ?>
                    <li class="pagination-item disabled">
                        <span class="pagination-link pagination-dots">...</span>
                    </li>
                <?php endif; ?>
                <li class="pagination-item">
                    <a class="pagination-link" href="<?php echo e($paginator->url($lastPage)); ?>"><?php echo e($lastPage); ?></a>
                </li>
            <?php endif; ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <li class="pagination-item">
                    <a class="pagination-link pagination-next" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="pagination-item disabled">
                    <span class="pagination-link pagination-next" aria-disabled="true">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
<?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/vendor/pagination/custom.blade.php ENDPATH**/ ?>
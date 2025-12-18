@if ($paginator->hasPages())
    @php
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
    @endphp
    <nav class="custom-pagination" aria-label="Pagination">
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled">
                    <span class="pagination-link pagination-prev" aria-disabled="true">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a class="pagination-link pagination-prev" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- First Page + Dots --}}
            @if ($startPage > 1)
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if ($startPage > 2)
                    <li class="pagination-item disabled">
                        <span class="pagination-link pagination-dots">...</span>
                    </li>
                @endif
            @endif

            {{-- Sliding Window Pages --}}
            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page == $currentPage)
                    <li class="pagination-item active">
                        <span class="pagination-link">{{ $page }}</span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a class="pagination-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last Page + Dots --}}
            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <li class="pagination-item disabled">
                        <span class="pagination-link pagination-dots">...</span>
                    </li>
                @endif
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a class="pagination-link pagination-next" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination-item disabled">
                    <span class="pagination-link pagination-next" aria-disabled="true">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

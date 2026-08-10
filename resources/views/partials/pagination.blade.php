@if ($paginator->hasPages())
    <div class="soma-pagination-wrap">
        <ul class="soma-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled"><span><i class="fas fa-chevron-left"></i></span></li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="active"><span>{{ $page }}</span></li>
                @else
                    <li>
                        <a href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="disabled"><span><i class="fas fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </div>
@endif
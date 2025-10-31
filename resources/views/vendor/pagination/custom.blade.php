@if ($paginator->hasPages())
    <a href="{{ $paginator->previousPageUrl() }}" class="arrowPagination {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
        <img src="{{ asset('img/arrowLeft.svg') }}" alt="">
    </a>
    <div class="pagination__paginationGirls">
        <div class="pagination__paginationGirls">
            @if ($paginator->currentPage() > 3)
                <a href="{{ $paginator->url(1) }}" class="block-paginationGirls">1</a>
                @if ($paginator->currentPage() > 4)
                    <span class="block-paginationGirls">...</span>
                @endif
            @endif

            @foreach (range(1, $paginator->lastPage()) as $i)
                @if ($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
                    @if ($i == $paginator->currentPage())
                        <a href="{{ $paginator->url($i) }}" class="block-paginationGirls block-paginationGirls__active">{{ $i }}</a>
                    @else
                        <a href="{{ $paginator->url($i) }}" class="block-paginationGirls">{{ $i }}</a>
                    @endif
                @endif
            @endforeach

            @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                @if ($paginator->currentPage() < $paginator->lastPage() - 3)
                    <span class="block-paginationGirls">...</span>
                @endif
                <a href="{{ $paginator->url($paginator->lastPage()) }}" class="block-paginationGirls">{{ $paginator->lastPage() }}</a>
            @endif
        </div>
    </div>
    <a href="{{ $paginator->nextPageUrl() }}" class="arrowPagination {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
        <img src="{{ asset('img/arrowNext.svg') }}" alt="">
    </a>
@endif

<style>
.arrowPagination.disabled {
    opacity: 0.5;
    pointer-events: none;
}
</style>


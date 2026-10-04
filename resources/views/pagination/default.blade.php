@if ($paginator->hasPages())
    <nav aria-label="Lapu navigācija">
        <ul class="pagination-list">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="pagination-link" aria-disabled="true">&lsaquo;</span>
                @else
                    <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Iepriekšējā lapa">&lsaquo;</a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination-link" aria-disabled="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-link" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Nākamā lapa">&rsaquo;</a>
                @else
                    <span class="pagination-link" aria-disabled="true">&rsaquo;</span>
                @endif
            </li>
        </ul>
    </nav>
@endif

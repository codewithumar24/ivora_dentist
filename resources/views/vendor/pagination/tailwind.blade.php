@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4 flex-wrap px-1">
        <p class="text-xs text-pine/55">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </p>

        <div class="flex items-center gap-1.5">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-ghost btn-sm opacity-40 cursor-not-allowed">&laquo; Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-ghost btn-sm">&laquo; Previous</a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-9 h-9 grid place-items-center text-sm text-pine/40">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-9 h-9 grid place-items-center rounded-full bg-pine text-porcelain text-sm font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-9 h-9 grid place-items-center rounded-full text-sm text-pine hover:bg-mint/40 transition">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-ghost btn-sm">Next &raquo;</a>
            @else
                <span class="btn btn-ghost btn-sm opacity-40 cursor-not-allowed">Next &raquo;</span>
            @endif
        </div>
    </nav>
@endif
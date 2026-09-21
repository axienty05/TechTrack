@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation"
         class="flex flex-col sm:flex-row items-center justify-between gap-3 py-1">

        {{-- Info teks kiri --}}
        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-base-200 border border-base-content/10 text-base-content/60 font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                @if ($paginator->firstItem())
                    <span>Data</span>
                    <span class="font-bold text-base-content/80">{{ $paginator->firstItem() }}</span>
                    <span>&ndash;</span>
                    <span class="font-bold text-base-content/80">{{ $paginator->lastItem() }}</span>
                    <span>dari</span>
                    <span class="font-bold text-primary">{{ $paginator->total() }}</span>
                    <span>total</span>
                @else
                    {{ $paginator->count() }} data
                @endif
            </span>
        </div>

        {{-- Tombol Pagination --}}
        <div class="join rounded-xl overflow-hidden shadow-sm border border-base-content/10">

            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <button type="button" class="join-item btn btn-sm btn-ghost opacity-30 cursor-not-allowed" disabled aria-disabled="true" title="Halaman Sebelumnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                   class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content transition-all duration-200"
                   title="Halaman Sebelumnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator "..." --}}
                @if (is_string($element))
                    <button type="button" class="join-item btn btn-sm btn-ghost cursor-default opacity-50" disabled>
                        {{ $element }}
                    </button>
                @endif

                {{-- Daftar Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button type="button" class="join-item btn btn-sm btn-primary font-bold shadow-inner pointer-events-none"
                                    aria-current="page">
                                {{ $page }}
                            </button>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                               class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content font-medium transition-all duration-200"
                               aria-label="Halaman {{ $page }}">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                   class="join-item btn btn-sm btn-ghost hover:btn-primary hover:text-primary-content transition-all duration-200"
                   title="Halaman Berikutnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <button type="button" class="join-item btn btn-sm btn-ghost opacity-30 cursor-not-allowed" disabled aria-disabled="true" title="Halaman Berikutnya">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @endif
        </div>
    </nav>
@endif
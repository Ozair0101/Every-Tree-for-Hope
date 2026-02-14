@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex justify-center">
        <ul class="flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-charcoal/40 cursor-default">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-deep-green hover:text-gold-accent transition-colors duration-200">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true">
                        <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-charcoal/40">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page">
                                <span
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-deep-green">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-charcoal/60 hover:text-deep-green transition-colors duration-200">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-deep-green hover:text-gold-accent transition-colors duration-200">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </a>
                </li>
            @else
                <li class="disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-charcoal/40 cursor-default">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

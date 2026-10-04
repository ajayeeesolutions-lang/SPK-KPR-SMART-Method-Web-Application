@if ($paginator->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">

    {{-- Info hasil --}}
    <div class="text-muted small">
        Menampilkan
        <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong>
        dari <strong>{{ $paginator->total() }}</strong> data
    </div>

    {{-- Tombol navigasi --}}
    <nav>
        <ul class="pagination pagination-sm mb-0 gap-1" style="flex-wrap: wrap;">

            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-3 border-0 bg-light text-muted px-3">
                        <i class="fa-solid fa-chevron-left me-1" style="font-size:0.75rem;"></i> Prev
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-3 border-0 bg-light text-dark px-3 fw-semibold"
                       href="{{ $paginator->previousPageUrl() }}"
                       style="transition: all 0.2s;">
                        <i class="fa-solid fa-chevron-left me-1" style="font-size:0.75rem;"></i> Prev
                    </a>
                </li>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link border-0 bg-transparent text-muted px-2">…</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link rounded-3 border-0 px-3 fw-bold"
                                      style="background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); color:#fff; box-shadow: 0 4px 12px -2px rgba(37,99,235,0.45);">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded-3 border-0 bg-light text-dark px-3 fw-semibold"
                                   href="{{ $url }}"
                                   style="transition: all 0.2s;">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-3 border-0 bg-light text-dark px-3 fw-semibold"
                       href="{{ $paginator->nextPageUrl() }}"
                       style="transition: all 0.2s;">
                        Next <i class="fa-solid fa-chevron-right ms-1" style="font-size:0.75rem;"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link rounded-3 border-0 bg-light text-muted px-3">
                        Next <i class="fa-solid fa-chevron-right ms-1" style="font-size:0.75rem;"></i>
                    </span>
                </li>
            @endif

        </ul>
    </nav>
</div>
@endif

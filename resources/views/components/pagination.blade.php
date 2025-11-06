@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </div>
        
        <nav aria-label="Navegación de páginas">
            <ul class="pagination pagination-sm mb-0">
                {{-- Botón Primera Página --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled first">
                        <span class="page-link">
                            <i class="ri ri-skip-back-mini-line ri-20px"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item first">
                        <a class="page-link" href="{{ $paginator->url(1) }}" wire:navigate>
                            <i class="ri ri-skip-back-mini-line ri-20px"></i>
                        </a>
                    </li>
                @endif

                {{-- Botón Página Anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled prev">
                        <span class="page-link">
                            <i class="ri ri-arrow-left-s-line ri-20px"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item prev">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" wire:navigate>
                            <i class="ri ri-arrow-left-s-line ri-20px"></i>
                        </a>
                    </li>
                @endif

                {{-- Enlaces de Páginas --}}
                @php
                    $start = max(1, $paginator->currentPage() - 2);
                    $end = min($paginator->lastPage(), $paginator->currentPage() + 2);
                @endphp
                
                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($page) }}" wire:navigate>{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Botón Página Siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item next">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" wire:navigate>
                            <i class="ri ri-arrow-right-s-line ri-20px"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled next">
                        <span class="page-link">
                            <i class="ri ri-arrow-right-s-line ri-20px"></i>
                        </span>
                    </li>
                @endif

                {{-- Botón Última Página --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item last">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" wire:navigate>
                            <i class="ri ri-skip-forward-mini-line ri-20px"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled last">
                        <span class="page-link">
                            <i class="ri ri-skip-forward-mini-line ri-20px"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
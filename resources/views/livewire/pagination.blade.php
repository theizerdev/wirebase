@if ($paginator->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </div>
        
        <nav aria-label="Page navigation">
            <ul class="pagination">
                {{-- Botón Primera Página --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item first disabled">
                        <span class="page-link">
                            <i class="icon-base ri ri-skip-back-mini-line icon-22px"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item first">
                        <button type="button" class="page-link" wire:click="gotoPage(1)">
                            <i class="icon-base ri ri-skip-back-mini-line icon-22px"></i>
                        </button>
                    </li>
                @endif

                {{-- Botón Página Anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item prev disabled">
                        <span class="page-link">
                            <i class="icon-base ri ri-arrow-left-s-line icon-22px"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item prev">
                        <button type="button" class="page-link" wire:click="previousPage">
                            <i class="icon-base ri ri-arrow-left-s-line icon-22px"></i>
                        </button>
                    </li>
                @endif

                {{-- Números de Página --}}
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
                            <button type="button" class="page-link" wire:click="gotoPage({{ $page }})">{{ $page }}</button>
                        </li>
                    @endif
                @endfor

                {{-- Botón Página Siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item next">
                        <button type="button" class="page-link" wire:click="nextPage">
                            <i class="icon-base ri ri-arrow-right-s-line icon-22px"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item next disabled">
                        <span class="page-link">
                            <i class="icon-base ri ri-arrow-right-s-line icon-22px"></i>
                        </span>
                    </li>
                @endif

                {{-- Botón Última Página --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item last">
                        <button type="button" class="page-link" wire:click="gotoPage({{ $paginator->lastPage() }})">
                            <i class="icon-base ri ri-skip-forward-mini-line icon-22px"></i>
                        </button>
                    </li>
                @else
                    <li class="page-item last disabled">
                        <span class="page-link">
                            <i class="icon-base ri ri-skip-forward-mini-line icon-22px"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
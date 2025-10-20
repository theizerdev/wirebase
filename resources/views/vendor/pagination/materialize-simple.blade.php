@if ($paginator->hasPages())
  <nav class="d-flex justify-content-between align-items-center">
    <div>
      <p class="mb-0 text-muted">
        Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
      </p>
    </div>

    <div>
      <ul class="pagination mb-0">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
          <li class="page-item disabled">
            <span class="page-link">
              <i class="ri ri-arrow-left-s-line"></i>
            </span>
          </li>
        @else
          <li class="page-item">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
              <i class="ri ri-arrow-left-s-line"></i>
            </a>
          </li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
          <li class="page-item">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
              <i class="ri ri-arrow-right-s-line"></i>
            </a>
          </li>
        @else
          <li class="page-item disabled">
            <span class="page-link">
              <i class="ri ri-arrow-right-s-line"></i>
            </span>
          </li>
        @endif
      </ul>
    </div>
  </nav>
@endif

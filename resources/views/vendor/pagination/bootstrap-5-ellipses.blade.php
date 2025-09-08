@if ($paginator->hasPages())
<nav>
  <ul class="pagination mb-0">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
      <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
    @else
      <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
    @endif

    @php
      $total   = $paginator->lastPage();
      $current = $paginator->currentPage();
    @endphp

    @if ($total <= 5)
      @for ($i = 1; $i <= $total; $i++)
        <li class="page-item {{ $i == $current ? 'active' : '' }}">
          <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
        </li>
      @endfor
    @else
      {{-- awal --}}
      @if ($current <= 3)
        @for ($i = 1; $i <= 3; $i++)
          <li class="page-item {{ $i == $current ? 'active' : '' }}">
            <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
          </li>
        @endfor
        <li class="page-item disabled"><span class="page-link">…</span></li>
        <li class="page-item"><a class="page-link" href="{{ $paginator->url($total) }}">{{ $total }}</a></li>

      {{-- akhir --}}
      @elseif ($current >= $total - 2)
        <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
        <li class="page-item disabled"><span class="page-link">…</span></li>
        @for ($i = $total - 2; $i <= $total; $i++)
          <li class="page-item {{ $i == $current ? 'active' : '' }}">
            <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
          </li>
        @endfor

      {{-- tengah --}}
      @else
        <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
        <li class="page-item disabled"><span class="page-link">…</span></li>
        @for ($i = $current - 1; $i <= $current + 1; $i++)
          <li class="page-item {{ $i == $current ? 'active' : '' }}">
            <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
          </li>
        @endfor
        <li class="page-item disabled"><span class="page-link">…</span></li>
        <li class="page-item"><a class="page-link" href="{{ $paginator->url($total) }}">{{ $total }}</a></li>
      @endif
    @endif

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
    @else
      <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
    @endif
  </ul>
</nav>
@endif

@php
    use Illuminate\Support\Str;

    $badgeClass = function ($status) {
        $s = Str::lower($status ?? '');
        return match (true) {
            str_contains($s, 'selesai') => 'bg-success',
            str_contains($s, 'berjalan') => 'bg-primary',
            str_contains($s, 'batal') => 'bg-danger',
            default => 'bg-secondary',
        };
    };

    $progressPct = function ($delivered, $serve) {
        $s = (int) max(1, (int) $serve);
        $d = (int) max(0, (int) $delivered);
        return min(100, round(($d / $s) * 100));
    };

    $deltaWeight = function ($start, $end) {
        if ($start === null || $end === null) {
            return null;
        }
        $diff = round($end - $start, 1);
        // tampilkan + / -
        return ($diff > 0 ? '+' : '') . $diff . ' kg';
    };
@endphp

<div class="col-12">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">{{ $title }}</h5>
            @if (!empty($items))
                <span class="text-muted small">{{ count($items) }} entri</span>
            @endif
        </div>

        <div class="card-body p-0">
            @if (empty($items))
                <div class="p-4 text-center text-muted">
                    {{ $emptyText }}
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width:180px">Periode</th>
                                <th>Paket</th>
                                <th class="text-center">Progress</th>
                                <th class="text-center">Hari Terkirim</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $it)
                                @php
                                    $pct = $progressPct($it['delivered_days'] ?? 0, $it['serve_days'] ?? 0);
                                    $delta = $deltaWeight($it['weight_start'] ?? null, $it['weight_end'] ?? null);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $it['period'] ?? '-' }}</div>
                                        <div class="text-muted small">
                                            {{ \Carbon\Carbon::parse($it['start_date'] ?? '')->format('d M Y') }}
                                            – {{ \Carbon\Carbon::parse($it['end_date'] ?? '')->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            {{ $it['package'] ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center" style="min-width:180px">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="small text-muted mt-1">{{ $pct }}%</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold">
                                            {{ (int) ($it['delivered_days'] ?? 0) }}/{{ (int) ($it['serve_days'] ?? 0) }}
                                            hari
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass($it['status'] ?? '') }}">
                                            {{ $it['status'] ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

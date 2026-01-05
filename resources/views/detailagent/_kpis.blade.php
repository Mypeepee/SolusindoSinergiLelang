{{-- resources/views/owner/analitik/partials/agent/_kpis.blade.php --}}
@php
  $rupiahShort = function($n){
    $n = (float)$n; $abs = abs($n);
    if ($abs >= 1e12) return 'Rp ' . number_format($n/1e12, 2, ',', '.') . ' T';
    if ($abs >= 1e9)  return 'Rp ' . number_format($n/1e9,  2, ',', '.') . ' M';
    if ($abs >= 1e6)  return 'Rp ' . number_format($n/1e6,  1, ',', '.') . ' Jt';
    if ($abs >= 1e3)  return 'Rp ' . number_format($n/1e3,  1, ',', '.') . ' Rb';
    return 'Rp ' . number_format($n, 0, ',', '.');
  };

  $avgKenaikan = (float)($trxSummary->avg_kenaikan ?? 0);
@endphp

<div
  data-hero-meta
  data-omzet="{{ (int)($trxSummary->omzet ?? 0) }}"
  data-gross="{{ (int)($trxSummary->pendapatan_kotor ?? 0) }}"
  data-income="{{ (int)($incomeAgent ?? 0) }}"
  data-avg-kenaikan="{{ (float)($trxSummary->avg_kenaikan ?? 0) }}"
  data-trx-count="{{ (int)($trxSummary->trx_count ?? 0) }}"
></div>

<div class="row g-3">
  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Omzet</div>
      <div class="fs-4 fw-bold">{{ $rupiahShort($trxSummary->omzet ?? 0) }}</div>
      <div class="small text-muted">Σ harga_deal</div>
    </div>
  </div>

  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Pendapatan Kotor</div>
      <div class="fs-4 fw-bold">{{ $rupiahShort($trxSummary->pendapatan_kotor ?? 0) }}</div>
      <div class="small text-muted">Σ basis_pendapatan</div>
    </div>
  </div>

  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Income Agent</div>
      <div class="fs-4 fw-bold">{{ $rupiahShort($incomeAgent ?? 0) }}</div>
      <div class="small text-muted">Σ commissions (all roles)</div>
    </div>
  </div>

  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Jumlah Transaksi</div>
      <div class="fs-4 fw-bold">{{ number_format((int)($trxSummary->trx_count ?? 0), 0, ',', '.') }}</div>
      <div class="small text-muted">Count transaksi</div>
    </div>
  </div>

  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Avg Kenaikan dari Limit</div>
      <div class="fs-4 fw-bold">{{ number_format($avgKenaikan, 1, ',', '.') }}%</div>
      <div class="small text-muted">AVG kenaikan_dari_limit</div>
    </div>
  </div>

  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Total Cobroke</div>
      <div class="fs-4 fw-bold">{{ $rupiahShort($trxSummary->total_cobroke ?? 0) }}</div>
      <div class="small text-muted">Σ cobroke_fee</div>
    </div>
  </div>

  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Total Royalty</div>
      <div class="fs-4 fw-bold">{{ $rupiahShort($trxSummary->total_royalty ?? 0) }}</div>
      <div class="small text-muted">Σ royalty_fee</div>
    </div>
  </div>

  <div class="col-12 col-md-3">
    <div class="p-3 rounded-4 border bg-white h-100">
      <div class="small text-muted fw-semibold">Rekrut Langsung</div>
      <div class="fs-4 fw-bold">{{ number_format((int)($directRecruit ?? 0), 0, ',', '.') }}</div>
      <div class="small text-muted">Downline langsung aktif: {{ (int)($activeDownline ?? 0) }}</div>
    </div>
  </div>
</div>

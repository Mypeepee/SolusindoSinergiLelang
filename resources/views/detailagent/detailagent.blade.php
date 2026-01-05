{{-- resources/views/owner/analitik/detailagent.blade.php --}}
@include('template.header')
@php
  $agentId = $agent->id_agent ?? request()->route('id_agent');
  $agentName = $agent->nama ?? ('Agent ' . $agentId);
@endphp
<div class="container-fluid px-2 px-lg-3 pt-2" id="agent-detail-page"
     data-agent-id="{{ $agentId }}"
     data-year="{{ (int)$year }}"
     data-preset="{{ $preset ?? 'year' }}"
     data-start="{{ optional($start)->toDateString() }}"
     data-end="{{ optional($end)->toDateString() }}"
     data-widget-kpis="{{ route('dashboard.owner.analitik.agent.widgets.kpis', $agentId) }}"
     data-widget-trend="{{ route('dashboard.owner.analitik.agent.widgets.trend', $agentId) }}"
     data-widget-roles="{{ route('dashboard.owner.analitik.agent.widgets.roles', $agentId) }}"
     data-widget-transactions="{{ route('dashboard.owner.analitik.agent.widgets.transactions', $agentId) }}"
     data-trx-count="{{ $trxCount ?? 0 }}"
>

  {{-- =========================
   Sticky Top Bar (Improved)
========================= --}}
<div class="agent-sticky-topbar">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

      {{-- Left: back + breadcrumb --}}
      <div class="d-flex align-items-center gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary rounded-pill agent-back">
          <i class="bi bi-arrow-left"></i>
        </a>

        <div class="agent-breadcrumb">
          <div class="small text-muted">
            <span class="me-1">Analitik</span>
            <span class="mx-1">/</span>
            <span class="me-1">Agent</span>
            <span class="mx-1">/</span>
            <span class="fw-semibold text-dark">{{ $agentId }}</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <div class="fw-semibold text-dark" style="line-height:1.1;">{{ $agentName }}</div>
            @if(!empty($agent->status))
              <span class="badge rounded-pill {{ $agent->status === 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                {{ $agent->status }}
              </span>
            @endif
          </div>
        </div>
      </div>

      {{-- Right: preset + year + refresh --}}
      <div class="d-flex align-items-center gap-2">

        <div class="agent-year-pill">
          <i class="bi bi-calendar3"></i>
          <select class="form-select form-select-sm border-0 bg-transparent" id="agentYearSelect">
            @foreach(($availableYears ?? []) as $y)
              <option value="{{ $y }}" @selected((int)$year === (int)$y)>{{ $y }}</option>
            @endforeach
          </select>
        </div>

        <button class="btn btn-sm btn-dark rounded-pill" id="btnRefreshWidgets">
          <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
      </div>
    </div>
  </div>

  @php
  $rupiahShort = function($n){
    $n = (float)$n; $abs = abs($n);
    if ($abs >= 1e12) return 'Rp ' . number_format($n/1e12, 2, ',', '.') . ' T';
    if ($abs >= 1e9)  return 'Rp ' . number_format($n/1e9,  2, ',', '.') . ' M';
    if ($abs >= 1e6)  return 'Rp ' . number_format($n/1e6,  1, ',', '.') . ' Jt';
    if ($abs >= 1e3)  return 'Rp ' . number_format($n/1e3,  1, ',', '.') . ' Rb';
    return 'Rp ' . number_format($n, 0, ',', '.');
  };
@endphp

  {{-- =========================
   Hero Agent Card (Improved)
========================= --}}
@php
// Ambil fileId foto agent (sesuaikan field kamu)
$fileId = $agent->agent_picture ?? $agent->agent_picture_id ?? $agent->picture ?? null;

$agentImg = $fileId
  ? 'https://drive.google.com/thumbnail?id='.$fileId.'&sz=w256'
  : asset('images/default-profile.png');

$agentAlt = $fileId
  ? 'https://drive.google.com/uc?export=view&id='.$fileId
  : asset('images/default-profile.png');
@endphp

<div class="card border-0 shadow-sm agent-hero">
<div class="card-body">
  <div class="row g-3 align-items-center">

    {{-- Left: Avatar + Identity --}}
    <div class="col-12 col-lg-4">
      <div class="d-flex align-items-center gap-3">
        <div class="agent-avatar">
          <img
            src="{{ $agentImg }}"
            alt="{{ $agentName }}"
            loading="lazy"
            onload="this.nextElementSibling.style.display='none';"
            onerror="this.onerror=null;this.src='{{ $agentAlt }}';"
          />
          <span class="agent-initial">{{ strtoupper(substr($agentName,0,1)) }}</span>
        </div>

        <div class="flex-grow-1">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <h4 class="mb-0 fw-bold agent-title">{{ $agentName }}</h4>
            <span class="badge rounded-pill bg-light text-dark border">{{ $agentId }}</span>
          </div>

          <div class="small text-muted mt-1">
            @if($upline)
              Upline:
              <a class="text-decoration-none" href="{{ route('dashboard.owner.analitik.agent.show', $upline->id_agent) }}">
                <span class="fw-semibold">{{ $upline->nama }}</span> ({{ $upline->id_agent }})
              </a>
            @else
              Upline: <span class="text-muted">—</span>
            @endif
          </div>

          <div class="small text-muted mt-1">
            Periode aktif: <span id="agentRangeLabel" class="fw-semibold">—</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Right: Mini KPI --}}
    <div class="col-12 col-lg-8">
      <div class="row g-2">

        {{-- 5 KPI (responsive) --}}
        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="mini-kpi">
            <div class="mini-kpi-label">Omzet</div>
            <div class="mini-kpi-value kpi-skeleton loaded" id="heroOmzet">
                {{ $rupiahShort($trxSummary->omzet ?? 0) }}
              </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="mini-kpi">
            <div class="mini-kpi-label">Pendapatan Kotor</div>
            <div class="mini-kpi-value kpi-skeleton loaded" id="heroGross">
                {{ $rupiahShort($trxSummary->pendapatan_kotor ?? 0) }}
              </div>
          </div>
        </div>

        <div class="col-6 col-md-4 col-lg-2-4">
          <div class="mini-kpi">
            <div class="mini-kpi-label">Income Agent</div>
            <div class="mini-kpi-value kpi-skeleton loaded" id="heroIncome">
                {{ $rupiahShort($incomeAgent ?? 0) }}
              </div>
          </div>
        </div>

        <div class="col-6 col-md-6 col-lg-2-4">
          <div class="mini-kpi">
            <div class="mini-kpi-label">Avg Kenaikan</div>
            <div class="mini-kpi-value kpi-skeleton loaded" id="heroKenaikan">
                {{ number_format((float)($trxSummary->avg_kenaikan ?? 0), 1, ',', '.') }}%
              </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-lg-2-4">
          <div class="mini-kpi mini-kpi-strong">
            <div class="mini-kpi-label d-flex align-items-center gap-2">
              <i class="bi bi-receipt"></i>
              <span>Jumlah Transaksi</span>
            </div>
            <div class="mini-kpi-value kpi-skeleton loaded" id="heroTrxCount">
                {{ number_format((int)($trxSummary->trx_count ?? 0), 0, ',', '.') }}
              </div>
            <div class="mini-kpi-sub text-muted small">Per periode terpilih</div>
          </div>
        </div>

      </div>

      <div class="small text-muted mt-2 d-flex align-items-center gap-2">
        <i class="bi bi-lightning-charge"></i>
        <span>Tip: klik segment chart untuk filter tabel transaksi (next step tinggal wiring).</span>
      </div>
    </div>

  </div>
</div>
</div>

<style>
/* bikin 5 kolom rapi di lg tanpa pecah layout bootstrap */
@media (min-width: 992px){
  .col-lg-2-4{
    flex: 0 0 auto;
    width: 20%;
  }
}
.mini-kpi-sub{ margin-top: .15rem; }
.mini-kpi-strong{
  border-color: rgba(255,90,31,.22) !important;
  background: rgba(255,90,31,.04) !important;
}
</style>



  {{-- =========================
     Styles (Scoped)
  ========================= --}}
  <style>
    /* Rapatin jarak atas halaman */
    #agent-detail-page{ padding-top: .25rem !important; }

    .agent-sticky-topbar{
      position: sticky;
      top: 0;
      z-index: 30;
      padding: .40rem 0 !important;
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0,0,0,.06);
    }
    .agent-breadcrumb{ line-height: 1.1; }
    .agent-back{ width: 36px; height: 36px; display:flex; align-items:center; justify-content:center; }

    /* Segmented preset */
    .agent-seg .btn{
      border-radius: 999px;
      padding: .35rem .7rem;
    }
    .agent-seg .btn + .btn{ margin-left: .35rem; }
    .agent-seg .btn.active{
      background: #0b1f3a;
      border-color: #0b1f3a;
      color: #fff;
    }

    /* Year pill */
    .agent-year-pill{
      display:flex;
      align-items:center;
      gap:.5rem;
      padding:.25rem .65rem;
      border: 1px solid rgba(0,0,0,.15);
      border-radius: 999px;
      background: rgba(0,0,0,.02);
      height: 36px;
    }
    .agent-year-pill .form-select{
      padding: 0 .15rem 0 0;
      width: 92px;
      height: 28px;
      box-shadow: none !important;
      font-weight: 600;
    }
    .agent-year-pill i{ opacity:.75; }

    /* Hero */
    .agent-hero{
      border-radius: 18px;
      margin-top: .75rem !important;
    }
    .agent-title{ letter-spacing: -0.3px; }

    /* Avatar */
    .agent-avatar{
      width: 58px; height: 58px;
      border-radius: 18px;
      overflow: hidden;
      position: relative;
      background: #111;
      box-shadow: 0 8px 20px rgba(0,0,0,.08);
      border: 2px solid rgba(255,90,31,.15);
      flex: 0 0 auto;
    }
    .agent-avatar img{
      width: 100%;
      height: 100%;
      object-fit: cover;
      display:block;
    }
    .agent-avatar .agent-initial{
      position:absolute;
      inset:0;
      display:flex;
      align-items:center;
      justify-content:center;
      color:#fff;
      font-weight:800;
      font-size:20px;
    }

    /* Mini KPI - lebih premium */
    .mini-kpi{
      background: rgba(0,0,0,.02);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 14px;
      padding: .65rem .75rem;
      height: 100%;
    }
    .mini-kpi-label{
      font-size: .72rem;
      color:#6c757d;
      font-weight: 700;
      letter-spacing: .2px;
    }
    .mini-kpi-value{
      font-size: 1.02rem;
      font-weight: 900;
      color:#111;
      margin-top: .15rem;
    }

    /* Skeleton shimmer (hilang kalau sudah diisi angka) */
    .kpi-skeleton{
      position: relative;
    }
    .kpi-skeleton:empty,
    .kpi-skeleton[data-loading="1"],
    .kpi-skeleton:not(.loaded){
      color: transparent;
    }
    .kpi-skeleton:not(.loaded)::after{
      content:"";
      display:block;
      height: 14px;
      border-radius: 8px;
      background: linear-gradient(90deg, rgba(0,0,0,.06), rgba(0,0,0,.12), rgba(0,0,0,.06));
      background-size: 200% 100%;
      animation: shimmer 1.2s infinite;
      margin-top: .35rem;
    }
    @keyframes shimmer{
      0%{ background-position: 0% 0; }
      100%{ background-position: 200% 0; }
    }

    .agent-action{
      height: 36px;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: .35rem .75rem;
    }
  </style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
      const root = document.getElementById('agent-detail-page');
      const yearSel = document.getElementById('agentYearSelect');
      const btnRefresh = document.getElementById('btnRefreshWidgets');

      if (!root || !yearSel) return;

      const el = {
        omzet: document.getElementById('heroOmzet'),
        gross: document.getElementById('heroGross'),
        income: document.getElementById('heroIncome'),
        avg: document.getElementById('heroKenaikan'),
        trx: document.getElementById('heroTrxCount'),
      };

      function rupiahShort(n){
        n = Number(n || 0);
        const abs = Math.abs(n);
        const fmt = (x, d=1) => x.toLocaleString('id-ID', { maximumFractionDigits: d });
        if (abs >= 1e12) return 'Rp ' + fmt(n/1e12, 2) + ' T';
        if (abs >= 1e9)  return 'Rp ' + fmt(n/1e9,  2) + ' M';
        if (abs >= 1e6)  return 'Rp ' + fmt(n/1e6,  1) + ' Jt';
        if (abs >= 1e3)  return 'Rp ' + fmt(n/1e3,  1) + ' Rb';
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
      }

      function setLoaded(){
        Object.values(el).forEach(x => x && x.classList.add('loaded'));
      }

      async function refreshHeroKpis(){
        try{
          const qs = new URLSearchParams({
            preset: 'year',
            year: yearSel.value
          }).toString();

          const url = root.dataset.widgetKpis + '?' + qs;

          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          if(!res.ok) throw new Error('HTTP ' + res.status);

          const html = await res.text();

          const tmp = document.createElement('div');
          tmp.innerHTML = html;

          const meta = tmp.querySelector('[data-hero-meta]');
          if(!meta) throw new Error('meta-not-found');

          el.omzet.textContent  = rupiahShort(meta.dataset.omzet || 0);
          el.gross.textContent  = rupiahShort(meta.dataset.gross || 0);
          el.income.textContent = rupiahShort(meta.dataset.income || 0);
          el.avg.textContent    = (Number(meta.dataset.avgKenaikan || 0).toFixed(1)).replace('.', ',') + '%';
          el.trx.textContent    = Number(meta.dataset.trxCount || 0).toLocaleString('id-ID');

          setLoaded();
        }catch(err){
          console.error('[Agent KPI] gagal refresh:', err);
        }
      }

      // ganti tahun => refresh hero
      yearSel.addEventListener('change', refreshHeroKpis);

      // tombol refresh => refresh hero
      btnRefresh && btnRefresh.addEventListener('click', refreshHeroKpis);

      // initial load (biar selalu sync dengan filter)
      refreshHeroKpis();
    });
    </script>

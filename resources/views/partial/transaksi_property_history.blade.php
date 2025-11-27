@php
  // helper kecil: ambil foto pertama
  function ph_first_photo($gambar){
      $raw = (string) ($gambar ?? '');
      $arr = array_values(array_filter(array_map('trim', explode(',', $raw))));
      $first = $arr[0] ?? '';
      if ($first !== '' && preg_match('~^https?://~i', $first)) {
          return $first;
      } elseif ($first !== '') {
          return asset(ltrim($first, '/'));
      }
      return asset('img/placeholder.jpg');
  }
@endphp

@if($rows->isEmpty())
  <div class="small text-muted">
    Belum ada riwayat lelang lain untuk properti ini.
  </div>
@else
  <div class="tc-property-history-list">
    @foreach($rows as $idx => $item)
      @php
        $noLelang    = $idx + 1;
        $hargaMarkup = (float) ($item->harga ?? 0);
        $hargaLimit  = $hargaMarkup > 0 ? round($hargaMarkup / 1.278) : 0;
        $thumb       = ph_first_photo($item->gambar ?? '');
        $tglLelang   = $item->batas_akhir_penawaran
                       ?? $item->tanggal_buyer_meeting
                       ?? $item->tanggal_dibuat;
      @endphp

      <div class="tc-property-history-item">
        <div class="tc-ph-header d-flex justify-content-between align-items-center mb-1">
          <div class="tc-ph-badge">Lelang ke-{{ $noLelang }}</div>
          <div class="small text-muted">
            ID: {{ $item->id_listing }}
          </div>
        </div>

        <div class="row g-2 align-items-center">
          <div class="col-auto">
            <div class="tc-ph-thumb">
              <img src="{{ $thumb }}"
                   alt="Lelang ke-{{ $noLelang }}"
                   loading="lazy">
            </div>
          </div>
          <div class="col">
            <div class="small text-muted mb-1">
              Tanggal lelang:
              @if($tglLelang)
                {{ \Carbon\Carbon::parse($tglLelang)->format('d M Y') }}
              @else
                -
              @endif
            </div>
            <div class="small">
              <span class="text-muted">Harga limit:</span>
              <span class="fw-semibold">
                Rp {{ number_format($hargaLimit, 0, ',', '.') }}
              </span>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

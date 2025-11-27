{{-- partial/transaksi_list.blade.php --}}
{{-- Transaksi fragment (AJAX-only, mirror style Stoker, fokus ke harga web & harga limit) --}}

<div id="transaksi-list-inner">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Gambar</th>
            <th>Lokasi</th>
            <th>Tipe</th>
            <th>Luas (m²)</th>
            <th>Harga</th>
            <th>Closing</th>
          </tr>
        </thead>
        <tbody>
            @forelse($transaksiProperties as $row)
            @php
              // 1) Harga: web (markup 27,8%) & limit
              $hargaMarkup = (float) ($row->harga ?? 0);          // harga web (sudah naik 27,8%)
              $hargaLimit  = $hargaMarkup > 0 ? round($hargaMarkup / 1.278) : 0; // kembalikan ke limit

              // 2) Foto utama: dipakai di tabel + dikirim ke modal
              $rawGambar = (string) ($row->gambar ?? '');
              $fotoArray = array_values(array_filter(array_map('trim', explode(',', $rawGambar))));
              $fotoUtama = $fotoArray[0] ?? '';

              if ($fotoUtama !== '' && preg_match('~^https?://~i', $fotoUtama)) {
                  $thumbSrc = $fotoUtama;
              } elseif ($fotoUtama !== '') {
                  $thumbSrc = asset(ltrim($fotoUtama, '/'));
              } else {
                  $thumbSrc = asset('img/placeholder.jpg');
              }
            @endphp

            <tr>
              {{-- kolom ID --}}
              <td class="fw-semibold">{{ $row->id_listing }}</td>

              {{-- kolom Gambar --}}
              <td>
                <img src="{{ $thumbSrc }}"
                     alt="thumb {{ $row->id_listing }}"
                     class="img-thumbnail"
                     style="width:64px;height:64px;object-fit:cover"
                     loading="lazy">
              </td>

              {{-- kolom Lokasi --}}
              <td class="text-start" style="max-width:420px">{{ $row->lokasi }}</td>

              {{-- kolom Tipe --}}
              <td>{{ ucfirst($row->tipe) }}</td>

              {{-- kolom Luas --}}
              <td>{{ $row->luas ?? '-' }}</td>

              {{-- kolom Harga (web + limit) --}}
              <td class="text-end">
                <div class="fw-semibold">
                  Rp {{ number_format($hargaMarkup, 0, ',', '.') }}
                </div>
                <div class="small text-muted">
                  Limit: Rp {{ number_format($hargaLimit, 0, ',', '.') }}
                </div>
              </td>

              {{-- kolom Closing --}}
              <td class="text-center">
                @php
                  $statusLower = strtolower(trim($row->status ?? ''));
                  $badgeClass  = 'bg-secondary';
                  if (in_array($statusLower, ['closing','kuitansi','kode billing'])) {
                    $badgeClass = 'bg-warning text-dark';
                  } elseif (in_array($statusLower, ['kutipan risalah lelang','akte grosse'])) {
                    $badgeClass = 'bg-info text-dark';
                  } elseif (in_array($statusLower, ['balik nama','eksekusi pengosongan','selesai'])) {
                    $badgeClass = 'bg-success';
                  }
                  $isDone = in_array($statusLower, ['selesai']);
                @endphp

                <button type="button"
                    class="btn btn-sm {{ $isDone ? 'btn-outline-success' : 'btn-success' }} rounded-pill btn-transaksi-closing"
                    data-id-listing="{{ $row->id_listing }}"
                    data-id-transaksi="{{ $row->id_transaksi ?? '' }}"
                    data-status="{{ $row->status ?? '' }}"
                    data-lokasi="{{ $row->lokasi }}"
                    data-tipe="{{ $row->tipe }}"
                    data-harga-markup="{{ $hargaMarkup }}"
                    data-harga-limit="{{ $hargaLimit }}"
                    data-gambar="{{ $row->gambar ?? '' }}"
                    data-photo="{{ $thumbSrc }}"
                    data-copic-name="{{ $row->agent_nama ?? '' }}"  {{-- CO PIC: nama agent --}}
                    {{ $isDone ? 'disabled' : '' }}>
                    {{ $isDone ? 'Selesai' : 'Closing' }}
                </button>


                <div class="small text-muted mt-1">
                  Status:
                  <span class="badge {{ $badgeClass }}">{{ $row->status ?? '-' }}</span>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">Tidak ada transaksi ditemukan.</td>
          @endforelse

        </tbody>
      </table>
    </div>

    {{-- Pagination (AJAX links, mirror Stoker tapi prefix transaksi) --}}
    <div class="col-12">
      <div class="pagination d-flex justify-content-center mt-4 gap-1 overflow-auto">
        @php
          $currentPage = $transaksiProperties->currentPage();
          $lastPage    = $transaksiProperties->lastPage();
          $start       = max($currentPage - 2, 1);
          $end         = min($currentPage + 2, $lastPage);
        @endphp

        {{-- Tombol Previous --}}
        @if ($transaksiProperties->onFirstPage())
          <a href="#" class="btn btn-sm btn-light rounded disabled" tabindex="-1" aria-disabled="true">&laquo;</a>
        @else
          <a href="#" class="btn btn-sm btn-light rounded js-transaksi-page" data-page="{{ $currentPage-1 }}">&laquo;</a>
        @endif

        {{-- Halaman awal + ... --}}
        @if ($start > 1)
          <a href="#" class="btn btn-sm btn-light rounded js-transaksi-page" data-page="1">1</a>
          @if ($start > 2)
            <span class="btn btn-sm btn-light rounded disabled">...</span>
          @endif
        @endif

        {{-- Range halaman di sekitar current --}}
        @for ($i = $start; $i <= $end; $i++)
          <a href="#"
             class="btn btn-sm rounded {{ $i === $currentPage ? 'btn-primary text-white' : 'btn-light' }} js-transaksi-page"
             data-page="{{ $i }}">{{ $i }}</a>
        @endfor

        {{-- Halaman akhir + ... --}}
        @if ($end < $lastPage)
          @if ($end < $lastPage - 1)
            <span class="btn btn-sm btn-light rounded disabled">...</span>
          @endif
          <a href="#" class="btn btn-sm btn-light rounded js-transaksi-page" data-page="{{ $lastPage }}">{{ $lastPage }}</a>
        @endif

        {{-- Tombol Next --}}
        @if ($transaksiProperties->hasMorePages())
          <a href="#" class="btn btn-sm btn-light rounded js-transaksi-page" data-page="{{ $currentPage+1 }}">&raquo;</a>
        @else
          <a href="#" class="btn btn-sm btn-light rounded disabled" tabindex="-1" aria-disabled="true">&raquo;</a>
        @endif
      </div>
    </div>
  </div>

  <script>
    // Binding default tombol closing -> lempar ke handler global kalau ada
    (function(){
      const btns = document.querySelectorAll('#transaksi-list-inner .btn-transaksi-closing');
      btns.forEach(btn => {
        btn.addEventListener('click', function(){
          const payload = {
            id_listing  : this.dataset.idListing,
            id_transaksi: this.dataset.idTransaksi || null,
            status      : this.dataset.status || null,
            lokasi      : this.dataset.lokasi || '',
            harga_markup: Number(this.dataset.hargaMarkup || 0),
            harga_limit : Number(this.dataset.hargaLimit  || 0),

            // >>> RAW dari kolom gambar + URL thumb yang sudah jadi <<<
            gambar      : (this.dataset.gambar || '').trim(),
            photo       : (this.dataset.photo  || '').trim(),

            copic_name  : (this.dataset.copicName || this.dataset.copic || '').trim()
          };

          console.log('DEBUG CLOSING BUTTON', payload); // bantu debugging

          if (window.handleTransaksiClosingClick) {
            try { window.handleTransaksiClosingClick(payload, this); } catch(e){ console.error(e); }
          } else {
            console.log('Closing clicked (no handler):', payload);
          }
        });
      });
    })();

    if (window.afterTransaksiListReplaced) {
      try { window.afterTransaksiListReplaced(); } catch(e) { console.error(e); }
    }
  </script>





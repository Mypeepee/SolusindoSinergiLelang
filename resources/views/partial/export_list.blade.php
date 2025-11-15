{{-- export_list.blade.php (PARTIAL SAJA: TABEL + PAGINATION) --}}

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-light">
        <tr>
          <th style="width:36px;">
            <input type="checkbox" id="check_all_export">
          </th>
          <th>ID</th>
          <th>Gambar</th>
          <th>Lokasi</th>
          <th>Tipe</th>
          <th>Luas (m²)</th>
          <th>Harga</th>
        </tr>
      </thead>
      <tbody>
        @forelse($exportProperties as $property)
          <tr class="{{ !empty($property->exported) && $property->exported ? 'table-warning row-exported' : '' }}"
              data-exported="{{ !empty($property->exported) && $property->exported ? 1 : 0 }}">
            <td>
              <input type="checkbox" class="row-check" value="{{ $property->id_listing }}">
            </td>
            <td class="fw-semibold">
              {{ $property->id_listing }}
              @if(!empty($property->exported) && $property->exported)
                <span class="badge bg-warning text-dark ms-1">Exported</span>
              @endif
            </td>
            <td>
              @php
                $fotoList   = array_values(array_filter(array_map('trim', explode(',', (string)$property->gambar))));
                $fotoUtama  = $fotoList[0] ?? '';
                $isAbsolute = $fotoUtama && preg_match('~^(https?:)?//~', $fotoUtama);
                $src        = $isAbsolute ? $fotoUtama : ($fotoUtama ? asset(ltrim($fotoUtama, '/')) : '');
              @endphp
              <img src="{{ $src ?: asset('img/placeholder.jpg') }}" alt="thumb {{ $property->id_listing }}"
                   class="img-thumbnail" style="width:72px;height:72px;object-fit:cover" loading="lazy">
            </td>
            <td class="text-start" style="max-width:420px">{{ $property->lokasi }}</td>
            <td>{{ ucfirst($property->tipe) }}</td>
            <td>{{ $property->luas ?? '-' }}</td>
            <td>Rp {{ number_format($property->harga, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">Tidak ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="col-12">
    @php
      $currentPage = $exportProperties->currentPage();
      $lastPage    = $exportProperties->lastPage();
      $start       = max($currentPage - 2, 1);
      $end         = min($currentPage + 2, $lastPage);
    @endphp

    <div class="d-flex align-items-center mt-2 gap-2">
      {{-- LEFT: input lompat halaman --}}
      <div class="d-flex align-items-center" style="min-width: 180px;">
        <div class="input-group input-group-sm" style="width: 150px;">
          <span class="input-group-text">Hal</span>
          <input id="page-jump-inline" type="number" min="1" step="1"
                 class="form-control"
                 placeholder="Nomor Halaman"
                 title="Masukkan nomor halaman untuk lompat">
        </div>
      </div>

      {{-- CENTER: pagination --}}
      <div class="pagination d-flex justify-content-center gap-1 overflow-auto flex-grow-1">

        @if ($exportProperties->onFirstPage())
          <a href="#" class="btn btn-sm btn-light rounded disabled" tabindex="-1" aria-disabled="true">&laquo;</a>
        @else
          <a href="#" class="btn btn-sm btn-light rounded js-export-page" data-page="{{ $currentPage-1 }}">&laquo;</a>
        @endif

        @if ($start > 1)
          <a href="#" class="btn btn-sm btn-light rounded js-export-page" data-page="1">1</a>
          @if ($start > 2)
            <span class="btn btn-sm btn-light rounded disabled">...</span>
          @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
          <a href="#"
             class="btn btn-sm rounded {{ $i === $currentPage ? 'btn-primary text-white' : 'btn-light' }} js-export-page"
             data-page="{{ $i }}">{{ $i }}</a>
        @endfor

        @if ($end < $lastPage)
          @if ($end < $lastPage - 1)
            <span class="btn btn-sm btn-light rounded disabled">...</span>
          @endif
          <a href="#" class="btn btn-sm btn-light rounded js-export-page" data-page="{{ $lastPage }}">{{ $lastPage }}</a>
        @endif

        @if ($exportProperties->hasMorePages())
          <a href="#" class="btn btn-sm btn-light rounded js-export-page" data-page="{{ $currentPage+1 }}">&raquo;</a>
        @else
          <a href="#" class="btn btn-sm btn-light rounded disabled" tabindex="-1" aria-disabled="true">&raquo;</a>
        @endif

      </div>

      {{-- RIGHT: white spot --}}
      <div class="flex-grow-1"></div>
    </div>
  </div>

  <script>
    (function () {
      const container = document.getElementById('export-list-inner');
      if (!container) return;

      // Panggil loader milikmu, apapun nama fungsinya
      function callLoad(page) {
        if (typeof window.__loadExportList === 'function') return window.__loadExportList({ page });
        if (typeof window.loadList === 'function') return window.loadList({ page });
        console.warn('loadList/__loadExportList tidak ditemukan');
      }

      // Ambil lastPage bila ada data-last="..." atau dari placeholder "cur/last"
      function getLastPageFrom(el) {
        const d = el.dataset?.last || el.getAttribute('data-last');
        if (d && /^\d+$/.test(d)) return parseInt(d, 10);
        const ph = el.getAttribute('placeholder') || '';
        const m = ph.match(/\/\s*(\d+)/); // contoh "3924/5000"
        if (m) return parseInt(m[1], 10);
        return null; // tak diketahui, biarkan apa adanya
      }

      let timer = null;
      let lastRequested = null;

      // Ketik: auto-jump dengan debounce
      container.addEventListener('input', function (e) {
        const el = e.target.closest('#page-jump-inline');
        if (!el) return;

        // sanitize: angka saja
        const cleaned = (el.value || '').replace(/[^\d]/g, '');
        if (el.value !== cleaned) el.value = cleaned;

        clearTimeout(timer);
        timer = setTimeout(() => {
          if (!cleaned) return;
          let n = parseInt(cleaned, 10);
          if (!Number.isNaN(n) && n > 0) {
            const last = getLastPageFrom(el);
            if (last && n > last) n = last; // clamp jika tahu lastPage
            if (n !== lastRequested) {
              lastRequested = n;
              callLoad(n);
            }
          }
        }, 300); // debounce 300ms biar mulus saat ngetik "3000"
      });

      // Paste/blur: pastikan tetap lompat
      container.addEventListener('change', function (e) {
        const el = e.target.closest('#page-jump-inline');
        if (!el) return;
        const cleaned = (el.value || '').replace(/[^\d]/g, '');
        let n = parseInt(cleaned, 10);
        if (!Number.isNaN(n) && n > 0) {
          const last = getLastPageFrom(el);
          if (last && n > last) n = last;
          if (n !== lastRequested) {
            lastRequested = n;
            callLoad(n);
          }
        }
      });

      // Opsional: cegah scroll wheel mengubah angka saat fokus
      container.addEventListener('wheel', function (e) {
        if (e.target && e.target.id === 'page-jump-inline' && document.activeElement === e.target) {
          e.preventDefault();
        }
      }, { passive: false });
    })();
    </script>


  <style>
    tr.row-exported.table-warning {
      --bs-table-bg: #fff7d6;
      --bs-table-striped-bg: #fff3c2;
      --bs-table-hover-bg: #ffefad;
    }
  </style>

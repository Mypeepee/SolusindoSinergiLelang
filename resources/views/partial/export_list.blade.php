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
      $totalItems  = $exportProperties->total(); // total semua hasil filter
    @endphp

    <div id="export-paginate-row" class="d-flex align-items-center mt-2 gap-2"
         data-total="{{ $totalItems }}">
      {{-- LEFT: input lompat halaman (punyamu) --}}
      <div class="d-flex align-items-center" style="min-width: 180px;">
        <div class="input-group input-group-sm" style="width: 150px;">
          <span class="input-group-text">Hal</span>
          <input id="page-jump-inline" type="number" min="1" step="1"
                 class="form-control"
                 placeholder="Nomor Halaman"
                 title="Masukkan nomor halaman untuk lompat">
        </div>
      </div>

      {{-- CENTER: pagination (punyamu) --}}
      <div class="pagination d-flex justify-content-center gap-1 overflow-auto flex-grow-1">
        {{-- ... pagination kamu seperti semula ... --}}
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

      {{-- RIGHT: tombol “Pilih semua” --}}
      <div class="d-flex align-items-center gap-2">
        <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" role="switch"
                 id="select-all-across">
          <label class="form-check-label small" for="select-all-across">
            Pilih semua ({{ number_format($totalItems, 0, ',', '.') }})
          </label>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const CONTAINER_ID = 'export-list-inner';      // container partial
      const PAGINATE_ROW_ID = 'export-paginate-row'; // wrapper row yang kamu pakai
      const SELECT_ALL_TOGGLE = '#select-all-across';
      const PAGE_JUMP_INPUT = '#page-jump-inline';

      const KEY_SELECT_ALL = 'exportSelectAllAcross';

      // helper: panggil loader milikmu
      function callLoad(page){
        if (typeof window.__loadExportList === 'function') return window.__loadExportList({ page });
        if (typeof window.loadList === 'function') return window.loadList({ page });
        console.warn('loadList/__loadExportList tidak ditemukan');
      }

      // ====== SELECT ALL (semua halaman) ======
      function isSelectAllOn(){ return localStorage.getItem(KEY_SELECT_ALL) === '1'; }
      function setSelectAll(on){
        localStorage.setItem(KEY_SELECT_ALL, on ? '1' : '0');

        const container = document.getElementById(CONTAINER_ID);
        if (!container) return;
        // sinkronkan toggle
        const toggle = container.querySelector(SELECT_ALL_TOGGLE);
        if (toggle) toggle.checked = !!on;

        // centang/bersihkan yang tampil sekarang (visual)
        container.querySelectorAll('.row-check').forEach(cb => cb.checked = !!on);

        // tombol export aktif bila ON
        const btnCSV = document.getElementById('btn-export-csv');
        const btnLET = document.getElementById('btn-export-letters');
        if (btnCSV) btnCSV.disabled = false;
        if (btnLET) btnLET.disabled = false;

        // update master checkbox
        const master = container.querySelector('#check_all_export');
        if (master){
          master.checked = !!on;
          master.indeterminate = false;
        }

        // update counter (kalau ada data-total di row)
        const row = document.getElementById(PAGINATE_ROW_ID);
        const total = row?.dataset?.total ? parseInt(row.dataset.total, 10) : null;
        document.querySelectorAll('#export-selected-counter').forEach(el => {
          el.textContent = on ? (total ? `Semua (${total})` : 'Semua') : `${document.querySelectorAll('.row-check:checked').length} dipilih`;
        });
      }

      // Saat submit, isi hidden `select_all`
      function syncSubmitFlag(){
        const form = document.getElementById('export-form');
        if (!form) return;
        form.addEventListener('submit', () => {
          const inputFlag = document.getElementById('select_all_input');
          if (inputFlag) inputFlag.value = isSelectAllOn() ? '1' : '0';
          if (isSelectAllOn()){
            // jika pilih semua aktif, kosongkan selected_ids; backend pakai filter + select_all
            const selectedInput = document.getElementById('selected_ids_input');
            if (selectedInput) selectedInput.value = '';
          }
        });
      }

      // ====== PAGE JUMP (tanpa enter) ======
      let jumpTimer = null;
      function attachPageJump(scope){
        const field = scope.querySelector(PAGE_JUMP_INPUT);
        if (!field) return;

        const lastPage = (scope.dataset && scope.dataset.totalPages) ? parseInt(scope.dataset.totalPages,10) : null;

        function doJump(){
          const cleaned = (field.value || '').replace(/[^\d]/g, '');
          if (!cleaned) return;
          let n = parseInt(cleaned, 10);
          if (Number.isNaN(n) || n < 1) return;
          // clamp kalau kamu mau; jika tidak tahu lastPage biarkan saja
          callLoad(n);
        }

        // input: debounce biar gak spam saat mengetik
        field.addEventListener('input', () => {
          const cleaned = field.value.replace(/[^\d]/g,'');
          if (field.value !== cleaned) field.value = cleaned;
          clearTimeout(jumpTimer);
          jumpTimer = setTimeout(doJump, 260);
        });

        // paste/blur fallback
        field.addEventListener('change', doJump);

        // optional: auto-select saat fokus
        field.addEventListener('focus', () => field.select());
      }

      // ====== WIRE PARTIAL (karena partial diganti2) ======
      function hydratePartial(){
        const container = document.getElementById(CONTAINER_ID);
        if (!container) return;

        const row = document.getElementById(PAGINATE_ROW_ID);
        if (row){
          // page jump
          attachPageJump(row);

          // select-all toggle
          const toggle = container.querySelector(SELECT_ALL_TOGGLE);
          if (toggle){
            toggle.checked = isSelectAllOn();
            // bind ulang listener (hapus dulu agar tidak double)
            toggle.addEventListener('change', (e) => {
              setSelectAll(e.target.checked);
            }, { once:false });
          }

          // kalau ON, centang semua di halaman aktif + aktifkan tombol
          if (isSelectAllOn()){
            setSelectAll(true);
          }
        }
      }

      // Panggil saat pertama + tiap partial berubah (pakai MutationObserver agar tanpa ubah script lain)
      function bootObserver(){
        const target = document.getElementById(CONTAINER_ID);
        if (!target) return;
        const obs = new MutationObserver((muts) => {
          // ketika pagination/table diganti
          hydratePartial();
        });
        obs.observe(target, { childList: true, subtree: true });
        // initial
        hydratePartial();
      }

      // start
      syncSubmitFlag();
      bootObserver();
    })();
    </script>


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

{{-- STYLE untuk spinner loading --}}
<style>
    /* Spinner overlay minimalis */
    #export-list-wrap { position: relative; }
    .export-loading {
      position: absolute; inset: 0;
      display: flex; align-items: center; justify-content: center;
      background: rgba(255,255,255,.6);
      backdrop-filter: saturate(120%) blur(1px);
      z-index: 3;
    }
    .export-loading.d-none { display: none; }
</style>

<div id="export-list-wrap">
  <!-- Overlay spinner (tetap ada, tidak ikut terganti oleh partial) -->
  <div id="export-loading" class="export-loading d-none">
    <div class="spinner-border" role="status" aria-label="Loading"></div>
  </div>

  <!-- Ini yang akan kamu replace via AJAX -->
  <div id="export-list-inner">
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
            <tr>
              <td><input type="checkbox" class="row-check" value="{{ $property->id_listing }}"></td>
              <td class="fw-semibold">{{ $property->id_listing }}</td>
              <td>
                @php
                  $fotoList   = array_values(array_filter(array_map('trim', explode(',', (string)$property->gambar))));
                  $fotoUtama  = $fotoList[0] ?? '';
                  $isAbsolute = $fotoUtama && preg_match('~^(https?:)?//~', $fotoUtama);
                  $src        = $isAbsolute ? $fotoUtama : ($fotoUtama ? asset(ltrim($fotoUtama, '/')) : '');
                @endphp
                <img src="{{ $src ?: asset('img/placeholder.jpg') }}" alt="thumb {{ $property->id_listing }}" class="img-thumbnail" style="width:72px;height:72px;object-fit:cover" loading="lazy">
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

{{-- Pagination (AJAX-only) --}}
<div class="col-12">
    <div class="pagination d-flex justify-content-center mt-2 gap-1 overflow-auto">
      @php
        $currentPage = $exportProperties->currentPage();
        $lastPage    = $exportProperties->lastPage();
        $start       = max($currentPage - 2, 1);
        $end         = min($currentPage + 2, $lastPage);
      @endphp

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
  </div>
  </div>
</div>

<script>
/**
 * ==== Persist & Sync Selection di Partial ====
 * - localStorage key: 'export_selected_ids'
 * - Update counter #export-selected-counter
 * - Update hidden input #selected_ids_input
 * - Support "check all" halaman aktif
 */
(function(){
  const STORAGE_KEY = 'export_selected_ids';

  function getSelected() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'); }
    catch { return []; }
  }
  function setSelected(arr) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(new Set(arr))));
  }
  function updateCounterAndHidden() {
    const ids = getSelected();
    const counter = document.getElementById('export-selected-counter');
    if (counter) counter.textContent = (ids.length || 0) + ' dipilih';
    const hidden = document.getElementById('selected_ids_input');
    if (hidden) hidden.value = ids.join(',');
  }

  // Dipanggil setelah partial diganti via AJAX
  window.initExportSelection = function initExportSelection() {
    const ids = new Set(getSelected());

    // Pre-check checkbox baris jika masuk list
    document.querySelectorAll('#export-list-inner .row-check').forEach(cb => {
      cb.checked = ids.has(String(cb.value));
    });

    // Master check-all refleksikan status halaman
    const all = document.getElementById('check_all_export');
    if (all) {
      const rows = Array.from(document.querySelectorAll('#export-list-inner .row-check'));
      all.checked = rows.length > 0 && rows.every(cb => cb.checked);
      all.indeterminate = rows.some(cb => cb.checked) && !all.checked;

      all.onchange = function(){
        const now = this.checked;
        const current = new Set(getSelected());
        rows.forEach(cb => {
          cb.checked = now;
          if (now) current.add(String(cb.value));
          else     current.delete(String(cb.value));
        });
        setSelected(Array.from(current));
        updateCounterAndHidden();
      };
    }

    // Dengarkan perubahan tiap row
    document.querySelectorAll('#export-list-inner .row-check').forEach(cb => {
      cb.addEventListener('change', function(){
        const current = new Set(getSelected());
        const val = String(this.value);
        if (this.checked) current.add(val); else current.delete(val);
        setSelected(Array.from(current));
        updateCounterAndHidden();

        // refresh state check-all
        const rows = Array.from(document.querySelectorAll('#export-list-inner .row-check'));
        const all2 = document.getElementById('check_all_export');
        if (all2) {
          all2.checked = rows.length > 0 && rows.every(x => x.checked);
          all2.indeterminate = rows.some(x => x.checked) && !all2.checked;
        }
      });
    });

    updateCounterAndHidden();
  };

  // Inisialisasi pertama kali saat halaman pertama render
  document.addEventListener('DOMContentLoaded', function(){
    if (window.initExportSelection) window.initExportSelection();

    // Delegasi klik pagination agar AJAX only
    document.getElementById('export-list-wrap')?.addEventListener('click', function(e){
      const a = e.target.closest('a.js-export-page');
      if (!a) return;
      e.preventDefault();
      const form    = document.getElementById('export-filter-form');
      const input   = document.getElementById('search_exp');
      const selType = document.getElementById('property_type_exp');
      const selProv = document.getElementById('province-export');
      const selCity = document.getElementById('city-export');
      const selDist = document.getElementById('district-export');
      const overlay = document.getElementById('export-loading');

      let ctrl;
      const fragmentRoute = "{{ route('dashboard.owner.export.list') }}";
      function qs(obj){
        const p = new URLSearchParams(obj);
        return p.toString();
      }
      function paramsObj(merge = {}) {
        return {
          tab: 'export',
          search: input?.value || '',
          property_type: selType?.value || '',
          province: selProv?.value || '',
          city: selCity?.value || '',
          district: selDist?.value || '',
          page: 1,
          ...merge
        };
      }
      async function loadList(extra = {}) {
        if (ctrl) ctrl.abort();
        ctrl = new AbortController();
        overlay?.classList.remove('d-none');
        try {
          const url = fragmentRoute + '?' + qs(paramsObj(extra));
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: ctrl.signal });
          const html = await res.text();
          document.getElementById('export-list-inner').innerHTML = html;
          if (window.initExportSelection) window.initExportSelection();
        } catch(err) {
          if (err.name !== 'AbortError') console.error(err);
        } finally {
          overlay?.classList.add('d-none');
        }
      }

      loadList({ page: a.dataset.page || 1 });
    });
  });
})();
</script>

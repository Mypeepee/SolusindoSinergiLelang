{{-- partial/stoker_list.blade.php --}}
{{-- Stoker fragment (AJAX-only, pakai checkbox, tanpa kolom Aksi) --}}

<div id="stoker-list-inner">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-light">
          <tr>
            <th style="width:36px;">
              <input type="checkbox" id="check_all_stoker">
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
        @forelse($stokerProperties as $property)
          <tr>
            {{-- checkbox per baris --}}
            <td>
              <input type="checkbox" class="row-check" value="{{ $property->id_listing }}">
            </td>

            {{-- kolom ID --}}
            <td class="fw-semibold">{{ $property->id_listing }}</td>

            {{-- kolom Gambar --}}
            <td>
              @php
                $fotoArray = array_values(array_filter(array_map('trim', explode(',', (string)$property->gambar))));
                $fotoUtama = $fotoArray[0] ?? '';
                $isAbs     = $fotoUtama && preg_match('~^(https?:)?//~', $fotoUtama);
                $src       = $isAbs ? $fotoUtama : ($fotoUtama ? asset(ltrim($fotoUtama,'/')) : asset('img/placeholder.jpg'));
              @endphp
              <img src="{{ $src }}" alt="thumb {{ $property->id_listing }}"
                   class="img-thumbnail" style="width:72px;height:72px;object-fit:cover" loading="lazy">
            </td>

            {{-- kolom Lokasi --}}
            <td class="text-start" style="max-width:420px">{{ $property->lokasi }}</td>

            {{-- kolom Tipe --}}
            <td>{{ ucfirst($property->tipe) }}</td>

            {{-- kolom Luas --}}
            <td>{{ $property->luas ?? '-' }}</td>

            {{-- kolom Harga --}}
            <td>Rp {{ number_format($property->harga, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center">Tidak ada data ditemukan.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination (AJAX links) --}}
    <div class="col-12">
      <div class="pagination d-flex justify-content-center mt-4 gap-1 overflow-auto">
        @php
          $currentPage = $stokerProperties->currentPage();
          $lastPage    = $stokerProperties->lastPage();
          $start       = max($currentPage - 2, 1);
          $end         = min($currentPage + 2, $lastPage);
        @endphp

        @if ($stokerProperties->onFirstPage())
          <a href="#" class="btn btn-sm btn-light rounded disabled" tabindex="-1" aria-disabled="true">&laquo;</a>
        @else
          <a href="#" class="btn btn-sm btn-light rounded js-stoker-page" data-page="{{ $currentPage-1 }}">&laquo;</a>
        @endif

        @if ($start > 1)
          <a href="#" class="btn btn-sm btn-light rounded js-stoker-page" data-page="1">1</a>
          @if ($start > 2)
            <span class="btn btn-sm btn-light rounded disabled">...</span>
          @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
          <a href="#"
             class="btn btn-sm rounded {{ $i === $currentPage ? 'btn-primary text-white' : 'btn-light' }} js-stoker-page"
             data-page="{{ $i }}">{{ $i }}</a>
        @endfor

        @if ($end < $lastPage)
          @if ($end < $lastPage - 1)
            <span class="btn btn-sm btn-light rounded disabled">...</span>
          @endif
          <a href="#" class="btn btn-sm btn-light rounded js-stoker-page" data-page="{{ $lastPage }}">{{ $lastPage }}</a>
        @endif

        @if ($stokerProperties->hasMorePages())
          <a href="#" class="btn btn-sm btn-light rounded js-stoker-page" data-page="{{ $currentPage+1 }}">&raquo;</a>
        @else
          <a href="#" class="btn btn-sm btn-light rounded disabled" tabindex="-1" aria-disabled="true">&raquo;</a>
        @endif
      </div>
    </div>
  </div>

  {{-- panggil hook agar checkbox terhidrasi ulang setelah partial direplace --}}
  <script>
    if (window.afterStokerListReplaced) {
      try { window.afterStokerListReplaced(); } catch(e) { console.error(e); }
    }
  </script>

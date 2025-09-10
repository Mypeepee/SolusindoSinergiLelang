@include('template.header')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg z-3" role="alert" style="min-width: 300px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-success alert-danger fade show position-fixed top-0 end-0 m-3 shadow-lg z-3" role="alert" style="min-width: 300px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- <div class="container-fluid px-4 my-0">
<div class="row mb-2">
    <div class="col-auto">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
</div> --}}

<div class="container-fluid px-4 my-4">
    <div class="row">
        <!-- LEFT: List Property -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded mb-4">
                <!-- Header -->
                <div class="card-header text-white fw-bold" style="background-color: #f15b2a;">
                    <i class="bi bi-house-door-fill me-2"></i> Daftar Property
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('pemilu.show', $event->id_event) }}" class="row g-2 align-items-center mb-3" id="search-form">
                        {{-- ID Listing --}}
                        <div class="col-12 col-lg-3">
                          <input type="text"
                                 name="search"
                                 class="form-control form-control-sm"
                                 placeholder="Cari ID Listing"
                                 value="{{ old('search', '') }}">
                        </div>

                        {{-- Tipe Property --}}
                        <div class="col-6 col-lg-2">
                          <select name="property_type" class="form-select form-select-sm">
                            <option value="" selected disabled>Tipe Property</option>
                            <option value="rumah" @if (old('property_type') === 'rumah') selected @endif>Rumah</option>
                            <option value="gudang" @if (old('property_type') === 'gudang') selected @endif>Gudang</option>
                            <option value="apartemen" @if (old('property_type') === 'apartemen') selected @endif>Apartemen</option>
                            <option value="tanah" @if (old('property_type') === 'tanah') selected @endif>Tanah</option>
                            <option value="pabrik" @if (old('property_type') === 'pabrik') selected @endif>Pabrik</option>
                            <option value="hotel dan villa" @if (old('property_type') === 'hotel dan villa') selected @endif>Hotel dan Villa</option>
                            <option value="ruko" @if (old('property_type') === 'ruko') selected @endif>Ruko</option>
                            <option value="sewa" @if (old('property_type') === 'sewa') selected @endif>Sewa</option>
                          </select>
                        </div>

                        {{-- Provinsi --}}
                        <div class="col-6 col-lg-2">
                          <select id="province-desktop" name="province" class="form-select form-select-sm">
                            <option value="" selected disabled>Pilih Provinsi</option>
                            {{-- options diisi via JS --}}
                          </select>
                        </div>

                        {{-- Kota/Kabupaten --}}
                        <div class="col-6 col-lg-2">
                          <select id="city-desktop" name="city" class="form-select form-select-sm" disabled>
                            <option value="" selected disabled>Pilih Kota/Kabupaten</option>
                          </select>
                        </div>

                        {{-- Kecamatan --}}
                        <div class="col-6 col-lg-2">
                          <select id="district-desktop" name="district" class="form-select form-select-sm" disabled>
                            <option value="" selected disabled>Pilih Kecamatan</option>
                          </select>
                        </div>

                        {{-- Tombol --}}
                        <div class="col-12 col-lg-1 d-grid">
                          <button type="submit" class="btn btn-dark btn-sm" id="search-btn">Search</button>
                        </div>
                      </form>




                      <script>
document.addEventListener('DOMContentLoaded', function () {
  const provinceDesktop = document.getElementById('province-desktop');
  const cityDesktop     = document.getElementById('city-desktop');
  const districtDesktop = document.getElementById('district-desktop');
  const searchForm = document.getElementById('search-form');

  const provinceMap = new Map(); // Provinsi => Set Kota
  const locationMap = new Map(); // Provinsi => (Kota => Set Kecamatan)

  let searchQuery = '';  // Variable to temporarily store search query values
  let propertyType = '';
  let province = '';
  let city = '';
  let district = '';

  // Load data lokasi (file harus ada di public/data/indonesia.json)
  fetch("{{ asset('data/indonesia.json') }}", { cache: 'no-store' })
    .then(res => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    })
    .then(data => {
      // Susun struktur data
      data.forEach(item => {
        const prov = (item.province || '').trim();
        const reg  = (item.regency  || '').trim();
        const dist = (item.district || '').trim();
        if (!prov || !reg || !dist) return;

        if (!provinceMap.has(prov)) provinceMap.set(prov, new Set());
        provinceMap.get(prov).add(reg);

        if (!locationMap.has(prov)) locationMap.set(prov, new Map());
        if (!locationMap.get(prov).has(reg)) locationMap.get(prov).set(reg, new Set());
        locationMap.get(prov).get(reg).add(dist);
      });

      // Isi pilihan provinsi
      for (const prov of provinceMap.keys()) {
        provinceDesktop.insertAdjacentHTML('beforeend', `<option value="${prov}">${prov}</option>`);
      }

      // Restore from query string (optional)
      const params = new URLSearchParams(location.search);
      const savedProv = params.get('province');
      const savedCity = params.get('city');
      const savedDist = params.get('district');

      // If there is a query string, remove it by resetting the form values
      if (savedProv || savedCity || savedDist) {
        resetForm(); // Reset form if query string is found
      }
    })
    .catch(err => {
      console.error('Gagal load indonesia.json:', err);
    });

  // Capture the form data before reset
  searchForm.addEventListener('submit', function (e) {
    // Prevent form submission
    e.preventDefault();

    // Store the current values before resetting
    searchQuery = document.querySelector('[name="search"]').value;
    propertyType = document.querySelector('[name="property_type"]').value;
    province = provinceDesktop.value;
    city = cityDesktop.value;
    district = districtDesktop.value;

    // Reset the form values
    resetForm();

    // After resetting, perform the search query with the captured values
    runSearchQuery();
  });

  // Reset form values and dropdowns
  function resetForm() {
    // Reset form values
    provinceDesktop.value = '';
    cityDesktop.value = '';
    districtDesktop.value = '';
    cityDesktop.disabled = true;
    districtDesktop.disabled = true;

    // Reset the options to the default 'Pilih' text
    cityDesktop.innerHTML = '<option value="" selected disabled>Pilih Kota/Kabupaten</option>';
    districtDesktop.innerHTML = '<option value="" selected disabled>Pilih Kecamatan</option>';
  }

  // Perform the search query with the captured values
  function runSearchQuery() {
    // Construct the URL with query parameters based on captured form values
    let searchUrl = `{{ route('pemilu.show', $event->id_event) }}?search=${searchQuery}`;

    if (propertyType) searchUrl += `&property_type=${propertyType}`;
    if (province) searchUrl += `&province=${province}`;
    if (city) searchUrl += `&city=${city}`;
    if (district) searchUrl += `&district=${district}`;

    // Perform the search by redirecting to the URL with query parameters
    window.location.href = searchUrl;

    // Remove query parameters from the URL after search to avoid input restore on reload
    window.history.replaceState({}, document.title, window.location.pathname);
  }

  // Events for handling dropdown changes
  provinceDesktop.addEventListener('change', function () {
    updateCityDropdown(this.value, cityDesktop);
  });

  cityDesktop.addEventListener('change', function () {
    updateDistrictDropdown(provinceDesktop.value, this.value);
  });

  // Helpers to update dropdowns based on previous selections
  function updateCityDropdown(prov, targetCityDropdown) {
    const citySet = provinceMap.get(prov) || new Set();
    targetCityDropdown.disabled = false;
    targetCityDropdown.innerHTML = '<option value="" selected>Pilih Kota/Kabupaten</option>';
    for (const c of citySet) {
      targetCityDropdown.insertAdjacentHTML('beforeend', `<option value="${c}">${c}</option>`);
    }
    // Reset kecamatan
    districtDesktop.disabled = true;
    districtDesktop.innerHTML = '<option value="" selected>Pilih Kecamatan</option>';
  }

  function updateDistrictDropdown(prov, selectedCity) {
    const districtSet = (locationMap.get(prov) && locationMap.get(prov).get(selectedCity)) || new Set();
    districtDesktop.disabled = false;
    districtDesktop.innerHTML = '<option value="" selected>Pilih Kecamatan</option>';
    for (const d of districtSet) {
      districtDesktop.insertAdjacentHTML('beforeend', `<option value="${d}">${d}</option>`);
    }
  }
});

                        </script>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Lokasi</th>
                                    <th>Luas (m²)</th>
                                    <th>Harga</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($properties as $property)
                                    <tr>
                                        <td>{{ $property->id_listing }}</td>
                                        <td>{{ $property->lokasi }}</td>
                                        <td>{{ $property->luas ?? '-' }}</td>
                                        <td>Rp {{ number_format($property->harga, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $fotoArray = explode(',', $property->gambar);
                                                $fotoUtama = $fotoArray[0] ?? 'default.jpg';
                                            @endphp
                                            <img src="{{ $fotoUtama }}"
                                                alt="Foto Properti"
                                                class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                                        </td>
                                        <td>
                                            @if(($eventStatus ?? 'Berjalan') === 'Berjalan'
                                                && ($current?->status_giliran === 'Berjalan')
                                                && ($current?->id_account === $accountId))

                                              <form action="{{ route('pemilu.pilih', [$event->id_event, $property->id_listing]) }}"
                                                    method="POST"
                                                    class="form-pilih"
                                                    id="formPilih_{{ $property->id_listing }}">
                                                @csrf
                                                <input type="hidden" name="id_event" value="{{ $event->id_event }}">
                                                <input type="hidden" name="id_listing" value="{{ $property->id_listing }}">

                                                <button type="submit"
                                                        class="btn btn-success btn-sm js-btn-pilih"
                                                        id="btn-pilih-{{ $property->id_listing }}">
                                                  <span class="spinner-border spinner-border-sm align-middle me-2 d-none"
                                                        aria-hidden="true"></span>
                                                  <span class="label">Pilih</span>
                                                </button>
                                              </form>

                                            @else
                                              <span class="text-muted">-</span>
                                            @endif
                                          </td>


                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="col-12">
                        <div class="pagination d-flex justify-content-center mt-4 gap-1 overflow-auto">
                            {{-- Previous --}}
                            @if ($properties->onFirstPage())
                                <a class="btn btn-sm btn-light rounded disabled">&laquo;</a>
                            @else
                                <a href="{{ $properties->appends(request()->query())->previousPageUrl() }}"
                                class="btn btn-sm btn-light rounded">&laquo;</a>
                            @endif

                            {{-- Pages --}}
                            @php
                                $currentPage = $properties->currentPage();
                                $lastPage = $properties->lastPage();
                                $start = max($currentPage - 2, 1);
                                $end = min($currentPage + 2, $lastPage);
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $properties->appends(request()->query())->url(1) }}"
                                class="btn btn-sm btn-light rounded">1</a>
                                @if ($start > 2)
                                    <span class="btn btn-sm btn-light rounded disabled">...</span>
                                @endif
                            @endif

                            @for ($i = $start; $i <= $end; $i++)
                                <a href="{{ $properties->appends(request()->query())->url($i) }}"
                                class="btn btn-sm rounded {{ $i === $currentPage ? 'btn-primary text-white' : 'btn-light' }}">
                                    {{ $i }}
                                </a>
                            @endfor

                            @if ($end < $lastPage)
                                @if ($end < $lastPage - 1)
                                    <span class="btn btn-sm btn-light rounded disabled">...</span>
                                @endif
                                <a href="{{ $properties->appends(request()->query())->url($lastPage) }}"
                                class="btn btn-sm btn-light rounded">{{ $lastPage }}</a>
                            @endif

                            {{-- Next --}}
                            @if ($properties->hasMorePages())
                                <a href="{{ $properties->appends(request()->query())->nextPageUrl() }}"
                                class="btn btn-sm btn-light rounded">&raquo;</a>
                            @else
                                <a class="btn btn-sm btn-light rounded disabled">&raquo;</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- RIGHT: Detail Client + Update Status -->
        <div class="col-md-4">
            {{-- Giliran Pemilu --}}
            <div class="card shadow-sm rounded mb-3">
                <div class="card-header text-white fw-bold" style="background-color: #3949ab;">
                <i class="bi bi-people-fill me-2"></i> Antrian Giliran
                </div>
                <div class="card-body">

                {{-- Info Event Status --}}
                <div class="alert alert-info py-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-lightning-charge-fill me-2"></i>
                        <div>
                            <div><strong>Sedang Giliran:</strong> {{ $current?->username ?? '-' }}</div>
                            @if($current)
                                <div class="small text-muted">
                                    {{ $current->mulai_aktif->format('H:i') }} – {{ $current->selesai_aktif->format('H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tabel antrian --}}
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Username</th>
                                <th>Waktu Tersisa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invites as $row)
                            <tr @class(['table-primary' => ($current?->id_invite ?? null) === $row->id_invite])>
                                <td class="fw-bold">{{ $row->urutan }}</td>
                                <td>{{ $row->username }}</td>
                                <td>
                                    @if($row->status_giliran === 'Menunggu')
                                      <span class="text-muted">—</span>
                                    @elseif($row->waktu_tersisa > 0)
                                      <span class="countdown" data-waktu="{{ $row->waktu_tersisa }}"></span>
                                    @else
                                      <span class="text-danger">Waktu Habis</span>
                                    @endif
                                  </td>
                                <td>
                                    <span class="badge
                                        @if($row->status_giliran === 'Berjalan') bg-success
                                        @elseif($row->status_giliran === 'Menunggu') bg-secondary
                                        @else bg-light text-dark @endif">
                                        {{ $row->status_giliran }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4"><em>Belum ada yang join.</em></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Status Event --}}
                @if($eventStatus === 'Selesai')
                    <div class="alert alert-danger">Event sudah selesai!</div>
                @endif
            </div>
        </div>
        {{-- Pengumuman / Transaction Log --}}
        <div class="card shadow-sm rounded mb-3">
            <div class="card-header text-white fw-bold" style="background-color: #3949ab;">
              <i class="bi bi-file-earmark-text-fill me-2"></i> Pengumuman Pilihan
            </div>
            <div class="card-body">
              {{-- List pengumuman --}}
              <div class="list-group overflow-auto" style="max-height: 320px;">
                @foreach ($logs as $log)
                  @php
                    $agentName = $log->agent_name ?? 'Nama tidak ditemukan';
                  @endphp
                  <div class="list-group-item">
                    <i class="bi bi-bell-fill me-2 text-primary"></i>
                    id {{ $log->id_listing }} telah diambil oleh {{ $agentName }}
                  </div>
                @endforeach
              </div>
            </div>
          </div>
    </div>



            {{-- <!-- History Catatan -->
            @if(count($transactionNotes))
            <div class="card shadow-sm rounded mt-3">
                <div class="card-header text-white fw-bold" style="background-color: #f4511e;">
                    <i class="bi bi-clock-history me-1"></i> Transaction Notes
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush small">
                        @foreach($transactionNotes as $note)
                            <li class="list-group-item">
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($note->tanggal_dibuat)->format('M d, Y h:i A') }}</div>
                                <div class="fw-semibold">
                                    <span class="text-secondary">{{ $note->status_transaksi }}</span> - {{ $note->catatan }}
                                    <span class="text-muted">({{ $note->account_name }})</span>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif --}}

        </div>
    </div>
</div>

  {{-- Polling ringan: per 10 detik update "Sedang Giliran" --}}
  {{-- <script>
    const stateUrl = "{{ route('pemilu.state', $event->id_event) }}";
    async function refreshTurn() {
      try {
        const res = await fetch(stateUrl, { cache: 'no-store' });
        const data = await res.json();
        // Kamu bisa pilih: reload halaman kecil atau update spot tertentu via DOM.
        // Versi simpel: reload jika current berubah.
        // (Implementasi DOM-only silakan lanjutkan sesuai struktur tabel di atas)
      } catch (e) { console.error(e); }
    }
    setInterval(refreshTurn, 10000);
  </script> --}}
<style>
/* Tambahkan ini pada file CSS kamu */
@media (max-width: 576px) {
    .pagination {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
    }
    .pagination .btn {
        min-width: 30px;
        margin: 0 5px;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Hanya form pilih (hindari form lain)
  document.querySelectorAll('form[id^="formPilih_"]').forEach(form => {
    form.addEventListener('submit', function () {
      const propertyId = form.id.split('_')[1];

      const btnPilih = document.getElementById('btn-pilih-' + propertyId);
      const spinner  = document.getElementById('spinner-' + propertyId);

      if (btnPilih) {
        btnPilih.disabled = true;
        btnPilih.classList.add('d-none');         // sembunyikan tombol
      }
      if (spinner) {
        spinner.classList.remove('d-none');       // tampilkan spinner
      }

      // Kunci tombol "Pilih" lain agar tidak double-submit
      document.querySelectorAll('button[id^="btn-pilih-"]').forEach(b => {
        if (b !== btnPilih) { b.disabled = true; b.classList.add('disabled'); }
      });
    }, { once: true }); // cegah event ganda
  });
});


document.addEventListener('DOMContentLoaded', function () {
  const nextRefreshAtMs = {!! $nextRefreshAtMs ? $nextRefreshAtMs : 'null' !!};
  const eventEndAtMs    = {!! $eventEndAtMs    ? $eventEndAtMs    : 'null' !!};

  const now = Date.now();
  const targets = [nextRefreshAtMs, eventEndAtMs].filter(t => t && t > now);
  if (!targets.length) return;

  let delay = Math.min(...targets) - now;
  if (delay <= 0) { location.reload(); return; }

  // batas maksimum setTimeout
  if (delay > 2147483647) delay = 2147483647;

  setTimeout(() => location.reload(), delay);
});


document.addEventListener("DOMContentLoaded", function() {
    function formatTime(sec) {
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        return [h, m, s]
            .map(v => String(v).padStart(2, '0'))
            .join(':');
    }

    function startCountdown(el) {
        let sisa = parseInt(el.dataset.waktu, 10);

        if (sisa <= 0) {
            el.innerHTML = '<span class="text-danger">Waktu Habis</span>';
            return;
        }

        el.textContent = formatTime(sisa);

        const interval = setInterval(() => {
            sisa--;
            if (sisa <= 0) {
                clearInterval(interval);
                el.innerHTML = '<span class="text-danger">Waktu Habis</span>';
            } else {
                el.textContent = formatTime(sisa);
            }
        }, 1000);
    }

    document.querySelectorAll('.countdown').forEach(startCountdown);
});
</script>

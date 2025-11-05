@include('template.header')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed end-0 m-3 shadow-lg"
         role="alert" style="min-width: 300px; top: 80px; z-index: 9999;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed end-0 m-3 shadow-lg"
         role="alert" style="min-width: 300px; top: 80px; z-index: 9999;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- /#header -->
        <!-- Content -->
        <div class="content">
            <!-- Animated -->
            <div class="animated fadeIn">
                <!-- Widgets  -->
                <div class="row g-4">
                    <!-- Pendapatan -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                    <i class="fas fa-wallet fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Pendapatan</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                    <i class="fas fa-chart-line fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Total Sales</h6>
                                    <h4 class="mb-0">Rp {{ number_format($totalSelisih, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Listing -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                                    <i class="fas fa-building fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Jumlah Listing</h6>
                                    <h4 class="mb-0">{{ $jumlahListing }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clients -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                                    <i class="fas fa-user-friends fa-lg"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Jumlah Klien</h6>
                                    <h4 class="mb-0">{{ $jumlahClients }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- /Widgets -->
                <div class="clearfix"></div>
                <!-- Orders -->
                <style>
                    .table thead th:nth-child(5), /* Lokasi */
                    .table tbody td:nth-child(5) {
                        width: 200px;
                    }

                    .table thead th:nth-child(6), /* Harga */
                    .table tbody td:nth-child(6) {
                        width: 180px;
                    }

                    .order-table {
                        width: 100%;
                    }

                    .card-body-- {
                        padding: 0 20px 20px 20px;
                    }
                </style>

                <div class="container-fluid">
                <!-- Tabs Utama -->
                <ul class="nav nav-tabs" id="mainTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab" aria-controls="progress" aria-selected="true">
                            📦 Progress
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" role="tab" aria-controls="calendar" aria-selected="false">
                            🗓️ Calendar
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="mainTabsContent">
                    <!-- Progress Tab -->
                    <div class="tab-pane fade show active" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">

                                <!-- AGENT -->
                                @if (session('role') === 'Agent')
                                <div class="card shadow-sm border-0 mb-4">
                                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-semibold text-primary">📋 Daftar Klien Tertarik</h5>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table align-middle table-hover" id="clientTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>ID Klien</th>
                                                    <th>Nama</th>
                                                    <th>ID Properti</th>
                                                    <th>Lokasi</th>
                                                    <th>Harga</th>
                                                    <th>Progress</th>
                                                    <th>Status</th>
                                                    <th>Detail</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($clients as $index => $client)
                                                @php
                                                    $status = $client->status;
                                                    $progress = match($status) {
                                                        'Pending' => 0,
                                                        'FollowUp' => 33,
                                                        'BuyerMeeting' => 66,
                                                        'Closing', 'Gagal' => 100,
                                                        default => 0,
                                                    };
                                                    $barClass = match($status) {
                                                        'gagal' => 'bg-danger',
                                                        'pending' => 'bg-warning',
                                                        default => 'bg-success',
                                                    };
                                                @endphp
                                                <tr id="row-{{ $client->id_account }}-{{ $client->id_listing }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><span class="badge bg-light text-dark">{{ $client->id_account }}</span></td>
                                                    <td>{{ $client->nama }}</td>
                                                    <td><span class="badge bg-secondary">{{ $client->id_listing }}</span></td>
                                                    <td>{{ $client->lokasi }}</td>
                                                    <td>Rp {{ number_format($client->harga, 0, ',', '.') }}</td>
                                                    <td style="min-width: 160px;">
                                                        <div class="progress" style="height: 18px;">
                                                        <div class="progress-bar {{ $barClass }}"
                                                            role="progressbar"
                                                            style="width: {{ $progress }}%;"
                                                            aria-valuenow="{{ $progress }}"
                                                            aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            {{ $progress }}%
                                                        </div>
                                                        </div>
                                                    </td>
                                                    @php
                                                        $status = $client->status; // biar konsisten kita nggak ubah case
                                                    @endphp
                                                    <td>{{ $client->status }}</td>
                                                    <td>
                                                        <form action="{{ route('dashboard.detail', ['id_listing' => $client->id_listing, 'id_account' => $client->id_account]) }}" method="GET">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm bg-secondary text-white rounded-pill px-3 shadow-sm">
                                                            Detail
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">Belum ada klien yang tertarik saat ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif

                            <!-- Stoker -->
@if (session('role') === 'Stoker')
    <div class="row">
      <!-- 3/4 kiri -->
      <div class="col-lg-9">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-primary">📋 Daftar Properti</h5>
          </div>

          <div class="card-body">
            <!-- Bar filter (GET) + tombol bulk (POST) -->
            {{-- =================== FILTER BAR (STOKER) =================== --}}
            <div class="row g-3 p-3 rounded shadow-sm bg-white mb-3 align-items-end stoker-filter-grid">
                {{-- Form FILTER (GET) tembus ke grid --}}
                <form id="stoker-filter-form"
                    method="GET"
                    action="{{ route('dashboard.owner') }}"
                    class="d-contents">
                <input type="hidden" name="tab" value="stoker" />

                {{-- Cari ID Listing (numeric) --}}
                <div class="col-6 col-lg-1 pe-lg-2">
                    <label for="stoker_search" class="form-label mb-1">Cari ID</label>
                    <input type="text" name="search" id="stoker_search" value="{{ request('search') }}"
                        class="form-control form-control-sm" placeholder="ID Listing"
                        inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                </div>

                {{-- Cari Vendor (text) --}}
                <div class="col-12 col-lg-3 pe-lg-2">
                    <label for="stoker_vendor" class="form-label mb-1">Cari Vendor</label>
                    <input type="text" name="vendor" id="stoker_vendor" value="{{ request('vendor') }}"
                        class="form-control form-control-sm" placeholder="Contoh : BRI Rajawali" autocomplete="off">
                </div>

                <div class="col-6 col-lg-1 pe-lg-2">
                    <label for="stoker_property_type" class="form-label mb-1">Tipe</label>
                    <select name="property_type" id="stoker_property_type" class="form-select form-select-sm">
                    <option value="" {{ request('property_type') ? '' : 'selected' }} disabled>Tipe Property</option>
                    <option value="rumah" @selected(request('property_type')==='rumah')>Rumah</option>
                    <option value="gudang" @selected(request('property_type')==='gudang')>Gudang</option>
                    <option value="apartemen" @selected(request('property_type')==='apartemen')>Apartemen</option>
                    <option value="tanah" @selected(request('property_type')==='tanah')>Tanah</option>
                    <option value="pabrik" @selected(request('property_type')==='pabrik')>Pabrik</option>
                    <option value="hotel dan villa" @selected(request('property_type')==='hotel dan villa')>Hotel dan Villa</option>
                    <option value="ruko" @selected(request('property_type')==='ruko')>Ruko</option>
                    <option value="toko" @selected(request('property_type')==='toko')>Toko</option>
                    <option value="lain-lain" @selected(request('property_type')==='lain-lain')>Lainnya</option>
                    </select>
                </div>

                <div class="col-6 col-lg-2 pe-lg-2">
                    <label for="stoker_province" class="form-label mb-1">Pilih Provinsi</label>
                    <select id="stoker_province" name="province" class="form-select form-select-sm">
                    <option disabled {{ request('province') ? '' : 'selected' }}>Pilih Provinsi</option>
                    </select>
                </div>

                <div class="col-6 col-lg-2 pe-lg-2">
                    <label for="stoker_city" class="form-label mb-1">Pilih Kota/Kab</label>
                    <select id="stoker_city" name="city" class="form-select form-select-sm" {{ request('province') ? '' : 'disabled' }}>
                    <option disabled selected>Pilih Kota/Kab</option>
                    </select>
                </div>

                <div class="col-6 col-lg-2 pe-lg-2">
                    <label for="stoker_district" class="form-label mb-1">Pilih Kecamatan</label>
                    <select id="stoker_district" name="district" class="form-select form-select-sm" {{ request('city') ? '' : 'disabled' }}>
                    <option disabled selected>Pilih Kecamatan</option>
                    </select>
                </div>
                </form>

                {{-- Tombol Reset --}}
                <div class="col-6 col-lg-1">
                <label class="form-label d-block invisible">Reset</label>
                <button type="button" id="btn-stoker-clear" class="btn reset-chip w-100">
                    <span class="me-1">↺</span>Reset
                </button>
                </div>
            </div>

            {{-- HOST STABIL untuk partial + spinner --}}
            <div id="stoker-list-wrap">
              <div id="stoker-loading" class="export-loading d-none">
                <div class="spinner-border" role="status" aria-label="Loading"></div>
              </div>
              <div id="stoker-fragment-host">@include('partial.stoker_list')</div>
            </div>

        </div>
    </div>
</div>
  {{-- ==== STYLE KHUSUS STOKER FILTER ==== --}}
  <style>
    /* Bikin jarak antarkolom terasa di desktop */
    .stoker-filter-grid > [class*="col-lg-"] { min-width: 0; }

    /* Label & input feel yang sama dengan tab Export */
    .stoker-filter-grid .form-label{ font-weight:600; color:#6b7280; }
    .stoker-filter-grid .form-control,
    .stoker-filter-grid .form-select{
      border-radius:.625rem;
    }

    /* Chip reset kecil, simetris, tidak kotak panjang norak */
    .reset-chip{
      --chip-border:#ffb98c;
      --chip-text:#ff7a00;
      display:inline-flex; align-items:center; justify-content:center;
      gap:.35rem; padding:.4rem .7rem;
      font-size:.875rem; line-height:1; font-weight:700;
      border-radius:.75rem; background:#fff; border:1px solid var(--chip-border);
      color:var(--chip-text);
      box-shadow:0 1px 0 rgba(0,0,0,.03);
      transition:filter .15s ease, transform .06s ease, background .15s ease;
    }
    .reset-chip:hover{ background:#fff8f3; filter:brightness(1.02); }
    .reset-chip:active{ transform:translateY(.5px); }
  </style>
              <style>
                .btn-reset-filter{
                  display:inline-flex; align-items:center; gap:.35rem;
                  padding:.35rem .6rem; font-size:.875rem; line-height:1; font-weight:600;
                  border-radius:.5rem; color:#ff6a00; background:#fff; border:1px solid #ffb98c;
                  box-shadow:0 1px 0 rgba(0,0,0,.03); transition:.15s ease-in-out;
                }
                .btn-reset-filter:hover{ background:#fff7f2; border-color:#ff9d66; color:#e85a00; }
                @media (max-width: 991.98px){ .w-lg-auto{ width:100%!important; } }
                #stoker-filter-form .form-label{ font-weight:600; color:#6b7280; }
                #stoker-filter-form .form-control, #stoker-filter-form .form-select{ border-radius:.625rem; }

                /* >>>>> Ini kunci: overlay hanya menutup wadah tabel Stoker */
                #stoker-list-wrap{ position:relative; min-height:120px; }
                #stoker-list-wrap .export-loading{
                  position:absolute; inset:0;
                  display:flex; align-items:center; justify-content:center;
                  background: rgba(255,255,255,.6);
                  backdrop-filter: saturate(120%) blur(1px);
                  z-index:3;
                }
                .export-loading.d-none{ display:none; }
              </style>

<script>
    (function(){
      const selProv  = document.getElementById('stoker_province');
      const selCity  = document.getElementById('stoker_city');
      const selDist  = document.getElementById('stoker_district');
      const btnClear = document.getElementById('btn-stoker-clear');
      const searchEl = document.getElementById('stoker_search');
      const selType  = document.getElementById('stoker_property_type');
      const vendorEl = document.getElementById('stoker_vendor');

      if (!selProv || !selCity || !selDist) return;

      // ==== helpers untuk baca & bandingkan filter ====
      function readFilters(){
        return {
          search:        (searchEl?.value || '').trim(),
          vendor:        (vendorEl?.value || '').trim(),
          property_type: selType?.value || '',
          province:      selProv?.value || '',
          city:          selCity?.value || '',
          district:      selDist?.value || ''
        };
      }
      function isEmptyFilters(f){
        return !f.search && !f.vendor && !f.property_type && !f.province && !f.city && !f.district;
      }
      function isEqual(a,b){ return JSON.stringify(a) === JSON.stringify(b); }

      // expose buat dipakai script bawah
      window.__stokerReadFilters  = readFilters;
      window.__stokerIsEmpty      = isEmptyFilters;
      window.__stokerFiltersEqual = isEqual;

      const DATA_URL = "{{ asset('data/indonesia.json') }}";
      const provinceMap = new Map();  // Prov => Set(Kota)
      const locationMap = new Map();  // Prov => Map(Kota => Set(Kec))

      const sortCity = (a,b) => {
        const A = a.toUpperCase().startsWith('KOTA');
        const B = b.toUpperCase().startsWith('KOTA');
        if (A && !B) return -1;
        if (!A && B) return 1;
        return a.localeCompare(b);
      };

      function resetSelect(el, ph){ el.innerHTML = `<option disabled selected>${ph}</option>`; }
      function fillProvinces(){
        resetSelect(selProv, 'Pilih Provinsi');
        Array.from(provinceMap.keys()).sort()
          .forEach(p => selProv.insertAdjacentHTML('beforeend', `<option value="${p}">${p}</option>`));
      }
      function fillCities(prov){
        resetSelect(selCity, 'Pilih Kota/Kab'); resetSelect(selDist, 'Pilih Kecamatan'); selDist.disabled = true;
        if (!prov || !provinceMap.has(prov)) { selCity.disabled = true; return; }
        Array.from(provinceMap.get(prov)).sort(sortCity)
          .forEach(c => selCity.insertAdjacentHTML('beforeend', `<option value="${c}">${c}</option>`));
        selCity.disabled = false;
      }
      function fillDistricts(prov, city){
        resetSelect(selDist, 'Pilih Kecamatan');
        if (!prov || !city || !locationMap.has(prov) || !locationMap.get(prov).has(city)){ selDist.disabled = true; return; }
        Array.from(locationMap.get(prov).get(city)).sort()
          .forEach(d => selDist.insertAdjacentHTML('beforeend', `<option value="${d}">${d}</option>`));
        selDist.disabled = false;
      }

      // Load data lokasi
      fetch(DATA_URL).then(r=>r.json()).then(rows=>{
        rows.forEach(x=>{
          const prov=(x.province||'').trim(), city=(x.regency||'').trim(), dist=(x.district||'').trim();
          if(!prov||!city||!dist) return;
          if(!provinceMap.has(prov)) provinceMap.set(prov,new Set());
          provinceMap.get(prov).add(city);
          if(!locationMap.has(prov)) locationMap.set(prov,new Map());
          if(!locationMap.get(prov).has(city)) locationMap.get(prov).set(city,new Set());
          locationMap.get(prov).get(city).add(dist);
        });
        fillProvinces();

        // Preselect dari query jika ada
        const rqProv = @json(request('province'));
        const rqCity = @json(request('city'));
        const rqDist = @json(request('district'));
        if (rqProv && provinceMap.has(rqProv)) {
          selProv.value = rqProv; fillCities(rqProv);
          if (rqCity && provinceMap.get(rqProv).has(rqCity)) {
            selCity.value = rqCity; fillDistricts(rqProv, rqCity);
            if (rqDist) selDist.value = rqDist;
          }
        } else {
          selCity.disabled = true; selDist.disabled = true;
          resetSelect(selCity, 'Pilih Kota/Kab'); resetSelect(selDist, 'Pilih Kecamatan');
        }
      }).catch(e=>console.error('Gagal load indonesia.json:', e));

      // Trigger AJAX tiap perubahan lokasi
      const softReload = () => {
        if (typeof window.__stokerDebounced === 'function') window.__stokerDebounced();
      };
      selProv.addEventListener('change', ()=>{ fillCities(selProv.value); softReload(); });
      selCity.addEventListener('change', ()=>{ fillDistricts(selProv.value, selCity.value); softReload(); });
      selDist.addEventListener('change', softReload);

      // RESET: hanya reload kalau sebelumnya ada filter aktif
      btnClear?.addEventListener('click', ()=>{
        const before = readFilters();

        // kosongkan input
        searchEl && (searchEl.value = '');
        vendorEl && (vendorEl.value = '');
        if (selType) selType.selectedIndex = 0;

        // reset lokasi
        selProv.selectedIndex = 0;
        fillCities(null); // auto-disable city & district

        const after = readFilters();

        // kalau sebelumnya ada filter baru reload
        if (!isEqual(before, after) && typeof window.__loadStokerList === 'function') {
          window.__loadStokerList({ page: 1 });
        }
      });
    })();
    </script>

      <!-- 1/4 kanan: panel pilihan + riwayat -->
      <div class="col-lg-3">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex justify-content-end">
            <form id="stoker-bulk-form" action="{{ route('stoker.bulkSold') }}" method="POST" class="m-0">
              @csrf
              <input type="hidden" name="selected_ids" id="stoker_selected_ids_input">
              <button type="submit" id="btn-stoker-bulk-sold" class="btn-bulk" disabled title="Centang minimal 1 listing">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"></path>
                </svg>
                <span>Tandai Terjual</span>
                <span class="badge bg-dark text-white badge-count ms-1" id="stoker-selected-counter">0</span>
              </button>
            </form>
          </div>

          <div class="card-body">
            <div id="stoker-selected-preview" class="d-flex flex-wrap gap-2 small"></div>
            <hr class="my-3">
            <div class="text-muted small">
              Centang item di halaman mana pun. Pilihan disimpan sementara di browser sampai kamu klik <strong>Tandai Terjual</strong>.
            </div>
          </div>
        </div>
        <style>
          /* Biar anak-anak di dalam form ikut grid parent row */
          .d-contents { display: contents !important; }

          /* Tombol bulk yang manusiawi (punyamu tadi) */
          .btn-bulk {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .55rem .9rem;
            border-radius: .75rem;
            font-weight: 600;
            background: #f7c74a;
            border: 1px solid #f1b933;
            box-shadow: 0 1px 0 rgba(0,0,0,.04), inset 0 -2px 0 rgba(0,0,0,.05);
            transition: transform .06s ease, box-shadow .15s ease, filter .15s ease;
            color: #5b3d00;
          }
          .btn-bulk:hover { filter: brightness(1.03); transform: translateY(-1px); }
          .btn-bulk:active { transform: translateY(0); box-shadow: inset 0 2px 0 rgba(0,0,0,.08); }
          .btn-bulk:disabled { background:#f0e6c8; border-color:#e2d7b6; color:#9c8c66; box-shadow:none; cursor:not-allowed; }
          .badge-count { font-weight:700; letter-spacing:.2px; }
        </style>

        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-primary">📝 Riwayat Properti</h5>
          </div>
          <div class="card-body">
            @if($soldProperties->isEmpty())
              <p class="text-center text-muted">Belum ada riwayat yang ditampilkan.</p>
            @else
              <ul class="list-group list-group-flush small" style="max-height: 360px; overflow:auto;">
                @foreach($soldProperties as $property)
                  <li class="list-group-item">
                    <strong>{{ \Carbon\Carbon::parse($property->tanggal_diupdate)->format('d M Y') }}</strong>
                    ({{ $property->id_listing }}) terjual
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>
    </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // ==== Filter refs ====
      const input   = document.getElementById('stoker_search');
      const vendor  = document.getElementById('stoker_vendor');
      const selType = document.getElementById('stoker_property_type');
      const selProv = document.getElementById('stoker_province');
      const selCity = document.getElementById('stoker_city');
      const selDist = document.getElementById('stoker_district');

      // ==== Host partial (stabil) ====
      const host = document.getElementById('stoker-fragment-host');

      // ==== Route fragment ====
      const fragmentRoute = "{{ route('dashboard.owner.stoker.list') }}";

      // ==== Overlay (hanya pada tabel) ====
      function getOverlay(){ return document.getElementById('stoker-loading'); }
      function showLoading(on){ const el = getOverlay(); el && el.classList.toggle('d-none', !on); }

      // ==== Params & state ====
      let t, lastReqId = 0;
      function paramsObj(merge = {}) {
        return {
          tab: 'stoker',
          search:        input?.value || '',
          vendor:        vendor?.value || '',
          property_type: selType?.value || '',
          province:      selProv?.value || '',
          city:          selCity?.value || '',
          district:      selDist?.value || '',
          page: 1,
          ...merge
        };
      }
      const qs = (o) => new URLSearchParams(o).toString();

      // ==== AJAX loader (replace partial tabel + pagination saja) ====
      async function loadList(extra = {}) {
        const myId = ++lastReqId;
        showLoading(true);
        try {
          const url = fragmentRoute + '?' + qs(paramsObj(extra));
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
          const html = await res.text();
          if (myId !== lastReqId) return;
          host.innerHTML = html;                          // replace tabel + pagination
          if (window.afterStokerListReplaced) window.afterStokerListReplaced(); // re-hydrate centangan
        } catch (e) {
          if (e?.name !== 'AbortError') console.error('stoker load error:', e);
        } finally {
          if (myId === lastReqId) showLoading(false);
        }
      }
      window.__loadStokerList = loadList;

      const debounced = () => { clearTimeout(t); t = setTimeout(() => loadList(), 220); };
      window.__stokerDebounced = debounced;

      // ==== Filter events ====
      input?.addEventListener('input', function(){
        const cleaned = this.value.replace(/[^\d]/g, '');
        if (this.value !== cleaned) this.value = cleaned;
        debounced();
      });
      vendor?.addEventListener('input', debounced);
      [selType, selProv, selCity, selDist].forEach(el => el && el.addEventListener('change', debounced));

      // Intercept submit (biar gak full reload)
      document.getElementById('stoker-filter-form')
        ?.addEventListener('submit', function(e){ e.preventDefault(); loadList({ page: 1 }); });

      // Delegasi pagination
      host?.addEventListener('click', function(e){
        const a = e.target.closest('a.js-stoker-page');
        if (!a) return;
        e.preventDefault();
        const page = a.dataset.page || '1';
        loadList({ page });
      });

      // =========================
      //   MANAGER SELEKSI STOKER
      // =========================
      (function(){
        const KEY = 'stokerSelectedIds';
        const getSel  = () => new Set(JSON.parse(localStorage.getItem(KEY) || '[]'));
        const saveSel = (set) => localStorage.setItem(KEY, JSON.stringify(Array.from(set)));

        const counters   = () => document.querySelectorAll('#stoker-selected-counter');
        const previewEl  = () => document.getElementById('stoker-selected-preview');
        const hiddenEl   = () => document.getElementById('stoker_selected_ids_input');
        const bulkBtn    = () => document.getElementById('btn-stoker-bulk-sold');

        function updateCounterAndHidden(){
          const size = getSel().size;
          counters().forEach(el => el.textContent = String(size));
          const hid = hiddenEl(); if (hid) hid.value = Array.from(getSel()).join(',');
          const btn = bulkBtn(); if (btn) { btn.disabled = size < 1; btn.title = size < 1 ? 'Centang minimal 1 listing' : ''; }
        }

        function renderPreview(){
          const el = previewEl(); if (!el) return;
          const sel = getSel();
          el.innerHTML = sel.size ? '' : '<span class="text-muted">Belum ada yang dipilih.</span>';
          sel.forEach(id => {
            const pill = document.createElement('button');
            pill.type = 'button';
            pill.className = 'btn btn-sm btn-outline-primary';
            pill.textContent = '#'+id;
            pill.title = 'Klik untuk hapus';
            pill.addEventListener('click', () => {
              const s = getSel(); s.delete(String(id)); saveSel(s);
              document.querySelectorAll('#stoker-list-inner .row-check[value="'+id+'"]').forEach(cb => cb.checked = false);
              syncMaster();
              updateCounterAndHidden();
              renderPreview();
            });
            el.appendChild(pill);
          });
        }

        function syncMaster(){
          const rows = Array.from(document.querySelectorAll('#stoker-list-inner .row-check'));
          const master = document.getElementById('check_all_stoker');
          if (!master) return;
          master.checked = rows.length > 0 && rows.every(x => x.checked);
          master.indeterminate = rows.some(x => x.checked) && !master.checked;
        }

        // Dipanggil SETIAP partial stoker_list selesai diganti
        window.afterStokerListReplaced = function(){
          const sel = getSel();

          // Pre-check baris
          document.querySelectorAll('#stoker-list-inner .row-check').forEach(cb => {
            cb.checked = sel.has(String(cb.value));
          });

          // Master checkbox
          const master = document.getElementById('check_all_stoker');
          if (master) {
            const rows = Array.from(document.querySelectorAll('#stoker-list-inner .row-check'));
            master.checked = rows.length > 0 && rows.every(cb => cb.checked);
            master.indeterminate = rows.some(cb => cb.checked) && !master.checked;

            master.onchange = function(){
              const now = this.checked;
              const s = getSel();
              rows.forEach(cb => {
                cb.checked = now;
                const val = String(cb.value);
                if (now) s.add(val); else s.delete(val);
              });
              saveSel(s);
              updateCounterAndHidden();
              renderPreview();
              syncMaster();
            };
          }

          // Row listeners
          document.querySelectorAll('#stoker-list-inner .row-check').forEach(cb => {
            cb.addEventListener('change', function(){
              const s = getSel();
              const val = String(this.value);
              if (this.checked) s.add(val); else s.delete(val);
              saveSel(s);
              updateCounterAndHidden();
              renderPreview();
              syncMaster();
            });
          });

          updateCounterAndHidden();
          renderPreview();
          syncMaster();
        };

        // Init pertama
        if (window.afterStokerListReplaced) window.afterStokerListReplaced();

        // Submit bulk: kirim semua ID
        document.getElementById('stoker-bulk-form')?.addEventListener('submit', function(e){
          const sel = Array.from(getSel());
          const hid = hiddenEl(); if (hid) hid.value = sel.join(',');
          if (sel.length < 1) e.preventDefault();
        });

        // Optional: clear selection via session flash
        @if (session('stoker_clear_selection'))
          localStorage.removeItem(KEY);
          if (window.afterStokerListReplaced) window.afterStokerListReplaced();
        @endif
      })();
    });
    </script>

        {{-- ========== Stoker ========== --}}
@endif


                            <!-- REGISTER -->
                            @if (session('role') === 'Register')
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-semibold text-primary">📋 Register Jobdesk</h5>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table align-middle table-hover text-center" id="clientClosingTable">
                                        <thead class="table-light align-middle">
                                            <tr>
                                                <th style="width: 40px;">#</th> <!-- ✅ Mepet -->
                                                <th style="width: 80px;">ID</th> <!-- ✅ Mepet -->
                                                <th style="min-width: 180px;">Name</th> <!-- ✅ Lebar untuk nama panjang -->
                                                <th style="width: 100px;">Property ID</th>
                                                <th style="min-width: 200px;">Lokasi</th> <!-- ✅ Lebih lebar -->
                                                <th style="min-width: 120px;">Harga</th>
                                                <th style="min-width: 160px;">Progess</th>
                                                <th style="min-width: 160px;">Status</th> <!-- ✅ Lebih lebar -->
                                                <th style="min-width: 160px;">Detail</th> <!-- ✅ Lebih lebar -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($clientsClosing as $index => $client)
                                            <tr id="row-{{ $client->id_account }}-{{ $client->id_listing }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $client->id_account }}</span></td>
                                                <td class="text-truncate" style="max-width: 220px;">{{ $client->nama }}</td>
                                                <td><span class="badge bg-secondary">{{ $client->id_listing }}</span></td>
                                                <td class="text-truncate" style="max-width: 300px;">{{ $client->lokasi }}</td>
                                                <td>Rp {{ number_format($client->harga, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                    $tahap = $client->status ?? 'Closing'; // Ambil status dari query
                                                    $progress = match($tahap) {
                                                        'Closing' => 0,
                                                        'Kuitansi' => 20,
                                                        'Kode Billing' => 40,
                                                        'Kutipan Risalah Lelang' => 60,
                                                        'Akte Grosse' => 80,
                                                        'Balik Nama' => 100,
                                                        default => 0
                                                    };
                                                @endphp
                                                    <div class="progress" style="height: 14px;">
                                                        <div class="progress-bar {{ $progress == 100 ? 'bg-success' : 'bg-secondary' }}"
                                                            role="progressbar"
                                                            style="width: {{ $progress }}%;"
                                                            aria-valuenow="{{ $progress }}"
                                                            aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            {{ $progress }}%
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>{{ $client->status }}</td>
                                                <td>
                                                    <form action="{{ route('dashboard.detail', ['id_listing' => $client->id_listing, 'id_account' => $client->id_account]) }}" method="GET">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm bg-secondary text-white rounded-pill px-3 shadow-sm">
                                                            Detail
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">Belum ada klien closing.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @endif

                            @if (session('role') === 'Pengosongan')
                            <style>
                                .table thead th:nth-child(5),
                                .table tbody td:nth-child(5) {
                                    width: 200px;
                                }

                                .table thead th:nth-child(6),
                                .table tbody td:nth-child(6) {
                                    width: 180px;
                                }

                                .table td,
                                .table th {
                                    vertical-align: middle;
                                }
                            </style>

                            <div class="card shadow-sm border-0 mb-4 mt-5">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-semibold text-primary">📦 Daftar Pengosongan Properti</h5>
                                </div>
                                <div class="card-body table-responsive">
                                    <table class="table align-middle table-hover" id="pengosonganTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>ID Klien</th>
                                                <th>Nama</th>
                                                <th>ID Properti</th>
                                                <th>Lokasi</th>
                                                <th>Harga</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $now = \Carbon\Carbon::now(); @endphp
                                            @forelse ($clientsPengosongan as $index => $client)
                                                @if ($client->status === 'Balik Nama' || $client->status === 'Eksekusi Pengosongan')
                                                    @php
                                                        $status = $client->status;
                                                        $progress = match($status) {
                                                            'Balik Nama' => 25,
                                                            'Eksekusi Pengosongan' => 50,
                                                            'Selesai' => 100,
                                                            default => 0,
                                                        };
                                                        $barClass = match($status) {
                                                            'Balik Nama' => 'bg-secondary',
                                                            'Eksekusi Pengosongan' => 'bg-warning',
                                                            'Selesai' => 'bg-success',
                                                            default => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <tr id="row-{{ $client->id_account }}-{{ $client->id_listing }}">
                                                        <td>{{ $index + 1 }}</td>
                                                        <td><span class="badge bg-light text-dark">{{ $client->id_account }}</span></td>
                                                        <td>{{ $client->nama }}</td>
                                                        <td><span class="badge bg-secondary">{{ $client->id_listing }}</span></td>
                                                        <td>{{ $client->lokasi }}</td>
                                                        <td>Rp {{ number_format($client->harga, 0, ',', '.') }}</td>
                                                        <td style="min-width: 160px;">
                                                            <div class="progress" style="height: 18px;">
                                                                <div class="progress-bar {{ $barClass }}"
                                                                    role="progressbar"
                                                                    style="width: {{ $progress }}%;"
                                                                    aria-valuenow="{{ $progress }}"
                                                                    aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                    {{ $progress }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $client->status }}</td>
                                                        <td>
                                                            <form action="{{ route('dashboard.detail', ['id_listing' => $client->id_listing, 'id_account' => $client->id_account]) }}" method="GET">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm bg-secondary text-white rounded-pill px-3 shadow-sm">
                                                                    Detail
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted py-4">Belum ada data pengosongan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif


                            </div>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <button id="calPrev" type="button" class="btn btn-sm btn-outline-primary">‹</button>
                                    <button id="calToday" type="button" class="btn btn-sm btn-outline-secondary">Today</button>
                                    <button id="calNext" type="button" class="btn btn-sm btn-outline-primary">›</button>
                                </div>
                                <h5 class="mb-0 fw-semibold text-primary" id="calTitle">🗓️ CALENDAR</h5>
                                <div style="width:116px;"></div> {{-- spacer supaya judul center --}}
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- KIRI: KALENDER -->
                                    <div class="col-lg-8">
                                        <div class="calendar-lite">
                                            <div class="calendar-lite__weekdays">
                                                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                                            </div>
                                            <div id="calendarGrid" class="calendar-lite__grid"></div>
                                        </div>
                                    </div>

                                    <!-- KANAN: EVENT 7 HARI KE DEPAN -->
                                    <div class="col-lg-4 d-flex flex-column gap-3">

                                        <!-- Event 7 Hari -->
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header text-white" style="background-color:#f4511e; color:#fff;">
                                                <h6 class="mb-0 fw-semibold">Event 7 Hari Ke Depan</h6>
                                            </div>
                                            <div id="upcomingList" class="list-group list-group-flush small" style="max-height: 300px; overflow:auto;">
                                                <!-- Diisi via JS -->
                                            </div>
                                            <div class="p-3 border-top small text-muted">
                                                <span id="rangeInfo"></span>
                                            </div>
                                        </div>

                                        <!-- Container Kedua -->
                                        <div class="card shadow-sm border-0">
                                            <div class="card-header text-white" style="background-color:#f4511e; color:#fff;">
                                                <h6 class="mb-0 fw-semibold">Container Kedua</h6>
                                            </div>
                                            <div class="p-3">
                                                Konten container kedua di sini...
                                            </div>
                                        </div>

                                    </div>

                                </div> <!-- /row -->
                            </div>
                        </div>

                        <style>
                            /* --- Calendar --- */
                            .calendar-lite { max-width: 100%; margin: 0 auto; background: #fff; }
                            .calendar-lite__weekdays {
                                display: grid; grid-template-columns: repeat(7, 1fr);
                                gap: 4px; padding: .25rem 0 .5rem 0; color: #6c757d;
                                font-size: .85rem; text-align: center; user-select: none;
                            }
                            .calendar-lite__weekdays > div { padding: .25rem 0; font-weight: 600; }

                            .calendar-lite__grid {
                                display: grid; grid-template-columns: repeat(7, 1fr);
                                gap: 4px; padding: 0;
                            }
                            .calendar-lite__cell {
                                aspect-ratio: 1 / 1; border: 1px solid rgba(0,0,0,.12);
                                border-radius: 8px; background: #f9fafb; position: relative;
                                transition: transform .15s ease, box-shadow .15s ease, background .15s ease, border-color .15s ease;
                                overflow: hidden;
                            }
                            .calendar-lite__cell:hover { transform: translateY(-2px); background: #fff; box-shadow: 0 6px 12px rgba(0,0,0,.06); border-color: rgba(0,0,0,.2); }
                            .calendar-lite__date { position: absolute; top: 5px; right: 6px; font-size: .8rem; font-weight: 600; color: #495057; }
                            .calendar-lite__cell.muted { background: #f3f4f6; color: #9aa0a6; }
                            .calendar-lite__cell.today { outline: 2px solid #0d6efd; background: #e7f1ff; }
                            .calendar-lite__cell .events { position: absolute; left: 6px; right: 6px; bottom: 6px; display: flex; flex-direction: column; gap: 4px; }
                            .calendar-lite__badge { display:inline-block; font-size:.72rem; padding:2px 6px; border-radius:6px; background:#e9ecef; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

                            /* --- Upcoming list --- */
                            .list-group-item.upcoming-item { display:flex; align-items:flex-start; gap:.5rem; }
                            .up-date {
                                min-width: 42px; text-align:center; border-radius:8px; background:#f1f3f5;
                                padding:.35rem .25rem; line-height:1;
                            }
                            .up-date .d { font-size: .95rem; font-weight:700; }
                            .up-date .m { font-size: .7rem; text-transform:uppercase; letter-spacing:.3px; color:#6c757d; }
                            .up-body .ttl { font-weight:600; }
                            .up-body .meta { color:#6c757d; }

                            @media (max-width: 576px) {
                                .calendar-lite__grid { gap: 3px; }
                                .calendar-lite__weekdays { gap: 3px; font-size: .8rem; }
                                .calendar-lite__date { font-size: .75rem; }
                            }

                            .custom-event-container {
                                border-radius: 12px;
                                overflow: hidden;
                                background: #fff;
                                display: flex;
                                flex-direction: column;
                            }

                            .custom-event-header {
                                font-weight: 600;
                                padding: 12px 16px;
                                background-color: #fff;
                                font-size: 0.95rem;
                            }

                            .custom-event-body {
                                padding: 12px 16px;
                                max-height: 300px;
                                overflow-y: auto;
                            }

                            .custom-event-footer {
                                padding: 8px 16px;
                                border-top: 1px solid rgba(0,0,0,0.08);
                            }

                            .list-group-item.upcoming-item {
                                display: flex;
                                align-items: flex-start;
                                gap: .5rem;
                                background-color: #fff;
                                color: #000;
                            }

                            .card-header {
                                font-size: 0.95rem;
                            }

                            .card-header h6 {
                                margin: 0;
                                color: #fff; /* Pastikan font judul putih */
                            }
                            .card-header {
                                font-size: 0.95rem;
                            }

                            .card-header h6 {
                                margin: 0;
                                color: #fff; /* Pastikan font judul putih */
                            }
                        </style>

                        <script>
            (function(){
                const events = @json($events ?? []);

                const titleEl = document.getElementById('calTitle');
                const gridEl  = document.getElementById('calendarGrid');
                const btnPrev = document.getElementById('calPrev');
                const btnNext = document.getElementById('calNext');
                const btnToday= document.getElementById('calToday');
                const upList  = document.getElementById('upcomingList');
                const rangeInfo = document.getElementById('rangeInfo');
                const containerKedua = document.querySelector('.col-lg-4 .card:nth-child(2)');
                const containerKeduaHeader = containerKedua.querySelector('.card-header h6');
                const containerKeduaBody = containerKedua.querySelector('.p-3');

                const now = new Date();
                let viewYear  = now.getFullYear();
                let viewMonth = now.getMonth();
                const MONTHS_ID = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                let selectedDate = null;

                function toDate(val){ return (val instanceof Date) ? val : new Date(val); }
                function startOfDay(d){ const x = new Date(d); x.setHours(0,0,0,0); return x; }
                function endOfDay(d){ const x = new Date(d); x.setHours(23,59,59,999); return x; }
                function daysInMonth(y, m){ return new Date(y, m+1, 0).getDate(); }
                function startWeekday(y, m){ return new Date(y, m, 1).getDay(); }
                function fmtDate(d){ return d.toLocaleString('id-ID', { weekday:'short', day:'2-digit', month:'short' }); }
                function fmtTime(d){ return d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', hour12:false }); }
                function isSameDay(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }

                function renderCalendar(){
                    titleEl.innerHTML = `🗓️ ${MONTHS_ID[viewMonth]} ${viewYear}`;
                    const firstDay = startWeekday(viewYear, viewMonth);
                    const thisCount = daysInMonth(viewYear, viewMonth);
                    const prevCount = daysInMonth(viewYear, (viewMonth-1+12)%12);

                    const cells = [];
                    for (let i = 0; i < firstDay; i++){
                        const dateNum = prevCount - firstDay + 1 + i;
                        cells.push({ num: dateNum, other:true, date: new Date(viewYear, viewMonth-1, dateNum) });
                    }
                    for (let d = 1; d <= thisCount; d++){
                        cells.push({ num: d, other:false, date: new Date(viewYear, viewMonth, d) });
                    }
                    while (cells.length < 42){
                        const d = cells.length - (firstDay + thisCount) + 1;
                        cells.push({ num: d, other:true, date: new Date(viewYear, viewMonth+1, d) });
                    }

                    gridEl.innerHTML = '';
                    cells.forEach(c => {
                        const cell = document.createElement('div');
                        cell.className = 'calendar-lite__cell' + (c.other ? ' muted' : '');
                        if (isSameDay(c.date, new Date()) && !c.other) cell.classList.add('today');

                        const num = document.createElement('div');
                        num.className = 'calendar-lite__date';
                        num.textContent = c.num;
                        cell.appendChild(num);

                        const evts = document.createElement('div');
                        evts.className = 'events';
                        const dayStart = startOfDay(c.date);
                        const dayEnd   = endOfDay(c.date);
                        const todays = events.filter(e=>{
                            const s = toDate(e.start);
                            const eEnd = toDate(e.end) || s;
                            return (s <= dayEnd && eEnd >= dayStart);
                        });

                        todays.slice(0,2).forEach(e=>{
                            const b = document.createElement('span');
                            b.className = 'calendar-lite__badge';
                            b.textContent = e.allDay ? e.title : `${fmtTime(toDate(e.start))} · ${e.title}`;
                            evts.appendChild(b);
                        });
                        if (todays.length > 2){
                            const more = document.createElement('span');
                            more.className = 'calendar-lite__badge';
                            more.textContent = `+${todays.length - 2} lagi`;
                            evts.appendChild(more);
                        }

                        cell.appendChild(evts);
                        cell.addEventListener('click', ()=> renderTodayEvents(c.date));
                        gridEl.appendChild(cell);
                    });
                }

                function renderUpcoming(){
                    const today = startOfDay(new Date());
                    const cutoff = startOfDay(new Date(today)); cutoff.setDate(cutoff.getDate()+7);
                    rangeInfo.textContent = `${fmtDate(today)} – ${fmtDate(new Date(cutoff.getTime()-86400000))}`;

                    const upcoming = events
                        .map(ev => ({...ev, _s: toDate(ev.start), _e: toDate(ev.end) || toDate(ev.start)}))
                        .filter(ev => ev._s < cutoff && ev._e >= today)
                        .sort((a,b)=> a._s - b._s)
                        .slice(0, 20);

                    upList.innerHTML = '';
                    if (upcoming.length === 0){
                        upList.innerHTML = `<div class="list-group-item text-muted">Tidak ada event.</div>`;
                        return;
                    }
                    upcoming.forEach(ev => {
                        const li = document.createElement('div');
                        li.className = 'list-group-item upcoming-item d-flex justify-content-between align-items-center';

                        // default konten kiri
                        let leftContent = `
                            <div class="d-flex align-items-center">
                                <div class="up-date me-2">
                                    <div class="d">${String(ev._s.getDate()).padStart(2,'0')}</div>
                                    <div class="m">${MONTHS_ID[ev._s.getMonth()]}</div>
                                </div>
                                <div class="up-body">
                                    <div class="ttl">${ev.title}</div>
                                    <div class="meta">${ev.allDay ? 'All day' : `${fmtTime(ev._s)}–${fmtTime(ev._e)}`} • ${fmtDate(ev._s)}</div>
                                </div>
                            </div>
                        `;

                        // kalau eventnya Pemilu, kasih button Join di kanan
                        let rightContent = '';
                        const now = new Date();
                        const eventEnd = toDate(ev.end); // pastikan ev.end diubah ke Date object

                        if(ev.title && ev.title.toLowerCase() === 'pemilu'  && now <= eventEnd){
                            rightContent = `<button class="btn btn-success btn-sm ms-2" id="btnJoin_${ev.id}">Join</button>`;
                        }

                        li.innerHTML = `
                            ${leftContent}
                            ${rightContent}
                        `;

                        // event detail kalau klik list (kecuali tombol join)
                        li.addEventListener('click', (e)=> {
                            if(!e.target.closest('button')){ // biar tombol join nggak ikut trigger
                                renderEventDetail(ev);
                            }
                        });

                        // event khusus tombol join
                        if(ev.title && ev.title.toLowerCase() === 'pemilu'){
                            li.querySelector(`#btnJoin_${ev.id}`).addEventListener('click', (e)=> {
                                e.stopPropagation(); // jangan trigger detail

                                // Ganti tombol "Join" jadi icon loading
                                const btnJoin = li.querySelector(`#btnJoin_${ev.id}`);
                                btnJoin.innerHTML = '<i class="spinner-border spinner-border-sm" role="status"></i> Loading...';
                                btnJoin.disabled = true; // disable tombol agar tidak bisa ditekan lagi

                                // Panggil updateInvite dan proses
                                updateInvite(ev.id, 'join', ev.access).then(() => {
                                    // Setelah berhasil, ganti tombol kembali menjadi "Join"
                                    btnJoin.innerHTML = 'Join';
                                    btnJoin.disabled = false; // enable kembali tombol
                                }).catch((err) => {
                                    // Jika terjadi error, kembalikan tombol ke "Join" dan beri pesan error
                                    btnJoin.innerHTML = 'Join';
                                    btnJoin.disabled = false;
                                    alert('Terjadi kesalahan: ' + err.message);
                                });
                            });
                        }

                        upList.appendChild(li);
                    });
                }

                function renderTodayEvents(date){
                    selectedDate = date;
                    const dayStart = startOfDay(date);
                    const dayEnd   = endOfDay(date);
                    const todays = events.filter(e=>{
                        const s = toDate(e.start);
                        const eEnd = toDate(e.end) || s;
                        return (s <= dayEnd && eEnd >= dayStart);
                    });

                    containerKedua.style.display = 'block';
                    containerKeduaHeader.textContent = `Event Hari Ini (${fmtDate(date)})`;
                    containerKeduaBody.innerHTML = '';

                    if (todays.length === 0){
                        containerKeduaBody.innerHTML = `<div class="list-group-item text-muted">Tidak ada event.</div>`;
                    } else {
                        todays.forEach(ev=>{
                            const li = document.createElement('div');
                            li.className = 'list-group-item upcoming-item';
                            li.innerHTML = `
                                <div class="up-date">
                                    <div class="d">${String(toDate(ev.start).getDate()).padStart(2,'0')}</div>
                                    <div class="m">${MONTHS_ID[toDate(ev.start).getMonth()]}</div>
                                </div>
                                <div class="up-body">
                                    <div class="ttl">${ev.title}</div>
                                    <div class="meta">${ev.allDay ? 'All day' : `${fmtTime(toDate(ev.start))}–${fmtTime(toDate(ev.end))}`} • ${fmtDate(toDate(ev.start))}</div>
                                </div>
                            `;
                            li.addEventListener('click', ()=> renderEventDetail(ev));
                            containerKeduaBody.appendChild(li);
                        });
                    }

                    const btn = document.createElement('button');
                    btn.className = 'btn btn-primary btn-sm mt-3';
                    btn.textContent = 'Tambah Event';
                    btn.addEventListener('click', ()=> renderAddEventForm(date));
                    containerKeduaBody.appendChild(btn);
                }

                function renderAddEventForm(date){
                    containerKedua.style.display = 'block';
                    containerKeduaHeader.textContent = `Tambah Event (${fmtDate(date)})`;

                    // Ambil YYYY-MM-DD dari date yang dipencet
                    function formatDateOnly(d){
                        return d.getFullYear() + "-" +
                            String(d.getMonth()+1).padStart(2,'0') + "-" +
                            String(d.getDate()).padStart(2,'0');
                    }

                    const dateOnly = formatDateOnly(date);

                    containerKeduaBody.innerHTML = `
                        <form id="addEventForm">
                            <div class="mb-2"><label>Judul</label><input type="text" class="form-control" name="title" required></div>
                            <div class="mb-2"><label>Deskripsi</label><textarea class="form-control" name="description"></textarea></div>
                            <div class="mb-2"><label>Mulai</label>
                                <input type="datetime-local" class="form-control" name="start" id="evStart" required value="${dateOnly}T00:00">
                            </div>
                            <div class="mb-2"><label>Selesai</label>
                                <input type="datetime-local" class="form-control" name="end" id="evEnd" required value="${dateOnly}T00:00">
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="evAllDay" name="allDay">
                                <label class="form-check-label">All Day</label>
                            </div>
                            <div class="mb-2"><label>Lokasi</label><input type="text" class="form-control" name="location"></div>
                            <div class="mb-2"><label>Akses Event</label>
                                <select class="form-select" name="access">
                                    <option value="terbuka">Terbuka</option>
                                    <option value="tertutup">Tertutup</option>
                                </select>
                            </div>
                            <div class="mb-2"><label>Durasi (menit)</label><input type="number" class="form-control" name="duration" id="evDuration"></div>
                            <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                        </form>
                    `;


                    document.getElementById('evAllDay').addEventListener('change', function(){
                        const dis = this.checked;
                        document.getElementById('evStart').disabled = dis;
                        document.getElementById('evEnd').disabled = dis;
                        if(dis){ document.getElementById('evStart').value=''; document.getElementById('evEnd').value=''; }
                    });

                    const startEl = document.getElementById('evStart');
                    const endEl = document.getElementById('evEnd');
                    const durationEl = document.getElementById('evDuration');
                    function updateDuration(){
                        const s = new Date(startEl.value);
                        const e = new Date(endEl.value);
                        if(s && e && e > s && !durationEl.matches(':focus')){
                            const diffMin = Math.floor((e - s) / (1000*60));
                            durationEl.value = diffMin;
                        }
                    }
                    startEl.addEventListener('change', updateDuration);
                    endEl.addEventListener('change', updateDuration);

                    document.getElementById('addEventForm').addEventListener('submit', async function(e){
                        e.preventDefault();
                        const formData = new FormData(this);
                        try {
                            const res = await fetch("{{ route('events.store') }}", {
                                method: "POST",
                                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                                body: formData
                            });
                            if(!res.ok) throw new Error('Gagal menyimpan event');
                            const data = await res.json(); // ← ambil data balik dari backend

                            // push event baru ke array events supaya langsung terpakai
                            events.push({
                                id: data.event.id_event,
                                title: data.event.title,
                                description: data.event.description,
                                start: data.event.mulai,
                                end: data.event.selesai,
                                allDay: !!data.event.all_day,
                                location: data.event.location,
                                access: data.event.akses,
                                duration: data.event.durasi,
                                created_by: data.event.created_by
                            });

                            alert('Event berhasil disimpan');
                            renderTodayEvents(selectedDate);
                            renderCalendar();
                            renderUpcoming();
                        } catch(err) {
                            alert(err.message);
                        }
                    });

                }

                function renderEditEvent(ev){
                    containerKedua.style.display = 'block';
                    containerKeduaHeader.textContent = `Edit Event`;

                    // Ambil YYYY-MM-DD dari start event
                    function formatDateOnly(d){
                        return d.getFullYear() + "-" +
                            String(d.getMonth()+1).padStart(2,'0') + "-" +
                            String(d.getDate()).padStart(2,'0');
                    }

                    function formatTimeForInput(d){
                        const h = String(d.getHours()).padStart(2,'0');
                        const m = String(d.getMinutes()).padStart(2,'0');
                        return `${h}:${m}`;
                    }

                    const startDate = toDate(ev.start);
                    const endDate = toDate(ev.end);
                    const dateOnlyStart = formatDateOnly(startDate);
                    const dateOnlyEnd = formatDateOnly(endDate);

                    containerKeduaBody.innerHTML = `
                        <form id="editEventForm">
                            <div class="mb-2"><label>Judul</label><input type="text" class="form-control" name="title" required value="${ev.title || ''}"></div>
                            <div class="mb-2"><label>Deskripsi</label><textarea class="form-control" name="description">${ev.description || ''}</textarea></div>
                            <div class="mb-2"><label>Mulai</label>
                                <input type="datetime-local" class="form-control" name="start" id="evEditStart" required
                                    value="${dateOnlyStart}T${formatTimeForInput(startDate)}">
                            </div>
                            <div class="mb-2"><label>Selesai</label>
                                <input type="datetime-local" class="form-control" name="end" id="evEditEnd" required
                                    value="${dateOnlyEnd}T${formatTimeForInput(endDate)}">
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="evEditAllDay" name="allDay" ${ev.allDay ? 'checked' : ''}>
                                <label class="form-check-label">All Day</label>
                            </div>
                            <div class="mb-2"><label>Lokasi</label><input type="text" class="form-control" name="location" value="${ev.location || ''}"></div>
                            <div class="mb-2"><label>Akses Event</label>
                                <select class="form-select" name="access">
                                    <option value="Terbuka" ${ev.access==='Terbuka' ? 'selected' : ''}>Terbuka</option>
                                    <option value="Tertutup" ${ev.access==='Tertutup' ? 'selected' : ''}>Tertutup</option>
                                </select>
                            </div>
                            <div class="mb-2"><label>Durasi (menit)</label><input type="number" class="form-control" name="duration" id="evEditDuration" value="${ev.duration || ''}"></div>
                            <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                            <button type="button" class="btn btn-secondary btn-sm" id="btnCancelEdit">Batal</button>
                        </form>
                    `;

                    // Handle All Day checkbox
                    const allDayEl = document.getElementById('evEditAllDay');
                    const startEl = document.getElementById('evEditStart');
                    const endEl = document.getElementById('evEditEnd');
                    const durationEl = document.getElementById('evEditDuration');

                    allDayEl.addEventListener('change', function(){
                        const dis = this.checked;
                        startEl.disabled = dis;
                        endEl.disabled = dis;
                        if(dis){ startEl.value=''; endEl.value=''; }
                    });

                    function updateDuration(){
                        const s = new Date(startEl.value);
                        const e = new Date(endEl.value);
                        if(s && e && e > s && !durationEl.matches(':focus')){
                            const diffMin = Math.floor((e - s) / (1000*60));
                            durationEl.value = diffMin;
                        }
                    }

                    startEl.addEventListener('change', updateDuration);
                    endEl.addEventListener('change', updateDuration);

                    // Cancel button
                    document.getElementById('btnCancelEdit').addEventListener('click', ()=>{
                        renderEventDetail(ev); // kembali ke detail
                    });

                    // Submit form -> PUT request
                    document.getElementById('editEventForm').addEventListener('submit', async function(e){
                        e.preventDefault();
                        const formData = new FormData(this);
                        const url = "{{ route('events.update', ['id' => '_ID_']) }}".replace('_ID_', ev.id);

                        try {
                            const res = await fetch(url, {
                                method: 'POST', // Laravel bisa PUT, tapi fetch kadang pakai POST + _method
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            });

                            if(!res.ok) throw new Error('Gagal update event');
                            const data = await res.json();

                            // Update array events di front-end
                            const index = events.findIndex(x=>x.id === ev.id);
                            if(index !== -1){
                                events[index] = {
                                    ...events[index],
                                    title: data.event.title,
                                    description: data.event.description,
                                    start: data.event.mulai,
                                    end: data.event.selesai,
                                    allDay: !!data.event.all_day,
                                    location: data.event.location,
                                    access: data.event.akses,
                                    duration: data.event.durasi
                                };
                            }

                            alert('Event berhasil diupdate');
                            renderTodayEvents(selectedDate);
                            renderCalendar();
                            renderUpcoming();
                        } catch(err){
                            alert(err.message);
                        }
                    });
                }


                function renderEventDetail(ev){
                    containerKedua.style.display = 'block';
                    containerKeduaHeader.textContent = `Detail Event`;

                    let actionButtons = '';

                    const now = new Date();
                    const eventEnd = toDate(ev.end); // pastikan ev.end diubah ke Date object

                    if (ev.title && ev.title.toLowerCase() === 'pemilu' && now <= eventEnd) {
                        actionButtons = `<button class="btn btn-primary btn-sm me-2" id="btnJoin2">Join</button>`;
                    }

                    if (ev.created_by_id === "{{ session('id_account') }}") {
                        actionButtons += `<button class="btn btn-warning btn-sm me-2" id="btnEdit">Edit</button>`;
                    }

                    containerKeduaBody.innerHTML = `
                        <p><strong>${ev.title}</strong></p>
                        <p>Penyelenggara: ${ev.created_by || '-'}</p>
                        <p><strong>Deskripsi:</strong><br>${ev.description || '-'}</p>
                        <p>${ev.allDay ? 'All Day' : `${fmtDate(toDate(ev.start))} ${fmtTime(toDate(ev.start))} - ${fmtTime(toDate(ev.end))}`}</p>
                        <p>Lokasi: ${ev.location || '-'}</p>
                        <p>Akses: ${ev.access || '-'}</p>
                        <div class="mt-3">
                            ${actionButtons}
                            <button class="btn btn-secondary btn-sm" id="btnBack">Kembali</button>
                        </div>
                    `;

                    document.getElementById('btnBack').addEventListener('click', ()=> renderTodayEvents(selectedDate));
                    // Edit (hanya muncul kalau penyelenggara)
                    if (String(ev.created_by_id) === "{{ session('id_account') }}") {
                        document.getElementById('btnEdit').addEventListener('click', ()=>{
                            renderEditEvent(ev);
                        });
                    }

                    if (ev.title && ev.title.toLowerCase() === 'pemilu') {
                        document.getElementById(`btnJoin2`).addEventListener('click', (e) => {
                            e.stopPropagation(); // Jangan trigger detail

                            const btnJoin = document.getElementById(`btnJoin2`);
                            // Ganti tombol "Join" menjadi loading spinner
                            btnJoin.innerHTML = '<i class="spinner-border spinner-border-sm" role="status"></i> Loading...';
                            btnJoin.disabled = true; // Disable tombol agar tidak bisa ditekan lagi

                            // Panggil updateInvite untuk proses "join"
                            updateInvite(ev.id, 'join', ev.access).then(() => {
                                // Jika sukses, ganti tombol kembali menjadi "Join"
                                btnJoin.innerHTML = 'Join';
                                btnJoin.disabled = false; // Enable kembali tombol
                            }).catch((err) => {
                                // Jika error, kembalikan tombol ke "Join" dan beri pesan error
                                btnJoin.innerHTML = 'Join';
                                btnJoin.disabled = false;
                                alert('Terjadi kesalahan: ' + err.message);
                            });
                        });
                    }
                }

                function updateInvite(eventId, status, access) {
                    // bikin form hidden agar bisa kirim POST dengan CSRF token
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('event.invite') }}";

                    // csrf
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    // data
                    const eventInput = document.createElement('input');
                    eventInput.type = 'hidden';
                    eventInput.name = 'event_id';
                    eventInput.value = eventId;
                    form.appendChild(eventInput);

                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = status;
                    form.appendChild(statusInput);

                    const accessInput = document.createElement('input');
                    accessInput.type = 'hidden';
                    accessInput.name = 'access';
                    accessInput.value = access;
                    form.appendChild(accessInput);

                    document.body.appendChild(form);
                    form.submit(); // langsung redirect ke view dari controller
                }


                function goPrev(){ viewMonth--; if(viewMonth<0){viewMonth=11;viewYear--;} renderCalendar(); }
                function goNext(){ viewMonth++; if(viewMonth>11){viewMonth=0;viewYear++;} renderCalendar(); }
                function goToday(){ const n=new Date(); viewYear=n.getFullYear(); viewMonth=n.getMonth(); renderCalendar(); }

                btnPrev.addEventListener('click', goPrev);
                btnNext.addEventListener('click', goNext);
                btnToday.addEventListener('click', goToday);

                containerKedua.style.display = 'none';
                renderCalendar();
                renderUpcoming();
            })();
            </script>

                    </div>
                    </div>
                </div>
            </div>


<div class="card shadow-sm border-0 mb-4">
    <div class="row">
        <!-- Line Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title m-0">Grafik Tahunan</h5>
                        <select id="chartToggle" class="form-select form-select-sm w-auto">
                            <option value="revenue">Total Sales</option>
                            <option value="transactions">Jumlah Transaksi</option>
                        </select>
                    </div>
                    <div style="position: relative; height:400px;">
                        <canvas id="dashboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Status Klien</h5>
                    <div style="position: relative; height:400px;">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data dari Controller
const labels = {!! json_encode($labels) !!};
const revenueData = {!! json_encode($revenue) !!};
const transactionData = {!! json_encode($transactions) !!};
const statusData = {!! json_encode($statusCounts) !!};

// Chart Line: Pendapatan vs Transaksi
const ctx = document.getElementById('dashboardChart').getContext('2d');
let chartInstance = new Chart(ctx, {
type: 'line',
data: {
labels: labels,
datasets: [{
label: 'Penjualan',
data: revenueData,
borderColor: '#f15b2a',
fill: false,
tension: 0.4,
pointBackgroundColor: '#f15b2a'
}]
},
options: {
responsive: true,
maintainAspectRatio: false,
scales: {
y: {
    beginAtZero: true,
    ticks: {
        callback: val => 'Rp ' + val.toLocaleString()
    }
}
},
plugins: {
tooltip: { enabled: true },
legend: { display: true }
}
}
});

document.getElementById('chartToggle').addEventListener('change', function () {
const mode = this.value;
if (mode === 'revenue') {
chartInstance.data.datasets[0].label = 'Penjualan';
chartInstance.data.datasets[0].data = revenueData;
chartInstance.data.datasets[0].borderColor = '#f15b2a';
chartInstance.options.scales.y.ticks.callback = val => 'Rp ' + val.toLocaleString();
} else {
chartInstance.data.datasets[0].label = 'Jumlah Transaksi';
chartInstance.data.datasets[0].data = transactionData;
chartInstance.data.datasets[0].borderColor = '#0d6efd';
chartInstance.options.scales.y.ticks.callback = val => val;
}
chartInstance.update();
});

// Pie Chart
const pieLabels = Object.keys(statusData);
const pieValues = Object.values(statusData);
const pieColors = [
'#f15b2a', '#007bff', '#28a745', '#ffc107', '#6f42c1', '#dc3545', '#17a2b8'
];

const pieCtx = document.getElementById('statusPieChart').getContext('2d');
new Chart(pieCtx, {
type: 'pie',
data: {
labels: pieLabels,
datasets: [{
data: pieValues,
backgroundColor: pieColors.slice(0, pieLabels.length),
borderWidth: 1
}]
},
options: {
responsive: true,
plugins: {
legend: {
    position: 'bottom'
},
tooltip: {
    callbacks: {
        label: function (context) {
            const label = context.label || '';
            const value = context.raw || 0;
            return `${label}: ${value}`;
        }
    }
}
}
}
});
</script>

{{-- JAVASCRIPT --}}
<script>
function handleInputKuitansi(id_account, id_listing) {

    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';
    fetch("{{ url('/update-status-closing') }}", { // ✅ pakai route baru
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            id_account: id_account,
            id_listing: id_listing,
            status: 'Kuitansi'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ✅ Buka Google Form di tab baru
            window.open('https://docs.google.com/forms/d/e/1FAIpQLSedVS9P5oePrsoGub64dx0sH9kT5eYFUk22RlHrtYKWE3jYbQ/viewform', '_blank');

            // ✅ Reload halaman untuk update progress bar
            location.reload();
        } else {
            alert('Gagal update status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi error saat update status');
    });
}

function updateProgress(id_account, id_listing, status, callback = null) {

    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';
    fetch("{{ url('/update-status-closing') }}", { // ✅ ganti ke route yg update kedua tabel
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id_account, id_listing, status })
    }).then(res => res.json())
    .then(res => {
        console.log('Response:', res);
        if (res.success) {
            location.reload(); // ✅ Reload biar progress ikut naik
        }

        if (callback) callback();
    }).catch(err => {
        console.error('Error:', err);
    });
}

function handleInputRisalahLelang(id_account, id_listing) {

    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';
    fetch("{{ url('/update-status-closing') }}", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            id_account: id_account,
            id_listing: id_listing,
            status: 'Kutipan Risalah Lelang'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ✅ Buka Google Form untuk Risalah Lelang
            window.open('https://docs.google.com/forms/d/e/1FAIpQLSedVS9P5oePrsoGub64dx0sH9kT5eYFUk22RlHrtYKWE3jYbQ/viewform', '_blank');

            // ✅ Reload halaman untuk update progress bar
            location.reload();
        } else {
            alert('Gagal update status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi error saat update status');
    });
}

function formatNumberWithDots(value) {
        const number_string = value.replace(/\D/g, '');
        return number_string.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.rupiah-input');

        inputs.forEach(input => {
            input.addEventListener('input', function (e) {
                const cursorPos = this.selectionStart;
                const raw = this.value.replace(/\D/g, '');
                const formatted = formatNumberWithDots(raw);
                this.value = formatted;

                const diff = formatted.length - raw.length;
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            });
        });
    });

    // Fungsi jika kamu mau ambil angka asli dari input:
    function parseRupiahToNumber(formatted) {
        return parseInt(formatted.replace(/\./g, ''), 10);
    }

function updateStatus(id_account, id_listing, status, callback = null) {


    fetch("{{ url('/update-status') }}", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id_account, id_listing, status })
    }).then(res => res.json())
    .then(res => {
        console.log('Response:', res); // ✅ Tambahkan ini
        if (res.success) {
            location.reload();
        }

        if (callback) callback();
    }).catch(err => {
        console.error('Error:', err); // ✅ Tambahkan ini
    });
}

</script>

<script>

document.addEventListener('DOMContentLoaded', function () {
    // Data untuk grafik
    const salesData = {!! $salesData !!};
    const defaultYear = Object.keys(salesData)[0] ?? new Date().getFullYear();

    const ctx = document.getElementById('sales-chart')?.getContext('2d');
    let chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                     'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Sales (Rp)',
                data: salesData[defaultYear] ?? Array(12).fill(0),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => 'Rp ' + value.toLocaleString()
                    }
                }
            }
        }
    });

    document.getElementById("yearSelector")?.addEventListener("change", function () {
        const year = this.value;
        chart.data.datasets[0].data = salesData[year] ?? Array(12).fill(0);
        chart.update();
    });

    // Pie Chart untuk status klien
    const statusCounts = {!! json_encode($statusCounts) !!};

    const pieLabels = ['Follow Up', 'Pending', 'Buyer Meeting', 'Gagal', 'Closing'];
    const pieData = [
        statusCounts.followup ?? 0,
        statusCounts.pending ?? 0,
        statusCounts.buyer_meeting ?? 0,
        statusCounts.gagal ?? 0,
        statusCounts.closing ?? 0
    ];

    const total = pieData.reduce((a, b) => a + b, 0);
    const finalData = total === 0 ? [1] : pieData;
    const finalLabels = total === 0 ? ['No Data'] : pieLabels;
    const finalColors = total === 0
        ? ['#ddd']
        : ['#ffc107', '#17a2b8', '#6f42c1', '#dc3545', '#28a745'];

    const ctxPie = document.getElementById('status-piechart')?.getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: finalLabels,
            datasets: [{
                data: finalData,
                backgroundColor: finalColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
});
</script>

<!-- Modal Input Catatan -->
<div class="modal fade" id="tahapanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="tahapanForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Catatan Tahapan</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_id_account">
                <input type="hidden" id="modal_id_listing">
                <input type="hidden" id="modal_tahap">

                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea class="form-control" id="catatan" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTahapanModal(id_account, id_listing, tahap) {
        document.getElementById('modal_id_account').value = id_account;
        document.getElementById('modal_id_listing').value = id_listing;
        document.getElementById('modal_tahap').value = tahap;

        let placeholder = '';
        switch (tahap) {
            case 'kutipan_risalah_lelang':
                placeholder = 'Sedang dalam proses pengurusan kutipan risalah lelang, estimasi pengerjaan dalam __ hari.';
                break;
            case 'akte_grosse':
                placeholder = 'Sedang dalam proses pengurusan akte grosse, estimasi pengerjaan dalam __ hari.';
                break;
            case 'balik_nama':
                placeholder = 'Sedang dalam proses balik nama, estimasi pengerjaan dalam __ hari.';
                break;
            default:
                placeholder = '';
        }

        document.getElementById('catatan').value = placeholder;

        const modal = new bootstrap.Modal(document.getElementById('tahapanModal'));
        modal.show();
    }

    document.getElementById('tahapanForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const id_account = document.getElementById('modal_id_account').value;
    const id_listing = document.getElementById('modal_id_listing').value;
    const tahap = document.getElementById('modal_tahap').value;
    const catatan = document.getElementById('catatan').value;

    fetch("{{ url('/tahapan/storeregister') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            id_account,
            id_listing,
            tahap,
            catatan
        })
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            location.reload(); // ✅ langsung reload tanpa pesan
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        // Optional: bisa dihilangkan juga biar benar-benar tanpa pesan
    });
});
</script>

<!-- Modal -->
<div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="catatanModalLabel">Update Pengosongan</h5>
        </div>
        <div class="modal-body">
          <textarea id="catatanTextarea" class="form-control" rows="4"></textarea>
          <input type="hidden" id="catatan_id_account">
          <input type="hidden" id="catatan_id_listing">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary" onclick="simpanCatatan()">Simpan</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    function selesaikan(idAccount, idListing, button) {
    fetch("/pengosongan/selesai", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_account: idAccount,
            id_listing: idListing
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const parent = button.parentNode;
            parent.innerHTML = `<span class="text-success">Sudah Closing</span>`;

            // Update progress bar
            const progressBar = document.querySelector(`#row-${idAccount}-${idListing} .progress-bar`);
            if (progressBar) {
                progressBar.style.width = '100%';
                progressBar.innerText = '100%';
                progressBar.classList.remove('bg-warning', 'bg-secondary');
                progressBar.classList.add('bg-success');
            }
        }
    });
}

    function openCatatanModal(idAccount, idListing, existingNote) {
        document.getElementById('catatan_id_account').value = idAccount;
        document.getElementById('catatan_id_listing').value = idListing;
        document.getElementById('catatanTextarea').value = existingNote;

        const modal = new bootstrap.Modal(document.getElementById('catatanModal'));
        modal.show();
    }

    function simpanCatatan() {
        const idAccount = document.getElementById('catatan_id_account').value;
        const idListing = document.getElementById('catatan_id_listing').value;
        const catatan = document.getElementById('catatanTextarea').value;

        fetch("{{ route('pengosongan.catatan') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_account: idAccount,
                id_listing: idListing,
                catatan: catatan
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Atau kamu bisa update langsung DOM kalau mau smooth
            }
        });
    }
    </script>
<!-- JavaScript AJAX -->
<script>
    function startEksekusi(idAccount, idListing, button) {
    fetch("{{ route('pengosongan.eksekusi') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_account: idAccount,
            id_listing: idListing
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Replace the button with Catatan & Selesai
            const parent = button.parentNode;
            parent.innerHTML = `
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-info">Catatan: Sedang dalam proses pengosongan</button>
                    <button class="btn btn-sm btn-success">Selesai</button>
                </div>
            `;

            // Update progress bar (set jadi 50%)
            const progressBar = document.querySelector(`#row-${idAccount}-${idListing} .progress-bar`);
            if (progressBar) {
                progressBar.style.width = '50%';
                progressBar.innerText = '50%';
                progressBar.classList.remove('bg-secondary');
                progressBar.classList.add('bg-warning');
            }
        }
    });
}

    function showPengosonganAction(button) {
        // Ambil row dan hapus tombol lama
        const row = button.closest('tr');
        const actionCell = button.parentElement;
        actionCell.innerHTML = `
            <button class="btn btn-sm btn-info me-2" onclick="alert('Catatan dicatat')">Catatan</button>
            <button class="btn btn-sm btn-success" onclick="selesaikanPengosongan(this)">Selesai</button>
        `;

        // Update progress bar jadi 50%
        const progressTd = row.querySelector('td:nth-child(7)');
        progressTd.innerHTML = `
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-warning" role="progressbar"
                    style="width: 50%;"
                    aria-valuenow="50"
                    aria-valuemin="0"
                    aria-valuemax="100">
                    3 hari lagi
                </div>
            </div>
        `;
    }

    function selesaikanPengosongan(button) {
        const row = button.closest('tr');
        const progressTd = row.querySelector('td:nth-child(7)');
        const actionCell = button.parentElement;

        // Update progress ke 100%
        progressTd.innerHTML = `
            <div class="progress" style="height: 20px;">
                <div class="progress-bar bg-success" role="progressbar"
                    style="width: 100%;"
                    aria-valuenow="100"
                    aria-valuemin="0"
                    aria-valuemax="100">
                    Selesai
                </div>
            </div>
        `;

        // Update tombol jadi selesai
        actionCell.innerHTML = `<span class="text-success">Pengosongan selesai ✅</span>`;
    }
    </script>

        <!-- /.content -->
        <div class="clearfix"></div>
        <!-- Footer -->
        <footer class="site-footer">
            <div class="footer-inner bg-white">

            </div>
        </footer>
        <!-- /.site-footer -->
    </div>
    <!-- /#right-panel -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="assets/js/main.js"></script>

    <!--  Chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>

    <!--Chartist Chart-->
    <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/simpleweather@3.1.0/jquery.simpleWeather.min.js"></script>
    <script src="assets/js/init/weather-init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
    <script src="assets/js/init/fullcalendar-init.js"></script>

    <!--Local Stuff-->
    <script>
        jQuery(document).ready(function($) {
            "use strict";

            // Pie chart flotPie1
            var piedata = [
                { label: "Desktop visits", data: [[1,32]], color: '#5c6bc0'},
                { label: "Tab visits", data: [[1,33]], color: '#ef5350'},
                { label: "Mobile visits", data: [[1,35]], color: '#66bb6a'}
            ];

            $.plot('#flotPie1', piedata, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        innerRadius: 0.65,
                        label: {
                            show: true,
                            radius: 2/3,
                            threshold: 1
                        },
                        stroke: {
                            width: 0
                        }
                    }
                },
                grid: {
                    hoverable: true,
                    clickable: true
                }
            });
            // Pie chart flotPie1  End
            // cellPaiChart
            var cellPaiChart = [
                { label: "Direct Sell", data: [[1,65]], color: '#5b83de'},
                { label: "Channel Sell", data: [[1,35]], color: '#00bfa5'}
            ];
            $.plot('#cellPaiChart', cellPaiChart, {
                series: {
                    pie: {
                        show: true,
                        stroke: {
                            width: 0
                        }
                    }
                },
                legend: {
                    show: false
                },grid: {
                    hoverable: true,
                    clickable: true
                }

            });
            // cellPaiChart End
            // Line Chart  #flotLine5
            var newCust = [[0, 3], [1, 5], [2,4], [3, 7], [4, 9], [5, 3], [6, 6], [7, 4], [8, 10]];

            var plot = $.plot($('#flotLine5'),[{
                data: newCust,
                label: 'New Data Flow',
                color: '#fff'
            }],
            {
                series: {
                    lines: {
                        show: true,
                        lineColor: '#fff',
                        lineWidth: 2
                    },
                    points: {
                        show: true,
                        fill: true,
                        fillColor: "#ffffff",
                        symbol: "circle",
                        radius: 3
                    },
                    shadowSize: 0
                },
                points: {
                    show: true,
                },
                legend: {
                    show: false
                },
                grid: {
                    show: false
                }
            });
            // Line Chart  #flotLine5 End
            // Traffic Chart using chartist
            if ($('#traffic-chart').length) {
                var chart = new Chartist.Line('#traffic-chart', {
                  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                  series: [
                  [0, 18000, 35000,  25000,  22000,  0],
                  [0, 33000, 15000,  20000,  15000,  300],
                  [0, 15000, 28000,  15000,  30000,  5000]
                  ]
              }, {
                  low: 0,
                  showArea: true,
                  showLine: false,
                  showPoint: false,
                  fullWidth: true,
                  axisX: {
                    showGrid: true
                }
            });

                chart.on('draw', function(data) {
                    if(data.type === 'line' || data.type === 'area') {
                        data.element.animate({
                            d: {
                                begin: 2000 * data.index,
                                dur: 2000,
                                from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                                to: data.path.clone().stringify(),
                                easing: Chartist.Svg.Easing.easeOutQuint
                            }
                        });
                    }
                });
            }
            // Traffic Chart using chartist End
            //Traffic chart chart-js
            if ($('#TrafficChart').length) {
                var ctx = document.getElementById( "TrafficChart" );
                ctx.height = 150;
                var myChart = new Chart( ctx, {
                    type: 'line',
                    data: {
                        labels: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul" ],
                        datasets: [
                        {
                            label: "Visit",
                            borderColor: "rgba(4, 73, 203,.09)",
                            borderWidth: "1",
                            backgroundColor: "rgba(4, 73, 203,.5)",
                            data: [ 0, 2900, 5000, 3300, 6000, 3250, 0 ]
                        },
                        {
                            label: "Bounce",
                            borderColor: "rgba(245, 23, 66, 0.9)",
                            borderWidth: "1",
                            backgroundColor: "rgba(245, 23, 66,.5)",
                            pointHighlightStroke: "rgba(245, 23, 66,.5)",
                            data: [ 0, 4200, 4500, 1600, 4200, 1500, 4000 ]
                        },
                        {
                            label: "Targeted",
                            borderColor: "rgba(40, 169, 46, 0.9)",
                            borderWidth: "1",
                            backgroundColor: "rgba(40, 169, 46, .5)",
                            pointHighlightStroke: "rgba(40, 169, 46,.5)",
                            data: [1000, 5200, 3600, 2600, 4200, 5300, 0 ]
                        }
                        ]
                    },
                    options: {
                        responsive: true,
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        }

                    }
                } );
            }
            //Traffic chart chart-js  End
            // Bar Chart #flotBarChart
            $.plot("#flotBarChart", [{
                data: [[0, 18], [2, 8], [4, 5], [6, 13],[8,5], [10,7],[12,4], [14,6],[16,15], [18, 9],[20,17], [22,7],[24,4], [26,9],[28,11]],
                bars: {
                    show: true,
                    lineWidth: 0,
                    fillColor: '#ffffff8a'
                }
            }], {
                grid: {
                    show: false
                }
            });
            // Bar Chart #flotBarChart End
        });
    </script>
</body>
</html>


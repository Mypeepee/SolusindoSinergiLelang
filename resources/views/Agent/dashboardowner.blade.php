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

<style>
.property-card {
    cursor: pointer;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s ease;
    padding: 10px;
}
.property-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}
.property-card.disabled {
    pointer-events: none;
    opacity: 0.5;
}
.property-card .action-btn {
    transition: all 0.3s ease;
}
</style>


<div class="container-fluid">
    <div class="row g-3">
        @foreach ($properties->chunk(4) as $chunk)
            <div class="row">
                @foreach ($chunk as $property)
                    @php
                        $tipeFormatted = ucwords($property->tipe);
                    @endphp

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card shadow-sm border-0 p-2 property-card"
                             data-property="{{ strtolower($property->tipe) }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Kiri: Text -->
                                <div class="text-start">
                                    <h6 class="fw-semibold mb-1">{{ $tipeFormatted }}</h6>
                                    <p class="mb-0">
                                        {{ $property->total }} Asset{{ $property->total != 1 ? 's' : '' }}
                                    </p>
                                </div>
                                <!-- Kanan: Gambar -->
                                <div>
                                    <img src="{{ asset('img/' . strtolower($property->tipe) . '.png') }}"
                                         alt="Icon {{ $tipeFormatted }}"
                                         class="img-fluid" style="width:40px;height:40px;">
                                </div>
                            </div>

                            <div class="action-btn mt-3" style="display: none;">
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <button type="button"
                                            class="btn btn-warning btn-sm px-3 scrape-btn"
                                            data-tipe="{{ strtolower($property->tipe) }}">
                                        <i class="fas fa-database me-1"></i> Scrape
                                    </button>

                                    <a href="{{ route('property.export', ['tipe' => strtolower($property->tipe)]) }}"
                                       class="btn btn-outline-primary btn-sm px-3">
                                        <i class="fas fa-file-csv me-1"></i> Export CSV
                                    </a>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    document.querySelectorAll('.scrape-btn').forEach(function (btn) {
                                        btn.addEventListener('click', function () {
                                            const tipe = this.getAttribute('data-tipe');
                                            if (!tipe) return;

                                            fetch("{{ route('property.scrape') }}", {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({ tipe: tipe })
                                            })
                                            .then(response => {
                                                if (!response.ok) throw new Error("Request gagal");
                                                return response.json();
                                            })
                                            .then(data => {
                                                alert(data.message);
                                                location.reload(); // reload data kalau perlu
                                            })
                                            .catch(error => {
                                                alert("Gagal menjalankan scrape: " + error.message);
                                            });
                                        });
                                    });
                                });
                                </script>


                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>


<div class="container-fluid">

    @php
    $allowed = ['verifikasi','progress','performance','stoker','calendar'];
    $tab = request('tab');
    if (!in_array($tab, $allowed, true)) {
        $tab = 'verifikasi'; // default benar2 fresh
    }

    // Kalau tab=stoker tapi TIDAK ada filter Stoker, paksa balik ke verifikasi
    if ($tab === 'stoker' && !request()->hasAny(['search','property_type','province','city','district'])) {
        $tab = 'verifikasi';
    }
  @endphp
<script>
    (function(){
      const url = new URL(window.location.href);
      const p = url.searchParams;
      const hasFilters = ['search','property_type','province','city','district'].some(k => p.has(k));
      if (p.get('tab') === 'stoker' && !hasFilters) {
        p.delete('tab');
        const qs = p.toString();
        history.replaceState({}, '', url.pathname + (qs ? '?' + qs : ''));
      }
    })();
    </script>


  <ul class="nav nav-tabs" id="mainTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link {{ $tab==='verifikasi' ? 'active' : '' }}"
              id="verifikasi-tab" data-bs-toggle="tab" data-bs-target="#verifikasi"
              type="button" role="tab" aria-controls="verifikasi"
              aria-selected="{{ $tab==='verifikasi' ? 'true' : 'false' }}">
        ✅ Verifikasi
      </button>
    </li>

    <li class="nav-item" role="presentation">
      <button class="nav-link {{ $tab==='performance' ? 'active' : '' }}"
              id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance"
              type="button" role="tab" aria-controls="performance"
              aria-selected="{{ $tab==='performance' ? 'true' : 'false' }}">
        📈 Performance
      </button>
    </li>

    <li class="nav-item" role="presentation">
      <button class="nav-link {{ $tab==='stoker' ? 'active' : '' }}"
              id="stoker-tab" data-bs-toggle="tab" data-bs-target="#stoker"
              type="button" role="tab" aria-controls="stoker"
              aria-selected="{{ $tab==='stoker' ? 'true' : 'false' }}">
        🏠 Stoker
      </button>
    </li>

    <li class="nav-item" role="presentation">
      <button class="nav-link {{ $tab==='calendar' ? 'active' : '' }}"
              id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar"
              type="button" role="tab" aria-controls="calendar"
              aria-selected="{{ $tab==='calendar' ? 'true' : 'false' }}">
        🗓️ Calendar
      </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $tab==='export' ? 'active' : '' }}" id="export-tab"
                data-bs-toggle="tab" data-bs-target="#export" type="button" role="tab"
                aria-controls="export" aria-selected="{{ $tab==='export' ? 'true' : 'false' }}">
          ⬇️ Export
        </button>
    </li>

    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $tab==='transaksi' ? 'active' : '' }}"
                id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi"
                type="button" role="tab" aria-controls="transaksi"
                aria-selected="{{ $tab==='transaksi' ? 'true' : 'false' }}">
          💳 Transaksi
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link {{ $tab==='progress' ? 'active' : '' }}"
                id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress"
                type="button" role="tab" aria-controls="progress"
                aria-selected="{{ $tab==='progress' ? 'true' : 'false' }}">
          📦 Progress Lelang
        </button>
      </li>
  </ul>

    <div class="tab-content mt-3" id="mainTabsContent">
        <!-- Verifikasi -->
        <div class="tab-pane fade {{ $tab==='verifikasi' ? 'show active' : '' }}" id="verifikasi" role="tabpanel" aria-labelledby="verifikasi-tab">
            <!-- Sub Tabs Verifikasi -->
            <ul class="nav nav-pills mb-3" id="verifikasiSubTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="verifikasi-client-tab" data-bs-toggle="pill" data-bs-target="#verifikasi-client" type="button" role="tab" aria-controls="verifikasi-client" aria-selected="true">
                        👤 Client
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="verifikasi-agent-tab" data-bs-toggle="pill" data-bs-target="#verifikasi-agent" type="button" role="tab" aria-controls="verifikasi-agent" aria-selected="false">
                        🧑‍💼 Agent
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="verifikasiSubTabsContent">
                <!-- Client Table -->
                <div class="tab-pane fade show active" id="verifikasi-client" role="tabpanel" aria-labelledby="verifikasi-client-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white fw-semibold">👤 Verifikasi Client</div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-hover w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>KTP</th>
                                            <th>NPWP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pendingClients as $index => $pclient)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $pclient->id_account }}</span></td>
                                                <td>{{ $pclient->nama }}</td>
                                                <td>{{ $pclient->email }}</td>
                                                <td>
                                                    <a href="https://drive.google.com/file/d/{{ $pclient->gambar_ktp }}/view" target="_blank" class="btn btn-outline-primary">
                                                        <i class="fa fa-id-card me-1"></i> Lihat KTP
                                                    </a>

                                                </td>
                                                <td>
                                                    <a href="https://drive.google.com/file/d/{{ $pclient->gambar_npwp }}/view" target="_blank" class="btn btn-outline-primary">
                                                        <i class="fa fa-id-card me-1"></i> Lihat NPWP
                                                    </a>
                                                </td>
                                                <td style="min-width: 230px;">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <!-- Form Verifikasi -->
                                                        <form action="{{ route('verify.client', $pclient->id_account) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                                <i class="fa fa-check me-1"></i> Verifikasi
                                                            </button>
                                                        </form>

                                                        <!-- Tombol Tolak -->
                                                        <form action="{{ route('reject.client', $pclient->id_account) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                                                                <i class="fa fa-times me-1"></i> Tolak
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">Belum ada klien yang mendaftar saat ini.</td>
                                            </tr>
                                            @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agent Table -->
                <div class="tab-pane fade" id="verifikasi-agent" role="tabpanel" aria-labelledby="verifikasi-agent-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white fw-semibold">🧑‍💼 Verifikasi Agent</div>
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-hover w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Nama Lengkap</th>
                                            <th>No. Telepon</th>
                                            <th>KTP</th>
                                            <th>NPWP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pendingAgents as $index => $agent)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $agent->id_account }}</span></td>
                                                <td>{{ $agent->username }}</td>
                                                <td>{{ $agent->nama }}</td>
                                                <td>+62{{ ltrim($agent->nomor_telepon, '0') }}</td>
                                                <td>
                                                    <a href="https://drive.google.com/file/d/{{ $agent->gambar_ktp }}/view"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                        <i class="fa fa-id-card me-1"></i> Lihat KTP
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="https://drive.google.com/file/d/{{ $agent->gambar_npwp }}/view"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                        <i class="fa fa-file-invoice me-1"></i> Lihat NPWP
                                                    </a>
                                                </td>

                                                <td style="min-width: 230px;">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <!-- Form Verifikasi -->
                                                        <form action="{{ route('verify.agent', $agent->id_account) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                                <i class="fa fa-check me-1"></i> Verifikasi
                                                            </button>
                                                        </form>

                                                        <!-- Tombol Tolak -->
                                                        <form action="{{ route('reject.agent', $agent->id_account) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                                                                <i class="fa fa-times me-1"></i> Tolak
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">Belum ada agen yang mendaftar saat ini.</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Lelang -->
<div class="tab-pane fade {{ $tab==='progress' ? 'show active' : '' }}" id="progress" role="tabpanel" aria-labelledby="progress-tab">

    {{-- ORIGINAL (disimpan, tidak dihapus):
    <ul class="nav nav-pills mb-3" id="progressSubTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="progress-agent-tab" data-bs-toggle="pill" data-bs-target="#progress-agent" type="button" role="tab" aria-controls="progress-agent" aria-selected="true">
                🧑‍💼 Agent
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="progress-register-tab" data-bs-toggle="pill" data-bs-target="#progress-register" type="button" role="tab" aria-controls="progress-register" aria-selected="false">
                📝 Register
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="progress-pengosongan-tab" data-bs-toggle="pill" data-bs-target="#progress-pengosongan" type="button" role="tab" aria-controls="progress-pengosongan" aria-selected="false">
                🏠 Pengosongan
            </button>
        </li>
    </ul>
    --}}

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-primary">📋 Progress Lelang (Semua Transaksi)</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table align-middle table-hover text-center" id="progressAllTable">
                <thead class="table-light align-middle">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="min-width: 180px;">Agent</th>
                        <th style="width: 120px;">Property ID</th>
                        <th style="min-width: 260px;">Lokasi</th>
                        <th style="min-width: 140px;">Harga</th>
                        <th style="min-width: 180px;">Progress</th>
                        <th style="min-width: 140px;">Status</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($progressTransactions as $index => $trx)
                        @php
                            $status = $trx->status ?? 'Closing';

                            // Progress single timeline (naik terus sampai selesai)
                            $progress = match($status) {
                                'Closing' => 0,
                                'Kuitansi' => 20,
                                'Kode Billing' => 40,
                                'Kutipan Risalah Lelang' => 60,
                                'Akte Grosse' => 80,
                                'Balik Nama' => 90,
                                'Eksekusi Pengosongan' => 95,
                                'Selesai' => 100,
                                default => 0,
                            };

                            $barClass = match($status) {
                                'Selesai' => 'bg-success',
                                'Eksekusi Pengosongan' => 'bg-warning',
                                default => 'bg-secondary',
                            };

                            // Detail URL: kalau id_klien null, tetap bisa buka pakai route opsional
                            $detailUrl = !empty($trx->id_klien)
                                ? route('dashboard.detail', ['id_listing' => $trx->id_listing, 'id_account' => $trx->id_klien])
                                : route('dashboard.detail', ['id_listing' => $trx->id_listing]);
                        @endphp

                        <tr id="row-{{ $trx->id_transaction }}-{{ $trx->id_listing }}">
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start">
                                <div class="fw-semibold">{{ $trx->agent_nama }}</div>
                                <div class="small text-muted">{{ $trx->id_agent }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $trx->id_listing }}</span>
                            </td>
                            <td class="text-start">
                                <span class="text-truncate d-inline-block" style="max-width: 360px;">
                                    {{ $trx->lokasi }}
                                </span>
                            </td>
                            <td>
                                Rp {{ number_format((int) ($trx->harga ?? 0), 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="progress" style="height: 16px;">
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
                            <td>
                                <span class="badge bg-light text-dark">{{ $status }}</span>
                            </td>
                            <td>
                                <a href="{{ $detailUrl }}" class="btn btn-sm bg-secondary text-white rounded-pill px-3 shadow-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>


        <!-- Performance -->
        @php
  $ptab = request('ptab', 'agent'); // sub-tab performance
@endphp
<div class="tab-pane fade {{ $tab==='performance' ? 'show active' : '' }}" id="performance" role="tabpanel" aria-labelledby="performance-tab">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold text-primary">📊 Performance</h5>
      </div>

      <div class="card-body">
        <!-- Sub Tabs Performance -->
        <ul class="nav nav-pills mb-3" id="performanceSubTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link {{ $ptab==='agent' ? 'active' : '' }}"
                    id="performance-agent-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#performance-agent"
                    type="button" role="tab"
                    aria-controls="performance-agent"
                    aria-selected="{{ $ptab==='agent' ? 'true' : 'false' }}">
              🧑‍💼 Agent
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link {{ $ptab==='client' ? 'active' : '' }}"
                    id="performance-client-tab"
                    data-bs-toggle="pill"
                    data-bs-target="#performance-client"
                    type="button" role="tab"
                    aria-controls="performance-client"
                    aria-selected="{{ $ptab==='client' ? 'true' : 'false' }}">
              👤 Client
            </button>
          </li>
        </ul>

        <div class="tab-content" id="performanceSubTabsContent">

            {{-- ========== Performance - Agent (konten lama kamu dipindah ke sini) ========== --}}
            <div class="tab-pane fade {{ $ptab==='agent' ? 'show active' : '' }}" id="performance-agent" role="tabpanel" aria-labelledby="performance-agent-tab">

              {{-- Filter Controls (tetap) --}}
              {{-- <div class="row mb-3">
                <div class="col-md-3">
                  <input type="text" id="filterIdAgent" class="form-control" placeholder="Cari ID Agent">
                </div>
                <div class="col-md-3">
                  <input type="text" id="filterNama" class="form-control" placeholder="Cari Nama">
                </div>
                <div class="col-md-2">
                  <select id="filterStatus" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Diterminasi">Diterminasi</option>
                    <option value="Pending">Pending</option>
                  </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                  <select id="sortListing" class="form-select">
                    <option value="">Sort Listing</option>
                    <option value="asc">Low → High</option>
                    <option value="desc">High → Low</option>
                  </select>
                  <select id="sortPenjualan" class="form-select">
                    <option value="">Sort Penjualan</option>
                    <option value="asc">Low → High</option>
                    <option value="desc">High → Low</option>
                  </select>
                  <select id="sortKomisi" class="form-select">
                    <option value="">Sort Komisi</option>
                    <option value="asc">Low → High</option>
                    <option value="desc">High → Low</option>
                  </select>
                </div>
              </div> --}}

              <div class="table-responsive">
                <table class="table align-middle table-hover" id="performanceTable">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>ID Agent</th>

                      <th>Nama</th>

                      {{-- NEW: Kolom Upline --}}
                      <th>Upline</th>

                      <th>Status</th>

                      {{-- NEW: Header sort klik (arrow kiri) --}}
                      <th>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 js-sort"
                                data-sort-key="ikut_pemilu" data-sort-dir="">
                          <span class="me-1 js-sort-arrow">↕</span>
                          <span>Ikut Pemilu</span>
                        </button>
                      </th>

                      <th>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 js-sort"
                                data-sort-key="jumlah_listing" data-sort-dir="">
                          <span class="me-1 js-sort-arrow">↕</span>
                          <span>Jumlah Listing</span>
                        </button>
                      </th>

                      <th>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 js-sort"
                                data-sort-key="jumlah_penjualan" data-sort-dir="">
                          <span class="me-1 js-sort-arrow">↕</span>
                          <span>Jumlah Penjualan</span>
                        </button>
                      </th>

                      <th>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 js-sort"
                                data-sort-key="total_komisi" data-sort-dir="">
                          <span class="me-1 js-sort-arrow">↕</span>
                          <span>Total Komisi</span>
                        </button>
                      </th>

                      <th>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 js-sort"
                                data-sort-key="referral_click" data-sort-dir="">
                          <span class="me-1 js-sort-arrow">↕</span>
                          <span>Referral Click</span>
                        </button>
                      </th>
                    </tr>
                  </thead>

                  <tbody id="performanceBody">
                    @forelse ($performanceAgents as $index => $agent)
                      <tr>
                        <td>{{ $index + 1 }}</td>

                        <td class="agent-id">{{ $agent->id_agent }}</td>

                        <td class="agent-nama">{{ $agent->nama }}</td>

                        {{-- NEW: isi upline nama --}}
                        <td class="agent-upline">
                          {{ $agent->upline_nama ?? '-' }}
                        </td>

                        <td class="agent-status">
                          <div class="dropdown d-inline-block position-relative" data-id-account="{{ $agent->id_account }}">
                            <button
                              class="btn btn-sm rounded-pill fw-semibold status-btn dropdown-toggle
                                     {{ $agent->status === 'Aktif' ? 'btn-active' : 'btn-terminated' }}"
                              type="button"
                              data-bs-toggle="dropdown"
                              aria-expanded="false">
                              {{ $agent->status }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                              <li>
                                <button class="dropdown-item js-choose-status" data-status="Aktif">
                                  Aktif
                                </button>
                              </li>
                              <li><hr class="dropdown-divider"></li>
                              <li>
                                <button class="dropdown-item text-danger js-choose-status" data-status="Diterminasi">
                                  Diterminasi
                                </button>
                              </li>
                            </ul>

                            <div class="spinner-border spinner-border-sm text-muted d-none js-spin"
                                 style="position:absolute; right:-8px; top:-8px;" role="status"></div>
                          </div>
                        </td>

                        <style>
                          .btn-active {
                            background:#ff5a1f; /* oranye brand-like */
                            color:#fff;
                            border-color:#ff5a1f;
                          }
                          .btn-active:hover { filter:brightness(0.95); }

                          .btn-terminated {
                            background:#6c757d; /* abu */
                            color:#fff;
                            border-color:#6c757d;
                          }
                          .btn-terminated:hover { filter:brightness(0.95); }

                          .status-btn { padding:.25rem .75rem; }

                          /* NEW: kecilin tombol sort biar rapih */
                          #performanceTable thead .js-sort {
                            color: inherit;
                            font-weight: 600;
                          }
                          #performanceTable thead .js-sort:hover {
                            opacity: .85;
                          }
                          #performanceTable thead .js-sort:focus {
                            box-shadow: none;
                          }
                        </style>

                        <script>
                          (() => {
                            const CSRF = '{{ csrf_token() }}';
                            const routeTpl = `{{ route('agents.update-status-agent', ':id') }}`; // ⬅️ update

                            function setUI(drop, status) {
                              const btn = drop.querySelector('.status-btn');
                              btn.textContent = status;
                              btn.classList.remove('btn-active','btn-terminated');
                              btn.classList.add(status === 'Aktif' ? 'btn-active' : 'btn-terminated');
                            }
                            function setLoading(drop, on) {
                              const spin = drop.querySelector('.js-spin');
                              drop.querySelector('.status-btn').disabled = on;
                              if (spin) spin.classList.toggle('d-none', !on);
                            }

                            document.querySelectorAll('.agent-status .dropdown').forEach(drop => {
                              drop.addEventListener('click', async (e) => {
                                const item = e.target.closest('.js-choose-status');
                                if (!item) return;

                                const newStatus = item.dataset.status;
                                const btn  = drop.querySelector('.status-btn');
                                const prev = btn.textContent.trim();
                                if (newStatus === prev) return;

                                const idAccount = drop.dataset.idAccount;
                                if (!idAccount) { alert('ID Account tidak ditemukan'); return; }

                                if (newStatus === 'Diterminasi' &&
                                    !confirm('Yakin ubah ke "Diterminasi"? Role agent akan menjadi "User".')) {
                                  return;
                                }

                                setLoading(drop, true);
                                setUI(drop, newStatus);

                                try {
                                  const url = routeTpl.replace(':id', idAccount);
                                  const res = await fetch(url, {
                                    method: 'PATCH',
                                    headers: {
                                      'X-CSRF-TOKEN': CSRF,
                                      'Accept': 'application/json',
                                      'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ status: newStatus })
                                  });
                                  if (!res.ok) throw new Error('save-failed');
                                } catch (err) {
                                  setUI(drop, prev);
                                  alert('Gagal memperbarui status. Coba lagi.');
                                } finally {
                                  setLoading(drop, false);
                                }
                              });
                            });
                          })();
                        </script>

                        <td class="agent-ikut-pemilu">
                          <span class="badge bg-primary">{{ (int) ($agent->ikut_pemilu ?? 0) }}</span>
                        </td>

                        <td class="agent-listing">{{ $agent->jumlah_listing }}</td>

                        <td class="agent-penjualan">{{ $agent->jumlah_penjualan }}</td>

                        <td class="agent-komisi" data-komisi="{{ $agent->total_komisi ?? 0 }}">
                          Rp {{ number_format($agent->total_komisi ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="agent-share-listing text-center">
                          {{ (int) ($agent->share_listing ?? 0) }}
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data agent.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              {{-- =========================================================
                   NEW: Script sorting header (klik panah ↑/↓)
                   - Sort numeric untuk: ikut_pemilu, jumlah_listing, jumlah_penjualan, total_komisi, referral_click
                   - Tidak menghapus filter/select sort yang sudah ada
                 ========================================================= --}}
              <script>
                (function(){
                  const table = document.getElementById('performanceTable');
                  const tbody = document.getElementById('performanceBody');
                  if (!table || !tbody) return;

                  function digitsOnly(v){
                    return String(v || '').replace(/[^\d\-]/g, '');
                  }

                  function toInt(v){
                    const n = parseInt(digitsOnly(v), 10);
                    return isNaN(n) ? 0 : n;
                  }

                  function getValue(tr, key){
                    if (!tr) return 0;

                    if (key === 'ikut_pemilu') {
                      const el = tr.querySelector('.agent-ikut-pemilu');
                      return toInt(el ? el.textContent : 0);
                    }

                    if (key === 'jumlah_listing') {
                      const el = tr.querySelector('.agent-listing');
                      return toInt(el ? el.textContent : 0);
                    }

                    if (key === 'jumlah_penjualan') {
                      const el = tr.querySelector('.agent-penjualan');
                      return toInt(el ? el.textContent : 0);
                    }

                    if (key === 'total_komisi') {
                      const el = tr.querySelector('.agent-komisi');
                      if (el && el.dataset && el.dataset.komisi !== undefined) {
                        return toInt(el.dataset.komisi);
                      }
                      return toInt(el ? el.textContent : 0);
                    }

                    if (key === 'referral_click') {
                      const el = tr.querySelector('.agent-share-listing');
                      return toInt(el ? el.textContent : 0);
                    }

                    return 0;
                  }

                  function setArrow(btn, dir){
                    const arrow = btn.querySelector('.js-sort-arrow');
                    if (!arrow) return;

                    // dir: 'asc' => low->high (▲), 'desc' => high->low (▼), '' => netral (↕)
                    if (dir === 'asc')  arrow.textContent = '▲';
                    if (dir === 'desc') arrow.textContent = '▼';
                    if (!dir) arrow.textContent = '↕';
                  }

                  function clearOtherSortButtons(activeBtn){
                    table.querySelectorAll('thead .js-sort').forEach(b => {
                      if (b !== activeBtn) {
                        b.dataset.sortDir = '';
                        setArrow(b, '');
                      }
                    });
                  }

                  function sortRowsBy(key, dir){
                    const rows = Array.from(tbody.querySelectorAll('tr'));

                    // simpan index awal untuk stable sort
                    const withIndex = rows.map((tr, idx) => ({ tr, idx }));

                    withIndex.sort((a, b) => {
                      const va = getValue(a.tr, key);
                      const vb = getValue(b.tr, key);

                      if (va === vb) return a.idx - b.idx;

                      if (dir === 'asc')  return va - vb;  // low -> high
                      if (dir === 'desc') return vb - va;  // high -> low

                      return 0;
                    });

                    // render ulang (append akan memindahkan node)
                    withIndex.forEach(x => tbody.appendChild(x.tr));
                  }

                  // klik tombol sort di header
                  table.querySelectorAll('thead .js-sort').forEach(btn => {
                    btn.addEventListener('click', function(e){
                      e.preventDefault();

                      const key = this.dataset.sortKey || '';
                      if (!key) return;

                      // toggle: default pertama kali => desc (high->low)
                      let dir = this.dataset.sortDir || '';
                      dir = (dir === 'desc') ? 'asc' : 'desc';

                      this.dataset.sortDir = dir;

                      clearOtherSortButtons(this);
                      setArrow(this, dir);

                      sortRowsBy(key, dir);
                    });
                  });

                  // tetap dukung select sort yang sudah kamu punya (kalau kamu masih pakai)
                  const sortListing   = document.getElementById('sortListing');
                  const sortPenjualan = document.getElementById('sortPenjualan');
                  const sortKomisi    = document.getElementById('sortKomisi');

                  if (sortListing) {
                    sortListing.addEventListener('change', function(){
                      const v = this.value || '';
                      if (!v) return;
                      sortRowsBy('jumlah_listing', v);
                    });
                  }
                  if (sortPenjualan) {
                    sortPenjualan.addEventListener('change', function(){
                      const v = this.value || '';
                      if (!v) return;
                      sortRowsBy('jumlah_penjualan', v);
                    });
                  }
                  if (sortKomisi) {
                    sortKomisi.addEventListener('change', function(){
                      const v = this.value || '';
                      if (!v) return;
                      sortRowsBy('total_komisi', v);
                    });
                  }

                })();
              </script>

            </div>
            {{-- ========== /Performance - Agent ========== --}}



          {{-- ========== Performance - Client (baru) ========== --}}
          <div class="tab-pane fade {{ $ptab==='client' ? 'show active' : '' }}" id="performance-client" role="tabpanel" aria-labelledby="performance-client-tab">
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                      <tr>
                        <th>#</th>
                        <th>ID Account</th>
                        <th>Nama</th>
                        <th>Referral (Nama Agent)</th>
                        <th>Kota</th>
                        <th>Pekerjaan</th>
                        {{-- <th>Status</th> --}}
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($performanceClients as $i => $c)
                        <tr>
                          <td>{{ $i + 1 }}</td>
                          <td><span class="badge bg-light text-dark">{{ $c->id_account }}</span></td>
                          <td>{{ $c->nama }}</td>
                          <td>{{ $c->nama_agent }}</td>
                          <td>{{ $c->kota }}</td>
                          <td>{{ $c->pekerjaan ?? '-' }}</td>
                          {{-- <td>
                            @php
                              $status = $c->status_verifikasi ?? '-';
                              $map = [
                                'Pending' => 'warning',
                                'Verified' => 'success',
                                'Ditolak' => 'danger',
                              ];
                              $cls = $map[$status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $cls }}">{{ $status }}</span>
                          </td> --}}
                        </tr>
                      @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data client.</td></tr>
                      @endforelse
                    </tbody>
                  </table>

            </div>
          </div>
          {{-- ========== /Performance - Client ========== --}}

        </div>
      </div>
    </div>
  </div>

{{-- ========== Stoker ========== --}}
<div class="tab-pane fade {{ $tab==='stoker' ? 'show active' : '' }}" id="stoker" role="tabpanel" aria-labelledby="stoker-tab">
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
                    <option value="inventaris" @selected(request('property_type')==='inventaris')>Inventaris</option>
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
            <!-- ====== HEADER (ASLI, TANPA PERUBAHAN SATU HURUF PUN) ====== -->
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <form id="stoker-bulk-form" action="{{ route('stoker.bulkSold') }}" method="POST" class="m-0">
                  @csrf
                  <input type="hidden" name="selected_ids" id="stoker_selected_ids_input">
                  <input type="hidden" name="return_tab" value="stoker">
                  <button type="submit" id="btn-stoker-bulk-sold" class="btn-bulk" disabled title="Centang minimal 1 listing">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                      <path d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"></path>
                    </svg>
                    <span>Tandai Terjual</span>
                    <span class="badge bg-dark text-white badge-count ms-1" id="stoker-selected-counter">0</span>
                  </button>
                </form>

                <!-- Tombol Clear All -->
                <button type="button" id="stoker-clear-all" class="btn btn-link btn-sm text-danger px-0 ms-2">
                  Hapus semua
                </button>
              </div>
            </div>

            <style>
              #stoker-clear-all { text-decoration: none; }
              #stoker-clear-all:hover { text-decoration: underline; }
            </style>

            <!-- ====== BODY (TAMBAHAN: area tag ID terpilih) ====== -->
            <div class="card-body">
              <div id="stoker-selected-preview" class="d-flex flex-wrap gap-2 small"></div>
              <hr class="my-3">
              <div class="text-muted small">
                Centang item di halaman mana pun. Pilihan disimpan sementara di browser sampai kamu klik <strong>Tandai Terjual</strong>.
              </div>
            </div>

            <style>
              /* Styling pill/ tag ID */
              #stoker-selected-preview .btn {
                padding: .25rem .5rem;
                border-radius: .5rem;
                font-weight: 600;
                line-height: 1.1;
              }
            </style>
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

        // =========================
        //   CLEAR ALL (TAMBAHAN)
        // =========================
        (function(){
          const clearBtn = document.getElementById('stoker-clear-all');
          function stokerClearAll(){
            try { localStorage.setItem(KEY, '[]'); } catch(e){ localStorage.removeItem(KEY); }
            document.querySelectorAll('#stoker-list-inner .row-check').forEach(cb => cb.checked = false);
            const master = document.getElementById('check_all_stoker');
            if (master){ master.checked = false; master.indeterminate = false; }
            updateCounterAndHidden();
            renderPreview();
            syncMaster();
          }
          window.stokerClearAll = stokerClearAll;
          clearBtn?.addEventListener('click', stokerClearAll);
        })();
      })();
    });
    </script>

        {{-- ========== Stoker ========== --}}

        <!-- Calendar -->
        <div class="tab-pane fade {{ $tab==='calendar' ? 'show active' : '' }}" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
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

        containerKeduaBody.innerHTML = `
            <p><strong>${ev.title}</strong></p>
            <p>Penyelenggara: ${ev.created_by || '-'}</p>
            <p><strong>Deskripsi:</strong><br>${ev.description || '-'}</p>
            <p>${ev.allDay ? 'All Day' : `${fmtDate(toDate(ev.start))} ${fmtTime(toDate(ev.start))} - ${fmtTime(toDate(ev.end))}`}</p>
            <p>Lokasi: ${ev.location || '-'}</p>
            <p>Akses: ${ev.access || '-'}</p>
            <div class="mt-3">
                ${actionButtons}
                <button class="btn btn-warning btn-sm me-2" id="btnEdit">Edit</button>
                <button class="btn btn-secondary btn-sm" id="btnBack">Kembali</button>
            </div>
        `;

        document.getElementById('btnBack').addEventListener('click', ()=> renderTodayEvents(selectedDate));
        document.getElementById('btnEdit').addEventListener('click', ()=> renderEditEvent(ev));

        if(ev.title && ev.title.toLowerCase() === 'pemilu'){
            document.getElementById('btnJoin').addEventListener('click', ()=> updateInvite(ev.id, 'join', ev.access));
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

{{-- EXPORTtttttttttttt --}}
<style>
/* Spinner overlay minimalis untuk area export-list */
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

<div class="tab-pane fade {{ $tab==='export' ? 'show active' : '' }}" id="export" role="tabpanel" aria-labelledby="export-tab">
  <div class="row">
    <div class="col-lg-9">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-semibold text-primary">⬇️ Export Properti</h5>
          {{-- <small class="text-muted" id="export-selected-counter">0 dipilih</small> --}}
        </div>

        <div class="card-body">
          <!-- Form Filter -->
          <form id="export-filter-form"
          method="GET"
          action="{{ route('dashboard.owner') }}"
          class="row g-3 align-items-end p-3 rounded shadow-sm bg-white mb-3">

            <input type="hidden" name="tab" value="export" />

            <div class="col-12 col-lg-3">
                <label for="search_exp" class="form-label d-block">Cari ID Listing</label>
                <input type="text" name="search" id="search_exp"
                    value="{{ request('search') }}"
                    class="form-control form-control-sm"
                    placeholder="Cari ID Listing" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
            </div>

            <div class="col-6 col-lg-2">
                <label for="property_type_exp" class="form-label d-block">Tipe Property</label>
                <select name="property_type" id="property_type_exp" class="form-select form-select-sm">
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

            <div class="col-6 col-lg-2">
                <label for="province-export" class="form-label d-block">Pilih Provinsi</label>
                <select id="province-export" name="province" class="form-select form-select-sm">
                <option disabled {{ request('province') ? '' : 'selected' }}>Pilih Provinsi</option>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="city-export" class="form-label d-block">Pilih Kota/Kab</label>
                <select id="city-export" name="city" class="form-select form-select-sm" {{ request('province') ? '' : 'disabled' }}>
                <option disabled selected>Pilih Kota/Kab</option>
                </select>
            </div>

            <div class="col-6 col-lg-2">
                <label for="district-export" class="form-label d-block">Pilih Kecamatan</label>
                <select id="district-export" name="district" class="form-select form-select-sm" {{ request('city') ? '' : 'disabled' }}>
                <option disabled selected>Pilih Kecamatan</option>
                </select>
            </div>

            {{-- Kolom kecil untuk tombol reset, sejajar dengan input lain --}}
            <div class="col-6 col-lg-1">
                <label class="form-label d-block invisible">Reset</label>
                <button type="button" id="btn-export-clear" class="btn btn-outline-warning btn-sm w-100 reset-chip">
                <span class="me-1">↺</span>Reset
                </button>
            </div>
    </form>

    <style>
        /* jarak antar kontrol biar nggak dempet */
        #export-filter-form .form-label { margin-bottom: .35rem; }
        #export-filter-form .form-control,
        #export-filter-form .form-select { border-radius: .5rem; }

        /* tombol reset kecil, kotak, sejajar */
        .reset-chip{
          padding: .35rem .5rem;
          border-radius: .5rem;
          font-weight: 600;
          line-height: 1.1;
        }
      </style>
<script>
    // ==== Lokasi: Provinsi -> Kota/Kab -> Kecamatan (Export) ====
    (function(){
      const selProv = document.getElementById('province-export');
      const selCity = document.getElementById('city-export');
      const selDist = document.getElementById('district-export');

      // Aman kalau tab lain gak punya elemen-elemen ini
      if (!selProv || !selCity || !selDist) return;

      // Data source
      const DATA_URL = "{{ asset('data/indonesia.json') }}";

      // Peta hirarki
      const provinceMap = new Map();  // Provinsi => Set(Kota/Kab)
      const locationMap = new Map();  // Provinsi => Map(Kota/Kab => Set(Kecamatan))

      // Helper reset isi select
      function resetSelect(el, placeholder){
        el.innerHTML = `<option disabled selected>${placeholder}</option>`;
      }

      // Urutkan: "KOTA ..." didahulukan, lalu alfabetis
      const sortCity = (a,b) => {
        const A = a.toUpperCase().startsWith('KOTA');
        const B = b.toUpperCase().startsWith('KOTA');
        if (A && !B) return -1;
        if (!A && B) return 1;
        return a.localeCompare(b);
      };

      // Isi provinsi
      function populateProvinces(){
        resetSelect(selProv, 'Pilih Provinsi');
        const list = Array.from(provinceMap.keys()).sort();
        for (const p of list) selProv.insertAdjacentHTML('beforeend', `<option value="${p}">${p}</option>`);
      }

      // Isi kota untuk provinsi terpilih
      function populateCities(prov){
        resetSelect(selCity, 'Pilih Kota/Kab');
        if (!prov || !provinceMap.has(prov)) {
          selCity.disabled = true;
          resetSelect(selDist, 'Pilih Kecamatan'); selDist.disabled = true;
          return;
        }
        const cities = Array.from(provinceMap.get(prov)).sort(sortCity);
        for (const c of cities) selCity.insertAdjacentHTML('beforeend', `<option value="${c}">${c}</option>`);
        selCity.disabled = false;

        // setiap ganti provinsi, kecamatan harus reset & disabled
        resetSelect(selDist, 'Pilih Kecamatan'); selDist.disabled = true;
      }

      // Isi kecamatan untuk kota terpilih
      function populateDistricts(prov, city){
        resetSelect(selDist, 'Pilih Kecamatan');
        if (!prov || !city || !locationMap.has(prov) || !locationMap.get(prov).has(city)) {
          selDist.disabled = true; return;
        }
        const dists = Array.from(locationMap.get(prov).get(city)).sort();
        for (const d of dists) selDist.insertAdjacentHTML('beforeend', `<option value="${d}">${d}</option>`);
        selDist.disabled = false;
      }

      // Ambil data -> bangun peta -> render awal -> preselect dari request()
      fetch(DATA_URL).then(r => r.json()).then(data => {
        data.forEach(item => {
          const prov = (item.province || '').trim();
          const city = (item.regency  || '').trim();
          const dist = (item.district || '').trim();
          if (!prov || !city || !dist) return;

          if (!provinceMap.has(prov)) provinceMap.set(prov, new Set());
          provinceMap.get(prov).add(city);

          if (!locationMap.has(prov)) locationMap.set(prov, new Map());
          if (!locationMap.get(prov).has(city)) locationMap.get(prov).set(city, new Set());
          locationMap.get(prov).get(city).add(dist);
        });

        populateProvinces();

        // Preselect dari query (kalau ada)
        const rqProv = @json(request('province'));
        const rqCity = @json(request('city'));
        const rqDist = @json(request('district'));

        if (rqProv && provinceMap.has(rqProv)) {
          selProv.value = rqProv;
          populateCities(rqProv);
          if (rqCity && provinceMap.get(rqProv).has(rqCity)) {
            selCity.value = rqCity;
            populateDistricts(rqProv, rqCity);
            if (rqDist) selDist.value = rqDist;
          }
        } else {
          // default: city & district nonaktif
          selCity.disabled = true; selDist.disabled = true;
          resetSelect(selCity, 'Pilih Kota/Kab');
          resetSelect(selDist, 'Pilih Kecamatan');
        }
      }).catch(err => console.error('Gagal load data lokasi:', err));

      // Event chain + trigger filter AJAX (pakai debounced() milikmu)
      selProv.addEventListener('change', () => {
        populateCities(selProv.value);
        if (typeof debounced === 'function') debounced();
      });

      selCity.addEventListener('change', () => {
        populateDistricts(selProv.value, selCity.value);
        if (typeof debounced === 'function') debounced();
      });

      selDist.addEventListener('change', () => {
        if (typeof debounced === 'function') debounced();
      });

      // Pastikan tombol reset yang kamu pasang kemarin ikut mereset dropdown
      document.getElementById('btn-export-clear')?.addEventListener('click', () => {
        // kosongkan nilai
        if (selProv) selProv.selectedIndex = 0;
        populateCities(null);   // otomatis disable city & reset district
        if (typeof debounced === 'function') debounced();
      });
    })();
    </script>

          <div class="spinner-border" id="loading-spinner" style="display:none;" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>

          {{-- Tabel + Export --}}
          <form id="export-form" action="{{ route('dashboard.owner.export') }}" method="POST" class="d-flex flex-column gap-2">
            @csrf
            {{-- Hidden untuk kirim pilihan & filter saat export --}}
            <input type="hidden" name="selected_ids" id="selected_ids_input">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="property_type" value="{{ request('property_type') }}">
            <input type="hidden" name="province" value="{{ request('province') }}">
            <input type="hidden" name="city" value="{{ request('city') }}">
            <input type="hidden" name="district" value="{{ request('district') }}">
            <!-- Template Google Docs untuk letters() -->
            <input type="hidden" name="template_url" id="template_url_input" value="https://docs.google.com/document/d/1SB9EqTBU3DlhKwKTMtV6QpBS18ycZoLZ/edit?usp=sharing">

            <div class="d-flex gap-2 flex-wrap align-items-center">
              <button type="button" id="btn-export-csv" class="btn btn-success btn-sm" disabled title="Pilih minimal 2 item dulu">Export CSV</button>
              <button type="button" id="btn-export-letters" class="btn btn-dark btn-sm" disabled title="Pilih minimal 1 listing">Export LBH Jaksa</button>
              <input type="hidden" name="format" id="export_format" value="csv">
              <small class="text-muted">Pilih minimal 2 item untuk di export.</small>
            </div>

            {{-- HOST STABIL: spinner + container partial --}}
            <div id="export-list-wrap">
              <div id="export-loading" class="export-loading d-none">
                <div class="spinner-border" role="status" aria-label="Loading"></div>
              </div>
              <div id="export-list-inner">
                @include('partial.export_list')  <!-- Panggil Partial Disini -->
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-3">
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center gap-2">
            <h5 class="mb-0 fw-semibold text-primary">
              ✅ Dipilih (<span id="export-selected-counter">0</span>)
            </h5>
            <button type="button" id="export-clear-all" class="btn btn-link btn-sm text-danger px-0">
              Hapus semua
            </button>
          </div>
          <style>
            #export-clear-all { text-decoration: none; }
            #export-clear-all:hover { text-decoration: underline; }
          </style>

        <div class="card-body">
          <div id="selected-preview" class="d-flex flex-wrap gap-2 small"></div>
          <hr class="my-3">
          <div class="text-muted small">
            Centang item di halaman mana pun. Pilihan kamu disimpan sementara di browser sampai kamu klik Export.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    /* Spinner overlay minimalis (sudah ada) */
    .btn.btn-loading { pointer-events: none; opacity: .9; }
    .btn.btn-loading .spinner-border { margin-right: .4rem; }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function () {
      const form    = document.getElementById('export-filter-form');
      const input   = document.getElementById('search_exp');
      const selType = document.getElementById('property_type_exp');
      const selProv = document.getElementById('province-export');
      const selCity = document.getElementById('city-export');
      const selDist = document.getElementById('district-export');
      const btnClear = document.getElementById('btn-export-clear');

      const listWrap = document.getElementById('export-list-inner');
      const fragmentRoute = "{{ route('dashboard.owner.export.list') }}";

      function getOverlay(){ return document.getElementById('export-loading'); }
      function showLoading(on){ const el = getOverlay(); if (el) el.classList.toggle('d-none', !on); }

      let t, lastReqId = 0;

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
      function qs(obj){ return new URLSearchParams(obj).toString(); }

      async function loadList(extra = {}) {
        const myId = ++lastReqId;
        showLoading(true);
        try {
          const url = fragmentRoute + '?' + qs(paramsObj(extra));
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          const html = await res.text();
          if (myId !== lastReqId) return;
          listWrap.innerHTML = html;
          if (window.initExportSelection) window.initExportSelection();

          // === SCROLL KE ATAS TABEL ===
          // anchor scroll: wrapper host yang stabil
          const wrap = document.getElementById('export-list-wrap');
          if (wrap) {
            const headerHeight =
              (document.querySelector('.navbar.fixed-top')?.offsetHeight) ||
              (document.querySelector('.sticky-top')?.offsetHeight) || 0;
            const top = wrap.getBoundingClientRect().top + window.pageYOffset - headerHeight - 8;
            window.scrollTo({ top, behavior: 'smooth' });

            // aksesibilitas (opsional, aman)
            wrap.setAttribute('tabindex','-1');
            wrap.focus({ preventScroll: true });
          }

          // opsional: sinkronkan ?page di URL (aman)
          try {
            const u = new URL(window.location.href);
            if (extra.page) u.searchParams.set('page', String(extra.page)); else u.searchParams.delete('page');
            history.replaceState(null, '', u.toString());
          } catch(_) {}

          // panggil rehydration kalau tersedia (selain initExportSelection)
          if (typeof window.afterExportListReplaced === 'function') {
            window.afterExportListReplaced();
          }
          // === END SCROLL ===

        } catch (e) {
          if (e && e.name !== 'AbortError') console.error('loadList error:', e);
        } finally {
          if (myId === lastReqId) showLoading(false);
        }
      }
      window.__loadExportList = loadList;

      function debounced(){ clearTimeout(t); t = setTimeout(() => loadList(), 220); }

      // Filter -> AJAX dengan sanitasi numeric
      input?.addEventListener('input', function(){
        const cleaned = this.value.replace(/[^\d]/g, '');
        if (this.value !== cleaned) this.value = cleaned;
        debounced();
      });
      [selType, selProv, selCity, selDist].forEach(el => el && el.addEventListener('change', debounced));
      form?.addEventListener('submit', function(e){ e.preventDefault(); loadList(); });

      // Pagination
      listWrap?.addEventListener('click', function(e){
        const a = e.target.closest('a.js-export-page');
        if (!a) return;
        e.preventDefault();
        const page = a.dataset.page || '1';
        loadList({ page });
      });

      // Reset filter
      btnClear?.addEventListener('click', function () {
        if (input)   input.value = '';
        if (selType) selType.value = '';
        if (selProv) selProv.value = '';
        if (selCity) {
          selCity.innerHTML = '<option disabled selected>Pilih Kota/Kab</option>';
          selCity.value = '';
          selCity.setAttribute('disabled','disabled');
        }
        if (selDist) {
          selDist.innerHTML = '<option disabled selected>Pilih Kecamatan</option>';
          selDist.value = '';
          selDist.setAttribute('disabled','disabled');
        }
        loadList({ search: '', property_type: '', province: '', city: '', district: '', page: 1 });
      });
    });
    </script>

<script>
    (function(){
      const KEY = 'exportSelectedIds';

      const container = document.getElementById('export-list-inner');
      if (!container) return;

      const getSelected = () => new Set(JSON.parse(localStorage.getItem(KEY) || '[]'));
      const saveSelected = (set) => localStorage.setItem(KEY, JSON.stringify(Array.from(set)));

      const headerCounter = document.querySelectorAll('#export-selected-counter');
      const previewEl = document.getElementById('selected-preview');
      const exportForm = document.getElementById('export-form');
      const selectedInput = document.getElementById('selected_ids_input');

      const btnCSV  = document.getElementById('btn-export-csv');
      const btnLET  = document.getElementById('btn-export-letters');
      const formatInput = document.getElementById('export_format');
      const lettersAction = "{{ route('dashboard.owner.export.docx') }}";
      const templateUrlInput = document.getElementById('template_url_input');

      const MARK_URL = "{{ route('dashboard.owner.export.mark') }}";

      const qRows   = () => Array.from(container.querySelectorAll('.row-check'));
      const qById   = (id) => container.querySelector(`#${id}`);
      const qValue  = (val) => Array.from(container.querySelectorAll(`.row-check[value="${val}"]`));

      function updateCounters(){
        const size = getSelected().size;
        headerCounter.forEach(el => el.textContent = `${size} dipilih`);
      }

      function renderPreview(){
        const sel = getSelected();
        if (!previewEl) return;
        if (sel.size === 0) {
          previewEl.innerHTML = '<span class="text-muted">Belum ada yang dipilih.</span>';
          return;
        }
        previewEl.innerHTML = '';
        sel.forEach(id => {
          const pill = document.createElement('button');
          pill.type = 'button';
          pill.className = 'btn btn-sm btn-outline-primary';
          pill.textContent = '#'+id;
          pill.title = 'Klik untuk hapus';
          pill.addEventListener('click', () => {
            const s = getSelected(); s.delete(id); saveSelected(s);
            qValue(id).forEach(cb => cb.checked = false);
            syncMaster();
            updateButtons();
            updateCounters();
            renderPreview();
          });
          previewEl.appendChild(pill);
        });
      }

      function updateButtons(){
        const size = getSelected().size;
        const enough2 = size >= 2;
        const enough1 = size >= 1;
        if (btnCSV) { btnCSV.disabled = !enough2; btnCSV.title = enough2 ? '' : 'Pilih minimal 2 item dulu'; }
        if (btnLET) { btnLET.disabled = !enough1; btnLET.title = enough1 ? '' : 'Pilih minimal 1 listing'; }
      }

      function syncMaster(){
        const master = qById('check_all_export');
        if (!master) return;
        const rows = qRows();
        master.checked = rows.length > 0 && rows.every(x => x.checked);
        master.indeterminate = !master.checked && rows.some(x => x.checked);
      }

      function hydratePage(){
        const sel = getSelected();
        qRows().forEach(cb => cb.checked = sel.has(cb.value));
        syncMaster();
      }

      // ==== TAMBAHAN: helper untuk auto-scroll ke tombol Export ====
      function scrollToExportAnchor(extraPadding = 12){
        const anchor = document.getElementById('export-form') || document.getElementById('export-list-wrap');
        if (!anchor) return;
        const headerHeight =
          (document.querySelector('.navbar.fixed-top')?.offsetHeight) ||
          (document.querySelector('.sticky-top')?.offsetHeight) || 0;
        const top = anchor.getBoundingClientRect().top + window.pageYOffset - headerHeight - extraPadding;
        window.scrollTo({ top, behavior: 'smooth' });
        // aksesibilitas kecil agar fokus ke area form
        anchor.setAttribute('tabindex','-1');
        anchor.focus({ preventScroll: true });
      }
      // ==== END TAMBAHAN ====

      // Util spinner tombol
      function setLoading(btn, text){
        if (!btn) return;
        if (!btn.dataset.orig) btn.dataset.orig = btn.innerHTML;
        btn.classList.add('btn-loading');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>' + ' ' + (text || 'Memproses…');
      }
      function clearLoading(btn){
        if (!btn) return;
        btn.classList.remove('btn-loading');
        btn.disabled = false;
        if (btn.dataset.orig) btn.innerHTML = btn.dataset.orig;
      }

      async function markExportedThenSubmit(targetAction, formatValue, { openInNewTab = false, loadingBtn = null, autoResetMs = 0 } = {}){
        const sel = Array.from(getSelected());
        if (!exportForm) return;

        // CSRF token
        const token =
          document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
          document.querySelector('input[name="_token"]')?.value || '';

        // Mark exported (non-blocking)
        try {
          await fetch(MARK_URL, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ selected_ids: sel })
          });
        } catch (err) {
          console.warn('mark exported failed (ignored):', err);
        }

        if (selectedInput) selectedInput.value = sel.join(',');
        if (formatInput && typeof formatValue === 'string') formatInput.value = formatValue;

        const prevAction = exportForm.getAttribute('action');
        const prevTarget = exportForm.getAttribute('target');

        exportForm.setAttribute('action', targetAction);
        if (openInNewTab) exportForm.setAttribute('target', '_blank');

        exportForm.requestSubmit();

        // Auto reset spinner setelah jeda (UI feedback)
        if (loadingBtn && autoResetMs > 0) {
          setTimeout(() => clearLoading(loadingBtn), autoResetMs);
        }

        setTimeout(() => {
          exportForm.setAttribute('action', prevAction || "{{ route('dashboard.owner.export') }}");
          if (openInNewTab) {
            if (prevTarget) exportForm.setAttribute('target', prevTarget);
            else exportForm.removeAttribute('target');
          }
        }, 0);
      }

      const clearAllBtn = document.getElementById('export-clear-all');
function clearAll(){
  // 1) kosongkan pilihan per-ID
  saveSelected(new Set());
  qRows().forEach(cb => cb.checked = false);

  // 2) matikan master checkbox halaman ini
  const master = qById('check_all_export');
  if (master){ master.checked = false; master.indeterminate = false; }

  // 3) ⬅️ NEW: matikan toggle "Pilih semua (semua halaman)"
  const SELECT_ALL_KEY = 'exportSelectAllAcross';
  localStorage.setItem(SELECT_ALL_KEY, '0');                                    // matikan flag global
  const toggleAcross = document.querySelector('#export-list-inner #select-all-across');
  if (toggleAcross) toggleAcross.checked = false;                               // sinkron UI toggle
  const selectAllInput = document.getElementById('select_all_input');
  if (selectAllInput) selectAllInput.value = '0';                               // sinkron hidden input (aman)

  // 4) refresh UI tombol & counter
  updateButtons();
  updateCounters();
  renderPreview();
}
clearAllBtn?.addEventListener('click', clearAll);


      // Delegasi checkbox baris
      container.addEventListener('change', function(e){
        if (!e.target.matches('.row-check')) return;
        const sel = getSelected();
        if (e.target.checked) sel.add(e.target.value); else sel.delete(e.target.value);
        saveSelected(sel);
        syncMaster();
        updateButtons();
        updateCounters();
        renderPreview();
      });

      // Master checkbox
      container.addEventListener('change', function(e){
        if (!e.target.matches('#check_all_export')) return;
        const rows = qRows();
        const sel = getSelected();
        rows.forEach(cb => {
          cb.checked = e.target.checked;
          if (cb.checked) sel.add(cb.value); else sel.delete(cb.value);
        });
        saveSelected(sel);
        syncMaster();
        updateButtons();
        updateCounters();
        renderPreview();
      });

      // Submit guard
      if (exportForm) {
        exportForm.addEventListener('submit', function(){
          if (selectedInput) selectedInput.value = Array.from(getSelected()).join(',');
        });

        // Export CSV (download di tab yang sama) → spinner 5 detik
        if (btnCSV)  btnCSV.addEventListener('click',  () => {
          if (btnCSV.disabled) return;
          setLoading(btnCSV, 'Memproses…');
          markExportedThenSubmit("{{ route('dashboard.owner.export') }}", 'csv', { loadingBtn: btnCSV, autoResetMs: 5000 });
        });

        // Export LBH Jaksa (tab baru) → spinner 1.5 detik
        if (btnLET) btnLET.addEventListener('click', () => {
          if (btnLET.disabled) return;
          if (!templateUrlInput || !templateUrlInput.value) {
            alert('Template URL belum diisi.');
            return;
          }
          setLoading(btnLET, 'Menyiapkan…');
          markExportedThenSubmit(lettersAction, undefined, { openInNewTab: true, loadingBtn: btnLET, autoResetMs: 1500 });
        });
      }

      window.afterExportListReplaced = function(){
        hydratePage();
        updateButtons();
        updateCounters();
        renderPreview();
        // ==== AUTO-SCROLL dengan padding lebih besar ====
        scrollToExportAnchor(96); // naikkan ke 128 kalau perlu
      };

      hydratePage();
      updateButtons();
      updateCounters();
      renderPreview();
    })();
    </script>

{{-- ========== Transaksi ========== --}}
<div class="tab-pane fade {{ $tab==='transaksi' ? 'show active' : '' }}" id="transaksi" role="tabpanel" aria-labelledby="transaksi-tab">
    <div class="row">
      {{-- 3/4 kiri: tabel transaksi (mirror Stoker) --}}
      <div class="col-lg-9">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-primary">💳 Daftar Transaksi</h5>
          </div>

          <div class="card-body">
            {{-- =================== FILTER BAR (TRANSAKSI) =================== --}}
            <div class="row g-3 p-3 rounded shadow-sm bg-white mb-3 align-items-end transaksi-filter-grid">
              {{-- Form FILTER (GET) tembus ke grid --}}
              <form id="transaksi-filter-form"
                    method="GET"
                    action="{{ route('dashboard.owner') }}"
                    class="d-contents">
                <input type="hidden" name="tab" value="transaksi" />

                {{-- Cari ID Listing (numeric) --}}
                <div class="col-6 col-lg-1 pe-lg-2">
                  <label for="transaksi_search" class="form-label mb-1">Cari ID</label>
                  <input type="text" name="search" id="transaksi_search" value="{{ request('search') }}"
                         class="form-control form-control-sm" placeholder="ID Listing"
                         inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                </div>

                {{-- Cari Vendor (text) --}}
                <div class="col-12 col-lg-3 pe-lg-2">
                  <label for="transaksi_vendor" class="form-label mb-1">Cari Vendor</label>
                  <input type="text" name="vendor" id="transaksi_vendor" value="{{ request('vendor') }}"
                         class="form-control form-control-sm" placeholder="Contoh : BRI Rajawali" autocomplete="off">
                </div>

                {{-- Tipe properti --}}
                <div class="col-6 col-lg-1 pe-lg-2">
                  <label for="transaksi_property_type" class="form-label mb-1">Tipe</label>
                  <select name="property_type" id="transaksi_property_type" class="form-select form-select-sm">
                    <option value="" {{ request('property_type') ? '' : 'selected' }} disabled>Tipe Property</option>
                    <option value="rumah" @selected(request('property_type')==='rumah')>Rumah</option>
                    <option value="gudang" @selected(request('property_type')==='gudang')>Gudang</option>
                    <option value="apartemen" @selected(request('property_type')==='apartemen')>Apartemen</option>
                    <option value="tanah" @selected(request('property_type')==='tanah')>Tanah</option>
                    <option value="pabrik" @selected(request('property_type')==='pabrik')>Pabrik</option>
                    <option value="hotel dan villa" @selected(request('property_type')==='hotel dan villa')>Hotel dan Villa</option>
                    <option value="ruko" @selected(request('property_type')==='ruko')>Ruko</option>
                    <option value="toko" @selected(request('property_type')==='toko')>Toko</option>
                    <option value="inventaris" @selected(request('property_type')==='inventaris')>Inventaris</option>
                    <option value="lain-lain" @selected(request('property_type')==='lain-lain')>Lainnya</option>
                  </select>
                </div>

                {{-- Provinsi --}}
                <div class="col-6 col-lg-2 pe-lg-2">
                  <label for="transaksi_province" class="form-label mb-1">Pilih Provinsi</label>
                  <select id="transaksi_province" name="province" class="form-select form-select-sm">
                    <option disabled {{ request('province') ? '' : 'selected' }}>Pilih Provinsi</option>
                  </select>
                </div>

                {{-- Kota/Kab --}}
                <div class="col-6 col-lg-2 pe-lg-2">
                  <label for="transaksi_city" class="form-label mb-1">Pilih Kota/Kab</label>
                  <select id="transaksi_city" name="city" class="form-select form-select-sm" {{ request('province') ? '' : 'disabled' }}>
                    <option disabled selected>Pilih Kota/Kab</option>
                  </select>
                </div>

                {{-- Kecamatan --}}
                <div class="col-6 col-lg-2 pe-lg-2">
                  <label for="transaksi_district" class="form-label mb-1">Pilih Kecamatan</label>
                  <select id="transaksi_district" name="district" class="form-select form-select-sm" {{ request('city') ? '' : 'disabled' }}>
                    <option disabled selected>Pilih Kecamatan</option>
                  </select>
                </div>
              </form>

              {{-- Tombol Reset --}}
              <div class="col-6 col-lg-1">
                <label class="form-label d-block invisible">Reset</label>
                <button type="button" id="btn-transaksi-clear" class="btn reset-chip w-100">
                  <span class="me-1">↺</span>Reset
                </button>
              </div>
            </div>

            {{-- HOST STABIL untuk partial + spinner (mirror Stoker) --}}
            <div id="transaksi-list-wrap">
              <div id="transaksi-loading" class="export-loading d-none">
                <div class="spinner-border" role="status" aria-label="Loading"></div>
              </div>
              <div id="transaksi-fragment-host">@include('partial.transaksi_list')</div>
            </div>

          </div>
        </div>
      </div>

{{-- 1/4 kanan: riwayat transaksi --}}
<div class="col-lg-3">
    <div class="card trx-history-card shadow-sm border-0 mb-4">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <span class="trx-status-dot"></span>
          <div>
            <h6 class="mb-0 fw-semibold text-danger">Riwayat Transaksi</h6>
            <small class="text-muted">Update terakhir aktivitas closing</small>
          </div>
        </div>
        <span class="badge bg-light text-muted border fw-normal small">
          {{ $transaksiHistory->count() }} aktivitas
        </span>
      </div>

      <div class="card-body p-0">
        @if($transaksiHistory->isEmpty())
          <div class="p-4 text-center text-muted small">
            <div class="empty-icon mb-2">📄</div>
            Belum ada riwayat transaksi.
          </div>
        @else
          <div class="trx-history-list">
            @foreach($transaksiHistory as $t)
              @php
                $status      = $t->status ?? 'Closing';
                $statusLower = strtolower($status);

                $badgeClass = 'status-pill-muted';
                if ($statusLower === 'closing') {
                    $badgeClass = 'status-pill-primary';
                } elseif (in_array($statusLower, ['kuitansi','kode billing'])) {
                    $badgeClass = 'status-pill-info';
                } elseif (in_array($statusLower, ['kutipan risalah lelang','akte grosse'])) {
                    $badgeClass = 'status-pill-warning';
                } elseif (in_array($statusLower, ['balik nama','eksekusi pengosongan'])) {
                    $badgeClass = 'status-pill-success';
                } elseif ($statusLower === 'selesai') {
                    $badgeClass = 'status-pill-finish';
                }

                $fotoArray = array_values(array_filter(
                    array_map('trim', explode(',', (string)($t->gambar ?? '')))
                ));
                $fotoUtama = $fotoArray[0] ?? '';
                if ($fotoUtama !== '' && preg_match('~^https?://~i', $fotoUtama)) {
                    $thumbSrc = $fotoUtama;
                } elseif ($fotoUtama !== '') {
                    $thumbSrc = asset(ltrim($fotoUtama, '/'));
                } else {
                    $thumbSrc = asset('img/placeholder.jpg');
                }
              @endphp

              <div class="trx-history-item">
                <div class="trx-history-left">
                  <div class="trx-history-thumb">
                    <img src="{{ $thumbSrc }}"
                         alt="Property {{ $t->id_listing }}"
                         loading="lazy">
                  </div>

                  <div class="trx-history-main">
                    {{-- Baris: ID transaksi – nama agent --}}
                    <div class="d-flex align-items-center gap-1 mb-1">
                      <span class="trx-history-id">
                        {{ $t->id_transaction ?? 'TR—' }}
                      </span>
                      @if(!empty($t->agent_nama))
                        <span class="text-muted">•</span>
                        <span class="trx-history-agent text-truncate">
                          {{ $t->agent_nama }}
                        </span>
                      @endif
                    </div>

                    {{-- Baris: tanggal • #id listing (tanpa tulisan "ID Listing") --}}
                    <div class="trx-history-meta mb-1">
                      <span>{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d M Y') }}</span>
                      <span class="mx-1">•</span>
                      <span>#{{ $t->id_listing }}</span>
                    </div>

                    {{-- Harga bidding (kalau ada), fallback harga_limit --}}
                    <div class="trx-history-amount mb-1">
                      Rp {{ number_format($t->harga_bidding ?? $t->harga_limit ?? 0, 0, ',', '.') }}
                    </div>

                    {{-- Lokasi singkat --}}
                    <div class="trx-history-location text-truncate">
                      {{ $t->lokasi }}
                    </div>
                  </div>
                </div>

                <div class="trx-history-right">
                  <span class="trx-status-pill {{ $badgeClass }}">
                    {{ $t->status }}
                  </span>

                  @php
                    // aman kalau $t stdClass atau model
                    $komisiPersen = '';
                    if (isset($t->persentase_komisi) && $t->persentase_komisi !== null) {
                        // di DB 0.050000 → 5
                        $komisiPersen = (float) $t->persentase_komisi * 100;
                    }

                    // fallback harga_limit:
                    // 1) pakai transaksi.harga_limit kalau ada
                    // 2) kalau kosong, pakai property.harga / 1.278 (markup → limit)
                    $hargaLimitBtn = 0;
                    if (isset($t->harga_limit) && $t->harga_limit > 0) {
                        $hargaLimitBtn = (int) $t->harga_limit;
                    } elseif (isset($t->harga) && $t->harga > 0) {
                        $hargaLimitBtn = (int) round($t->harga / 1.278);
                    }
                @endphp

<button type="button"
        class="btn btn-sm btn-outline-danger rounded-pill mt-2 trx-history-edit"
        data-id-listing="{{ $t->id_listing }}"
        data-id-transaksi="{{ $t->id_transaction ?? '' }}"
        data-status="{{ $t->status ?? '' }}"
        data-lokasi="{{ $t->lokasi }}"
        data-tipe="{{ $t->tipe ?? '' }}"
        data-harga-limit="{{ $hargaLimitBtn }}"
        data-harga-menang="{{ $t->harga_bidding ?? 0 }}"
        data-harga-deal="{{ $t->harga_deal ?? 0 }}"
        data-cobroke-fee="{{ $t->cobroke_fee ?? 0 }}"
        data-royalty-fee="{{ $t->royalty_fee ?? 0 }}"
        data-closing-type="{{ ($t->skema_komisi ?? '') === 'Selisih harga' ? 'price_gap' : 'profit' }}"
        data-komisi-persen="{{ $komisiPersen }}"
        data-biaya-balik-nama="{{ $t->biaya_baliknama ?? '' }}"
        data-biaya-eksekusi="{{ $t->biaya_pengosongan ?? '' }}"
        data-tanggal="{{ \Carbon\Carbon::parse($t->tanggal_transaksi ?? $t->tanggal_diupdate)->format('Y-m-d') }}"
        data-id-agent="{{ $t->id_agent ?? '' }}"
        data-agent-nama="{{ $t->agent_nama ?? '' }}"
        data-id-klien="{{ $t->id_klien ?? '' }}"
        data-gambar="{{ $t->gambar ?? '' }}"
        data-photo="{{ $thumbSrc }}"
        data-copic-name="{{ $t->agent_nama ?? '' }}">
        Edit
</button>


                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

  <script>
    (function(){
      // Format angka → "1.500.000"
      function formatRupiahFromNumber(value){
        if (value === null || value === undefined || value === '') return '';
        const n = Number(String(value).replace(/[^\d\-]/g,''));
        if (isNaN(n)) return '';
        return n.toLocaleString('id-ID');
      }

      // Isi semua field di modal dari data-* tombol Edit
      function prefillClosingFormFromDataset(btn){

        const ds = btn.dataset;

        const inputIdListing    = document.getElementById('tc-id-listing');
        const inputIdTrans      = document.getElementById('tc-id-transaksi');
        const inputTgl          = document.getElementById('tc-tanggal');
        const selectStatus      = document.getElementById('tc-status');
        const inputClosingType  = document.getElementById('tc-closing-type');

        const hargaMenangInput  = document.getElementById('tc-harga-menang');
        const hargaDealInput    = document.getElementById('tc-harga-deal');
        const cobrokeFeeInput   = document.getElementById('tc-cobroke-fee');
        const royaltyFeeInput   = document.getElementById('tc-royalty-fee');

        const hargaLimitEl      = document.getElementById('tc-harga-limit');
        const komisiInput       = document.getElementById('tc-komisi-persen');
        const biayaBNInput      = document.getElementById('tc-biaya-balik-nama');
        const biayaEksInput     = document.getElementById('tc-biaya-eksekusi');

        const selisihInputEl    = document.getElementById('tc-selisih');
        const selisihSummaryEl  = document.getElementById('tc-selisih-summary');

        const hiddenAgentInput  = document.getElementById('tc-agent');
        const agentLabelEl      = document.getElementById('tc-agent-label');
        const agentAvatarEl     = document.getElementById('tc-agent-avatar-btn');

        const hiddenClientInput = document.getElementById('tc-client');

        // --- hidden: id listing & id transaksi ---
        if (inputIdListing) inputIdListing.value = ds.idListing || '';
        if (inputIdTrans)   inputIdTrans.value   = ds.idTransaksi || '';

        // --- tanggal closing ---
        if (inputTgl && ds.tanggal) {
          inputTgl.value = ds.tanggal;
        }

        // --- status transaksi ---
        if (selectStatus && ds.status){
          const lower = ds.status.toLowerCase();
          const opt = Array.from(selectStatus.options)
            .find(o => o.value.toLowerCase() === lower);
          selectStatus.value = opt ? opt.value : 'Closing';
        }

        // --- Harga Limit (label kiri) ---
        if (hargaLimitEl) {
          const limitText = formatRupiahFromNumber(ds.hargaLimit || '');
          hargaLimitEl.textContent = 'Rp ' + (limitText || '0');
        }

        // ===== PENTING: set field-field NEW dulu, baru trigger kalkulasi =====
        // --- Harga Deal (NEW) ---
        if (hargaDealInput && ds.hargaDeal !== undefined && ds.hargaDeal !== '') {
          hargaDealInput.value = formatRupiahFromNumber(ds.hargaDeal);
        }

        // --- Cobroke Fee (NEW) ---
        if (cobrokeFeeInput && ds.cobrokeFee !== undefined && ds.cobrokeFee !== '') {
          cobrokeFeeInput.value = formatRupiahFromNumber(ds.cobrokeFee);
        }

        // --- Royalty Fee (NEW) ---
        if (royaltyFeeInput && ds.royaltyFee !== undefined && ds.royaltyFee !== '') {
          royaltyFeeInput.value = formatRupiahFromNumber(ds.royaltyFee);
        }

        // --- skema komisi (persentase / selisih) ---
        const closingType = ds.closingType || 'profit';
        if (inputClosingType) inputClosingType.value = closingType;

        const schemeOpt = document.querySelector(
          '.tc-scheme-option[data-value="'+ closingType +'"]'
        );
        if (schemeOpt) schemeOpt.click();

        // --- Harga Menang ---
        if (hargaMenangInput && ds.hargaMenang !== undefined && ds.hargaMenang !== '') {
          hargaMenangInput.value = formatRupiahFromNumber(ds.hargaMenang);
          hargaMenangInput.dispatchEvent(new Event('input', { bubbles:true }));
        }

        // --- Komisi (%) kalau mode profit ---
        if (closingType === 'profit'
            && komisiInput
            && ds.komisiPersen !== undefined
            && ds.komisiPersen !== '') {

          komisiInput.value = ds.komisiPersen;
          komisiInput.dispatchEvent(new Event('input',  { bubbles:true }));
          komisiInput.dispatchEvent(new Event('change', { bubbles:true }));
        }

        // --- Biaya Balik Nama ---
        if (biayaBNInput && ds.biayaBalikNama !== undefined && ds.biayaBalikNama !== '') {
          biayaBNInput.value = formatRupiahFromNumber(ds.biayaBalikNama);
          biayaBNInput.dispatchEvent(new Event('input', { bubbles:true }));
        }

        // --- Biaya Eksekusi ---
        if (biayaEksInput && ds.biayaEksekusi !== undefined && ds.biayaEksekusi !== '') {
          biayaEksInput.value = formatRupiahFromNumber(ds.biayaEksekusi);
          biayaEksInput.dispatchEvent(new Event('input', { bubbles:true }));
        }

        // --- Pilih Agent (klik option dropdown) ---
        if (ds.idAgent) {
          const agBtn = document.querySelector(
            '.tc-agent-option[data-id="'+ ds.idAgent +'"]'
          );
          if (agBtn) {
            agBtn.click();
          } else {
            if (hiddenAgentInput) hiddenAgentInput.value = ds.idAgent;
            if (agentLabelEl && ds.agentNama) agentLabelEl.textContent = ds.agentNama;
            if (agentAvatarEl && ds.agentNama) {
              agentAvatarEl.textContent = ds.agentNama.trim().charAt(0).toUpperCase() || '?';
            }
          }
        }

        // --- Pilih Client ---
        if (ds.idKlien && hiddenClientInput) {
          hiddenClientInput.value = ds.idKlien;
        }

        // Trigger input/change supaya listener “auto ribuan + updateAllCalc” jalan
        if (hargaDealInput) {
          hargaDealInput.dispatchEvent(new Event('input', { bubbles:true }));
          hargaDealInput.dispatchEvent(new Event('change', { bubbles:true }));
        }
        if (cobrokeFeeInput) {
          cobrokeFeeInput.dispatchEvent(new Event('input', { bubbles:true }));
          cobrokeFeeInput.dispatchEvent(new Event('change', { bubbles:true }));
        }
        if (royaltyFeeInput) {
          royaltyFeeInput.dispatchEvent(new Event('input', { bubbles:true }));
          royaltyFeeInput.dispatchEvent(new Event('change', { bubbles:true }));
        }

        // Sync selisih summary
        if (selisihSummaryEl) {
          const v = selisihInputEl ? (selisihInputEl.value || '') : '';
          selisihSummaryEl.textContent = 'Rp ' + (v || '0');
        }
      }

      // EVENT DELEGATION: aman walau tombol muncul setelah AJAX
      document.addEventListener('click', function(e){
        const btn = e.target.closest('.trx-history-edit');
        if (!btn) return;

        const ds = btn.dataset;

        // ===== DEBUG (ini yang kamu butuh untuk diagnosa) =====
        console.group('DEBUG EDIT CLICK');
        console.log('ATTR data-harga-deal   :', btn.getAttribute('data-harga-deal'));
        console.log('ATTR data-cobroke-fee  :', btn.getAttribute('data-cobroke-fee'));
        console.log('ATTR data-royalty-fee  :', btn.getAttribute('data-royalty-fee'));
        console.table({
          ds_hargaDeal: ds.hargaDeal,
          ds_cobrokeFee: ds.cobrokeFee,
          ds_royaltyFee: ds.royaltyFee,
          ds_hargaMenang: ds.hargaMenang,
          ds_idTransaksi: ds.idTransaksi
        });
        console.groupEnd();

        // Payload dasar untuk buka modal
        const payload = {
          id_listing   : ds.idListing,
          id_transaksi : ds.idTransaksi || null,
          status       : ds.status || null,
          lokasi       : ds.lokasi || '',
          tipe         : ds.tipe || '',
          harga_deal   : ds.hargaDeal,
          cobroke_fee  : ds.cobrokeFee,
          royalty_fee  : ds.royaltyFee,
          harga_limit  : Number(ds.hargaLimit || 0),
          gambar       : (ds.gambar || '').trim(),
          photo        : (ds.photo  || '').trim(),
          copic_name   : (ds.copicName || '').trim()
        };

        if (window.handleTransaksiClosingClick) {
          try { window.handleTransaksiClosingClick(payload, btn); }
          catch(err){ console.error(err); }
        }

        // Prefill setelah modal “beneran” kebuka/dirender
        requestAnimationFrame(function(){
          prefillClosingFormFromDataset(btn);

          // Debug hasil akhir input
          console.group('DEBUG AFTER PREFILL');
          console.table({
            input_harga_deal: document.getElementById('tc-harga-deal')?.value,
            input_cobroke_fee: document.getElementById('tc-cobroke-fee')?.value,
            input_royalty_fee: document.getElementById('tc-royalty-fee')?.value
          });
          console.groupEnd();
        });
      });
    })();
    </script>


  <style>
  .trx-history-card{
    border-radius: 1.25rem;
    overflow: hidden;
  }

  .trx-history-card .card-header{
    border-bottom: 1px solid rgba(148,163,184,.25);
  }

  .trx-status-dot{
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: linear-gradient(135deg,#22c55e,#3b82f6);
    box-shadow: 0 0 0 3px rgba(59,130,246,.25);
    display:inline-block;
  }

  .trx-history-list{
    max-height: 420px;
    overflow-y: auto;
    padding: 0.75rem 0.75rem 0.9rem;
  }
  .trx-history-list::-webkit-scrollbar{
    width: 5px;
  }
  .trx-history-list::-webkit-scrollbar-thumb{
    background: rgba(148,163,184,.6);
    border-radius: 999px;
  }

  /* CARD TRANSAKSI */
  .trx-history-item{
    display: flex;
    justify-content: space-between;
    align-items: stretch;
    gap: .65rem;
    padding: .75rem .9rem;
    margin-bottom: .55rem;
    border-radius: 1rem;
    border: 1px solid rgba(148,163,184,.35);
    background: linear-gradient(120deg,#ecfdf3,#ffffff); /* hijau muda soft */
    box-shadow: 0 6px 16px rgba(15,23,42,.03);
    position: relative;
    transition: all .18s ease-out;
  }
  .trx-history-item:hover{
    box-shadow: 0 10px 24px rgba(15,23,42,.08);
    transform: translateY(-1px);
    border-color: rgba(22,163,74,.45);
  }

  /* LEFT SIDE */
  .trx-history-left{
    display:flex;
    gap:.75rem;
    min-width:0;
  }

  /* THUMB: kotak 1:1 */
  .trx-history-thumb{
    width:64px;
    height:64px;
    border-radius: 16px;
    overflow:hidden;
    flex-shrink:0;
    background:#e5e7eb;
  }
  .trx-history-thumb img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
  }

  .trx-history-main{
    min-width:0;
  }

  .trx-history-id{
    font-size: .78rem;
    font-weight: 600;
    color:#111827;
    letter-spacing:.06em;
  }

  .trx-history-agent{
    font-size: .78rem;
    max-width: 130px;
    color:#111827;
    font-weight:500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .trx-history-meta{
    font-size:.74rem;
    color:#6b7280;
  }

  .trx-history-amount{
    font-size:.95rem;
    font-weight:700;
    color:#0f172a;
  }

  .trx-history-location{
    font-size:.72rem;
    color:#6b7280;
    text-transform: uppercase;
    letter-spacing:.02em;
  }

  /* RIGHT SIDE */
  .trx-history-right{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    justify-content:space-between;
    gap:.25rem;
  }

  .trx-status-pill{
    padding: .18rem .7rem;
    border-radius: 999px;
    font-size: .7rem;
    font-weight: 600;
    border: 1px solid transparent;
    text-transform: capitalize;
  }
  .status-pill-primary{
    background: rgba(248,113,113,.09);
    border-color: rgba(239,68,68,.7);
    color:#b91c1c;
  }
  .status-pill-info{
    background: rgba(56,189,248,.1);
    border-color: rgba(56,189,248,.7);
    color:#0369a1;
  }
  .status-pill-warning{
    background: rgba(234,179,8,.08);
    border-color: rgba(234,179,8,.7);
    color:#92400e;
  }
  .status-pill-success{
    background: rgba(34,197,94,.08);
    border-color: rgba(34,197,94,.7);
    color:#166534;
  }
  .status-pill-finish{
    background: rgba(22,163,74,.1);
    border-color: rgba(22,163,74,.75);
    color:#14532d;
  }
  .status-pill-muted{
    background: rgba(148,163,184,.12);
    border-color: rgba(148,163,184,.7);
    color:#4b5563;
  }

  .trx-history-edit{
    padding-inline: 1.1rem;
    font-size:.75rem;
  }

  /* mobile */
  @media (max-width: 991.98px){
    .trx-history-card{
      margin-top:.75rem;
    }
  }
  </style>



    </div>
  </div>

  {{-- ==== STYLE KHUSUS TRANSAKSI FILTER (mirror Stoker) ==== --}}
  <style>
    .transaksi-filter-grid > [class*="col-lg-"] { min-width: 0; }
    .transaksi-filter-grid .form-label{ font-weight:600; color:#6b7280; }
    .transaksi-filter-grid .form-control,
    .transaksi-filter-grid .form-select{
      border-radius:.625rem;
    }
    #transaksi-list-wrap{ position:relative; min-height:120px; }
    #transaksi-list-wrap .export-loading{
      position:absolute; inset:0;
      display:flex; align-items:center; justify-content:center;
      background: rgba(255,255,255,.6);
      backdrop-filter: saturate(120%) blur(1px);
      z-index:3;
    }
  </style>

<script>
    // =========================
    //  LOCATION PICKER TRANSAKSI (mirror Stoker)
    // =========================
    (function(){
      const selProv  = document.getElementById('transaksi_province');
      const selCity  = document.getElementById('transaksi_city');
      const selDist  = document.getElementById('transaksi_district');
      const btnClear = document.getElementById('btn-transaksi-clear');
      const searchEl = document.getElementById('transaksi_search');
      const selType  = document.getElementById('transaksi_property_type');
      const vendorEl = document.getElementById('transaksi_vendor');

      if (!selProv || !selCity || !selDist) return;

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

      window.__transaksiReadFilters  = readFilters;
      window.__transaksiIsEmpty      = isEmptyFilters;
      window.__transaksiFiltersEqual = isEqual;

      const DATA_URL = "{{ asset('data/indonesia.json') }}";
      const provinceMap = new Map();
      const locationMap = new Map();

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
      }).catch(e=>console.error('Gagal load indonesia.json (transaksi):', e));

      const softReload = () => {
        if (typeof window.__transaksiDebounced === 'function') window.__transaksiDebounced();
      };
      selProv.addEventListener('change', ()=>{ fillCities(selProv.value); softReload(); });
      selCity.addEventListener('change', ()=>{ fillDistricts(selProv.value, selCity.value); softReload(); });
      selDist.addEventListener('change', softReload);

      btnClear?.addEventListener('click', ()=>{
        const before = readFilters();

        searchEl && (searchEl.value = '');
        vendorEl && (vendorEl.value = '');
        if (selType) selType.selectedIndex = 0;

        selProv.selectedIndex = 0;
        fillCities(null);

        const after = readFilters();

        if (!isEqual(before, after) && typeof window.__loadTransaksiList === 'function') {
          window.__loadTransaksiList({ page: 1 });
        }
      });
    })();


// =========================
//   AJAX TABEL TRANSAKSI (mirror Stoker, tanpa bulk)
// =========================
document.addEventListener('DOMContentLoaded', function () {
  // ==== Filter refs ====
  const input   = document.getElementById('transaksi_search');
  const vendor  = document.getElementById('transaksi_vendor');
  const selType = document.getElementById('transaksi_property_type');
  const selProv = document.getElementById('transaksi_province');
  const selCity = document.getElementById('transaksi_city');
  const selDist = document.getElementById('transaksi_district');

  // ==== Host partial (stabil) ====
  const host = document.getElementById('transaksi-fragment-host');

  // ==== Route fragment ====
  const fragmentRoute = "{{ route('dashboard.owner.transaksi.list') }}";

  // ==== Overlay (hanya pada tabel) ====
  function getOverlay(){ return document.getElementById('transaksi-loading'); }
  function showLoading(on){ const el = getOverlay(); el && el.classList.toggle('d-none', !on); }

  // ==== Params & state ====
  let t, lastReqId = 0;
  function paramsObj(merge = {}) {
    return {
      tab: 'transaksi',
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
      host.innerHTML = html;     // replace tabel + pagination
      if (window.afterTransaksiListReplaced) window.afterTransaksiListReplaced();
    } catch (e) {
      if (e?.name !== 'AbortError') console.error('transaksi load error:', e);
    } finally {
      if (myId === lastReqId) showLoading(false);
    }
  }
  window.__loadTransaksiList = loadList;

  const debounced = () => { clearTimeout(t); t = setTimeout(() => loadList(), 220); };
  window.__transaksiDebounced = debounced;

  // ==== Filter events ====
  input?.addEventListener('input', function(){
    const cleaned = this.value.replace(/[^\d]/g, '');
    if (this.value !== cleaned) this.value = cleaned;
    debounced();
  });
  vendor?.addEventListener('input', debounced);
  [selType, selProv, selCity, selDist].forEach(el => el && el.addEventListener('change', debounced));

  // Intercept submit (biar gak full reload)
  document.getElementById('transaksi-filter-form')
    ?.addEventListener('submit', function(e){ e.preventDefault(); loadList({ page: 1 }); });

  // Delegasi pagination (butuh link dengan class .js-transaksi-page di partial.transaksi_list)
  host?.addEventListener('click', function(e){
    const a = e.target.closest('a.js-transaksi-page');
    if (!a) return;
    e.preventDefault();
    const page = a.dataset.page || '1';
    loadList({ page });
  });

  // >>>> REVISI: event delegation untuk tombol CLOSING + EDIT (kebal replace innerHTML) <<<<
  window.afterTransaksiListReplaced = function(){
    if (!host) return;

    // pasang 1x saja
    if (host.dataset.tcDelegated === '1') return;
    host.dataset.tcDelegated = '1';

    host.addEventListener('click', function(e){

      // ====== EDIT ======
      const editBtn = e.target.closest('#transaksi-list-inner .trx-history-edit');
      if (editBtn) {
        e.preventDefault();

        const ds = editBtn.dataset;

        const payload = {
          id_listing   : ds.idListing,
          id_transaksi : ds.idTransaksi || null,
          status       : ds.status || null,
          lokasi       : ds.lokasi || '',
          tipe         : ds.tipe || '',
          harga_limit  : Number(ds.hargaLimit || 0),
          gambar       : (ds.gambar || '').trim(),
          photo        : (ds.photo  || '').trim(),
          copic_name   : (ds.copicName || '').trim(),

          // === FIELD BARU (EDIT) ===
          harga_deal   : ds.hargaDeal || '',
          cobroke_fee  : ds.cobrokeFee || '',
          royalty_fee  : ds.royaltyFee || ''
        };

        if (window.handleTransaksiClosingClick) {
          try {
            // OPEN dulu (ini biasanya reset)
            window.handleTransaksiClosingClick(payload, editBtn);

            // lalu PREFILL dari dataset (biar gak ketiban resetCalculation)
            if (typeof window.prefillClosingFormFromDataset === 'function') {
              window.prefillClosingFormFromDataset(editBtn);
            }
          } catch(err) {
            console.error(err);
          }
        } else {
          console.log('Edit clicked (no handler):', payload);
        }
        return;
      }

      // ====== CLOSING (LIST) ======
      const closeBtn = e.target.closest('#transaksi-list-inner .btn-transaksi-closing');
      if (closeBtn) {
        e.preventDefault();

        const ds = closeBtn.dataset;

        const payload = {
          id_listing:   ds.idListing,
          id_transaksi: ds.idTransaksi || null,
          status:       ds.status || null,
          lokasi:       ds.lokasi || '',
          tipe:         ds.tipe || '',
          harga_markup: Number(ds.hargaMarkup || 0),
          harga_limit:  Number(ds.hargaLimit  || 0),

          // kalau tombol closing juga membawa nilai edit (opsional)
          harga_deal   : ds.hargaDeal || '',
          cobroke_fee  : ds.cobrokeFee || '',
          royalty_fee  : ds.royaltyFee || ''
        };

        if (window.handleTransaksiClosingClick) {
          try { window.handleTransaksiClosingClick(payload, closeBtn); } catch(err){ console.error(err); }
        } else {
          console.log('Closing clicked (no handler):', payload);
        }
        return;
      }

    });
  };

  // initial bind untuk render pertama (include blade)
  window.afterTransaksiListReplaced();
});

</script>

{{-- ========= MODAL / CARD CLOSING TRANSAKSI ========= --}}
<div id="transaksi-closing-overlay" class="tc-overlay d-none">
    <div class="tc-dialog">
      <div class="tc-card">
        <button type="button" class="btn-close tc-close-btn transaksi-modal-close" aria-label="Close"></button>

        {{-- FORM SEKARANG MEMBUNGKUS KEDUA KOLOM (FOTO + INPUT) --}}
        <form id="closingForm" method="POST" action="{{ route('transaction.updateStatus') }}">
          @csrf

          {{-- hidden --}}
          <input type="hidden" name="id_listing"   id="tc-id-listing">
          <input type="hidden" name="id_transaksi" id="tc-id-transaksi">
          <input type="hidden" name="id_klien"     id="tc-client">

          {{-- NEW: TEAM LEADER (ID) dikirim ke server --}}
          <input type="hidden" name="team_leader" id="tc-team-leader" value="AG016">

          <script>
            // Paksa foto modal selalu ngikut data-photo tombol Closing
            document.addEventListener('click', function(e){
              const btn = e.target.closest('.btn-transaksi-closing');
              if (!btn) return; // kalau yang diklik bukan tombol Closing, keluar

              const img = document.getElementById('tc-photo');
              if (!img) return;

              // Ambil URL dari data-photo
              var src = (btn.dataset.photo || '').trim();

              // Kalau data-photo kosong, coba parse dari data-gambar (RAW kolom gambar)
              if (!src && btn.dataset.gambar) {
                var parts = String(btn.dataset.gambar)
                  .split(',')
                  .map(function(s){ return s.trim(); })
                  .filter(Boolean);
                if (parts.length > 0) {
                  src = parts[0];
                }
              }

              // Kalau tetap kosong, baru fallback ke placeholder
              if (!src) {
                src = "{{ asset('img/placeholder.jpg') }}";
              }

              img.src = src;
              img.alt = 'Foto Properti ' + (btn.dataset.idListing || '');
              console.log('SET FOTO MODAL ->', src);

              // === NEW: saat modal dibuka, set default Team Leader ke AG016 (tampilkan namanya) ===
              // (label akan di-set oleh script Team Leader di bawah)
              const tlHidden = document.getElementById('tc-team-leader');
              if (tlHidden && (!tlHidden.value || tlHidden.value.trim() === '')) {
                tlHidden.value = 'AG016';
              }
              // trigger refresh label jika fungsi tersedia
              if (window.__tcRefreshTeamLeaderLabel) window.__tcRefreshTeamLeaderLabel();
            });
          </script>

          <style>
            .tc-photo-wrap{
              width: 100%;
              aspect-ratio: 3 / 4;       /* 3x4 */
              border-radius: .75rem;
              overflow: hidden;
              background: #f9fafb;
              border: 2px solid #000;    /* garis hitam jelas */
            }

            .tc-photo-wrap img#tc-photo{
              width: 100%;
              height: 100%;
              object-fit: cover;         /* isi frame 3x4 dengan rapi */
              display: block;
            }

            /* NEW: avatar kecil untuk TL biar konsisten */
            .tc-agent-avatar{
              width: 24px;
              height: 24px;
              border-radius: 999px;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              font-weight: 700;
              border: 1px solid rgba(0,0,0,.15);
              background: #fff;
            }
          </style>

          <div class="row g-4 align-items-start tc-form-body">
            {{-- 1/4: FOTO + ID + ALAMAT + TANGGAL --}}
            <div class="col-12 col-md-3">
              <div class="tc-photo-wrap mb-3">
                <img id="tc-photo" src="{{ asset('img/placeholder.jpg') }}" alt="Foto Properti" class="w-100 h-100">
              </div>

              <div class="small fw-semibold text-muted mb-1" id="tc-summary-id">
                ID : –
              </div>
              <div class="small text-muted mb-2" id="tc-summary-lokasi">
                Alamat : –
              </div>

              <label class="form-label small mb-1">Tanggal Closing</label>
              <input type="date"
                     name="tanggal_diupdate"
                     id="tc-tanggal"
                     class="form-control form-control-sm"
                     required>
            </div>

            {{-- 3/4: FORM INPUT UTAMA --}}
            <div class="col-12 col-md-9">
              <h5 class="fw-semibold mb-2">Update Status Closing</h5>
              <p class="small text-muted mb-3">
                Lengkapi detail closing untuk properti ini. Data akan tersimpan di riwayat transaksi.
              </p>

              <div class="row g-3">
                {{-- STATUS (disembunyikan, tetap dikirim ke server) --}}
                <div class="col-12 col-md-6 d-none">
                  <label class="form-label small mb-1">Status Transaksi</label>
                  <select name="status" id="tc-status" class="form-select form-select-sm">
                    <option value="Closing">Closing</option>
                    <option value="Kuitansi">Kuitansi</option>
                    <option value="Kode Billing">Kode Billing</option>
                    <option value="Kutipan Risalah Lelang">Kutipan Risalah Lelang</option>
                    <option value="Akte Grosse">Akte Grosse</option>
                    <option value="Balik Nama">Balik Nama</option>
                    <option value="Eksekusi Pengosongan">Eksekusi Pengosongan</option>
                    <option value="Selesai">Selesai</option>
                  </select>
                </div>

                {{-- BARIS: SKEMA KOMISI | AGENT | CLIENT --}}
                {{-- SKEMA KOMISI --}}
                <div class="col-12 col-md-4">
                  <label class="form-label small mb-1">Skema komisi</label>

                  {{-- hidden yang dikirim ke server --}}
                  <input type="hidden" name="closing_type" id="tc-closing-type" value="profit">

                  <div class="dropdown w-100">
                    <button
                      class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center tc-select-btn"
                      type="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <span id="tc-scheme-label">Persentase komisi</span>
                      <i class="bi bi-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu w-100">
                      <li>
                        <button type="button"
                                class="dropdown-item tc-scheme-option"
                                data-value="profit">
                          Persentase komisi
                        </button>
                      </li>
                      <li>
                        <button type="button"
                                class="dropdown-item tc-scheme-option"
                                data-value="price_gap">
                          Selisih harga
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>

                {{-- AGENT (custom dropdown + avatar kecil) --}}
                <div class="col-12 col-md-4">
                  <label class="form-label small mb-1">Agent yang Closing</label>

                  {{-- hidden yang dikirim ke server --}}
                  <input type="hidden" name="id_agent" id="tc-agent">

                  <div class="dropdown w-100">
                    <button
                      class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center tc-select-btn"
                      type="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <span class="d-flex align-items-center gap-2">
                        <span class="tc-agent-avatar" id="tc-agent-avatar-btn">?</span>
                        <span id="tc-agent-label">Pilih Agent</span>
                      </span>
                      <i class="bi bi-chevron-down ms-2"></i>
                    </button>

                    <ul class="dropdown-menu w-100 tc-agent-menu">
                      @foreach($performanceAgents as $ag)
                        @php
                          $initial = mb_strtoupper(mb_substr($ag->nama, 0, 1, 'UTF-8'));
                        @endphp
                        <li>
                          <button type="button"
                                  class="dropdown-item d-flex align-items-center gap-2 tc-agent-option"
                                  data-id="{{ $ag->id_agent }}"
                                  data-name="{{ $ag->nama }}"
                                  data-initial="{{ $initial }}">
                            <span class="tc-agent-avatar">{{ $initial }}</span>
                            <span>{{ $ag->nama }}</span>
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>

                {{-- CLIENT (dropdown mirip Agent, dari account roles=User) --}}
                <div class="col-12 col-md-4">
                  <label class="form-label small mb-1">Client</label>

                  <div class="dropdown w-100">
                    <button
                      class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center tc-select-btn"
                      type="button"
                      data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <span class="d-flex align-items-center gap-2">
                        <span class="tc-agent-avatar" id="tc-client-avatar-btn">?</span>
                        <span id="tc-client-label">Pilih Client</span>
                      </span>
                      <i class="bi bi-chevron-down ms-2"></i>
                    </button>

                    <ul class="dropdown-menu w-100 tc-agent-menu">
                      @foreach($clientsDropdown as $cl)
                        @php
                          $initial = mb_strtoupper(mb_substr($cl->nama, 0, 1, 'UTF-8'));
                        @endphp
                        <li>
                          <button type="button"
                                  class="dropdown-item d-flex align-items-center gap-2 tc-client-option"
                                  data-id="{{ $cl->id_account }}"
                                  data-name="{{ $cl->nama }}"
                                  data-initial="{{ $initial }}">
                            <span class="tc-agent-avatar">{{ $initial }}</span>
                            <span>{{ $cl->nama }}</span>
                          </button>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>

                {{-- INFO HARGA + AREA PERHITUNGAN (4 TAB) --}}
                <div class="col-12">
                  <div class="tc-price-box mt-2">
                    {{-- TAB BUTTONS --}}
                    <div class="tc-tabs small mb-2">
                      {{-- NEW: TAB PALING KIRI --}}
                      <button type="button"
                              class="tc-tab-btn"
                              data-tab="agentinfo">
                        Informasi Agent
                      </button>

                      <button type="button"
                              class="tc-tab-btn tc-tab-btn-active"
                              data-tab="transaksi">
                        Informasi Transaksi
                      </button>
                      <button type="button"
                              class="tc-tab-btn"
                              data-tab="property">
                        Informasi Properti
                      </button>
                      <button type="button"
                              class="tc-tab-btn"
                              data-tab="pembagian">
                        Detail Pembagian
                      </button>
                    </div>

                    {{-- NEW PANEL: INFORMASI AGENT --}}
<div class="tc-tab-panel" data-tab="agentinfo">
    <div class="row g-3 small">
      <div class="col-12 col-md-6">
        <label class="form-label small mb-1">Team Leader</label>

        {{-- Hidden value yang dikirim ke server (DEFAULT SELALU AG016) --}}
        <input type="hidden" name="team_leader" id="tc-team-leader" value="AG016">

        <div class="dropdown w-100">
          <button
            class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center tc-select-btn"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
              <span class="tc-agent-avatar" id="tc-tl-avatar-btn">A</span>
              {{-- label tampil NAMA --}}
              <span id="tc-tl-label">Memuat...</span>
            </span>
            <i class="bi bi-chevron-down ms-2"></i>
          </button>

          <ul class="dropdown-menu w-100 tc-agent-menu" id="tc-tl-menu">
            @foreach($agentsDropdown as $ag)
              @php
                $initial = mb_strtoupper(mb_substr($ag->nama ?? '-', 0, 1, 'UTF-8'));
              @endphp
              <li>
                <button type="button"
                        class="dropdown-item d-flex align-items-center gap-2 tc-tl-option"
                        data-id="{{ $ag->id_agent }}"
                        data-name="{{ $ag->nama }}"
                        data-initial="{{ $initial }}">
                  <span class="tc-agent-avatar">{{ $initial }}</span>
                  <span>{{ $ag->nama }}</span>
                  <span class="text-muted ms-auto">{{ $ag->id_agent }}</span>
                </button>
              </li>
            @endforeach
          </ul>
        </div>

        <div class="text-muted small mt-2">
          Default Team Leader ID: <span class="fw-semibold" id="tc-tl-default-hint">AG016</span>
        </div>
      </div>

      <div class="col-12 col-md-6">
        <div class="border rounded-3 p-3 h-100">
          <div class="text-muted small mb-1">Ringkasan</div>
          <div class="small">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Agent Closing</span>
              <span class="fw-semibold" id="tc-agent-summary">-</span>
            </div>
            <div class="d-flex justify-content-between mt-1">
              <span class="text-muted">Team Leader</span>
              <span class="fw-semibold" id="tc-tl-summary">-</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    (function(){
      function setTeamLeader(id, name, initial){
        const hidden = document.getElementById('tc-team-leader');
        const label  = document.getElementById('tc-tl-label');
        const avatar = document.getElementById('tc-tl-avatar-btn');
        const sum    = document.getElementById('tc-tl-summary');

        if (hidden) hidden.value = id || 'AG016';

        // Yang ditampilkan adalah NAMA
        const displayName = (name && String(name).trim()) ? name : (id || 'AG016');

        if (label) label.textContent = displayName;
        if (avatar) avatar.textContent = (initial || displayName.charAt(0) || 'A').toUpperCase();
        if (sum) sum.textContent = displayName + ' (' + (id || 'AG016') + ')';
      }

      // Default: selalu AG016, tapi tampilkan nama dari list jika ada
      function initDefaultAG016(){
        const hidden = document.getElementById('tc-team-leader');
        if (hidden) hidden.value = 'AG016';

        // cari option yang id=AG016
        const btn = document.querySelector('.tc-tl-option[data-id="AG016"]');
        if (btn) {
          setTeamLeader(btn.dataset.id, btn.dataset.name, btn.dataset.initial);
        } else {
          // fallback jika AG016 tidak ada di dropdown
          setTeamLeader('AG016', 'AG016', 'A');
        }
      }

      // Klik option Team Leader
      document.addEventListener('click', function(e){
        const opt = e.target.closest('.tc-tl-option');
        if (!opt) return;
        e.preventDefault();
        setTeamLeader(opt.dataset.id, opt.dataset.name, opt.dataset.initial);
      });

      // OPTIONAL: update ringkasan Agent Closing kalau user pilih agent
      document.addEventListener('click', function(e){
        const opt = e.target.closest('.tc-agent-option');
        if (!opt) return;
        const sum = document.getElementById('tc-agent-summary');
        if (sum) sum.textContent = (opt.dataset.name ? (opt.dataset.name + ' (' + opt.dataset.id + ')') : (opt.dataset.id || '-'));
      });

      // Saat modal dibuka, set default AG016 lagi (biar konsisten tiap buka modal)
      document.addEventListener('click', function(e){
        const btn = e.target.closest('.btn-transaksi-closing');
        if (!btn) return;
        initDefaultAG016();
      });

      // init pertama kali
      initDefaultAG016();
    })();
      // Map untuk cari nama agent berdasarkan id_agent
  window.AGENT_NAME_MAP = window.AGENT_NAME_MAP || {};
  @foreach($agentsDropdown as $ag)
    window.AGENT_NAME_MAP[@json($ag->id_agent)] = @json($ag->nama);
  @endforeach
    </script>


                    {{-- PANEL: INFORMASI TRANSAKSI --}}
                    <div class="tc-tab-panel tc-tab-panel-active" data-tab="transaksi">
                      {{-- BARIS 1: harga limit | harga deal | harga menang | komisi/selisih --}}
                      <div class="row g-2 small align-items-end">

                        {{-- Harga Limit (EXISTING - TIDAK DIUBAH) --}}
                        <div class="col-6 col-md-3">
                          <div class="text-muted">Harga Limit</div>
                          <div class="fw-semibold" id="tc-harga-limit">Rp 0</div>
                        </div>

                        {{-- Harga Deal (NEW) --}}
                        <div class="col-6 col-md-3">
                          <label class="form-label small mb-1">Harga Deal</label>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   name="harga_deal"
                                   id="tc-harga-deal"
                                   class="form-control"
                                   placeholder="0">
                          </div>
                        </div>

                        {{-- Harga Menang Transaksi (EXISTING - TIDAK DIUBAH ISINYA) --}}
                        <div class="col-6 col-md-3">
                          <label class="form-label small mb-1">Harga bidding</label>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   name="harga_menang"
                                   id="tc-harga-menang"
                                   class="form-control"
                                   placeholder="0">
                          </div>
                        </div>

                        {{-- Komisi / Selisih (EXISTING - TIDAK DIUBAH LOGIC) --}}
                        <div class="col-6 col-md-3">

                          {{-- MODE 1: KOMISI (%) --}}
                          <div id="tc-komisi-wrapper">
                            <label class="form-label small mb-1">Komisi (%)</label>
                            <div class="input-group input-group-sm">
                              <input type="number"
                                     min="0"
                                     step="0.01"
                                     name="komisi_persen"
                                     id="tc-komisi-persen"
                                     class="form-control"
                                     placeholder="0">
                              <span class="input-group-text">%</span>
                            </div>
                          </div>

                          {{-- MODE 2: SELISIH (auto Rp) --}}
                          <div id="tc-selisih-wrapper" class="d-none">
                            <label class="form-label small mb-1">Selisih</label>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text">Rp</span>
                              <input type="text"
                                     id="tc-selisih"
                                     class="form-control"
                                     readonly>
                            </div>
                          </div>

                        </div>
                      </div>

                      <hr class="tc-dash my-3">

                      {{-- BARIS 2: Biaya balik nama | biaya eksekusi | royalty | cobroke --}}
                      <div class="row g-2 small align-items-end">

                        {{-- Biaya Balik Nama (EXISTING) --}}
                        <div class="col-6 col-md-3">
                          <label class="form-label small mb-1">Biaya Balik Nama</label>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   name="biaya_balik_nama"
                                   id="tc-biaya-balik-nama"
                                   class="form-control"
                                   placeholder="0">
                          </div>
                        </div>

                        {{-- Biaya Eksekusi (EXISTING) --}}
                        <div class="col-6 col-md-3">
                          <label class="form-label small mb-1">Biaya Eksekusi</label>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   name="biaya_eksekusi"
                                   id="tc-biaya-eksekusi"
                                   class="form-control"
                                   placeholder="0">
                          </div>
                        </div>

                        {{-- Royalty Fee (NEW) --}}
                        <div class="col-6 col-md-3">
                          <label class="form-label small mb-1">Royalty Fee</label>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   name="royalty_fee"
                                   id="tc-royalty-fee"
                                   class="form-control"
                                   placeholder="0">
                          </div>
                        </div>

                        {{-- Cobroke Fee (NEW) --}}
                        <div class="col-6 col-md-3">
                          <label class="form-label small mb-1">Cobroke Fee</label>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   name="cobroke_fee"
                                   id="tc-cobroke-fee"
                                   class="form-control"
                                   placeholder="0">
                          </div>
                        </div>

                      </div>

                      <hr class="tc-dash my-3">

                      {{-- BARIS 3: auto perhitungan --}}
                      <div class="tc-price-summary small pt-2 border-top-0">
                        <div class="row g-2 tc-summary-row">

                          <div class="col-6 col-md-3">
                            <div class="text-muted mb-1" id="tc-komisi-label-summary">Komisi Agent</div>
                            <div class="fw-semibold" id="tc-komisi-estimasi">Rp 0</div>
                          </div>

                          <div class="col-6 col-md-3 tc-summary-extra" id="tc-kotor-wrapper">
                            <div class="text-muted mb-1">Gross Profit kantor</div>
                            <div class="fw-semibold" id="tc-kotor-estimasi">Rp 0</div>
                          </div>

                          <div class="col-6 col-md-3 tc-summary-extra" id="tc-kenaikan-wrapper">
                            <div class="text-muted mb-1">Kenaikan dari limit</div>
                            <div class="fw-semibold">
                              <span id="tc-kenaikan-persentase">0</span>%
                            </div>
                          </div>

                          {{-- Selisih (NEW - DISPLAY ONLY) --}}
                          <div class="col-6 col-md-3 tc-summary-extra" id="tc-selisih-summary-wrapper">
                            <div class="text-muted mb-1">Selisih</div>
                            <div class="fw-semibold" id="tc-selisih-summary">Rp 0</div>
                          </div>

                        </div>
                      </div>
                    </div>

                    {{-- PANEL: INFORMASI PROPERTI (riwayat lelang) --}}
                    <div class="tc-tab-panel" data-tab="property">
                      <div id="tc-property-history" class="tc-property-history">
                        <div class="small text-muted">
                          Riwayat lelang properti akan ditampilkan di sini.
                        </div>
                      </div>
                    </div>

                    {{-- PANEL: DETAIL PEMBAGIAN --}}
                    <div class="tc-tab-panel" data-tab="pembagian">
                      <div class="tc-pembagian">
                        <div class="tc-pembagian-header d-flex justify-content-between align-items-baseline mb-2">
                          <div class="small text-muted" id="tc-base-label">
                            Basis pembagian akan muncul setelah Anda mengisi harga & komisi.
                          </div>
                          <div class="small fw-semibold" id="tc-base-nominal">Rp 0</div>
                        </div>

                        <div class="table-responsive">
                          <table class="table table-sm tc-pembagian-table align-middle mb-0">
                            <thead class="table-light">
                              <tr>
                                <th class="text-start">Pos</th>
                                <th class="text-end">Porsi</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-center">Nama Agent</th>
                              </tr>
                            </thead>
                            <tbody id="tc-pembagian-body">
                              <tr>
                                <td colspan="4" class="text-center text-muted small">
                                  Isi dulu harga menang / komisi untuk melihat detail pembagian.
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 tc-form-footer">
                  <button type="button" class="btn btn-light btn-sm transaksi-modal-cancel">
                    Batal
                  </button>
                  <button type="submit" class="btn btn-primary btn-sm px-3" id="tc-submit-btn">
                    Simpan Perubahan
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>

        {{-- NEW: JS untuk Team Leader dropdown + sinkron ringkasan --}}
        <script>
          (function(){
            function setTeamLeader(id, name, initial){
              const hidden = document.getElementById('tc-team-leader');
              const label  = document.getElementById('tc-tl-label');
              const avatar = document.getElementById('tc-tl-avatar-btn');
              const hint   = document.getElementById('tc-tl-default-hint');
              const sum    = document.getElementById('tc-tl-summary');

              if (hidden) hidden.value = id || '';
              if (label)  label.textContent = name || id || 'Pilih Team Leader';
              if (avatar) avatar.textContent = (initial || (name ? name.trim().charAt(0) : (id || 'A'))).toUpperCase();
              if (hint)   hint.textContent = 'AG016';
              if (sum)    sum.textContent = (name ? (name + ' (' + (id||'') + ')') : (id||'-'));
            }

            // expose refresh function for "modal open" handler di atas
            window.__tcRefreshTeamLeaderLabel = function(){
              const hidden = document.getElementById('tc-team-leader');
              const current = hidden && hidden.value ? hidden.value.trim() : 'AG016';

              // cari option button yang match id
              const btn = document.querySelector('.tc-tl-option[data-id="'+ CSS.escape(current) +'"]');
              if (btn) {
                setTeamLeader(btn.dataset.id, btn.dataset.name, btn.dataset.initial);
              } else {
                // fallback tampilkan ID saja
                setTeamLeader(current, current, (current||'A').charAt(0));
              }
            };

            // click option TL
            document.addEventListener('click', function(e){
              const opt = e.target.closest('.tc-tl-option');
              if (!opt) return;

              e.preventDefault();
              setTeamLeader(opt.dataset.id, opt.dataset.name, opt.dataset.initial);
            });

            // sinkron ringkasan agent closing (biar enak di tab Informasi Agent)
            document.addEventListener('click', function(e){
              const opt = e.target.closest('.tc-agent-option');
              if (!opt) return;

              const sum = document.getElementById('tc-agent-summary');
              if (sum) sum.textContent = (opt.dataset.name ? (opt.dataset.name + ' (' + opt.dataset.id + ')') : (opt.dataset.id || '-'));
            });

            // init pertama kali
            window.__tcRefreshTeamLeaderLabel();
          })();
        </script>

        <script>
          (function(){
            const form = document.getElementById('closingForm');
            if (!form) return;
            const submitBtn = document.getElementById('tc-submit-btn') || form.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            let isSubmitting = false;

            form.addEventListener('submit', function(e){
              if (isSubmitting) {
                e.preventDefault();
                return;
              }

              const agentInput  = document.getElementById('tc-agent');
              const hargaInput  = document.getElementById('tc-harga-menang');
              const schemeInput = document.getElementById('tc-closing-type');
              const komisiInput = document.getElementById('tc-komisi-persen');

              // NEW: TL wajib ada (default AG016)
              const tlInput = document.getElementById('tc-team-leader');

              const agentVal = (agentInput && agentInput.value ? agentInput.value : '').trim();
              const tlVal    = (tlInput && tlInput.value ? tlInput.value : '').trim();

              const hargaRaw = (hargaInput && hargaInput.value ? hargaInput.value : '').replace(/[^\d]/g,'');
              const hargaNum = hargaRaw ? parseInt(hargaRaw, 10) : 0;
              const schemeVal = (schemeInput && schemeInput.value ? schemeInput.value : '').trim();
              const komisiRaw = (komisiInput && komisiInput.value ? komisiInput.value : '').trim();
              const komisiNum = komisiRaw ? parseFloat(komisiRaw.replace(',','.')) : 0;

              if (!tlVal) {
                e.preventDefault();
                alert('Silakan pilih Team Leader terlebih dahulu.');
                return;
              }

              if (!agentVal) {
                e.preventDefault();
                alert('Silakan pilih agent yang closing terlebih dahulu.');
                return;
              }

              if (!hargaNum || isNaN(hargaNum) || hargaNum <= 0) {
                e.preventDefault();
                alert('Silakan isi harga menang transaksi dengan benar.');
                return;
              }

              if (schemeVal === 'profit' && (!komisiRaw || isNaN(komisiNum) || komisiNum <= 0)) {
                e.preventDefault();
                alert('Silakan isi persentase komisi (%).');
                return;
              }

              // lulus validasi → aktifkan spinner & lock tombol
              isSubmitting = true;
              submitBtn.disabled = true;
              submitBtn.dataset.originalHtml = submitBtn.innerHTML;
              submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
                'Menyimpan...';
            });
          })();
        </script>

      </div>
    </div>
  </div>



  <style>
    .tc-overlay{
      position:fixed;
      inset:0;
      z-index:1100; /* di atas navbar (1050) */
      background:rgba(15,23,42,.45);
      backdrop-filter:blur(2px);
      display:flex;
      align-items:center;
      justify-content:center;
      padding:1rem;
    }
    .tc-overlay.d-none{ display:none!important; }

    .tc-dialog{
      max-width:900px;
      width:100%;
    }

    .tc-card{
      position:relative;
      background:#fff;
      border-radius:1rem;
      padding:1.5rem 1.75rem;
      box-shadow:0 18px 45px rgba(15,23,42,.25);
      display:flex;
      flex-direction:column;
      max-height:90vh;
    }

    #closingForm{
      flex:1;
      display:flex;
      flex-direction:column;
    }

    .tc-form-body{
      flex:1;
      min-height:0;
    }

    .tc-form-footer{
      margin-top:auto;
    }

    .tc-close-btn{
      position:absolute;
      top:.75rem;
      right:.75rem;
      font-size:.85rem;
    }

    /* >>> Foto vertikal ala 3x4 */
    .tc-photo-wrap{
      width:100%;
      aspect-ratio:3 / 4; /* lebih tinggi daripada lebar */
      border-radius:.75rem;
      overflow:hidden;
      background:#f3f4f6;
      border:1px solid #e5e7eb;
    }
    .tc-photo-wrap img{
      width:100%;
      height:100%;
      object-fit:cover;
    }

    /* tombol select (skema & agent & client) */
    .tc-select-btn{
      border-color:#fd6e14;
      color:#fd6e14;
      font-weight:600;
      border-width:1.5px;
      border-radius:.5rem;
      padding:.3rem .7rem;
      min-height:34px;
      font-size:.85rem;
      background:#fff;
      transition:all .12s ease-in-out;
    }
    .tc-select-btn:hover,
    .tc-select-btn:focus{
      border-color:#f97316;
      color:#f97316;
      box-shadow:0 0 0 .15rem rgba(250,204,21,.35);
      background:#fffbeb; /* kuning lembut */
    }

    .tc-price-box{
      border-radius:.75rem;
      border:1px dashed #e5e7eb;
      padding:.9rem 1rem;
      background:#f9fafb;
    }

    .tc-dash{
      border:0;
      border-top:1px dashed #e5e7eb;
    }

    /* TAB STYLES */
    .tc-tabs{
      display:flex;
      flex-wrap:wrap;
      gap:.5rem;
      justify-content:center; /* center di dalam box abu-abu */
    }
    .tc-tab-btn{
      border-radius:999px;
      border:1px solid #e5e7eb;
      background:transparent;
      color:#6b7280;
      padding。.25rem .9rem;
      font-size。.8rem;
      font-weight:500;
      cursor:pointer;
      transition:all .12s ease-in-out;
    }
    .tc-tab-btn:hover{
      border-color:#facc15;
      background:#fffbeb;   /* hover kuning lembut */
      color:#374151;
    }
    .tc-tab-btn-active{
      background:#fd6e14;
      border-color:#fd6e14;
      color:#fff;
      box-shadow:0 0 0 .12rem rgba(253,110,20,.16);
    }

    .tc-tab-panel{
      display:none;
      margin-top:.5rem;
    }
    .tc-tab-panel-active{
      display:block;
    }

    .tc-price-summary{
      border-radius:.5rem;
    }
    .tc-summary-row{
      align-items:flex-start;
    }

    .tc-agent-avatar{
      width:24px;
      height:24px;
      border-radius:999px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:.75rem;
      font-weight:700;
      background:#fee2e2;
      color:#b91c1c;
      flex-shrink:0;
    }

    /* dropdown agent/client biar bisa scroll */
    .tc-agent-menu{
      max-height:260px;
      overflow-y:auto;
    }

    @media (max-width: 768px){
      .tc-card{ padding:1.25rem 1rem; }
    }
    .tc-property-history-list{
      display:flex;
      flex-direction:column;
      gap:.75rem;
      margin-top:.5rem;
    }
    .tc-property-history-item{
      border-radius:.75rem;
      border:1px solid #e5e7eb;
      background:#fefefe;
      padding:.6rem .75rem;
    }
    .tc-ph-badge{
      padding:.1rem .5rem;
      border-radius:999px;
      background:#fffbeb;
      border:1px solid #facc15;
      font-size:.7rem;
      font-weight:600;
      color:#92400e;
    }
    .tc-ph-thumb{
      width:64px;
      height:64px;
      border-radius:.5rem;
      overflow:hidden;
      background:#e5e7eb;
      flex-shrink:0;
    }
    .tc-ph-thumb img{
      width:100%;
      height:100%;
      object-fit:cover;
      display:block;
    }
    /* LIST RIWAYAT PROPERTI (scroll kalau ketinggian) */
    .tc-property-history-list{
      display:flex;
      flex-direction:column;
      gap:.75rem;
      margin-top:.75rem;

      max-height:260px;        /* tinggi maksimum area abu-abu history */
      overflow-y:auto;         /* kalau lebih tinggi dari ini baru bisa scroll */
      padding-right:.25rem;    /* sedikit space biar scrollbar nggak nempel */
    }

    .tc-property-history-list::-webkit-scrollbar{
      width:6px;
    }
    .tc-property-history-list::-webkit-scrollbar-track{
      background:transparent;
    }
    .tc-property-history-list::-webkit-scrollbar-thumb{
      background:#d1d5db;
      border-radius:999px;
    }
    .tc-property-history-list::-webkit-scrollbar-thumb:hover{
      background:#9ca3af;
    }

    /* === DETAIL PEMBAGIAN === */
    .tc-pembagian{
      border-radius:.75rem;
      border:1px solid #e5e7eb;
      background:#fefefe;
      padding:.8rem .9rem;
    }
    .tc-pembagian-header{
      border-bottom:1px dashed #e5e7eb;
      padding-bottom:.35rem;
      margin-bottom:.5rem;
    }
    .tc-pembagian-table th,
    .tc-pembagian-table td{
      font-size。.78rem;
      padding。.25rem .4rem;
    }
    /* ===== SCROLL UNTUK DETAIL PEMBAGIAN ===== */
    .tc-pembagian .table-responsive{
      max-height: 260px;      /* atur tinggi area tabel */
      overflow-y: auto;       /* isi tabel bisa discroll */
    }

    /* Header tabel tetap kelihatan saat discroll */
    .tc-pembagian-table thead th{
      position: sticky;
      top: 0;
      z-index: 2;
      background: #fee2e2;    /* warna soft supaya kebaca */
      box-shadow: 0 1px 0 rgba(0,0,0,.04);
    }

  </style>

<script>
    // Binding default tombol closing -> lempar ke handler global kalau ada
    (function(){
      const btns = document.querySelectorAll('#transaksi-list-inner .btn-transaksi-closing');
      btns.forEach(btn => {
        btn.addEventListener('click', function(){
          // ambil foto dari data-photo, kalau kosong baru coba parse data-gambar
          let photo = this.dataset.photo || '';
          if (!photo) {
            const raw = this.dataset.gambar || '';
            if (raw) {
              const parts = raw.split(',').map(s => s.trim()).filter(Boolean);
              if (parts.length > 0) {
                photo = parts[0];
              }
            }
          }

          // normalisasi path foto seperti asset():
          // - kalau sudah http/https biarkan
          // - kalau relatif dan tidak diawali "/", tambahkan "/" di depan
          if (photo && !/^https?:\/\//i.test(photo)) {
            if (!photo.startsWith('/')) {
              photo = '/' + photo;
            }
          }

          const payload = {
            id_listing:   this.dataset.idListing,
            id_transaksi: this.dataset.idTransaksi || null,
            status:       this.dataset.status || null,
            lokasi:       this.dataset.lokasi || '',
            harga_markup: Number(this.dataset.hargaMarkup || 0),
            harga_limit:  Number(this.dataset.hargaLimit  || 0),
            photo:        photo,
            copic_name:   this.dataset.copicName || this.dataset.copic || ''
          };

          console.log('payload closing:', payload); // bantu debugging

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

@php
  // Ambil mapping upline langsung di Blade (id_agent => upline_id)
  $allAgentsForUpline = \DB::table('agent')
      ->select('id_agent','upline_id')
      ->get();
  $agentUplineMap = $allAgentsForUpline->pluck('upline_id','id_agent');
@endphp


<script>
    (function(){
      const PLACEHOLDER = "{{ asset('img/placeholder.jpg') }}";
      const PROPERTY_HISTORY_ROUTE = "{{ route('dashboard.owner.transaksi.history') }}";

      // === KONSTANTA SKEMA KOMISI (mirror dari tabel skema_komisi_solusindo) ===
      const THC_RATE    = 0.4000; // 40% untuk "Perkiraan hasil komisi"
      const KANTOR_RATE = 0.39;   // 39% pendapatan kotor kantor

      // maksimal Fee Team Leader
      const FEE_TL_MAX  = 2000000;
      // Fee TL dihitung dari 10% basis (selisih / fee) dengan maksimal 2jt
      const FEE_TL_RATE = 0.10;

      const KOMISI_SCHEMA = [
        { kode:'UP1',       label:'Upline 1',           rate:0.004000 },
        { kode:'UP2',       label:'Upline 2',           rate:0.003000 },
        { kode:'UP3',       label:'Upline 3',           rate:0.002000 },
        { kode:'LISTER',    label:'Lister',             rate:0.010000 },
        { kode:'COPIC',     label:'CO PIC',             rate:0.002500 },
        { kode:'CONS',      label:'Consultant',         rate:0.008500 },
        { kode:'REWARD',    label:'Reward Fund',        rate:0.030000 },
        { kode:'INV_FUND',  label:'Investment Fund',    rate:0.020000 },
        { kode:'PROMO_FUND',label:'Promotion Fund',     rate:0.020000 },
        { kode:'PIC1',      label:'PIC 1',              rate:0.040000 },
        { kode:'PIC2',      label:'PIC 2',              rate:0.040000 },
        { kode:'PIC3',      label:'PIC 3',              rate:0.040000 },
        { kode:'PIC4',      label:'PIC 4',              rate:0.040000 },
        { kode:'PIC5',      label:'PIC 5',              rate:0.040000 },
        { kode:'THC',       label:'THC',                rate:0.400000 },
        { kode:'SERVICE',   label:'Service Fund',       rate:0.100000 },
        { kode:'FEE_TL',    label:'Fee Team Leader',    rate:0.000000 }, // dinamis
        { kode:'PRINC_FEE', label:'Principal Fee',      rate:0.030000 },
        { kode:'INV_SHARE', label:'Investor Sharing',   rate:0.095200 },
        { kode:'MGMT_FUND', label:'Management Fund',    rate:0.059500 },
        { kode:'EMP_INC',   label:'Employee Incentive', rate:0.015300 },
      ];

      const KANTOR_CODES = [
        'SERVICE','MGMT_FUND','INV_SHARE','EMP_INC',
        'PIC1','REWARD','INV_FUND','PROMO_FUND','LISTER'
      ];

      // mapping kode skema -> id_agent
      const KOMISI_KODE_TO_AGENT_ID = {
        LISTER:     'AG001',
        CONS:       'AG014',
        REWARD:     'AG006',
        INV_FUND:   'AG006',
        PROMO_FUND: 'AG006',
        PIC1:       'AG006',
        PIC2:       'AG012',
        PIC3:       'AG008',
        PIC4:       'AG014',
        PIC5:       'AG009',
        SERVICE:    'AG006',
        PRINC_FEE:  'AG012',
        INV_SHARE:  'AG001',
        MGMT_FUND:  'AG006',
        EMP_INC:    'AG001',
      };

      // map id_agent -> nama (data agent aktif)
      const AGENT_NAME_MAP   = @json($performanceAgents->pluck('nama','id_agent'));
      // map id_agent -> upline_id
      const AGENT_UPLINE_MAP = @json($agentUplineMap);
      // map COPIC: id_listing -> { ids: [...], names: [...] }
      const COPIC_AGENTS_MAP = @json($copicAgentsMap ?? []);
      const GLOBAL_COPIC_MAP = COPIC_AGENTS_MAP || {};

      // kode yang selalu tampil 2 angka desimal
      const FIXED_TWO_DECIMAL_CODES = ['INV_SHARE','MGMT_FUND','EMP_INC','COPIC','CONS','FEE_TL'];

      // kode yang badge di Pos pakai label penuh
      const FULL_LABEL_BADGE_CODES = [
        'REWARD','INV_FUND','PROMO_FUND',
        'SERVICE','PRINC_FUND','INV_SHARE','MGMT_FUND','EMP_INC',
        'FEE_TL'
      ];

      function rupiah(x){
        const n = Number(x || 0);
        return 'Rp ' + n.toLocaleString('id-ID');
      }

      function onlyDigits(str){
        return (str || '').replace(/[^\d]/g,'');
      }

      // NEW: helper format input uang + trigger event
      function setMoneyInput(el, val){
        if (!el) return;
        const num = Number(String(val ?? '').replace(/[^\d]/g,'')) || 0;
        el.value = num ? num.toLocaleString('id-ID') : '';
        el.dispatchEvent(new Event('input',  { bubbles:true }));
        el.dispatchEvent(new Event('change', { bubbles:true }));
      }

      // bersihkan nama COPIC
      function cleanCopicName(raw){
        if (!raw) return '';
        let s = String(raw).replace(/\s+/g,' ').trim();
        if (!s) return '';
        let upper = s.toUpperCase();
        const stops = [
          ' LELANG', ' ID:', ' ID ', ' TANGGAL',
          ' HARGA LIMIT', ' HARGA ', ' CO PIC:'
        ];
        let cutPos = s.length;
        stops.forEach(function(sw){
          const idx = upper.indexOf(sw);
          if (idx !== -1 && idx < cutPos) cutPos = idx;
        });
        s = s.substring(0, cutPos).trim();
        s = s.replace(/[.,;:]+$/,'').trim();
        return s;
      }

      function setup(){
        const overlay     = document.getElementById('transaksi-closing-overlay');
        const form        = document.getElementById('closingForm');
        const closeBtn    = overlay?.querySelector('.transaksi-modal-close');
        const cancelBtn   = overlay?.querySelector('.transaksi-modal-cancel');
        const summaryId   = document.getElementById('tc-summary-id');
        const summaryLok  = document.getElementById('tc-summary-lokasi');
        const hargaLimitEl= document.getElementById('tc-harga-limit');
        const inputId     = document.getElementById('tc-id-listing');
        const inputTrx    = document.getElementById('tc-id-transaksi');
        const inputStatus = document.getElementById('tc-status');
        const inputTgl    = document.getElementById('tc-tanggal');
        const photoEl     = document.getElementById('tc-photo');

        const selisihSummaryEl = document.getElementById('tc-selisih-summary');

        let isHydratingModal = false;
        let lastModalPayload  = null;

        let currentHargaLimit    = 0;
        let currentCopicName     = '-';
        let currentCopicAgents   = [];
        let loadedHistoryForId   = null;
        let biayaBalikNamaManual = false;
        let currentClosingAgentName = '';
        let currentClosingAgentId   = '';

        let up1AgentName = '';
        let up2AgentName = '';
        let up3AgentName = '';

        // elemen custom skema closing
        const schemeInput = document.getElementById('tc-closing-type');
        const schemeLabel = document.getElementById('tc-scheme-label');
        const schemeOpts  = document.querySelectorAll('.tc-scheme-option');

        // elemen custom agent
        const agentInput       = document.getElementById('tc-agent');
        const agentLabel       = document.getElementById('tc-agent-label');
        const agentAvatarBtn   = document.getElementById('tc-agent-avatar-btn');
        const agentPrevName    = document.getElementById('tc-agent-name');
        const agentPrevAvatar  = document.getElementById('tc-agent-avatar');
        const agentOptions     = document.querySelectorAll('.tc-agent-option');
        const copicNameEl      = document.getElementById('tc-copic-name');

        // elemen custom client
        const clientInput      = document.getElementById('tc-client');
        const clientLabel      = document.getElementById('tc-client-label');
        const clientAvatarBtn  = document.getElementById('tc-client-avatar-btn');
        const clientOptions    = document.querySelectorAll('.tc-client-option');

        // elemen perhitungan
        const hargaMenangInput   = document.getElementById('tc-harga-menang');
        const hargaDealInput     = document.getElementById('tc-harga-deal');
        const cobrokeFeeInput    = document.getElementById('tc-cobroke-fee');
        const royaltyFeeInput    = document.getElementById('tc-royalty-fee');

        const komisiPersenInput  = document.getElementById('tc-komisi-persen');
        const komisiEstimasi     = document.getElementById('tc-komisi-estimasi');
        const komisiSummaryLbl   = document.getElementById('tc-komisi-label-summary');
        const komisiWrapper      = document.getElementById('tc-komisi-wrapper');
        const selisihWrapper     = document.getElementById('tc-selisih-wrapper');
        const selisihInput       = document.getElementById('tc-selisih');
        const kotorWrapper       = document.getElementById('tc-kotor-wrapper');
        const kotorEstimasi      = document.getElementById('tc-kotor-estimasi');
        const kenaikanWrapper    = document.getElementById('tc-kenaikan-wrapper');
        const kenaikanPersenEl   = document.getElementById('tc-kenaikan-persentase');

        const biayaBalikNamaInput= document.getElementById('tc-biaya-balik-nama');
        const biayaEksekusiInput = document.getElementById('tc-biaya-eksekusi');

        // TAB
        const tabButtons = document.querySelectorAll('.tc-tab-btn');
        const tabPanels  = document.querySelectorAll('.tc-tab-panel');
        const propertyPanel = document.querySelector('.tc-tab-panel[data-tab="property"]');

        // DETAIL PEMBAGIAN
        const baseLabelEl   = document.getElementById('tc-base-label');
        const baseNominalEl = document.getElementById('tc-base-nominal');
        const pembagianBody = document.getElementById('tc-pembagian-body');

        // NEW: Team Leader hidden (value id_agent)
        const teamLeaderInput = document.getElementById('tc-team-leader');

        if (!overlay || !form) return;

        // ==== HELPER UNTUK UPLINE ====
        const _AGENT_NAME_MAP   = (typeof AGENT_NAME_MAP !== 'undefined' && AGENT_NAME_MAP) ? AGENT_NAME_MAP : {};
        const _AGENT_UPLINE_MAP = (typeof AGENT_UPLINE_MAP !== 'undefined' && AGENT_UPLINE_MAP) ? AGENT_UPLINE_MAP : {};

        function resolveAgentNameById(agentId){
          if (agentId === null || agentId === undefined) return '';
          const key = String(agentId).trim();
          if (!key) return '';
          return _AGENT_NAME_MAP[key] || key;
        }

        function getUplineId(agentId, defaultId){
          const fallback = (defaultId === null || defaultId === undefined) ? null : String(defaultId).trim();
          if (agentId === null || agentId === undefined) return fallback;
          const key = String(agentId).trim();
          if (!key) return fallback;

          const up = _AGENT_UPLINE_MAP[key];
          if (up === null || up === undefined) return fallback;

          const upStr = String(up).trim();
          if (upStr !== '') return upStr;

          return fallback;
        }

        function recomputeUplineNames(){
          if (!currentClosingAgentId) {
            up1AgentName = '';
            up2AgentName = '';
            up3AgentName = '';
            return;
          }

          const up1Id = getUplineId(currentClosingAgentId, 'AG006');
          const up2Id = getUplineId(up1Id, 'AG001');
          const up3Id = getUplineId(up2Id, 'Cash');

          up1AgentName = resolveAgentNameById(up1Id);
          up2AgentName = resolveAgentNameById(up2Id);
          up3AgentName = resolveAgentNameById(up3Id);
        }
        // =============================

        // ✅ NEW: ambil nama Team Leader terpilih dari dropdown (hidden input tc-team-leader)
        function getSelectedTeamLeaderId(){
          const id = (teamLeaderInput && teamLeaderInput.value) ? String(teamLeaderInput.value).trim() : '';
          return id || 'AG016';
        }
        function getSelectedTeamLeaderName(){
          const id = getSelectedTeamLeaderId();

          // prioritas 1: map dari data agentsDropdown (yang Anda inject di No 1)
          if (window.AGENT_NAME_MAP && window.AGENT_NAME_MAP[id]) {
            return window.AGENT_NAME_MAP[id];
          }

          // prioritas 2: map performanceAgents
          if (_AGENT_NAME_MAP && _AGENT_NAME_MAP[id]) {
            return _AGENT_NAME_MAP[id];
          }

          // prioritas 3: label dropdown kalau ada
          const lbl = document.getElementById('tc-tl-label');
          const txt = lbl && lbl.textContent ? lbl.textContent.trim() : '';
          if (txt && txt !== 'Memuat...' && txt !== 'Pilih Team Leader') return txt;

          return 'Team Leader';
        }

        function resetPembagian(){
          if (baseLabelEl)   baseLabelEl.textContent   = 'Basis pembagian akan muncul setelah Anda mengisi harga & komisi.';
          if (baseNominalEl) baseNominalEl.textContent = 'Rp 0';
          if (pembagianBody){
            pembagianBody.innerHTML =
              '<tr>' +
                '<td colspan="4" class="text-center text-muted small">' +
                  'Isi dulu harga menang / komisi untuk melihat detail pembagian.' +
                '</td>' +
              '</tr>';
          }
        }

        function resetScheme(){
          if (schemeInput) schemeInput.value = 'profit';
          if (schemeLabel) schemeLabel.textContent = 'Persentase komisi';
          applySchemeUI('profit');
        }

        function resetAgent(){
          if (agentInput) agentInput.value = '';
          if (agentLabel) agentLabel.textContent = 'Pilih Agent';
          if (agentAvatarBtn) agentAvatarBtn.textContent = '?';
          if (agentPrevName) agentPrevName.textContent = 'Belum dipilih';
          if (agentPrevAvatar) agentPrevAvatar.textContent = '?';
          currentClosingAgentName = '';
          currentClosingAgentId   = '';
          up1AgentName = '';
          up2AgentName = '';
          up3AgentName = '';
        }

        function resetClient(){
          if (clientInput) clientInput.value = '';
          if (clientLabel) clientLabel.textContent = 'Pilih Client';
          if (clientAvatarBtn) clientAvatarBtn.textContent = '?';
        }

        function resetCalculation(){
          if (hargaMenangInput) hargaMenangInput.value = '';
          if (hargaDealInput) hargaDealInput.value = '';
          if (cobrokeFeeInput) cobrokeFeeInput.value = '';
          if (royaltyFeeInput) royaltyFeeInput.value = '';
          if (komisiPersenInput) komisiPersenInput.value = '';
          if (selisihInput) selisihInput.value = '';
          if (selisihSummaryEl) selisihSummaryEl.textContent = 'Rp 0';
          if (komisiEstimasi) komisiEstimasi.textContent = 'Rp 0';
          if (kotorEstimasi) kotorEstimasi.textContent = 'Rp 0';
          if (kenaikanPersenEl) kenaikanPersenEl.textContent = '0';
          if (biayaBalikNamaInput) biayaBalikNamaInput.value = '';
          if (biayaEksekusiInput) biayaEksekusiInput.value = '';
          biayaBalikNamaManual = false;
          resetPembagian();
        }

        function updateKenaikanPercent(){
          if (!kenaikanPersenEl) return;
          const hargaNum = hargaMenangInput ? Number(onlyDigits(hargaMenangInput.value)) || 0 : 0;
          if (!currentHargaLimit) {
            kenaikanPersenEl.textContent = '0';
            return;
          }
          const diff = Math.max(hargaNum - currentHargaLimit, 0);
          const pct  = diff <= 0 ? 0 : (diff / currentHargaLimit) * 100;
          const pctRounded = Math.round(pct * 10) / 10;
          kenaikanPersenEl.textContent = pctRounded.toLocaleString('id-ID');
        }

        function refreshCopicAgentsFromHistory(){
          if (!propertyPanel) return;

          const found = [];
          const nodes = propertyPanel.querySelectorAll('*');
          nodes.forEach(function(el){
            const text = (el.textContent || '').trim();
            if (!text) return;

            const upper = text.toUpperCase();
            const marker = 'CO PIC:';
            const idx = upper.indexOf(marker);
            if (idx === -1) return;

            let raw = text.substring(idx + marker.length).trim();
            const name = cleanCopicName(raw);
            if (!name) return;
            if (name === '-' || name === '–') return;

            found.push(name);
          });

          const cleanedCurrent = cleanCopicName(currentCopicName);
          if (cleanedCurrent && cleanedCurrent !== '-' && cleanedCurrent !== '–') {
            found.push(cleanedCurrent);
          }

          const seen = new Set();
          const uniq = [];
          found.forEach(function(n){
            const key = n.replace(/\s+/g,' ').trim().toLowerCase();
            if (!key || seen.has(key)) return;
            seen.add(key);
            uniq.push(n.replace(/\s+/g,' ').trim());
          });

          if (uniq.length > 0) {
            currentCopicAgents = uniq;
            updateAllCalc();
          }
        }

        // Update panel "Detail Pembagian" dari baseAmount
        function updateDetailPembagian(baseAmount, mode){
          if (!pembagianBody || !baseLabelEl || !baseNominalEl) return;

          if (!baseAmount || !mode){
            resetPembagian();
            return;
          }

          const label = (mode === 'price_gap')
            ? 'Basis pembagian: Selisih (Harga Deal - (Harga Bidding + Balik Nama + Eksekusi + Cobroke))'
            : 'Basis pembagian: Komisi (fee)';

          baseLabelEl.textContent   = label;
          baseNominalEl.textContent = rupiah(baseAmount);

          function getSchemaRate(kode, fallback){
            for (let i=0; i<KOMISI_SCHEMA.length; i++){
              if (KOMISI_SCHEMA[i].kode === kode) return Number(KOMISI_SCHEMA[i].rate || 0);
            }
            return Number(fallback || 0);
          }

          // ===== DYNAMIC FEE TL (BERLAKU UNTUK price_gap DAN profit) =====
          let dynamicNominalMap = null;

          if (mode === 'price_gap' || mode === 'profit') {
            const base = Number(baseAmount || 0);

            const serviceRate = getSchemaRate('SERVICE', 0.10);
            const rewardRate  = getSchemaRate('REWARD', 0.03);
            const promoRate   = getSchemaRate('PROMO_FUND', 0.02);

            let serviceNom = Math.round(base * serviceRate);
            let rewardNom  = Math.round(base * rewardRate);
            let promoNom   = Math.round(base * promoRate);

            const feeBase = base * FEE_TL_RATE;
            let feeTlNom  = Math.round(Math.min(feeBase, FEE_TL_MAX));

            serviceNom = serviceNom - feeTlNom;

            if (serviceNom < 0) {
              let sisa = -serviceNom;
              serviceNom = 0;

              rewardNom = rewardNom - sisa;
              if (rewardNom < 0) {
                sisa = -rewardNom;
                rewardNom = 0;

                promoNom = promoNom - sisa;
                if (promoNom < 0) promoNom = 0;
              }
            }

            dynamicNominalMap = {
              FEE_TL:     Math.max(feeTlNom, 0),
              SERVICE:    Math.max(serviceNom, 0),
              REWARD:     Math.max(rewardNom, 0),
              PROMO_FUND: Math.max(promoNom, 0)
            };
          }
          // ======================================================

          let html = '';
          KOMISI_SCHEMA.forEach(function(item){
            const isCopic  = (item.kode === 'COPIC');
            const isKantor = KANTOR_CODES.indexOf(item.kode) !== -1;

            // ===== KHUSUS COPIC: bisa banyak agent & 0,25% dibagi rata =====
            if (isCopic) {
              let agents = currentCopicAgents && currentCopicAgents.length
                ? currentCopicAgents
                : (cleanCopicName(currentCopicName) && currentCopicName !== '-' && currentCopicName !== '–'
                    ? [cleanCopicName(currentCopicName)]
                    : []);

              if (agents.length > 0) {
                const totalRate = item.rate;
                const perRate   = totalRate / agents.length;

                const posHtml =
                  '<span class="badge bg-light text-muted border me-1">' + item.kode + '</span>' +
                  '<span>' + item.label + '</span>';

                agents.forEach(function(agentNameRaw){
                  const agentName = cleanCopicName(agentNameRaw) || '-';
                  const nominal = Math.round(baseAmount * perRate);

                  let rawPercent = perRate * 100;
                  let percentText = rawPercent.toFixed(2);
                  percentText = percentText.replace('.',',') + '%';

                  html += '<tr>' +
                    '<td class="text-start">' + posHtml + '</td>' +
                    '<td class="text-end">' + percentText + '</td>' +
                    '<td class="text-end">' + rupiah(nominal) + '</td>' +
                    '<td class="text-center small text-muted">' + agentName + '</td>' +
                  '</tr>';
                });

                return;
              }
            }
            // ===== END KHUSUS COPIC =====

            let nominal;
            let rateUsed;

            if (dynamicNominalMap && Object.prototype.hasOwnProperty.call(dynamicNominalMap, item.kode)) {
              nominal = Number(dynamicNominalMap[item.kode] || 0);
              rateUsed = baseAmount ? (nominal / Number(baseAmount || 1)) : 0;
            } else {
              nominal = Math.round(baseAmount * item.rate);
              rateUsed = Number(item.rate || 0);
            }

            const rawPercent = rateUsed * 100;
            let percentText;

            if (FIXED_TWO_DECIMAL_CODES.indexOf(item.kode) !== -1) {
              percentText = rawPercent.toFixed(2);
            } else {
              const rounded = Math.round(rawPercent * 10) / 10;
              if (Number.isInteger(rounded)) percentText = rounded.toFixed(0);
              else percentText = rounded.toString();
            }
            percentText = percentText.replace('.',',') + '%';

            // nama agent
            let agentName = '';
            if (item.kode === 'THC') {
              agentName = currentClosingAgentName || '';
            } else if (item.kode === 'UP1') {
              agentName = up1AgentName || '';
            } else if (item.kode === 'UP2') {
              agentName = up2AgentName || '';
            } else if (item.kode === 'UP3') {
              agentName = up3AgentName || '';
            } else if (item.kode === 'FEE_TL') {
              // ✅ INI SATU-SATUNYA PERUBAHAN INTI:
              // ambil nama TL dari dropdown yang dipilih
              agentName = getSelectedTeamLeaderName();
            } else {
              const agentId = KOMISI_KODE_TO_AGENT_ID[item.kode];
              if (agentId && AGENT_NAME_MAP && AGENT_NAME_MAP[agentId]) {
                agentName = AGENT_NAME_MAP[agentId];
              }
            }
            if (!agentName) {
              agentName = isKantor ? 'Kantor' : '-';
            }

            // tampilan badge Pos
            let posHtml = '';
            if (FULL_LABEL_BADGE_CODES.indexOf(item.kode) !== -1) {
              posHtml =
                '<span class="badge bg-light text-muted border me-1">' +
                  item.label +
                '</span>';
            } else {
              posHtml =
                '<span class="badge bg-light text-muted border me-1">' +
                  item.kode +
                '</span>' +
                '<span>' + item.label + '</span>';
            }

            const agentClass = isKantor ? 'text-success' : 'text-muted';

            html += '<tr>' +
              '<td class="text-start">' + posHtml + '</td>' +
              '<td class="text-end">' + percentText + '</td>' +
              '<td class="text-end">' + rupiah(nominal) + '</td>' +
              '<td class="text-center small ' + agentClass + '">' + agentName + '</td>' +
            '</tr>';
          });

          pembagianBody.innerHTML = html;
        }

        function computeSelisihDanRoyalty(){
          const hargaBidding = hargaMenangInput ? Number(onlyDigits(hargaMenangInput.value)) || 0 : 0;
          const hargaDeal    = hargaDealInput ? Number(onlyDigits(hargaDealInput.value)) || 0 : 0;
          const biayaBN      = biayaBalikNamaInput ? Number(onlyDigits(biayaBalikNamaInput.value)) || 0 : 0;
          const biayaEks     = biayaEksekusiInput ? Number(onlyDigits(biayaEksekusiInput.value)) || 0 : 0;
          const cobrokeFee   = cobrokeFeeInput ? Number(onlyDigits(cobrokeFeeInput.value)) || 0 : 0;

          const selisih = Math.max(hargaDeal - (hargaBidding + biayaBN + biayaEks + cobrokeFee), 0);

          if (selisihInput) selisihInput.value = selisih ? selisih.toLocaleString('id-ID') : '';

          if (selisihSummaryEl) selisihSummaryEl.textContent = rupiah(selisih);

          const royalty = Math.round(selisih * 0.003);
          if (royaltyFeeInput) royaltyFeeInput.value = royalty ? royalty.toLocaleString('id-ID') : '';

          return selisih;
        }

        function updateKomisiFromPercent(){
          if (!komisiEstimasi) return;
          const hargaNum = hargaMenangInput ? Number(onlyDigits(hargaMenangInput.value)) || 0 : 0;
          const persenRaw= komisiPersenInput ? (komisiPersenInput.value || '') : '';
          const persen   = parseFloat(persenRaw.replace(',','.')) || 0;

          const fee = Math.round(hargaNum * persen / 100);

          const komisiThc = Math.round(fee * THC_RATE);
          const kantor    = Math.round(fee * KANTOR_RATE);

          komisiEstimasi.textContent = rupiah(komisiThc);
          if (kotorEstimasi) kotorEstimasi.textContent = rupiah(kantor);

          updateKenaikanPercent();
          updateDetailPembagian(fee, 'profit');
        }

        function updateSelisihFromGap(selisih){
          if (!komisiEstimasi) return;

          const base = Number(selisih || 0);

          const komisiThc = Math.round(base * THC_RATE);
          const kantor    = Math.round(base * KANTOR_RATE);

          komisiEstimasi.textContent = rupiah(komisiThc);
          if (kotorEstimasi) kotorEstimasi.textContent = rupiah(kantor);

          updateKenaikanPercent();
          updateDetailPembagian(base, 'price_gap');
        }

        function updateAllCalc(){
          if (isHydratingModal) return;

          const selisihDeal = computeSelisihDanRoyalty();
          const mode = schemeInput ? schemeInput.value : 'profit';

          if (mode === 'price_gap') updateSelisihFromGap(selisihDeal);
          else updateKomisiFromPercent();
        }

        function handleHargaMenangInput(){
          if (!hargaMenangInput) return;
          const raw = onlyDigits(hargaMenangInput.value);
          if (!raw){
            hargaMenangInput.value = '';
            updateAllCalc();
            return;
          }
          const num = Number(raw);
          hargaMenangInput.value = num.toLocaleString('id-ID');
          updateAllCalc();

          if (biayaBalikNamaInput && !biayaBalikNamaManual) {
            const autoBN = Math.round(num * 0.085) + 7000000;
            biayaBalikNamaInput.value = autoBN ? autoBN.toLocaleString('id-ID') : '';
            updateAllCalc();
          }
        }

        function handleKomisiInput(){ updateAllCalc(); }

        function handleBiayaInput(e){
          const el = e.target;
          const raw = onlyDigits(el.value);
          if (!raw){ el.value = ''; return; }
          el.value = Number(raw).toLocaleString('id-ID');
        }

        function applySchemeUI(mode){
          const isPriceGap = (mode === 'price_gap');
          if (komisiWrapper)  komisiWrapper.classList.toggle('d-none', isPriceGap);
          if (selisihWrapper) selisihWrapper.classList.toggle('d-none', !isPriceGap);
          if (komisiSummaryLbl) komisiSummaryLbl.textContent = 'Komisi Agent';
          updateAllCalc();
        }

        function loadPropertyHistoryIfNeeded(){
          if (!propertyPanel || !PROPERTY_HISTORY_ROUTE) return;
          const idListing = inputId ? (inputId.value || '') : '';
          if (!idListing) return;
          if (loadedHistoryForId === idListing) return;

          loadedHistoryForId = idListing;
          propertyPanel.innerHTML = '<div class="small text-muted">Memuat riwayat properti...</div>';

          fetch(PROPERTY_HISTORY_ROUTE + '?id_listing=' + encodeURIComponent(idListing), {
            headers: { 'X-Requested-With':'XMLHttpRequest' }
          })
          .then(res => res.text())
          .then(function(html){
            propertyPanel.innerHTML = html || '<div class="small text-muted">Riwayat tidak ditemukan.</div>';
            refreshCopicAgentsFromHistory();
          })
          .catch(function(err){
            console.error('Gagal memuat history properti:', err);
            propertyPanel.innerHTML = '<div class="small text-danger">Gagal memuat riwayat properti.</div>';
          });
        }

        function activateTab(key){
          tabButtons.forEach(function(btn){
            btn.classList.toggle('tc-tab-btn-active', btn.dataset.tab === key);
          });
          tabPanels.forEach(function(panel){
            panel.classList.toggle('tc-tab-panel-active', panel.dataset.tab === key);
          });
          if (key === 'property') loadPropertyHistoryIfNeeded();
        }

        function openModal(payload){
          lastModalPayload = payload || null;
          isHydratingModal = true;

          currentHargaLimit    = Number(payload.harga_limit || 0);
          currentCopicName     = cleanCopicName(payload.copic_name || '-');
          currentCopicAgents   = [];
          loadedHistoryForId   = null;

          if (currentCopicName && currentCopicName !== '-' && currentCopicName !== '–') {
            currentCopicAgents = [currentCopicName];
          }

          try {
            const key     = String(payload.id_listing || '').trim();
            const mapData = GLOBAL_COPIC_MAP[key] || null;
            if (mapData && Array.isArray(mapData.names) && mapData.names.length > 0) {
              currentCopicAgents = mapData.names.map(cleanCopicName).filter(Boolean);
            }
          } catch (e) {
            console.error('Error baca GLOBAL_COPIC_MAP:', e);
          }

          summaryId.textContent  = 'ID : ' + (payload.id_listing || '-');
          summaryLok.textContent = 'Alamat : ' + (payload.lokasi || 'Lokasi belum tersedia');

          if (hargaLimitEl) hargaLimitEl.textContent = rupiah(currentHargaLimit);

          inputId.value  = payload.id_listing || '';
          inputTrx.value = payload.id_transaksi || '';

          if (inputStatus) {
            if (payload.status) {
              const opt = Array.from(inputStatus.options)
                .find(o => o.value.toLowerCase() === String(payload.status).toLowerCase());
              inputStatus.value = opt ? opt.value : 'Closing';
            } else inputStatus.value = 'Closing';
          }

          resetScheme();
          resetAgent();
          resetClient();
          resetCalculation();
          activateTab('transaksi');

          if (propertyPanel) {
            propertyPanel.innerHTML = '<div class="small text-muted">Klik "Informasi Properti" untuk melihat riwayat lelang.</div>';
          }

          if (copicNameEl) {
            if (currentCopicAgents && currentCopicAgents.length > 0) copicNameEl.textContent = currentCopicAgents.join(', ');
            else copicNameEl.textContent = currentCopicName || '-';
          }

          const today = new Date().toISOString().slice(0,10);
          inputTgl.value = payload.tanggal || today;

          if (photoEl) {
            let src = (payload.photo || '').trim();
            if (!src && payload.gambar) {
              const parts = String(payload.gambar).split(',').map(s => s.trim()).filter(Boolean);
              if (parts.length > 0) src = parts[0];
            }
            photoEl.setAttribute('src', src || PLACEHOLDER);
            photoEl.setAttribute('alt', 'Foto Properti ' + (payload.id_listing || ''));
          }

          const nav = document.getElementById('mainNavbar');
          if (nav) nav.style.display = 'none';

          overlay.classList.remove('d-none');
          document.body.classList.add('overflow-hidden');

          try {
            setMoneyInput(hargaDealInput,  payload.harga_deal);
            setMoneyInput(cobrokeFeeInput, payload.cobroke_fee);
            setMoneyInput(royaltyFeeInput, payload.royalty_fee);
          } catch(e) {
            console.error('Error prefill new fields:', e);
          }

          // ✅ Pastikan default TL selalu ada (kalau kosong)
          if (teamLeaderInput && (!teamLeaderInput.value || !String(teamLeaderInput.value).trim())) {
            teamLeaderInput.value = 'AG016';
          }

          isHydratingModal = false;
          updateAllCalc();
        }

        function closeModal(){
          overlay.classList.add('d-none');
          document.body.classList.remove('overflow-hidden');
          const nav = document.getElementById('mainNavbar');
          if (nav) nav.style.display = '';
        }

        closeBtn?.addEventListener('click', closeModal);
        cancelBtn?.addEventListener('click', function(e){ e.preventDefault(); closeModal(); });

        overlay.addEventListener('click', function(e){
          if (e.target === overlay) closeModal();
        });

        document.addEventListener('keydown', function(e){
          if (e.key === 'Escape' && !overlay.classList.contains('d-none')) closeModal();
        });

        window.handleTransaksiClosingClick = function(payload, btn){
          openModal(payload);
        };

        schemeOpts.forEach(function(btn){
          btn.addEventListener('click', function(e){
            e.preventDefault();
            const val  = this.dataset.value || '';
            const text = this.textContent.trim() || '';
            if (schemeInput) schemeInput.value = val;
            if (schemeLabel && text) schemeLabel.textContent = text;
            applySchemeUI(val || 'profit');
          });
        });

        agentOptions.forEach(function(btn){
          btn.addEventListener('click', function(e){
            e.preventDefault();
            const id      = this.dataset.id || '';
            const name    = this.dataset.name || '';
            const initial = (this.dataset.initial || '?').toUpperCase();

            if (agentInput) agentInput.value = id;
            if (agentLabel) agentLabel.textContent = name || 'Pilih Agent';
            if (agentAvatarBtn) agentAvatarBtn.textContent = initial;
            if (agentPrevName) agentPrevName.textContent = name || 'Belum dipilih';
            if (agentPrevAvatar) agentPrevAvatar.textContent = initial;

            currentClosingAgentName = name || '';
            currentClosingAgentId   = id || '';
            recomputeUplineNames();
            updateAllCalc();
          });
        });

        clientOptions.forEach(function(btn){
          btn.addEventListener('click', function(e){
            e.preventDefault();
            const id      = this.dataset.id || '';
            const name    = this.dataset.name || '';
            const initial = (this.dataset.initial || '?').toUpperCase();

            if (clientInput) clientInput.value = id;
            if (clientLabel) clientLabel.textContent = name || 'Pilih Client';
            if (clientAvatarBtn) clientAvatarBtn.textContent = initial;
          });
        });

        // ✅ NEW: kalau user ganti Team Leader, langsung update detail pembagian
        document.addEventListener('click', function(e){
          const opt = e.target.closest('.tc-tl-option');
          if (!opt) return;
          // diasumsikan script dropdown TL Anda akan set #tc-team-leader
          // kita paksa hitung ulang supaya baris FEE_TL refresh
          updateAllCalc();
        });

        if (hargaMenangInput) hargaMenangInput.addEventListener('input', handleHargaMenangInput);

        if (komisiPersenInput) {
          komisiPersenInput.addEventListener('input', handleKomisiInput);
          komisiPersenInput.addEventListener('change', handleKomisiInput);
        }

        if (biayaBalikNamaInput) {
          biayaBalikNamaInput.addEventListener('input', function(e){
            handleBiayaInput(e);
            biayaBalikNamaManual = true;
            updateAllCalc();
          });
          biayaBalikNamaInput.addEventListener('change', function(e){
            handleBiayaInput(e);
            updateAllCalc();
          });
        }
        if (biayaEksekusiInput) {
          biayaEksekusiInput.addEventListener('input', function(e){
            handleBiayaInput(e);
            updateAllCalc();
          });
          biayaEksekusiInput.addEventListener('change', function(e){
            handleBiayaInput(e);
            updateAllCalc();
          });
        }

        if (hargaDealInput) {
          hargaDealInput.addEventListener('input', function(e){
            handleBiayaInput(e);
            updateAllCalc();
          });
          hargaDealInput.addEventListener('change', function(e){
            handleBiayaInput(e);
            updateAllCalc();
          });
        }

        if (cobrokeFeeInput) {
          cobrokeFeeInput.addEventListener('input', function(e){
            handleBiayaInput(e);
            updateAllCalc();
          });
          cobrokeFeeInput.addEventListener('change', function(e){
            handleBiayaInput(e);
            updateAllCalc();
          });
        }

        if (royaltyFeeInput) {
          royaltyFeeInput.addEventListener('input', handleBiayaInput);
          royaltyFeeInput.addEventListener('change', handleBiayaInput);
        }

        tabButtons.forEach(function(btn){
          btn.addEventListener('click', function(){
            activateTab(this.dataset.tab || 'transaksi');
          });
        });

        window.__tcLastModalPayload = function(){
          console.log('DEBUG __tcLastModalPayload:', lastModalPayload);
          return lastModalPayload;
        };
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setup);
      } else {
        setup();
      }
    })();
    </script>














        </div>
    </div>
</div>


<div class="container-fluid px-3 mt-4">
    <div class="row">
        <!-- Grafik Transaksi (kiri) -->
        <div class="col-md-6">
            <div class="card shadow-sm rounded mb-4">
                <div class="card-header d-flex justify-content-between align-items-center text-white" style="background-color: #f4511e;">
                    <span><i class="bi bi-graph-up-arrow me-2"></i> Grafik Transaksi</span>
                    <select id="chartToggle" class="form-select form-select-sm w-auto">
                        <option value="revenue">Pendapatan</option>
                        <option value="transactions">Jumlah Transaksi</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="dashboardChart" height="160"></canvas>
                </div>
            </div>
        </div>

        <!-- Placeholder chart lain (kanan) -->
        <div class="col-md-6">
            <div class="card shadow-sm rounded mb-4 h-100">
                <div class="card-body d-flex align-items-center justify-content-center text-muted">
                    <div>
                        <h6 class="text-center mb-3">Distribusi Status Minat</h6>
                        <canvas id="statusPieChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
//property types

document.addEventListener('DOMContentLoaded', function () {
    // Toggle tombol muncul / hilang
    const cards = document.querySelectorAll('.property-card');
    cards.forEach(card => {
        card.addEventListener('click', function () {
            const btn = this.querySelector('.action-btn');
            if (btn.style.display === 'block') {
                btn.style.display = 'none';
            } else {
                document.querySelectorAll('.property-card .action-btn').forEach(otherBtn => {
                    otherBtn.style.display = 'none';
                });
                btn.style.display = 'block';
            }
        });
    });

    // Kirim AJAX saat klik tombol scrape
    const scrapeButtons = document.querySelectorAll('.scrape-btn');
    scrapeButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation(); // Supaya tidak toggle lagi
            const tipe = this.dataset.tipe;

            fetch("{{ route('property.scrape') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ tipe: tipe })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message); // atau pakai toast
                } else {
                    alert("❌ Gagal: " + data.message);
                }
            })
            .catch(err => alert("❌ Error: " + err.message));
        });
    });
});

//ini filter performance
document.addEventListener('DOMContentLoaded', function () {
    const idInput = document.getElementById('filterIdAgent');
    const namaInput = document.getElementById('filterNama');
    const statusSelect = document.getElementById('filterStatus');
    const sortListing = document.getElementById('sortListing');
    const sortPenjualan = document.getElementById('sortPenjualan');
    const sortKomisi = document.getElementById('sortKomisi');
    const tableBody = document.getElementById('performanceBody');

    // ✅ Simpan semua rows original
    const originalRows = Array.from(tableBody.querySelectorAll('tr'));

    function filterAndSortTable() {
        // Ambil ulang dari original rows setiap kali filter/sort
        let filteredRows = originalRows.slice();

        const idFilter = idInput.value.toLowerCase();
        const namaFilter = namaInput.value.toLowerCase();
        const statusFilter = statusSelect.value;

        // ✅ Filter
        filteredRows = filteredRows.filter(row => {
            const id = row.querySelector('.agent-id').textContent.toLowerCase();
            const nama = row.querySelector('.agent-nama').textContent.toLowerCase();
            const status = row.querySelector('.agent-status').textContent.trim();
            return (
                (id.includes(idFilter)) &&
                (nama.includes(namaFilter)) &&
                (statusFilter === "" || status === statusFilter)
            );
        });

        // ✅ Sort
        if (sortListing.value) {
            filteredRows.sort((a, b) => {
                const aVal = parseInt(a.querySelector('.agent-listing').textContent);
                const bVal = parseInt(b.querySelector('.agent-listing').textContent);
                return sortListing.value === 'asc' ? aVal - bVal : bVal - aVal;
            });
        } else if (sortPenjualan.value) {
            filteredRows.sort((a, b) => {
                const aVal = parseInt(a.querySelector('.agent-penjualan').textContent);
                const bVal = parseInt(b.querySelector('.agent-penjualan').textContent);
                return sortPenjualan.value === 'asc' ? aVal - bVal : bVal - aVal;
            });
        } else if (sortKomisi.value) {
            filteredRows.sort((a, b) => {
                const aVal = parseFloat(a.querySelector('.agent-komisi').dataset.komisi);
                const bVal = parseFloat(b.querySelector('.agent-komisi').dataset.komisi);
                return sortKomisi.value === 'asc' ? aVal - bVal : bVal - aVal;
            });
        }

        // ✅ Render ulang rows
        tableBody.innerHTML = '';
        filteredRows.forEach(row => tableBody.appendChild(row));
    }

    // Event listeners
    idInput.addEventListener('input', filterAndSortTable);
    namaInput.addEventListener('input', filterAndSortTable);
    statusSelect.addEventListener('change', filterAndSortTable);
    sortListing.addEventListener('change', function() {
        // Reset dropdown lain saat pilih ini
        sortPenjualan.value = "";
        sortKomisi.value = "";
        filterAndSortTable();
    });
    sortPenjualan.addEventListener('change', function() {
        sortListing.value = "";
        sortKomisi.value = "";
        filterAndSortTable();
    });
    sortKomisi.addEventListener('change', function() {
        sortListing.value = "";
        sortPenjualan.value = "";
        filterAndSortTable();
    });
});

//grafik
    const labels = {!! json_encode($labels) !!};
    const revenueData = {!! json_encode($revenue) !!};
    const transactionData = {!! json_encode($transactions) !!};

    const ctx = document.getElementById('dashboardChart').getContext('2d');
    let chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: revenueData,
                fill: false,
                borderColor: '#f15b2a',
                tension: 0.3,
                pointBackgroundColor: '#f15b2a'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => 'Rp ' + value.toLocaleString()
                    }
                }
            }
        }
    });

    document.getElementById('chartToggle').addEventListener('change', function () {
        const mode = this.value;
        if (mode === 'revenue') {
            chartInstance.data.datasets[0].label = 'Pendapatan';
            chartInstance.data.datasets[0].data = revenueData;
            chartInstance.data.datasets[0].borderColor = '#f15b2a';
            chartInstance.options.scales.y.ticks.callback = (val) => 'Rp ' + val.toLocaleString();
        } else {
            chartInstance.data.datasets[0].label = 'Jumlah Transaksi';
            chartInstance.data.datasets[0].data = transactionData;
            chartInstance.data.datasets[0].borderColor = '#0d6efd';
            chartInstance.options.scales.y.ticks.callback = (val) => val;
        }
        chartInstance.update();
    });

    // Pie Chart Data (dari controller)
    const statusData = {!! json_encode($statusCounts) !!};

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
                        label: function(context) {
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

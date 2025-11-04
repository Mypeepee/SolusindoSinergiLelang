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
      <button class="nav-link {{ $tab==='progress' ? 'active' : '' }}"
              id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress"
              type="button" role="tab" aria-controls="progress"
              aria-selected="{{ $tab==='progress' ? 'true' : 'false' }}">
        📦 Progress Lelang
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
            <!-- Sub Tabs Progress -->
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
            <div class="tab-content" id="progressSubTabsContent">
                <div class="tab-pane fade show active" id="progress-agent" role="tabpanel" aria-labelledby="progress-agent-tab">
                    <!-- Isi tabel progress agent di sini -->
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
                                                    <th>ID Agent</th>
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
                                                        <td>{{ $client->id_agent }}</td>
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

                </div>
                <div class="tab-pane fade" id="progress-register" role="tabpanel" aria-labelledby="progress-register-tab">
                    <!-- Isi tabel progress register di sini -->
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
                                        <th style="min-width: 180px;">Nama</th> <!-- ✅ Lebar untuk nama panjang -->
                                        <th style="width: 100px;">Property ID</th>
                                        <th style="min-width: 200px;">Lokasi</th> <!-- ✅ Lebih lebar -->
                                        <th style="min-width: 120px;">Harga</th>
                                        <th style="min-width: 160px;">Progess</th>
                                        <th>Status</th>
                                        <th style="min-width: 160px;">Detail</th>
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

                </div>
                <div class="tab-pane fade" id="progress-pengosongan" role="tabpanel" aria-labelledby="progress-pengosongan-tab">
                    <!-- Isi tabel progress pengosongan di sini -->
                    <div class="orders mt-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3"><i class="bi bi-truck me-2 text-warning"></i> Progress Pengosongan</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="pengosonganTable">
                                        <thead class="table-light align-middle">
                                            <tr>
                                                <th>#</th>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Property ID</th>
                                                <th>Lokasi</th>
                                                <th>Harga</th>
                                                <th>Progress</th>
                                                <th>Status</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $now = \Carbon\Carbon::now(); @endphp
                                            @forelse ($clientsPengosongan as $client)
                                                @if ($client->status === 'Balik Nama' || $client->status === 'Eksekusi Pengosongan' || $client->status === 'Selesai')
                                                    @php
                                                        $tahap = $client->status ?? 'Balik Nama'; // Ambil status dari query
                                                        $progress = match($tahap) {
                                                            'Balik Nama' => 0,
                                                            'Eksekusi Pengosongan' => 50,
                                                            'Selesai' => 100,
                                                            default => 0
                                                        };
                                                        $progressColor = match($client->status) {
                                                            'balik_nama' => 'bg-secondary',
                                                            'eksekusi_pengosongan' => 'bg-warning',
                                                            'closing' => 'bg-success',
                                                            default => 'bg-light'
                                                        };
                                                    @endphp
                                                    <tr id="row-{{ $client->id_account }}-{{ $client->id_listing }}">
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $client->id_account }}</td>
                                                        <td>{{ $client->nama }}</td>
                                                        <td>{{ $client->id_listing }}</td>
                                                        <td>{{ $client->lokasi }}</td>
                                                        <td>Rp {{ number_format($client->harga, 0, ',', '.') }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar {{ $progressColor }}" style="width: {{ $progress }}%;">
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
                                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada data pengosongan.</td>
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
            <div class="row mb-3">
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
                  <option value="Nonaktif">Nonaktif</option>
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
            </div>

            <div class="table-responsive">
              <table class="table align-middle table-hover" id="performanceTable">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>ID Agent</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Ikut Pemilu</th>
                    <th>Jumlah Listing</th>
                    <th>Jumlah Penjualan</th>
                    <th>Total Komisi</th>
                    <th>Referral Click</th>
                  </tr>
                </thead>
                <tbody id="performanceBody">
                  @forelse ($performanceAgents as $index => $agent)
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td class="agent-id">{{ $agent->id_agent }}</td>
                      <td class="agent-nama">{{ $agent->nama }}</td>
                      <td class="agent-status">
                        @if ($agent->status === 'Aktif')
                          <span class="badge bg-success">Aktif</span>
                        @else
                          <span class="badge bg-secondary">{{ $agent->status }}</span>
                        @endif
                      </td>
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
                    <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data agent.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
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

      {{-- 3-2-2-2-2 = 11 kolom; 1 kolom untuk Reset --}}
      <div class="col-12 col-lg-3 pe-lg-2">
        <label for="stoker_search" class="form-label mb-1">Cari ID Listing</label>
        <input type="text" name="search" id="stoker_search" value="{{ request('search') }}"
               class="form-control form-control-sm" placeholder="Cari ID Listing"
               inputmode="numeric" pattern="[0-9]*" autocomplete="off">
      </div>

      <div class="col-6 col-lg-2 pe-lg-2">
        <label for="stoker_property_type" class="form-label mb-1">Tipe Property</label>
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

    {{-- Kolom kecil untuk tombol Reset biar sejajar dan simetris --}}
    <div class="col-6 col-lg-1">
      <label class="form-label d-block invisible">Reset</label>
      <button type="button" id="btn-stoker-clear" class="btn reset-chip w-100">
        <span class="me-1">↺</span>Reset
      </button>
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
                  const selProv = document.getElementById('stoker_province');
                  const selCity = document.getElementById('stoker_city');
                  const selDist = document.getElementById('stoker_district');
                  const btnClear = document.getElementById('btn-stoker-clear');
                  const searchEl = document.getElementById('stoker_search');
                  const selType  = document.getElementById('stoker_property_type');

                  if (!selProv || !selCity || !selDist) return;

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

                  // Trigger AJAX tiap perubahan
                  const kick = () => { if (typeof debounced === 'function') debounced(); };
                  selProv.addEventListener('change', ()=>{ fillCities(selProv.value); kick(); });
                  selCity.addEventListener('change', ()=>{ fillDistricts(selProv.value, selCity.value); kick(); });
                  selDist.addEventListener('change', kick);

                  // Reset filter
                  btnClear?.addEventListener('click', ()=>{
                    searchEl && (searchEl.value = '');
                    selType && (selType.selectedIndex = 0);
                    selProv.selectedIndex = 0;
                    fillCities(null); // auto-disable city & district
                    kick();
                  });
                })();
              </script>
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
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    // ==== Filter refs ====
    const input   = document.getElementById('stoker_search');
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

    // ==== Param builder ====
    let t, lastReqId = 0;
    function paramsObj(merge = {}) {
      return {
        tab: 'stoker',
        search:        input?.value || '',
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

    // ==== Filter events ====
    input?.addEventListener('input', function(){
      const cleaned = this.value.replace(/[^\d]/g, '');
      if (this.value !== cleaned) this.value = cleaned;
      debounced();
    });
    [selType, selProv, selCity, selDist].forEach(el => el && el.addEventListener('change', debounced));

    // Intercept submit (biar gak full reload)
    document.getElementById('stoker-filter-form')
      ?.addEventListener('submit', function(e){ e.preventDefault(); loadList(); });

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
          <small class="text-muted" id="export-selected-counter">0 dipilih</small>
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

            <div class="d-flex gap-2 flex-wrap align-items-center">
              <button type="button" id="btn-export-csv"  class="btn btn-success btn-sm" disabled title="Pilih minimal 2 item dulu">Export CSV</button>
              <button type="button" id="btn-export-xlsx" class="btn btn-primary btn-sm" disabled title="Pilih minimal 2 item dulu">Export XLSX</button>
              <button type="button" id="btn-export-docx" class="btn btn-secondary btn-sm" disabled title="Pilih minimal 2 item dulu">Export DOCX (Template)</button>
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
          <h5 class="mb-0 fw-semibold text-primary">✅ Dipilih (<span id="export-selected-counter">0</span>)</h5>
        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form    = document.getElementById('export-filter-form');
  const input   = document.getElementById('search_exp');
  const selType = document.getElementById('property_type_exp');
  const selProv = document.getElementById('province-export');
  const selCity = document.getElementById('city-export');
  const selDist = document.getElementById('district-export');
  const btnClear = document.getElementById('btn-export-clear'); // <= tombol reset

  // HOST STABIL yang tidak pernah diganti
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

  // Pagination: delegation ke HOST STABIL
  listWrap?.addEventListener('click', function(e){
    const a = e.target.closest('a.js-export-page');
    if (!a) return;
    e.preventDefault();
    const page = a.dataset.page || '1';
    loadList({ page });
  });

  // ========== RESET / CLEAR FILTER ==========
  btnClear?.addEventListener('click', function () {
    // kosongkan semua nilai
    if (input)   input.value = '';
    if (selType) selType.value = '';
    if (selProv) selProv.value = '';

    // reset & disable dropdown turunan
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

    // reload list dengan parameter kosong
    loadList({
      search: '',
      property_type: '',
      province: '',
      city: '',
      district: '',
      page: 1
    });
  });
});
</script>


{{-- Manager seleksi khusus TAB EXPORT (di-scope ke #export-list-inner) --}}
<script>
    (function(){
      const KEY = 'exportSelectedIds';

      const container = document.getElementById('export-list-inner'); // <<< scope
      if (!container) return;

      const getSelected = () => new Set(JSON.parse(localStorage.getItem(KEY) || '[]'));
      const saveSelected = (set) => localStorage.setItem(KEY, JSON.stringify(Array.from(set)));

      // Elemen UI panel kanan
      const headerCounter = document.querySelectorAll('#export-selected-counter');
      const previewEl = document.getElementById('selected-preview');
      const exportForm = document.getElementById('export-form');
      const selectedInput = document.getElementById('selected_ids_input');
      const btnCSV  = document.getElementById('btn-export-csv');
      const btnXLSX = document.getElementById('btn-export-xlsx');
      const btnDOCX = document.getElementById('btn-export-docx');
      const formatInput = document.getElementById('export_format');
      const defaultAction = exportForm?.getAttribute('action');
      const docxAction    = "{{ route('dashboard.owner.export.docx') }}";

      // Helper yang SELALU di-scope ke kontainer Export
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
            // uncheck baris di TAB EXPORT saja
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
        const enough = getSelected().size >= 2;
        [btnCSV, btnXLSX, btnDOCX].forEach(b => { if (b) { b.disabled = !enough; b.title = enough ? '' : 'Pilih minimal 2 item dulu'; }});
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

      // Delegasi: toggle tiap baris (HANYA di kontainer export)
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

      // Delegasi: master checkbox (HANYA di kontainer export)
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

      // Submit: kirim semua ID
      if (exportForm) {
        exportForm.addEventListener('submit', function(){
          if (selectedInput) selectedInput.value = Array.from(getSelected()).join(',');
        });

        if (btnCSV)  btnCSV.addEventListener('click',  () => { if (btnCSV.disabled) return;  exportForm.setAttribute('action', defaultAction); formatInput.value='csv';  exportForm.requestSubmit(); });
        if (btnXLSX) btnXLSX.addEventListener('click', () => { if (btnXLSX.disabled) return; exportForm.setAttribute('action', defaultAction); formatInput.value='xlsx'; exportForm.requestSubmit(); });
        if (btnDOCX) btnDOCX.addEventListener('click', () => { if (btnDOCX.disabled) return; exportForm.setAttribute('action', docxAction); exportForm.requestSubmit(); setTimeout(()=>exportForm.setAttribute('action', defaultAction),0); });
      }

      // Dipanggil setelah fragment export diganti via AJAX
      window.afterExportListReplaced = function(){
        hydratePage();
        updateButtons();
        updateCounters();
        renderPreview();
      };

      // Init awal
      hydratePage();
      updateButtons();
      updateCounters();
      renderPreview();
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

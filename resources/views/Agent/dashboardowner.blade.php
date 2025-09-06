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
                <!-- 3/4 Bagian Kiri: Tabel Properti -->
                <div class="col-lg-9">
                  <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                      <h5 class="mb-0 fw-semibold text-primary">📋 Daftar Properti</h5>
                    </div>
                    <div class="card-body table-responsive">

                      <!-- Form Pencarian (kirim ke owner, tapi pertahankan tab=stoker) -->
                      <form method="GET" action="{{ route('dashboard.owner') }}" class="row g-2 p-3 rounded shadow-sm bg-white">
                        <input type="hidden" name="tab" value="stoker" />
                        {{-- ID Listing --}}
                        <div class="col-12 col-lg-3">
                          <label for="search" class="form-label d-block">Cari ID Listing</label>
                          <input type="text" name="search" id="search" value="{{ request('search') }}"
                                 class="form-control form-control-sm" placeholder="Cari ID Listing">
                        </div>

                        {{-- Tipe Property --}}
                        <div class="col-6 col-lg-2">
                          <label for="property_type" class="form-label d-block">Tipe Property</label>
                          <select name="property_type" id="property_type" class="form-select form-select-sm">
                            <option value="" {{ request('property_type') ? '' : 'selected' }} disabled>Tipe Property</option>
                            <option value="rumah" @selected(request('property_type') === 'rumah')>Rumah</option>
                            <option value="gudang" @selected(request('property_type') === 'gudang')>Gudang</option>
                            <option value="apartemen" @selected(request('property_type') === 'apartemen')>Apartemen</option>
                            <option value="tanah" @selected(request('property_type') === 'tanah')>Tanah</option>
                            <option value="pabrik" @selected(request('property_type') === 'pabrik')>Pabrik</option>
                            <option value="hotel dan villa" @selected(request('property_type') === 'hotel dan villa')>Hotel dan Villa</option>
                            <option value="ruko" @selected(request('property_type') === 'ruko')>Ruko</option>
                            <option value="toko" @selected(request('property_type') === 'toko')>Toko</option>
                            <option value="lain-lain" @selected(request('property_type') === 'lain-lain')>Lainnya</option>
                          </select>
                        </div>

                        {{-- Provinsi --}}
                        <div class="col-6 col-lg-2">
                          <label for="province-desktop" class="form-label d-block">Pilih Provinsi</label>
                          <select id="province-desktop" name="province" class="form-select form-select-sm">
                            <option disabled {{ request('province') ? '' : 'selected' }}>Pilih Provinsi</option>
                          </select>
                        </div>

                        {{-- Kota/Kabupaten --}}
                        <div class="col-6 col-lg-2">
                          <label for="city-desktop" class="form-label d-block">Pilih Kota/Kabupaten</label>
                          <select id="city-desktop" name="city" class="form-select form-select-sm" {{ request('province') ? '' : 'disabled' }}>
                            <option disabled selected>Pilih Kota/Kabupaten</option>
                          </select>
                        </div>

                        {{-- Kecamatan --}}
                        <div class="col-6 col-lg-2">
                          <label for="district-desktop" class="form-label d-block">Pilih Kecamatan</label>
                          <select id="district-desktop" name="district" class="form-select form-select-sm" {{ request('city') ? '' : 'disabled' }}>
                            <option disabled selected>Pilih Kecamatan</option>
                          </select>
                        </div>

                        {{-- Tombol --}}
                        <div class="col-12 col-lg-1 d-grid">
                          <button type="submit" class="btn btn-dark btn-sm">Search</button>
                        </div>
                      </form>

                      {{-- Script lokasi (biarkan sesuai punyamu) --}}
                      <script>
                        document.addEventListener('DOMContentLoaded', function () {
                          const provinceDesktop = document.getElementById('province-desktop');
                          const cityDesktop     = document.getElementById('city-desktop');
                          const districtDesktop = document.getElementById('district-desktop');

                          const provinceMap = new Map(); // Provinsi => Set Kota
                          const locationMap = new Map(); // Provinsi => (Kota => Set Kecamatan)

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

                              // Restore dari query string (opsional)
                              const params = new URLSearchParams(location.search);
                              const savedProv = params.get('province');
                              const savedCity = params.get('city');
                              const savedDist = params.get('district');

                              if (savedProv && provinceMap.has(savedProv)) {
                                provinceDesktop.value = savedProv;
                                updateCityDropdown(savedProv, cityDesktop);
                                if (savedCity && provinceMap.get(savedProv).has(savedCity)) {
                                  cityDesktop.value = savedCity;
                                  cityDesktop.disabled = false;
                                  updateDistrictDropdown(savedProv, savedCity);
                                  if (savedDist) {
                                    districtDesktop.value = savedDist;
                                    districtDesktop.disabled = false;
                                  }
                                }
                              }
                            })
                            .catch(err => {
                              console.error('Gagal load indonesia.json:', err);
                            });

                          // Events
                          provinceDesktop.addEventListener('change', function () {
                            updateCityDropdown(this.value, cityDesktop);
                          });

                          cityDesktop.addEventListener('change', function () {
                            updateDistrictDropdown(provinceDesktop.value, this.value);
                          });

                          // Helpers
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
                        }); // <-- ini yang hilang tadi
                        </script>

                      <!-- Tabel Properti -->
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
                          @forelse($stokerProperties as $property)
                            <tr>
                              <td>{{ $property->id_listing }}</td>
                              <td>{{ $property->lokasi }}</td>
                              <td>{{ $property->luas ?? '-' }}</td>
                              <td>Rp {{ number_format($property->harga, 0, ',', '.') }}</td>
                              <td>
                                @php
                                  $fotoArray = explode(',', $property->gambar);
                                  $fotoUtama = trim($fotoArray[0] ?? 'default.jpg');
                                @endphp
                                <img src="{{ $fotoUtama }}" alt="Foto Properti"
                                     class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                              </td>
                              <td>
                                <form action="{{ route('listing.deletes', $property->id_listing) }}" method="POST" onsubmit="return confirm('Tandai listing ini sebagai Terjual?');">
                                    @csrf
                                    <input type="hidden" name="redirect" value="{{ request()->fullUrlWithQuery(['tab' => 'stoker']) }}">
                                    <button type="submit" class="btn btn-warning btn-sm" {{ $property->status === 'Terjual' ? 'disabled' : '' }}>
                                      Tandai Terjual
                                    </button>
                                  </form>
                              </td>
                            </tr>
                          @empty
                            <tr>
                              <td colspan="6" class="text-center">Tidak ada data ditemukan.</td>
                            </tr>
                          @endforelse
                          </tbody>
                        </table>
                      </div>

                      <!-- Pagination -->
                      <div class="col-12">
                        <div class="pagination d-flex justify-content-center mt-4 gap-1 overflow-auto">
                          @php
                            $currentPage = $stokerProperties->currentPage();
                            $lastPage = $stokerProperties->lastPage();
                            $start = max($currentPage - 2, 1);
                            $end = min($currentPage + 2, $lastPage);
                          @endphp

                          {{-- Previous --}}
                          @if ($stokerProperties->onFirstPage())
                            <a class="btn btn-sm btn-light rounded disabled">&laquo;</a>
                          @else
                            <a href="{{ $stokerProperties->appends(request()->query())->previousPageUrl() }}" class="btn btn-sm btn-light rounded">&laquo;</a>
                          @endif

                          {{-- Pages --}}
                          @if ($start > 1)
                            <a href="{{ $stokerProperties->appends(request()->query())->url(1) }}" class="btn btn-sm btn-light rounded">1</a>
                            @if ($start > 2)
                              <span class="btn btn-sm btn-light rounded disabled">...</span>
                            @endif
                          @endif

                          @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $stokerProperties->appends(request()->query())->url($i) }}"
                               class="btn btn-sm rounded {{ $i === $currentPage ? 'btn-primary text-white' : 'btn-light' }}">
                              {{ $i }}
                            </a>
                          @endfor

                          @if ($end < $lastPage)
                            @if ($end < $lastPage - 1)
                              <span class="btn btn-sm btn-light rounded disabled">...</span>
                            @endif
                            <a href="{{ $stokerProperties->appends(request()->query())->url($lastPage) }}" class="btn btn-sm btn-light rounded">{{ $lastPage }}</a>
                          @endif

                          {{-- Next --}}
                          @if ($stokerProperties->hasMorePages())
                            <a href="{{ $stokerProperties->appends(request()->query())->nextPageUrl() }}" class="btn btn-sm btn-light rounded">&raquo;</a>
                          @else
                            <a class="btn btn-sm btn-light rounded disabled">&raquo;</a>
                          @endif
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- 1/4 Bagian Kanan: Riwayat -->
                <div class="col-lg-3">
                    <!-- Riwayat properti, untuk dikembangkan lebih lanjut -->
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
            </div> {{-- /row --}}

        </div> {{-- /tab-pane stoker --}}

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
                <button class="btn btn-secondary btn-sm" id="btnBack">Kembali</button>
            </div>
        `;

        document.getElementById('btnBack').addEventListener('click', ()=> renderTodayEvents(selectedDate));

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

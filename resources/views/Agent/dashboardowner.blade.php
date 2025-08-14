@include('template.header')

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

    <!-- Tabs Utama -->
    <ul class="nav nav-tabs" id="mainTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="verifikasi-tab" data-bs-toggle="tab" data-bs-target="#verifikasi" type="button" role="tab" aria-controls="verifikasi" aria-selected="true">
                ✅ Verifikasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress" type="button" role="tab" aria-controls="progress" aria-selected="false">
                📦 Progress Lelang
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="performance-tab" data-bs-toggle="tab" data-bs-target="#performance" type="button" role="tab" aria-controls="performance" aria-selected="false">
                📈 Performance
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" role="tab" aria-controls="calendar" aria-selected="false">
                🗓️ Calendar
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="mainTabsContent">
        <!-- Verifikasi -->
        <div class="tab-pane fade show active" id="verifikasi" role="tabpanel" aria-labelledby="verifikasi-tab">
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
        <div class="tab-pane fade" id="progress" role="tabpanel" aria-labelledby="progress-tab">
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
        <div class="tab-pane fade" id="performance" role="tabpanel" aria-labelledby="performance-tab">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-primary">📊 Performance Agent</h5>
                </div>

                <div class="card-body">
                    <!-- Filter Controls -->
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

                        <!-- 3 Dropdown Sort -->
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

                    <!-- Tabel -->
                    <div class="table-responsive">
                        <table class="table align-middle table-hover" id="performanceTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>ID Agent</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th>Jumlah Listing</th>
                                    <th>Jumlah Penjualan</th>
                                    <th>Total Komisi</th>
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
                                    <td class="agent-listing">{{ $agent->jumlah_listing }}</td>
                                    <td class="agent-penjualan">{{ $agent->jumlah_penjualan }}</td>
                                    <td class="agent-komisi" data-komisi="{{ $agent->total_komisi ?? 0 }}">
                                        Rp {{ number_format($agent->total_komisi ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada data agent tersedia saat ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                        <div class="col-lg-4">
                            <div class="upcoming card border-0 shadow-sm h-100">
                                <div class="card-header bg-white py-3">
                                    <h6 class="mb-0 fw-semibold">📅 Event 7 Hari Ke Depan</h6>
                                </div>
                                <div id="upcomingList" class="list-group list-group-flush small" style="max-height: 520px; overflow:auto;">
                                    <!-- Diisi via JS -->
                                </div>
                                <div class="p-3 border-top text-muted small">
                                    <span id="rangeInfo"></span>
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
            </style>

            <script>
                (function(){
                    const events = @json($events ?? []);

                    // ====== Elemen ======
                    const titleEl = document.getElementById('calTitle');
                    const gridEl  = document.getElementById('calendarGrid');
                    const btnPrev = document.getElementById('calPrev');
                    const btnNext = document.getElementById('calNext');
                    const btnToday= document.getElementById('calToday');
                    const upList  = document.getElementById('upcomingList');
                    const rangeInfo = document.getElementById('rangeInfo');

                    // ====== State Kalender ======
                    const now = new Date();
                    let viewYear  = now.getFullYear();
                    let viewMonth = now.getMonth(); // 0-11
                    const MONTHS_ID = ['January','February','March','April','May','June','July','August','September','October','November','December'];

                    // ====== Utils ======
                    function toDate(val){
                        if (!val) return null;
                        return (val instanceof Date) ? val : new Date(val);
                    }
                    function startOfDay(d){ const x = new Date(d); x.setHours(0,0,0,0); return x; }
                    function endOfDay(d){ const x = new Date(d); x.setHours(23,59,59,999); return x; }
                    function daysInMonth(y, m){ return new Date(y, m+1, 0).getDate(); }
                    function startWeekday(y, m){ return new Date(y, m, 1).getDay(); } // 0=Sun
                    function fmtDate(d){
                        return d.toLocaleString('id-ID', { weekday:'short', day:'2-digit', month:'short' });
                    }
                    function fmtTime(d){
                        return d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', hour12:false });
                    }
                    function isSameDay(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }

                    // ====== Render Kalender ======
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
                            const isToday = isSameDay(c.date, new Date());
                            if (isToday && !c.other) cell.classList.add('today');

                            const num = document.createElement('div');
                            num.className = 'calendar-lite__date';
                            num.textContent = c.num;
                            cell.appendChild(num);

                            const evts = document.createElement('div');
                            evts.className = 'events';

                            // ambil event dalam hari ini (00:00-23:59)
                            const dayStart = startOfDay(c.date);
                            const dayEnd   = endOfDay(c.date);
                            const todays = events.filter(e=>{
                                const s = toDate(e.start);
                                const eEnd = toDate(e.end) || s;
                                return (s <= dayEnd && eEnd >= dayStart); // overlap dengan hari tsb
                            });

                            todays.slice(0,2).forEach(e=>{
                                const b = document.createElement('span');
                                b.className = 'calendar-lite__badge';
                                if (e.allDay) {
                                    b.textContent = e.title;
                                } else {
                                    b.textContent = `${fmtTime(toDate(e.start))} · ${e.title}`;
                                }
                                evts.appendChild(b);
                            });
                            if (todays.length > 2){
                                const more = document.createElement('span');
                                more.className = 'calendar-lite__badge';
                                more.textContent = `+${todays.length - 2} lagi`;
                                evts.appendChild(more);
                            }

                            cell.appendChild(evts);
                            gridEl.appendChild(cell);
                        });
                    }

                    // ====== Render Sidebar 7 Hari Ke Depan ======
                    function renderUpcoming(){
                        const today = startOfDay(new Date());
                        const cutoff = startOfDay(new Date(today)); cutoff.setDate(cutoff.getDate()+7);

                        rangeInfo.textContent = `${fmtDate(today)} – ${fmtDate(new Date(cutoff.getTime()-86400000))}`;

                        // event yang overlap dalam rentang [today, cutoff)
                        const upcoming = events
                            .map(ev => {
                                const s = toDate(ev.start);
                                const e = toDate(ev.end) || s;
                                return { ...ev, _s: s, _e: e };
                            })
                            .filter(ev => ev._s < cutoff && ev._e >= today)
                            .sort((a,b)=> a._s - b._s)
                            .slice(0, 20); // batasi agar list tidak kepanjangan

                        upList.innerHTML = '';
                        if (upcoming.length === 0){
                            upList.innerHTML = `<div class="list-group-item text-muted">Tidak ada event dalam 7 hari ke depan.</div>`;
                            return;
                        }

                        upcoming.forEach(ev=>{
                            const s = ev._s, e = ev._e;
                            const li = document.createElement('div');
                            li.className = 'list-group-item upcoming-item';

                            const badge = document.createElement('div');
                            badge.className = 'up-date';
                            badge.innerHTML = `<div class="d">${String(s.getDate()).padStart(2,'0')}</div><div class="m">${MONTHS_ID[s.getMonth()]}</div>`;

                            const body = document.createElement('div');
                            body.className = 'up-body';
                            const ttl = document.createElement('div');
                            ttl.className = 'ttl';
                            ttl.textContent = ev.title;

                            const meta = document.createElement('div');
                            meta.className = 'meta';
                            if (ev.allDay){
                                meta.textContent = `All day • ${fmtDate(s)}` + (ev.location ? ` • ${ev.location}` : '');
                            } else {
                                const timePart = (isSameDay(s,e) || !e) ? `${fmtTime(s)}` : `${fmtTime(s)}–${fmtTime(e)}`;
                                meta.textContent = `${fmtDate(s)} • ${timePart}` + (ev.location ? ` • ${ev.location}` : '');
                            }

                            body.appendChild(ttl);
                            body.appendChild(meta);
                            li.appendChild(badge);
                            li.appendChild(body);
                            upList.appendChild(li);
                        });
                    }

                    // ====== Navigasi Bulan ======
                    function goPrev(){ viewMonth--; if (viewMonth < 0){ viewMonth = 11; viewYear--; } renderCalendar(); }
                    function goNext(){ viewMonth++; if (viewMonth > 11){ viewMonth = 0; viewYear++; } renderCalendar(); }
                    function goToday(){ const n = new Date(); viewYear = n.getFullYear(); viewMonth = n.getMonth(); renderCalendar(); }

                    document.getElementById('calPrev').addEventListener('click', goPrev);
                    document.getElementById('calNext').addEventListener('click', goNext);
                    document.getElementById('calToday').addEventListener('click', goToday);

                    // pertama kali render
                    renderCalendar();
                    renderUpcoming();

                    // ====== OPTIONAL: jika kamu nanti load events dari server via AJAX,
                    // panggil renderCalendar() dan renderUpcoming() ulang setelah data masuk.
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

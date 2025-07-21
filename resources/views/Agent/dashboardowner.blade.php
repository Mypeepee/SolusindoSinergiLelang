@include('template.header')

<div class="container-fluid">
    <h1 class="fw-bold mb-4">📊 Dashboard Owner</h1>

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
                                        @forelse ($pendingClients as $index => $client)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><span class="badge bg-light text-dark">{{ $client->id_account }}</span></td>
                                                <td>{{ $client->nama }}</td>
                                                <td>{{ $client->email }}</td>
                                                <td>
                                                    <a href="{{ asset($client->gambar_ktp) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                        <i class="fa fa-id-card me-1"></i> Lihat KTP
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ asset($client->gambar_npwp) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                                        <i class="fa fa-file-invoice me-1"></i> Lihat NPWP
                                                    </a>
                                                </td>
                                                <td style="min-width: 230px;">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <!-- Form Verifikasi -->
                                                        <form action="{{ route('verify.client', $client->id_account) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                                <i class="fa fa-check me-1"></i> Verifikasi
                                                            </button>
                                                        </form>

                                                        <!-- Tombol Tolak -->
                                                        <form action="{{ route('reject.client', $client->id_account) }}" method="POST">
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
                                                        <a href="https://wa.me/62{{ ltrim($agent->nomor_telepon, '0') }}?text={{ urlencode('Maaf, pendaftaran Anda sebagai agent ditolak. Silakan lengkapi data dan ajukan ulang.') }}"
                                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm"
                                                        target="_blank">
                                                            <i class="fa fa-times me-1"></i> Tolak
                                                        </a>
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
                </div>
                <div class="tab-pane fade" id="progress-register" role="tabpanel" aria-labelledby="progress-register-tab">
                    <!-- Isi tabel progress register di sini -->
                </div>
                <div class="tab-pane fade" id="progress-pengosongan" role="tabpanel" aria-labelledby="progress-pengosongan-tab">
                    <!-- Isi tabel progress pengosongan di sini -->
                </div>
            </div>
        </div>

        <!-- Performance -->
        <div class="tab-pane fade" id="performance" role="tabpanel" aria-labelledby="performance-tab">
            <!-- Isi tabel performance agent di sini -->
        </div>
    </div>
</div>

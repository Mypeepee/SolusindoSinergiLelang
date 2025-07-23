@include('template.header')

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
                                                    <th>Progres</th>
                                                    <th>Aksi</th>
                                                    <th>Download KTP</th>
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

                                                        <td style="min-width: 220px;">
                                                            @if ($status === 'Gagal')
                                                            <span class="badge bg-danger">GAGAL</span>

                                                            @elseif ($status === 'Pending')
                                                            @php
                                                                $pesan = urlencode("Halo {$client->nama}, saya ingin memastikan apakah ada informasi yang bisa saya bantu terkait rumah di {$client->lokasi}?");

                                                            @endphp
                                                            <a href="https://wa.me/+62{{ $client->nomor_telepon }}?text={{ $pesan }}"
                                                            class="btn btn-sm btn-success mb-1"
                                                            target="_blank"
                                                            onclick="
                                                                    // Jalankan update status di background
                                                                    updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'FollowUp');">
                                                                FollowUp
                                                            </a>

                                                            @elseif (!$status || $status === null)
                                                            <a href="https://wa.me/{{ $client->nomor_telepon }}"
                                                            class="btn btn-sm btn-success mb-1" target="_blank"
                                                            onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'FollowUp')">
                                                                WA
                                                            </a>

                                                            @elseif ($status === 'FollowUp')
                                                            @php
                                                                $alamatProperty = $client->lokasi;
                                                                $pesan = urlencode(
                                                                    "📅 Reminder Buyer Meeting\n" .
                                                                    "Obyek: {$alamatProperty}\n" .
                                                                    "Hari: Senin\n" . // agent nanti ubah manual
                                                                    "Tanggal: ".date('Y-m-d')."\n" .
                                                                    "Pukul: 13:00\n\n" . // agent nanti ubah manual
                                                                    "📍 Lokasi: Solitaire Property\n" .
                                                                    "Justicia Law Firm\n" .
                                                                    "Kantor Pemasaran dan Layanan Hukum\n" .
                                                                    "Santorini Town Square\n" .
                                                                    "Jl. Ronggolawe No.2A, DR. Soetomo\n" .
                                                                    "Kec. Tegalsari, Surabaya, Jawa Timur 60160\n\n" .
                                                                    "🌐 GMAP: https://maps.app.goo.gl/6gR4s3xDtEaeEya26?g_st=awb"
                                                                );
                                                            @endphp

                                                            <a href="https://wa.me/+62{{ $client->nomor_telepon }}?text={{ $pesan }}"
                                                            class="btn btn-sm btn-primary mb-1"
                                                            target="_blank"
                                                            onclick="
                                                                // Jalankan update status di background
                                                                updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'BuyerMeeting');
                                                            ">
                                                                Buyer Meeting
                                                            </a>

                                                            <button class="btn btn-sm btn-outline-danger mb-1"
                                                                onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'Gagal')">
                                                                Batal
                                                            </button>

                                                            @elseif ($status === 'BuyerMeeting')
                                                            <a href="{{ route('closing.show', ['id_listing' => $client->id_listing, 'id_klien' => $client->id_account]) }}"
                                                                class="btn btn-sm btn-success mb-1">
                                                                 Closing
                                                             </a>
                                                            <button class="btn btn-sm btn-warning mb-1"
                                                                onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'Pending')">
                                                                Pending
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger mb-1"
                                                                onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'Gagal')">
                                                                Cancel
                                                            </button>
                                                            @else
                                                                <span class="badge bg-success text-uppercase">{{ $status }}</span>
                                                            @endif

                                                            @if ($progress === 100 && $status !== 'Pending')
                                                                <button class="btn btn-sm btn-outline-secondary mt-1"
                                                                    onclick="hideRow('{{ $client->id_account }}', '{{ $client->id_listing }}')">
                                                                    Hide
                                                                </button>
                                                            @endif
                                                        </td>

                                                        <td style="min-width: 150px;">
                                                            @if ($client->gambar_ktp)
                                                            <a href="{{ asset('storage/ktp/'.$client->gambar_ktp) }}"
                                                                class="btn btn-sm btn-outline-info"
                                                                download="{{ $client->gambar_ktp }}">
                                                                Download KTP
                                                            </a>

                                                            @else
                                                                <span class="text-muted">Belum Ada</span>
                                                            @endif
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
                    <th style="min-width: 160px;">Proges</th>
                    <th style="min-width: 160px;">Aksi</th> <!-- ✅ Lebih lebar -->
                    <th style="min-width: 160px;">Surat Kuasa</th> <!-- ✅ Lebih lebar -->
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

                    <td>
                        @if ($tahap === 'Closing')
                            <button class="btn btn-sm btn-primary"
                                onclick="handleInputKuitansi('{{ $client->id_account }}', '{{ $client->id_listing }}')">
                                Input Kuitansi
                            </button>
                        @elseif ($tahap === 'Kuitansi')
                            <button class="btn btn-sm btn-warning"
                                onclick="updateProgress('{{ $client->id_account }}', '{{ $client->id_listing }}', 'Kode Billing')">
                                Input Kode Billing
                            </button>
                        @elseif ($tahap === 'Kode Billing')
                            <button class="btn btn-sm btn-secondary"
                                onclick="handleInputRisalahLelang('{{ $client->id_account }}', '{{ $client->id_listing }}')">
                                Input Risalah Lelang
                            </button>
                        @elseif ($tahap === 'Kutipan Risalah Lelang')
                            <button class="btn btn-sm btn-info"
                                onclick="updateProgress('{{ $client->id_account }}', '{{ $client->id_listing }}', 'Akte Grosse')">
                                Grosse
                            </button>
                        @elseif ($tahap === 'Akte Grosse')
                            <button class="btn btn-sm btn-success"
                                onclick="updateProgress('{{ $client->id_account }}', '{{ $client->id_listing }}', 'Balik Nama')">
                                Balik Nama
                            </button>
                        @elseif ($tahap === 'Balik Nama')
                            <span class="text-muted">Selesai</span>
                        @else
                            <span class="text-muted">Selesai</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('download.suratkuasa', ['id' => $client->id_account, 'listing' => $client->id_listing]) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-download"></i>
                        </a>
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



        @if (session('role') === 'Owner')
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-primary">👥 Permintaan Pendaftaran Agent</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table align-middle table-hover" id="agentTable">
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
        @endif

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
                <!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row mb-4"> <!-- ✅ Tambahkan mb-4 di sini -->
    <!-- Yearly Sales Line Chart -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Yearly Sales</h4>
                    <select id="yearSelector" class="form-select w-auto" onchange="updateChart()">
                        @foreach (array_keys(json_decode($salesData, true)) as $year)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            <div style="height: 300px;">
                <canvas id="sales-chart"></canvas>
            </div>
        </div>
    </div>
</div>

    <!-- Client Status Pie Chart -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h4 class="mb-3">Client Status</h4>
                <div style="height: 300px;">
                    <canvas id="status-piechart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // === SALES LINE CHART ===
    const salesData = {!! $salesData !!};
    const defaultYear = Object.keys(salesData)[0] ?? new Date().getFullYear();

    const ctx = document.getElementById('sales-chart').getContext('2d');
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
                    grid: { drawBorder: true, color: '#ccc' }
                },
                x: {
                    grid: { drawBorder: true, color: '#eee' }
                }
            },
            plugins: {
                tooltip: { enabled: true },
                legend: { display: true }
            }
        }
    });

    function updateChart() {
        const selectedYear = document.getElementById("yearSelector").value;
        const newData = salesData[selectedYear] ?? Array(12).fill(0);
        chart.data.datasets[0].data = newData;
        chart.update();
    }

    // === CLIENT STATUS PIE CHART ===
    const statusCounts = {!! json_encode($statusCounts) !!};

    const pieLabels = ['Follow Up', 'Pending', 'Buyer Meeting', 'Gagal', 'Closing'];
    const pieData = [
        statusCounts.followup ?? 0,
        statusCounts.pending ?? 0,
        statusCounts.buyer_meeting ?? 0,
        statusCounts.gagal ?? 0,
        statusCounts.closing ?? 0
    ];

    // Cek kalau semua data nol
    const total = pieData.reduce((a, b) => a + b, 0);
    const finalData = total === 0 ? [1] : pieData;
    const finalLabels = total === 0 ? ['No Data'] : pieLabels;
    const finalColors = total === 0
        ? ['#ddd']
        : ['#ffc107', '#17a2b8', '#6f42c1', '#dc3545', '#28a745']; // ungu untuk buyer meeting

    const ctxPie = document.getElementById('status-piechart').getContext('2d');
    const clientStatusChart = new Chart(ctxPie, {
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

@if (session('role') === 'Pengosongan')
<div class="orders mt-5">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="box-title">Pengosongan Property</h4>
                </div>
                <div class="card-body--">
                    <div class="table-stats order-table ov-h">
                        <table class="table" id="pengosonganTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Property ID</th>
                                    <th>Lokasi</th>
                                    <th>Harga</th>
                                    <th>Progress</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $now = \Carbon\Carbon::now(); @endphp
                                @forelse ($clientsPengosongan as $client)
                                    @if ($client->status === 'balik_nama' || $client->status === 'eksekusi_pengosongan')
                                        @php
                                            $daysLeft = null;
                                            $start = \Carbon\Carbon::parse($client->updated_at);
                                            $end = $start->copy()->addDays(7);
                                            $totalDays = $start->diffInDays($end);
                                            $remainingDays = max(0, $now->diffInDays($end, false));
                                            $progress = 100 - (($remainingDays / $totalDays) * 100);
                                        @endphp
                                        <tr id="row-{{ $client->id_account }}-{{ $client->id_listing }}">
                                            <td class="serial">•</td>
                                            <td>{{ $client->id_account }}</td>
                                            <td>{{ $client->nama }}</td>
                                            <td>{{ $client->id_listing }}</td>
                                            <td>{{ $client->lokasi }}</td>
                                            <td>Rp {{ number_format($client->harga, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $status = $client->status;
                                                    $progress = 0;
                                                    $progressColor = 'bg-secondary';

                                                    if ($status === 'balik_nama') {
                                                        $progress = 25;
                                                        $progressColor = 'bg-secondary';
                                                    } elseif ($status === 'eksekusi_pengosongan') {
                                                        $progress = 50;
                                                        $progressColor = 'bg-warning';
                                                    } elseif ($status === 'closing') {
                                                        $progress = 100;
                                                        $progressColor = 'bg-success';
                                                    }
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar {{ $progressColor }}" style="width: {{ $progress }}%;">
                                                        {{ $progress }}%
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="align-middle">
                                                @if ($client->status === 'balik_nama')
                                                    <button class="btn btn-sm btn-danger"
                                                            onclick="startEksekusi('{{ $client->id_account }}', '{{ $client->id_listing }}', this)">
                                                        Eksekusi Pengosongan
                                                    </button>
                                                    @elseif ($client->status === 'eksekusi_pengosongan')
                                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                                        <button class="badge bg-info text-dark flex-grow-1 text-start px-2 py-2 border-0"
                                                                onclick="openCatatanModal('{{ $client->id_account }}', '{{ $client->id_listing }}', `{{ $client->catatan ?? 'Belum ada catatan' }}`)">
                                                            Catatan: {{ Str::limit($client->catatan ?? 'Klik untuk tambah catatan', 30) }}
                                                        </button>
                                                        <button class="btn btn-sm btn-success"
            onclick="selesaikan('{{ $client->id_account }}', '{{ $client->id_listing }}', this)">
            Selesai
        </button>
                                                    </div>
                                                @endif
                                            </td>


                                        </tr>
                                    @endif
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data pengosongan.</td>
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

@endif
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


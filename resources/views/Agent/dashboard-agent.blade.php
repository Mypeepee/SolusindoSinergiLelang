@include('template.header')
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


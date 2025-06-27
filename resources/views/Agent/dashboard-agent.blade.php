@extends('layouts.admin')

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Solusindo Agent Admin">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">

    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/pe-icon-7-stroke.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/weather-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">
<!-- DataTables CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

   <style>
    #weatherWidget .currentDesc {
        color: #ffffff!important;
    }
        .traffic-chart {
            min-height: 335px;
        }
        #flotPie1  {
            height: 150px;
        }
        #flotPie1 td {
            padding:3px;
        }
        #flotPie1 table {
            top: 20px!important;
            right: -10px!important;
        }
        .chart-container {
            display: table;
            min-width: 270px ;
            text-align: left;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        #flotLine5  {
             height: 105px;
        }

        #flotBarChart {
            height: 150px;
        }
        #cellPaiChart{
            height: 160px;
        }

    </style>
</head>

<body>


    <!-- Right Panel -->
    <div id="right-panel" class="container-fluid">
        <!-- Header-->
        <header id="header" class="header d-flex justify-content-between align-items-center px-4 py-3 shadow-sm bg-white">
            {{-- Kiri: Logo & Welcome --}}
            <div class="d-flex align-items-center gap-3">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('img/Logo.png') }}" alt="Logo" style="height: 45px;">
                </a>

                <!-- Welcome Message -->
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-semibold text-dark">Selamat datang, <span class="text-primary">{{ session('username') }}</span> 👋</h5>
                    <small class="text-muted">Semoga harimu menyenangkan!</small>
                </div>
            </div>

            {{-- Tengah (Optional): Search Bar --}}
            {{-- <form class="d-none d-md-block w-50 mx-3">
                <input type="search" class="form-control" placeholder="Cari properti, klien, dll..." />
            </form> --}}

            {{-- Kanan: Notifikasi + Profil --}}
            <div class="d-flex align-items-center gap-4">

                <!-- Notifikasi -->
                <div class="dropdown position-relative">
                    <button class="btn btn-light position-relative p-2" id="notifDropdown" data-bs-toggle="dropdown">
                        <i class="fa fa-bell fa-lg text-secondary"></i>
                        @if (($newClientsCount ?? 0) > 0)
                            <span id="clientCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $newClientsCount }}
                            </span>
                        @endif
                    </button>
                    <div id="clientDropdown" class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="min-width: 280px;">
                        <p class="dropdown-item fw-bold mb-2">Client Baru</p>
                        <div class="overflow-auto" style="max-height: 300px;">
                            @forelse ($newClients ?? [] as $client)
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ url('client/detail/'.$client->id) }}">
                                    <span>{{ $client->nama }}</span>
                                    <small class="text-muted">{{ $client->created_at->diffForHumans() }}</small>
                                </a>
                            @empty
                                <span class="dropdown-item text-muted">Tidak ada client baru</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Profil Agent -->
                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(session('username')) }}&background=0D8ABC&color=fff&size=128&rounded=true"
                            alt="Profile"
                            class="rounded-circle"
                            style="width: 36px; height: 36px; object-fit: cover;">
                        <span class="d-none d-md-inline text-dark">{{ session('username') }}</span>
                        <i class="fa fa-chevron-down small"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-2 shadow-sm">
                        <a class="dropdown-item" href="{{ url('/profile') }}"><i class="fa fa-user me-2"></i>Profil</a>
                        <a class="dropdown-item" href="{{ url('/settings') }}"><i class="fa fa-cog me-2"></i>Pengaturan</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{ url('/logout') }}"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
                    </div>
                </div>
            </div>

            <!-- Notif Script -->
            <script>
                setInterval(function() {
                    $.ajax({
                        url: "{{ url('/agent/new-clients-json') }}",
                        method: 'GET',
                        success: function(response) {
                            $('#clientCount').text(response.count);
                            let dropdown = $('#clientDropdown');
                            dropdown.empty();
                            dropdown.append('<p class="dropdown-item fw-bold">Client Baru</p>');
                            if (response.clients.length > 0) {
                                response.clients.slice(0, 5).forEach(function(client) {
                                    dropdown.append(`
                                        <a class="dropdown-item d-flex justify-content-between align-items-center"
                                           href="/client/detail/${client.id}">
                                            <span>${client.nama}</span>
                                            <small class="text-muted">${client.created_at}</small>
                                        </a>`);
                                });
                            } else {
                                dropdown.append('<span class="dropdown-item text-muted">Tidak ada client baru</span>');
                            }
                        }
                    });
                }, 10000);
            </script>
        </header>

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
                    <th>Status & Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clients as $index => $client)
                    @php
                        $status = $client->status;
                        $progress = match($status) {
                            'followup' => 33,
                            'buyer_meeting' => 66,
                            'closing', 'pending', 'gagal' => 100,
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
                        <td style="min-width: 220px;">
                            @if ($status === 'gagal')
                                <span class="badge bg-danger">GAGAL</span>
                            @elseif ($status === 'pending')
                                <a href="https://wa.me/{{ $client->nomor_telepon }}" class="btn btn-sm btn-success mb-1" target="_blank"
                                   onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'followup')">
                                    WA (Ulangi)
                                </a>
                            @elseif (!$status)
                                <a href="https://wa.me/{{ $client->nomor_telepon }}" class="btn btn-sm btn-success mb-1" target="_blank"
                                   onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'followup')">
                                    WA
                                </a>
                            @elseif ($status === 'followup')
                                <button class="btn btn-sm btn-primary mb-1"
                                    onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'buyer_meeting')">
                                    Buyer Meeting
                                </button>
                                <button class="btn btn-sm btn-outline-danger mb-1"
                                    onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'gagal')">
                                    Batal
                                </button>
                            @elseif ($status === 'buyer_meeting')
                                <button class="btn btn-sm btn-success mb-1"
                                    onclick="openClosingModal('{{ $client->id_account }}', '{{ $client->id_listing }}')">
                                    Closing
                                </button>
                                <button class="btn btn-sm btn-warning mb-1"
                                    onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'pending')">
                                    Pending
                                </button>
                                <button class="btn btn-sm btn-outline-danger mb-1"
                                    onclick="updateStatus('{{ $client->id_account }}', '{{ $client->id_listing }}', 'gagal')">
                                    Cancel
                                </button>
                            @else
                                <span class="badge bg-success text-uppercase">{{ $status }}</span>
                            @endif

                            @if ($progress === 100 && $status !== 'pending')
                                <button class="btn btn-sm btn-outline-secondary mt-1"
                                    onclick="hideRow('{{ $client->id_account }}', '{{ $client->id_listing }}')">
                                    Hide
                                </button>
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



{{-- MODAL CLOSING --}}
<div class="modal fade" id="closingModal" tabindex="-1" aria-labelledby="closingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="closingForm" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closingModalLabel">Konfirmasi Closing</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_id_account">
                <input type="hidden" id="modal_id_listing">

                <div class="mb-3">
                    <label for="harga_deal" class="form-label">Harga Deal</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah-input" id="harga_deal" placeholder="0" required>
                    </div>
                </div>

                <!-- HTML: Input Harga Bidding -->
                <div class="mb-3">
                    <label for="harga_bidding" class="form-label">Harga Bidding</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control rupiah-input" id="harga_bidding" placeholder="0" required>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Submit Closing</button>
            </div>
        </form>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>

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

    function hideRow(id_account, id_listing) {
        const rowId = `row-${id_account}-${id_listing}`;
        const row = document.getElementById(rowId);
        if (row) row.style.display = 'none';

        $.ajax({
            url: '{{ route('hide.client') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id_account: id_account,
                id_listing: id_listing
            },
            success: () => console.log('Client hidden successfully'),
            error: () => alert('Failed to hide client.')
        });
    }

    function openClosingModal(id_account, id_listing) {
        document.getElementById('modal_id_account').value = id_account;
        document.getElementById('modal_id_listing').value = id_listing;
        var modal = new bootstrap.Modal(document.getElementById('closingModal'));
        modal.show();
    }

    document.getElementById('closingForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const id_account = document.getElementById('modal_id_account').value;
    const id_listing = document.getElementById('modal_id_listing').value;
    const harga_deal = document.getElementById('harga_deal').value;
    const harga_bidding = document.getElementById('harga_bidding').value;

    fetch("{{ url('/update-status') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            id_account,
            id_listing,
            status: 'closing',
            harga_deal,
            harga_bidding
        })
    }).then(() => location.reload());
});
function handleWA(id_account, id_listing, nomor_telepon) {
    updateStatus(id_account, id_listing, 'followup', function () {
        window.open('https://wa.me/' + nomor_telepon, '_blank');
    });
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
        if (res.success) {
            // Optional: Refresh halaman, atau update row secara dinamis
            location.reload(); // paling simple
        }

        if (callback) callback();
    });
}
</script>



{{-- JS --}}

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

@if (session('role') === 'Register')
<div class="orders mt-5">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="box-title">Register Jobdesk</h4>
                </div>
                <div class="card-body--">
                    <div class="table-stats order-table ov-h">
                        <table class="table" id="clientClosingTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Property ID</th>
                                    <th>Lokasi</th>
                                    <th>Harga</th>
                                    <th>Status</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientsClosing as $client)
                                @php
                                    $tahap = $client->status;
                                    $progress = match($tahap) {
                                        'closing' => 0,
                                        'kutipan_risalah_lelang' => 33,
                                        'akte_grosse' => 66,
                                        'balik_nama' => 100,
                                        default => 0
                                    };
                                @endphp
                                <tr id="row-{{ $client->id_account }}-{{ $client->id_listing }}">
                                    <td class="serial">•</td>
                                    <td>{{ $client->id_account }}</td>
                                    <td>{{ $client->nama }}</td>
                                    <td>{{ $client->id_listing }}</td>
                                    <td>{{ $client->lokasi }}</td>
                                    <td>Rp {{ number_format($client->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
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
                                        @if ($tahap === 'closing')
                                            <button class="btn btn-sm btn-primary"
                                                onclick="openTahapanModal('{{ $client->id_account }}', '{{ $client->id_listing }}', 'kutipan_risalah_lelang')">
                                                Kutipan Risalah
                                            </button>
                                        @elseif ($tahap === 'kutipan_risalah_lelang')
                                            <button class="btn btn-sm btn-info"
                                                onclick="openTahapanModal('{{ $client->id_account }}', '{{ $client->id_listing }}', 'akte_grosse')">
                                                Akte Grosse
                                            </button>
                                        @elseif ($tahap === 'akte_grosse')
                                            <button class="btn btn-sm btn-success"
                                                onclick="openTahapanModal('{{ $client->id_account }}', '{{ $client->id_listing }}', 'balik_nama')">
                                                Balik Nama
                                            </button>
                                        @else
                                            <span class="text-muted">Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Belum ada klien closing.</td>
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
@endif

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


                {{-- <div class="col-xl-4">
                            <div class="row">
                                <div class="col-lg-6 col-xl-12">
                                    <div class="card br-0">
                                        <div class="card-body">
                                            <h4 class="mb-3">Yearly Sales </h4>
                                            <canvas id="sales-chart"></canvas>
                                        </div>
                                    </div><!-- /.card -->
                                </div>

                                <div class="col-lg-6 col-xl-12">
                                    <div class="card bg-flat-color-3  ">
                                        <div class="card-body">
                                            <h4 class="card-title m-0  white-color ">August 2018</h4>
                                        </div>
                                         <div class="card-body">
                                             <div id="flotLine5" class="flot-line"></div>
                                         </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /.col-md-4 --> --}}
                <!-- To Do and Live Chat -->
                {{-- <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title box-title">To Do List</h4>
                                <div class="card-content">
                                    <div class="todo-list">
                                        <div class="tdl-holder">
                                            <div class="tdl-content">
                                                <ul>
                                                    <li>
                                                        <label>
                                                            <input type="checkbox"><i class="check-box"></i><span>Conveniently fabricate interactive technology for ....</span>
                                                            <a href='#' class="fa fa-times"></a>
                                                            <a href='#' class="fa fa-pencil"></a>
                                                            <a href='#' class="fa fa-check"></a>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label>
                                                            <input type="checkbox"><i class="check-box"></i><span>Creating component page</span>
                                                            <a href='#' class="fa fa-times"></a>
                                                            <a href='#' class="fa fa-pencil"></a>
                                                            <a href='#' class="fa fa-check"></a>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" checked><i class="check-box"></i><span>Follow back those who follow you</span>
                                                            <a href='#' class="fa fa-times"></a>
                                                            <a href='#' class="fa fa-pencil"></a>
                                                            <a href='#' class="fa fa-check"></a>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" checked><i class="check-box"></i><span>Design One page theme</span>
                                                            <a href='#' class="fa fa-times"></a>
                                                            <a href='#' class="fa fa-pencil"></a>
                                                            <a href='#' class="fa fa-check"></a>
                                                        </label>
                                                    </li>

                                                    <li>
                                                        <label>
                                                            <input type="checkbox" checked><i class="check-box"></i><span>Creating component page</span>
                                                            <a href='#' class="fa fa-times"></a>
                                                            <a href='#' class="fa fa-pencil"></a>
                                                            <a href='#' class="fa fa-check"></a>
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div> <!-- /.todo-list -->
                                </div>
                            </div> <!-- /.card-body -->
                        </div><!-- /.card -->
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title box-title">Live Chat</h4>
                                <div class="card-content">
                                    <div class="messenger-box">
                                        <ul>
                                            <li>
                                                <div class="msg-received msg-container">
                                                    <div class="avatar">
                                                       <img src="images/avatar/64-1.jpg" alt="">
                                                       <div class="send-time">11.11 am</div>
                                                    </div>
                                                    <div class="msg-box">
                                                        <div class="inner-box">
                                                            <div class="name">
                                                                John Doe
                                                            </div>
                                                            <div class="meg">
                                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perspiciatis sunt placeat velit ad reiciendis ipsam
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- /.msg-received -->
                                            </li>
                                            <li>
                                                <div class="msg-sent msg-container">
                                                    <div class="avatar">
                                                       <img src="images/avatar/64-2.jpg" alt="">
                                                       <div class="send-time">11.11 am</div>
                                                    </div>
                                                    <div class="msg-box">
                                                        <div class="inner-box">
                                                            <div class="name">
                                                                John Doe
                                                            </div>
                                                            <div class="meg">
                                                                Hay how are you doing?
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- /.msg-sent -->
                                            </li>
                                        </ul>
                                        <div class="send-mgs">
                                            <div class="yourmsg">
                                                <input class="form-control" type="text">
                                            </div>
                                            <button class="btn msg-send-btn">
                                                <i class="pe-7s-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div><!-- /.messenger-box -->
                                </div>
                            </div> <!-- /.card-body -->
                        </div><!-- /.card -->
                    </div>
                </div>
                <!-- /To Do and Live Chat -->
                <!-- Calender Chart Weather  -->
                <div class="row">
                    <div class="col-md-12 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <!-- <h4 class="box-title">Chandler</h4> -->
                                <div class="calender-cont widget-calender">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div><!-- /.card -->
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card ov-h">
                            <div class="card-body bg-flat-color-2">
                                <div id="flotBarChart" class="float-chart ml-4 mr-4"></div>
                            </div>
                            <div id="cellPaiChart" class="float-chart"></div>
                        </div><!-- /.card -->
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card weather-box">
                            <h4 class="weather-title box-title">Weather</h4>
                            <div class="card-body">
                                <div class="weather-widget">
                                    <div id="weather-one" class="weather-one"></div>
                                </div>
                            </div>
                        </div><!-- /.card -->
                    </div>
                </div>
                <!-- /Calender Chart Weather -->
                <!-- Modal - Calendar - Add New Event -->
                <div class="modal fade none-border" id="event-modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title"><strong>Add New Event</strong></h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-success save-event waves-effect waves-light">Create event</button>
                                <button type="button" class="btn btn-danger delete-event waves-effect waves-light" data-dismiss="modal">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /#event-modal -->
                <!-- Modal - Calendar - Add Category -->
                <div class="modal fade none-border" id="add-category">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title"><strong>Add a category </strong></h4>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">Category Name</label>
                                            <input class="form-control form-white" placeholder="Enter name" type="text" name="category-name"/>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Choose Category Color</label>
                                            <select class="form-control form-white" data-placeholder="Choose a color..." name="category-color">
                                                <option value="success">Success</option>
                                                <option value="danger">Danger</option>
                                                <option value="info">Info</option>
                                                <option value="pink">Pink</option>
                                                <option value="primary">Primary</option>
                                                <option value="warning">Warning</option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-danger waves-effect waves-light save-category" data-dismiss="modal">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- /#add-category -->
            </div>
            <!-- .animated -->
        </div> --}}
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


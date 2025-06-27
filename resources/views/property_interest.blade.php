@include('template.header')

<section id="appointment" data-stellar-background-ratio="3" style="margin-top: 20px;">
    <div class="container">
        <div class="row">
            <!-- Gambar Properti -->
            <div class="col-md-6 col-sm-6">
                <div class="carousel-inner">
                    @foreach(explode(',', $property->gambar) as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img class="img-fluid rounded w-100" src="{{ $image }}" alt="Property Image" loading="lazy"
                                 style="filter: brightness(1) !important; opacity: 1 !important;">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Detail Lot Lelang & Detail Peserta -->
            <div class="col-md-6 col-sm-6">
                <!-- Detail Lot Lelang -->
                <h2>Detail Lot Lelang</h2>
                <hr style="border: 1px solid #ccc;">
                <p><strong>Kode Lot:</strong> {{ $property->id_listing }}</p>
                <p><strong>Nama Jalan:</strong> 1 bidang tanah dengan total luas {{ $property->luas_tanah }} m2 berikut bangunan dan barang bergerak lainnya di {{ $property->kota }}</p>
                <p><strong>Harga:</strong> Rp{{ number_format($property->harga, 0, ',', '.') }}</p>
                <p class="fst-italic">
                    Harga properti ini belum termasuk biaya tambahan. Berikut rincian perkiraan biaya tambahan yang perlu diperhatikan:
                </p>
                <ul>
                    <li>Biaya dokumen: Rp {{ number_format($property->harga * 0.085, 0, ',', '.') }} (8,5% dari harga)</li>
                    <li>Biaya pengosongan:
                        @php
                            $biaya_pengosongan = 0;
                            if ($property->harga < 500000000) {
                                $biaya_pengosongan = 75000000 + 25000000;
                            } elseif ($property->harga >= 500000000 && $property->harga <= 1500000000) {
                                $biaya_pengosongan = 100000000 + 25000000;
                            } elseif ($property->harga > 1500000000 && $property->harga <= 2500000000) {
                                $biaya_pengosongan = 150000000 + 25000000;
                            } elseif ($property->harga > 3000000000 && $property->harga <= 10000000000) {
                                $biaya_pengosongan = 200000000 + 25000000;
                            } elseif ($property->harga > 10000000000 && $property->harga <= 100000000000) {
                                $biaya_pengosongan = 350000000 + 25000000;
                            } elseif ($property->harga > 100000000000 && $property->harga <= 250000000000) {
                                $biaya_pengosongan = 500000000 + 25000000;
                            } else {
                                $biaya_pengosongan = 1000000000 + 25000000;
                            }
                        @endphp
                        Rp {{ number_format($biaya_pengosongan, 0, ',', '.') }}
                    </li>
                </ul>
                <hr style="border: 1px solid #ccc;">
                <!-- Detail Peserta -->
                <h2>Detail Peserta</h2>
                <hr style="border: 1px solid #ccc;">
                <p><strong>Nama Akun:</strong> {{ $user ? $user->nama : 'Guest' }}</p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
@if(
    empty($clientData?->gambar_ktp) || empty($clientData?->nik)
)
    <div class="alert alert-warning">
        <p><strong>Data KTP belum lengkap.</strong></p>
        <a href="{{ route('profile', ['id_account' => Session::get('id_account') ?? $_COOKIE['id_account'] ?? '']) }}" class="btn btn-warning">Lengkapi Data KTP</a>
    </div>
@elseif(
    empty($clientData?->nomor_npwp) || empty($clientData?->gambar_npwp)
)
    <div class="alert alert-warning">
        <p><strong>Data NPWP belum lengkap.</strong></p>
        <a href="{{ route('profile', ['id_account' => Session::get('id_account') ?? $_COOKIE['id_account'] ?? '']) }}" class="btn btn-warning">Lengkapi Data NPWP</a>
    </div>
@elseif(
    empty($clientData?->nomor_rekening)
)
    <div class="alert alert-warning">
        <p><strong>Data rekening belum lengkap.</strong></p>
        <a href="{{ route('profile', ['id_account' => Session::get('id_account') ?? $_COOKIE['id_account'] ?? '']) }}" class="btn btn-warning">Lengkapi Data Rekening</a>
    </div>
@else

    <form action="{{ route('property.interest.submit', $property->id_listing) }}" method="POST">
        @csrf
        <div id="ktp-section">
            <div class="form-group">
                <label>Pilih KTP:</label>
                <select id="ktpDropdown" class="form-control" required>
                    <option value="">Pilih Data KTP</option>
                    <option value="show">KTP - {{ $clientData->nik ?? 'N/A' }}</option>
                </select>
            </div>
        </div>

        {{-- Card KTP Redesigned --}}
<div id="ktpCard" style="display: none;">
    <div class="card shadow-sm rounded-3 border-0 mb-4">
        <div
            class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
            style="cursor: pointer;"
            onclick="backToKtpSelect()"
            aria-label="Klik untuk ubah KTP"
            role="button"
            tabindex="0"
            onkeypress="if(event.key === 'Enter'){ backToKtpSelect(); }"
        >
            <h5 class="mb-0">KTP</h5>
            <small class="fst-italic" style="font-size: 0.9rem;">Klik untuk ubah</small>
        </div>
        <div class="card-body d-flex flex-column flex-md-row align-items-center">
            {{-- Informasi KTP di kiri --}}
            <div class="flex-grow-1 pe-md-4">
                @if(isset($clientData) && !empty($clientData->gambar_ktp))
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Nomor KTP</dt>
                        <dd class="col-7">{{ $clientData->nik ?? 'N/A' }}</dd>

                        <dt class="col-5 text-muted">Berlaku Hingga</dt>
                        <dd class="col-7">{{ $clientData->berlaku_hingga ?? 'N/A' }}</dd>

                        <dt class="col-5 text-muted">Pekerjaan</dt>
                        <dd class="col-7">{{ $clientData->pekerjaan ?? 'N/A' }}</dd>

                        <dt class="col-5 text-muted">Status Verifikasi</dt>
                        <dd class="col-7">
                            @if($clientData->status_verifikasi === 'terverifikasi')
                                <span class="badge bg-success">Terverifikasi</span>
                            @elseif($clientData->status_verifikasi === 'ditolak')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-secondary">Belum Diverifikasi</span>
                            @endif
                        </dd>
                    </dl>
                @else
                    <p class="text-muted fst-italic mb-0">Belum ada data KTP.</p>
                @endif
            </div>
            {{-- Gambar KTP di kanan --}}
            <div class="white-text flex-shrink-0 mt-4 mt-md-0" style="max-width: 200px;">
                @if(isset($clientData) && !empty($clientData->gambar_ktp))
                    <img
                    src="{{ asset('storage/' . $clientData->gambar_ktp) }}"
                        alt="Foto KTP"
                        class="img-fluid rounded shadow-sm border"
                        style="aspect-ratio: 5 / 3; object-fit: contain; width: 100%;"
                        loading="lazy"
                    >
                    @else
                    <div class="text-center text-muted fst-italic" style="height: 150px; line-height: 150px;">
                        Tidak ada gambar KTP
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="npwp-section" class="form-group">
    <label>Pilih NPWP:</label>
    <select name="npwp" class="form-control" onchange="showNpwpCard(this.value)" required>
        <option value="">Pilih NPWP</option>
        <option value="show">NPWP - {{ $clientData->nomor_npwp ?? 'N/A' }}</option>
    </select>
</div>

<div id="npwp-card" class="card mb-4 border-warning shadow-sm" style="display: none;">
    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center"
         style="cursor: pointer;" onclick="backToNpwpSelect()">
        <h5 class="mb-0">NPWP</h5>
        <small><i>Klik untuk ubah</i></small>
    </div>
    <div class="card-body row align-items-center">
        <div class="col-md-8">
            <p class="mb-2"><strong class="d-inline-block w-50">Nomor NPWP</strong>: {{ $clientData->nomor_npwp ?? 'N/A' }}</p>
            <p class="mb-0"><strong class="d-inline-block w-50">Status Verifikasi</strong>:
                <span class="badge bg-{{ $clientData->status_verifikasi == 'terverifikasi' ? 'success' : 'secondary' }} text-uppercase">
                    {{ ucfirst($clientData->status_verifikasi ?? 'Belum diverifikasi') }}
                </span>
            </p>
        </div>
        <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
            @if (!empty($clientData->gambar_npwp))
                <img
                    src="{{ asset('storage/' . $clientData->gambar_npwp) }}"
                    alt="Foto NPWP"
                    class="img-fluid rounded shadow-sm border"
                    style="max-width: 100%; height: auto; object-fit: contain;"
                    loading="lazy"
                >
            @else
                <p class="text-muted fst-italic">Belum ada gambar NPWP.</p>
            @endif
        </div>
    </div>
</div>
                <div id="bukutabungan-section" class="form-group">
                    <label>Pilih Cover Buku Tabungan:</label>
                    <select id="bukutabunganDropdown" class="form-control" onchange="showBukuTabunganCard(this.value)" required>
                        <option value="">Pilih Buku Tabungan</option>
                        <option value="show">Buku Tabungan - {{ $clientData->nomor_rekening ?? 'N/A' }}</option>
                    </select>
                </div>

                <div id="bukutabunganCard" class="card mb-4 border-primary shadow-sm" style="display: none;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center"
                         style="cursor: pointer;" onclick="backToBukuTabunganDropdown()">
                        <h5 class="mb-0">Buku Tabungan</h5>
                        <small><i>Klik untuk ubah</i></small>
                    </div>
                    <div class="card-body d-flex align-items-center gap-4">
                        <!-- Informasi rekening di kiri -->
                        <div class="flex-grow-1">
                            <p class="mb-2"><strong>Nama Bank:</strong> {{ $clientData->nama_bank ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Atas Nama:</strong> {{ $clientData->atas_nama ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Nomor Rekening:</strong> {{ $clientData->nomor_rekening ?? 'N/A' }}</p>
                        </div>

                        <!-- Visualisasi kartu di kanan -->
                        <div class="rekening-card shadow rounded-4 text-white p-3"
                             style="width: 180px; height: 110px; background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
                                    display: flex; flex-direction: column; justify-content: center; align-items: flex-start; gap: 6px;">
                            <div class="fs-5 fw-bold text-uppercase">{{ $clientData->nama_bank ?? 'BANK NAME' }}</div>
                            <div class="small opacity-75" style="letter-spacing: 2px; font-family: monospace;">
                                {{ $clientData->nomor_rekening
                                    ? preg_replace('/(\d{4})(?=\d)/', '$1 ', $clientData->nomor_rekening)
                                    : 'XXXX XXXX XXXX XXXX' }}
                            </div>
                            <div class="fw-semibold">{{ $clientData->atas_nama ?? 'NAMA PEMILIK' }}</div>
                        </div>
                    </div>
                </div>

                <script>
                    function backToBukuTabunganDropdown() {
                        document.getElementById('bukutabunganCard').style.display = 'none';
                        document.getElementById('bukutabunganDropdown').value = '';
                    }
                </script>

                {{-- Tombol submit --}}
                <button type="submit" class="btn btn-success mt-3 w-100">Saya Tertarik</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>

@include('template.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.getElementById('ktpDropdown');
        const card = document.getElementById('ktpCard');
        const section = document.getElementById('ktp-section');

        dropdown.addEventListener('change', function () {
            if (this.value === 'show') {
                card.style.display = 'block';
                section.style.display = 'none';
            }
        });
    });

    function backToKtpSelect() {
        document.getElementById('ktpCard').style.display = 'none';
        document.getElementById('ktp-section').style.display = 'block';
        document.getElementById('ktpDropdown').value = "";
    }
    function showBukuTabunganCard(value) {
                        if (value === "show") {
                            document.getElementById("bukutabungan-section").style.display = "none";
                            document.getElementById("bukutabunganCard").style.display = "block";
                        }
                    }

                    function backToBukuTabunganDropdown() {
                        document.getElementById("bukutabunganCard").style.display = "none";
                        document.getElementById("bukutabungan-section").style.display = "block";
                        document.getElementById("bukutabunganDropdown").value = "";
                    }
                    function showNpwpCard(value) {
        if (value === "show") {
            document.getElementById("npwp-section").style.display = "none";
            document.getElementById("npwp-card").style.display = "block";
        }
    }

    function backToNpwpSelect() {
        document.getElementById("npwp-card").style.display = "none";
        document.getElementById("npwp-section").style.display = "block";
    }
</script>

<!-- Redesigned Add Property Page -->
<script src="https://unpkg.com/browser-image-compression/dist/browser-image-compression.js"></script>

@include('template.header')
<section class="container my-5">
<div class="container mt-5 mb-5 p-4 bg-white rounded shadow-sm" style="max-width: 900px;">
    <h2 class="text-center text-primary fw-bold mb-4">
        <i class="bi bi-building-add me-2"></i>Tambah Properti Baru
    </h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('property.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        <!-- Wrapper Card Background -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-3 bg-light">

            <ul class="nav nav-pills justify-content-between flex-nowrap gap-2" id="wizardTabs" role="tablist" style="--bs-nav-link-padding-y: 0;">
                @php
                    $steps = [
                        ['id' => 'step1', 'label' => 'Info Umum', 'icon' => 'bi-info-circle'],
                        ['id' => 'step2', 'label' => 'Lokasi', 'icon' => 'bi-geo-alt'],
                        // ['id' => 'step3', 'label' => 'Spesifikasi', 'icon' => 'bi-sliders'],
                        ['id' => 'step3', 'label' => 'Gambar', 'icon' => 'bi-images'],
                    ];
                @endphp

                @foreach ($steps as $index => $step)
                <li class="nav-item flex-fill" role="presentation">
                    <button
                        class="nav-link w-100 h-100 text-center d-flex flex-column justify-content-center align-items-center py-3 px-2 {{ $index === 0 ? 'active' : '' }}"
                        style="min-height: 100px; border-radius: 0.75rem;"
                        data-bs-toggle="pill"
                        data-bs-target="#{{ $step['id'] }}"
                        type="button"
                        role="tab"
                    >
                        <i class="bi {{ $step['icon'] }} fs-4 mb-1"></i>
                        <span class="fw-semibold small">Step {{ $index + 1 }}<br>{{ $step['label'] }}</span>
                    </button>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="tab-content" id="wizardContent">
                <!-- Step 1: Info Umum -->
                <div class="tab-pane fade show active" id="step1" role="tabpanel">
                    <div class="card shadow-sm rounded-4 p-4">
                        <h5 class="mb-4 fw-semibold text-orange">
                            <i class="bi bi-info-circle me-2 text-orange"></i> Informasi Umum
                        </h5>

                        <div class="row g-3">
                            <!-- Judul & Harga dalam 1 row -->
                            <div class="row g-3">
                                <!-- Judul -->
                                <div class="col-md-6">
                                    <label class="form-label">Judul Properti <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-pencil-square"></i></span>
                                        <input type="text" class="form-control" id="judul" name="judul" value="{{ old('judul') }}" required>
                                    </div>
                                </div>

                                <!-- Harga -->
                                <div class="col-md-6">
                                    <label class="form-label">Harga <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                                        <input type="text" class="form-control" id="harga" name="harga" value="{{ old('harga') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <!-- Luas Tanah -->
                                <div class="col-md-6">
                                    <label class="form-label">Luas Tanah (m²)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-fullscreen"></i></span>
                                        <input type="number" class="form-control" id="luas_tanah" name="luas_tanah" value="{{ old('luas_tanah') }}" required>
                                    </div>
                                </div>

                                <!-- Jenis Sertifikat -->
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Sertifikat</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                                        <select name="sertifikat" class="form-select">
                                            <option value="">Pilih Sertifikat</option>
                                            @foreach(['SHM', 'HGB', 'AJB', 'Girik', 'Hak Pakai'] as $sertif)
                                                <option value="{{ $sertif }}" {{ old('sertifikat') == $sertif ? 'selected' : '' }}>{{ $sertif }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 align-items-end">
                                <!-- Tipe Properti -->
                                <div class="col-md-6">
                                    <label class="form-label">Tipe Properti <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                                        <select name="tipe" class="form-select" required>
                                            <option value="">Pilih tipe</option>
                                            @foreach(['rumah', 'hotel dan villa', 'pabrik', 'ruko', 'tanah', 'gudang', 'apartemen', 'sewa'] as $type)
                                                <option value="{{ $type }}" {{ old('tipe') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Metode Pembayaran -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-wallet2 me-2"></i> Metode Pembayaran
                                    </label>
                                    <div class="d-flex gap-3 mt-1">
                                        @foreach(['cash', 'kpr'] as $pay)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="payment[]" id="payment_{{ $pay }}" value="{{ $pay }}" {{ in_array($pay, old('payment', explode(',', $property->payment ?? ''))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="payment_{{ $pay }}">{{ strtoupper($pay) }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="col-12">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi') }}</textarea>
                                </div>
                                <div class="text-end"><small><span id="charCount">{{ strlen(old('deskripsi')) }}</span>/2200</small></div>
                            </div>
                        </div>
                    </div>
                </div>



        <!-- Step 2: Lokasi -->
        <div class="tab-pane fade" id="step2" role="tabpanel">
            <div class="card shadow-sm rounded-4 p-4">
                <h5 class="mb-4 fw-semibold text-orange">
                    <i class="bi bi-geo-alt-fill text-orange me-2"></i> Lokasi Properti
                </h5>

                <!-- Alamat Lengkap -->
                <div class="mb-4">
                    <label for="lokasi" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                        <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" required>
                    </div>
                </div>

                <!-- Lokasi Berjenjang -->
                <div class="row g-3">
                    <!-- Provinsi -->
                    <div class="col-md-4">
                        <label for="province" class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-globe"></i></span>
                            <select class="form-select" id="province" name="provinsi" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kota / Kabupaten -->
                    <div class="col-md-4">
                        <label for="city" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select class="form-select" id="city" name="kota" required disabled>
                                <option value="">Pilih Kota/Kabupaten</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kelurahan -->
                    <div class="col-md-4">
                        <label for="kelurahan" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                            <select class="form-select" id="kelurahan" name="kelurahan" required disabled>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="mt-4">
                    <label class="form-label"><i class="bi bi-map text-orange me-2"></i> Lokasi di Peta (Live)</label>
                    <div style="overflow:hidden;max-width:100%;width:100%;height:300px;">
                        <div id="my-map-display" style="height:100%; width:100%;max-width:100%;">
                            <iframe
                                id="googleMap"
                                style="height:100%;width:100%;border:0;"
                                frameborder="0"
                                src="https://www.google.com/maps/embed/v1/place?q=Surabaya&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Spesifikasi -->
        {{-- <div class="tab-pane fade" id="step3" role="tabpanel">
            <div class="card shadow-sm rounded-4 p-4">
                <h5 class="mb-4 fw-semibold text-orange">
                    <i class="bi bi-building text-orange me-2"></i> Spesifikasi Properti
                </h5>

                <!-- Ukuran & Fasilitas Dasar -->
                <div class="row g-3 mb-3">
                    <!-- Kamar Tidur -->
                    <div class="col-md-4">
                        <label class="form-label">Kamar Tidur <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-door-closed"></i></span>
                            <input type="number" class="form-control" id="kamar_tidur" name="kamar_tidur" min="0" value="{{ old('kamar_tidur') }}" required>
                        </div>
                    </div>

                    <!-- Kamar Mandi -->
                    <div class="col-md-4">
                        <label class="form-label">Kamar Mandi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-droplet"></i></span>
                            <input type="number" class="form-control" id="kamar_mandi" name="kamar_mandi" min="0" value="{{ old('kamar_mandi') }}" required>
                        </div>
                    </div>

                    <!-- Jumlah Lantai -->
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Lantai</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-layers"></i></span>
                            <input type="number" class="form-control" id="lantai" name="lantai" value="{{ old('lantai') }}" required>
                    </div>
                </div>

                <!-- Luas -->
                <div class="row g-3 mb-3">
                    <!-- Luas Tanah -->
                    <div class="col-md-6">
                        <label class="form-label">Luas Tanah (m²)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-fullscreen"></i></span>
                            <input type="number" class="form-control" id="luas_tanah" name="luas_tanah" value="{{ old('luas_tanah') }}" required>
                        </div>
                    </div>

                    <!-- Luas Bangunan -->
                    <div class="col-md-6">
                        <label class="form-label">Luas Bangunan (m²)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-aspect-ratio"></i></span>
                            <input type="number" class="form-control" id="luas_bangunan" name="luas_bangunan" value="{{ old('luas_bangunan') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Hadap & Sertifikat -->
                <div class="row g-3 mb-3">
                    <!-- Arah Hadap -->
                    <div class="col-md-6">
                        <label class="form-label"><i></i> Arah Hadap</label>
                        <div class="d-flex justify-content-between">
                            @foreach(['utara', 'selatan', 'timur', 'barat'] as $ori)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="orientation" id="orientation_{{ $ori }}" value="{{ $ori }}" {{ old('orientation') == $ori ? 'checked' : '' }}>
                                    <label class="form-check-label" for="orientation_{{ $ori }}">{{ ucfirst($ori) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Jenis Sertifikat -->
                    <div class="col-md-6">
                        <label class="form-label">
                            <i></i> Jenis Sertifikat
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                            <select name="sertifikat" class="form-select">
                                <option value="">Pilih Sertifikat</option>
                                @foreach(['SHM', 'HGB', 'AJB', 'Girik', 'Hak Pakai'] as $sertif)
                                    <option value="{{ $sertif }}" {{ old('sertifikat')}}>{{ $sertif }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}


        <div class="tab-pane fade" id="step3" role="tabpanel">
            <div class="card shadow-sm rounded-4 p-4">
                <!-- Upload Gambar -->
                <div class="col-md-12">
                    <label for="gambar" class="form-label fw-semibold">
                        <i class="bi bi-images me-1"></i> Unggah Gambar
                    </label>

                    <div id="dropzone" class="border rounded-3 p-4 text-center bg-light" style="cursor: pointer;">
                        <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                        <p class="mb-1">Tarik & Lepas gambar ke sini</p>
                        <small class="text-muted">Atau klik untuk memilih gambar (bisa lebih dari satu)</small>
                        <input type="file" id="gambar" name="gambar[]" multiple accept="image/*" style="display: none;" required>
                    </div>

                    <!-- Preview Gambar -->
                    <div id="previewContainer" class="mt-3 d-flex flex-wrap gap-3"></div>
            <input type="hidden" name="cover_new" id="coverNewInput" value="0">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary w-100 py-2">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Properti
                </button>
            </div>
        </div>
    </form>
</div>
</section>
@include('template.footer')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alamatInput = document.getElementById('lokasi');
    const mapFrame = document.getElementById('googleMap');
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const kelurahanSelect = document.getElementById('kelurahan');

    let lokasiData = [];

    // Fetch indonesia.json
    fetch('{{ asset("data/indonesia.json") }}')
        .then(res => res.json())
        .then(data => {
            lokasiData = data;

            // Populate Provinsi (UPPERCASE in value, display normal)
            const provinsiSet = new Set(data.map(item => item.province.toUpperCase().trim()));
            provinsiSet.forEach(prov => {
                provinceSelect.innerHTML += `<option value="${prov}">${prov}</option>`;
            });
        });

    alamatInput.addEventListener('input', function () {
        const alamat = this.value.trim();

        if (alamat === "") {
            // Default Surabaya jika alamat kosong
            mapFrame.src = "https://www.google.com/maps/embed/v1/place?q=Surabaya&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8";
            provinceSelect.value = "";
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            citySelect.disabled = true;
            kelurahanSelect.disabled = true;
            return;
        }

        // Update Google Maps
        const encodedAlamat = encodeURIComponent(alamat);
        const apiKey = 'AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8';
        mapFrame.src = `https://www.google.com/maps/embed/v1/place?q=${encodedAlamat}&key=${apiKey}`;

        // Auto-fill dropdowns
        const found = lokasiData.find(item =>
            alamat.toLowerCase().includes(item.district.toLowerCase().trim()) ||
            alamat.toLowerCase().includes(item.regency.toLowerCase().trim()) ||
            alamat.toLowerCase().includes(item.province.toLowerCase().trim())
        );

        if (found) {
            // Set Provinsi
            const provinsiFormatted = found.province.trim().toUpperCase();
            provinceSelect.value = provinsiFormatted;

            // Fill Kota/Kabupaten
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            const kotaSet = new Set(
                lokasiData
                    .filter(item => item.province.trim().toUpperCase() === provinsiFormatted)
                    .map(item => {
                        if (item.regency.toLowerCase().startsWith("kota")) {
                            return `KOTA ${item.regency.substring(5).trim().toUpperCase()}`;
                        } else if (item.regency.toLowerCase().startsWith("kabupaten")) {
                            return `KAB. ${item.regency.substring(10).trim().toUpperCase()}`;
                        } else {
                            return item.regency.trim().toUpperCase();
                        }
                    })
            );
            kotaSet.forEach(kota => {
                citySelect.innerHTML += `<option value="${kota}">${kota}</option>`;
            });

            // Set value Kota/Kabupaten
            let regencyFormatted = "";
            if (found.regency.toLowerCase().startsWith("kota")) {
                regencyFormatted = `KOTA ${found.regency.substring(5).trim().toUpperCase()}`;
            } else if (found.regency.toLowerCase().startsWith("kabupaten")) {
                regencyFormatted = `KAB. ${found.regency.substring(10).trim().toUpperCase()}`;
            } else {
                regencyFormatted = found.regency.trim().toUpperCase();
            }
            citySelect.value = regencyFormatted;
            citySelect.disabled = false;

            // Fill Kelurahan
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            const kelurahanSet = new Set(
                lokasiData
                    .filter(item =>
                        item.province.trim().toUpperCase() === provinsiFormatted &&
                        item.regency.trim().toUpperCase() === found.regency.trim().toUpperCase()
                    )
                    .map(item =>
                        item.district
                            .toLowerCase()
                            .split(" ")
                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                            .join(" ")
                    )
            );
            kelurahanSet.forEach(kel => {
                kelurahanSelect.innerHTML += `<option value="${kel}">${kel}</option>`;
            });

            // Set value kelurahan
            const kelurahanFormatted = found.district
                .toLowerCase()
                .split(" ")
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(" ");
            kelurahanSelect.value = kelurahanFormatted;
            kelurahanSelect.disabled = false;
        }
    });

    // Manual Dropdown Chain
    provinceSelect.addEventListener('change', function () {
        const selectedProvinsi = this.value.trim().toUpperCase();
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
        citySelect.disabled = false;
        kelurahanSelect.disabled = true;

        const kotaSet = new Set(
            lokasiData
                .filter(item => item.province.trim().toUpperCase() === selectedProvinsi)
                .map(item => {
                    if (item.regency.toLowerCase().startsWith("kota")) {
                        return `KOTA ${item.regency.substring(5).trim().toUpperCase()}`;
                    } else if (item.regency.toLowerCase().startsWith("kabupaten")) {
                        return `KAB. ${item.regency.substring(10).trim().toUpperCase()}`;
                    } else {
                        return item.regency.trim().toUpperCase();
                    }
                })
        );
        kotaSet.forEach(kota => {
            citySelect.innerHTML += `<option value="${kota}">${kota}</option>`;
        });
    });

    citySelect.addEventListener('change', function () {
        const selectedProvinsi = provinceSelect.value.trim().toUpperCase();
        const selectedKota = this.value.trim().toUpperCase();
        kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
        kelurahanSelect.disabled = false;

        const kelurahanSet = new Set(
            lokasiData
                .filter(item => {
                    let regencyFormatted = "";
                    if (item.regency.toLowerCase().startsWith("kota")) {
                        regencyFormatted = `KOTA ${item.regency.substring(5).trim().toUpperCase()}`;
                    } else if (item.regency.toLowerCase().startsWith("kabupaten")) {
                        regencyFormatted = `KAB. ${item.regency.substring(10).trim().toUpperCase()}`;
                    } else {
                        regencyFormatted = item.regency.trim().toUpperCase();
                    }
                    return (
                        item.province.trim().toUpperCase() === selectedProvinsi &&
                        regencyFormatted === selectedKota
                    );
                })
                .map(item =>
                    item.district
                        .toLowerCase()
                        .split(" ")
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(" ")
                )
        );
        kelurahanSet.forEach(kel => {
            kelurahanSelect.innerHTML += `<option value="${kel}">${kel}</option>`;
        });
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputHarga = document.getElementById('harga');

        // Format angka saat diketik
        inputHarga.addEventListener('input', function (e) {
            const value = this.value.replace(/\D/g, ''); // Hapus semua non-digit
            const formatted = new Intl.NumberFormat('id-ID').format(value); // Format ribuan Indonesia
            this.value = formatted;
        });

        // Saat submit form, ubah jadi angka mentah (misalnya 2000000)
        inputHarga.form.addEventListener('submit', function () {
            inputHarga.value = inputHarga.value.replace(/\./g, ''); // Hapus titik
        });
    });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropzone = document.getElementById('dropzone');
        const input = document.getElementById('gambar'); // input[type="file"] multiple
        let compressedFiles = [];

        // === Klik Dropzone untuk trigger input file ===
        dropzone.addEventListener('click', () => input.click());

        // === Drag n Drop Styling ===
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-primary', 'bg-white');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-primary', 'bg-white');
        });

        // === Handle Drop Files ===
        dropzone.addEventListener('drop', async (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-white');
            const files = e.dataTransfer.files;
            await handleFiles(files);
        });

        // === Handle Manual File Input ===
        input.addEventListener('change', async () => {
            await handleFiles(input.files);
        });

        // === Fungsi utama kompres dan ganti input ===
        async function handleFiles(files) {
            compressedFiles = [];

            for (const file of files) {
                if (!file.type.startsWith('image/')) continue;

                let compressed = file;

                // Hanya kompres jika size > 500 KB
                if (file.size > 500000) {
                    const options = {
                        maxSizeMB: 1,
                        maxWidthOrHeight: 1920,
                        useWebWorker: true,
                    };
                    try {
                        compressed = await imageCompression(file, options);
                    } catch (error) {
                        console.error('Gagal kompres:', error);
                    }
                }

                compressedFiles.push(compressed);
            }

            // Gantikan input files dengan versi terkompresi
            const dataTransfer = new DataTransfer();
            compressedFiles.forEach(f => dataTransfer.items.add(f));
            input.files = dataTransfer.files;

            console.log('Gambar siap dikirim:', [...input.files].map(f => f.name));
        }
    });
    </script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropzone = document.getElementById('dropzone');
        const input = document.getElementById('gambar');
        const previewContainer = document.getElementById('previewContainer');
        const coverInput = document.getElementById('coverNewInput');

        let selectedFiles = [];

        dropzone.addEventListener('click', () => input.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-primary', 'bg-white');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-primary', 'bg-white');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-primary', 'bg-white');
            selectedFiles = Array.from(e.dataTransfer.files);
            renderPreviews();
        });

        input.addEventListener('change', (e) => {
            selectedFiles = Array.from(e.target.files);
            renderPreviews();
        });

        function renderPreviews() {
            previewContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'position-relative';
                    wrapper.style.width = '240px';
                    wrapper.style.height = '240px';
                    wrapper.dataset.index = index;

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail rounded';
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';

                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.innerHTML = '<i class="bi bi-x"></i>';
                    deleteBtn.className = 'btn btn-danger btn-sm position-absolute';
                    deleteBtn.style.top = '6px';
                    deleteBtn.style.right = '6px';
                    deleteBtn.style.width = '28px';
                    deleteBtn.style.height = '28px';
                    deleteBtn.style.borderRadius = '50%';
                    deleteBtn.style.padding = '0';
                    deleteBtn.title = 'Hapus';

                    deleteBtn.onclick = () => {
                        selectedFiles.splice(index, 1);
                        updateInputFiles();
                        renderPreviews();
                    };

                    const coverBtn = document.createElement('button');
                    coverBtn.type = 'button';
                    coverBtn.innerHTML = '<i class="bi bi-star me-1"></i> Cover';
                    coverBtn.className = 'btn btn-outline-secondary btn-sm position-absolute';
                    coverBtn.style.bottom = '6px';
                    coverBtn.style.right = '6px';
                    coverBtn.style.padding = '4px 10px';
                    coverBtn.style.borderRadius = '20px';
                    coverBtn.style.fontSize = '12px';
                    coverBtn.style.background = 'rgba(255, 255, 255, 0.85)';
                    coverBtn.style.color = '#333';
                    coverBtn.style.backdropFilter = 'blur(2px)';
                    coverBtn.style.boxShadow = '0 0 4px rgba(0,0,0,0.1)';
                    coverBtn.setAttribute('data-cover-btn', 'true');

                    coverBtn.onclick = () => {
                        previewContainer.querySelectorAll('[data-cover-btn]').forEach(btn => {
                            btn.classList.remove('btn-success');
                            btn.classList.add('btn-outline-secondary');
                            btn.innerHTML = '<i class="bi bi-star me-1"></i> Cover';
                            btn.style.background = 'rgba(255, 255, 255, 0.85)';
                            btn.style.color = '#333';
                        });

                        coverBtn.classList.remove('btn-outline-secondary');
                        coverBtn.classList.add('btn-success');
                        coverBtn.innerHTML = '<i class="bi bi-star-fill me-1"></i> Cover';
                        coverBtn.style.background = '';
                        coverBtn.style.color = '';

                        coverInput.value = index;
                    };

                    wrapper.appendChild(img);
                    wrapper.appendChild(deleteBtn);
                    wrapper.appendChild(coverBtn);
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        }

        function updateInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }
    });
    </script>



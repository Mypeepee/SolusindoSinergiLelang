<!-- Redesigned Add Property Page -->
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
                        ['id' => 'step3', 'label' => 'Spesifikasi', 'icon' => 'bi-sliders'],
                        ['id' => 'step4', 'label' => 'Gambar', 'icon' => 'bi-images'],
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


                    <div class="row g-3 align-items-end">
                        <!-- Kiri: Tipe Properti (Full) -->
                        <div class="col-md-6">
                            <label class="form-label">Tipe Properti <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                                <select name="tipe" class="form-select" required>
                                    <option value="">Pilih tipe</option>
                                    @foreach(['rumah', 'villa', 'pabrik', 'ruko', 'tanah', 'gudang', 'apartemen', 'sewa'] as $type)
                                    <option value="{{ $type }}" {{ old('tipe') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>


                        <!-- Kanan bawah: Metode Pembayaran -->
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
                        <label for="kelurahan" class="form-label">Kelurahan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                            <select class="form-select" id="kelurahan" name="kelurahan" required disabled>
                                <option value="">Pilih Kelurahan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="mt-4">
                    <label class="form-label"><i class="bi bi-map text-orange me-2"></i> Lokasi di Peta (Opsional)</label>
                    <div class="rounded-3 border overflow-hidden shadow-sm" style="height: 250px; background-color: #f8f9fa;">
                        <iframe
                            width="100%"
                            height="100%"
                            frameborder="0"
                            style="border:0;"
                            src="https://maps.google.com/maps?q={{ urlencode($property->lokasi ?? 'Surabaya') }}&output=embed"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
        <!-- Step 3: Spesifikasi -->
        <div class="tab-pane fade" id="step3" role="tabpanel">
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
        </div>


        </div>
        <div class="tab-pane fade" id="step4" role="tabpanel">
            <div class="card p-4">
                            <!-- Dropzone Upload -->
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

                    <!-- Preview -->
                    <div id="previewContainer" class="mt-3 d-flex flex-wrap gap-3"></div>
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
    document.querySelector('form').addEventListener('submit', function (e) {
  const previews = document.querySelectorAll('#previewContainer img');
  if (previews.length === 0) {
    e.preventDefault();
    alert('Minimal satu gambar harus diunggah!');
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const dropzone = document.getElementById('dropzone');
  const fileInput = document.getElementById('gambar');
  const previewContainer = document.getElementById('previewContainer');

  const coverInput = document.createElement('input');
  coverInput.type = 'hidden';
  coverInput.name = 'cover_image_index';
  coverInput.value = '';
  previewContainer.appendChild(coverInput);

  dropzone.addEventListener('click', () => fileInput.click());

  dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.classList.add('border-primary');
  });

  dropzone.addEventListener('dragleave', () => {
    dropzone.classList.remove('border-primary');
  });

  dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('border-primary');
    fileInput.files = e.dataTransfer.files;
    renderPreviews(fileInput.files);
  });

  fileInput.addEventListener('change', e => {
    renderPreviews(e.target.files);
  });

  function renderPreviews(files) {
    previewContainer.innerHTML = '';
    Array.from(files).forEach((file, index) => {
      if (!file.type.startsWith('image/')) return;

      const reader = new FileReader();
      reader.onload = e => {
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
        deleteBtn.onclick = () => {
          alert('Untuk menghapus gambar, silakan pilih ulang semua file.');
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
});

document.addEventListener('DOMContentLoaded', function () {
    const hargaInput = document.getElementById('harga');
    hargaInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');
        if (!value) return this.value = '';
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });

    // Province - City dynamic dropdown
    const provinsiSelect = document.getElementById('provinsi');
    const kotaSelect = document.getElementById('kota');

    fetch('{{ url("data/indonesia.json") }}')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(prov => {
                let option = document.createElement('option');
                option.value = prov;
                option.textContent = prov;
                provinsiSelect.appendChild(option);
            });

            provinsiSelect.addEventListener('change', function () {
                let selectedProvinsi = this.value;
                kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                if (selectedProvinsi && data[selectedProvinsi]) {
                    data[selectedProvinsi].forEach(kota => {
                        let option = document.createElement('option');
                        option.value = kota;
                        option.textContent = kota;
                        kotaSelect.appendChild(option);
                    });
                }
            });
        });
});

const deskripsiInput = document.getElementById('deskripsi');
const charCount = document.getElementById('charCount');

deskripsiInput.addEventListener('input', function () {
    const currentLength = this.value.length;
    if (currentLength > 2200) {
        this.value = this.value.slice(0, 2200);
    }
    charCount.textContent = this.value.length;
});

// Untuk preload jika ada old('deskripsi')
document.addEventListener('DOMContentLoaded', function () {
    charCount.textContent = deskripsiInput.value.length;
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const kelurahanSelect = document.getElementById('kelurahan');

        let lokasiData = [];

        fetch("{{ asset('data/indonesia.json') }}")
            .then(res => res.json())
            .then(data => {
                lokasiData = data;

                const provinsiSet = new Set(data.map(item => item.province));
                provinsiSet.forEach(prov => {
                    provinceSelect.innerHTML += `<option value="${prov}">${prov}</option>`;
                });
            });

        provinceSelect.addEventListener('change', function () {
            const selectedProvinsi = this.value;
            citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            citySelect.disabled = false;
            kelurahanSelect.disabled = true;

            const kotaSet = new Set(
                lokasiData
                    .filter(item => item.province === selectedProvinsi)
                    .map(item => item.regency)
            );

            kotaSet.forEach(kota => {
                citySelect.innerHTML += `<option value="${kota}">${kota}</option>`;
            });
        });

        citySelect.addEventListener('change', function () {
            const selectedProvinsi = provinceSelect.value;
            const selectedKota = this.value;
            kelurahanSelect.innerHTML = '<option value="">Pilih Kelurahan</option>';
            kelurahanSelect.disabled = false;

            const kelurahanSet = new Set(
                lokasiData
                    .filter(item => item.province === selectedProvinsi && item.regency === selectedKota)
                    .map(item => item.district)
            );

            kelurahanSet.forEach(kel => {
                kelurahanSelect.innerHTML += `<option value="${kel}">${kel}</option>`;
            });
        });
    });
    </script>

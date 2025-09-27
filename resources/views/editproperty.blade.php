<!-- Redesigned Add Property Page -->
@include('template.header')
<section>
<div class="container mt-5 mb-5 p-4 bg-white rounded shadow-sm" style="max-width: 900px;">
    <h2 class="text-center text-primary fw-bold mb-4">
        <i class="bi bi-building-add me-2"></i>Edit Properti
    </h2>

<form action="{{ route('property.update', $property->id_listing) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
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

@php use Illuminate\Support\Str; @endphp
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
                        <input type="text" name="judul"
                               class="form-control @error('judul') is-invalid @enderror"
                               required
                               value="{{ old('judul', $property->judul) }}">
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Harga -->
                <div class="col-md-6">
                    <label class="form-label">Harga <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-cash-stack"></i></span>
                        <input type="text" name="harga" id="harga"
                               class="form-control @error('harga') is-invalid @enderror"
                               value="{{ old('harga', number_format($property->harga, 0, ',', '.')) }}">
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row g-3 align-items-end">
                <!-- Tipe Properti -->
                <div class="col-md-6">
                    <label class="form-label">Tipe Properti <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                        <select name="tipe" class="form-select @error('tipe') is-invalid @enderror" required>
                            <option value="">Pilih tipe</option>
                            @foreach(['rumah','villa','pabrik','ruko','tanah','gudang','apartemen','sewa'] as $type)
                                <option value="{{ $type }}"
                                    {{ str_contains(strtolower(old('tipe', $property->tipe ?? '')), strtolower($type)) ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Kanan atas: Status Listing -->
                <div class="col-md-3">
                    <label class="form-label">Status Listing <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="Tersedia" {{ old('status', $property->status) == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Terjual" {{ old('status', $property->status) == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                                <input class="form-check-input @error('payment') is-invalid @enderror"
                                       type="checkbox"
                                       name="payment[]"
                                       id="payment_{{ $pay }}"
                                       value="{{ $pay }}"
                                       {{ in_array($pay, old('payment', explode(',', $property->payment ?? ''))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_{{ $pay }}">{{ strtoupper($pay) }}</label>
                            </div>
                        @endforeach
                        @error('payment')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Luas Tanah & Sertifikat -->
            <div class="row g-3 mt-2">
                <!-- Luas Tanah -->
                <div class="col-md-6">
                    <label class="form-label">Luas Tanah (m²) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-fullscreen"></i></span>
                        <input type="number" class="form-control @error('luas_tanah') is-invalid @enderror"
                               id="luas_tanah" name="luas_tanah"
                               value="{{ old('luas_tanah', $property->luas ?? '') }}" required>
                        @error('luas_tanah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Sertifikat -->
                <div class="col-md-6">
                    <label class="form-label">Jenis Sertifikat <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
                        <select name="sertifikat" class="form-select @error('sertifikat') is-invalid @enderror" required>
                            <option value="">Pilih Sertifikat</option>
                            @foreach(['SHM', 'HGB', 'AJB', 'Girik', 'Hak Pakai'] as $sertif)
                                <option value="{{ $sertif }}"
                                    {{ Str::startsWith(old('sertifikat', $property->sertifikat ?? ''), $sertif) ? 'selected' : '' }}>
                                    {{ $sertif }}
                                </option>
                            @endforeach
                        </select>
                        @error('sertifikat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="col-12">
                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                    <textarea name="deskripsi"
                              rows="4"
                              class="form-control @error('deskripsi') is-invalid @enderror"
                              required>{{ old('deskripsi', $property->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="text-end"><small><span id="charCount">{{ strlen(old('deskripsi', $property->deskripsi)) }}</span>/2200</small></div>
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
                <input type="text"
                       class="form-control @error('lokasi') is-invalid @enderror"
                       name="lokasi"
                       value="{{ old('lokasi', $property->lokasi) }}"
                       placeholder="Contoh: Jl. Raya Simo Gunung Baru No. 12"
                       required>
                @error('lokasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Lokasi Berjenjang -->
        <div class="row g-3">
            <!-- Provinsi -->
            <div class="col-md-4">
                <label for="province" class="form-label">Provinsi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-globe"></i></span>
                    <select class="form-select @error('provinsi') is-invalid @enderror"
                            id="province" name="provinsi" required>
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province }}"
                                {{ old('provinsi', $property->provinsi) == $province ? 'selected' : '' }}>
                                {{ ucfirst($province) }}
                            </option>
                        @endforeach
                    </select>
                    @error('provinsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Kota / Kabupaten -->
            <div class="col-md-4">
                <label for="city" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <select class="form-select @error('kota') is-invalid @enderror"
                            id="city" name="kota" required>
                        <option value="">Pilih Kota</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}"
                                {{ old('kota', $property->kota) == $city ? 'selected' : '' }}>
                                {{ ucfirst($city) }}
                            </option>
                        @endforeach
                    </select>
                    @error('kota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Kecamatan -->
            <div class="col-md-4">
                <label for="kecamatan" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-fill"></i></span>
                    <select class="form-select @error('kecamatan') is-invalid @enderror"
                            id="kecamatan" name="kecamatan" required>
                        <option value="">Pilih Kecamatan</option>
                        @foreach($districts as $district)
                            <option value="{{ $district }}"
                                {{ old('kecamatan', $property->kecamatan) == $district ? 'selected' : '' }}>
                                {{ ucfirst($district) }}
                            </option>
                        @endforeach
                    </select>
                    @error('kecamatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Google Maps -->
        <div class="mt-4">
            <label class="form-label"><i class="bi bi-map text-orange me-2"></i> Lokasi di Peta (Opsional)</label>
            <div class="rounded-3 border overflow-hidden shadow-sm"
                 style="height: 250px; background-color: #f8f9fa;">
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

        <div class="tab-pane fade" id="step3" role="tabpanel">
            <div class="card p-4">
                <!-- Dropzone Upload -->
                <div class="col-md-12">
                    <label for="gambar" class="form-label fw-semibold">
                        <i class="bi bi-images me-1"></i> Unggah Gambar
                    </label>

                    <div id="dropzone"
                        class="border rounded-3 p-4 text-center bg-light @error('gambar') border-danger @enderror"
                        style="cursor: pointer;">
                        <i class="bi bi-cloud-arrow-up fs-1 text-primary"></i>
                        <p class="mb-1">Tarik & Lepas gambar ke sini</p>
                        <small class="text-muted">Atau klik untuk memilih gambar (bisa lebih dari satu)</small>
                        <input type="file"
                            id="gambar"
                            name="gambar[]"
                            multiple
                            accept="image/*"
                            style="display: none;"
                            class="@error('gambar') is-invalid @enderror">
                        @error('gambar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('gambar.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Preview -->
                    <div id="previewContainer" class="mt-3 d-flex flex-wrap gap-3"></div>
                </div>

                @if($property->gambar)
                    @php
                        $gambarList = explode(',', $property->gambar);
                    @endphp
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-image me-2 text-orange"></i> Gambar Saat Ini
                        </label>
                        <div class="d-flex flex-wrap gap-3" id="existingImages">
                            @foreach($gambarList as $index => $img)
                                <div class="position-relative"
                                    style="width: 240px; height: 240px;"
                                    data-index="{{ $index }}">
                                    <img src="{{ asset(ltrim($img, '/')) }}"
                                        alt="Image {{ $index+1 }}"
                                        class="img-thumbnail rounded w-100 h-100"
                                        style="object-fit: cover;">

                                    <!-- Tombol Delete -->
                                    <button type="button"
                                            class="btn btn-danger btn-sm position-absolute"
                                            title="Hapus manual"
                                            style="top: 6px; right: 6px; width: 28px; height: 28px; border-radius: 50%; padding: 0;"
                                            onclick="alert('Untuk menghapus gambar, silakan pilih ulang semua file.')">
                                        <i class="bi bi-x"></i>
                                    </button>

                                    <!-- Tombol Cover -->
                                    <button type="button"
                                            class="btn {{ $property->cover == $index ? 'btn-success' : 'btn-outline-secondary' }} btn-sm position-absolute"
                                            style="bottom: 6px; right: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px;
                                            {{ $property->cover == $index ? '' : 'background: rgba(255, 255, 255, 0.85); color: #333;' }}
                                            backdrop-filter: blur(2px); box-shadow: 0 0 4px rgba(0,0,0,0.1);"
                                            data-cover-btn="true"
                                            onclick="setExistingCover({{ $index }}, this)">
                                        <i class="bi {{ $property->cover == $index ? 'bi-star-fill' : 'bi-star' }} me-1"></i> Cover
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="cover_existing" id="coverExistingInput" value="{{ $property->cover }}">
                    </div>
                @endif
            </div>
        </div>

        <!-- Tombol Update -->
        <div class="mt-4">
            <button type="submit" id="submitBtn" class="btn btn-primary w-100 py-2">
                <span id="btnText"><i class="bi bi-plus-circle me-2"></i>Update Properti</span>
                <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
            </button>
        </div>

    </form>
    {{-- Notifikasi Error Global --}}
@if ($errors->any())
<div class="alert alert-danger">
    <strong>Terjadi kesalahan:</strong>
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
</div>

{{-- Script tombol loading --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("updateForm");
        const btn = document.getElementById("submitBtn");
        const btnText = document.getElementById("btnText");
        const btnSpinner = document.getElementById("btnSpinner");

        if (form) {
            form.addEventListener("submit", function () {
                btn.disabled = true;
                btnText.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menyimpan...';
                btnSpinner.classList.remove("d-none");
            });
        }
    });
    </script>

<script>
    function setExistingCover(index, btn) {
        // Reset semua tombol
        document.querySelectorAll('[data-cover-btn]').forEach(b => {
            b.classList.remove('btn-success');
            b.classList.add('btn-outline-secondary');
            b.innerHTML = '<i class="bi bi-star me-1"></i> Cover';
            b.style.background = 'rgba(255, 255, 255, 0.85)';
            b.style.color = '#333';
        });

        // Aktifkan tombol ini
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        btn.innerHTML = '<i class="bi bi-star-fill me-1"></i> Cover';
        btn.style.background = '';
        btn.style.color = '';

        // Update hidden input
        document.getElementById('coverExistingInput').value = index;
    }
    </script>

<script>
    function setExistingCover(index, btn) {
        // Reset semua tombol
        document.querySelectorAll('[data-cover-btn]').forEach(b => {
            b.classList.remove('btn-success');
            b.classList.add('btn-outline-secondary');
            b.innerHTML = '<i class="bi bi-star me-1"></i> Cover';
            b.style.background = 'rgba(255, 255, 255, 0.85)';
            b.style.color = '#333';
        });

        // Aktifkan tombol ini
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        btn.innerHTML = '<i class="bi bi-star-fill me-1"></i> Cover';
        btn.style.background = '';
        btn.style.color = '';

        // Update hidden input
        document.getElementById('coverExistingInput').value = index;
    }
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
      const provinceSelect   = document.getElementById('province');
      const citySelect       = document.getElementById('city');
      const kecamatanSelect  = document.getElementById('kecamatan');

      let lokasiData = [];

      // --- helpers ---
      const norm = s => (s || '').trim().toLowerCase();
      const titleCase = s => (s || '')
        .toLowerCase()
        .split(' ')
        .map(w => (w ? w[0].toUpperCase() + w.slice(1) : ''))
        .join(' ');
      const sortAZ = (a, b) => a.localeCompare(b, 'id', { sensitivity: 'base' });

      // "KOTA" dulu, lalu abjad
      const kotaSortKey = (raw) => {
        const n = raw.trim().toLowerCase();
        if (n.startsWith('kota'))      return '0' + raw;
        if (n.startsWith('kab'))       return '1' + raw;
        return '2' + raw;
      };
      const sortKotaFirst = (a, b) => sortAZ(kotaSortKey(a), kotaSortKey(b));

      // Ambil nilai lama (biar auto-select)
      const oldProvince = "{{ $property->provinsi ?? '' }}";
      const oldCity     = "{{ $property->kota ?? '' }}";
      const oldDistrict = "{{ $property->kecamatan ?? '' }}";

      // ---- fetch data ----
      fetch("{{ asset('data/indonesia.json') }}")
        .then(res => res.json())
        .then(data => {
          lokasiData = data;

          // === PROVINSI: unik + sort A-Z ===
          const provMap = new Map();
          data.forEach(it => {
            const key = norm(it.province);
            if (!provMap.has(key)) provMap.set(key, it.province.trim());
          });
          const provArr = Array.from(provMap.values()).sort(sortAZ);

          provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
          provArr.forEach(prov => {
            provinceSelect.innerHTML += `<option value="${prov}">${prov}</option>`;
          });

          // Set explicit value lama (case-insensitive)
          if (oldProvince) {
            const match = provArr.find(p => norm(p) === norm(oldProvince));
            if (match) provinceSelect.value = match;
          }

          // Populate chaining jika ada nilai lama
          if (provinceSelect.value) {
            populateCities(provinceSelect.value, () => {
              if (oldCity) {
                const matchCity = [...citySelect.options].find(opt => norm(opt.value) === norm(oldCity));
                if (matchCity) citySelect.value = matchCity.value;
              }
              if (citySelect.value) {
                populateKecamatan(provinceSelect.value, citySelect.value, () => {
                  if (oldDistrict) {
                    const matchKec = [...kecamatanSelect.options].find(opt => norm(opt.value) === norm(oldDistrict));
                    if (matchKec) kecamatanSelect.value = matchKec.value;
                  }
                });
              }
            });
          }
        });

      // === isi kota/kab berdasarkan provinsi ===
      function populateCities(provinsi, done = null) {
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kecamatanSelect.disabled = true;

        const kotaMap = new Map();
        lokasiData
          .filter(it => norm(it.province) === norm(provinsi))
          .forEach(it => {
            const raw = it.regency.trim();
            const key = norm(raw);
            if (!kotaMap.has(key)) kotaMap.set(key, raw);
          });

        const kotaArr = Array.from(kotaMap.values()).sort(sortKotaFirst);
        kotaArr.forEach(kota => {
          citySelect.innerHTML += `<option value="${kota}">${kota}</option>`;
        });

        citySelect.disabled = false;
        if (done) done();
      }

      // === isi kecamatan berdasarkan provinsi + kota ===
      function populateKecamatan(provinsi, kota, done = null) {
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

        const kecMap = new Map();
        lokasiData
          .filter(it => norm(it.province) === norm(provinsi) && norm(it.regency) === norm(kota))
          .forEach(it => {
            const raw = it.district.trim();
            const key = norm(raw);
            if (!kecMap.has(key)) kecMap.set(key, raw);
          });

        const kecArr = Array.from(kecMap.values())
          .map(raw => ({ value: raw, label: titleCase(raw) }))
          .sort((a, b) => sortAZ(a.label, b.label));

        kecArr.forEach(k => {
          kecamatanSelect.innerHTML += `<option value="${k.value}">${k.label}</option>`;
        });

        kecamatanSelect.disabled = false;
        if (done) done();
      }

      // === events ===
      provinceSelect.addEventListener('change', function () {
        const prov = this.value;
        citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        citySelect.disabled = true;
        kecamatanSelect.disabled = true;

        if (prov) populateCities(prov);
      });

      citySelect.addEventListener('change', function () {
        const prov = provinceSelect.value;
        const kota = this.value;
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kecamatanSelect.disabled = true;

        if (kota) populateKecamatan(prov, kota);
      });
    });
    </script>


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

    // === FORMAT HARGA ===
    const hargaInput = document.getElementById('harga');
    if (hargaInput) {
        hargaInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            if (!value) return this.value = '';
            this.value = new Intl.NumberFormat('id-ID').format(value);
        });
    }

    // === PROVINSI - KOTA DROPDOWN ===
    const provinsiSelect = document.getElementById('provinsi');
    const kotaSelect = document.getElementById('kota');

    fetch('{{ url("data/indonesia.json") }}')
        .then(response => response.json())
        .then(data => {
            Object.keys(data).forEach(prov => {
                const option = document.createElement('option');
                option.value = prov;
                option.textContent = prov;
                provinsiSelect.appendChild(option);
            });

            provinsiSelect.addEventListener('change', function () {
                const selectedProvinsi = this.value;
                kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
                if (selectedProvinsi && data[selectedProvinsi]) {
                    data[selectedProvinsi].forEach(kota => {
                        const option = document.createElement('option');
                        option.value = kota;
                        option.textContent = kota;
                        kotaSelect.appendChild(option);
                    });
                }
            });
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
    document.querySelector("form").addEventListener("submit", function(e) {
        let invalid = false;
        this.querySelectorAll("[required]").forEach(inp => {
            if (!inp.value.trim()) {
                inp.classList.add("is-invalid");
                invalid = true;
            } else {
                inp.classList.remove("is-invalid");
            }
        });
        if (invalid) {
            e.preventDefault();
            let firstError = this.querySelector(".is-invalid");
            if (firstError) {
                let tabPane = firstError.closest(".tab-pane");
                if (tabPane && tabPane.id) {
                    let trigger = document.querySelector(`[data-bs-target="#${tabPane.id}"]`);
                    if (trigger) new bootstrap.Tab(trigger).show();
                }
                firstError.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    });
    </script>

</section>
@include('template.footer')
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
                    <input type="number" name="kamar_tidur" class="form-control" min="0" placeholder="Contoh: 3" value="{{ old('kamar_tidur', $property->kamar_tidur) }}">
                </div>
            </div>

            <!-- Kamar Mandi -->
            <div class="col-md-4">
                <label class="form-label">Kamar Mandi <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-droplet"></i></span>
                    <input type="number" name="kamar_mandi" class="form-control" min="0" placeholder="Contoh: 2" value="{{ old('kamar_mandi', $property->kamar_mandi) }}">
                </div>
            </div>

            <!-- Jumlah Lantai -->
            <div class="col-md-4">
                <label class="form-label">Jumlah Lantai</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-layers"></i></span>
                    <input type="number" name="lantai" class="form-control" placeholder="Contoh: 2" value="{{ old('lantai', $property->lantai) }}">
                </div>
            </div>
        </div>

        <!-- Luas -->
        <div class="row g-3 mb-3">
            <!-- Luas Tanah -->
            <div class="col-md-6">
                <label class="form-label">Luas Tanah (m²)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-fullscreen"></i></span>
                    <input type="number" name="luas_tanah" class="form-control" placeholder="Contoh: 120" value="{{ old('luas_tanah', $property->luas_tanah) }}">
                </div>
            </div>

            <!-- Luas Bangunan -->
            <div class="col-md-6">
                <label class="form-label">Luas Bangunan (m²)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-aspect-ratio"></i></span>
                    <input type="number" name="luas_bangunan" class="form-control" placeholder="Contoh: 90" value="{{ old('luas_bangunan', $property->luas_bangunan) }}">
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
                            <input class="form-check-input" type="radio" name="orientation" id="ori_{{ $ori }}" value="{{ $ori }}" {{ old('orientation', $property->orientation) == $ori ? 'checked' : '' }}>
                            <label class="form-check-label" for="ori_{{ $ori }}">{{ ucfirst($ori) }}</label>
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
                            <option value="{{ $sertif }}" {{ old('sertifikat', $property->sertifikat) == $sertif ? 'selected' : '' }}>{{ $sertif }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div> --}}

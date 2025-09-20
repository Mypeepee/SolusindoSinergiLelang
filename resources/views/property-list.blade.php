@include('template.header')
@section('meta')
    <title>Jual {{ ucfirst($property_type) }} di {{ ucfirst($city) }} | Harga {{ $price_range }} | Solusindo Lelang</title>

    <meta name="description" content="Temukan {{ $property_type }} terbaik di {{ $city }}, {{ $province }}. Harga mulai {{ $price_range }}. Lelang resmi, aman, & transparan bersama Solusindo Lelang.">

    <meta name="keywords" content="jual {{ $property_type }} {{ $city }}, properti {{ $city }}, lelang rumah {{ $city }}, harga properti {{ $province }}, Solusindo Lelang">

    <meta property="og:title" content="Jual {{ $property_type }} di {{ $city }} - Harga {{ $price_range }}">
    <meta property="og:description" content="Cari {{ $property_type }} di {{ $city }} dengan harga mulai {{ $price_range }}. Lelang resmi & legal di Solusindo Lelang.">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Solusindo Lelang">
    <meta property="og:image" content="{{ $property->foto ?? asset('default.jpg') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Jual {{ $property_type }} di {{ $city }} - Harga {{ $price_range }}">
    <meta name="twitter:description" content="Temukan {{ $property_type }} murah di {{ $city }}. Lelang resmi, harga mulai {{ $price_range }}.">
    <meta name="twitter:image" content="{{ $property->foto ?? asset('default.jpg') }}">
@endsection


{{-- @section('structured_data')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "RealEstateListing",
        "name": "{{ $property->deskripsi }}",
        "price": "{{ number_format($property->harga, 0, ',', '.') }}",
        "priceCurrency": "IDR",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $property->lokasi }}",
            "addressLocality": "{{ $property->kota }}",
            "addressRegion": "{{ $property->provinsi }}"
        },
        "image": "{{ explode(',', $property->gambar)[0] }}",
        "url": "{{ route('property-detail', $property->id_listing) }}"
    }
    </script>
@endsection --}}

<!-- Header Start -->
<style>
    .header-banner {
        position: relative;
        width: 100%;
        height: 80vh;
        background: url('{{ asset('img/header.jpg') }}') center center / cover no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .header-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Overlay gelap */
        z-index: 1;
    }

    .header-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
        padding: 0 15px;
    }

    .header-content h1 {
        font-size: 3rem;
        font-weight: bold;
        color: white;
    }

    .breadcrumb-custom {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .breadcrumb-custom a {
        color: white;
        text-decoration: none;
    }

    .breadcrumb-custom span.active {
        color: #FF5722;
        font-weight: 700;
    }

    .breadcrumb-divider {
        color: white;
    }

    @media (max-width: 576px){

/* Pill search rapi & gak nembus */
.d-md-none .search-rail{
  display:flex; align-items:center; gap:.5rem;
  height:56px; width:100%;
  padding:8px 8px 8px 12px;
  border-radius:999px;
  overflow:hidden;                      /* cegah isi tembus */
  border:1.5px solid var(--rail-border);
  box-shadow:0 8px 24px rgba(0,0,0,.06);
  background:#fff;
}

/* icon kaca pembesar kiri */
.d-md-none .search-rail i.bi-search:first-child{
  flex:0 0 auto;
  font-size:1.15rem; color:#8a949f;
  margin-right:.25rem;
}

/* input fleksibel & boleh mengecil */
.d-md-none .search-rail-input{
  flex:1 1 auto;
  min-width:0;                          /* kunci: biar bisa menyusut */
  height:40px; font-size:1rem;
  border:0; outline:0; background:transparent;
}

/* grup tombol kanan (yang .d-flex itu) jangan menyusut */
.d-md-none .search-rail > .d-flex{
  flex:0 0 auto;
  gap:.5rem;
}

/* ukuran tombol: target sentuh 44px */
.d-md-none .search-rail .btn{
  flex:0 0 auto;
  width:44px; height:44px; padding:0;
  border-radius:999px;
  display:inline-flex; align-items:center; justify-content:center;
}

/* style tombol (tetap pakai var warna brand) */
.d-md-none .btn-search{
  background:var(--navy); color:#fff; border:0;
  transition:filter .15s ease, transform .04s ease;
}
.d-md-none .btn-search:hover{ filter:brightness(1.08); }
.d-md-none .btn-search:active{ transform:translateY(1px); }

.d-md-none .btn-filter{
  background:#fff; color:var(--brand-orange);
  border:2px solid var(--brand-orange);
  transition:background .15s ease, color .15s ease, border-color .15s ease;
}
.d-md-none .btn-filter:hover{
  background:var(--brand-orange); color:#fff; border-color:var(--brand-orange);
}
}

    body {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    .navbar {
        z-index: 9999;
    }
</style>

<div class="header-banner">
    <div class="header-content">
        <h1 class="mb-3">
            @if($property_type !== 'semua' && $city !== 'semua')
                Jual {{ ucfirst($property_type) }} di {{ ucfirst($city) }}
            @elseif($property_type !== 'semua' && $city === 'semua')
                Jual {{ ucfirst($property_type) }} di Indonesia
            @elseif($property_type === 'semua' && $city !== 'semua')
                Jual Properti di {{ ucfirst($city) }}
            @else
                Jual Properti Murah di Indonesia
            @endif
        </h1>

        <div class="breadcrumb-custom">
            <a href="/">Home</a>
            <span class="breadcrumb-divider">/</span>
            <a href="/properti">Properti</a>
            <span class="breadcrumb-divider">/</span>
            <span class="active">
                @if($property_type !== 'semua' && $city !== 'semua')
                    Jual {{ ucfirst($property_type) }} di {{ ucfirst($city) }}
                @elseif($property_type !== 'semua')
                    Jual {{ ucfirst($property_type) }} di Indonesia
                @elseif($city !== 'semua')
                    Jual Properti di {{ ucfirst($city) }}
                @else
                    Semua Properti
                @endif
            </span>
        </div>
    </div>
</div>


<!-- Header End -->


<!-- Tombol baru (UI konsisten seperti desktop) -->
<!-- Updated Mobile View for Search and Filter (Always Visible) -->
<div class="container-fluid bg-primary mb-5 wow fadeIn d-md-none" data-wow-delay="0.1s" style="padding: 35px;">
    <form id="searchForm" method="GET" action="{{ route('property.list') }}#property-list-section" class="search-hero">
        <div class="search-rail d-flex align-items-center">
            <i class="bi bi-search ms-3 me-2 text-muted fs-5"></i>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="search-rail-input flex-grow-1"
                placeholder="Cari lokasi / perumahan / jalan… (mis. 'Citraland')"
                autocomplete="off"
                aria-label="Ketik kata kunci pencarian"
            />
            <div class="d-flex align-items-center gap-2 ms-2">
                <button type="submit" class="btn btn-search px-4 fw-semibold">
                    <i class="bi bi-search"></i> <!-- Bootstrap icon for magnifying glass -->
                </button>
                <button type="button" class="btn btn-filter px-3 fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="bi bi-sliders"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Filter (Fullscreen on small screens) -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Pencarian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('property.list', [
    'property_type' => request('property_type', 'rumah'),
    'city' => request('city', 'semua'),  {{-- Default fallback ke 'semua' --}}
    'price_range' => 'harga-dibawah-' . str_replace('.', '', request('max_price', '0')),  {{-- Format harga --}}
    'land_size' => 'luas-tanah-max-' . str_replace('.', '', request('max_land_size', '0')),  {{-- Format luas tanah --}}
    'province' => request('province', 'semua'),  {{-- Provinsi --}}
    'page' => 1  {{-- Menambahkan page untuk pagination --}}
]) }}" method="GET">
                <div class="modal-body" style="max-height: calc(100vh - 150px); overflow-y: auto;">
                    <div class="container">
                        <div class="row g-2">

                            {{-- === Keyword search (lokasi / ID listing) === --}}
                            <div class="col-12">
                                <div class="input-group input-group-lg rounded-pill overflow-hidden shadow-sm mobile-search">
                                    <span class="input-group-text bg-white border-0 ps-3">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input
                                        type="search"
                                        name="q"
                                        value="{{ request('q') }}"
                                        class="form-control border-0"
                                        placeholder='Cari lokasi / perumahan / jalan… atau ID Listing (angka)'
                                        autocomplete="off"
                                        inputmode="search"
                                        aria-label="Ketik kata kunci pencarian"
                                    >
                                </div>
                                <div class="form-text mt-1">
                                    Contoh: <em>Citraland</em>, <em>Jl. Sudirman</em>, atau <em>12345</em> (ID Listing).
                                </div>
                            </div>
                            {{-- === /Keyword === --}}

                            <!-- Harga (Min–Max) dalam 1 row -->
                            <div class="col-12">
                                <label class="form-label mb-1">Harga (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="min_price" id="min_price"
                                        class="form-control js-num js-price"
                                        placeholder="Min" inputmode="numeric"
                                        value="{{ request('min_price') }}">
                                    <span class="input-group-text">–</span>
                                    <input type="text" name="max_price" id="max_price"
                                        class="form-control js-num js-price"
                                        placeholder="Max" inputmode="numeric"
                                        value="{{ request('max_price') }}">
                                </div>
                            </div>


                            <!-- Tipe Properti -->
                            <div class="col-12">
                                <select name="property_type" class="form-select border-0 py-3">
                                    <option selected disabled>Tipe Property</option>
                                    <option value="rumah">Rumah</option>
                                    <option value="gudang">Gudang</option>
                                    <option value="apartemen">Apartemen</option>
                                    <option value="tanah">Tanah</option>
                                    <option value="pabrik">Pabrik</option>
                                    <option value="hotel dan villa">Hotel dan Villa</option>
                                    <option value="ruko">Ruko</option>
                                    <option value="toko">Toko</option>
                                    <option value="lain-lain">Lain-lain</option>
                                </select>
                            </div>

                            <!-- Luas Tanah (Min–Max) dalam 1 row -->
<div class="col-12">
    <label class="form-label mb-1">Luas Tanah (m²)</label>
    <div class="input-group">
        <input type="text" name="min_land_size" id="min_land_size"
               class="form-control js-num js-area"
               placeholder="Min" inputmode="numeric"
               value="{{ request('min_land_size') }}"
               oninput="validateInteger(this)" onblur="formatNumber(this)">
        <span class="input-group-text">m²</span>
        <span class="input-group-text">–</span>
        <input type="text" name="max_land_size" id="max_land_size"
               class="form-control js-num js-area"
               placeholder="Max" inputmode="numeric"
               value="{{ request('max_land_size') }}"
               oninput="validateInteger(this)" onblur="formatNumber(this)">
        <span class="input-group-text">m²</span>
    </div>
</div>

                            <!-- Provinsi -->
                            <div class="col-12">
                                <select id="province" name="province" class="form-select border-0 py-3">
                                    <option selected disabled>Pilih Provinsi</option>
                                </select>
                            </div>

                            <!-- Kota -->
                            <div class="col-12">
                                <select id="city" class="form-select border-0 py-3" disabled>
                                    <option selected disabled>Pilih Kota/Kabupaten</option>
                                </select>
                            </div>

                            <!-- Kecamatan -->
                            <div class="col-12">
                                <select id="district" class="form-select border-0 py-3" disabled>
                                    <option selected disabled>Pilih Kecamatan</option>
                                </select>
                            </div>
                        </div>

                        <div id="selected-cities" class="mt-3 d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <!-- Fixed footer with sticky positioning -->
                <div class="modal-footer sticky-bottom d-flex w-100 gap-2">
                    <input type="hidden" name="selected_city_values" id="selected-city-values">

                    <div class="d-flex w-100 gap-2">
                      <button type="button" class="btn btn-secondary flex-fill py-3" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-dark flex-fill py-3">Search</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Fungsi untuk memastikan hanya angka yang bisa dimasukkan
    function validateInteger(input) {
        // Hapus semua karakter selain angka
        input.value = input.value.replace(/[^0-9]/g, '');
    }

    // Fungsi untuk format angka menjadi dengan pemisah ribuan (1.000, 10.000, etc.)
    function formatNumber(input) {
        // Hapus karakter selain angka
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            // Format angka menggunakan Intl.NumberFormat
            input.value = new Intl.NumberFormat('id-ID').format(value);
        }
    }

    // Sebelum form submit, hapus titik biar masuk ke DB sebagai angka bersih
    document.querySelector("form").addEventListener("submit", function () {
        document.querySelectorAll('input[name="min_land_size"], input[name="max_land_size"]').forEach(function (el) {
            el.value = el.value.replace(/\./g, "");  // Menghapus titik sebelum submit
        });
    });
</script>
<style>
    .mobile-search .form-control:focus { box-shadow:none; }
    .mobile-search .input-group-text { border:0; }
  </style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const districtSelect = document.getElementById('district');
        const selectedTagsContainer = document.getElementById('selected-cities'); // container buat tag
        const selectedTagsInput = document.getElementById('selected-city-values'); // hidden input

        let provinceMap = new Map();    // Provinsi => Set Kota
        let locationMap = new Map();    // Provinsi => Kota => Set Kecamatan
        let selectedTagList = [];       // List tag terpilih {value, type}

        citySelect.disabled = true;
        districtSelect.disabled = true;

        // Load data lokasi
        fetch("{{ asset('data/indonesia.json') }}")
            .then(res => res.json())
            .then(data => {
                data.forEach(item => {
                    const prov = item.province.trim();
                    const regency = item.regency.trim();
                    const district = item.district.trim();

                    // Provinsi -> Kota
                    if (!provinceMap.has(prov)) {
                        provinceMap.set(prov, new Set());
                    }
                    provinceMap.get(prov).add(regency);

                    // Provinsi -> Kota -> Kecamatan
                    if (!locationMap.has(prov)) {
                        locationMap.set(prov, new Map());
                    }
                    if (!locationMap.get(prov).has(regency)) {
                        locationMap.get(prov).set(regency, new Set());
                    }
                    locationMap.get(prov).get(regency).add(district);
                });

                // Urutkan provinsi secara abjad
                let sortedProvinces = Array.from(provinceMap.keys()).sort();
                sortedProvinces.forEach(prov => {
                    provinceSelect.innerHTML += `<option value="${prov}">${prov}</option>`;
                });

                // Event provinsi
                provinceSelect.addEventListener('change', function () {
                    updateCityDropdown(this.value, citySelect);
                });

                // Event kota
                citySelect.addEventListener('change', function () {
                    updateDistrictDropdown(provinceSelect.value, this.value);
                    addTag(this.value, 'city'); // Tambahkan tag kota
                });

                // Event kecamatan
                districtSelect.addEventListener('change', function () {
                    const city = citySelect.value;
                    addTag(`${city} - ${this.value}`, 'district'); // Tambahkan tag kecamatan
                });
            });

        // Fungsi untuk mengupdate dropdown kota sesuai provinsi yang dipilih
        function updateCityDropdown(selected, targetCityDropdown) {
            if (!selected) {
                targetCityDropdown.disabled = true;
                targetCityDropdown.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';
                districtSelect.disabled = true; // Disable kecamatan jika kota belum dipilih
                districtSelect.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
                return;
            }

            // Urutkan kota berdasarkan abjad dan prioritaskan Kota lebih dahulu
            const citySet = Array.from(provinceMap.get(selected)).sort((a, b) => {
                const isKotaA = a.startsWith("KOTA");
                const isKotaB = b.startsWith("KOTA");
                if (isKotaA && !isKotaB) return -1;
                if (!isKotaA && isKotaB) return 1;
                return a.localeCompare(b);
            });

            targetCityDropdown.disabled = false;  // Enable kota jika provinsi sudah dipilih
            targetCityDropdown.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';  // Tetap pertahankan opsi default
            citySet.forEach(c => {
                const cleanedValue = c.replace(/^Kota\s|^Kabupaten\s/, '');
                targetCityDropdown.innerHTML += `<option value="${c}">${c}</option>`;
            });

            // Reset kecamatan
            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
        }

        // Fungsi untuk mengupdate dropdown kecamatan sesuai kota yang dipilih
        function updateDistrictDropdown(prov, selectedCity) {
            const districtSet = locationMap.get(prov).get(selectedCity);
            const sortedDistricts = Array.from(districtSet).sort();

            districtSelect.disabled = false;
            districtSelect.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
            sortedDistricts.forEach(d => {
                districtSelect.innerHTML += `<option value="${d}">${d}</option>`;
            });
        }

        // Render tag
        function renderTags() {
            selectedTagsContainer.innerHTML = '';
            selectedTagList.forEach(tag => {
                const tagEl = document.createElement('div');
                tagEl.className = 'city-tag';
                tagEl.innerHTML = `${tag.value} <span class="remove-tag" data-value="${tag.value}" data-type="${tag.type}">&times;</span>`;
                selectedTagsContainer.appendChild(tagEl);
            });
            selectedTagsInput.value = selectedTagList.map(t => t.value).join(',');
        }

        // Tambah tag (cek duplikat berdasarkan value + type)
        function addTag(value, type) {
            if (type === 'district') {
                const cityName = value.split(' - ')[0].trim();
                // Hapus tag kota dengan nama yang sama
                selectedTagList = selectedTagList.filter(t => !(t.type === 'city' && t.value === cityName));
            }

            if (!selectedTagList.find(t => t.value === value && t.type === type)) {
                selectedTagList.push({ value, type });
                renderTags();
            }
        }

        // Hapus tag
        selectedTagsContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-tag')) {
                const value = e.target.dataset.value;
                const type = e.target.dataset.type;
                selectedTagList = selectedTagList.filter(t => !(t.value === value && t.type === type));
                renderTags();
            }
        });
    });

</script>


<!-- Desktop View Original Search Form (Visible Only on md and Up) -->
<div class="container-fluid bg-primary mb-5 wow fadeIn d-none d-md-block" data-wow-delay="0.1s" style="padding: 35px;">
    <form id="searchForm" method="GET" action="{{ route('property.list') }}#property-list-section" class="search-hero">

        {{-- Keyword bar + buttons (replaces your input-group) --}}
        <div class="search-rail d-flex align-items-center">
            <i class="bi bi-search ms-3 me-2 text-muted fs-5"></i>

            <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            class="search-rail-input flex-grow-1"
            placeholder='Cari lokasi / perumahan / jalan… (mis. "Citraland")'
            autocomplete="off"
            aria-label="Ketik kata kunci pencarian"
            />

            <div class="d-flex align-items-center gap-2 ms-2 me-2">
            <button type="submit" class="btn btn-search px-4 fw-semibold">Search</button>
            <button type="button" class="btn btn-filter px-3 fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-sliders"></i><span class="ms-2 d-none d-lg-inline">Filter</span>
            </button>
            </div>
        </div>
      </form>
</div>
<style>
/* ====== palette */
:root{
  --brand-orange: #f15b2a;      /* brand/oranye */
  --navy:         #0f2a44;      /* dark blue tombol Search */
  --rail-ring:    rgba(15,42,68,.12);
  --rail-border:  #ffd3c2;      /* garis halus pill */
}

/* ====== pill putih biar kontras di bg oranye */
.search-rail{
  height:64px;
  width:100%;
  background:#fff;
  border:2px solid var(--brand-orange);
  border-radius:40px;
  box-shadow: 0 8px 24px rgba(0,0,0,.06);
  padding-right:.5rem;
  transition: box-shadow .18s ease, border-color .18s ease;
}
.search-rail:focus-within{
  box-shadow: 0 0 0 8px var(--rail-ring), 0 10px 26px rgba(0,0,0,.08);
  border-color: var(--brand-orange);
}

/* input minimal & enak dibaca */
.search-rail-input{
  border:0; outline:0; background:transparent;
  height:60px; font-size:1.1rem;
}
.search-rail-input::placeholder{ color:#9aa0a6; }

/* tombol utama (dark blue) */
.btn-search{
  background: var(--navy); color:#fff; border:none;
  height:46px; border-radius:24px;
  transition: filter .15s ease, transform .04s ease;
}
.btn-search:hover{ filter:brightness(1.08); }
.btn-search:active{ transform:translateY(1px); }

/* tombol sekunder (orange outline, invert saat hover) */
.btn-filter{
  background:#fff; color:var(--brand-orange);
  border:2px solid var(--brand-orange);
  height:46px; border-radius:24px;
  transition: background .15s ease, color .15s ease, border-color .15s ease;
}
.btn-filter:hover{
  background:var(--brand-orange); color:#fff; border-color:var(--brand-orange);
}

/* kecilkan di layar sedang biar proporsional */
@media (max-width: 1400px){
  .search-rail{ height:60px; }
  .search-rail-input{ height:56px; font-size:1rem; }
  .btn-search,.btn-filter{ height:42px; }
}

    </style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinceDesktop = document.getElementById('province-desktop');
        const cityDesktop = document.getElementById('city-desktop');
        const districtDesktop = document.getElementById('district-desktop');
        const selectedCitiesDesktop = document.getElementById('selected-cities-desktop');
        const selectedCityValuesDesktop = document.getElementById('selected-city-values-desktop');

        let provinceMap = new Map();    // Provinsi => Set Kota
        let locationMap = new Map();    // Provinsi => Kota => Set Kecamatan
        let selectedTags = []; // List tag terpilih (punya value + type)

        // Load data lokasi
        fetch("{{ asset('data/indonesia.json') }}")
            .then(res => res.json())
            .then(data => {
                data.forEach(item => {
                    const prov = item.province.trim();
                    const regency = item.regency.trim();
                    const district = item.district.trim();

                    // Provinsi -> Kota
                    if (!provinceMap.has(prov)) {
                        provinceMap.set(prov, new Set());
                    }
                    provinceMap.get(prov).add(regency);

                    // Provinsi -> Kota -> Kecamatan
                    if (!locationMap.has(prov)) {
                        locationMap.set(prov, new Map());
                    }
                    if (!locationMap.get(prov).has(regency)) {
                        locationMap.get(prov).set(regency, new Set());
                    }
                    locationMap.get(prov).get(regency).add(district);
                });

                // Isi provinsi
                for (let prov of provinceMap.keys()) {
                    provinceDesktop.innerHTML += `<option value="${prov}">${prov}</option>`;
                }

                // Provinsi change
                provinceDesktop.addEventListener('change', function () {
                    updateCityDropdown(this.value, cityDesktop);
                });

                // Kota change
                cityDesktop.addEventListener('change', function () {
                    updateDistrictDropdown(provinceDesktop.value, this.value);
                    addTag(this.value, 'city'); // Tambahkan tag kota
                });

                // Kecamatan change
                districtDesktop.addEventListener('change', function () {
                    const city = cityDesktop.value;
                    addTag(`${city} - ${this.value}`, 'district'); // Tambahkan tag kecamatan
                });
            });

        function updateCityDropdown(selected, targetCityDropdown) {
            const citySet = provinceMap.get(selected);
            targetCityDropdown.disabled = false;
            targetCityDropdown.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';
            citySet.forEach(c => {
                const cleanedValue = c.replace(/^Kota\s|^Kabupaten\s/, '');
                targetCityDropdown.innerHTML += `<option value="${c}">${c}</option>`;
            });

            // Reset kecamatan
            districtDesktop.disabled = true;
            districtDesktop.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
        }

        function updateDistrictDropdown(prov, selectedCity) {
            const districtSet = locationMap.get(prov).get(selectedCity);
            districtDesktop.disabled = false;
            districtDesktop.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
            districtSet.forEach(d => {
                districtDesktop.innerHTML += `<option value="${d}">${d}</option>`;
            });
        }

        // Render tag
        function renderTags() {
            selectedCitiesDesktop.innerHTML = '';
            selectedTags.forEach(tag => {
                const tagEl = document.createElement('div');
                tagEl.className = 'city-tag';
                tagEl.innerHTML = `${tag.value} <span class="remove-tag" data-value="${tag.value}" data-type="${tag.type}">&times;</span>`;
                selectedCitiesDesktop.appendChild(tagEl);
            });
            selectedCityValuesDesktop.value = selectedTags.map(t => t.value).join(',');
        }

        // Tambah tag (cek duplikat berdasarkan value + type)
        function addTag(value, type) {
    if (type === 'district') {
        const cityName = value.split(' - ')[0].trim();
        // Hapus tag kota dengan nama yang sama
        selectedTags = selectedTags.filter(t => !(t.type === 'city' && t.value === cityName));
    }

    if (!selectedTags.find(t => t.value === value && t.type === type)) {
        selectedTags.push({ value, type });
        renderTags();
    }
}


        // Hapus tag
        selectedCitiesDesktop.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-tag')) {
                const value = e.target.dataset.value;
                const type = e.target.dataset.type;
                selectedTags = selectedTags.filter(t => !(t.value === value && t.type === type));
                renderTags();
            }
        });
    });
    </script>


<!-- Styling Tag Kota (Tetap Berlaku di Modal dan Desktop) -->
<style>
    #selected-cities, #selected-cities-desktop {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .city-tag {
        background-color: #ffffff;
        color: #000000;
        border: 1px solid #ccc;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    .city-tag .remove-tag {
        cursor: pointer;
        font-weight: bold;
        margin-left: 8px;
        color: red;
    }
</style>

<!-- Script -->
<script>

function formatNumberInput(input) {
        // Hapus semua karakter kecuali angka
        let value = input.value.replace(/\D/g, "");
        if (value === "") {
            input.value = "";
            return;
        }
        // Format ke ribuan dengan titik
        input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Terapkan ke semua input harga
    document.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(function (el) {
        el.addEventListener("input", function () {
            formatNumberInput(this);
        });
    });

    // Optional: sebelum form submit, hapus titik biar masuk ke DB sebagai angka bersih
    document.querySelector("form").addEventListener("submit", function () {
        document.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(function (el) {
            el.value = el.value.replace(/\./g, "");
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const provinceMobile = document.getElementById('province');
        const cityMobile = document.getElementById('city');
        const selectedCities = document.getElementById('selected-cities');
        const selectedCityValues = document.getElementById('selected-city-values');
        const selectedCityList = [];

        const provinceDesktop = document.getElementById('province-desktop');
        const cityDesktop = document.getElementById('city-desktop');
        const selectedCitiesDesktop = document.getElementById('selected-cities-desktop');
        const selectedCityValuesDesktop = document.getElementById('selected-city-values-desktop');
        const selectedCityListDesktop = [];

        let provinceMap = new Map(); // Map provinsi => Set kota

        // Load data lokasi
        fetch("{{ asset('data/indonesia.json') }}")
            .then(res => res.json())
            .then(data => {
                data.forEach(item => {
                    const prov = item.province;
                    const regency = item.regency;

                    if (!provinceMap.has(prov)) {
                        provinceMap.set(prov, new Set());
                    }
                    provinceMap.get(prov).add(regency);
                });

                // Populate both dropdowns
                for (let prov of provinceMap.keys()) {
                    provinceMobile.innerHTML += `<option value="${prov}">${prov}</option>`;
                    provinceDesktop.innerHTML += `<option value="${prov}">${prov}</option>`;
                }

                // Mobile - provinsi change
                provinceMobile.addEventListener('change', function () {
                    updateCityDropdown(this.value, cityMobile);
                });

                // Desktop - provinsi change
                provinceDesktop.addEventListener('change', function () {
                    updateCityDropdown(this.value, cityDesktop);
                });
            });

        function updateCityDropdown(selected, targetCityDropdown) {
            const citySet = provinceMap.get(selected);

            targetCityDropdown.disabled = false;
            targetCityDropdown.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';

            citySet.forEach(c => {
                const cleanedValue = c.replace(/^Kota\s|^Kabupaten\s/, '');
                targetCityDropdown.innerHTML += `<option value="${cleanedValue}">${c}</option>`;
            });
        }

        function updateDistrictDropdown(prov, selectedCity) {
        const districtSet = locationMap.get(prov).get(selectedCity);
        districtDesktop.disabled = false;
        districtDesktop.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
        districtSet.forEach(d => {
            districtDesktop.innerHTML += `<option value="${d}">${d}</option>`;
        });
    }

        // Mobile - pilih kota
        cityMobile.addEventListener('change', function () {
            const selectedCity = this.value;
            if (!selectedCityList.includes(selectedCity)) {
                selectedCityList.push(selectedCity);
                renderSelectedCities(selectedCities, selectedCityValues, selectedCityList);
            }
        });

        // Desktop - pilih kota
        cityDesktop.addEventListener('change', function () {
            const selectedCity = this.value;
            if (!selectedCityListDesktop.includes(selectedCity)) {
                selectedCityListDesktop.push(selectedCity);
                renderSelectedCities(selectedCitiesDesktop, selectedCityValuesDesktop, selectedCityListDesktop);
            }
        });

        // Render tag kota
        function renderSelectedCities(container, hiddenInput, cityList) {
            container.innerHTML = '';
            cityList.forEach(city => {
                const tag = document.createElement('div');
                tag.className = 'city-tag';
                tag.innerHTML = `${city} <span class="remove-tag" data-city="${city}">&times;</span>`;
                container.appendChild(tag);
            });
            hiddenInput.value = cityList.join(',');
        }

        // Remove tag - mobile
        selectedCities.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-tag')) {
                const cityToRemove = e.target.dataset.city;
                const index = selectedCityList.indexOf(cityToRemove);
                if (index > -1) {
                    selectedCityList.splice(index, 1);
                    renderSelectedCities(selectedCities, selectedCityValues, selectedCityList);
                }
            }
        });

        // Remove tag - desktop
        selectedCitiesDesktop.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-tag')) {
                const cityToRemove = e.target.dataset.city;
                const index = selectedCityListDesktop.indexOf(cityToRemove);
                if (index > -1) {
                    selectedCityListDesktop.splice(index, 1);
                    renderSelectedCities(selectedCitiesDesktop, selectedCityValuesDesktop, selectedCityListDesktop);
                }
            }
        });

        // Format harga (semua min_price dan max_price yang ditemukan)
        document.querySelectorAll('#min_price, #max_price').forEach(input => {
            input.addEventListener('input', function () {
                let angka = this.value.replace(/\D/g, '');
                this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            });
        });
    });
    </script>

<div class="alert alert-info">
    Menampilkan <strong>{{ $properties->total() }}</strong> properti
    @if (!empty($selectedTags))
        di {{ implode(', ', $selectedTags) }}
    @endif
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Cek apakah ada query string 'q' (search)
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('q');

        if (searchQuery) {
            // Jika ada pencarian, scroll ke bagian daftar properti
            const propertyList = document.getElementById("property-list-section");
            if (propertyList) {
                propertyList.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        }
    });
</script>

<div id="property-list-section" class="container-xxl py-5">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="text-start mx-auto mb-4 wow slideInLeft" data-wow-delay="0.1s">
                    <h1 class="mb-3">Jelajahi Beragam Tipe Properti</h1>
                    <p>Temukan pilihan properti terbaik mulai dari rumah lelang murah, properti sewa strategis, hingga gudang investasi. Semua ada di sini untuk kebutuhan dan rencana finansialmu.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex flex-wrap justify-content-start justify-content-lg-end gap-2 mb-4">
                    <!-- Date Toggle Buttons (visible when sorting by date) -->
<!-- Date Toggle Buttons (visible when sorting by date) -->
<div id="date-toggle" class="{{ request('sort') === 'tanggal_terdekat' ? '' : 'd-none' }}">
    <button class="btn btn-date-toggle {{ request('sort') === 'tanggal_sekarang' ? 'active' : '' }}" id="from-now">Dari Tanggal Sekarang</button>
    <button class="btn btn-date-toggle {{ request('sort') === 'semua' ? 'active' : '' }}" id="all">Semua</button>
</div>

<!-- Sort Dropdown -->
<div class="dropdown">
    <button class="btn btn-custom dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <span id="selectedSortOption">Urutkan</span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
        <!-- Sorting Options -->
        <li>
            <a class="dropdown-item {{ request('sort') === 'harga_asc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'harga_asc']) }}">
                Dari Harga Paling Rendah
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ request('sort') === 'harga_desc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'harga_desc']) }}">
                Dari Harga Paling Tinggi
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ request('sort') === 'tanggal_terdekat' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal_terdekat']) }}">
                Dari Tanggal Lelang Terdekat
            </a>
        </li>
        <li>
            <a class="dropdown-item {{ request('sort') === 'tanggal_terjauh' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'tanggal_terjauh']) }}">
                Dari Tanggal Lelang Terjauh
            </a>
        </li>
    </ul>
</div>
                </div>
            </div>

        <div class="row g-4">
            <script>
document.addEventListener('DOMContentLoaded', function () {
    const dateToggleContainer = document.getElementById('date-toggle');
    const fromNowButton = document.getElementById('from-now');
    const allButton = document.getElementById('all');
    const selectedSortOption = document.getElementById('selectedSortOption');

    // Tampilkan tombol "Dari Tanggal Sekarang" dan "Semua" saat "Dari Tanggal Lelang Terdekat" dipilih
    const dateOptionButton = document.querySelector('a[href*="tanggal_terdekat"]');
    if (dateOptionButton) {
        dateOptionButton.addEventListener('click', function () {
            dateToggleContainer.classList.remove('d-none');
            // Menetapkan tombol "Dari Tanggal Sekarang" aktif ketika filter tanggal terdekat dipilih
            fromNowButton.classList.add('active');
            allButton.classList.remove('active');
        });
    }

    // Sembunyikan tombol "Dari Tanggal Sekarang" dan "Semua" jika opsi lain dipilih
    const otherSortOptions = document.querySelectorAll('.dropdown-item:not([href*="tanggal_terdekat"])');
    otherSortOptions.forEach(option => {
        option.addEventListener('click', function () {
            dateToggleContainer.classList.add('d-none');
            fromNowButton.classList.remove('active');
            allButton.classList.remove('active');
        });
    });

    // Menangani klik tombol "Dari Tanggal Sekarang"
    if (fromNowButton) {
        fromNowButton.addEventListener('click', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', 'tanggal_sekarang');
            window.location.href = url.toString();
        });
    }

    // Menangani klik tombol "Semua"
    if (allButton) {
        allButton.addEventListener('click', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', 'semua');
            window.location.href = url.toString();
        });
    }

    // Memperbarui dropdown sesuai dengan pilihan yang ada di URL
    if (selectedSortOption) {
        const currentSort = new URLSearchParams(window.location.search).get('sort');

        // Mengubah teks tombol dropdown sesuai dengan filter yang dipilih
        if (currentSort === 'harga_asc') {
            selectedSortOption.textContent = 'Dari Harga Paling Rendah';
        } else if (currentSort === 'harga_desc') {
            selectedSortOption.textContent = 'Dari Harga Paling Tinggi';
        } else if (currentSort === 'tanggal_terdekat') {
            selectedSortOption.textContent = 'Dari Tanggal Lelang Terdekat';
            // Tombol "Dari Tanggal Sekarang" aktif
            fromNowButton.classList.add('active');
            allButton.classList.remove('active');
        } else if (currentSort === 'tanggal_terjauh') {
            selectedSortOption.textContent = 'Dari Tanggal Lelang Terjauh';
        } else if (currentSort === 'tanggal_sekarang') {
            selectedSortOption.textContent = 'Dari Tanggal Sekarang';
            fromNowButton.classList.add('active');
            allButton.classList.remove('active');
        }
         else {
            selectedSortOption.textContent = 'Urutkan';  // Tampilkan "Urutkan" jika belum ada pilihan
        }
    }
});

                </script>
            <style>
/* Style for the 'Dari Tanggal Sekarang' and 'Semua' toggle buttons */
#date-toggle {
    display: flex;
    gap: 5px;
}
/* --primary: #dc3545;
    --secondary: #f35525;
    --light: #fdefef;
    --dark: #0E2E50; */
.btn-date-toggle {
    background-color: #f35525; /* Use a color similar to Solusindo Lelang's theme (blue) */
    color: white;
    padding: 8px 20px;
    font-weight: 600;
    border-radius: 30px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-date-toggle.active {
    background-color: #f35525; /* Red background for active option */
    color: white;
}

.btn-date-toggle:hover {
    background-color: #dc3545; /* Darker blue on hover */
}

/* Ensure the toggle buttons are hidden by default */
.d-none {
    display: none;
}

/* Style for the dropdown button */
.btn-custom {
    background-color: transparent;
    color: #333;
    border: 2px solid #008CBA; /* Match the website's primary color */
    border-radius: 30px;
    font-weight: 600;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Custom dropdown menu style */
.dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Dropdown item style */
.dropdown-item {
    font-size: 14px;
    padding: 10px 20px;
    color: #333;
}

/* Hover effect for dropdown items */
.dropdown-item:hover {
    background-color: #f1f1f1;
    color: #008CBA;
}

/* Active state for dropdown items */
.dropdown-item.active {
    background-color: #f53b57;
    color: black;
}

/* Chevron icon style */
.bi-chevron-down {
    font-size: 14px;
}


                /* Custom button style for dropdown */
.btn-custom {
    background-color: transparent; /* Transparent background */
    color: #333; /* Black text */
    border: 2px solid #d74949; /* Blue border for better visibility */
    border-radius: 30px; /* Rounded corners */
    font-weight: 600; /* Slightly bold font */
    padding: 10px 20px; /* Padding for the button */
    display: flex;
    align-items: center;
    gap: 8px; /* Space between the text and the icon */
}

/* Button text color when option is selected */
#selectedSortOption {
    color: #333; /* Black color for selected option text */
}

/* Custom style for the dropdown menu */
.dropdown-menu {
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
}

/* Custom item styles for the dropdown */
.dropdown-item {
    font-size: 14px; /* Adjusted font size */
    padding: 10px 20px; /* Padding for better spacing */
    color: #333; /* Black text */
}

/* Hover effect for the dropdown items */
.dropdown-item:hover {
    background-color: #f1f1f1; /* Light gray background on hover */
    color: #d9534f; /* Red text color on hover */
}

/* Active state for the selected option */
.dropdown-item.active {
    background-color: #d9534f; /* Red background for active item */
    color: black; /* Keep the text black when selected */
}

/* Down arrow icon */
.bi-chevron-down {
    font-size: 14px; /* Icon size */
    margin-left: 5px; /* Space between text and icon */
}

                .property-item {
                    display: flex;
                    flex-direction: column;
                }

                .property-item img {
                    width: 100%;
                    height: 300px;
                    object-fit: cover;
                    object-position: center;
                }

                .property-item .p-4 {
                    flex-grow: 1;
                }

                @media (max-width: 576px) {
                    .property-item img {
                        height: 220px;
                    }

                    .property-item h5, .property-item a.d-block {
                        font-size: 1rem;
                    }

                    .property-item .text-primary.mb-3 {
                        font-size: 1rem;
                    }
                }
                .property-image-wrapper img {
                    width: 100%;
                    aspect-ratio: 4 / 3;
                    object-fit: cover;
                    border-radius: 8px;
                }
            </style>

@foreach ($properties as $property)
<div class="col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch">
  <div class="property-item rounded overflow-hidden flex-fill d-flex flex-column">
    <div class="position-relative overflow-hidden property-image-wrapper">
      <a href="{{ route('property-detail', $property->id_listing) }}">
        <img src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy" class="w-100 h-auto">
      </a>

      <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-2 py-1 px-3 text-capitalize">
        {{ $property->tipe }}
      </div>
      <div class="bg-primary rounded text-white position-absolute end-0 top-0 m-2 py-1 px-3">
        ID: {{ $property->id_listing }}
      </div>

      {{-- Fade putih tipis di bawah gambar biar nyatu ke area konten --}}
      <div class="img-bottom-fade"></div>


      {{-- CHIP AGENT: kanan-bawah, ukuran terkunci 26×26 --}}
{{-- @if(!empty($property->agent_nama) || !empty($property->agent_picture))
@php
  $fileId   = $property->agent_picture;
  $agentImg = $fileId
    ? 'https://drive.google.com/thumbnail?id='.$fileId.'&sz=w64'   // endpoint yang kamu pakai di halaman agent
    : asset('images/default-profile.png');
  $agentAlt = $fileId
    ? 'https://drive.google.com/uc?export=view&id='.$fileId        // fallback kedua
    : asset('images/default-profile.png');
@endphp

<div class="position-absolute end-0 bottom-0 m-2 agent-chip-wrap">
  <div class="d-flex align-items-center shadow-sm rounded-pill px-2 py-1 agent-chip">
    <div class="agent-avatar rounded-circle overflow-hidden me-2">
      <img
        src="{{ $agentImg }}"
        alt="{{ $property->agent_nama ?? 'Agent' }}"
        class="w-100 h-100"
        style="object-fit:cover;"
        referrerpolicy="no-referrer"
        onerror="if(this.dataset.step!=='1'){this.dataset.step='1';this.src='{{ $agentAlt }}';}else{this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';}"
      >
    </div>
    <span class="small fw-semibold text-dark agent-chip-name">
      {{ \Illuminate\Support\Str::limit($property->agent_nama ?? '—', 18) }}
    </span>
  </div>
</div>
@endif --}}

    </div>

    <div class="p-4 pb-0">
      <h5 class="text-primary mb-3">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
      <a class="d-block h5 mb-2" href="{{ route('property-detail', $property->id_listing) }}">
        {{ \Illuminate\Support\Str::limit($property->deskripsi, 50, '...') }}
      </a>
      <p>
        <i class="fa fa-map-marker-alt text-primary me-2"></i>
        {{ \Illuminate\Support\Str::limit($property->lokasi, 70, '...') }}
      </p>
    </div>

    <div class="d-flex border-top border-2 border-dashed border-orange mt-auto">
      <small class="flex-fill text-center border-end border-dashed py-2">
        <i class="fa fa-vector-square text-danger me-2"></i>
        <span class="text-dark">{{ $property->luas }} m²</span>
      </small>
      <small class="flex-fill text-center border-end border-dashed py-2">
        <i class="fa fa-map-marker-alt text-danger me-2"></i>
        <span class="text-dark text-uppercase">{{ $property->kota }}</span>
      </small>
      <small class="flex-fill text-center py-2">
        <i class="fa fa-calendar-alt text-danger me-2"></i>
        <span class="text-dark">
          {{ \Carbon\Carbon::parse($property->batas_akhir_penawaran)->format('d M Y') }}
        </span>
      </small>
    </div>
  </div>
</div>
@endforeach

<style>
      /* sudah ada */
  .img-bottom-fade{
    position:absolute;left:0;right:0;bottom:0;height:44px;
    background:linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,.85) 70%, #fff 100%);
    pointer-events:none;
  }
  .agent-chip{ background:rgba(255,255,255,.95); backdrop-filter:blur(2px); }

  /* >>> fix ukuran avatar supaya gak kena rule img global */
  .property-image-wrapper .agent-avatar{ width:26px; height:26px; flex:0 0 26px; }
  .property-image-wrapper .agent-chip-name{
    max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }

  /* jaga-jaga jika ada rule .property-image-wrapper img { width:100%!important } */
  .property-image-wrapper .agent-avatar img{
    width:100% !important; height:100% !important; object-fit:cover !important;
    border-radius:50% !important; display:block;
  }
  .img-bottom-fade{
    position:absolute;left:0;right:0;bottom:0;height:44px;
    background:linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,.85) 70%, #fff 100%);
    pointer-events:none;
  }
  .agent-chip{
    background:rgba(255,255,255,.95);
    backdrop-filter:blur(2px);
  }
  /* Biar nama agent rapi kalau kepanjangan */
  .agent-chip-name{
    max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
</style>



            <!-- Pagination -->
            <div class="col-12">
                <div class="pagination d-flex justify-content-center mt-5">
                    {{-- Previous --}}
                    @if ($properties->onFirstPage())
                        <a class="rounded disabled">&laquo;</a>
                    @else
                        <a href="{{ $properties->appends(request()->query())->previousPageUrl() }}" class="rounded">&laquo;</a>
                    @endif

                    {{-- Pages --}}
                    @php
                        $currentPage = $properties->currentPage();
                        $lastPage = $properties->lastPage();
                        $start = max($currentPage - 3, 1);
                        $end = min($currentPage + 3, $lastPage);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $properties->appends(request()->query())->url(1) }}" class="rounded">1</a>
                        @if ($start > 2)
                            <span class="rounded disabled">...</span>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        <a href="{{ $properties->appends(request()->query())->url($i) }}"
                            class="rounded {{ $i === $currentPage ? 'active' : '' }}">{{ $i }}</a>
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="rounded disabled">...</span>
                        @endif
                        <a href="{{ $properties->appends(request()->query())->url($lastPage) }}" class="rounded">{{ $lastPage }}</a>
                    @endif

                    {{-- Next --}}
                    @if ($properties->hasMorePages())
                        <a href="{{ $properties->appends(request()->query())->nextPageUrl() }}" class="rounded">&raquo;</a>
                    @else
                        <a class="rounded disabled">&raquo;</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Property List End -->



        <!-- Call to Action Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="bg-light rounded p-3">
                    <div class="bg-white rounded p-4" style="border: 1px dashed rgba(0, 185, 142, .3)">
                        <div class="row g-5 align-items-center">
                            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                                <img class="img-fluid rounded w-100" src="img/call-to-action.jpg" alt="">
                            </div>
                            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                                <div class="mb-4">
                                    <h1 class="mb-3">Hubungi Agen Professional Kami!</h1>
                                    <p>Hilangkan semua kekhawatiran anda mengenai dunia lelang. Informasi selengkapnya mohon hubungi Agen Professional Kami!. </p>
                                </div>
                                <a href="https://wa.me/6281335716679" class="btn btn-primary py-3 px-4 me-2"><i class="fa fa-phone-alt me-2"></i>Whatsapp</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Call to Action End -->


        @include('template.footer')

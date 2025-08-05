@include('template.header')
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

    @media (max-width: 768px) {
        .header-banner {
            height: 60vh;
        }

        .header-content h1 {
            font-size: 2rem;
        }

        .breadcrumb-custom {
            font-size: 0.85rem;
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
        <h1 class="mb-3">List Property</h1>
        <div class="breadcrumb-custom">
            <a href="/">HOME</a>
            <span class="breadcrumb-divider">/</span>
            <a href="#">PROPERTY</a>
            <span class="breadcrumb-divider">/</span>
            <span class="active">LIST PROPERTY</span>
        </div>
    </div>
</div>
<!-- Header End -->

<!-- Tombol baru (UI konsisten seperti desktop) -->
<button type="button" class="btn btn-dark w-100 py-3 d-md-none mb-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#filterModal">
    Klik Untuk Filter Pencarian
</button>

<!-- Modal Filter (Fullscreen on small screens) -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Pencarian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('property.list') }}#property-list-section" method="GET">
                <div class="modal-body">
                    <div class="container">
                        <div class="row g-2">
                            <!-- Harga Minimum -->
                            <div class="col-12">
                                <input type="text" name="min_price" id="min_price" class="form-control border-0 py-3" placeholder="Harga Minimum">
                            </div>

                            <!-- Harga Maksimum -->
                            <div class="col-12">
                                <input type="text" name="max_price" id="max_price" class="form-control border-0 py-3" placeholder="Harga Maksimum">
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
                                    <option value="sewa">Sewa</option>
                                </select>
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

                <div class="modal-footer flex-column">
                    <input type="hidden" name="selected_city_values" id="selected-city-values">

                    <!-- Tombol Search -->
                    <button type="submit" class="btn btn-dark w-100 py-3 mb-2">
                        Search
                    </button>

                    <!-- Tombol Close -->
                    <button type="button" class="btn btn-secondary w-100 py-3" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
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

                // Isi dropdown provinsi
                for (let prov of provinceMap.keys()) {
                    provinceSelect.innerHTML += `<option value="${prov}">${prov}</option>`;
                }

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
                    addTag(`${city} - ${this.value}`, 'district'); // Tambahkan tag kota + kecamatan
                });
            });

        function updateCityDropdown(selectedProv, targetCityDropdown) {
            const citySet = provinceMap.get(selectedProv);
            targetCityDropdown.disabled = false;
            targetCityDropdown.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';
            citySet.forEach(c => {
                const cleanedValue = c.replace(/^Kota\s|^Kabupaten\s/, '');
                targetCityDropdown.innerHTML += `<option value="${c}">${c}</option>`;
            });

            // Reset kecamatan
            districtSelect.disabled = true;
            districtSelect.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
        }

        function updateDistrictDropdown(prov, selectedCity) {
            const districtSet = locationMap.get(prov).get(selectedCity);
            districtSelect.disabled = false;
            districtSelect.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
            districtSet.forEach(d => {
                districtSelect.innerHTML += `<option value="${d}">${d}</option>`;
            });
        }

        // Render tag
        function renderTags() {
            selectedTagsContainer.innerHTML = '';
            selectedTagList.forEach(tag => {
                const tagElement = document.createElement('div');
                tagElement.className = 'city-tag';
                tagElement.innerHTML = `${tag.value} <span class="remove-tag" data-value="${tag.value}" data-type="${tag.type}">&times;</span>`;
                selectedTagsContainer.appendChild(tagElement);
            });
            selectedTagsInput.value = selectedTagList.map(t => t.value).join(',');
        }

        // Tambah tag (hindari duplikat berdasarkan value+type)
        function addTag(value, type) {
            if (type === 'district') {
                // Ambil nama kota dari format "KOTA SURABAYA - Tandes"
                const cityName = value.split(' - ')[0].trim();
                // Hapus tag kota yang sama dengan kota ini
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
    <form action="{{ route('property.list') }}#property-list-section" method="GET">
        <div class="container">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="min_price" class="form-control border-0 py-3" placeholder="Harga Min">
                        <input type="text" name="max_price" class="form-control border-0 py-3" placeholder="Harga Max">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="property_type" class="form-select border-0 py-3">
                        <option selected disabled>Tipe Property</option>
                        <option value="rumah">Rumah</option>
                        <option value="gudang">Gudang</option>
                        <option value="apartemen">Apartemen</option>
                        <option value="tanah">Tanah</option>
                        <option value="pabrik">Pabrik</option>
                        <option value="hotel dan villa">Hotel dan Villa</option>
                        <option value="ruko">Ruko</option>
                        <option value="sewa">Sewa</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="province-desktop" name="province" class="form-select border-0 py-3">
                        <option selected disabled>Pilih Provinsi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="city-desktop" class="form-select border-0 py-3" disabled>
                        <option selected disabled>Pilih Kota/Kabupaten</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="district-desktop" class="form-select border-0 py-3" disabled>
                        <option selected disabled>Pilih Kecamatan</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-dark border-0 w-100 py-3">Search</button>
                </div>
            </div>
            <div id="selected-cities-desktop" class="mt-2 d-flex flex-wrap gap-2"></div>
            <input type="hidden" name="selected_city_values" id="selected-city-values-desktop">
        </div>
    </form>
</div>
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
                <div class="filter-buttons d-flex flex-wrap justify-content-start justify-content-lg-end gap-2 mb-4">
                    <a class="btn {{ request('sort') === null ? 'btn-primary' : 'btn-outline-primary' }}"
                        href="{{ request()->fullUrlWithQuery(['sort' => null]) }}">Unggulan</a>

                    <a class="btn {{ request('sort') === 'harga_asc' ? 'btn-primary' : 'btn-outline-primary' }}"
                        href="{{ request()->fullUrlWithQuery(['sort' => 'harga_asc']) }}">Dari Harga Paling Rendah</a>

                    <a class="btn {{ request('sort') === 'harga_desc' ? 'btn-primary' : 'btn-outline-primary' }}"
                        href="{{ request()->fullUrlWithQuery(['sort' => 'harga_desc']) }}">Dari Harga Paling Tinggi</a>
                </div>
            </div>
        </div>


        <div class="row g-4">
            <style>
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
                                <img src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">
                            </a>
                            <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-2 py-1 px-3 text-capitalize">
                                {{ $property->tipe }}
                            </div>
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
                            <!-- Luas -->
                            <small class="flex-fill text-center border-end border-dashed py-2">
                                <i class="fa fa-vector-square text-danger me-2"></i>
                                <span class="text-dark">{{ $property->luas }} m²</span>
                            </small>
                            <!-- Kota -->
                            <small class="flex-fill text-center border-end border-dashed py-2">
                                <i class="fa fa-map-marker-alt text-danger me-2"></i>
                                <span class="text-dark text-uppercase">{{ $property->kota }}</span>
                            </small>
                            <!-- Tanggal -->
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

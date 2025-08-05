@include('template.header')
<!-- Bootstrap JS, Popper.js, dan jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<style>
    /* Pastikan carousel tampil penuh */
    #carousel {
        margin-top: 0 !important;
    }

    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel,
    .carousel-inner,
    .carousel-item {
        height: 80vh; /* Default tinggi carousel */
    }

    .carousel-caption {
        bottom: 15%;
        z-index: 10;
    }

    @media (max-width: 768px) {
        .carousel,
        .carousel-inner,
        .carousel-item {
            height: 60vh; /* lebih pendek untuk mobile */
        }

        .carousel-caption h1 {
            font-size: 1.3rem;
        }

        .carousel-caption p {
            font-size: 0.9rem;
        }

        .carousel-caption .btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
        }
    }

    body {
        padding-top: 0 !important;
    }

    .navbar {
        z-index: 9999;
    }
</style>


<!-- Carousel Start -->
<div id="carousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('img/carousel1.jpg') }}" alt="Carousel Image">
            <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100 text-center px-3">

                <p class="animated fadeInRight text-white mb-2">Temukan Rumah Sempurna</p>
                <h1 class="animated fadeInLeft text-white mb-4">Untuk Tinggal Bersama Keluarga Anda</h1>
                <a href="{{ url('/property-list') }}" class="btn btn-primary py-3 px-5 animated fadeIn">
                    Explore Lebih Lanjut
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Carousel End -->



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




        <!-- Category Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Property Types</h1>
                    <p>Temukan beragam tipe properti terbaik sesuai kebutuhan Anda — dari rumah lelang harga miring hingga apartemen modern dan ruko strategis. Pilih jenis properti favorit Anda dan mulai jelajahi sekarang!</p>
                </div>
                <div class="row g-3 g-md-4">
                    @foreach ($properties as $property)
                        @php
                            // Capitalize tiap kata pada tipe properti
                            $tipeFormatted = ucwords($property->tipe);
                            $isDisabled = $property->total == 0;
                        @endphp

                        <div class="col-6 col-md-4 col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
                            <a
                                class="cat-item d-block text-center rounded p-3 {{ $isDisabled ? 'bg-light opacity-50 pointer-events-none' : 'bg-light' }}"
                                href="{{ $isDisabled ? 'javascript:void(0);' : route('property.list', ['property_type' => strtolower($property->tipe)]) . '#property-list-section' }}"
                            >
                                <div class="rounded py-3 px-2 border {{ $isDisabled ? 'border-secondary' : '' }}">
                                    <div class="icon mb-3">
                                        <img
                                            class="img-fluid {{ $isDisabled ? 'grayscale' : '' }}"
                                            src="{{ asset('img/' . strtolower($property->tipe) . '.png') }}"
                                            alt="Icon {{ $tipeFormatted }}">
                                    </div>
                                    <h6 class="mb-1 {{ $isDisabled ? 'text-muted' : 'text-dark' }}">{{ $tipeFormatted }}</h6>
                                    <span class="{{ $isDisabled ? 'text-muted small' : 'fw-bold' }}">
                                        {{ $property->total }} Asset{{ $property->total != 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- Category End -->



        <!-- About Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <div class="about-img position-relative overflow-hidden p-5 pe-0">
                            <img class="img-fluid w-100" src="img/about.jpg">
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                        <h1 class="mb-4">Tempat #1 Untuk Membeli Properti Lelang Dengan Tenang dan Aman </h1>
                        <p class="mb-4">Banyak yang ragu membeli rumah lewat lelang karena dianggap rumit dan bermasalah. Padahal, lelang bisa menjadi cara efektif mendapatkan properti dengan harga di bawah nilai pasar. Proses lelang kini transparan dan mudah diikuti, terutama dengan bantuan agen profesional seperti kami yang khusus menangani pembelian properti lelang, membeli rumah lewat lelang menjadi langkah cerdas dan aman untuk investasi properti Anda.</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Sah Secara Hukum untuk Menjual Asset Lelang</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Motto Kami adalah Duduk - Diam - Ambil Dokumen</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Mempunyai lebih dari 100 ribu listing diseluruh Indonesia</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Garansi 100% serah terima kunci</p>
                        <a class="btn btn-primary py-3 px-5 mt-3" href="{{ url('/about') }}">Baca selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->


        <!-- Property List Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                    <h1 class="mb-3">Hot Listing Property</h1>
                    <p>Temukan properti paling diminati di pasaran saat ini!. Jangan lewatkan kesempatan untuk mendapatkan properti terbaik dengan harga terbaik. Bertindaklah cepat dan jadikan salah satu listing istimewa ini milik Anda!</p>
                </div>
            </div>
        </div>

        @if (isset($hotListingNote))
            <div class="alert alert-warning text-center">
                {{ $hotListingNote }}
            </div>
        @endif

        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">

                    <!-- Hot Listing Section Start -->
                    <section id="hot-listing">
                        <div class="container-xxl py-5">
                            <div class="container">
                                <div class="row g-4">
                                    @foreach ($hotListings as $property)
                                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                                        <div class="property-item rounded overflow-hidden flex-fill d-flex flex-column shadow-sm hover-shadow">
                                            <div class="position-relative overflow-hidden">
                                                <style>
                                                    .property-item img {
                                                        width: 100%;
                                                        height: 300px;
                                                        object-fit: cover;
                                                        object-position: center;
                                                    }
                                                    .property-item {
                                                        display: flex;
                                                        flex-direction: column;
                                                        transition: all 0.3s ease;
                                                    }
                                                    .property-item:hover {
                                                        transform: translateY(-5px);
                                                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
                                                    }
                                                    .property-item .p-4 {
                                                        flex-grow: 1;
                                                        padding: 1.5rem 1rem;
                                                    }
                                                    .text-truncate-2 {
                                                        overflow: hidden;
                                                        display: -webkit-box;
                                                        -webkit-line-clamp: 2;
                                                        -webkit-box-orient: vertical;
                                                    }
                                                </style>

                                                <img class="img-fluid" src="{{ explode(',', $property->gambar)[0] }}" alt="Gambar {{ $property->tipe }}">

                                                <!-- Only top-left label -->
                                                <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-2 py-1 px-3 text-capitalize">
                                                    {{ $property->tipe }}
                                                </div>
                                            </div>
                                            <div class="p-4 pb-0">
                                                <h5 class="text-primary mb-3">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
                                                <a class="d-block h5 mb-2" href="{{ route('property-detail', $property->id_listing) }}">
                                                    {{ \Illuminate\Support\Str::limit($property->deskripsi, 50, '...') }}
                                                </a>
                                                <p class="text-truncate-2">
                                                    <i class="fa fa-map-marker-alt text-primary me-2"></i>
                                                    {{ $property->lokasi }}
                                                </p>
                                            </div>
                                            <div class="d-flex border-top border-1 border-light">
                                                <small class="flex-fill text-center border-end py-2">
                                                    <i class="fa fa-vector-square text-danger me-2"></i>
                                                    <span class="text-dark">{{ $property->luas }} m²</span>
                                                </small>
                                                <small class="flex-fill text-center border-end py-2">
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
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Hot Listing Section End -->

                    <div class="col-12 text-center mt-4">
                        <a href="{{ url('/property-list') }}" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">
                            Explore Lebih Lanjut
                        </a>
                    </div>
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


       <!-- Service Start -->
       <div class="service">
        <div class="container">
            <div class="section-header text-center">
                <p>Layanan Kami</p>
                <h2>Kami Menyediakan Berbagai Layanan</h2>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item">
                        <div class="service-img">
                            <img src="img/BalaiLelang.png" alt="Image">
                            <div class="service-overlay">
                                <p>
                                    Kami menyediakan layanan <strong>pendaftaran lelang</strong> untuk berbagai keperluan, baik <strong>lelang sukarela</strong> maupun <strong>lelang hak tanggungan</strong>. Dengan dukungan tim profesional, kami memastikan proses berjalan secara <strong>transparan</strong>, <strong>efisien</strong>, dan sesuai dengan ketentuan hukum yang berlaku.
                                </p>
                            </div>
                        </div>
                        <div class="service-text">
                            <h3>Pendaftaran Asset Lelang</h3>
                            <a class="btn" href="img/service-1.jpg" data-lightbox="service"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="service-item">
                        <div class="service-img">
                            <img src="img/PengurusanDokumen.png" alt="Image">
                            <div class="service-overlay">
                                <p>
                                    Kami menyediakan layanan <strong>pengurusan dokumen</strong> properti dengan biaya yang jauh lebih terjangkau dan proses yang lebih cepat dibandingkan notaris. <br><br>
                                    Didukung oleh tim profesional dan prosedur yang sesuai hukum, kami memastikan seluruh dokumen penting Anda selesai dengan <strong>cepat, aman, dan tanpa ribet</strong>. Serahkan semua kepada kami, Anda hanya perlu duduk tenang hingga dokumen resmi atas nama Anda selesai.
                                </p>
                            </div>
                        </div>
                        <div class="service-text">
                            <h3>Pengurusan Dokumen</h3>
                            <a class="btn" href="img/service-2.jpg" data-lightbox="service"></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item">
                        <div class="service-img">
                            <img src="img/EksekusiPengosongan.png" alt="Image">
                            <div class="service-overlay">
                                <p>
                                    Kami menjamin <strong>serah terima kunci</strong> dengan proses pengosongan yang legal, aman, dan sesuai ketentuan hukum. Dengan rekam jejak <strong>100% sukses</strong>, Anda cukup duduk tenang—semua akan kami urus hingga properti siap digunakan.
                                </p>

                            </div>
                        </div>
                        <div class="service-text">
                            <h3>Eksekusi Pengosongan Asset</h3>
                            <a class="btn" href="img/service-3.jpg" data-lightbox="service"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->



        <!-- Testimonial Start -->

        <script>
            $(document).ready(function(){
                $(".testimonial-carousel").owlCarousel({
                    loop: true,
                    margin: 30,
                    nav: false,
                    dots: true,
                    autoplay: true,
                    autoplayTimeout: 5000,
                    responsive:{
                        0:{ items:1 },
                        768:{ items:2 },
                        992:{ items:3 }
                    }
                });
            });
            </script>
<style>
    /*** Testimonial ***/
    .testimonial-carousel .owl-item .testimonial-item {
        position: relative;
        transition: .5s;
    }

    .testimonial-carousel .owl-item.center .testimonial-item {
        box-shadow: 0 0 45px rgba(0, 0, 0, .08);
        animation: pulse 1s ease-out .5s;
    }

    .testimonial-carousel .owl-dots {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .testimonial-carousel .owl-dot {
        position: relative;
        display: inline-block;
        margin: 0 5px;
        width: 15px;
        height: 15px;
        background: var(--primary);
        border: 5px solid var(--light);
        border-radius: 15px;
        transition: .5s;
    }

    .testimonial-carousel .owl-dot.active {
        background: var(--light);
        border-color: var(--primary);
    }
    </style>

<!-- FAQs Start -->
<style>
/*******************************/
/*********** FAQs CSS **********/
/*******************************/
.faqs {
    position: relative;
    width: 100%;
    padding: 45px 0;
}

.faqs .row {
    position: relative;
}

.faqs .row::after {
    position: absolute;
    content: "";
    width: 1px;
    height: 100%;
    top: 0;
    left: calc(50% - .5px);
    background: #f4511e;
}

.faqs #accordion-1 {
    padding-right: 15px;
}

.faqs #accordion-2 {
    padding-left: 15px;
}

@media(max-width: 767.98px) {
    .faqs .row::after {
        display: none;
    }

    .faqs #accordion-1,
    .faqs #accordion-2 {
        padding: 0;
    }

    .faqs #accordion-2 {
        padding-top: 15px;
    }
}

.faqs .card {
    margin-bottom: 15px;
    border: none;
    border-radius: 0;
}

.faqs .card:last-child {
    margin-bottom: 0;
}

.faqs .card-header {
    padding: 0;
    border: none;
    background: #ffffff;
}

.faqs .card-header a {
    display: block;
    padding: 10px 25px;
    width: 100%;
    color: #121518;
    font-size: 16px;
    line-height: 40px;
    border: 1px solid rgba(0, 0, 0, .1);
    transition: .5s;
}

.faqs .card-header [data-toggle="collapse"][aria-expanded="true"] {
    background: #fdefef;
}

.faqs .card-header [data-toggle="collapse"]:after {
    font-family: 'font Awesome 5 Free';
    content: "\f067";
    float: right;
    color: #fdefef;
    font-size: 12px;
    font-weight: 900;
    transition: .5s;
}

.faqs .card-header [data-toggle="collapse"][aria-expanded="true"]:after {
    font-family: 'font Awesome 5 Free';
    content: "\f068";
    float: right;
    color: #030f27;
    font-size: 12px;
    font-weight: 900;
    transition: .5s;
}

.faqs .card-body {
    padding: 20px 25px;
    font-size: 16px;
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, .1);
    border-top: none;
}
</style>
<div class="faqs">
    <div class="container">
        <div class="section-header text-center">
            <p>Pertanyaan Yang Sering Muncul</p>
            <h2>Kamu Mungkin Bertanya</h2>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div id="accordion-1">
                    <div class="card wow fadeInLeft" data-wow-delay="0.1s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseOne">
                                Apakah ada biaya tersembunyi di akhir?
                            </a>
                        </div>
                        <div id="collapseOne" class="collapse" data-parent="#accordion-1">
                            <div class="card-body">
                                Tidak ada biaya tersembunyi. Harga yang tertera sudah mencakup <strong>harga properti</strong>, <strong>biaya pengosongan</strong>, dan <strong>biaya pengurusan dokumen</strong> seperti <strong>Pajak BPHTB (Bea Perolehan Hak atas Tanah dan Bangunan)</strong>, <strong>biaya lelang</strong>, <strong>biaya balik nama sertifikat</strong>, dan <strong>biaya administrasi lainnya</strong>. <br><br>
                                <strong>Catatan:</strong> <strong>Tunggakan utilitas</strong> (listrik, air, dan lainnya) apabila ada, <strong>tidak termasuk</strong> dalam harga dan menjadi tanggung jawab pembeli.
                            </div>
                        </div>
                    </div>
                    <div class="card wow fadeInLeft" data-wow-delay="0.2s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseTwo">
                                Apakah saya bisa survei properti sebelum membeli?
                            </a>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#accordion-1">
                            <div class="card-body">
                                Ya, Anda dapat melakukan <strong>survei properti</strong> terlebih dahulu untuk memastikan <strong>lokasi</strong> dan <strong>kondisi dari sisi luar</strong>. <br><br>
                                Kami menyarankan hal ini agar Anda mendapatkan gambaran tentang <strong>lingkungan sekitar</strong>, <strong>akses jalan</strong>, dan <strong>fasilitas umum</strong> di area tersebut. Kebijakan ini juga diterapkan untuk menjaga <strong>keamanan</strong> dan <strong>kenyamanan penghuni sebelumnya</strong> hingga proses <strong>pengosongan</strong> selesai. <br><br>
                                Setelah pembayaran dan <strong>proses administrasi</strong> diselesaikan, Anda akan mendapatkan <strong>akses penuh ke properti</strong>.
                            </div>
                        </div>
                    </div>
                    <div class="card wow fadeInLeft" data-wow-delay="0.3s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseThree">
                                Apakah saya bisa beli properti ini secara KPR?
                            </a>
                        </div>
                        <div id="collapseThree" class="collapse" data-parent="#accordion-1">
                            <div class="card-body">
                                Pengajuan pembelian properti ini melalui <strong>KPR (Kredit Pemilikan Rumah)</strong> sangat bergantung pada kebijakan masing-masing <strong>bank</strong>. Namun, berdasarkan pengalaman kami, sebagian besar bank <strong>tidak menerima</strong pembiayaan KPR untuk properti hasil lelang atau sejenisnya. <br><br>
                                Untuk memastikan proses yang lebih cepat dan <strong>aman secara hukum</strong>, kami menyarankan metode pembayaran secara <strong>tunai (cash)</strong>. Hal ini membantu mempercepat proses balik nama sertifikat dan serah terima properti tanpa hambatan administrasi dari pihak ketiga.
                            </div>
                        </div>
                    </div>
                    <div class="card wow fadeInLeft" data-wow-delay="0.4s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseFour">
                                Bagaimana kekuatan hukum dokumen lelang dibandingkan jual beli biasa?
                            </a>
                        </div>
                        <div id="collapseFour" class="collapse" data-parent="#accordion-1">
                            <div class="card-body">
                                Dari sisi <strong>legalitas</strong>, pembelian properti melalui <strong>lelang</strong> adalah salah satu proses <strong>paling kuat dan aman</strong> di Indonesia. Sebagai pemenang lelang, Anda mendapatkan <strong>kepastian hukum yang dijamin langsung oleh negara</strong> melalui peraturan perundang-undangan yang berlaku. <br><br>
                                Semua dokumen, termasuk <strong>Berita Acara Lelang (BAL)</strong> dan <strong>Risalah Lelang</strong>, memiliki kekuatan hukum yang lebih tinggi dibanding akta jual beli di hadapan notaris/PPAT. Dengan demikian, tidak ada pihak lain yang bisa menggugat kepemilikan Anda setelah proses lelang selesai.
                            </div>
                        </div>
                    </div>
                    <div class="card wow fadeInLeft" data-wow-delay="0.5s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseFive">
                                Bagaimana jika pemilik lama tidak mau keluar dari rumah?
                            </a>
                        </div>
                        <div id="collapseFive" class="collapse" data-parent="#accordion-1">
                            <div class="card-body">
                                Anda tidak perlu khawatir mengenai proses <strong>pengosongan rumah</strong>. Sebagai klien, Anda cukup <strong>duduk tenang</strong> dan menerima <strong>dokumen kepemilikan resmi</strong> setelah semua tahapan selesai. <br><br>
                                Seluruh proses, mulai dari <strong>pengurusan dokumen legal</strong> hingga <strong>pengosongan properti</strong>, akan ditangani sepenuhnya oleh tim kami sesuai prosedur hukum yang berlaku. Kami memastikan properti dalam kondisi <strong>siap serah terima</strong> tanpa hambatan.
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="accordion-2">
                    <div class="card wow fadeInRight" data-wow-delay="0.1s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseSix">
                                Apakah saya mendapatkan bukti resmi sebagai pemenang lelang?
                            </a>
                        </div>
                        <div id="collapseSix" class="collapse" data-parent="#accordion-2">
                            <div class="card-body">
                                Ya, sebagai pemenang lelang Anda akan menerima <strong>Risalah Lelang</strong> dan <strong>Berita Acara Lelang (BAL)</strong> dari Kantor Pelayanan Kekayaan Negara dan Lelang (KPKNL). <br><br>
                                Kedua dokumen ini memiliki <strong>kekuatan hukum yang sah</strong> dan diakui negara sebagai dasar kepemilikan properti. Dengan dokumen tersebut, hak Anda sebagai pemilik <strong>tidak dapat diganggu gugat</strong> oleh pihak manapun.
                            </div>
                        </div>
                    </div>
                    <div class="card wow fadeInRight" data-wow-delay="0.2s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseSeven">
                                Apakah saya bisa menempati properti sebelum dokumen selesai?
                            </a>
                        </div>
                        <div id="collapseSeven" class="collapse" data-parent="#accordion-2">
                            <div class="card-body">
                                Demi menjaga <strong>kepastian hukum</strong> dan menghindari risiko sengketa, properti hanya dapat ditempati setelah seluruh <strong>proses administrasi dan balik nama sertifikat</strong> selesai. <br><br>
                                Kami memastikan semua tahapan berjalan cepat dan lancar agar Anda segera dapat <strong>menempati properti</strong> dengan <strong>status kepemilikan yang aman</strong>.
                            </div>
                        </div>
                    </div>
                    <div class="card wow fadeInRight" data-wow-delay="0.3s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseEight">
                                Berapa lama proses serah terima setelah lelang?
                            </a>
                        </div>
                        <div id="collapseEight" class="collapse" data-parent="#accordion-2">
                            <div class="card-body">
                                Proses <strong>serah terima properti</strong> sangat bergantung pada kondisi saat lelang:
                                <ul>
                                    <li>Jika properti <strong>sudah kosong</strong>, maka Anda dapat <strong>menempati rumah segera</strong> setelah <strong>balik nama sertifikat</strong> selesai.</li>
                                    <li>Jika properti masih ditempati, proses <strong>pengosongan</strong> biasanya memerlukan waktu sekitar <strong>3–6 bulan</strong> sesuai prosedur hukum yang berlaku.</li>
                                </ul>
                                Tim kami akan mengurus seluruh <strong>proses administrasi</strong> dan memastikan properti dalam kondisi <strong>siap huni</strong> dengan kepemilikan yang <strong>aman dan sah</strong> atas nama Anda.
                            </div>

                        </div>
                    </div>
                    <div class="card wow fadeInRight" data-wow-delay="0.4s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseNine">
                                Apakah hasil lelang bisa dibatalkan oleh pihak manapun?
                            </a>
                        </div>
                        <div id="collapseNine" class="collapse" data-parent="#accordion-2">
                            <div class="card-body">
                                Tidak. Setelah Anda dinyatakan sebagai <strong>pemenang lelang</strong>, hasil lelang bersifat <strong>final dan mengikat secara hukum</strong>. Dokumen resmi seperti <strong>Risalah Lelang</strong> dan <strong>Berita Acara Lelang (BAL)</strong> memiliki <strong>kekuatan eksekutorial</strong> yang diakui negara. <br><br>
                                Dengan demikian, tidak ada pihak manapun yang dapat <strong>membatalkan atau menggugat hasil lelang</strong> setelah proses selesai.
                            </div>

                        </div>
                    </div>
                    <div class="card wow fadeInRight" data-wow-delay="0.5s">
                        <div class="card-header">
                            <a class="card-link collapsed" data-toggle="collapse" href="#collapseTen">
                                Bagaimana perlindungan hukum saya sebagai pemenang lelang?
                            </a>
                        </div>
                        <div id="collapseTen" class="collapse" data-parent="#accordion-2">
                            <div class="card-body">
                                Sebagai <strong>pemenang lelang</strong>, hak kepemilikan Anda dilindungi penuh oleh hukum. Dokumen resmi seperti <strong>Risalah Lelang</strong> dan <strong>Berita Acara Lelang (BAL)</strong> yang dikeluarkan oleh <strong>Kantor Pelayanan Kekayaan Negara dan Lelang (KPKNL)</strong> memiliki <strong>kekuatan eksekutorial</strong> yang sah di mata hukum. <br><br>
                                Perlindungan hukum ini diatur dalam:
                                <ul>
                                    <li><strong>Undang-Undang Nomor 5 Tahun 1960</strong> tentang Pokok Agraria</li>
                                    <li><strong>Peraturan Menteri Keuangan RI Nomor 213/PMK.06/2020</strong> tentang Petunjuk Pelaksanaan Lelang</li>
                                    <li><strong>Herzien Inlandsch Reglement (HIR) Pasal 224</strong> yang mengatur eksekusi hasil lelang</li>
                                </ul>
                                Dengan dasar hukum tersebut, kepemilikan Anda <strong>tidak dapat dibatalkan atau digugat oleh pihak manapun</strong>. Anda mendapatkan <strong>kepastian hukum 100%</strong> sebagai pemilik sah properti.
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FAQs End -->

<!-- Testimonial Start -->
<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="text-center">
            <h6 class="text-secondary text-uppercase">Testimonial</h6>
            <h1 class="mb-0">Dari Client Kami!</h1>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            @foreach ($testimonials as $testimonial)
            <div class="testimonial-item p-4 my-5">
                <div class="testimonial-img position-relative text-center">
                    <img class="img-fluid rounded-circle mx-auto mb-5" src="{{ asset('img/default-profile.jpg') }}" alt="{{ $testimonial->nama }}" style="width: 80px; height: 80px;">
                    <div class="btn-square bg-primary rounded-circle">
                        <i class="fa fa-quote-left text-white"></i>
                    </div>
                </div>
                <div class="testimonial-text text-center rounded p-4">
                    <p>{{ $testimonial->comment }}</p>
                    <h5 class="mb-1">{{ $testimonial->nama }}</h5>
                    <div class="stars">
                        @for ($i = 1; $i <= 5; $i++)
                        <i class="fa {{ $i <= $testimonial->rating ? 'fa-star' : 'fa-star-o' }} {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Testimonial End -->

<!-- Testimonial End -->



@include('template.footer')

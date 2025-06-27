@include('template.header')
        <!-- Header Start -->
        <div class="container-fluid header bg-white p-0">
            <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
                <div class="col-md-6 p-5 mt-lg-5">
                    <h1 class="display-5 animated fadeIn mb-4">Property Type</h1>
                        <nav aria-label="breadcrumb animated fadeIn">
                        <ol class="breadcrumb text-uppercase">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Pages</a></li>
                            <li class="breadcrumb-item text-body active" aria-current="page">Property Type</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 animated fadeIn">
                    <img class="img-fluid" src="img/header.jpg" alt="">
                </div>
            </div>
        </div>
        <!-- Header End -->


       <!-- Search Start -->
<form action="{{ route('property.list') }}#property-list-section" method="GET">
    <div class="container-fluid bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px;">
        <div class="container">
            <div class="row g-2">
                <!-- Harga Minimum -->
                <div class="col-md-2">
                    <input type="text" name="min_price" id="min_price" class="form-control border-0 py-3" placeholder="Harga Minimum">
                </div>

                <!-- Harga Maksimum -->
                <div class="col-md-2">
                    <input type="text" name="max_price" id="max_price" class="form-control border-0 py-3" placeholder="Harga Maksimum">
                </div>

                <!-- Tipe Properti -->
                <div class="col-md-2">
                    <select name="property_type" class="form-select border-0 py-3">
                        <option selected disabled>Tipe Property</option>
                        <option value="Lelang">Lelang</option>
                        <option value="Primary">Primary</option>
                        <option value="Secondary">Secondary</option>
                        <option value="Apartemen">Apartemen</option>
                        <option value="Gudang">Gudang</option>
                        <option value="Tanah">Tanah</option>
                        <option value="Ruko">Ruko</option>
                        <option value="Sewa">Sewa</option>
                    </select>
                </div>

                <!-- Provinsi -->
                <div class="col-md-2">
                    <select id="province" class="form-select border-0 py-3">
                        <option selected disabled>Pilih Provinsi</option>
                    </select>
                </div>

                <!-- Kota -->
                <div class="col-md-2">
                    <select id="city" class="form-select border-0 py-3" disabled>
                        <option selected disabled>Pilih Kota/Kabupaten</option>
                    </select>
                </div>

                <!-- Tombol Search -->
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark border-0 w-100 py-3">Search</button>
                </div>
            </div>
            <div id="selected-cities" class="mt-2 d-flex flex-wrap gap-2"></div>
        </div>
    </div>

    <!-- Input Hidden Kota[] -->
    <input type="hidden" name="selected_city_values" id="selected-city-values">
</form>
<!-- Search End -->

<!-- Styling Tag Kota -->
<style>
    #selected-cities {
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
        const province = document.getElementById('province');
        const city = document.getElementById('city');
        const selectedCities = document.getElementById('selected-cities');
        const selectedCityValues = document.getElementById('selected-city-values');
        const selectedCityList = [];

        // Load data lokasi
        fetch("{{ asset('data/indonesia.json') }}")
            .then(res => res.json())
            .then(data => {
                Object.keys(data).forEach(prov => {
                    province.innerHTML += `<option value="${prov}">${prov}</option>`;
                });

                province.addEventListener('change', function () {
                    const selected = this.value;
                    const cities = data[selected];

                    city.disabled = false;
                    city.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';

                    cities.forEach(c => {
                        const cleanedValue = c.replace(/^Kota\s|^Kabupaten\s/, '');
                        city.innerHTML += `<option value="${cleanedValue}">${c}</option>`;
                    });
                });
            });

        // Saat kota dipilih
        city.addEventListener('change', function () {
            const selectedCity = this.value;

            if (!selectedCityList.includes(selectedCity)) {
                selectedCityList.push(selectedCity);
                renderSelectedCities();
            }
        });

        // Render tag kota
        function renderSelectedCities() {
            selectedCities.innerHTML = '';

            selectedCityList.forEach(city => {
                const tag = document.createElement('div');
                tag.className = 'city-tag';
                tag.innerHTML = `${city} <span class="remove-tag" data-city="${city}">&times;</span>`;
                selectedCities.appendChild(tag);
            });

            selectedCityValues.value = selectedCityList.join(',');
        }

        // Remove tag
        selectedCities.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-tag')) {
                const cityToRemove = e.target.dataset.city;
                const index = selectedCityList.indexOf(cityToRemove);
                if (index > -1) {
                    selectedCityList.splice(index, 1);
                    renderSelectedCities();
                }
            }
        });

        // Format harga
        function formatRupiah(input) {
            let angka = input.value.replace(/\D/g, '');
            input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        const minPriceInput = document.getElementById('min_price');
        const maxPriceInput = document.getElementById('max_price');

        minPriceInput.addEventListener('input', function () {
            formatRupiah(this);
        });

        maxPriceInput.addEventListener('input', function () {
            formatRupiah(this);
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
                <div class="row g-4">
                    @foreach ($properties as $property)
                        <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                            <a class="cat-item d-block bg-light text-center rounded p-3" href="{{ route('property.list', ['property_type' => $property->tipe]) }}#property-list-section">
                                <div class="rounded p-4">
                                    <div class="icon mb-3">
                                        <img class="img-fluid" src="{{ asset('img/' . strtolower($property->tipe) . '.png') }}" alt="Icon">
                                    </div>
                                    <h6>{{ $property->tipe }}</h6>
                                    <span>{{ $property->total }} Properties</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                </div>
            </div>
        </div>
        <!-- Category End -->
        @include('template.footer')

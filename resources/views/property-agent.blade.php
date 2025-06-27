@include('template.header')
        <!-- Header Start -->
        <div class="container-fluid header bg-white p-0">
            <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
                <div class="col-md-6 p-5 mt-lg-5">
                    <h1 class="display-5 animated fadeIn mb-4">Property Agent</h1>
                        <nav aria-label="breadcrumb animated fadeIn">
                        <ol class="breadcrumb text-uppercase">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Pages</a></li>
                            <li class="breadcrumb-item text-body active" aria-current="page">Property Agent</li>
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
                <!-- Fields input dalam 1 row -->
                <div class="col-md-2">
                    <input type="text" name="min_price" id="min_price" class="form-control border-0 py-3" placeholder="Harga Minimum">
                </div>
                <div class="col-md-2">
                    <input type="text" name="max_price" id="max_price" class="form-control border-0 py-3" placeholder="Harga Maksimum">
                </div>
                <script>
                    function formatRupiah(input) {
                        let angka = input.value.replace(/\D/g, '');
                        input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }

                    document.addEventListener('DOMContentLoaded', function () {
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
                <div class="col-md-2">
                    <select name="property_type" class="form-select border-0 py-3">
                        <option selected>Tipe Property</option>
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
                <div class="col-md-2">
                    <select id="province" class="form-select border-0 py-3">
                        <option selected disabled>Pilih Provinsi</option>
                        {{-- JS Isi Otomatis --}}
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="city" name="kota" class="form-select border-0 py-3" disabled>
                        <option selected disabled>Pilih Kota/Kabupaten</option>
                        {{-- JS Isi Otomatis --}}
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark border-0 w-100 py-3">Search</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- Search End -->
<!-- Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const province = document.getElementById('province');
        const city = document.getElementById('city');
        const selectedCities = document.getElementById('selected-cities');
        const selectedCityValues = document.getElementById('selected-city-values');
        const selectedCityList = [];

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

                // Isi dropdown provinsi
                for (let prov of provinceMap.keys()) {
                    province.innerHTML += `<option value="${prov}">${prov}</option>`;
                }

                province.addEventListener('change', function () {
                    const selected = this.value;
                    const citySet = provinceMap.get(selected);

                    city.disabled = false;
                    city.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';

                    citySet.forEach(c => {
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

        <!-- Team Start -->

        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Property Agents</h1>
                    <p>Eirmod sed ipsum dolor sit rebum labore magna erat. Tempor ut dolore lorem kasd vero ipsum sit eirmod sit. Ipsum diam justo sed rebum vero dolor duo.</p>
                </div>
                <div class="row g-4">
                    @foreach ($agents as $agent)
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="team-item rounded overflow-hidden">
                            <div class="position-relative">
                                <img class="img-fluid" src="{{asset($agent->picture)}}" alt="">
                                <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">
                                    <a class="btn btn-square mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-square mx-1" href=""><i class="fab fa-twitter"></i></a>
                                    <a class="btn btn-square mx-1" href=""><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                            <div class="text-center p-4 mt-3">
                                <h5 class="fw-bold mb-0">{{ $agent->nama }}</h5>
                                <small>{{ $agent->deskripsi }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
        <!-- Team End -->


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
                                    <h1 class="mb-3">Contact With Our Certified Agent</h1>
                                    <p>Eirmod sed ipsum dolor sit rebum magna erat. Tempor lorem kasd vero ipsum sit sit diam justo sed vero dolor duo.</p>
                                </div>
                                <a href="" class="btn btn-primary py-3 px-4 me-2"><i class="fa fa-phone-alt me-2"></i>Make A Call</a>
                                <a href="" class="btn btn-dark py-3 px-4"><i class="fa fa-calendar-alt me-2"></i>Get Appoinment</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Call to Action End -->


       @include('template.footer')

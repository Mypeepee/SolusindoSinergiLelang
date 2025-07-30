@include('template.header')

<!-- Header Start -->
<div class="container-fluid header bg-white p-0">
    <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
        <div class="col-md-6 p-5 mt-lg-5">
            <h1 class="display-5 animated fadeIn mb-4">Agent Kami</h1>
            <nav aria-label="breadcrumb animated fadeIn">
                <ol class="breadcrumb text-uppercase">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item text-body active" aria-current="page">Agent Kami</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 animated fadeIn">
            <img class="img-fluid" src="{{ asset('img/header.jpg') }}" alt="">
        </div>
    </div>
</div>
<!-- Header End -->

<!-- Search Agent Name -->
<form action="{{ route('property.agent') }}#agent-list-section" method="GET">
    <div class="container-fluid bg-primary mb-5 wow fadeIn" style="padding: 35px;">
        <div class="container">
            <div class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="agent_name" value="{{ request('agent_name') }}" class="form-control border-0 py-3" placeholder="Cari Nama Agent">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark border-0 w-100 py-3">Search</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Agent List -->
@if(!$selectedAgent)
<div id="agent-list-section" class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto mb-5 wow fadeInUp" style="max-width: 600px;">
            <h1 class="mb-3">Our Property Agents</h1>
        </div>
        <div class="row g-4">
            @foreach ($agents as $agent)
    @if (!request('agent_name') || str_contains(strtolower($agent->nama), strtolower(request('agent_name'))))
    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <a href="{{ route('property.agent', ['agent_id' => $agent->id_agent]) }}">
                <div class="ratio ratio-1x1 bg-light overflow-hidden">
                    <img class="w-100 h-100 object-fit-cover"
                         src="{{ $agent->picture ? 'https://drive.google.com/thumbnail?id=' . $agent->picture : asset('images/default-profile.png') }}"
                         alt="{{ $agent->nama }}">
                </div>
            </a>

            <div class="text-center p-3">
                <h6 class="fw-bold mb-2">{{ $agent->nama }}</h6>
                <div class="d-flex justify-content-center gap-2">
                    @if($agent->facebook)
                        <a class="btn btn-sm btn-outline-primary rounded-circle" href="https://{{ $agent->facebook }}" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    @endif
                    @if($agent->nomor_telepon)
                        <a class="btn btn-sm btn-outline-success rounded-circle" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agent->nomor_telepon) }}" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    @endif
                    @if($agent->instagram)
                        <a class="btn btn-sm btn-outline-danger rounded-circle" href="https://instagram.com/{{ ltrim($agent->instagram, '@') }}" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
<style>
.object-fit-cover {
    object-fit: cover;
    object-position: center;
}
    </style>
        </div>
    </div>
</div>
@endif

<!-- Property List for Selected Agent -->
@if($selectedAgent)
<div class="container">
    <a href="{{ route('property.agent') }}" class="btn btn-secondary mb-4">&larr; Kembali ke Daftar Agent</a>

    <div class="d-flex align-items-center flex-wrap mb-4">
        <h3 class="mb-0 me-3">Properti {{ $selectedAgent->nama }}</h3>
        <div class="d-flex align-items-center">
            @if($selectedAgent->facebook)
            <a class="btn btn-square mx-1" href="https://{{ $selectedAgent->facebook }}" target="_blank">
                <i class="fab fa-facebook-f"></i>
            </a>
            @endif

            @if($selectedAgent->nomor_telepon)
            <a class="btn btn-square mx-1" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedAgent->nomor_telepon) }}" target="_blank">
                <i class="fab fa-whatsapp"></i>
            </a>
            @endif

            @if($selectedAgent->instagram)
            <a class="btn btn-square mx-1" href="https://instagram.com/{{ $selectedAgent->instagram }}" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            @endif
        </div>
    </div>
</div>


<!-- Tombol baru (UI konsisten seperti desktop) -->
<button type="button" class="btn btn-dark w-100 py-3 d-md-none mb-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#filterModal">
    🔍 Filter Pencarian
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
                                    <option value="Lelang">Lelang</option>
                                    <option value="rumah">Rumah</option>
                                    <option value="hotel dan villa">Villa</option>
                                    <option value="apartemen">Apartemen</option>
                                    <option value="gudang">Gudang</option>
                                    <option value="tanah">Tanah</option>
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

<!-- Desktop View Original Search Form (Visible Only on md and Up) -->
<div class="container-fluid bg-primary mb-5 wow fadeIn d-none d-md-block" data-wow-delay="0.1s" style="padding: 35px;">
    <form action="{{ route('property.list') }}#property-list-section" method="GET">
        <div class="container">
            <div class="row g-2">
                <div class="col-md-2">
                    <input type="text" name="min_price" class="form-control border-0 py-3" placeholder="Harga Minimum">
                </div>
                <div class="col-md-2">
                    <input type="text" name="max_price" class="form-control border-0 py-3" placeholder="Harga Maksimum">
                </div>
                <div class="col-md-2">
                    <select name="property_type" class="form-select border-0 py-3">
                        <option selected disabled>Tipe Property</option>
                        <option value="Lelang">Lelang</option>
                        <option value="rumah">Rumah</option>
                        <option value="hotel dan villa">Villa</option>
                        <option value="apartemen">Apartemen</option>
                        <option value="gudang">Gudang</option>
                        <option value="tanah">Tanah</option>
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
                    <button type="submit" class="btn btn-dark border-0 w-100 py-3">Search</button>
                </div>
            </div>
            <div id="selected-cities-desktop" class="mt-2 d-flex flex-wrap gap-2"></div>
            <input type="hidden" name="selected_city_values" id="selected-city-values-desktop">
        </div>
    </form>
</div>

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


        <!-- Property List Start -->
        @if (!empty($selectedCities))
    <div class="alert alert-info">
        Menampilkan properti di {{ implode(', ', $selectedCities) }}
    </div>
@endif
        <div id="property-list-section" class="container-xxl py-5">
            <div class="container">
                <div class="row g-0 gx-5 align-items-end">
                    <div class="col-lg-6">
                        <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                            <h1 class="mb-3">Jelajahi Beragam Tipe Properti</h1>
                            <p>Temukan pilihan properti terbaik mulai dari rumah lelang murah, properti sewa strategis, hingga gudang investasi. Semua ada di sini untuk kebutuhan dan rencana finansialmu.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                        <ul class="nav nav-pills d-inline-flex justify-content-end mb-5">
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-primary active" data-bs-toggle="pill" href="#tab-1">Featured</a>
                            </li>
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-primary" data-bs-toggle="pill" href="#tab-2">For Sell</a>
                            </li>
                            <li class="nav-item me-0">
                                <a class="btn btn-outline-primary" data-bs-toggle="pill" href="#tab-3">For Rent</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <style>
                                .property-img-square {
                                    aspect-ratio: 1 / 1;
                                    width: 100%;
                                    object-fit: cover;
                                }
                            </style>
                            @foreach ($properties as $property)
                                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                    <div class="property-item rounded overflow-hidden">
                                        <div class="position-relative overflow-hidden">
                                            <style>
                                                .property-item img {
                                                    width: 100%;
                                                    height: 300px; /* atur tinggi sesuai selera */
                                                    object-fit: cover;
                                                    object-position: center;
                                                }
                                            </style>
                                            <a href="{{ route('property-detail', $property->id_listing) }}">
                                                <img class="img-fluid rounded w-100 property-img-square" src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">

                                            </a>
                                            <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">{{ $property->tipe }}</div>
                                            <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{ $property->tipe }}</div>
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
                                        <div class="d-flex border-top border-2 border-dashed border-orange">
                                            <!-- Luas Properti -->
                                            <small class="flex-fill text-center border-end border-dashed py-2">
                                                <i class="fa fa-vector-square text-danger me-2"></i>
                                                <span class="text-dark">{{ $property->luas }} m²</span>
                                            </small>

                                            <!-- Kota -->
                                            <small class="flex-fill text-center border-end border-dashed py-2">
                                                <i class="fa fa-map-marker-alt text-danger me-2"></i>
                                                <span class="text-dark text-uppercase">{{ $property->kota }}</span>
                                            </small>

                                            <!-- Batas Penawaran -->
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




                            <!-- Pagination links -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="pagination d-flex justify-content-center mt-5">
                                        {{-- Previous Page Link --}}
                                        @if ($properties->onFirstPage())
                                            <a class="rounded disabled">&laquo;</a>
                                        @else
                                            <a href="{{ $properties->appends(request()->query())->previousPageUrl() }}" class="rounded">&laquo;</a>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @php
                                            $currentPage = $properties->currentPage();
                                            $lastPage = $properties->lastPage();
                                            $start = max($currentPage - 3, 1);
                                            $end = min($currentPage + 3, $lastPage);
                                        @endphp

                                        {{-- First Page Link --}}
                                        @if ($start > 1)
                                            <a href="{{ $properties->appends(request()->query())->url(1) }}" class="rounded">1</a>
                                            @if ($start > 2)
                                                <span class="rounded disabled">...</span>
                                            @endif
                                        @endif

                                        {{-- Page Number Links --}}
                                        @for ($i = $start; $i <= $end; $i++)
                                            <a href="{{ $properties->appends(request()->query())->url($i) }}"
                                            class="rounded {{ $i === $currentPage ? 'active' : '' }}">{{ $i }}</a>
                                        @endfor

                                        {{-- Last Page Link --}}
                                        @if ($end < $lastPage)
                                            @if ($end < $lastPage - 1)
                                                <span class="rounded disabled">...</span>
                                            @endif
                                            <a href="{{ $properties->appends(request()->query())->url($lastPage) }}" class="rounded">{{ $lastPage }}</a>
                                        @endif

                                        {{-- Next Page Link --}}
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
                </div>
            </div>
        </div>
        <!-- Property List End -->

@endif

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

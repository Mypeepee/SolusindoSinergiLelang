@include('template.header')
<!-- Bootstrap JS, Popper.js, dan jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<style>
    /* Hilangkan jarak antara navbar & carousel */
    #carousel {
        margin-top: -50px; /* geser carousel naik */
    }

    body {
        padding-top: 0 !important; /* jika ada header fixed hilangkan padding body */
    }

    .navbar {
        z-index: 9999; /* pastikan navbar di atas carousel */
    }
</style>

<!-- Carousel Start -->
<div id="carousel" class="carousel slide mt-0 pt-0" data-ride="carousel" style="margin-top: -50px;">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="img/carousel1.jpg" alt="Carousel Image" style="width:100%; height:auto;">
            <div class="carousel-caption">
                <p class="animated fadeInRight">Temukan Rumah Sempurna</p>
                <h1 class="animated fadeInLeft">Untuk Tinggal Bersama Keluarga Anda</h1>
                <a href="{{ url('/property-list') }}" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">
                    Explore Lebih Lanjut
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Carousel End -->


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
                <div class="col-md-2">
                    <select id="province" name="province" class="form-select border-0 py-3">
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



        <!-- Category Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Property Types</h1>
                    <p>Temukan beragam tipe properti terbaik sesuai kebutuhan Anda — dari rumah lelang harga miring hingga apartemen modern dan ruko strategis. Pilih jenis properti favorit Anda dan mulai jelajahi sekarang!</p>
                </div>
                <div class="row g-4">
                    @foreach ($properties as $property)
                        @php
                            // Capitalize tiap kata pada tipe properti
                            $tipeFormatted = ucwords($property->tipe);
                            $isDisabled = $property->total == 0;
                            @endphp

                        <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                            <a
                                class="cat-item d-block text-center rounded p-3 {{ $isDisabled ? 'bg-light opacity-50 pointer-events-none' : 'bg-light' }}"
                                href="{{ $isDisabled ? 'javascript:void(0);' : route('property.list', ['property_type' => strtolower($property->tipe)]) . '#property-list-section' }}"
                            >
                                <div class="rounded p-4 border {{ $isDisabled ? 'border-secondary' : '' }}">
                                    <div class="icon mb-3">
                                        <img
                                            class="img-fluid {{ $isDisabled ? 'grayscale' : '' }}"
                                            src="{{ asset('img/' . strtolower($property->tipe) . '.png') }}"
                                            alt="Icon {{ $tipeFormatted }}">
                                    </div>
                                    <h6 class="{{ $isDisabled ? 'text-muted' : 'text-dark' }}">{{ $tipeFormatted }}</h6>
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
        <div class="property-item rounded overflow-hidden flex-fill d-flex flex-column">
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
                    }
                    .property-item .p-4 {
                        flex-grow: 1; /* supaya bagian konten mengisi ruang yang ada */
                    }
                </style>
                <img class="img-fluid" src="{{ explode(',', $property->gambar)[0] }}" alt="Gambar {{ $property->tipe }}">

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


                                        </div>
                                    </div>
                                </div>
                            </section>
                            <!-- Hot Listing Section End -->
                            <div class="col-12 text-center">
                                <a href="{{ url('/property-list') }}" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">Explore Lebih Lanjut</a>
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

@include('template.header')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg z-3" role="alert" style="min-width: 300px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-success alert-danger fade show position-fixed top-0 end-0 m-3 shadow-lg z-3" role="alert" style="min-width: 300px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
        <!-- Property List Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="bg-light rounded p-3">
                    <div class="bg-white rounded p-4" style="border: 1px dashed rgba(0, 185, 142, .3)">
                        <div class="row g-5 align-items-center">
                            <!-- Swiper CSS -->
                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />

                            <div class="col-lg-6 wow fadeIn">
                                <!-- Aspect Ratio Box -->
                                <div class="position-relative">
                                    <div class="swiper mySwiperMain rounded overflow-hidden">
                                        <div class="swiper-wrapper">
                                            @foreach(explode(',', $property->gambar) as $index => $image)
                                                <div class="swiper-slide">
                                                    <div class="img-wrapper">
                                                        <img src="{{ $image }}" alt="Property Image">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Custom Buttons -->
                                        <!-- Arrow Left -->
                                        <button id="prevBtn" class="carousel-control-prev custom-btn" type="button">
                                            <span class="custom-btn-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" width="24" height="24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </span>
                                        </button>

                                        <!-- Arrow Right -->
                                        <button id="nextBtn" class="carousel-control-next custom-btn" type="button">
                                            <span class="custom-btn-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="2" width="24" height="24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </span>
                                        </button>
                                    </div>

                                    <style>
                                    /* Wrapper to maintain ratio */
                                    .img-wrapper {
                                        width: 100%;
                                        aspect-ratio: 16 / 9;
                                        overflow: hidden;
                                        border-radius: 10px;
                                    }

                                    .img-wrapper img {
                                        width: 100%;
                                        height: 100%;
                                        object-fit: cover;
                                    }

                                    /* Custom Nav Buttons */
                                    .custom-btn {
                                        position: absolute;
                                        top: 50%;
                                        transform: translateY(-50%);
                                        width: 48px;
                                        height: 48px;
                                        background: rgba(0, 0, 0, 0.6); /* lebih gelap */
                                        border-radius: 50%;
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                        border: none;
                                        cursor: pointer;
                                        z-index: 10;
                                        transition: background 0.3s, transform 0.2s;
                                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4); /* bayangan */
                                    }

                                    .custom-btn:hover {
                                        background: rgba(0, 0, 0, 0.8);
                                        transform: translateY(-50%) scale(1.1);
                                    }

                                    .custom-btn-icon {
                                        width: 24px;
                                        height: 24px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                    }



                                    #prevBtn { left: 16px; }
                                    #nextBtn { right: 16px; }

                                    @media (max-width: 768px) {
                                        .img-wrapper {
                                            aspect-ratio: 4 / 3;
                                        }
                                    }
                                    </style>

                                    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
                                    <script>
                                        const swiper = new Swiper(".mySwiperMain", {
                                            loop: true,
                                            spaceBetween: 10,
                                        });

                                        document.getElementById('prevBtn').addEventListener('click', (e) => {
                                            e.preventDefault();
                                            swiper.slidePrev();
                                        });

                                        document.getElementById('nextBtn').addEventListener('click', (e) => {
                                            e.preventDefault();
                                            swiper.slideNext();
                                        });
                                    </script>

                                </div>
                            </div>

                            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                                <div class="mb-4">
                                    <h1 class="mb-3 fs-3 fs-md-2 lh-base">{{ $property->judul }}</h1>

                                    <!-- Harga & Jaminan -->
                                    <div class="row text-center mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-center">
                                        <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                            <div class="text-muted mb-1">Harga</div>
                                            <div class="fw-bold fs-5 text-secondary text-break">
                                                Rp.{{ number_format($property->harga, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="text-muted mb-1">Uang Jaminan</div>
                                            <div class="fw-bold fs-5 text-secondary text-break">
                                                Rp.{{ number_format($property->uang_jaminan, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Properti -->
                                <div class="row mb-4">
                                    <div class="col-md-6 col-lg-4 text-center border-top border-bottom py-3">
                                        <span class="d-inline-block text-black mb-0 caption-text">Luas Tanah</span>
                                        <strong class="d-block">{{ $property->luas }} m<sup>2</sup></strong>
                                    </div>
                                    <div class="col-md-6 col-lg-4 text-center border-top border-bottom py-3">
                                        <span class="d-inline-block text-black mb-0 caption-text">Sertifikat</span>
                                        <strong class="d-block">{{ $property->sertifikat }}</strong>
                                    </div>
                                    <div class="col-md-6 col-lg-4 text-center border-top border-bottom py-3">
                                        <span class="d-inline-block text-black mb-0 caption-text">Batas Setoran Jaminan</span>
                                        <strong class="d-block">{{ \Carbon\Carbon::parse($property->batas_akhir_jaminan)->format('d M Y') }}</strong>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-md-row flex-md-nowrap gap-2 mt-4 justify-content-md-center">
                                    <!-- Hubungi Agent -->
                                    <a href="{{ $property->agent && $property->agent->nomor_telepon
                                        ? 'https://wa.me/62' . ltrim($property->agent->nomor_telepon, '0') . '?text=' . urlencode('Halo ' . $property->agent->nama . ', saya melihat property "' . $property->lokasi . '" di website. Bisa minta info lebih lengkap tentang property tersebut?')
                                        : '#' }}"
                                        class="btn btn-danger px-3 py-2 flex-shrink-1"
                                        style="min-width: 180px;"
                                        {{ $property->agent && $property->agent->nomor_telepon ? '' : 'onclick="return false;"' }}>
                                        <i class="fa fa-phone-alt me-2"></i>Hubungi Agent
                                    </a>

                                    <!-- Ikuti / Login -->
                                    @if (Session::has('id_account') || Cookie::has('id_account'))
                                        <a href="{{ route('property.interest.show', $property->id_listing) }}"
                                           class="btn btn-danger px-3 py-2 flex-shrink-1"
                                           style="min-width: 180px;">
                                           <i class="fa fa-calendar-alt me-2"></i>Ikuti Lelang Ini
                                        </a>
                                        @else
                                        <a href="{{ url('login') }}"
                                           class="btn btn-dark-blue px-3 py-2 flex-shrink-1"
                                           style="min-width: 180px;">
                                           <i class="fa fa-lock me-2"></i>Login untuk Ikut Lelang
                                        </a>
                                    @endif

                                    <!-- Edit -->
                                    @if (Session::has('id_account'))
                                        @php
                                            $loggedInId = Session::get('id_account');
                                            $loggedInAgentId = \App\Models\Agent::where('id_account', $loggedInId)->value('id_agent');
                                        @endphp
                                        @if ($property->id_agent === $loggedInAgentId)
                                            <a href="{{ route('editproperty', $property->id_listing) }}"
                                               class="btn btn-warning text-black px-3 py-2 flex-shrink-1"
                                               style="min-width: 180px;">
                                               <i class="fa fa-edit me-2"></i>Edit Properti
                                            </a>
                                        @endif
                                    @endif
                                </div>

                            </div>


                            <!-- Swiper JS -->
                            <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
                            <script>
                                const swiper = new Swiper(".mySwiperMain", {
                                    loop: true,
                                    spaceBetween: 10,
                                });

                                document.getElementById('prevBtn').addEventListener('click', function(e) {
                                    e.preventDefault();
                                    swiper.slidePrev();
                                });

                                document.getElementById('nextBtn').addEventListener('click', function(e) {
                                    e.preventDefault();
                                    swiper.slideNext();
                                });
                            </script>

                            <style>
                                .aspect-ratio-box {
                                    position: relative;
                                    width: 100%;
                                    padding-top: 56.25%; /* 16:9 */
                                    overflow: hidden;
                                    border-radius: 10px;
                                }

                                .aspect-ratio-box .swiper-slide img {
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                }

                                .custom-btn {
                                    position: absolute;
                                    top: 50%;
                                    transform: translateY(-50%);
                                    width: 40px;
                                    height: 40px;
                                    background: rgba(0, 0, 0, 0.5);
                                    border-radius: 50%;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    border: none;
                                    cursor: pointer;
                                    z-index: 10;
                                }

                                .custom-btn-icon {
                                    color: white;
                                    font-size: 18px;
                                    font-weight: bold;
                                }

                                #prevBtn {
                                    left: 10px;
                                }

                                #nextBtn {
                                    right: 10px;
                                }

                                @media (max-width: 768px) {
                                    .aspect-ratio-box {
                                        padding-top: 66.66%; /* 3:2 for mobile */
                                    }

                                    .custom-btn {
                                        width: 35px;
                                        height: 35px;
                                    }

                                    .custom-btn-icon {
                                        font-size: 16px;
                                    }
                                }
                            </style>
                        </div>


                        <div class="single-property section">
                            <div class="container">
                                <div class="row">
                                    <section id="features" class="features section">
                                        <!-- Section Title -->
                                        <div class="container-xxl py-2" data-aos="fade-up">
                                        </div><!-- End Section Title -->
                                        <div class="container-fluid px-3 px-md-5" data-aos="fade-up" data-aos-delay="100">
                                        <div class="row">
                                            <div class="col-lg-3">
                                              <ul class="nav nav-tabs flex-column">
                                                <li class="nav-item mb-2">
                                                    <a class="nav-link active show" data-bs-toggle="tab" href="#features-tab-1"><i class="fa fa-tag me-2"></i>Harga Properti</a>
                                                </li>
                                                <li class="nav-item mb-2">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#features-tab-2"><i class="fa fa-list me-2"></i>Spesifikasi</a>
                                                </li>
                                                <li class="nav-item mb-2">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#features-tab-3"><i class="fa fa-map-marked-alt me-2"></i>Google Maps</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#features-tab-4">
                                                        <i class="fa fa-chart-line me-2"></i>Analisa market
                                                      </a>
                                                </li>
                                              </ul>
                                            </div>
                                            <div class="col-lg-9 mt-4 mt-lg-0">
                                              <div class="tab-content">
                                                <div class="tab-pane active show" id="features-tab-1">
                                                    <div class="card shadow-sm border-0 p-4 mb-4">
                                                        <h4 class="text-primary">Harga Properti</h4>
                                                        <!-- TABEL VERSI DESKTOP -->
                                                            <div class="table-responsive d-none d-md-block">
                                                                <table class="table table-bordered align-middle mt-3">
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light w-50">Harga Properti</th>
                                                                            <td><strong class="text-dark">Rp {{ number_format($property->harga, 0, ',', '.') }}</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Biaya Dokumen</th>
                                                                            <td>Rp {{ number_format($property->harga * 0.085, 0, ',', '.') }} (8,5% dari harga)</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Biaya Pengosongan</th>
                                                                            <td>
                                                                                @php
                                                                                    $biayaPengosongan = match(true) {
                                                                                        $property->harga < 500000000 => 100000000,
                                                                                        $property->harga <= 1500000000 => 125000000,
                                                                                        $property->harga <= 2500000000 => 175000000,
                                                                                        $property->harga <= 10000000000 => 225000000,
                                                                                        $property->harga <= 100000000000 => 375000000,
                                                                                        $property->harga <= 250000000000 => 525000000,
                                                                                        default => 1025000000
                                                                                    };
                                                                                @endphp
                                                                                Rp {{ number_format($biayaPengosongan, 0, ',', '.') }}
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <!-- MOBILE CARD VERSION -->
<!-- MOBILE CARD VERSION -->
<div class="d-block d-md-none mt-3">
    <div class="container"> <!-- Ini bikin lebarnya nyesuaiin sama konten atas -->
      <div class="row g-3">
        <div class="col-12">
          <div class="p-3 rounded-3 shadow-sm border bg-white w-100">
            <div class="text-uppercase small text-muted mb-1">Harga Properti</div>
            <div class="fw-bold text-primary fs-5">Rp {{ number_format($property->harga, 0, ',', '.') }}</div>
          </div>
        </div>
        <div class="col-12">
          <div class="p-3 rounded-3 shadow-sm border bg-white w-100">
            <div class="text-uppercase small text-muted mb-1">Biaya Dokumen</div>
            <div class="text-secondary">
              <span class="fw-semibold text-primary">Rp {{ number_format($property->harga * 0.085, 0, ',', '.') }}</span>
              <div class="small text-muted">(8,5% dari harga)</div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="p-3 rounded-3 shadow-sm border bg-white w-100">
            <div class="text-uppercase small text-muted mb-1">Biaya Pengosongan</div>
            <div class="text-secondary fw-semibold text-primary">Rp {{ number_format($biayaPengosongan, 0, ',', '.') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>



                                                        </div>
                                                    </div>

                                                    <div class="tab-pane" id="features-tab-2">
                                                        <div class="card shadow-sm border-0 p-4 mb-4">
                                                            <h4 class="text-primary mb-3">Spesifikasi Properti</h4>

                                                            <div class="table-responsive">
                                                                <table class="table table-bordered align-middle mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Tipe</th>
                                                                            <td class="text-capitalize">{{ $property->tipe }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Luas Tanah</th>
                                                                            <td>{{ $property->luas }} m²</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Sertifikat</th>
                                                                            <td>{{ $property->sertifikat }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Lokasi</th>
                                                                            <td>{{ $property->kelurahan }}, {{ $property->kota }}, {{ $property->provinsi }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Batas Penawaran</th>
                                                                            <td>{{ \Carbon\Carbon::parse($property->batas_akhir_penawaran)->format('d M Y') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Status</th>
                                                                            <td>
                                                                                <span class="badge {{ $property->status == 'Tersedia' ? 'bg-success' : 'bg-danger' }}">
                                                                                    {{ $property->status }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="tab-pane" id="features-tab-3">
                                                        @php
                                                            $encodedAlamat = urlencode($property->lokasi); // Pastikan $property->alamat sudah tersedia
                                                            $apiKey = 'AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8'; // Ganti dengan API key kamu sendiri jika diperlukan
                                                            $gmapSrc = "https://www.google.com/maps/embed/v1/place?q={$encodedAlamat}&key={$apiKey}";
                                                        @endphp

                                                        <div class="row g-4 align-items-stretch">
                                                            <!-- Map -->
                                                            <div class="col-md-8">
                                                                <div class="rounded shadow-sm overflow-hidden" style="height: 410px;">
                                                                    <iframe
                                                                        src="{{ $gmapSrc }}"
                                                                        style="width: 100%; height: 100%; border: 0;"
                                                                        allowfullscreen
                                                                        loading="lazy"
                                                                        referrerpolicy="no-referrer-when-downgrade">
                                                                    </iframe>
                                                                </div>
                                                            </div>

                                                            <!-- Location Info & Nearby -->
                                                            <div class="col-md-4">
                                                                <div class="card border-0 shadow-sm h-100">
                                                                    <div class="card-body">
                                                                        <h5 class="card-title text-primary mb-3"><i class="fa fa-map-marker-alt me-2"></i>Detail Lokasi</h5>
                                                                        <p class="mb-2"><strong>Alamat:</strong><br>{{ $property->lokasi }}</p>
                                                                        <p class="mb-2"><strong>Kota:</strong> {{ $property->kota }}</p>
                                                                        <p class="mb-3"><strong>Kelurahan:</strong> {{ $property->kelurahan }}</p>
                                                                        <a href="https://maps.google.com/?q={{ urlencode($property->lokasi) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 mb-3">
                                                                            <i class="fa fa-location-arrow me-1"></i> Buka di Google Maps
                                                                        </a>

                                                                        <hr>

                                                                        <h6 class="text-muted mb-3">Fasilitas Sekitar</h6>
                                                                        <div class="row text-center small">
                                                                            <div class="col-6 mb-3">
                                                                                <i class="fa fa-utensils fa-lg text-success mb-1"></i><br>Restoran
                                                                            </div>
                                                                            <div class="col-6 mb-3">
                                                                                <i class="fa fa-bed fa-lg text-info mb-1"></i><br>Hotel
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <i class="fa fa-hospital fa-lg text-danger mb-1"></i><br>Rumah Sakit
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <i class="fa fa-bus fa-lg text-warning mb-1"></i><br>Transportasi
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="features-tab-4">
                                                        <div class="row">
                                                            <!-- Keunggulan Properti (Kiri) -->
                                                            <div class="col-lg-8 details order-2 order-lg-1">
                                                                <h3 class="text-primary mb-4">Mengapa Properti Ini Pilihan Terbaik untuk Anda?</h3>
                                                                <p class="mb-3">Kami tidak hanya menjual rumah, kami menawarkan <strong>gaya hidup dan kenyamanan</strong> jangka panjang. Berikut adalah alasan mengapa properti ini sangat menarik dan bernilai tinggi:</p>

                                                                <ul class="list-group list-group-flush mb-4">
                                                                <li class="list-group-item d-flex align-items-center">
                                                                    <i class="fa fa-map-marker-alt text-success me-3"></i>
                                                                    <span><strong>Lokasi Strategis:</strong> Terletak di {{ $property->kelurahan }}, {{ $property->kota }} — kawasan yang berkembang pesat dan dekat pusat kota.</span>
                                                                </li>
                                                                <li class="list-group-item d-flex align-items-center">
                                                                    <i class="fa fa-shield-alt text-success me-3"></i>
                                                                    <span><strong>Legalitas Terjamin:</strong> Sertifikat resmi jenis <strong>{{ $property->sertifikat }}</strong> menjamin keamanan transaksi Anda.</span>
                                                                </li>
                                                                <li class="list-group-item d-flex align-items-center">
                                                                    <i class="fa fa-home text-success me-3"></i>
                                                                    <span><strong>Bangunan Berkualitas:</strong> Dengan luas bangunan {{ $property->luas_bangunan }} m² dan {{ $property->lantai }} lantai, cocok untuk keluarga besar atau investasi kos.</span>
                                                                </li>
                                                                <li class="list-group-item d-flex align-items-center">
                                                                    <i class="fa fa-tree text-success me-3"></i>
                                                                    <span><strong>Lingkungan Nyaman:</strong> Dikelilingi area hijau, aman, dan minim polusi — cocok untuk hunian sehat dan tenang.</span>
                                                                </li>
                                                                <li class="list-group-item d-flex align-items-center">
                                                                    <i class="fa fa-money-bill-wave text-success me-3"></i>
                                                                    <span><strong>Harga Kompetitif:</strong> Hanya <strong>Rp {{ number_format($property->harga, 0, ',', '.') }}</strong>, setara atau lebih murah dari properti sekelas di area yang sama.</span>
                                                                </li>
                                                                </ul>
                                                            </div>

                                                            <!-- Analisa Harga dan Diskon Properti (Kanan) -->
                                                            <div class="col-lg-4 order-1 order-lg-2">
                                                                <div class="row">
                                                                    <!-- Rentang Harga Pasaran -->
                                                                    <div class="col-md-12 mb-4">
                                                                        <div class="alert alert-info d-flex align-items-center">
                                                                            <i class="fa fa-lightbulb me-3"></i>
                                                                            <div>
                                                                                <strong>Rentang Harga Pasaran per m² di {{ $property->kelurahan ?? $property->kecamatan }}:</strong>
                                                                                @if ($minPricePerM2 == 0 || $maxPricePerM2 == 0)
                                                                                    <br><strong>Tidak ada properti sebanding di area ini untuk perbandingan harga.</strong>
                                                                                @else
                                                                                    <br>Rp {{ number_format($minPricePerM2, 0, ',', '.') }} /m² - Rp {{ number_format($maxPricePerM2, 0, ',', '.') }} /m²
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Harga Tengah (Median) -->
                                                                    <div class="col-md-12 mb-4">
                                                                        <div class="alert alert-info d-flex align-items-center">
                                                                            <i class="fa fa-chart-line me-3"></i>
                                                                            <div>
                                                                                <strong>Harga Tengah (Median) per m²:</strong>
                                                                                @if ($medianPricePerM2 == 0 || empty($medianPricePerM2))
                                                                                    <br><strong>Tidak ada properti sebanding di area ini untuk perbandingan harga.</strong>
                                                                                @else
                                                                                    <br>Rp {{ number_format($medianPricePerM2, 0, ',', '.') }} /m²
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Diskon Properti -->
                                                                    <div class="col-md-12 mb-4">
                                                                        <div class="alert alert-warning d-flex align-items-center">
                                                                            <i class="fa fa-percent me-3"></i>
                                                                            <div>
                                                                                <strong>Diskon Properti:</strong>
                                                                                @if (is_string($selisihPersen))
                                                                                    <p>{{ $selisihPersen }}</p>
                                                                                @else
                                                                                    <p>Harga rata-rata properti di wilayah ini adalah Rp {{ number_format($avgPricePerM2, 0, ',', '.') }} /m², sementara properti ini dijual dengan harga <strong>Rp {{ number_format($thisPricePerM2, 0, ',', '.') }} /m²</strong>.</p>
                                                                                    @if ($selisihPersen >= 0)
                                                                                        <p>Properti ini lebih murah <strong>{{ number_format($selisihPersen, 2, ',', '.') }}%</strong> dibanding rata-rata.</p>
                                                                                    @else
                                                                                        <p>Properti ini lebih mahal <strong>{{ number_format(abs($selisihPersen), 2, ',', '.') }}%</strong> dibanding rata-rata.</p>
                                                                                    @endif
                                                                                @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section><!-- /Features Section -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-top border-2 border-dashed border-secondary">

            {{-- Property Serupa --}}
            <style>
                .property-item img {
                    height: 200px;
                    object-fit: cover;
                }

                .overflow-auto::-webkit-scrollbar {
                    height: 6px;
                }

                .overflow-auto::-webkit-scrollbar-thumb {
                    background: #ddd;
                    border-radius: 4px;
                }

                .overflow-auto {
                    scrollbar-color: #ccc transparent;
                    scrollbar-width: thin;
                }
            </style>
<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"
/>

{{-- Sliding Properti Serupa --}}
{{-- Sliding Properti Serupa --}}
<h4 class="mb-3">Properti Serupa di {{ $similarLocation }}</h4>

<div class="swiper mySwiper">
    <div class="swiper-wrapper">
        @foreach ($similarProperties as $property)
        <div class="swiper-slide">
            <div class="property-item rounded overflow-hidden shadow-sm">
                <div class="position-relative overflow-hidden">
                    <a href="{{ route('property-detail', $property->id_listing) }}">
                        <img class="img-fluid rounded w-100" src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">
                    </a>
                    <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-2 py-1 px-3">{{ $property->tipe }}</div>
                    <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-2 pt-1 px-3">{{ $property->tipe }}</div>
                </div>
                <div class="p-3">
                    <h5 class="text-primary mb-2">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
                    <a class="d-block h6 mb-2" href="{{ route('property-detail', $property->id_listing) }}">
                        {{ \Illuminate\Support\Str::limit($property->deskripsi, 50) }}
                    </a>
                    <p><i class="fa fa-map-marker-alt text-primary me-2"></i>{{ $property->lokasi }}</p>
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

    <!-- Tombol Navigasi -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const swiper = new Swiper(".mySwiper", {
            slidesPerView: 1.2,
            spaceBetween: 15,
            loop: true,
            grabCursor: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                576: {
                    slidesPerView: 2.2,
                },
                768: {
                    slidesPerView: 3,
                },
                992: {
                    slidesPerView: 4,
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper(".mySwiper", {
        slidesPerView: 1.2,
        spaceBetween: 15,
        loop: true,
        grabCursor: true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        breakpoints: {
            576: {
                slidesPerView: 2.2,
            },
            768: {
                slidesPerView: 3,
            },
            992: {
                slidesPerView: 4,
            }
        }
    });
</script>
<style>
    .swiper {
        padding-bottom: 30px;
    }
    .swiper-slide {
        width: 320px;
    }
    .swiper-button-next, .swiper-button-prev {
        color: #ff6600; /* warna tombol navigasi */
    }
    .swiper-slide {
    display: flex;
    height: auto; /* Biarkan Swiper menyesuaikan tinggi otomatis */
}

.property-item {
    height: 100%; /* Buat semua card setara tinggi */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.2s ease;
}

.property-item:hover {
    transform: translateY(-4px);
}
.property-item a.d-block.h6 {
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Maks 2 baris */
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.swiper {
    padding-bottom: 40px; /* Tambahkan space bawah slider */
}

.swiper-button-next,
.swiper-button-prev {
    color: #ff6600; /* Warna panah sesuai brand */
    top: 45%; /* Posisikan panah di tengah */
}

    </style>


        </div>





        <!-- Property List End -->




@include('template.footer')

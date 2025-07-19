@include('template.header')

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />

<section class="container my-5">
    <div class="row g-4">
        <!-- Swiper Carousel -->
        <div class="col-lg-6">
            <div class="position-relative">
                <div class="swiper mySwiperMain rounded shadow-sm">
                    <div class="swiper-wrapper">
                        @foreach(explode(',', $property->gambar) as $image)
                            <div class="swiper-slide">
                                <div class="square-container">
                                    <img src="{{ $image }}" alt="Property Image" class="rounded">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Custom Navigation Buttons -->
                <button id="prevBtn" class="carousel-control-prev custom-btn" type="button">
                    <span class="custom-btn-icon">&larr;</span>
                </button>
                <button id="nextBtn" class="carousel-control-next custom-btn" type="button">
                    <span class="custom-btn-icon">&rarr;</span>
                </button>
            </div>
        </div>

        <!-- Detail & Form -->
<div class="col-lg-6">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-primary text-white fw-semibold fs-5">
            <i class="fa fa-circle-info me-2"></i> Detail Properti
        </div>
        <div class="card-body">
            <dl class="row mb-3">
                <dt class="col-sm-5 text-muted d-flex align-items-center">
                    <i class="fa fa-tag me-2 text-primary"></i> Judul
                </dt>
                <dd class="col-sm-7 fw-semibold">: {{ $property->judul }}</dd>

                <dt class="col-sm-5 text-muted d-flex align-items-center">
                    <i class="fa fa-location-dot me-2 text-success"></i> Lokasi
                </dt>
                <dd class="col-sm-7">: {{ $property->lokasi }}</dd>

                <dt class="col-sm-5 text-muted d-flex align-items-center">
                    <i class="fa fa-money-bill-wave me-2 text-warning"></i> Harga Deal
                </dt>
                <dd class="col-sm-7">
                    : <span class="badge bg-success fs-6">
                        Rp {{ number_format($property->harga, 0, ',', '.') }}
                    </span>
                </dd>

                <dt class="col-sm-5 text-muted d-flex align-items-center">
                    <i class="fa fa-ruler-combined me-2 text-info"></i> Luas
                </dt>
                <dd class="col-sm-7">: {{ $property->luas }} m²</dd>

                <dt class="col-sm-5 text-muted d-flex align-items-center">
                    <i class="fa fa-file-contract me-2 text-danger"></i> Sertifikat
                </dt>
                <dd class="col-sm-7">: {{ $property->sertifikat }}</dd>
            </dl>

            <hr class="my-4">

            <!-- Simulasi Komisi Agent -->
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fa fa-calculator me-2"></i>
                <div>
                    <strong>Proyeksi Komisi Agent:</strong>
                    <span id="proyeksi_komisi" class="fw-bold">Rp 0</span>
                </div>
            </div>

            <!-- Form Harga Bidding -->
            <form action="{{ route('agent.closing') }}" method="POST" class="mt-3">
                @csrf
                @method('POST')
                <input type="hidden" name="id_agent" value="{{ $property->id_agent }}">
                <input type="hidden" name="id_klien" value="{{ $id_klien }}">
                <input type="hidden" name="id_listing" value="{{ $property->id_listing }}">
                <input type="hidden" name="harga_deal" value="{{ $property->harga }}">

                <div class="mb-3">
                    <label for="harga_bidding" class="form-label fw-semibold">
                        <i class="fa fa-gavel me-1 text-secondary"></i> Harga Bidding
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" class="form-control" id="harga_bidding" name="harga_bidding" placeholder="Masukkan harga bidding" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fa fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="fa fa-check-circle me-1"></i> Submit Closing
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<!-- Script Format Rupiah & Hitung Komisi -->
<script>
    new AutoNumeric('#harga_bidding', {
    digitGroupSeparator: '.',
    decimalCharacter: ',',
    decimalPlaces: 0,
    unformatOnSubmit: true
});

document.addEventListener('DOMContentLoaded', function () {
    const hargaDeal = {{ $property->harga }};
    const inputBidding = document.getElementById('harga_bidding');
    const proyeksiKomisi = document.getElementById('proyeksi_komisi');

    // Format input saat mengetik
    inputBidding.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, ''); // Hapus semua non-digit
        this.value = formatRupiah(value);

        // Hitung proyeksi komisi
        const bidding = parseInt(value) || 0;
        const sisa = hargaDeal - bidding;
        const komisi = sisa > 0 ? sisa * 0.4 : 0;
        proyeksiKomisi.textContent = 'Rp ' + formatRupiah(komisi.toString());
    });

    function formatRupiah(angka) {
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
</script>


<!-- FontAwesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    </div>
</section>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script>
    // Init Swiper
    var swiper = new Swiper(".mySwiperMain", {
        spaceBetween: 10,
        loop: true,
    });

    // Custom Buttons Control Swiper
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
    /* Make Image Square and Crop Center */
    .square-container {
        position: relative;
        width: 100%;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        overflow: hidden;
        border-radius: 0.5rem;
    }
    .square-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover; /* Crop to square */
    }

    /* Custom Button Style */
    .custom-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 45px;
        height: 45px;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        border: none;
        cursor: pointer;
        z-index: 10;
        transition: background 0.3s, transform 0.2s;
    }
    .custom-btn:hover {
        background: rgba(0, 0, 0, 0.7);
        transform: translateY(-50%) scale(1.1);
    }
    .custom-btn-icon {
        color: white;
        font-size: 20px;
        font-weight: bold;
        user-select: none;
    }
    #prevBtn {
        left: 10px;
    }
    #nextBtn {
        right: 10px;
    }
</style>

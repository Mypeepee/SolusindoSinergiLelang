@include('template.header')
        <!-- Header Start -->
        <div class="container-fluid header bg-white p-0">
            <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
                <div class="col-md-6 p-5 mt-lg-5">
                    <h1 class="display-5 animated fadeIn mb-4">Tentang Kami</h1>
                        <nav aria-label="breadcrumb animated fadeIn">
                        <ol class="breadcrumb text-uppercase">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="#">Halaman</a></li>
                            <li class="breadcrumb-item text-body active" aria-current="page">Tentang Kami</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 animated fadeIn">
                    <img class="img-fluid" src="img/header.jpg" alt="">
                </div>
            </div>
        </div>
        <!-- Header End -->


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
                        <p><i class="fa fa-check text-primary me-3"></i>Mempunyai Lebih Dari 100 Ribu Database Listing</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Garansi Serah Terima Asset Kurang dari 1 Tahun</p>
                        <p><i class="fa fa-check text-primary me-3"></i>Tingkat kemenangan kami 100%</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- About End -->

<!-- Facts Start -->
<style>

/*** Facts ***/
.facts {
    position: relative;
    margin: 6rem 0;
    background: var(--dark);
}

.facts .border {
    border-color: rgba(255, 255, 255, .1) !important;
}

</style>
<div class="container-fluid facts my-5 p-5">
    <div class="row g-5">
        <div class="col-md-6 col-xl-3 wow fadeIn" data-wow-delay="0.1s">
            <div class="text-center border p-5">
                <i class="fa fa-certificate fa-3x text-white mb-3"></i>
                <h1 class="display-2 text-primary mb-0" data-toggle="counter-up">8+</h1>
                <span class="fs-5 fw-semi-bold text-white">Tahun Pengalaman</span>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 wow fadeIn" data-wow-delay="0.3s">
            <div class="text-center border p-5">
                <i class="fa fa-users-cog fa-3x text-white mb-3"></i>
                <h1 class="display-2 text-primary mb-0" data-toggle="counter-up">60+</h1>
                <span class="fs-5 fw-semi-bold text-white">Agent Aktif</span>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 wow fadeIn" data-wow-delay="0.5s">
            <div class="text-center border p-5">
                <i class="fa fa-users fa-3x text-white mb-3"></i>
                <h1 class="display-2 text-primary mb-0" data-toggle="counter-up">100+</h1>
                <span class="fs-5 fw-semi-bold text-white">Client Senang</span>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 wow fadeIn" data-wow-delay="0.7s">
            <div class="text-center border p-5">
                <i class="fa fa-check-double fa-3x text-white mb-3"></i>
                <h1 class="display-2 text-primary mb-0" data-toggle="counter-up">100+</h1>
                <span class="fs-5 fw-semi-bold text-white">Projek Sukses</span>
            </div>
        </div>
    </div>
</div>
<!-- Facts End -->

<!-- Features Start -->
<style>
/*** Features ***/
.btn-play {
    position: absolute;
    top: 50%;
    right: -30px;
    transform: translateY(-50%);
    display: block;
    box-sizing: content-box;
    width: 16px;
    height: 26px;
    border-radius: 100%;
    border: none;
    outline: none !important;
    padding: 18px 20px 20px 28px;
    background: var(--primary);
}

@media (max-width: 992px) {
    .btn-play {
        left: 50%;
        right: auto;
        transform: translate(-50%, -50%);
    }
}

.btn-play:before {
    content: "";
    position: absolute;
    z-index: 0;
    left: 50%;
    top: 50%;
    transform: translateX(-50%) translateY(-50%);
    display: block;
    width: 60px;
    height: 60px;
    background: var(--primary);
    border-radius: 100%;
    animation: pulse-border 1500ms ease-out infinite;
}

.btn-play:after {
    content: "";
    position: absolute;
    z-index: 1;
    left: 50%;
    top: 50%;
    transform: translateX(-50%) translateY(-50%);
    display: block;
    width: 60px;
    height: 60px;
    background: var(--primary);
    border-radius: 100%;
    transition: all 200ms;
}

.btn-play span {
    display: block;
    position: relative;
    z-index: 3;
    width: 0;
    height: 0;
    left: -1px;
    border-left: 16px solid #FFFFFF;
    border-top: 11px solid transparent;
    border-bottom: 11px solid transparent;
}

@keyframes pulse-border {
    0% {
        transform: translateX(-50%) translateY(-50%) translateZ(0) scale(1);
        opacity: 1;
    }

    100% {
        transform: translateX(-50%) translateY(-50%) translateZ(0) scale(2);
        opacity: 0;
    }
}

.modal-video .modal-dialog {
    position: relative;
    max-width: 800px;
    margin: 60px auto 0 auto;
}

.modal-video .modal-body {
    position: relative;
    padding: 0px;
}

.modal-video .close {
    position: absolute;
    width: 30px;
    height: 30px;
    right: 0px;
    top: -30px;
    z-index: 999;
    font-size: 30px;
    font-weight: normal;
    color: #FFFFFF;
    background: #000000;
    opacity: 1;
}

</style>
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="position-relative me-lg-4">
                    <img class="img-fluid w-100" src="{{ asset('img/lelangfaktagelap.JPG') }}" alt="Keunggulan Solusindo">
                    <span
                        class="position-absolute top-50 start-100 translate-middle bg-white rounded-circle d-none d-lg-block"
                        style="width: 120px; height: 120px;"></span>
                        <a href="https://vt.tiktok.com/ZSh3AMRds/" target="_blank" class="btn-play">
                            <span></span>
                        </a>

                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <p class="fw-medium text-uppercase text-primary mb-2">Kenapa Harus Pilih Balai Lelang Solusindo?</p>
                <h1 class="display-5 mb-4">Jawaban atas Keraguan Anda Tentang Properti Lelang</h1>
                <p class="mb-4">Banyak orang ragu membeli properti lelang karena takut ribet, tidak aman, atau tidak tahu caranya. Di sinilah Balai Lelang Solusindo hadir sebagai solusi terpercaya yang memudahkan Anda memiliki aset dengan harga jauh di bawah pasaran.</p>
                <div class="row gy-4">
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="fa fa-check text-white"></i>
                            </div>
                            <div class="ms-4">
                                <h4>Harga Di Bawah Pasaran</h4>
                                <span>Kami membantu Anda mendapatkan properti dengan harga miring dari hasil lelang resmi yang legal dan transparan.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="fa fa-check text-white"></i>
                            </div>
                            <div class="ms-4">
                                <h4>Proses Aman & Legal</h4>
                                <span>Kami hanya menangani lelang dari sumber terpercaya seperti bank dan lembaga hukum, sehingga dokumen dan legalitas properti sudah terjamin.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="fa fa-check text-white"></i>
                            </div>
                            <div class="ms-4">
                                <h4>Bimbingan dari Awal Hingga Akhir</h4>
                                <span>Tim kami siap membimbing Anda dari survei lokasi, proses bidding, hingga balik nama sertifikat. Anda tidak dibiarkan bingung sendiri.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="fa fa-check text-white"></i>
                            </div>
                            <div class="ms-4">
                                <h4>Transparan & Tanpa Biaya Tersembunyi</h4>
                                <span>Seluruh biaya dijelaskan sejak awal, tidak ada biaya tersembunyi yang tiba-tiba muncul di akhir.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0 btn-lg-square rounded-circle bg-primary">
                                <i class="fa fa-check text-white"></i>
                            </div>
                            <div class="ms-4">
                                <h4>Dukungan After-Sales</h4>
                                <span>Kami tetap mendampingi Anda bahkan setelah proses lelang selesai, termasuk renovasi atau jual kembali aset Anda.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <!-- Video Modal Start -->
<div class="modal modal-video fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content rounded-0">
        <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLabel">Youtube Video</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <!-- 16:9 aspect ratio -->
            <div class="ratio ratio-16x9">
                <iframe class="embed-responsive-item" src="" id="video" allowfullscreen
                    allowscriptaccess="always" allow="autoplay"></iframe>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Video Modal End --> --}}
<!-- Features End -->
<style>
/*** Service ***/
.service-item {
    position: relative;
    margin: 65px 0 25px 0;
    box-shadow: 0 0 45px rgba(0, 0, 0, .07);
}

.service-item .service-img {
    position: absolute;
    padding: 12px;
    width: 130px;
    height: 130px;
    top: -65px;
    left: 50%;
    transform: translateX(-50%);
    background: #FFFFFF;
    box-shadow: 0 0 45px rgba(0, 0, 0, .09);
    z-index: 2;
}

.service-item .service-detail {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    overflow: hidden;
    z-index: 1;
}

.service-item .service-title {
    position: absolute;
    padding: 65px 30px 25px 30px;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: #FFFFFF;
    transition: .5s;
}

.service-item:hover .service-title {
    top: -100%;
}

.service-item .service-text {
    position: absolute;
    overflow: hidden;
    padding: 65px 30px 25px 30px;
    width: 100%;
    height: 100%;
    top: 100%;
    left: 0;
    display: flex;
    align-items: center;
    text-align: center;
    background: rgba(2, 36, 91, .7);
    transition: .5s;
}

.service-item:hover .service-text {
    top: 0;
}

.service-item .service-text::before {
    position: absolute;
    content: "";
    width: 100%;
    height: 100px;
    top: -100%;
    left: 0;
    transform: skewY(-12deg);
    background: #FFFFFF;
    transition: .5s;
}

.service-item:hover .service-text::before {
    top: -55px;
}

.service-item .btn {
    position: absolute;
    width: 130px;
    height: 50px;
    left: 50%;
    bottom: -25px;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--secondary);
    background: #FFFFFF;
    border: none;
    box-shadow: 0 0 45px rgba(0, 0, 0, .09);
    z-index: 2;
}

.service-item .btn:hover {
    color: #FFFFFF;
    background: var(--primary);
}
</style>
<!-- Service Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center mx-auto pb-4 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 700px;">
            <p class="fw-medium text-uppercase text-primary mb-2">Layanan Kami</p>
            <h1 class="display-5 mb-4">Beli Properti Lelang Tanpa Ribet, Aman & Penuh Potensi Untung</h1>
            <p>Kami paham banyak orang ragu dengan properti lelang. Katanya ribet, berisiko, bahkan menyeramkan. Di Balai Lelang Solusindo, kami hadir untuk mematahkan stigma itu dan menjadikan lelang sebagai peluang yang menguntungkan.</p>
        </div>
        <div class="row gy-5 gx-4">
            <!-- Layanan 1 -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item">
                    <img class="img-fluid" src="img/analisa.png" alt="">
                    <div class="service-img">
                        <img class="img-fluid" src="img/analisa.png" alt="">
                    </div>
                    <div class="service-detail">
                        <div class="service-title">
                            <hr class="w-25">
                            <h3 class="mb-0">Analisa Harga & Potensi Untung</h3>
                            <hr class="w-25">
                        </div>
                        <div class="service-text">
                            <p class="text-white mb-0">Kami bantu bandingkan harga lelang dengan harga pasar sehingga Anda tahu seberapa besar potensi keuntungannya sebelum membeli.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Layanan 2 -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item">
                    <img class="img-fluid" src="img/eksekusi.png" alt="">
                    <div class="service-img">
                        <img class="img-fluid" src="img/eksekusi.png" alt="">
                    </div>
                    <div class="service-detail">
                        <div class="service-title">
                            <hr class="w-25">
                            <h3 class="mb-0">Eksekusi Pengosongan Properti</h3>
                            <hr class="w-25">
                        </div>
                        <div class="service-text">
                            <p class="text-white mb-0">Tak perlu khawatir jika properti masih dihuni. Tim kami siap bantu proses pengosongan secara legal dan humanis sampai properti sepenuhnya bisa Anda kuasai.</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Layanan 3 -->
            <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item">
                    <img class="img-fluid" src="img/serahterima.png" alt="">
                    <div class="service-img">
                        <img class="img-fluid" src="img/serahterima.png" alt="">
                    </div>
                    <div class="service-detail">
                        <div class="service-title">
                            <hr class="w-25">
                            <h3 class="mb-0">Jaminan Ambil Alih Properti</h3>
                            <hr class="w-25">
                        </div>
                        <div class="service-text">
                            <p class="text-white mb-0">Kami berikan pendampingan hingga Anda benar-benar menguasai aset yang Anda beli, bukan sekadar menang lelang saja.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Service End -->


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


<!-- Script Owl Carousel -->
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

<!-- Styles for Testimonial Carousel -->
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
<!-- Testimonial Start -->
<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="text-center">
            <h6 class="text-secondary text-uppercase">Testimonial</h6>
            <h1 class="mb-0">Dari Klien Kami!</h1>
        </div>

        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            @foreach ($testimonials as $testimonial)
                <div class="testimonial-item p-4 my-5">
                    <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                    <div class="d-flex align-items-end mb-4">
                        <img class="img-fluid flex-shrink-0 rounded-circle" src="{{ asset('img/default-user.jpg') }}" style="width: 80px; height: 80px;">
                        <div class="ms-4">
                            <h5 class="mb-1">{{ $testimonial->nama }}</h5>
                            <div class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $testimonial->rating)
                                        <i class="fa fa-star"></i>
                                    @else
                                        <i class="fa fa-star-o"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">{{ $testimonial->comment }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Testimonial End -->

        @include('template.footer')

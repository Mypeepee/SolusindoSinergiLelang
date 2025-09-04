@include('template.header')
<!-- CropperJS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<!-- Feature Start -->
<div class="container-fluid overflow-hidden py-5 px-lg-0">
    <div class="container feature py-5 px-lg-0">
        <div class="row g-5 mx-lg-0">
            <div class="col-lg-6 feature-text wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase mb-3">Kenapa Harus Gabung Jadi Agent Kami?</h6>
                <h1 class="mb-5">Dapatkan Penghasilan Maksimal Tanpa Ribet!</h1>
                <div class="d-flex mb-5 wow fadeInUp" data-wow-delay="0.3s">
                    <i class="fa fa-money-bill-wave text-primary fa-3x flex-shrink-0"></i>
                    <div class="ms-4">
                        <h5>Komisi 3–8x Lipat Lebih Tinggi</h5>
                        <p class="mb-0">Dibanding agensi properti lainnya, komisi kami jauh lebih besar. Semakin rajin closing, semakin besar cuanmu!</p>
                    </div>
                </div>
                <div class="d-flex mb-5 wow fadeIn" data-wow-delay="0.5s">
                    <i class="fa fa-user-friends text-primary fa-3x flex-shrink-0"></i>
                    <div class="ms-4">
                        <h5>Client Disediakan Otomatis</h5>
                        <p class="mb-0">Nggak perlu cari-cari pembeli. Kami punya sistem auto forward yang langsung kirimkan pembeli ke kamu.</p>
                    </div>
                </div>
                <div class="d-flex mb-0 wow fadeInUp" data-wow-delay="0.7s">
                    <i class="fa fa-list text-primary fa-3x flex-shrink-0"></i>
                    <div class="ms-4">
                        <h5>100 Ribu+ Listing Properti di Seluruh Indonesia</h5>
                        <p class="mb-0">Akses properti lelang & undervalue sampai 80% di bawah harga pasar. Listing lengkap, tinggal jual!</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 pe-lg-0 wow fadeInRight" data-wow-delay="0.1s" style="min-height: 400px;">
                <div class="position-relative h-100">
                    <img class="position-absolute img-fluid w-100 h-100" src="img/mengapa.png" style="object-fit: contain; object-position: center; background-color: #fff;" alt="">

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feature End -->

{{-- <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container py-5">
        <div class="text-center">
            <h6 class="text-secondary text-uppercase">Testimonial</h6>
            <h1 class="mb-0">Dari Agent Kami!</h1>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
            <div class="testimonial-item p-4 my-5">
                <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                <div class="d-flex align-items-end mb-4">
                    <img class="img-fluid flex-shrink-0" src="img/testimonial-1.jpg" style="width: 80px; height: 80px;">
                    <div class="ms-4">
                        <h5 class="mb-1">Agnes</h5>
                        <p class="m-0">Mantan Driver Ojol</p>
                    </div>
                </div>
                <p class="mb-0">Dulu saya cuma narik ojol, sekarang saya bisa closing properti ratusan juta. Nggak nyangka bisa bantu keluarga sambil bangun masa depan sendiri!</p>
            </div>
            <div class="testimonial-item p-4 my-5">
                <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                <div class="d-flex align-items-end mb-4">
                    <img class="img-fluid flex-shrink-0" src="img/testimonial-2.jpg" style="width: 80px; height: 80px;">
                    <div class="ms-4">
                        <h5 class="mb-1">Stella</h5>
                        <p class="m-0">Mantan Online Shop</p>
                    </div>
                </div>
                <p class="mb-0">Bisnis online shopku pernah sepi, tapi sejak gabung jadi agent lelang properti, income-ku justru makin stabil. Closing pertama aja udah dapet ratusan juta!</p>
            </div>
            <div class="testimonial-item p-4 my-5">
                <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                <div class="d-flex align-items-end mb-4">
                    <img class="img-fluid flex-shrink-0" src="img/testimonial-3.jpg" style="width: 80px; height: 80px;">
                    <div class="ms-4">
                        <h5 class="mb-1">Grace</h5>
                        <p class="m-0">Ibu dengan Semangat Muda</p>
                    </div>
                </div>
                <p class="mb-0">Usia bukan halangan. Saya sudah kepala lima, tapi penghasilan dari properti bisa puluhan juta per bulan. Saya kerja dari rumah, tetap bisa urus cucu.</p>
            </div>
            <div class="testimonial-item p-4 my-5">
                <i class="fa fa-quote-right fa-3x text-light position-absolute top-0 end-0 mt-n3 me-4"></i>
                <div class="d-flex align-items-end mb-4">
                    <img class="img-fluid flex-shrink-0" src="img/testimonial-4.jpg" style="width: 80px; height: 80px;">
                    <div class="ms-4">
                        <h5 class="mb-1">Shintya</h5>
                        <p class="m-0">Mantan Usaha Catering</p>
                    </div>
                </div>
                <p class="mb-0">Dulu sibuk di dapur, sekarang sibuk bantu orang beli rumah impian. Dari bisnis makanan pindah ke properti, dan hasilnya luar biasa!</p>
            </div>
        </div>
    </div>
</div>
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
    </script> --}}

<!-- Testimonial End -->

<!-- Benefit Start -->
<div class="container-xxl py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase mb-3">Keuntungan Eksklusif</h6>
                <h1 class="mb-5">Benefit yang Cuma Ada di Solusindo</h1>
                <p class="mb-5">Gabung sebagai agent di Solusindo dan nikmati fasilitas premium untuk bantu kamu closing lebih cepat. Tanpa biaya, tanpa ribet.</p>
                <div class="d-flex align-items-center">
                    <i class="fa fa-headphones fa-2x flex-shrink-0 bg-primary p-3 text-white"></i>
                    <div class="ps-4">
                        <h6>Siap sukses bareng Solusindo?</h6>
                        <h3 class="text-primary m-0">+62 813-3571-6679</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-4 align-items-center">
                    <div class="col-sm-6">
                        <div class="p-4 mb-4 wow fadeIn" style="background-color: #f39c12;" data-wow-delay="0.3s">
                            <i class="fa fa-gift fa-2x text-white mb-3"></i>
                            <h5 class="text-white mb-2">Bonus Setiap Closingan</h5>
                            <p class="text-white mb-0">Reward Emas untuk agent yang closing sebagai appresiasi.</p>
                        </div>
                        <div class="p-4 wow fadeIn" style="background-color: #1abc9c;" data-wow-delay="0.5s">
                            <i class="fa fa-book fa-2x text-white mb-3"></i>
                            <h5 class="text-white mb-2">Materi Promosi</h5>
                            <p class="text-white mb-0">Dibekali template, caption, dan panduan promosi.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-4 wow fadeIn" style="background-color: #9b59b6;" data-wow-delay="0.7s">
                            <i class="fa fa-home fa-2x text-white mb-3"></i>
                            <h5 class="text-white mb-2">Listing Pribadi</h5>
                            <p class="text-white mb-0">Bisa upload properti kamu sendiri ke platform kami.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Benefit End -->
<!-- Quote Start -->
<div class="container-xxl py-5">
    <div class="container py-5">
        <div class="row g-5 align-items-center">

            <!-- Left info -->
            <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-secondary text-uppercase mb-3">Gabung Agen</h6>
                <h1 class="mb-4">Formulir Pendaftaran Agen</h1>
                <p class="mb-4">Isi data di samping untuk mendaftar sebagai agen properti resmi kami. Pastikan semua data lengkap dan valid.</p>
                <div class="d-flex align-items-center mb-3">
                    <i class="fa fa-headphones fa-2x flex-shrink-0 bg-primary text-white p-3 rounded-circle"></i>
                    <div class="ps-3">
                        <h6 class="mb-1">Butuh Bantuan?</h6>
                        <h5 class="text-primary m-0">+62 812 3456 7890</h5>
                    </div>
                </div>
            </div>

        <div class="col-lg-7">
            <div class="bg-light rounded p-5 wow fadeIn" data-wow-delay="0.5s">
                @if (isset($isPending) && $isPending === true)
                <div class="alert alert-info text-center mb-0">
                    <h5 class="mb-2">Akun Anda Sedang Diverifikasi</h5>
                    <p class="mb-0">Terima kasih telah mendaftar sebagai agen. Kami sedang memproses verifikasi akun Anda. Silakan tunggu konfirmasi dari Admin.</p>
                </div>
                @elseif (isset($isRejected) && $isRejected === true)
            <div class="alert alert-danger text-center mb-0">
                <h5 class="mb-2">Pendaftaran Ditolak</h5>
                <p class="mb-0">Mohon perbaiki data Anda dan daftar ulang sebagai agen.</p>
            </div>
            <form action="{{ route('join.agent.submit') }}" method="POST" enctype="multipart/form-data" id="agentForm">
                @csrf
                <input type="hidden" name="id_account" value="{{ session('id_account') }}">

                <div class="row g-3">

                    <!-- Nama -->
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" name="nama" value="{{ $user->nama ?? old('nama') }}" class="form-control" placeholder="Nama Lengkap" readonly>
                        </div>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            <input type="text" name="nomor_telepon" value="{{ $user->nomor_telepon ?? old('nomor_telepon') }}" class="form-control" placeholder="Nomor Telepon" readonly>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            <input type="email" name="email" value="{{ $user->email ?? old('email') }}" class="form-control" placeholder="Email Aktif" readonly>
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                            <input type="text" name="instagram" value="{{ old('instagram') }}" class="form-control" placeholder="Instagram">
                        </div>
                    </div>

                    <!-- Facebook -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                            <input type="text" name="facebook" value="{{ old('facebook') }}" class="form-control" placeholder="Facebook">
                        </div>
                    </div>

                    <!-- Provinsi & Kota dalam 1 row -->
                    <div class="col-12 col-md-6">
                        <label class="form-label">Provinsi</label>
                        <select id="province" name="provinsi" class="form-select" required>
                            <option selected disabled value="">Pilih Provinsi</option>
                        </select>
                        <div class="invalid-feedback">Provinsi wajib dipilih.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Kota / Kabupaten</label>
                        <select name="lokasi_kerja" id="city" class="form-select" disabled required>
                            <option selected disabled value="">Pilih Kota/Kabupaten</option>
                        </select>
                        <div class="invalid-feedback">Kota/Kabupaten wajib dipilih.</div>
                    </div>

                    {{-- === KTP (WAJIB UPLOAD) === --}}
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">Foto KTP (Wajib diunggah)</label>
                        <input type="file" id="gambar_ktp" name="gambar_ktp" accept="image/*" class="form-control mb-2" required>
                        <img id="imagePreview" class="img-fluid rounded d-none" style="max-height: 300px;">
                        <input type="hidden" name="cropped_image_ktp" id="cropped_image" required>
                        <div class="invalid-feedback">Foto KTP wajib diunggah.</div>
                    </div>

                    {{-- === NPWP (WAJIB UPLOAD) === --}}
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">Foto NPWP (Wajib diunggah)</label>
                        <input type="file" id="gambar_npwp" name="gambar_npwp" accept="image/*" class="form-control mb-2" required>
                        <img id="imagePreviewNPWP" class="img-fluid rounded d-none" style="max-height: 300px;">
                        <input type="hidden" name="cropped_image_npwp" id="cropped_image_npwp" required>
                        <div class="invalid-feedback">Foto NPWP wajib diunggah.</div>
                    </div>

                    <!-- Foto Profil & Kode Referral dalam 1 row -->
                    <div class="col-12 col-md-6 mb-4">
                        <label for="profileImageInput" class="form-label">Foto Profil (Wajib diunggah)</label>
                        <input type="file" name="profile_image_input" id="profileImageInput" class="form-control" accept="image/*" required>
                        <div id="previewContainer" class="mt-2" style="display: none;">
                            <img id="profilePreview" class="img-fluid rounded" style="width: 150px;" />
                        </div>
                        <input type="hidden" name="cropped_profile_image" id="croppedProfileImage" required>
                        <div class="invalid-feedback">Foto profil wajib diunggah.</div>
                    </div>

                    <!-- Kode Referral -->
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">Kode Referral</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user-plus"></i></span>
                            <input type="text" name="kode_referral" value="{{ old('kode_referral') }}" class="form-control" placeholder="Masukkan Kode Referral (Opsional)">
                        </div>
                        <div class="form-text text-muted">Masukkan kode dari agen yang mereferensikan Anda.</div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="col-12">
                        <button class="btn btn-primary w-100 py-3" type="submit">Daftar Sebagai Agen</button>
                    </div>
                </div>
            </form>
            @else
            <form action="{{ route('join.agent.submit') }}" method="POST" enctype="multipart/form-data" id="agentForm">
                @csrf
                <input type="hidden" name="id_account" value="{{ session('id_account') }}">

                <div class="row g-3">

                    <!-- Nama -->
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" name="nama" value="{{ $user->nama ?? old('nama') }}" class="form-control" placeholder="Nama Lengkap" readonly>
                        </div>
                    </div>

                    <!-- Nomor Telepon -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            <input type="text" name="nomor_telepon" value="{{ $user->nomor_telepon ?? old('nomor_telepon') }}" class="form-control" placeholder="Nomor Telepon" readonly>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            <input type="email" name="email" value="{{ $user->email ?? old('email') }}" class="form-control" placeholder="Email Aktif" readonly>
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                            <input type="text" name="instagram" value="{{ old('instagram') }}" class="form-control" placeholder="Instagram">
                        </div>
                    </div>

                    <!-- Facebook -->
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                            <input type="text" name="facebook" value="{{ old('facebook') }}" class="form-control" placeholder="Facebook">
                        </div>
                    </div>

                    <!-- Provinsi & Kota dalam 1 row -->
                    <div class="col-12 col-md-6">
                        <label class="form-label">Provinsi</label>
                        <select id="province" name="provinsi" class="form-select" required>
                            <option selected disabled value="">Pilih Provinsi</option>
                        </select>
                        <div class="invalid-feedback">Provinsi wajib dipilih.</div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label">Kota / Kabupaten</label>
                        <select name="lokasi_kerja" id="city" class="form-select" disabled required>
                            <option selected disabled value="">Pilih Kota/Kabupaten</option>
                        </select>
                        <div class="invalid-feedback">Kota/Kabupaten wajib dipilih.</div>
                    </div>

                    {{-- === KTP (WAJIB UPLOAD) === --}}
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">Foto KTP (Wajib diunggah)</label>
                        <input type="file" id="gambar_ktp" name="gambar_ktp" accept="image/*" class="form-control mb-2" required>
                        <img id="imagePreview" class="img-fluid rounded d-none" style="max-height: 300px;">
                        <input type="hidden" name="cropped_image_ktp" id="cropped_image" required>
                        <div class="invalid-feedback">Foto KTP wajib diunggah.</div>
                    </div>

                    {{-- === NPWP (WAJIB UPLOAD) === --}}
                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">Foto NPWP (Wajib diunggah)</label>
                        <input type="file" id="gambar_npwp" name="gambar_npwp" accept="image/*" class="form-control mb-2" required>
                        <img id="imagePreviewNPWP" class="img-fluid rounded d-none" style="max-height: 300px;">
                        <input type="hidden" name="cropped_image_npwp" id="cropped_image_npwp" required>
                        <div class="invalid-feedback">Foto NPWP wajib diunggah.</div>
                    </div>

                    <!-- Foto Profil & Kode Referral dalam 1 row -->
                    <div class="col-12 col-md-6 mb-4">
                        <label for="profileImageInput" class="form-label">Foto Profil (Wajib diunggah)</label>
                        <input type="file" name="profile_image_input" id="profileImageInput" class="form-control" accept="image/*" required>
                        <div id="previewContainer" class="mt-2" style="display: none;">
                            <img id="profilePreview" class="img-fluid rounded" style="width: 150px;" />
                        </div>
                        <input type="hidden" name="cropped_profile_image" id="croppedProfileImage" required>
                        <div class="invalid-feedback">Foto profil wajib diunggah.</div>
                    </div>

                    <div class="col-12 col-md-6 mb-4">
                        <label class="form-label">Kode Referal</label>
                        <div class="input-group">
                            <span class="input-group-text">AG</span>
                            <input type="text"
                                   id="kode_referal"
                                   name="kode_referal"
                                   class="form-control @error('kode_referal') is-invalid @enderror"
                                   value="{{ old('kode_referal') }}"
                                   placeholder="Hubungi agent anda untuk mendapatkan kode referal"
                                   pattern="[0-9]{3}"
                                   title="Masukkan 3 digit angka (contoh: 001)">
                        </div>
                        <small class="text-muted">Opsional. Isi jika memiliki kode referal dari Agent.</small>
                        @error('kode_referal')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const urlParams = new URLSearchParams(window.location.search);
                        const ref = urlParams.get('ref');
                        if (ref) {
                            const input = document.getElementById('kode_referal');
                            if (input) {
                                input.value = ref.replace('AG', '');
                                input.readOnly = true;
                            }
                        }
                    });
                    </script>



                    <!-- Tombol Submit -->
                    <div class="col-12">
                        <button class="btn btn-primary w-100 py-3" type="submit" id="submitBtn">Daftar Sebagai Agen</button>
                    </div>
                </div>
            </form>
            <script>
                document.getElementById('agentForm').addEventListener('submit', function(e) {
                    let valid = true;

                    const provinsi = document.getElementById('province');
                    const kota = document.getElementById('city');
                    const ktp = document.getElementById('gambar_ktp');
                    const npwp = document.getElementById('gambar_npwp');
                    const profil = document.getElementById('profileImageInput');

                    [provinsi, kota, ktp, npwp, profil].forEach(el => el.classList.remove('is-invalid'));

                    if (!provinsi.value) { provinsi.classList.add('is-invalid'); valid = false; }
                    if (!kota.value) { kota.classList.add('is-invalid'); valid = false; }
                    if (ktp.files.length === 0) { ktp.classList.add('is-invalid'); valid = false; }
                    if (npwp.files.length === 0) { npwp.classList.add('is-invalid'); valid = false; }
                    if (profil.files.length === 0) { profil.classList.add('is-invalid'); valid = false; }

                    if (!valid) {
                        e.preventDefault();
                        alert('Harap lengkapi semua field wajib sebelum mengirim.');
                    }
                });
                </script>

        @endif
    </div>
</div>
<script>
    document.getElementById('agentForm').addEventListener('submit', function(e) {
        let valid = true;

        // Elemen yang perlu diperiksa
        const provinsi = document.getElementById('province');
        const kota = document.getElementById('city');
        const ktp = document.getElementById('gambar_ktp');
        const npwp = document.getElementById('gambar_npwp');
        const profil = document.getElementById('profileImageInput');
        const croppedKTP = document.getElementById('cropped_image');
        const croppedNPWP = document.getElementById('cropped_image_npwp');
        const croppedProfileImage = document.getElementById('croppedProfileImage');

        // Reset validasi sebelumnya
        [provinsi, kota, ktp, npwp, profil].forEach(el => el.classList.remove('is-invalid'));

        // Validasi inputan provinsi, kota, dan file
        if (!provinsi.value) {
            provinsi.classList.add('is-invalid');
            valid = false;
        }
        if (!kota.value) {
            kota.classList.add('is-invalid');
            valid = false;
        }
        if (ktp.files.length === 0 || !croppedKTP.value) {  // Pastikan file KTP ada dan sudah dicrop
            ktp.classList.add('is-invalid');
            valid = false;
        }
        if (npwp.files.length === 0 || !croppedNPWP.value) {  // Pastikan file NPWP ada dan sudah dicrop
            npwp.classList.add('is-invalid');
            valid = false;
        }
        if (profil.files.length === 0 || !croppedProfileImage.value) {  // Pastikan foto profil ada dan sudah dicrop
            profil.classList.add('is-invalid');
            valid = false;
        }

        // Jika form tidak valid, tampilkan alert dan stop submit
        if (!valid) {
            e.preventDefault();  // Menghentikan proses submit
            alert('Harap lengkapi semua field wajib sebelum mengirim.');
        } else {
            // Jika validasi berhasil, ubah tombol menjadi loading
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengirim...'; // Spinner Loading
            submitBtn.disabled = true; // Nonaktifkan tombol agar tidak bisa diklik dua kali
        }
    });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    let ktpCropper, npwpCropper;

    const previewKTP = document.getElementById('imagePreview');
    const previewNPWP = document.getElementById('imagePreviewNPWP');

    // === GANTI KTP ===
    document.getElementById('gantiKTPBtn')?.addEventListener('click', () => {
      document.getElementById('uploadKTPSection').style.display = 'block';
    });

    document.getElementById('gambar_ktp')?.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;
      const url = URL.createObjectURL(file);
      previewKTP.src = url;
      previewKTP.classList.remove('d-none');

      if (ktpCropper) ktpCropper.destroy();
      setTimeout(() => {
        ktpCropper = new Cropper(previewKTP, {
          aspectRatio: NaN,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true,
          movable: true,
          zoomable: true,
          cropBoxResizable: true,
        });
      }, 200);
    });

    // === GANTI NPWP ===
    document.getElementById('gantiNPWPBtn')?.addEventListener('click', () => {
      document.getElementById('uploadNPWPSection').style.display = 'block';
    });

    document.getElementById('gambar_npwp')?.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;
      const url = URL.createObjectURL(file);
      previewNPWP.src = url;
      previewNPWP.classList.remove('d-none');

      if (npwpCropper) npwpCropper.destroy();
      setTimeout(() => {
        npwpCropper = new Cropper(previewNPWP, {
          aspectRatio: NaN,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true,
          movable: true,
          zoomable: true,
          cropBoxResizable: true,
        });
      }, 200);
    });

    // === Saat submit form ===
    document.querySelector('form')?.addEventListener('submit', function () {
      if (ktpCropper) {
        const canvas = ktpCropper.getCroppedCanvas({ width: 800, height: 600 });
        document.getElementById('cropped_image').value = canvas.toDataURL('image/jpeg');
      }
      if (npwpCropper) {
        const canvas = npwpCropper.getCroppedCanvas({ width: 800, height: 600 });
        document.getElementById('cropped_image_npwp').value = canvas.toDataURL('image/jpeg');
      }
    });
  });
    document.addEventListener('DOMContentLoaded', function () {
    let cropper;
    const input = document.getElementById('profileImageInput');
    const image = document.getElementById('profilePreview');
    const previewContainer = document.getElementById('previewContainer');
    const cropActionsId = 'profileCropActions';

    function showCropActions() {
      let cropActions = document.getElementById(cropActionsId);
      if (!cropActions) {
        cropActions = document.createElement('div');
        cropActions.id = cropActionsId;
        cropActions.classList.add('d-flex', 'gap-2', 'mt-2');
        previewContainer.appendChild(cropActions); // tombol setelah gambar

        const cropBtn = document.createElement('button');
        cropBtn.type = 'button';
        cropBtn.className = 'btn btn-success btn-sm';
        cropBtn.id = 'cropProfileBtn';
        cropBtn.textContent = 'Crop';
        cropActions.appendChild(cropBtn);

        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn-secondary btn-sm';
        cancelBtn.id = 'cancelProfileCropBtn';
        cancelBtn.textContent = 'Cancel';
        cropActions.appendChild(cancelBtn);

        cropBtn.addEventListener('click', cropImage);
        cancelBtn.addEventListener('click', cancelCrop);
      }
      cropActions.style.display = 'flex';
    }

    function hideCropActions() {
      const cropActions = document.getElementById(cropActionsId);
      if (cropActions) cropActions.remove();
    }

    input.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (event) {
        image.src = event.target.result;
        image.style.display = 'block';
        previewContainer.style.display = 'block';
        showCropActions();

        if (cropper) cropper.destroy();

        cropper = new Cropper(image, {
          aspectRatio: 1,
          viewMode: 1,
          autoCropArea: 1,
          dragMode: 'move',
          responsive: true,
          zoomable: true,
          cropBoxMovable: true,
          cropBoxResizable: true,
        });
      };
      reader.readAsDataURL(file);
    });

    function cropImage() {
  if (!cropper) return;

  const canvas = cropper.getCroppedCanvas({
    width: 300,
    height: 300,
    imageSmoothingEnabled: true,
    imageSmoothingQuality: 'high',
  });

  const base64 = canvas.toDataURL('image/jpeg');

  // Set hasil crop ke input hidden dan preview
  document.getElementById('croppedProfileImage').value = base64;
  image.src = base64;

  cropper.destroy();
  cropper = null;
  hideCropActions();
}


    function cancelCrop() {
      if (cropper) {
        cropper.destroy();
        cropper = null;
      }
      input.value = '';
      image.style.display = 'none';
      previewContainer.style.display = 'none';
      hideCropActions();
      document.getElementById('croppedProfileImage').value = '';
    }
  });
    document.addEventListener('DOMContentLoaded', function () {
                                    const province = document.getElementById('province');
                                    const city = document.getElementById('city');
                                    const provinceMap = new Map();

                                    fetch("{{ asset('data/indonesia.json') }}")
                                        .then(res => res.json())
                                        .then(data => {
                                            // Bangun map provinsi => Set kota
                                            data.forEach(({ province: prov, regency }) => {
                                                if (!provinceMap.has(prov)) {
                                                    provinceMap.set(prov, new Set());
                                                }
                                                provinceMap.get(prov).add(regency);
                                            });

                                            // Isi dropdown provinsi (sekali)
                                            [...provinceMap.keys()].sort().forEach(prov => {
                                                const option = document.createElement('option');
                                                option.value = prov;
                                                option.textContent = prov;
                                                province.appendChild(option);
                                            });
                                        });

                                    // Event: isi kota saat provinsi berubah
                                    province.addEventListener('change', function () {
                                        const selectedProv = this.value;
                                        const kotaSet = provinceMap.get(selectedProv);

                                        city.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';
                                        city.disabled = true;

                                        if (kotaSet) {
                                            [...kotaSet]
                                                .sort()
                                                .forEach(kota => {
                                                    const cleaned = kota.replace(/^Kota\s|^Kabupaten\s/, '');
                                                    const option = document.createElement('option');
                                                    option.value = cleaned;
                                                    option.textContent = kota;
                                                    city.appendChild(option);
                                                });
                                            city.disabled = false;
                                        }
                                    });
                                });
    document.addEventListener('DOMContentLoaded', function () {
        let cropper;
        const input = document.getElementById('gambar_ktp');
        const image = document.getElementById('imagePreview');
        const cropActionsId = 'cropActions';

        function showCropActions() {
            let cropActions = document.getElementById(cropActionsId);
            if (!cropActions) {
                cropActions = document.createElement('div');
                cropActions.id = cropActionsId;
                cropActions.classList.add('d-flex', 'gap-2', 'mt-2');
                input.parentNode.appendChild(cropActions);

                const cropBtn = document.createElement('button');
                cropBtn.type = 'button';
                cropBtn.className = 'btn btn-primary btn-sm';
                cropBtn.id = 'cropBtn';
                cropBtn.textContent = 'Crop';
                cropActions.appendChild(cropBtn);

                const cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.className = 'btn btn-secondary btn-sm';
                cancelBtn.id = 'cancelCropBtn';
                cancelBtn.textContent = 'Cancel';
                cropActions.appendChild(cancelBtn);

                cropBtn.addEventListener('click', cropImage);
                cancelBtn.addEventListener('click', cancelCrop);
            }
            cropActions.style.display = 'flex';
        }

        function hideCropActions() {
            const cropActions = document.getElementById(cropActionsId);
            if (cropActions) cropActions.remove();
        }

        input.addEventListener('change', function (e) {
            if (e.target.files && e.target.files.length > 0) {
                const file = e.target.files[0];
                const url = URL.createObjectURL(file);

                image.src = url;
                image.style.display = 'block';
                showCropActions();

                if (cropper) cropper.destroy();

                cropper = new Cropper(image, {
                    aspectRatio: NaN,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    movable: true,
                    zoomable: true,
                    cropBoxResizable: true,
                    cropBoxMovable: true,
                    minCropBoxWidth: 50,
                    minCropBoxHeight: 50,
                });
            }
        });

        function cropImage() {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 800,
                height: 600,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            document.getElementById('cropped_image').value = canvas.toDataURL('image/jpeg');
            hideCropActions();

            cropper.destroy();
            cropper = null;
            image.style.display = 'none';
        }

        function cancelCrop() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            image.style.display = 'none';
            hideCropActions();
            input.value = null;
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
                                    let cropperNPWP;
                                    const inputNPWP = document.getElementById('gambar_npwp');
                                    const imageNPWP = document.getElementById('imagePreviewNPWP');

                                    const cropActionsIdNPWP = 'cropActionsNpwp';

                                    function showCropActionsNPWP() {
                                        let cropActions = document.getElementById(cropActionsIdNPWP);
                                        if (!cropActions) {
                                            cropActions = document.createElement('div');
                                            cropActions.id = cropActionsIdNPWP;
                                            cropActions.classList.add('d-flex', 'gap-2', 'mt-2');
                                            inputNPWP.parentNode.appendChild(cropActions);

                                            const cropBtn = document.createElement('button');
                                            cropBtn.type = 'button';
                                            cropBtn.className = 'btn btn-primary btn-sm';
                                            cropBtn.textContent = 'Crop';
                                            cropBtn.addEventListener('click', cropImageNPWP);
                                            cropActions.appendChild(cropBtn);

                                            const cancelBtn = document.createElement('button');
                                            cancelBtn.type = 'button';
                                            cancelBtn.className = 'btn btn-secondary btn-sm';
                                            cancelBtn.textContent = 'Cancel';
                                            cancelBtn.addEventListener('click', cancelCropNPWP);
                                            cropActions.appendChild(cancelBtn);
                                        }
                                        cropActions.style.display = 'flex';
                                    }

                                    function hideCropActionsNPWP() {
                                        const cropActions = document.getElementById(cropActionsIdNPWP);
                                        if (cropActions) cropActions.remove();
                                    }

                                    inputNPWP.addEventListener('change', function (e) {
                                        if (e.target.files && e.target.files.length > 0) {
                                            const file = e.target.files[0];
                                            const url = URL.createObjectURL(file);

                                            imageNPWP.src = url;
                                            imageNPWP.style.display = 'block';
                                            showCropActionsNPWP();

                                            if (cropperNPWP) cropperNPWP.destroy();

                                            cropperNPWP = new Cropper(imageNPWP, {
                                                aspectRatio: NaN,
                                                viewMode: 1,
                                                autoCropArea: 1,
                                                responsive: true,
                                                movable: true,
                                                zoomable: true,
                                                cropBoxResizable: true,
                                                cropBoxMovable: true,
                                                minCropBoxWidth: 50,
                                                minCropBoxHeight: 50,
                                            });
                                        }
                                    });

                                    function cropImageNPWP() {
                                        if (!cropperNPWP) return;

                                        const canvas = cropperNPWP.getCroppedCanvas({
                                            width: 800,
                                            height: 600,
                                            fillColor: '#fff',
                                            imageSmoothingEnabled: true,
                                            imageSmoothingQuality: 'high',
                                        });

                                        document.getElementById('cropped_image_npwp').value = canvas.toDataURL('image/jpeg');
                                        hideCropActionsNPWP();

                                        cropperNPWP.destroy();
                                        cropperNPWP = null;
                                        imageNPWP.style.display = 'none';
                                    }

                                    function cancelCropNPWP() {
                                        if (cropperNPWP) {
                                            cropperNPWP.destroy();
                                            cropperNPWP = null;
                                        }
                                        imageNPWP.style.display = 'none';
                                        hideCropActionsNPWP();
                                        inputNPWP.value = null;
                                    }
                                });
    </script>
<!-- Quote End -->


          <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

          <!-- Vendor JS Files -->
          <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
          <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
          <script src="assets/vendor/chart.js/chart.umd.js"></script>
          <script src="assets/vendor/echarts/echarts.min.js"></script>
          <script src="assets/vendor/quill/quill.js"></script>
          <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
          <script src="assets/vendor/tinymce/tinymce.min.js"></script>
          <script src="assets/vendor/php-email-form/validate.js"></script>

          <!-- Template Main JS File -->
          <script src="assets/js/mainadmin.js"></script>


          <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

          <!-- Vendor JS Files -->
          <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
          <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
          <script src="assets/vendor/chart.js/chart.umd.js"></script>
          <script src="assets/vendor/echarts/echarts.min.js"></script>
          <script src="assets/vendor/quill/quill.js"></script>
          <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
          <script src="assets/vendor/tinymce/tinymce.min.js"></script>
          <script src="assets/vendor/php-email-form/validate.js"></script>

          <!-- Template Main JS File -->
          <script src="assets/js/main.js"></script>
@include('template.footer')

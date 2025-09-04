@if(isset($ogTags))
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $ogTags['og_url'] }}">
    <meta property="og:title" content="{{ $ogTags['og_title'] }}">
    <meta property="og:description" content="{{ $ogTags['og_description'] }}">

    <meta property="og:image" content="{{ $ogTags['og_image'] }}">
    <meta property="og:image:secure_url" content="{{ $ogTags['og_image'] }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $ogTags['og_title'] }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTags['og_title'] }}">
    <meta name="twitter:description" content="{{ $ogTags['og_description'] }}">
    <meta name="twitter:image" content="{{ $ogTags['og_image'] }}">
@endif


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
                                                <div class="swiper-slide position-relative">
                                                    <div class="img-wrapper">
                                                        <img src="{{ $image }}" alt="Property Image" class="w-100">
                                                    </div>
                                                    <!-- Label ID Listing di pojok kanan atas -->
                                                    <div class="bg-primary rounded text-white position-absolute end-0 top-0 m-2 py-1 px-3">
                                                        ID: {{ $property->id_listing }}
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
                                            aspect-ratio: 4 / 4;
                                        }
                                    }

                                    .btn-dark-blue {
                                        background-color: #0d3b66; /* contoh biru donker */
                                        color: #fff;
                                        border: none;
                                    }

                                    .btn-dark-blue:hover {
                                        background-color: #092c4c;
                                        color: #fff;
                                    }

                                    /* Biar tombol sticky di bawah layar di mobile */
                                    @media (max-width: 768px) {
                                        .position-relative.mt-4 {
                                            position: fixed;
                                            bottom: 0;
                                            left: 0;
                                            right: 0;
                                            background: #fff;
                                            padding: 10px;
                                            box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
                                            z-index: 999;
                                        }
                                        .position-relative.mt-4 .btn {
                                            flex: 1 1 48%;
                                            min-height: 50px;
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

                                @php
                                    $userId = Session::get('id_account') ?? Cookie::get('id_account');
                                    $loggedIn = !empty($userId);

                                    $roleRaw = $loggedIn ? \App\Models\Account::where('id_account', $userId)->value('roles') : null;
                                    $role = strtolower(trim($roleRaw ?? 'User'));

                                    $privilegedRoles = ['agent', 'owner', 'register'];
                                    $showJaminan = $loggedIn && in_array($role, $privilegedRoles, true);

                                    $tglJaminan = $property->batas_akhir_jaminan
                                        ? \Carbon\Carbon::parse($property->batas_akhir_jaminan)->translatedFormat('d M Y')
                                        : '-';

                                    // Sertifikat: Tampilkan hanya SHM atau SHGB jika belum login
                                    $sertifikatDisplay = $loggedIn ? $property->sertifikat : (strstr($property->sertifikat, 'SHM') ? 'SHM' : 'SHGB');

                                    // Batas Penawaran: Tidak ditampilkan jika belum login
                                    $batasPenawaranDisplay = $loggedIn ? \Carbon\Carbon::parse($property->batas_akhir_penawaran)->format('d M Y') : null;
                                @endphp

                                <!-- Detail Properti -->
                                <div class="row mb-4">
                                    <div class="col-md-6 col-lg-4 text-center border-top border-bottom py-3">
                                        <span class="d-inline-block text-black mb-0 caption-text">Luas Tanah</span>
                                        <strong class="d-block">{{ $property->luas }} m<sup>2</sup></strong>
                                    </div>

                                    <div class="col-md-6 col-lg-4 text-center border-top border-bottom py-3">
                                        <span class="d-inline-block text-black mb-0 caption-text">Sertifikat</span>
                                        <strong class="d-block">{{ $sertifikatDisplay }}</strong>
                                    </div>

                                    <div class="col-md-6 col-lg-4 text-center border-top border-bottom py-3">
                                        @if ($showJaminan)
                                            <span class="d-inline-block text-black mb-0 caption-text">Batas Setoran Jaminan</span>
                                            <strong class="d-block">{{ $tglJaminan }}</strong>
                                        @else
                                            <span class="d-inline-block text-black mb-0 caption-text">Tipe</span>
                                            <strong class="d-block">{{ $property->tipe ?? '-' }}</strong>
                                        @endif
                                    </div>
                                </div>

                                <div class="position-relative mt-4">
                                    @php
                                        use App\Models\Account;

                                        $userId = Session::get('id_account') ?? Cookie::get('id_account');
                                        $userRole = $userId ? Account::where('id_account', $userId)->value('roles') : null;
                                        $targetAgent = $sharedAgent ?? $property->agent;
                                        $shareAgent = $sharedAgent ? $sharedAgent->id_agent : ($property->agent->id_agent ?? 'DEFAULT');
                                    @endphp

                                    <!-- Tombol Utama (Hubungi, Ikuti/Login, Share) -->
                                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-md-center align-items-stretch">
                                        <!-- Tombol Hubungi Agent / Tanyakan Stok -->
                                        @php
                                            // === Deteksi login & role via session ===
                                            $loggedIn = session()->has('id_account');                 // true kalau sudah login
                                            $role     = $userRole ?? session('roles', 'User');
                                            if (app('router')->has('property.show')) {
                                                $baseUrl = route('property.show', $property->id_listing);
                                            } else {
                                                $baseUrl = url()->current();
                                            }
                                                                                    $propertyUrl = $baseUrl . '?' . http_build_query([
                                                'src'   => 'wa_group',
                                                'share' => $shareAgent,
                                                // misal sekalian sisipkan referral (kalau mau): 'ref' => $shareAgent,
                                            ]);       // fallback ke 'User' kalau kosong
                                        @endphp
                                        @if (!$loggedIn || $role === 'User')
                                        <!-- Untuk User: tetap tombol WA ke Agent -->
                                        <a href="{{ $targetAgent && $targetAgent->nomor_telepon
                                                ? 'https://wa.me/62' . ltrim($targetAgent->nomor_telepon) . '?text=' . urlencode('Halo ' . $targetAgent->nama . ', saya melihat property "' . $property->lokasi . '" di website. Bisa minta info lebih lengkap tentang property tersebut? Link properti: ' . url('/property-detail/' . $property->id_listing))
                                                : '#' }}"
                                            class="btn btn-danger d-flex align-items-center justify-content-center flex-fill px-3 py-2"
                                            style="min-width: 180px;"
                                            {{ $targetAgent && $targetAgent->nomor_telepon ? '' : 'onclick="return false;"' }}>
                                            <i class="fa fa-phone-alt me-2"></i>Hubungi Agent
                                        </a>
                                    @else
                                        <!-- Untuk Owner dan Stoker: tombol Hapus Listing -->
                                        @if(in_array($role, ['Owner', 'Stoker']))
                                        <a href="javascript:void(0)"
                                        onclick="submitForm('{{ $property->id_listing }}')"
                                        class="btn btn-danger d-flex align-items-center justify-content-center flex-fill px-3 py-2"
                                        style="min-width: 180px;"
                                        id="delete-button-{{ $property->id_listing }}">
                                         <i class="fa fa-trash-alt me-2"></i>Hapus Listing
                                     </a>

                                     <form id="delete-form-{{ $property->id_listing }}" action="{{ route('listing.delete', $property->id_listing) }}" method="POST" style="display: none;">
                                         @csrf
                                         @method('POST')
                                     </form>

                                     <script>
                                         function submitForm(id) {
                                             // Ganti tombol menjadi loading
                                             let button = document.getElementById('delete-button-' + id);
                                             button.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Loading...';
                                             button.disabled = true;  // Disable button while loading

                                             // Submit form untuk mengupdate status
                                             document.getElementById('delete-form-' + id).submit();
                                         }
                                     </script>

                                        @else
                                            <!-- Untuk selain Owner dan Stoker: tombol Tanyakan Stok -->
                                            <a href="https://chat.whatsapp.com/BRKrMZk2wWJ9rEGV7Oy06V"
                                                onclick="copyTanyakanStok('{{ $property->id_listing }}', `{{ $property->lokasi }}`, `{{ \Carbon\Carbon::parse($property->batas_akhir_penawaran)->translatedFormat('d F Y') }}`, `{{ $propertyUrl }}`)"
                                                target="_blank"
                                                class="btn btn-danger d-flex align-items-center justify-content-center flex-fill px-3 py-2"
                                                style="min-width: 180px;">
                                                <i class="fa fa-question-circle me-2"></i>Tanyakan Stok
                                            </a>
                                        @endif

                                        <script>
                                            function copyTanyakanStok(id, lokasi, tanggalLelang, urlSumber) {
                                                const lines = [
                                                    `📍 *${id}*: ${lokasi}`,
                                                    `📅 *Tanggal Lelang*: ${tanggalLelang}`,
                                                    `📢 Mohon update stok please`,
                                                    `🔗 Detail: ${urlSumber}`
                                                ];

                                                const teks = lines.join("\n");

                                                navigator.clipboard.writeText(teks)
                                                    .then(() => {
                                                        alert("✅ Pesan berhasil disalin. Tinggal paste di grup WhatsApp.");
                                                    })
                                                    .catch(err => {
                                                        console.error(err);
                                                        alert("❌ Gagal menyalin pesan. Browser mungkin memblokir akses clipboard.");
                                                    });
                                            }
                                        </script>
                                    @endif


                                        <!-- Tombol Ikuti / Login atau Tombol Download Gambar -->
                                        @if ($userId && in_array($userRole, ['User', 'Agent', 'Register', 'Pengosongan', 'Owner', 'Stoker', 'Principal']))
                                            @if ($userRole === 'User')
                                                <a href="{{ route('property.interest.show', $property->id_listing) }}"
                                                    class="btn btn-dark d-flex align-items-center justify-content-center flex-fill px-3 py-2"
                                                    style="min-width: 180px;">
                                                    <i class="fa fa-calendar-alt me-2"></i>Ikuti Lelang Ini
                                                </a>
                                            @else
                                        <!-- Tombol untuk mendownload gambar -->
                                            @if ($property->gambar)
                                                <a href="javascript:void(0)" onclick="downloadImages('{{ $property->gambar }}')"
                                                class="btn btn-dark-blue d-flex align-items-center justify-content-center flex-fill px-3 py-2"
                                                style="min-width: 180px;">
                                                <i class="fa fa-download me-2"></i>Download Gambar
                                                </a>
                                            @endif

                                        @endif
                                        @else
                                        <a href="{{ url('login') }}"
                                            class="btn btn-dark-blue d-flex align-items-center justify-content-center flex-fill px-3 py-2"
                                            style="min-width: 180px;">
                                            <i class="fa fa-lock me-2"></i>Login untuk Ikut Lelang
                                        </a>
                                        @endif

                                        <script>
                                            function downloadImages(gambarUrls) {
                                            // Pisahkan URL gambar yang dipisahkan oleh koma
                                            const urls = gambarUrls.split(',');

                                            // Unduh setiap gambar menggunakan fetch secara paralel
                                            const fetchPromises = urls.map(url => {
                                                return fetch(url.trim())
                                                    .then(response => response.blob()) // Mengambil gambar sebagai blob
                                                    .then(blob => {
                                                        // Menyimpan gambar sebagai file di sistem pengguna
                                                        const a = document.createElement('a');
                                                        a.href = URL.createObjectURL(blob); // Menggunakan URL.createObjectURL untuk menyimpan gambar
                                                        a.download = ''; // Menandakan bahwa ini adalah file yang dapat diunduh
                                                        document.body.appendChild(a);
                                                        a.click(); // Memulai pengunduhan
                                                        document.body.removeChild(a);
                                                    });
                                            });

                                            // Tunggu sampai semua gambar diunduh
                                            Promise.all(fetchPromises)
                                                .then(() => {
                                                    console.log("Semua gambar telah diunduh.");
                                                })
                                                .catch(err => {
                                                    console.error("Error saat mendownload gambar:", err);
                                                });
                                        }

                                        </script>

                                        <!-- Tombol Share / Copy Link (Desktop) -->
                                        <a href="javascript:void(0);"
                                        id="shareBtn"
                                        class="btn btn-outline-secondary d-none d-md-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 50px; height: 50px;"
                                        title="Bagikan">
                                        <i class="fa fa-share-alt"></i>
                                        </a>
                                    </div>
                                    <!-- Menu Share (popover sederhana) -->
                                    <div id="shareMenu" class="share-popover" style="display:none; position:absolute; z-index:9999; background:#fff; border:1px solid #ddd; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.12); padding:8px; width:220px;">
                                        <!-- Instagram -->
                                        <button class="share-item" data-action="instagram" style="display:flex;align-items:center;width:100%;border:0;background:transparent;padding:10px;border-radius:10px;cursor:pointer;">
                                            <img src="{{ asset('img/instagram.png') }}" alt="Instagram" style="width:20px;height:20px;margin-right:10px;">
                                            Instagram
                                        </button>

                                        <!-- TikTok -->
                                        <button class="share-item" data-action="tiktok" style="display:flex;align-items:center;width:100%;border:0;background:transparent;padding:10px;border-radius:10px;cursor:pointer;">
                                            <img src="{{ asset('img/tiktok.png') }}" alt="TikTok" style="width:20px;height:20px;margin-right:10px;">
                                            TikTok
                                        </button>

                                        <!-- WhatsApp -->
                                        <button class="share-item" data-action="whatsapp" style="display:flex;align-items:center;width:100%;border:0;background:transparent;padding:10px;border-radius:10px;cursor:pointer;">
                                            <img src="{{ asset('img/wa.png') }}" alt="WhatsApp" style="width:20px;height:20px;margin-right:10px;">
                                            WhatsApp
                                        </button>

                                        <button class="share-item" data-action="copy" style="display:flex;align-items:center;width:100%;border:0;background:transparent;padding:10px;border-radius:10px;cursor:pointer;">
                                            <i class="fa fa-link" style="margin-right:10px;"></i> Copy link
                                        </button>
                                    </div>
                                    <!-- Share + Edit (Mobile Only) -->
                                    <div class="d-flex d-md-none gap-2 mt-3">
                                        <!-- Share dengan teks -->
                                        <a href="javascript:void(0);"
                                        id="shareBtnMobile"
                                        class="btn btn-outline-secondary d-flex align-items-center justify-content-center px-3 py-2"
                                        style="min-width: 180px; height: 50px;"
                                        title="Bagikan">
                                        <i class="fa fa-share-alt me-2"></i> Bagikan
                                     </a>


                                        <!-- Edit (Hanya Pemilik) -->
                                        @if (Session::has('id_account'))
                                            @php
                                                $loggedInId = Session::get('id_account');
                                                $loggedInAgentId = \App\Models\Agent::where('id_account', $loggedInId)->value('id_agent');
                                            @endphp
                                            @if ($property->id_agent === $loggedInAgentId)
                                                <a href="{{ route('editproperty', $property->id_listing) }}"
                                                   class="btn btn-warning text-black d-flex align-items-center justify-content-center flex-fill"
                                                   title="Edit Properti">
                                                   <i class="fa fa-edit me-2"></i>Edit
                                                </a>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Edit (Desktop) -->
                                    @if (Session::has('id_account'))
                                        @php
                                            $loggedInId = Session::get('id_account');
                                            $loggedInAgentId = \App\Models\Agent::where('id_account', $loggedInId)->value('id_agent');
                                        @endphp
                                        @if ($property->id_agent === $loggedInAgentId)
                                            <div class="mt-3 d-none d-md-block">
                                                <a href="{{ route('editproperty', $property->id_listing) }}"
                                                   class="btn btn-warning text-black d-flex align-items-center justify-content-center px-3 py-2 w-100 w-md-auto"
                                                   style="min-width: 180px;">
                                                   <i class="fa fa-edit me-2"></i>Edit Properti
                                                </a>
                                            </div>
                                        @endif
                                    @endif
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
                            <div class="row mb-3">
                                <div class="row">
                                    <section id="features" class="features section">
                                        <!-- Section Title -->
                                        <div class="py-2" data-aos="fade-up">
                                        </div><!-- End Section Title -->
                                        <div class="container-fluid px-2 px-md-3" data-aos="fade-up" data-aos-delay="100">
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
                                                                            <th scope="row" class="bg-light">Uang Jaminan</th>
                                                                            <td>Rp {{ number_format($property->uang_jaminan, 0, ',', '.') }}</td>
                                                                        </tr>
                                                                        {{-- <tr>
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
                                                                        </tr> --}}
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <!-- MOBILE CARD VERSION -->
                                                            <div class="d-block d-md-none mt-3"> <!-- Ini bikin lebarnya nyesuaiin sama konten atas -->
                                                                <div class="row g-3">
                                                                    <div class="col-12">
                                                                    <div class="p-3 rounded-3 shadow-sm border bg-white w-100">
                                                                        <div class="text-uppercase small text-muted mb-1">Harga Properti</div>
                                                                        <div class="text-secondary">
                                                                            <span class="fw-semibold text-primary">Rp {{ number_format($property->harga, 0, ',', '.') }}</span>
                                                                        </div>

                                                                    </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="p-3 rounded-3 shadow-sm border bg-white w-100">
                                                                            <div class="text-uppercase small text-muted mb-1">Uang Jaminan</div>
                                                                            <div class="text-secondary">
                                                                            <span class="fw-semibold text-primary">Rp {{ number_format($property->uang_jaminan, 0, ',', '.') }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    {{-- <div class="col-12">
                                                                    <div class="p-3 rounded-3 shadow-sm border bg-white w-100">
                                                                        <div class="text-uppercase small text-muted mb-1">Biaya Pengosongan</div>
                                                                        <div class="text-secondary fw-semibold text-primary">Rp {{ number_format($biayaPengosongan, 0, ',', '.') }}</div>
                                                                    </div>
                                                                    </div> --}}
                                                                </div>
                                                            </div>
                                                            <!-- CATATAN -->
                                                            <div class="d-flex justify-content-center">
                                                                <div class="alert alert-warning mt-3 rounded-3 shadow-sm border-start border-4 border-warning bg-warning-subtle w-100" style="max-width: 720px;">
                                                                    <i class="fa fa-info-circle me-2 text-warning"></i>
                                                                    <strong>Catatan:</strong> <br class="d-md-none">
                                                                    <span class="text-dark">
                                                                        Belum termasuk pajak, biaya pengosongan, dan biaya balik nama.
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Spesifikasi Properti -->
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
                                                                            <td>{{ $sertifikatDisplay }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="row" class="bg-light">Lokasi</th>
                                                                            <td>{{ $property->kelurahan }}, {{ $property->kota }}, {{ $property->provinsi }}</td>
                                                                        </tr>
                                                                        @if ($batasPenawaranDisplay)
                                                                            <tr>
                                                                                <th scope="row" class="bg-light">Batas Penawaran</th>
                                                                                <td>{{ $batasPenawaranDisplay }}</td>
                                                                            </tr>
                                                                        @endif
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
                                                                    {{-- <div class="col-md-12 mb-4">
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
                                                                    </div> --}}
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
                href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>

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
                <style>
                    .mySwiper {
                    height: 70%; /* ini buat batasi tinggi keseluruhan swiper */
                }
                /* Swiper slide size */
                .swiper-slide {
                    width: auto; /* atau biarkan tanpa width */
                    flex-shrink: 0; /* biar tidak menyusut */
                }

                /* Tombol navigasi Swiper */
                .swiper-button-next,
                .swiper-button-prev {
                    color: #ff6600;
                    top: 50%; /* Lebih pas di tengah secara vertikal */
                    transform: translateY(-50%); /* Tengah beneran */
                }

                /* Kartu properti */
                .property-item {
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                    transition: transform 0.2s ease;
                    background: #fff; /* Tambahkan agar tidak transparan */
                }

                /* Hover effect */
                .property-item:hover {
                    transform: translateY(-4px);
                }

                /* Judul atau deskripsi pendek, dibatasi 2 baris */
                .property-item a.d-block.h6 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    min-height: 3em; /* Tambahkan agar tinggi konsisten walau konten sedikit */
                }

                /* Tambahan opsional: untuk gambar agar rapi */
                .property-item img {
                    width: 100%;
                    height: 200px; /* Atur sesuai desain */
                    object-fit: cover;
                    border-top-left-radius: 8px;
                    border-top-right-radius: 8px;
                }

                /* share */
                #shareMenu .share-item {
                transition: background-color 0.2s ease, transform 0.1s ease;
                }

                #shareMenu .share-item:hover {
                background-color: #f5f7fa !important;
                transform: translateX(2px);
                }

                #shareMenu .share-item[data-action="instagram"]:hover {
                background-color: #fddde6 !important; /* pink IG */
                }

                #shareMenu .share-item[data-action="tiktok"]:hover {
                background-color: #d9f5f4 !important; /* hijau muda TikTok */
                }

                #shareMenu .share-item[data-action="whatsapp"]:hover {
                background-color: #e6f7ec !important; /* hijau muda WA */
                }

                </style>
        </div>
        <script>
document.addEventListener('DOMContentLoaded', function () {
  // === konfigurasi share (boleh kamu ubah) ===
  const shareUrl  = "{{ url()->current() }}";
  const shareText = "Cek properti ini — cocok banget!";

  // === fungsi copy seperti yang kamu minta ===
  async function copyPropertyLink() {
    try {
      // navigator.clipboard butuh HTTPS/localhost
      await navigator.clipboard.writeText(shareUrl);
      alert('Link berhasil disalin!');
    } catch (err) {
      // fallback untuk http / browser lama
      try {
        const ta = document.createElement('textarea');
        ta.value = shareUrl;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        alert('Link berhasil disalin!');
      } catch (err2) {
        console.error('Gagal menyalin link:', err2);
        alert('Gagal menyalin link. Coba lagi.');
      }
    }
  }

  // === ambil trigger dan menu ===
  const triggers = [
    document.getElementById('shareBtn'),       // desktop (jika ada)
    document.getElementById('shareBtnMobile')  // mobile (jika ada)
  ].filter(Boolean);

  const menu = document.getElementById('shareMenu');
  if (!triggers.length || !menu) return;

  // pindahkan menu ke body biar tidak ketahan overflow parent
  document.body.appendChild(menu);
  menu.style.position = 'fixed';
  menu.style.display  = 'none';
  menu.style.zIndex   = 99999;

  function positionMenu(anchor) {
    const r = anchor.getBoundingClientRect();
    const menuW = menu.offsetWidth || 220;
    const top   = r.top - menu.offsetHeight - 8;
    const left  = r.left + (r.width/2) - (menuW/2);
    const finalTop  = top < 8 ? (r.bottom + 8) : top;
    const finalLeft = Math.max(8, Math.min(left, window.innerWidth - menuW - 8));
    menu.style.top  = finalTop + 'px';
    menu.style.left = finalLeft + 'px';
  }

  function openMenu(anchor) { positionMenu(anchor); menu.style.display = 'block'; }
  function closeMenu()      { menu.style.display = 'none'; }

  // klik tombol (desktop/mobile) membuka/menutup menu
  triggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      if (menu.style.display === 'block') closeMenu();
      else openMenu(trigger);
    });
  });

  // klik di luar menutup menu
  document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && !triggers.some(t => t.contains(e.target))) closeMenu();
  });

  // reposisi saat viewport berubah
  window.addEventListener('resize', () => { if (menu.style.display === 'block') positionMenu(triggers[0]); });
  window.addEventListener('scroll',  () => { if (menu.style.display === 'block') positionMenu(triggers[0]); });

  // tampilkan tombol "Share… (native)" jika tersedia
  if (navigator.share) {
    const nativeBtn = menu.querySelector('[data-action="native"]');
    if (nativeBtn) nativeBtn.style.display = 'flex';
  }

  // === aksi tiap item di menu ===
  menu.addEventListener('click', async (e) => {
    const item = e.target.closest('.share-item');
    if (!item) return;
    const action = item.getAttribute('data-action');

    switch (action) {
      case 'copy':
        await copyPropertyLink();        // <<— di sini dipanggil
        break;

      case 'whatsapp':
        window.open(`https://wa.me/?text=${encodeURIComponent(shareText + "\n" + shareUrl)}`, '_blank', 'noopener,noreferrer');
        break;

      case 'instagram':
        // sementara: buka app (best-effort) lalu fallback web
        openAppOrWeb('instagram://camera', 'https://www.instagram.com/');
        // opsional: copy caption
        try { await navigator.clipboard.writeText(`${shareText}\n${shareUrl}`); } catch (_) {}
        break;

      case 'tiktok':
        openAppOrWeb('snssdk1128://', 'https://www.tiktok.com/upload?lang=id-ID');
        try { await navigator.clipboard.writeText(`${shareText}\n${shareUrl}`); } catch (_) {}
        break;

      case 'native':
        try { await navigator.share({ title: document.title, text: shareText, url: shareUrl }); } catch (_) {}
        break;
    }

    closeMenu();
  });

  // coba buka app; kalau gagal, fallback ke web
  function openAppOrWeb(appUrl, webUrl) {
    const now = Date.now();
    const a = document.createElement('a');
    a.href = appUrl;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
      if (Date.now() - now < 1200) {
        window.open(webUrl, '_blank', 'noopener,noreferrer');
      }
      a.remove();
    }, 800);
  }
});

            // fungsi copy seperti yang kamu inginkan
            function copyPropertyLink() {
            const link = "{{ url()->current() }}";
            return navigator.clipboard.writeText(link).then(() => {
                alert('Link berhasil disalin!');
            }).catch(err => {
                console.error('Gagal menyalin link:', err);
                alert("Gagal menyalin link. Coba lagi.");
            });
            }
            </script>

@include('template.footer')

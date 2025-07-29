@include('template.header')

<section class="container my-5">
    <div class="row">
        <!-- Gambar Properti -->
        <div class="col-md-6">
            <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach(explode(',', $property->gambar) as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ asset($image) }}" class="d-block w-100 rounded" alt="Property Image">
                        </div>
                    @endforeach
                </div>
                @if(count(explode(',', $property->gambar)) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Detail Lot -->
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white fw-semibold fs-5">
                    <i class="fa fa-gavel me-2"></i> Status Lelang
                </div>
                <div class="card-body">
                    {{-- Status Lelang --}}
                    <div class="row mb-3 align-items-center">
                        <div class="col-4 text-muted">Status Lelang</div>
                        <div class="col-auto px-1">:</div>
                        <div class="col">
                            <span class="badge p-2
                                @if ($status == 'menang' || $status == 'closing') bg-info
                                @elseif ($status == 'tunggu_verifikasi') bg-warning text-dark
                                @elseif ($status == 'kalah') bg-secondary
                                @else bg-secondary
                                @endif">
                                {{
                                    strtoupper(
                                        $status == 'kalah' ? 'SELESAI (Kalah)' :
                                        ($status == 'closing' ? 'MENANG' :
                                        ucwords(str_replace('_', ' ', $status)))
                                    )
                                }}
                            </span>
                        </div>
                        {{-- Tombol Beri Rating jika Status Selesai --}}
                        @if ($status === 'Selesai' && (is_null($transactionReview?->rating) || is_null($transactionReview?->comment)))
                            <div class="col-auto">
                                <button class="btn btn-warning btn-sm" onclick="bukaModalRating('{{ $interest->id_account ?? session('id_account') }}', '{{ $interest->id_listing ?? $property->id_listing }}')">
                                    Beri Rating
                                </button>
                            </div>
                        @endif


                    </div>

                    {{-- Catatan --}}
                    <div class="row align-items-center">
                        <div class="col-4 text-muted">Catatan</div>
                        <div class="col-auto px-1">:</div>
                        <div class="col">{{ $catatan ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Font Awesome for beautiful stars -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<!-- Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 p-3" style="background-color: #fdfdfd;">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-dark">📝 Feedback Pengosongan</h5>
      </div>
      <div class="modal-body">

        <label class="form-label fw-semibold">Rating:</label>
        <div class="mb-4 d-flex gap-2 justify-content-center">
          @for ($i = 1; $i <= 5; $i++)
            <i class="fa-regular fa-star fa-2x text-secondary star-hover" id="star-{{ $i }}"
              onclick="pilihRating({{ $i }})" style="cursor: pointer; transition: transform 0.2s ease;"></i>
          @endfor
        </div>

        <label for="deskripsiFeedback" class="form-label fw-semibold">Deskripsi:</label>
        <textarea id="deskripsiFeedback" class="form-control rounded-3" rows="4" placeholder="Tulis pengalaman kamu di sini..."></textarea>

        <input type="hidden" id="rating_id_account">
        <input type="hidden" id="rating_id_listing">
        <input type="hidden" id="rating_nilai">

      </div>
      <div class="modal-footer border-0 d-flex justify-content-between">
        <button class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
        <button class="btn btn-warning text-white rounded-pill px-4" onclick="kirimRating()">Kirim</button>
      </div>
    </div>
  </div>
</div>

<style>
  .star-hover:hover {
    transform: scale(1.2);
    color: #ffc107 !important;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('nav.navbar');
        const ratingModal = document.getElementById('ratingModal');
        if (ratingModal) {
            ratingModal.addEventListener('show.bs.modal', function () {
                navbar.style.display = 'none';
            });
            ratingModal.addEventListener('hidden.bs.modal', function () {
                navbar.style.display = '';
            });
        }
    });
    function bukaModalRating(idAccount, idListing) {
    document.getElementById('rating_id_account').value = idAccount;
    document.getElementById('rating_id_listing').value = idListing;
    document.getElementById('rating_nilai').value = 0;
    document.getElementById('deskripsiFeedback').value = '';
    resetBintang();
    new bootstrap.Modal(document.getElementById('ratingModal')).show();
}

function pilihRating(nilai) {
    document.getElementById('rating_nilai').value = nilai;
    for (let i = 1; i <= 5; i++) {
        const star = document.getElementById('star-' + i);
        star.classList.remove('fa-solid');
        star.classList.remove('text-warning');
        star.classList.add('fa-regular');
    }
    for (let i = 1; i <= nilai; i++) {
        const star = document.getElementById('star-' + i);
        star.classList.remove('fa-regular');
        star.classList.add('fa-solid', 'text-warning');
    }
}

function resetBintang() {
    for (let i = 1; i <= 5; i++) {
        const star = document.getElementById('star-' + i);
        star.classList.remove('fa-solid', 'text-warning');
        star.classList.add('fa-regular', 'text-secondary');
    }
}

function kirimRating() {
    const kirimButton = document.querySelector('#ratingModal .modal-footer button.btn-warning');
    const originalButtonText = kirimButton.innerHTML;

    // Ganti tombol jadi loading
    kirimButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...`;
    kirimButton.disabled = true;

    fetch("/pengosongan/rating", {
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_account: document.getElementById('rating_id_account').value,
            id_listing: document.getElementById('rating_id_listing').value,
            rating: document.getElementById('rating_nilai').value,
            deskripsi: document.getElementById('deskripsiFeedback').value,
        })
    }).then(res => res.json()).then(data => {
        if (data.success) {
            // Sukses
            setTimeout(() => {
                kirimButton.innerHTML = originalButtonText;
                kirimButton.disabled = false;

                bootstrap.Modal.getInstance(document.getElementById('ratingModal')).hide();

                // Setelah modal ketutup, munculin alert sukses
                setTimeout(() => {
                    alert('✅ Feedback telah diterima, terima kasih!');
                }, 500);
            }, 1000); // kasih jeda biar loading keliatan sedikit
        }
    }).catch(error => {
        // Kalau error
        console.error('Error:', error);
        kirimButton.innerHTML = originalButtonText;
        kirimButton.disabled = false;
        alert('❌ Gagal mengirim feedback. Coba lagi!');
    });
}


    </script>
            {{-- SECTION 2: Detail Lot --}}
            {{-- SECTION 2: Detail Lot --}}
<div class="card shadow-sm rounded-4">
    <div class="card-header bg-primary text-white fw-semibold fs-5">
        <i class="fa fa-file-alt me-2"></i> Detail Lot Lelang
    </div>
    <div class="card-body">
        <dl class="row mb-4">

            <dt class="col-sm-4 text-muted fw-semibold">Lokasi</dt>
            <dd class="col-sm-8">1 bidang tanah dengan total luas <strong>{{ $property->luas_tanah }} m²</strong> berikut bangunan di <strong>{{ $property->kota }}</strong></dd>

            <dt class="col-sm-4 text-muted fw-semibold">Cara Penawaran</dt>
            <dd class="col-sm-8">Tanpa Kehadiran, Open Bidding</dd>

            <dt class="col-sm-4 text-muted fw-semibold">Harga Property</dt>
            <dd class="col-sm-8 text-primary fw-bold fs-5">Rp {{ number_format($property->harga, 0, ',', '.') }}</dd>
        </dl>

        <hr>

        <p class="fst-italic mb-3">Harga properti ini belum termasuk biaya tambahan berikut:</p>

        <dl class="row">
            <dt class="col-sm-4 text-muted fw-semibold">Biaya Dokumen</dt>
            <dd class="col-sm-8">Rp {{ number_format($property->harga * 0.085, 0, ',', '.') }} <span class="badge bg-info text-dark">8.5%</span></dd>

            @php
                $biaya_pengosongan = match(true) {
                    $property->harga < 500_000_000 => 100_000_000,
                    $property->harga <= 1_500_000_000 => 125_000_000,
                    $property->harga <= 2_500_000_000 => 175_000_000,
                    $property->harga <= 10_000_000_000 => 225_000_000,
                    $property->harga <= 100_000_000_000 => 375_000_000,
                    default => 0,
                };
            @endphp

            <dt class="col-sm-4 text-muted fw-semibold">Biaya Pengosongan</dt>
            <dd class="col-sm-8 text-danger fw-semibold">Rp {{ number_format($biaya_pengosongan, 0, ',', '.') }}</dd>
        </dl>
    </div>
</div>

        </div>

        @php
    $status = $status ?? null;

    $stepStatus = [
        1 => ['FollowUp', 'BuyerMeeting'],
        2 => ['Closing', 'Kuitansi', 'Kode Billing', 'Kutipan Risalah Lelang', 'Akte Grosse', 'Balik Nama'],
        3 => ['Eksekusi Pengosongan'],
        4 => ['Selesai'],
    ];

    $currentStep = 0;
    foreach ($stepStatus as $step => $statuses) {
        if (in_array($status, $statuses)) {
            $currentStep = $step;
            break;
        }
    }

    function getStepClass($step, $currentStep) {
        if ($step < $currentStep) {
            return 'bg-success text-white';
        } elseif ($step === $currentStep) {
            return 'bg-danger text-white'; // tanpa scale
        } else {
            return 'bg-light';
        }
    }

    function getTextClass($step, $currentStep) {
        return $step <= $currentStep ? 'text-white' : 'text-primary';
    }

    function getMutedClass($step, $currentStep) {
        return $step <= $currentStep ? 'text-light' : 'text-muted';
    }

    function showCurrentNote($step, $currentStep) {
        return $step === $currentStep ? '<p class="small mt-2 fst-italic text-white">*sedang dalam tahap ini</p>' : '';
    }
@endphp

<div class="container-xxl py-5" data-aos="fade-up">
    <section id="progress" class="section">
        <div class="container">
            <h4 class="mb-5 fw-bold text-center text-primary">Progress Kerja</h4>
            <div class="row gy-5 justify-content-center">

                @php
                $steps = [
                    1 => [
                        'title' => 'Pendaftaran Lelang',
                        'duration' => '1 - 3 Hari Kerja',
                        'tasks' => ['Verifikasi dokumen secara menyeluruh untuk memastikan kelengkapan persyaratan.', 'Pengajuan formulir resmi untuk mengikuti proses lelang.', 'Pembayaran uang jaminan sebagai bagian dari komitmen awal.'],
                        'desc' => 'Tahap awal yang sangat penting, memastikan semua dokumen dan pembayaran telah valid sebelum memasuki proses lelang yang sesungguhnya.',
                        'icon' => 'bi-file-check-fill',
                    ],
                    2 => [
                        'title' => 'Pengurusan Dokumen',
                        'duration' => '3 - 5 Minggu',
                        'tasks' => [
                                        'Proses balik nama sertifikat agar hak kepemilikan resmi dialihkan.',
                                        'Penerbitan Kutipan Risalah Lelang (KRL) sebagai bukti transaksi sah.',
                                        'Pengurusan pajak PPh dan BPHTB sesuai ketentuan hukum yang berlaku.',
                                    ],
                        'desc' => 'Tahap administratif yang memastikan legalitas dan keabsahan transaksi serta kepemilikan aset terjamin.',
                        'icon' => 'bi-file-earmark-text-fill',
                    ],
                    3 => [
                        'title' => 'Pengosongan Aset',
                        'duration' => '3 - 6 Bulan',
                        'tasks' => ['Proses mediasi apabila terdapat pihak yang masih menempati properti.', 'Koordinasi pengosongan secara efektif dan manusiawi.', 'Eksekusi aset dengan prosedur hukum dan etika yang tepat.'],
                        'desc' => 'Tahap yang memerlukan koordinasi intensif untuk memastikan aset siap diserahkan tanpa kendala penghuni lama.',
                        'icon' => 'bi-house-door-fill',
                    ],
                    4 => [
                        'title' => 'Serah Terima Aset',
                        'duration' => 'Setelah Pengosongan',
                        'tasks' => ['Serah terima kunci sebagai simbol pengalihan kepemilikan.', 'Pemeriksaan akhir kondisi aset untuk memastikan sesuai standar.', 'Penyelesaian administrasi akhir guna menutup seluruh proses lelang.'],
                        'desc' => 'Tahap final yang menandai berakhirnya proses dengan serah terima aset secara resmi dan administratif.',
                        'icon' => 'bi-check2-circle',
                    ],
                ];
                @endphp

                @foreach($steps as $stepNumber => $step)
                    <div class="col-xl-3 col-lg-6" data-aos="fade-up" data-aos-delay="{{ 100 * $stepNumber }}">
                        <div class="card h-100 border-0 shadow-lg rounded-4 p-4
                            {{ getStepClass($stepNumber, $currentStep) }}
                            {{ $currentStep === $stepNumber ? 'scale-110 border-primary' : '' }}">

                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 48px; height: 48px;">
                                    <i class="bi {{ $step['icon'] }} fs-3"></i>
                                </div>
                                <h5 class="fw-semibold mb-0 {{ getTextClass($stepNumber, $currentStep) }}">{{ $stepNumber }}. {{ $step['title'] }}</h5>
                            </div>

                            <p class="text-muted {{ getMutedClass($stepNumber, $currentStep) }} mb-3"><strong>Durasi:</strong> {{ $step['duration'] }}</p>

                            <ul class="list-unstyled {{ $currentStep >= $stepNumber ? 'text-light' : 'text-muted' }} mb-3 ps-3">
                                @foreach ($step['tasks'] as $task)
                                    <li class="mb-1">• {{ $task }}</li>
                                @endforeach
                            </ul>

                            <p class="fst-italic {{ $currentStep >= $stepNumber ? 'text-white' : 'text-muted' }}">
                                {{ $step['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </section>
</div>
</div>
</section>

@include('template.footer')

@include('template.header')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<!-- Cropper CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>

<!-- Cropper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<main class="mt-4 mb-5">
  <div class="container">
    <div class="row g-4">

        <div class="col-lg-6">

            {{-- === ROLE: USER === --}}
            @if(session('role') === 'User' || session('role') === 'Pending')

              {{-- === KTP === --}}
              <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                  <h6 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Data KTP</h6>
                  <button class="btn btn-sm btn-secondary-theme" data-bs-toggle="modal" data-bs-target="#modalKTP">
                    <i class="bi bi-pencil-square me-1"></i>{{ isset($informasi_klien->gambar_ktp) ? 'Edit' : 'Tambah' }}
                  </button>
                </div>
                <div class="card-body">
                  @if(!empty($informasi_klien->gambar_ktp))
                    <div class="d-flex align-items-center">
                        <img src="https://drive.google.com/thumbnail?id={{ $informasi_klien->gambar_ktp }}" alt="KTP"
                        class="img-thumbnail me-4" style="width: 180px; height: auto; object-fit: contain;" />
                      <div>
                        <p class="mb-1"><strong>NIK:</strong> {{ $informasi_klien->nik }}</p>
                        <p class="mb-1"><strong>Jenis Kelamin:</strong> {{ ucfirst($informasi_klien->jenis_kelamin) }}</p>
                        <p class="mb-1"><strong>Pekerjaan:</strong> {{ $informasi_klien->pekerjaan }}</p>
                        <p class="mb-0"><strong>Berlaku Hingga:</strong> {{ $informasi_klien->berlaku_hingga }}</p>
                      </div>
                    </div>
                  @else
                    <p class="text-muted fst-italic">Belum ada data KTP.</p>
                  @endif
                </div>
              </div>

              {{-- === NPWP === --}}
              <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                  <h6 class="mb-0"><i class="bi bi-credit-card-2-back me-2"></i>Data NPWP</h6>
                  <button class="btn btn-sm btn-secondary-theme" data-bs-toggle="modal" data-bs-target="#modalNPWP">
                    <i class="bi bi-pencil-square me-1"></i>{{ isset($informasi_klien->gambar_npwp) ? 'Edit' : 'Tambah' }}
                  </button>
                </div>
                <div class="card-body">
                  @if(!empty($informasi_klien->gambar_npwp))
                    <div class="d-flex align-items-center">
                        <img src="https://drive.google.com/thumbnail?id={{ $informasi_klien->gambar_npwp }}" alt="NPWP"
                        class="img-thumbnail me-4" style="width: 180px; height: auto; object-fit: contain;" />

                      <div>
                        <p class="mb-1"><strong>Nomor NPWP:</strong></p>
                        <p class="fs-5">{{ $informasi_klien->nomor_npwp }}</p>
                      </div>
                    </div>
                  @else
                    <p class="text-muted fst-italic">Belum ada data NPWP.</p>
                  @endif
                </div>
              </div>

              {{-- === REKENING === --}}
              <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                  <h6 class="mb-0"><i class="bi bi-bank me-2"></i>Data Rekening</h6>
                  <button class="btn btn-sm btn-secondary-theme" data-bs-toggle="modal" data-bs-target="#modalRekening">
                    <i class="bi bi-pencil-square me-1"></i>{{ !empty($informasi_klien->nama_bank) ? 'Edit' : 'Tambah' }}
                  </button>
                </div>
                <div class="card-body">
                  @if(!empty($informasi_klien->nama_bank))
                    <div class="d-flex align-items-center gap-4">
                      <div class="rekening-card d-flex flex-column justify-content-between p-3 rounded shadow-sm text-white"
                           style="width: 180px; height: 110px; background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);">
                        <div class="fs-5 fw-bold">{{ strtoupper($informasi_klien->nama_bank) }}</div>
                        <div class="small opacity-75">{{ $informasi_klien->atas_nama }}</div>
                      </div>
                      <div>
                        <p class="mb-1"><strong>Nama Bank:</strong> {{ $informasi_klien->nama_bank }}</p>
                        <p class="mb-1"><strong>Atas Nama:</strong> {{ $informasi_klien->atas_nama }}</p>
                        <p class="mb-0"><strong>Nomor Rekening:</strong> {{ $informasi_klien->nomor_rekening }}</p>
                      </div>
                    </div>
                  @else
                    <p class="text-muted fst-italic">Belum ada data rekening.</p>
                  @endif
                </div>
              </div>

            @endif

@if(in_array(session('role'), ['Agent', 'Register', 'Pengosongan']))
{{-- === KTP === --}}
<div class="row g-3 mb-4">
    {{-- === KTP === --}}
    <div class="col-md-6">
      <div class="card shadow-sm border-0 rounded-4 h-100">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Data KTP</h6>
          <button class="btn btn-sm btn-secondary-theme" data-bs-toggle="modal" data-bs-target="#modalKTPView">
            <i class="bi bi-pencil-square me-1"></i>Edit
          </button>
        </div>
        <div class="card-body text-center">
            @if (!empty($informasi_klien) && !empty($informasi_klien->gambar_ktp))
  <img src="https://drive.google.com/thumbnail?id={{ $informasi_klien->gambar_ktp }}" alt="KTP"
       class="img-thumbnail me-4" style="max-height: 250px; object-fit: contain;">
@else
            <p class="text-muted fst-italic">Belum ada data KTP.</p>
          @endif
        </div>
      </div>
    </div>

    {{-- === NPWP === --}}
    <div class="col-md-6">
      <div class="card shadow-sm border-0 rounded-4 h-100">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bi bi-credit-card-2-back me-2"></i>Data NPWP</h6>
          <button class="btn btn-sm btn-secondary-theme" data-bs-toggle="modal" data-bs-target="#modalNPWPView">
            <i class="bi bi-pencil-square me-1"></i>Edit
          </button>
        </div>
        <div class="card-body text-center">
            @if (!empty($informasi_klien) && !empty($informasi_klien->gambar_npwp))
            <img src="https://drive.google.com/thumbnail?id={{ $informasi_klien->gambar_npwp }}" alt="NPWP"
         class="img-thumbnail me-4" style="max-height: 250px; object-fit: contain;">
        @else
            <p class="text-muted fst-italic">Belum ada data NPWP.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
@endif
</div>

{{-- === Modal Edit KTP === --}}
<div id="modalKTPView" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalKTPViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content shadow-lg rounded-4">
        <form
          action="{{ route('ktp.update') }}" {{-- Ganti dengan route update sesuai backend --}}
          method="POST"
          enctype="multipart/form-data"
        >
          @csrf
          <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="modalKTPViewLabel">
              <i class="bi bi-card-image me-2"></i>Edit KTP
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            {{-- Preview Gambar Lama --}}
            @if(!empty($informasi_klien->gambar_ktp))
              <div class="mb-3 text-center">
                <label class="form-label fw-semibold">Gambar KTP Lama:</label>
                <div class="border rounded p-2">
                  <img src="https://drive.google.com/thumbnail?id={{ $informasi_klien->gambar_ktp }}"
                       alt="KTP Lama"
                       class="img-fluid rounded shadow"
                       style="max-height: 300px; object-fit: contain;">
                </div>
              </div>
            @else
              <p class="text-muted fst-italic">Belum ada gambar KTP sebelumnya.</p>
            @endif

            {{-- Upload Gambar Baru --}}
            <div class="mb-3">
              <label for="gambar_ktp" class="form-label fw-semibold">
                <i class="bi bi-upload me-1"></i>Upload Gambar KTP Baru
              </label>
              <input
                type="file"
                class="form-control"
                id="gambar_ktp"
                name="gambar_ktp"
                accept="image/*"
                onchange="previewKTP(event)"
                required
              >
            </div>

            {{-- Preview Cropper --}}
            <div class="text-center mt-3">
              <img
                id="imagePreview"
                class="img-fluid rounded shadow"
                style="max-width: 100%; max-height: 400px; display: none;"
                alt="Preview Gambar KTP"
              >
            </div>

            {{-- Hidden input untuk hasil crop --}}
            <input type="hidden" name="cropped_image" id="cropped_image" value="">
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  {{-- === Modal Edit NPWP === --}}
<div id="modalNPWPView" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalNPWPViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content shadow-lg rounded-4">
        <form
          action="{{ route('agent.updateNPWP') }}" {{-- ✅ Route untuk update NPWP --}}
          method="POST"
          enctype="multipart/form-data"
        >
          @csrf
          <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="modalNPWPViewLabel">
              <i class="bi bi-card-image me-2"></i>Edit NPWP
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            {{-- Preview Gambar Lama --}}
            @if(!empty($informasi_klien->gambar_npwp))
              <div class="mb-3 text-center">
                <label class="form-label fw-semibold">Gambar NPWP Lama:</label>
                <div class="border rounded p-2">
                  <img src="https://drive.google.com/thumbnail?id={{ $informasi_klien->gambar_npwp }}"
                       alt="NPWP Lama"
                       class="img-fluid rounded shadow"
                       style="max-height: 300px; object-fit: contain;">
                </div>
              </div>
            @else
              <p class="text-muted fst-italic">Belum ada gambar NPWP sebelumnya.</p>
            @endif

            {{-- Upload Gambar Baru --}}
            <div class="mb-3">
              <label for="gambar_npwp" class="form-label fw-semibold">
                <i class="bi bi-upload me-1"></i>Upload Gambar NPWP Baru
              </label>
              <input
                type="file"
                class="form-control"
                id="gambar_npwp"
                name="gambar_npwp"
                accept="image/*"
                required
              >
            </div>

            {{-- Preview Cropper --}}
            <div class="text-center mt-3">
              <img
                id="imagePreviewNPWP"
                class="img-fluid rounded shadow"
                style="max-width: 100%; max-height: 400px; display: none;"
                alt="Preview Gambar NPWP"
              >
            </div>

            {{-- Hidden input untuk hasil crop --}}
            <input type="hidden" name="cropped_image_npwp" id="cropped_image_npwp" value="">
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </form>
      </div>
    </div>
  </div>




  <!-- HIDE NAVBAR ON MODAL -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
      const navbar = document.querySelector('nav.navbar');
      const modalKTP = document.getElementById('modalKTPView');
      const modalNPWP = document.getElementById('modalNPWPView');

      if (modalKTP) {
        modalKTP.addEventListener('show.bs.modal', function () {
          navbar.style.display = 'none';
        });
        modalKTP.addEventListener('hidden.bs.modal', function () {
          navbar.style.display = '';
        });
      }

      if (modalNPWP) {
        modalNPWP.addEventListener('show.bs.modal', function () {
          navbar.style.display = 'none';
        });
        modalNPWP.addEventListener('hidden.bs.modal', function () {
          navbar.style.display = '';
        });
      }
    });
    </script>


      <style>
        .rekening-card {
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          box-shadow: 0 4px 8px rgb(0 0 0 / 0.1);
          user-select: none;
        }
      </style>

<!-- Modal Ganti Foto Profil -->
<div id="modalGantiFoto" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalGantiFotoLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">
        <form action="{{ route('agent.updateProfilePicture') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          @csrf

          <div class="modal-header bg-secondary text-white">
            <h6 class="mb-0 text-white"><i class="bi bi-image me-2 text-white"></i>Ganti Foto Profil Agent</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="text-center mb-3 " id="previewContainer" style="display: block;">
                @php
                    $pictureId = ($user->roles === 'Agent' && !empty($informasi_klien->picture)) ? $informasi_klien->picture : null;
                @endphp

                <img
                    id="profilePreview"
                    src="{{ $pictureId ? 'https://drive.google.com/thumbnail?id=' . $pictureId : asset('img/default-profile.jpg') }}"
                    alt="Foto Profil"
                    class="shadow"
                    style="width: 180px; height: 180px; object-fit: cover; margin: 0 auto;"
                />
                <div id="profileCropActions" class="d-flex justify-content-center gap-2 mt-2"></div>
              </div>

              <div class="mb-3">
                <label for="profileImageInput" class="form-label fw-bold">Pilih Foto Baru</label>
                <input type="file" class="form-control" id="profileImageInput" name="profile_image_input" accept="image/*" required>
                <input type="hidden" name="cropped_profile_image" id="croppedProfileImage">
              </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-secondary-theme">
              <i class="bi bi-save me-1"></i> Simpan
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </form>
      </div>
    </div>
  </div>



      <!-- RIGHT SIDE -->
      <div class="col-lg-6">
        <div class="card shadow border-0 rounded-4">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Update Profil</h5>
          </div>
          <div class="card-body">
            <form action="{{ route('profile.update') }}" method="POST" class="needs-validation" novalidate>
              @method('PUT')
              @csrf

              @if($user->roles === 'Agent')
                <div class="row g-3 align-items-start">
                {{-- Foto Profil Agent --}}
                <div class="col-md-5 text-center">
                    @php
                    $agentFoto = !empty($agent->picture)
                        ? asset('storage/' . $agent->picture)
                        : asset('img/default-profile.jpg');
                    @endphp
                    <div class="position-relative d-inline-block">
                        <img src="https://drive.google.com/thumbnail?id={{ $informasi_klien->picture }}"
                        alt="Foto Agent"
                        class="img-fluid shadow"
                        style="width: 200px; height: 200px; object-fit: cover;">

                    <button type="button"
                            class="btn btn-sm btn-light border position-absolute top-0 end-0 m-1"
                            data-bs-toggle="modal"
                            data-bs-target="#modalGantiFoto"
                            style="background: rgba(255, 255, 255, 0.8);">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    </div>
                    <p class="text-muted mt-2"></p>
                </div>
                {{-- Form Data --}}
                <div class="col-md-7">
                  <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                      <input type="text" class="form-control" id="nama" name="nama"
                             value="{{ old('nama', $user->nama ?? '') }}" required>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                      <input type="email" class="form-control" id="email" name="email"
                             value="{{ old('email', $user->email ?? '') }}" required>
                    </div>
                  </div>
                </div>
              </div>
              @else
              {{-- Jika bukan Agent --}}
              <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" class="form-control" id="nama" name="nama"
                         value="{{ old('nama', $user->nama ?? '') }}" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                  <input type="email" class="form-control" id="email" name="email"
                         value="{{ old('email', $user->email ?? '') }}" required>
                </div>
              </div>
              @endif

              <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-calendar-event-fill"></i></span>
                  <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir ?? '') }}" required>
                </div>
              </div>

              <!-- Kota -->
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="kota" class="form-label">Kabupaten/Kota</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input type="text" class="form-control" id="kota" name="kota" value="{{ old('kota', $user->kota ?? '') }}" readonly>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="kecamatan" class="form-label">Kecamatan</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                    <input type="text" class="form-control" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $user->kecamatan ?? '') }}" readonly>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                  <input type="tel" class="form-control" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon ?? '') }}" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">Pesan Tambahan</label>
                <textarea class="form-control" id="message" name="message" rows="3">{{ old('message') }}</textarea>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

@include('template.footer')


<div id="modalKTP" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalKTPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">

        @php
            $isEdit = isset($informasi_klien) && $informasi_klien !== null;
        @endphp

        <form
            action="{{ $isEdit ? route('ktp.edit') : route('ktp.save') }}"
            method="POST"
            enctype="multipart/form-data"
            class="needs-validation"
            novalidate
        >
            @csrf

          <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title text-white" id="modalKTPLabel">
              <i class="bi bi-card-text me-2"></i>Form Kartu Tanda Penduduk (KTP)
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">

              <!-- Nomor KTP (NIK) -->
              <div class="col-md-6">
                <label for="nik" class="form-label">
                  <i class="bi bi-credit-card-2-front-fill me-1"></i> Nomor KTP (NIK)
                </label>
                <input
                  type="text"
                  class="form-control"
                  id="nik"
                  name="nik"
                  value="{{ old('nik', $informasi_klien->nik ?? '') }}"
                  required
                >
                <div class="invalid-feedback">Mohon isi Nomor KTP dengan benar.</div>
              </div>

              <!-- Alamat -->
              <div class="col-md-6">
                <label for="alamat" class="form-label">
                  <i class="bi bi-geo-alt-fill me-1"></i> Alamat
                </label>
                <textarea
                  class="form-control"
                  id="alamat"
                  name="alamat"
                  rows="2"
                  required
                >{{ old('alamat', $informasi_klien->alamat ?? '') }}</textarea>
                <div class="invalid-feedback">Mohon isi alamat lengkap.</div>
              </div>

              <!-- Jenis Kelamin -->
              <div class="col-md-6">
                <label for="jenis_kelamin" class="form-label">
                  <i class="bi bi-gender-ambiguous me-1"></i> Jenis Kelamin
                </label>
                <select
                  class="form-select"
                  id="jenis_kelamin"
                  name="jenis_kelamin"
                  required
                >
                  <option value="" disabled {{ old('jenis_kelamin', $informasi_klien->jenis_kelamin ?? '') == '' ? 'selected' : '' }}>
                    Pilih Jenis Kelamin
                  </option>
                  <option value="Laki-laki" {{ old('jenis_kelamin', $informasi_klien->jenis_kelamin ?? '') == 'Laki-laki' ? 'selected' : '' }}>
                    Laki-laki
                  </option>
                  <option value="Perempuan" {{ old('jenis_kelamin', $informasi_klien->jenis_kelamin ?? '') == 'Perempuan' ? 'selected' : '' }}>
                    Perempuan
                  </option>
                </select>
                <div class="invalid-feedback">Mohon pilih jenis kelamin.</div>
              </div>

              <!-- Pekerjaan -->
              <div class="col-md-6">
                <label for="pekerjaan" class="form-label">
                  <i class="bi bi-briefcase-fill me-1"></i> Pekerjaan
                </label>
                <input
                  type="text"
                  class="form-control"
                  id="pekerjaan"
                  name="pekerjaan"
                  value="{{ old('pekerjaan', $informasi_klien->pekerjaan ?? '') }}"
                >
              </div>

              <!-- Berlaku Hingga -->
              <div class="col-md-6">
                <label for="berlaku_hingga" class="form-label">
                  <i class="bi bi-clock-history me-1"></i> Berlaku Hingga
                </label>
                <div class="input-group">
                  <div class="input-group-text">
                    <input
                      type="checkbox"
                      id="seumur_hidup"
                      name="seumur_hidup"
                      aria-label="Seumur Hidup Checkbox"
                      {{ old('berlaku_hingga', $informasi_klien->berlaku_hingga ?? '') === 'Seumur Hidup' ? 'checked' : '' }}
                    >
                    <label for="seumur_hidup" class="ms-2 mb-0">Seumur Hidup</label>
                  </div>
                  <input
                    type="date"
                    class="form-control"
                    id="berlaku_hingga"
                    name="berlaku_hingga"
                    aria-label="Tanggal berlaku hingga"
                    @if(session('role') === 'User' && isset($informasi_klien->berlaku_hingga))
                    value="{{ old('berlaku_hingga', $informasi_klien->berlaku_hingga !== 'Seumur Hidup' ? $informasi_klien->berlaku_hingga : '') }}"
                    @else
                    value=""
                    @endif
                  >
                </div>
                </div>

<!-- Upload Gambar KTP -->
<div class="col-md-6">
    <label for="gambar_ktp" class="form-label">
      <i class="bi bi-upload me-1"></i> Upload Gambar KTP
    </label>
    <input
      type="file"
      class="form-control"
      id="gambar_ktp_modal"
      name="gambar_ktp"
      accept="image/*"
      onchange="initCropper(event, 'imagePreview_modal', 'cropped_image_modal')"
      required
    >

    <div class="text-center mt-3">
      <img
        id="imagePreview_modal"
        class="img-fluid rounded shadow"
        style="max-width: 100%; max-height: 400px; display: none;"
        alt="Preview Gambar KTP"
      >
    </div>

    <input type="hidden" name="cropped_image" id="cropped_image_modal">
</div>

<script>
let activeCropperKTP = null;
let activeInputKTP = null;
let activeHiddenInputKTP = null;
let activeImageKTP = null;
let cropActionsKTP = null;

function initCropperKTP(event, previewImageId, hiddenInputId) {
    activeInputKTP = event.target;
    activeHiddenInputKTP = document.getElementById(hiddenInputId);
    activeImageKTP = document.getElementById(previewImageId);

    if (activeInputKTP.files && activeInputKTP.files.length > 0) {
        const file = activeInputKTP.files[0];
        const url = URL.createObjectURL(file);

        activeImageKTP.src = url;
        activeImageKTP.style.display = 'block';

        if (activeCropperKTP) activeCropperKTP.destroy();

        activeCropperKTP = new Cropper(activeImageKTP, {
            aspectRatio: NaN,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            movable: true,
            zoomable: true,
            scalable: false,
            rotatable: false,
            cropBoxResizable: true,
            cropBoxMovable: true,
            minCropBoxWidth: 50,
            minCropBoxHeight: 50,
        });

        // ✅ KIRIM PARAMETER INPUT
        showCropActionsKTP(activeInputKTP);
    }
}

function showCropActionsKTP() {
    if (cropActionsKTP) cropActionsKTP.remove();

    cropActionsKTP = document.createElement('div');
    cropActionsKTP.style.marginTop = '10px';
    cropActionsKTP.classList.add('d-flex', 'justify-content-center', 'gap-2');

    // ✅ Force tempel tombol langsung setelah img preview
    activeImageKTP.insertAdjacentElement('afterend', cropActionsKTP);

    const cropBtn = document.createElement('button');
    cropBtn.type = 'button';
    cropBtn.className = 'btn btn-success btn-sm';
    cropBtn.textContent = 'Crop';
    cropBtn.addEventListener('click', cropImageKTP);
    cropActionsKTP.appendChild(cropBtn);

    const cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.className = 'btn btn-danger btn-sm';
    cancelBtn.textContent = 'Cancel';
    cancelBtn.addEventListener('click', cancelCropKTP);
    cropActionsKTP.appendChild(cancelBtn);
}

function cropImageKTP() {
    if (!activeCropperKTP) return;

    const canvas = activeCropperKTP.getCroppedCanvas({
        width: 800,
        height: 600,
        fillColor: '#fff',
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    activeHiddenInputKTP.value = canvas.toDataURL('image/jpeg');
    activeCropperKTP.destroy();
    activeCropperKTP = null;
    activeImageKTP.style.display = 'none';
    cropActionsKTP.remove();
}

function cancelCropKTP() {
    if (activeCropperKTP) {
        activeCropperKTP.destroy();
        activeCropperKTP = null;
    }
    activeImageKTP.style.display = 'none';
    activeInputKTP.value = '';
    cropActionsKTP.remove();
}

    </script>

        </div>







            <div class="mt-4 alert alert-info d-flex align-items-center">
              <i class="bi bi-info-circle-fill me-2"></i>
              Setelah menyimpan data KTP, Anda diwajibkan mengunggah file scan/foto KTP.
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Simpan
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </form>
      </div>
    </div>
  </div>



  <script>
    // Disable/enable tanggal berlaku jika checkbox seumur hidup dicentang
    document.addEventListener("DOMContentLoaded", function () {
      const checkbox = document.getElementById("seumur_hidup");
      const dateInput = document.getElementById("berlaku_hingga");

      checkbox.addEventListener("change", function () {
        if (this.checked) {
          dateInput.value = '';
          dateInput.disabled = true;
        } else {
          dateInput.disabled = false;
        }
      });

      // Trigger initial state
      if (checkbox.checked) {
        dateInput.disabled = true;
      }
    });

    // Bootstrap form validation
    (() => {
      'use strict'
      const forms = document.querySelectorAll('.needs-validation')

      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })()
  </script>

<!-- Modal Tambah/Edit NPWP -->
<div id="modalNPWP" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalNPWPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">
        <form action="{{ route('npwp.save') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          @csrf

          <div class="modal-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-white">
              <i class="bi bi-credit-card-2-back me-2"></i>Form Nomor Pokok Wajib Pajak (NPWP)
            </h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">

              <div class="col-md-6">
                <label for="nomor_npwp" class="form-label"><i class="bi bi-card-text me-1"></i> Nomor NPWP</label>
                <input
                  type="text" class="form-control" id="nomor_npwp" name="nomor_npwp"
                  value="{{ old('nomor_npwp', $informasi_klien->nomor_npwp ?? '') }}" required
                >
                <div class="invalid-feedback">Mohon isi Nomor NPWP dengan benar.</div>
              </div>

              <div class="col-md-6">
                <label for="gambar_npwp_modal" class="form-label">
                  <i class="bi bi-upload me-1"></i> Upload Gambar NPWP
                </label>
                <input
                  type="file"
                  class="form-control"
                  id="gambar_npwp_modal"
                  name="gambar_npwp"
                  accept="image/*"
                  onchange="initCropper(event, 'imagePreviewNPWP_modal', 'cropped_image_npwp_modal')"
                  {{ isset($informasi_klien) ? '' : 'required' }}
                >
                <input type="hidden" name="cropped_image_npwp" id="cropped_image_npwp_modal" value="{{ old('cropped_image_npwp') }}">

                <div class="mt-3" style="max-height: 400px; max-width: 100%;">
                  <img
                    id="imagePreviewNPWP_modal"
                    class="img-fluid rounded shadow"
                    style="max-width: 100%; max-height: 400px; display: none;"
                    src=""
                    alt="Preview Gambar NPWP"
                  >
                </div>
              </div>

              <script>
                let cropper;
                const input = document.getElementById('profileImageInput');
                const image = document.getElementById('profilePreview');
                const previewContainer = document.getElementById('previewContainer');
                const hiddenInput = document.getElementById('croppedProfileImage');
                const actionContainer = document.getElementById('profileCropActions');

                input.addEventListener('change', function (e) {
                  const file = e.target.files[0];
                  if (!file) return;

                  const url = URL.createObjectURL(file);
                  image.src = url;
                  image.style.display = 'block';
                  previewContainer.style.display = 'block';

                  if (cropper) cropper.destroy();

                  cropper = new Cropper(image, {
                    aspectRatio: 1, // Kotak
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                  });

                  // Tampilkan tombol aksi crop
                  renderCropButtons();
                });

                function renderCropButtons() {
                  actionContainer.innerHTML = '';

                  const cropBtn = document.createElement('button');
                  cropBtn.type = 'button';
                  cropBtn.className = 'btn btn-primary btn-sm';
                  cropBtn.textContent = 'Crop';
                  cropBtn.onclick = function () {
                    const canvas = cropper.getCroppedCanvas({
                      width: 500,
                      height: 500,
                      imageSmoothingEnabled: true,
                      imageSmoothingQuality: 'high',
                    });

                    const base64 = canvas.toDataURL('image/jpeg');
                    hiddenInput.value = base64;
                    image.src = base64;

                    cropper.destroy();
                    cropper = null;
                    actionContainer.innerHTML = '';
                  };

                  const cancelBtn = document.createElement('button');
                  cancelBtn.type = 'button';
                  cancelBtn.className = 'btn btn-secondary btn-sm';
                  cancelBtn.textContent = 'Cancel';
                  cancelBtn.onclick = function () {
                    cropper.destroy();
                    cropper = null;
                    input.value = '';
                    image.src = '';
                    previewContainer.style.display = 'none';
                    actionContainer.innerHTML = '';
                  };

                  actionContainer.appendChild(cropBtn);
                  actionContainer.appendChild(cancelBtn);
                }
              </script>


              <script>
                let activeCropper = null;
                let activeInput = null;
                let activeHiddenInput = null;
                let activeImage = null;
                let cropActions = null;

                function initCropper(event, previewImageId, hiddenInputId) {
                  activeInput = event.target;
                  activeHiddenInput = document.getElementById(hiddenInputId);
                  activeImage = document.getElementById(previewImageId);

                  if (activeInput.files && activeInput.files.length > 0) {
                    const file = activeInput.files[0];
                    const url = URL.createObjectURL(file);

                    activeImage.src = url;
                    activeImage.style.display = 'block';

                    // Hancurkan cropper lama jika ada
                    if (activeCropper) {
                      activeCropper.destroy();
                    }

                    // Inisialisasi cropper baru
                    activeCropper = new Cropper(activeImage, {
                      aspectRatio: NaN, // Bebas resize
                      viewMode: 1,
                      autoCropArea: 1,
                      responsive: true,
                      movable: true,
                      zoomable: true,
                      scalable: false,
                      rotatable: false,
                      cropBoxResizable: true,
                      cropBoxMovable: true,
                      minCropBoxWidth: 50,
                      minCropBoxHeight: 50,
                    });

                    showCropActions(activeInput);
                  }
                }

                function showCropActions(input) {
                  if (cropActions) cropActions.remove(); // Bersihkan tombol sebelumnya

                  cropActions = document.createElement('div');
                  cropActions.style.marginTop = '10px';
                  cropActions.classList.add('d-flex', 'gap-2');
                  input.parentNode.appendChild(cropActions);

                  const cropBtn = document.createElement('button');
                  cropBtn.type = 'button';
                  cropBtn.className = 'btn btn-primary btn-sm';
                  cropBtn.textContent = 'Crop';
                  cropBtn.addEventListener('click', cropImage);
                  cropActions.appendChild(cropBtn);

                  const cancelBtn = document.createElement('button');
                  cancelBtn.type = 'button';
                  cancelBtn.className = 'btn btn-secondary btn-sm';
                  cancelBtn.textContent = 'Cancel';
                  cancelBtn.addEventListener('click', cancelCrop);
                  cropActions.appendChild(cancelBtn);
                }

                function cropImage() {
                  if (!activeCropper) return;

                  // Ambil area crop yang dipilih user
                  const canvas = activeCropper.getCroppedCanvas({
                    width: 800,  // Ukuran final (atur sesuai kebutuhan)
                    height: 600,
                    fillColor: '#fff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                  });

                  // Simpan hasil crop ke hidden input
                  activeHiddenInput.value = canvas.toDataURL('image/jpeg');

                  // Bersihkan cropper & UI
                  activeCropper.destroy();
                  activeCropper = null;
                  activeImage.style.display = 'none';
                  cropActions.remove();
                }

                function cancelCrop() {
                  if (activeCropper) {
                    activeCropper.destroy();
                    activeCropper = null;
                  }
                  activeImage.style.display = 'none';
                  activeInput.value = ''; // Reset file input
                  cropActions.remove();
                }

              </script>



            </div>

            <div class="mt-4 alert alert-info d-flex align-items-center">
              <i class="bi bi-info-circle-fill me-2"></i>
              Harap unggah foto/scan kartu NPWP Anda secara jelas.
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-secondary-theme">
              <i class="bi bi-save me-1"></i> Simpan
            </button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <script>
    // Bootstrap form validation
    (() => {
      'use strict'
      const forms = document.querySelectorAll('.needs-validation')

      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })()

  </script>


             <!-- Modal Tambah/Edit Rekening -->
<div id="modalRekening" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalRekeningLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">
        <form action="{{ route('rekening.save') }}" method="POST" onsubmit="return validateRekening()" class="needs-validation" novalidate>
  @csrf
  <div class="modal-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h6 class="mb-0 text-white"><i class="bi bi-bank me-2"></i>Form Data Rekening Bank</h6>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
  <div class="modal-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label for="nama_bank" class="form-label"><i class="bi bi-building me-1"></i>Nama Bank</label>
        <input type="text" class="form-control" id="nama_bank" name="nama_bank" value="{{ old('nama_bank', $informasi_klien->nama_bank ?? '') }}" required>
        <div class="invalid-feedback">Mohon isi nama bank.</div>
      </div>
      <div class="col-md-6">
        <label for="atas_nama" class="form-label"><i class="bi bi-person-check-fill me-1"></i>Atas Nama</label>
        <input type="text" class="form-control" id="atas_nama" name="atas_nama" value="{{ old('atas_nama', $informasi_klien->atas_nama ?? '') }}" required>
        <div class="invalid-feedback">Mohon isi nama pemilik rekening.</div>
      </div>
      <div class="col-md-6">
        <label for="nomor_rekening" class="form-label"><i class="bi bi-credit-card-2-front-fill me-1"></i>Nomor Rekening</label>
        <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" value="{{ old('nomor_rekening', $informasi_klien->nomor_rekening ?? '') }}" required>
        <div class="invalid-feedback">Mohon isi nomor rekening.</div>
      </div>
      <div class="col-md-6">
        <label for="konfirmasi_rekening" class="form-label"><i class="bi bi-arrow-repeat me-1"></i>Ulangi Nomor Rekening</label>
        <input type="text" class="form-control" id="konfirmasi_rekening" name="konfirmasi_rekening" value="{{ old('konfirmasi_rekening', $informasi_klien->nomor_rekening ?? '') }}" required>
        <div id="rekeningError" class="text-danger mt-1 d-none">Nomor rekening tidak cocok!</div>
        <div class="invalid-feedback">Mohon ulangi nomor rekening dengan benar.</div>
      </div>
    </div>
    <div class="mt-4 alert alert-info d-flex align-items-center">
      <i class="bi bi-info-circle-fill me-2"></i>
      Pastikan data rekening sesuai dengan nama dan nomor yang terdaftar di bank.
    </div>
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-secondary-theme">
      <i class="bi bi-save me-1"></i> Simpan
    </button>
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
  </div>
</form>

      </div>
    </div>
  </div>

            <!-- HIDE NAVBAR -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.querySelector('nav.navbar');
    const modalKTP = document.getElementById('modalKTP');
    const modalNPWP = document.getElementById('modalNPWP');
    const modalRekening = document.getElementById('modalRekening');

    if (modalKTP) {
      modalKTP.addEventListener('show.bs.modal', function () {
        navbar.style.display = 'none';
      });
      modalKTP.addEventListener('hidden.bs.modal', function () {
        navbar.style.display = '';
      });
    }

    if (modalNPWP) {
      modalNPWP.addEventListener('show.bs.modal', function () {
        navbar.style.display = 'none';
      });
      modalNPWP.addEventListener('hidden.bs.modal', function () {
        navbar.style.display = '';
      });
    }

    if (modalRekening) {
      modalRekening.addEventListener('show.bs.modal', function () {
        navbar.style.display = 'none';
      });
      modalRekening.addEventListener('hidden.bs.modal', function () {
        navbar.style.display = '';
      });
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.querySelector('nav.navbar');
    const modalGantiFoto = document.getElementById('modalGantiFoto');

    if (modalGantiFoto) {
      modalGantiFoto.addEventListener('show.bs.modal', function () {
        if (navbar) navbar.style.display = 'none';
      });

      modalGantiFoto.addEventListener('hidden.bs.modal', function () {
        if (navbar) navbar.style.display = '';
      });
    }
  });

    document.addEventListener("DOMContentLoaded", function () {
                    const checkbox = document.getElementById("seumur_hidup");
                    const dateInput = document.getElementById("berlaku_hingga");

                    checkbox.addEventListener("change", function () {
                        if (this.checked) {
                            dateInput.value = 'Seumur Hidup';
                            dateInput.disabled = true;
                        } else {
                            dateInput.disabled = false;
                            dateInput.value = ''; // kosongkan kalau ubah dari 'Seumur Hidup'
                        }
                    });

                    // Trigger default state on load
                    if (checkbox.checked) {
                        dateInput.value = 'Seumur Hidup';
                        dateInput.disabled = true;
                    }
                });

    function validateRekening() {
        const rekening = document.getElementById("nomor_rekening").value.trim();
        const konfirmasi = document.getElementById("konfirmasi_rekening").value.trim();
        const errorDiv = document.getElementById("rekeningError");

        if (rekening !== konfirmasi) {
            errorDiv.classList.remove("d-none");
            return false; // Gagal submit
        }

        errorDiv.classList.add("d-none");
        return true; // Lolos validasi
    }
</script>
<style>
    :root {
      --dark: #0E2E50;
      --primary: #dc3545;
      --secondary: #f35525;
    }

    .btn-primary,
    .btn-success,
    .btn-warning,
    .btn-darkblue {
      background-color: var(--dark) !important;
      border-color: var(--dark) !important;
      color: #fff !important;
    }

    .card-header {
      color: white !important;
    }

    .card-header h6,
    .card-header span,
    .card-header h5 {
      color: white !important;
    }

    .btn-secondary-theme {
      background-color: var(--secondary) !important;
      border-color: var(--secondary) !important;
      color: #fff !important;
      transition: all 0.2s ease-in-out;
    }

    /* Hover warna darkblue */
    .btn-secondary-theme:hover {
      background-color: var(--dark) !important;
      border-color: var(--dark) !important;
      color: #fff !important;
    }
  </style>

<script>
// === Untuk KTP ===
let cropperKTP;
const inputKTP = document.getElementById('gambar_ktp');
const imageKTP = document.getElementById('imagePreview');
const cropActionsIdKTP = 'cropActionsKTP';

function showCropActionsKTP() {
    let cropActions = document.getElementById(cropActionsIdKTP);
    if (!cropActions) {
        cropActions = document.createElement('div');
        cropActions.id = cropActionsIdKTP;
        cropActions.style.marginTop = '10px';
        cropActions.classList.add('d-flex', 'gap-2');
        inputKTP.parentNode.appendChild(cropActions);

        const cropBtn = document.createElement('button');
        cropBtn.type = 'button';
        cropBtn.className = 'btn btn-primary btn-sm';
        cropBtn.id = 'cropBtnKTP';
        cropBtn.textContent = 'Crop';
        cropActions.appendChild(cropBtn);

        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn-secondary btn-sm';
        cancelBtn.id = 'cancelCropBtnKTP';
        cancelBtn.textContent = 'Cancel';
        cropActions.appendChild(cancelBtn);

        cropBtn.addEventListener('click', cropImageKTP);
        cancelBtn.addEventListener('click', cancelCropKTP);
    }
    cropActions.style.display = 'flex';
}

function hideCropActionsKTP() {
    const cropActions = document.getElementById(cropActionsIdKTP);
    if (cropActions) {
        cropActions.remove();
    }
}

// ✅ Ini fungsi global yang bisa dipanggil onchange dari HTML
function previewKTP(event) {
    if (event.target.files && event.target.files.length > 0) {
        const file = event.target.files[0];
        const url = URL.createObjectURL(file);

        imageKTP.src = url;
        imageKTP.style.display = 'block';
        showCropActionsKTP();

        if (cropperKTP) cropperKTP.destroy();

        cropperKTP = new Cropper(imageKTP, {
            aspectRatio: NaN,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            movable: true,
            zoomable: true,
            scalable: false,
            rotatable: false,
            cropBoxResizable: true,
            cropBoxMovable: true,
            minCropBoxWidth: 50,
            minCropBoxHeight: 50,
        });
    }
}

function cropImageKTP() {
    if (!cropperKTP) return;

    const canvas = cropperKTP.getCroppedCanvas({
        width: 800,
        height: 600,
        fillColor: '#fff',
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    document.getElementById('cropped_image').value = canvas.toDataURL('image/jpeg');

    hideCropActionsKTP();
    cropperKTP.destroy();
    cropperKTP = null;
    imageKTP.style.display = 'none';
}

function cancelCropKTP() {
    if (cropperKTP) {
        cropperKTP.destroy();
        cropperKTP = null;
    }
    imageKTP.style.display = 'none';
    hideCropActionsKTP();
    inputKTP.value = null;
}


    // === Untuk NPWP ===
    let cropperNPWP;
    const inputNPWP = document.getElementById('gambar_npwp');
    const imageNPWP = document.getElementById('imagePreviewNPWP');
    const cropActionsIdNPWP = 'cropActionsNPWP';

    function showCropActionsNPWP() {
        let cropActions = document.getElementById(cropActionsIdNPWP);
        if (!cropActions) {
            cropActions = document.createElement('div');
            cropActions.id = cropActionsIdNPWP;
            cropActions.style.marginTop = '10px';
            cropActions.classList.add('d-flex', 'gap-2');
            inputNPWP.parentNode.appendChild(cropActions);

            const cropBtn = document.createElement('button');
            cropBtn.type = 'button';
            cropBtn.className = 'btn btn-primary btn-sm';
            cropBtn.id = 'cropBtnNPWP';
            cropBtn.textContent = 'Crop';
            cropActions.appendChild(cropBtn);

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.className = 'btn btn-secondary btn-sm';
            cancelBtn.id = 'cancelCropBtnNPWP';
            cancelBtn.textContent = 'Cancel';
            cropActions.appendChild(cancelBtn);

            cropBtn.addEventListener('click', cropImageNPWP);
            cancelBtn.addEventListener('click', cancelCropNPWP);
        }
        cropActions.style.display = 'flex';
    }

    function hideCropActionsNPWP() {
        const cropActions = document.getElementById(cropActionsIdNPWP);
        if (cropActions) {
            cropActions.remove();
        }
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
                scalable: false,
                rotatable: false,
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
</script>







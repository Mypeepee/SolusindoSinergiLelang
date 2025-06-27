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
                      <img src="{{ asset('storage/' . $informasi_klien->gambar_ktp) }}" alt="KTP"
                           class="img-thumbnail me-4" style="width: 150px; height: auto; object-fit: contain;" />
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
                      <img src="{{ asset('storage/' . $informasi_klien->gambar_npwp) }}" alt="NPWP"
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

            {{-- === ROLE: AGENT === --}}
            @if(session('role') === 'Agent')
              <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-primary text-white">
                  <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Data Pribadi Agent</h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    {{-- Gambar KTP --}}
                    <div class="col-md-6 text-center mb-3">
                      <h6 class="text-muted mb-2">Foto KTP</h6>
                      @if(!empty($informasi_klien->gambar_ktp))
                        <img src="{{ asset('storage/' . $informasi_klien->gambar_ktp) }}"
                             alt="KTP Agent"
                             class="img-thumbnail"
                             style="max-width: 100%; height: auto; object-fit: contain;" />
                      @else
                        <p class="text-muted fst-italic">Belum ada gambar KTP.</p>
                      @endif
                    </div>

                    {{-- Gambar NPWP --}}
                    <div class="col-md-6 text-center mb-3">
                      <h6 class="text-muted mb-2">Foto NPWP</h6>
                      @if(!empty($informasi_klien->gambar_npwp))
                        <img src="{{ asset('storage/' . $informasi_klien->gambar_npwp) }}"
                             alt="NPWP Agent"
                             class="img-thumbnail"
                             style="max-width: 100%; height: auto; object-fit: contain;" />
                      @else
                        <p class="text-muted fst-italic">Belum ada gambar NPWP.</p>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endif

          </div>


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
                <img
                  id="profilePreview"
                  src="{{ optional($informasi_klien)->picture ? asset('storage/' . $informasi_klien->picture) : asset('img/default-profile.jpg') }}"
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

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      let cropper;
      const input = document.getElementById('profileImageInput');
      const image = document.getElementById('profilePreview');
      const previewContainer = document.getElementById('previewContainer');
      const hiddenInput = document.getElementById('croppedProfileImage');
      const cropActionsId = 'profileCropActions';

      function showCropActions() {
  const cropActions = document.getElementById(cropActionsId);
  cropActions.innerHTML = ''; // hapus isi sebelumnya jika ada

  const cropBtn = document.createElement('button');
  cropBtn.type = 'button';
  cropBtn.className = 'btn btn-success btn-sm';
  cropBtn.textContent = 'Crop';
  cropBtn.onclick = cropImage;

  const cancelBtn = document.createElement('button');
  cancelBtn.type = 'button';
  cancelBtn.className = 'btn btn-secondary btn-sm';
  cancelBtn.textContent = 'Cancel';
  cancelBtn.onclick = cancelCrop;

  cropActions.appendChild(cropBtn);
  cropActions.appendChild(cancelBtn);
  cropActions.style.display = 'flex';
}


function hideCropActions() {
  const cropActions = document.getElementById(cropActionsId);
  if (cropActions) cropActions.innerHTML = '';
}


      input.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
          image.onload = () => {
            if (cropper) cropper.destroy();

            cropper = new Cropper(image, {
              aspectRatio: 1,
              viewMode: 1,
              autoCropArea: 1,
              responsive: true,
              dragMode: 'move',
              zoomable: true,
            });

            showCropActions();
          };

          image.src = event.target.result;
          image.style.display = 'block';
          previewContainer.style.display = 'block';
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
  hiddenInput.value = base64;

  cropper.destroy();
  cropper = null;

  image.onload = () => {
    image.style.display = 'block';
    image.style.margin = '0 auto';
    image.style.objectFit = 'cover';
    image.style.width = '180px';
    image.style.height = '180px';
    image.className = 'shadow'; // Hapus 'rounded-circle' agar kotak
    hideCropActions();
  };

  image.src = base64;
}




      function cancelCrop() {
        if (cropper) {
          cropper.destroy();
          cropper = null;
        }
        input.value = '';
        hiddenInput.value = '';
        previewContainer.style.display = 'none';
        image.style.display = 'none';
        hideCropActions();
      }
    });
    </script>

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
$agentFoto = optional($informasi_klien)->picture
    ? asset('storage/' . $informasi_klien->picture)
    : asset('img/default-profile.jpg');
@endphp

<div class="position-relative d-inline-block">
  <img src="{{ $agentFoto }}"
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
                  id="gambar_ktp"
                  name="gambar_ktp"
                  accept="image/*"
                  onchange="previewKTP(event)"
                  {{ $isEdit ? '' : 'required' }}
                >
                <input type="hidden" name="cropped_image" id="cropped_image" value="{{ old('cropped_image') }}">

                <div class="mt-3" style="max-height: 400px; max-width: 100%;">
                  <img
                    id="imagePreview"
                    style="max-width: 100%; {{ $isEdit && $informasi_klien->gambar_ktp ? '' : 'display:none;' }}"
                    src="{{ $isEdit && $informasi_klien->gambar_ktp ? asset('storage/' . $informasi_klien->gambar_ktp) : '' }}"
                    alt="Preview Gambar KTP"
                  >
                </div>
              </div>
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
    let cropper;
    const input = document.getElementById('gambar_ktp');
    const image = document.getElementById('imagePreview');
    const cropActionsId = 'cropActions';

    function showCropActions() {
      let cropActions = document.getElementById(cropActionsId);
      if (!cropActions) {
        cropActions = document.createElement('div');
        cropActions.id = cropActionsId;
        cropActions.style.marginTop = '10px';
        cropActions.classList.add('d-flex', 'gap-2');
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

        // Add event listeners here
        cropBtn.addEventListener('click', cropImage);
        cancelBtn.addEventListener('click', cancelCrop);
      }
      cropActions.style.display = 'flex';
    }

    function hideCropActions() {
  const cropActions = document.getElementById('cropActions');
  if (cropActions) {
    cropActions.remove();
  }
}

    input.addEventListener('change', function (e) {
      if (e.target.files && e.target.files.length > 0) {
        const file = e.target.files[0];
        const url = URL.createObjectURL(file);

        image.src = url;
        image.style.display = 'block';
        showCropActions();

        // Destroy cropper jika sudah ada
        if (cropper) cropper.destroy();

        cropper = new Cropper(image, {
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

  // Sembunyikan tombol crop dan cancel
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

    // Preview gambar setelah pilih file (sebelum crop)
    function previewKTP(event) {
      const preview = document.getElementById('imagePreview');
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    }
  </script>


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
                <label for="gambar_npwp" class="form-label"><i class="bi bi-upload me-1"></i> Upload Gambar NPWP</label>
                <input
                  type="file" class="form-control" id="gambar_npwp" name="gambar_npwp" accept="image/*"
                  {{ isset($informasi_klien) ? '' : 'required' }}
                >
                <div class="form-text">Unggah foto/scan kartu NPWP dengan jelas.</div>
                <div class="invalid-feedback">Mohon unggah gambar NPWP.</div>
              </div>

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
    let cropperNPWP;
    const inputNPWP = document.getElementById('gambar_npwp');

    // Buat elemen preview gambar untuk cropper
    const imageNPWP = document.createElement('img');
    imageNPWP.id = 'imagePreviewNPWP';
    imageNPWP.style.maxWidth = '100%';
    imageNPWP.style.display = 'none';
    imageNPWP.style.marginTop = '10px';

    // Tempelkan preview tepat setelah input file
    inputNPWP.parentNode.appendChild(imageNPWP);

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

    inputNPWP.addEventListener('change', function(e) {
      if (e.target.files && e.target.files.length > 0) {
        const file = e.target.files[0];
        const url = URL.createObjectURL(file);

        imageNPWP.src = url;
        imageNPWP.style.display = 'block';

        // Atur ukuran gambar sesuai lebar input file supaya cropper tidak melebar
        imageNPWP.style.width = inputNPWP.clientWidth + 'px';
        imageNPWP.style.maxWidth = '100%';

        showCropActionsNPWP();

        if (cropperNPWP) cropperNPWP.destroy();

        cropperNPWP = new Cropper(imageNPWP, {
          aspectRatio: NaN,  // Bebas resize
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
        width: inputNPWP.clientWidth, // sesuaikan dengan lebar input file
        height: undefined,            // biarkan proporsional
        fillColor: '#fff',
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
      });

      // Simpan hasil crop dalam input hidden
      let croppedInput = document.getElementById('cropped_image_npwp');
      if (!croppedInput) {
        croppedInput = document.createElement('input');
        croppedInput.type = 'hidden';
        croppedInput.name = 'cropped_image_npwp';
        croppedInput.id = 'cropped_image_npwp';
        inputNPWP.parentNode.appendChild(croppedInput);
      }
      croppedInput.value = canvas.toDataURL('image/jpeg');

      // Sembunyikan tombol crop dan cancel
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


@include('template.header')

<!-- Tambahkan Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Styling Fokus & UX -->
<style>
  .form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  }
  .toggle-password {
    cursor: pointer;
  }
</style>

<main>
  <div class="container">
    <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 col-md-8">

            <div class="text-center mb-4">
              <h4 class="fw-bold">📝 Daftar Akun Baru</h4>
              <p class="text-muted">Silakan isi data diri Anda untuk membuat akun</p>
            </div>

            <div class="card shadow-lg rounded-4">
              <div class="card-body p-4">

                @if ($errors->any())
                  <div class="alert alert-danger">
                    <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('register.form') }}" method="POST" class="needs-validation" novalidate>
                  @csrf

                  <!-- Informasi Pribadi -->
                  <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                      <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                      <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                      <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                      <input type="password" name="password" id="yourPassword" class="form-control" value="{{ old('password') }}" required>
                      <span class="input-group-text toggle-password" onclick="togglePassword()"><i class="bi bi-eye-slash-fill" id="eyeIcon"></i></span>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Nomor Telepon</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                      <input type="tel" name="nomor_telepon" pattern="[0-9]{10,15}" class="form-control" value="{{ old('nomor_telepon') }}" required>
                    </div>
                  </div>

                  <!-- Lokasi -->
                  <hr class="my-4">
                  <h5 class="mb-3"><i class="bi bi-geo-alt-fill"></i> Domisili</h5>

                  <div class="mb-3">
                    <label class="form-label">Provinsi</label>
                    <select class="form-select" id="provinsi" name="provinsi" required>
                      <option value="">Pilih Provinsi</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Kabupaten/Kota</label>
                    <select class="form-select" id="regency" name="kota" required>
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kecamatan</label>
                    <select class="form-select" id="district" name="kecamatan" required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>

                  <div class="mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                  </div>

                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="terms" id="acceptTerms" required>
                    <label class="form-check-label" for="acceptTerms">
                      Saya menyetujui <a href="#">syarat & ketentuan</a>
                    </label>
                  </div>

                  <div class="d-grid">
                    <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-person-plus-fill me-2"></i>Buat Akun</button>
                  </div>

                  <p class="text-center small mt-3">Sudah punya akun? <a href="{{ url('/login') }}">Masuk di sini</a></p>
                </form>

              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>
</main>

<!-- Show/Hide Password Toggle -->
<script>
  function togglePassword() {
    const password = document.getElementById('yourPassword');
    const icon = document.getElementById('eyeIcon');
    if (password.type === 'password') {
      password.type = 'text';
      icon.classList.remove('bi-eye-slash-fill');
      icon.classList.add('bi-eye-fill');
    } else {
      password.type = 'password';
      icon.classList.remove('bi-eye-fill');
      icon.classList.add('bi-eye-slash-fill');
    }
  }
</script>

<!-- Script Dropdown Lokasi -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById("provinsi");
    const regencySelect = document.getElementById("regency");
    const districtSelect = document.getElementById("district");
    let data = [];

    fetch("{{ asset('data/indonesia.json') }}")
      .then(response => response.json())
      .then(json => {
        data = json;
        const provinces = [...new Set(data.map(item => item.province))];
        provinces.forEach(province => {
          provinceSelect.add(new Option(province, province));
        });
      });

    provinceSelect.addEventListener("change", function () {
      regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
      districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
      const filtered = data.filter(item => item.province === this.value);
      const regencies = [...new Set(filtered.map(item => item.regency))];
      regencies.forEach(reg => {
        regencySelect.add(new Option(reg, reg));
      });
    });

    regencySelect.addEventListener("change", function () {
      districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
      const filtered = data.filter(item =>
        item.province === provinceSelect.value && item.regency === this.value
      );
      const districts = [...new Set(filtered.map(item => item.district))];
      districts.forEach(dist => {
        districtSelect.add(new Option(dist, dist));
      });
    });
  });
</script>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/js/mainadmin.js"></script>

@include('template.footer')

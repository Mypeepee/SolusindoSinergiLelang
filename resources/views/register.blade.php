@include('template.header')

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }

  body {
    background: url('/img/home.webp') no-repeat center center fixed;
    background-size: cover;
  }

  .page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  main {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 5rem 1rem 3rem; /* Space atas & bawah */
  }

  .register-card {
    background: rgba(255, 255, 255, 0.95); /* Semi transparan putih */
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    width: 600px;
    max-width: 95%; /* Responsive di mobile */
    padding: 2rem;
    color: #333;
  }

  .register-card h4 {
    text-align: center;
    margin-bottom: 1rem;
    font-weight: 600;
  }

  .form-control,
  .input-group-text,
  .form-select {
    background: #fff; /* Putih solid */
    border: 1px solid #ddd;
    color: #333;
  }

  .form-control::placeholder {
    color: #888;
  }

  .form-control:focus,
  .form-select:focus {
    background: #fff;
    border-color: #ff8c00; /* Orange saat focus */
    box-shadow: 0 0 5px rgba(255, 140, 0, 0.5);
    color: #333;
  }

  .btn-primary {
    background-color: #ff8c00; /* Orange */
    border: none;
    transition: 0.3s;
  }

  .btn-primary:hover {
    background-color: #e67e00; /* Darker orange */
  }

  .register-card a {
    color: #ff8c00; /* Link orange */
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
  }

  .register-card a:hover {
    color: #e67e00; /* Darker saat hover */
    text-decoration: underline;
  }

  .toggle-password {
    cursor: pointer;
  }

  .alert {
    backdrop-filter: blur(5px);
  }
</style>

<div class="page-wrapper">
  <main>
    <div class="register-card">
      <h4>📝 Daftar Akun Baru</h4>
      <p class="text-muted text-center mb-4">Silakan isi data diri Anda untuk membuat akun</p>

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
            <span class="input-group-text toggle-password" onclick="togglePassword()">
              <i class="bi bi-eye-slash-fill" id="eyeIcon"></i>
            </span>
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
            <select id="province" class="form-select" required>
                <option selected disabled>Pilih Provinsi</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Kota / Kabupaten</label>
            <select id="regency" name="kota" class="form-select" disabled required>
                <option selected disabled>Pilih Kota/Kabupaten</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Kecamatan</label>
            <select class="form-select" id="district" name="kecamatan" disabled required>
                <option selected disabled>Pilih Kecamatan</option>
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
          <button class="btn btn-primary btn-lg" type="submit">
            <i class="bi bi-person-plus-fill me-2"></i>Buat Akun
          </button>
        </div>

        <p class="text-center small mt-3">Sudah punya akun? <a href="{{ url('/login') }}">Masuk di sini</a></p>
      </form>
    </div>
  </main>

  @include('template.footer')
</div>

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

  document.addEventListener('DOMContentLoaded', function () {
    const province = document.getElementById('province');
    const city = document.getElementById('regency');
    const district = document.getElementById('district');

    const provinceMap = new Map(); // Provinsi => Set kota
    const cityMap = new Map();     // Kota => Set kecamatan

    fetch("{{ asset('data/indonesia.json') }}")
        .then(res => res.json())
        .then(data => {
            // Bangun map provinsi => kota dan kota => kecamatan
            data.forEach(({ province: prov, regency, district: kec }) => {
                if (!provinceMap.has(prov)) {
                    provinceMap.set(prov, new Set());
                }
                provinceMap.get(prov).add(regency);

                if (!cityMap.has(regency)) {
                    cityMap.set(regency, new Set());
                }
                cityMap.get(regency).add(kec);
            });

            // Reset & tambahkan placeholder
            province.innerHTML = '<option selected disabled>Pilih Provinsi</option>';

            // Isi dropdown provinsi
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

        // Reset city dan district
        city.innerHTML = '<option selected disabled>Pilih Kota/Kabupaten</option>';
        city.disabled = true;
        district.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
        district.disabled = true;

        if (kotaSet) {
            [...kotaSet].sort().forEach(kota => {
                const option = document.createElement('option');
                option.value = kota;
                option.textContent = kota;
                city.appendChild(option);
            });
            city.disabled = false;
        }
    });

    // Event: isi kecamatan saat kota berubah
    city.addEventListener('change', function () {
        const selectedCity = this.value;
        const kecSet = cityMap.get(selectedCity);

        // Reset district
        district.innerHTML = '<option selected disabled>Pilih Kecamatan</option>';
        district.disabled = true;

        if (kecSet) {
            [...kecSet].sort().forEach(kec => {
                const option = document.createElement('option');
                option.value = kec;
                option.textContent = kec;
                district.appendChild(option);
            });
            district.disabled = false;
        }
    });
});
    
</script>

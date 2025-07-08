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
    font-family: 'Poppins', sans-serif;
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
    padding: 5rem 1rem 3rem;
  }

  .otp-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    width: 400px;
    max-width: 90%;
    padding: 2rem;
    color: #333;
  }

  .otp-card h4 {
    text-align: center;
    margin-bottom: 1rem;
    font-weight: 600;
  }

  .form-control,
  .input-group-text {
    background: #fff;
    border: 1px solid #ddd;
    color: #333;
  }

  .form-control:focus {
    background: #fff;
    border-color: #ff8c00;
    box-shadow: 0 0 5px rgba(255, 140, 0, 0.5);
    color: #333;
  }

  .btn-primary {
    background-color: #ff8c00;
    border: none;
    transition: 0.3s;
  }

  .btn-primary:hover {
    background-color: #e67e00;
  }

  .otp-card a {
    color: #ff8c00;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
  }

  .otp-card a:hover {
    color: #e67e00;
    text-decoration: underline;
  }

  .otp-input {
    text-align: center;
    font-size: 1.5rem;
    letter-spacing: 0.5rem;
  }
</style>

<div class="page-wrapper">
  <main>
    <div class="otp-card">
      <h4>🔒 Verifikasi OTP</h4>
      <p class="text-muted text-center mb-4">Masukkan 6 digit kode OTP yang telah kami kirim ke email Anda</p>

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      <form action="{{ route('verify.otp') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="mb-4">
          <label for="otp" class="form-label">Kode OTP</label>
          <input type="text" name="otp" id="otp" maxlength="6" minlength="6" pattern="\d{6}" class="form-control otp-input" placeholder="••••••" required>
          <div class="invalid-feedback text-center">
            Masukkan 6 digit kode OTP.
          </div>
        </div>

        <div class="d-grid mb-3">
          <button class="btn btn-primary btn-lg" type="submit">
            <i class="bi bi-shield-lock-fill me-2"></i>Verifikasi OTP
          </button>
        </div>

        <p class="text-center small mt-3">
          Belum menerima kode? <a href="{{ route('resend.otp') }}">Kirim Ulang</a>
        </p>
      </form>
    </div>
  </main>

  @include('template.footer')
</div>

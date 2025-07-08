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
    padding: 5rem 1rem 3rem; /* Jarak atas & bawah */
  }

  .forgot-card {
    background: rgba(255, 255, 255, 0.95); /* Semi transparan putih */
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    width: 400px;
    max-width: 90%; /* Responsive di mobile */
    padding: 2rem;
    color: #333;
  }

  .forgot-card h4 {
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

  .form-control::placeholder {
    color: #888;
  }

  .form-control:focus {
    background: #fff;
    border-color: #ff8c00;
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

  .forgot-card a {
    color: #ff8c00; /* Link orange */
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
  }

  .forgot-card a:hover {
    color: #e67e00;
    text-decoration: underline;
  }
</style>

<div class="page-wrapper">
  <main>
    <div class="forgot-card">
      <h4>🔑 Lupa Password</h4>
      <p class="text-muted text-center mb-4">Masukkan alamat email Anda dan kami akan mengirimkan kode OTP aman untuk mereset kata sandi Anda.
</p>

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

      <form action="{{ route('forgot.send') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="mb-3">
          <label for="email" class="form-label">Alamat Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
            <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" id="email" required>
          </div>
        </div>

        <div class="d-grid mb-3">
          <button class="btn btn-primary btn-lg" type="submit">
            <i class="bi bi-envelope-paper-fill me-2"></i>Kirim Kode OTP
          </button>
        </div>

        <p class="text-center small mt-3">
          Sudah ingat password? <a href="{{ url('/login') }}">Masuk di sini</a>
        </p>
      </form>
    </div>
  </main>

  @include('template.footer')
</div>

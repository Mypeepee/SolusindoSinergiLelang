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

  .reset-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 1rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    width: 400px;
    max-width: 90%;
    padding: 2rem;
    color: #333;
  }

  .reset-card h4 {
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

  .toggle-password {
    cursor: pointer;
  }

  .alert {
    backdrop-filter: blur(5px);
  }
</style>

<div class="page-wrapper">
  <main>
    <div class="reset-card">
      <h4>🔒 Reset Password</h4>
      <p class="text-muted text-center mb-4">Masukkan password baru Anda di bawah ini</p>

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

      <form action="{{ route('password.update', ['email' => $email]) }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <div class="mb-3">
          <label for="newPassword" class="form-label">New Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="new_password" id="newPassword" class="form-control" placeholder="Password baru" required>
            <span class="input-group-text toggle-password" onclick="toggleNewPassword()">
              <i class="bi bi-eye-slash-fill" id="eyeNew"></i>
            </span>
          </div>
        </div>

        <div class="mb-3">
          <label for="confirmPassword" class="form-label">Confirm Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Konfirmasi password" required>
            <span class="input-group-text toggle-password" onclick="toggleConfirmPassword()">
              <i class="bi bi-eye-slash-fill" id="eyeConfirm"></i>
            </span>
          </div>
        </div>

        <div class="d-grid mb-3">
          <button class="btn btn-primary btn-lg" type="submit">
            <i class="bi bi-key-fill me-2"></i>Simpan Password Baru
          </button>
        </div>

        <p class="text-center small">
          <a href="{{ url('/login') }}">Kembali ke Login</a>
        </p>
      </form>
    </div>
  </main>

  @include('template.footer')
</div>

<script>
  function toggleNewPassword() {
    const newPassword = document.getElementById('newPassword');
    const icon = document.getElementById('eyeNew');
    if (newPassword.type === 'password') {
      newPassword.type = 'text';
      icon.classList.remove('bi-eye-slash-fill');
      icon.classList.add('bi-eye-fill');
    } else {
      newPassword.type = 'password';
      icon.classList.remove('bi-eye-fill');
      icon.classList.add('bi-eye-slash-fill');
    }
  }

  function toggleConfirmPassword() {
    const confirmPassword = document.getElementById('confirmPassword');
    const icon = document.getElementById('eyeConfirm');
    if (confirmPassword.type === 'password') {
      confirmPassword.type = 'text';
      icon.classList.remove('bi-eye-slash-fill');
      icon.classList.add('bi-eye-fill');
    } else {
      confirmPassword.type = 'password';
      icon.classList.remove('bi-eye-fill');
      icon.classList.add('bi-eye-slash-fill');
    }
  }
</script>

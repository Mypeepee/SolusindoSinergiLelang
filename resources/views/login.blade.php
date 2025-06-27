@include('template.header')

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .input-group-text {
        background-color: #f8f9fa;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe;
    }
    .toggle-password {
        cursor: pointer;
    }
</style>

<main>
    <div class="container">
        <section class="section login min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold">🔐 Masuk ke Akun Anda</h4>
                            <p class="text-muted">Silakan login dengan username dan password Anda</p>
                        </div>
                        <div class="card shadow-lg rounded-4">
                            <div class="card-body p-4">

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

                                <form action="{{ route('login') }}" method="POST" class="needs-validation" novalidate>
                                    @csrf

                                    <div class="mb-3">
                                        <label for="yourUsername" class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                            <input type="text" name="username" value="{{ old('username') }}" class="form-control" id="yourUsername" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="yourPassword" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                                            <span class="input-group-text toggle-password" onclick="togglePasswordVisibility()">
                                                <i class="bi bi-eye-fill" id="eyeIcon"></i>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- <div class="mb-3 form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">Ingat saya</label>
                                    </div> --}}

                                    <div class="d-grid mb-3">
                                        <button class="btn btn-primary btn-lg" type="submit"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
                                    </div>

                                    <p class="text-center small">Belum punya akun? <a href="{{ url('/register') }}">Daftar sekarang</a></p>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Show/Hide Password Script -->
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("yourPassword");
        const eyeIcon = document.getElementById("eyeIcon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("bi-eye-fill");
            eyeIcon.classList.add("bi-eye-slash-fill");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("bi-eye-slash-fill");
            eyeIcon.classList.add("bi-eye-fill");
        }
    }
</script>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>

@include('template.footer')

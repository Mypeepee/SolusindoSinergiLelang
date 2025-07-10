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
        padding: 5rem 1rem 3rem; /* Jarak atas 5rem, bawah 3rem */
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 1rem;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        width: 400px;
        max-width: 90%;
        padding: 2rem;
        color: #333;
        margin-top: 2rem;
    }

    .login-card h4 {
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
        background-color: #ff8c00;
        border: none;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background-color: #e67e00;
    }

    .text-muted, .small a {
        color: #666;
    }

    .login-card a {
        color: #ff8c00; /* Warna orange untuk link Daftar Sekarang */
        text-decoration: none;
        font-weight: 500;
        transition: 0.3s;
    }

    .login-card a:hover {
        color: #e67e00; /* Lebih gelap saat hover */
        text-decoration: underline;
    }
</style>

<div class="page-wrapper">
    <main>
        <div class="login-card">
            <h4>🔐 Masuk ke Akun Anda</h4>

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
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Enter your username" class="form-control" id="yourUsername" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="yourPassword" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" id="yourPassword" required>
                        <span class="input-group-text toggle-password" onclick="togglePasswordVisibility()">
                            <i class="bi bi-eye-slash-fill" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login now
                    </button>
                </div>

                <p class="text-center small">
                    Belum Punya Akun? <a href="{{ url('/register') }}">Daftar Sekarang</a>
                </p>
                <p class="text-center small">
                    Lupa Kata Sandi? <a href="{{ route('forgot.password') }}">Pulihkan Sekarang</a>
                </p>
            </form>
        </div>
    </main>

    @include('template.footer')
</div>

<!-- Show/Hide Password Script -->
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("yourPassword");
        const eyeIcon = document.getElementById("eyeIcon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("bi-eye-slash-fill");
            eyeIcon.classList.add("bi-eye-fill");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("bi-eye-fill");
            eyeIcon.classList.add("bi-eye-slash-fill");
        }
    }
</script>

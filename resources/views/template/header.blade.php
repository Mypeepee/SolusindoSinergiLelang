<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Solusindo Sinergi Lelang</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('css/style.css')}}" rel="stylesheet">

    <style>
        /* Hilangkan panah kecil Bootstrap */
        .navbar .dropdown-toggle::after {
            content: none;
        }

        /* Panah rotate saat hover */
        .navbar .dropdown-toggle .fa-chevron-down {
            transition: transform 0.3s ease;
        }

        .navbar .dropdown:hover .fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Header tetap di atas saat scroll */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1050;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        body {
            padding-top: 80px; /* Supaya konten tidak ketumpuk navbar */
        }

        /* Perbaiki dropdown agar tidak geser */
        .dropdown-menu {
            left: 0;
            right: auto;
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        /* Icon spacing seragam */
        .icon-actions {
            display: flex;
            align-items: center;
            gap: 16px; /* jarak antar icon seragam */
            margin-left: 12px; /* jarak dari user dropdown */
            margin-right: 16px; /* jarak ke Add Property */
        }

        .icon-actions a {
            color: #0d2f57; /* warna default icon */
            font-size: 1.2rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .icon-actions a:hover {
            color: #fd6e14; /* warna hover orange */
        }
    </style>
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Navbar Start -->
        <div id="mainNavbar" class="container-fluid nav-bar bg-transparent">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4">
                <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center text-center">
                    <div class="icon p-2 me-2">
                        <img class="img-fluid" src="{{ asset('img/Logo.png') }}" alt="Icon" style="width: 30px; height: 30px;">
                    </div>
                    <h3 class="m-0 text-primary">Sinergi Solusindo Lelang</h3>
                </a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto">
                        @if (Session::has('id_account') || Cookie::has('id_account'))
                        @if (
                            Session::get('role') === 'Agent' ||
                            Session::get('role') === 'Register' ||
                            Session::get('role') === 'Pengosongan' ||
                            Session::get('role') === 'Owner' ||
                            Cookie::get('role') === 'Agent' ||
                            Cookie::get('role') === 'Register'
                        )
                            <a href="{{ route('dashboard.agent') }}" class="nav-item nav-link">Dashboard</a>
                            @endif
                        @endif
                        <a href="{{ url('/') }}" class="nav-item nav-link active">Home</a>
                        <a href="{{ url('/about') }}" class="nav-item nav-link">Tentang Kami</a>

                        <!-- Property Dropdown -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                                Property <i class="fas fa-chevron-down ms-1"></i>
                            </a>
                            <div class="dropdown-menu rounded-0 m-0">
                                <a href="{{ url('/property-list') }}" class="dropdown-item">Property List</a>
                                <a href="{{ url('/property-agent') }}" class="dropdown-item">Property Agent</a>
                            </div>
                        </div>

                        <!-- User Dropdown -->
                        @if (Session::has('id_account') || isset($_COOKIE['id_account']))
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown d-flex align-items-center">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user" style="font-size: 1rem;"></i>
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile', ['id_account' => Session::get('id_account') ?? $_COOKIE['id_account'] ?? '']) }}">
                                            <i class="fa fa-user me-2"></i> Profile
                                        </a>
                                    </li>
                                    @if (
                                        Session::get('role') === 'Agent' ||
                                        Session::get('role') === 'Register' ||
                                        Cookie::get('role') === 'Agent' ||
                                        Cookie::get('role') === 'Register'
                                    )
                                        <li>
                                            <a class="dropdown-item" href="{{ route('agent.properties') }}">
                                                <i class="fa fa-home me-2"></i> Daftar Listingan Saya
                                            </a>
                                        </li>
                                    @endif
                                    @if (
                                        Session::get('role') === 'User' || Cookie::get('role') === 'User'
                                    )
                                        <li>
                                            <a class="dropdown-item" href="{{ route('cart.view') }}">
                                                <i class="fa fa-shopping-cart me-2"></i> Status Lelang Saya
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        @else
                        <!-- Guest User Dropdown -->
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user" style="font-size: 1rem;"></i>
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ url('/login') }}">Login</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url('/register') }}">Register</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        @endif
                    </div>
                </div>

                <!-- Icon Notif & Settings -->
                <div class="icon-actions">
                    @if (
                        Session::get('role') === 'Agent' ||
                        Session::get('role') === 'Register' ||
                        Session::get('role') === 'Pengosongan' ||
                        Session::get('role') === 'Owner' ||
                        Cookie::get('role') === 'Agent' ||
                        Cookie::get('role') === 'Register'
                    )
                    <a href="#">
                        <i class="fas fa-bell"></i>
                    </a>
                    @endif
                    <a href="#">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>

                <!-- Buttons -->
                @if (Session::has('id_account') || Cookie::has('id_account'))
                    @if (
                        Session::get('role') === 'Agent' ||
                        Session::get('role') === 'Register' ||
                        Cookie::get('role') === 'Agent' ||
                        Cookie::get('role') === 'Register'
                    )
                        <a href="{{ route('property.create') }}" class="btn btn-add-property">Add Property</a>
                    @elseif (
                        Session::get('role') === 'User' || Cookie::get('role') === 'User' ||
                        Session::get('role') === 'Pending' || Cookie::get('role') === 'Pending'
                    )
                        <a href="{{ url('/join-agent') }}" class="btn btn-add-property">Bergabung Jadi Agent</a>
                    @endif
                @endif

            </nav>
        </div>
        <!-- Navbar End -->

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
        <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
        <script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
        <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
        <script src="{{ asset('js/main.js')}}"></script>
    </div>
</body>
</html>

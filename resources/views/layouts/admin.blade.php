<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">

    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/pe-icon-7-stroke.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/weather-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admincss/assets/css/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">

    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Aplikasi Stemming Bahasa Indonesia')</title>

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #EFE4D2;
                color: #131D4F;
            }

            .navbar {
                background-color: #254D70;
            }

            .navbar .nav-link {
                color: #EFE4D2;
            }

            .navbar .nav-link:hover,
            .navbar .nav-link.active {
                color: #954C2E;
                font-weight: 600;
            }

            .card {
                border: none;
                border-radius: 1rem;
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
                background-color: #fff;
            }

            .btn-primary {
                background-color: #131D4F;
                border: none;
            }

            .btn-primary:hover {
                background-color: #254D70;
            }

            .btn-outline-secondary {
                color: #954C2E;
                border-color: #954C2E;
            }

            .btn-outline-secondary:hover {
                background-color: #954C2E;
                color: #fff;
            }

            .form-label {
                color: #254D70;
                font-weight: 600;
            }
        </style>
    </head>
    <body class="d-flex flex-column min-vh-100">

        @include('layouts.navbar')

        <main class="flex-grow-1 py-5">
            @yield('content')
        </main>

        @include('layouts.footer')

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Nyam CRM')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- AdminLTE & FontAwesome --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">

    <style>
        .brand-link {
            background-color: #f89f24;
            color: white;
        }

        .main-sidebar {
            background-color: #f89f24 !important;
        }

        .nav-sidebar .nav-link {
            color: white;
        }

        .nav-sidebar .nav-link.active {
            background-color: #1e40af;
            color: white;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .nav-link p {
            display: none;
            /* sembunyikan label teks saat collapse */
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .nav-icon {
            margin: 0 auto;
            /* icon center */
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        @include('components.header')
        @include('components.sidebar')

        <div class="content-wrapper">
            <section class="content pt-4 px-3">
                @yield('content')
            </section>
        </div>

    </div>

        <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>

</html>

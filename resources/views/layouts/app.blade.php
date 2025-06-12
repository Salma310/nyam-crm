<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Nyam CRM')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- AdminLTE & FontAwesome --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        /* Tambahkan style custom seperti di pertanyaan */
        .custom-sidebar {
            background-color: #2159d2;
            color: #e5e7eb;
            font-family: 'Poppins', sans-serif;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
        }

        .brand-link {
            background-color: #2b62da;
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            padding: 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .brand-image {
            margin-bottom: 5px;
            border-radius: 8px;
        }

        .nav-header {
            color: #9ca3af;
            font-weight: 600;
            font-size: 12px;
            padding: 12px 15px 5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .nav-sidebar .nav-link {
            color: #fff;
            transition: background-color 0.2s ease-in-out;
        }

        .nav-sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.341);
            color: #fff !important;
        }

        .nav-sidebar .nav-link.active {
            background: linear-gradient(to right, #ffbc51, #ffc107);
            color: #fff;
            font-weight: 600;
        }

        .nav-sidebar .nav-icon {
            color: #fff;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .nav-link p {
            display: none;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .nav-icon {
            margin: 0 auto;
        }

        .collapsible-header {
            cursor: pointer;
            user-select: none;
            padding: 10px 15px;
            font-weight: bold;
            background-color: rgba(255, 255, 255, 0.1);
            transition: background-color 0.3s;
        }

        .collapsible-header:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff !important;
        }

        .collapsible-content {
            transition: max-height 0.3s ease;
            overflow: hidden;
        }

        .collapsible-content.hidden {
            display: none;
        }

        .btn-group .btn {
            margin-right: 5px;
        }
    </style>

    @stack('css')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        @include('components.header')
        @include('components.sidebar')

        <div class="content-wrapper" style="padding-top: 60px;">
            <section class="content pt-4 px-3">
                @yield('content')
            </section>
        </div>

        <footer class="main-footer">
            <strong>&copy; 2025 <a href="#">Nyam Baby Food CRM</a>.</strong> All rights reserved.
        </footer>

    </div>

    @include('components.password-modal')

    {{-- Scripts --}}
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Logout Logic
        document.getElementById('logout-btn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin logout?',
                imageUrl: "{{ asset('logo.png') }}",
                imageWidth: 80,
                imageAlt: 'Logo Nyam!',
                showCancelButton: true,
                confirmButtonColor: '#e67e22',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('logout') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json",
                            "Content-Type": "application/json"
                        }
                    }).then(response => {
                        if (response.ok) {
                            Swal.fire({
                                title: 'Sampai jumpa!',
                                text: 'Logout berhasil dilakukan',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = "{{ route('login') }}";
                            });
                        } else {
                            Swal.fire('Oops!', 'Terjadi kesalahan saat logout.', 'error');
                        }
                    }).catch(error => {
                        Swal.fire('Error!', 'Gagal melakukan logout.', 'error');
                        console.error("Logout error:", error);
                    });
                }
            });
        });

        // Password Change Submit
        $('#ubahPasswordForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "{{ route('ubah-password') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#ubahPasswordForm')[0].reset();
                    $('#modalUbahPassword').modal('hide');
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMessage
                    });
                }
            });
        });

        // Collapsible Sidebar Header
        document.addEventListener('DOMContentLoaded', function() {
            const headers = document.querySelectorAll('.collapsible-header');
            headers.forEach(header => {
                header.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const target = document.querySelector(targetId);
                    if (target) target.classList.toggle('hidden');
                });
            });
        });
    </script>

    @stack('js')
</body>

</html>

<nav class="main-header navbar navbar-expand navbar-light fixed-top shadow"
    style="background: linear-gradient(to right, #ffbc51, #ffc107); z-index: 1030;">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars text-white"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-toggle="dropdown" aria-expanded="false"
                    style="padding: 6px 10px; border-radius: 8px; background-color: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    <i class="fas fa-user-circle text-dark mr-2"></i>
                    <span class="text-dark font-weight-bold">{{ Auth::user()->name ?? 'Pengguna' }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="min-width: 180px;">
                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#modalUbahPassword">
                        <i class="fas fa-key mr-2 text-primary"></i> Ubah Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" id="logout-btn" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

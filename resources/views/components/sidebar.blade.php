<aside class="main-sidebar elevation-3">
    <a href="#" class="brand-link text-center">
        <img src="{{ asset('logo.png') }}" alt="Nyam Logo" style="width: 60px;">
    </a>

    <div class="sidebar">
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/transaksi') }}"
                        class="nav-link {{ request()->is('transaksi*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Transaksi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/barang') }}" class="nav-link {{ request()->is('barang*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Stok Produk</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/agen') }}" class="nav-link {{ request()->is('agen*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Agen</p>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="{{ url('/purchase') }}" class="nav-link {{ request()->is('purchase*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>Pembelian</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/history') }}"
                        class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>History</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

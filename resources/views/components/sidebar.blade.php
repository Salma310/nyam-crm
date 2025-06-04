<aside class="main-sidebar custom-sidebar">
    <a href="#" class="brand-link text-center">
        <img src="{{ asset('logo.png') }}" alt="Nyam Logo" class="brand-image" style="width: 60px;">
        <span class="brand-text">NYAM CRM</span>
    </a>

    <div class="sidebar">
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-header">Menu Utama</li>
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">Transaksi</li>
                <li class="nav-item">
                    <a href="{{ url('/transaksi') }}"
                        class="nav-link {{ request()->is('transaksi*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Penjualan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/purchase') }}" class="nav-link {{ request()->is('purchase*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-truck-loading"></i>
                        <p>Pembelian</p>
                    </a>
                </li>

                <li class="nav-header">Master Data</li>
                <li class="nav-item">
                    <a href="{{ url('/barang') }}" class="nav-link {{ request()->is('barang*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Produk / Stok</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/agen') }}" class="nav-link {{ request()->is('agen*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Agen</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

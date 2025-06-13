<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nyam CRM - Landing Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Fredoka', sans-serif;
            background: #fffaf4;
            color: #333;
            overflow-x: hidden;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            width: 120px;
        }

        .main-nav {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex: 1;
        }

        .main-nav a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .main-nav a:hover {
            color: #ff725e;
        }

        .btn-login {
            background: #ff725e;
            color: white;
            padding: .6rem 1.4rem;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #e65b47;
        }

        .hero {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #ffffff, #ff5c5c);
        }

        .hero-content {
            flex: 1;
            max-width: 500px;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 2.8rem;
            color: #ff5c5c;
            margin-bottom: 1rem;
        }

        .hero-content p {
            font-size: 1.1rem;
            margin-bottom: 1.8rem;
            line-height: 1.5;
        }

        .cta a {
            background: #ff725e;
            color: white;
            padding: .8rem 2rem;
            border-radius: 12px;
            font-weight: bold;
            text-decoration: none;
            transition: .3s;
        }

        .cta a:hover {
            background: #e65b47;
        }

        .hero-image {
            flex: 1;
            text-align: center;
        }

        .hero-image img {
            width: 100%;
            max-width: 1000px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .1);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        section {
            padding: 4rem 2rem;
            position: relative;
        }

        section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, #fffaf4, #fffbf7);
            z-index: -1;
        }

        h2.section-title {
            font-size: 2.2rem;
            color: #ff5c5c;
            text-align: center;
            margin-bottom: 3rem;
        }

        .about p {
            text-align: center;
            max-width: 800px;
            margin: auto;
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
        }

        /* Carousel Produk Smooth */
        .product-carousel {
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        .product-track {
            display: flex;
            width: calc(240px * 12);
            animation: scroll 30s linear infinite;
        }

        @keyframes scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .product-card {
            background: white;
            border-radius: 24px;
            width: 240px;
            margin: 0 1rem;
            padding: 1.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 1rem;
        }

        .product-card h3 {
            color: #ff725e;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .product-card p {
            font-size: .95rem;
            color: #666;
        }

        .team {
            background: linear-gradient(135deg, #ff725e, #fffbf0);
        }

        .team-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
        }

        .team-member {
            background: white;
            padding: 1.2rem;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 160px;
        }

        .team-member img {
            width: 100%;
            border-radius: 50%;
            margin-bottom: 1rem;
        }

        .team-member p {
            font-size: 0.9rem;
            color: #333;
        }

        footer {
            text-align: center;
            padding: 2rem;
            background: #fff0e5;
            font-size: .9rem;
            color: #777;
        }
    </style>
</head>

<body>

    {{-- <header>
        <img src="{{ asset('logo.png') }}" alt="Nyam Logo" class="logo">
        <nav class="main-nav">
            <a href="#home">Home</a>
            <a href="#about">Tentang</a>
            <a href="#product">Produk</a>
            <a href="#team">Tim</a>
        </nav>
        <a href="{{ route('login') }}" class="btn-login">Masuk</a>
    </header>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Nyam CRM untuk Agen & Cemilan Sehat</h1>
            <p>Kelola agen, stok, transaksi, dan laporan secara mudah dan ceria. Ideal untuk pelaku UMKM cemilan sehat.
            </p>
            <div class="cta"><a href="{{ route('login') }}">Mulai Sekarang!</a></div>
        </div>
        <div class="hero-image"><img src="{{ asset('img/nyambaby.jpg') }}" alt="Nyam Product"></div>
    </section>

    <section class="about" id="about">
        <h2 class="section-title">Tentang Nyam CRM</h2>
        <p>Nyam CRM adalah sistem manajemen bisnis UMKM yang membantu mengelola transaksi penjualan, pembelian, stok
            produk, dan data agen secara otomatis.</p>
        <p>Dengan fitur dashboard interaktif, pemilik bisnis dapat memantau pendapatan, produk terlaris, serta aktivitas
            agen secara real-time. Sistem CRM dapat menampilkan pola pembelian agen, memberikan notifikasi
            pengingat, dan membantu memaksimalkan potensi penjualan secara berkelanjutan.</p>
        <p>Semua fitur seperti: <strong>Transaksi Penjualan, Pembelian, Produk/Stok, Data Agen, dan Analisis
                Dashboard</strong> sudah terintegrasi dalam satu sistem yang user-friendly dan efisien.</p>
    </section> --}}
    <header>
        <img src="{{ asset('logo.png') }}" alt="Nyam Logo" class="logo">
        <nav class="main-nav">
            <a href="#home">Home</a>
            <a href="#about">Tentang</a>
            <a href="#visi">Visi & Misi</a>
            <a href="#product">Produk</a>
            <a href="#team">Tim</a>
        </nav>
        <a href="{{ route('login') }}" class="btn-login">Masuk</a>
    </header>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Nyam CRM untuk Agen & Cemilan Sehat</h1>
            <p>Satu platform praktis untuk mengelola bisnis agen cemilan sehat, mulai dari transaksi hingga analisis
                penjualan.</p>
            <div class="cta"><a href="{{ route('login') }}">Mulai Sekarang!</a></div>
        </div>
        <div class="hero-image"><img src="{{ asset('img/nyambaby.jpg') }}" alt="Nyam Product"></div>
    </section>

    <section class="about" id="about">
        <h2 class="section-title">Tentang Nyam</h2>
        <p>Nyam CRM adalah sistem manajemen bisnis UMKM yang membantu mengelola transaksi penjualan, pembelian, stok
            produk, dan data agen secara otomatis.</p>
        <p>Dengan fitur dashboard interaktif, pemilik bisnis dapat memantau pendapatan, produk terlaris, serta aktivitas
            agen secara real-time. Sistem CRM dapat menampilkan pola pembelian agen, memberikan notifikasi
            pengingat, dan membantu memaksimalkan potensi penjualan secara berkelanjutan.</p>
        <p>Semua fitur seperti: <strong>Transaksi Penjualan, Pembelian, Produk/Stok, Data Agen, dan Analisis
                Dashboard</strong> sudah terintegrasi dalam satu sistem yang user-friendly dan efisien.</p>
    </section>



    {{-- <section class="visi" id="visi"
        style="background: linear-gradient(135deg, #fff0f5, #ffe5ec); padding: 4rem 2rem;">
        <h2 class="section-title" style="color: #ff5c5c; margin-bottom: 3rem;">Visi & Misi</h2>
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem;">
            <div
                style="
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            padding: 2rem; 
            border-radius: 20px; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.15); 
            width: 300px;
            transition: transform 0.3s ease;
            ">
                <h3 style="color: #ff5c5c; text-align: center; margin-bottom: 1rem;">Visi</h3>
                <p style="text-align: center;">Menjadi penyedia cemilan sehat pilihan utama bagi anak dan keluarga.</p>
            </div>

            <div
                style="
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            padding: 2rem; 
            border-radius: 20px; 
            box-shadow: 0 8px 24px rgba(0,0,0,0.15); 
            width: 300px;
            transition: transform 0.3s ease;
            ">
                <h3 style="color: #ff5c5c; text-align: center; margin-bottom: 1rem;">Misi</h3>
                <p style="text-align: center;">Menghadirkan produk sehat berkualitas, memperluas jaringan agen UMKM,
                    serta membantu pertumbuhan ekonomi lokal melalui sistem CRM modern.</p>
            </div>
        </div>
    </section> --}}

    <section class="visi" id="visi"
        style="background: linear-gradient(135deg, #fff0f5, #ffe5ec); padding: 4rem 2rem;">
        <h2 class="section-title" style="color: #ff5c5c; margin-bottom: 3rem;">Visi & Misi</h2>
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem;">
            <!-- Visi Card -->
            <div style="
            background: linear-gradient(145deg, #ffeaea, #fff6f6);
            padding: 2.5rem; 
            border-radius: 30px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        "
                onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.2)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)'">
                {{-- <img src="{{ asset('img/visi.png') }}" alt="Visi Icon" style="width: 60px; margin-bottom: 1rem;"> --}}
                <h3 style="color: #ff5c5c; font-weight: 700; margin-bottom: 1rem;">Visi</h3>
                <p style="font-size: 1rem; color: #333;">Menjadi penyedia cemilan sehat pilihan utama bagi anak dan
                    keluarga.</p>
            </div>

            <!-- Misi Card -->
            <div style="
            background: linear-gradient(145deg, #ffeaea, #fff6f6);
            padding: 2.5rem; 
            border-radius: 30px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        "
                onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.2)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)'">
                {{-- <img src="{{ asset('img/misi.png') }}" alt="Misi Icon" style="width: 60px; margin-bottom: 1rem;"> --}}
                <h3 style="color: #ff5c5c; font-weight: 700; margin-bottom: 1rem;">Misi</h3>
                <p style="font-size: 1rem; color: #333;">Menghadirkan produk sehat berkualitas, memperluas jaringan agen
                    UMKM, serta membantu pertumbuhan ekonomi lokal melalui sistem CRM modern.</p>
            </div>
        </div>
    </section>


    <section class="products" id="product">
        <h2 class="section-title">Produk Cemilan Sehat</h2>
        <div class="product-carousel">
            <div class="product-track">
                <!-- Produk diulang 2x agar looping -->
                @for ($i = 0; $i < 2; $i++)
                    <div class="product-card"><img src="{{ asset('img/cikenpuding.jpg') }}" alt="Chicken Chips">
                        <h3>Chicken Pudding</h3>
                        <p>Pudding lembut yang terbuat dari daging ayam pilihan. Kalori 142kkal/50gr.</p>
                    </div>
                    <div class="product-card"><img src="{{ asset('img/beefpuding.jpg') }}" alt="Chicken Crunchy">
                        <h3>Beef Pudding</h3>
                        <p>Pudding lembut yang terbuat dari daging sapi asli. Kalori 142kkal/50gr.</p>
                    </div>
                    <div class="product-card"><img src="{{ asset('img/panna.jpg') }}" alt="Panna Cotta">
                        <h3>Pannababy</h3>
                        <p>Snack creamy manis sehat ala Italia. Kalori 120kkal/80gr.</p>
                    </div>
                    <div class="product-card"><img src="{{ asset('img/ice.jpg') }}" alt="Ice Cream">
                        <h3>Ice Cream</h3>
                        <p>Es krim sehat dengan berbagai protein hewani dan nabati. Kalori 200kkal/50gr, tanpa pengawet.
                        </p>
                    </div>
                    <div class="product-card"><img src="{{ asset('img/abon.jpeg') }}" alt="Abon">
                        <h3>Abon</h3>
                        <p>Abon protein tinggi bebas MSG. Kalori 142kkal/20gr, tanpa pengawet.</p>
                    </div>
                    <div class="product-card"><img src="{{ asset('img/cikibone.jpg') }}" alt="Bone Broth">
                        <h3>Bone Broth</h3>
                        <p>Kaldu tulang sehat untuk nutrisi optimal. Kalori 47kkal/20gr, tanpa pengawet.</p>
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <section class="team" id="team"
        style="background: linear-gradient(135deg, #ff725e, #fffbf0); padding: 4rem 2rem;">
        <h2 class="section-title" style="color: #ff5c5c; margin-bottom: 3rem;">Kelompok 4 – Anggota</h2>
        <div class="team-grid" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem;">
            <!-- Card Anggota -->
            <div class="team-member"
                style="
            background: linear-gradient(145deg, #fff, #ffecec);
            padding: 2rem;
            border-radius: 24px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 200px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        "
                onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.2)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px rgba(0,0,0,0.1)'">
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('img/ian.png') }}" alt="Fahmi"
                        style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                </div>
                <p style="font-weight: 600;">Fahmi Mardiansyah<br><span style="font-weight: 400;">2241760064</span></p>
            </div>

            <!-- Copy paste card lainnya -->
            <div class="team-member" style="...">
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('img/risa.jpg') }}" alt="Maritza"
                        style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                </div>
                <p style="font-weight: 600;">Maritza Ulfa Huriyah<br><span style="font-weight: 400;">2241760119</span>
                </p>
            </div>

            <div class="team-member" style="...">
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('img/salma.jpg') }}" alt="Nasywa"
                        style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                </div>
                <p style="font-weight: 600;">Nasywa Salma<br><span style="font-weight: 400;">2241760140</span></p>
            </div>

            <div class="team-member" style="...">
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('img/naswa.jpeg') }}" alt="Nasywa"
                        style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                </div>
                <p style="font-weight: 600;">Nasywa Syafinka<br><span style="font-weight: 400;">2241760002</span></p>
            </div>

            <div class="team-member" style="...">
                <div style="margin-bottom: 1rem;">
                    <img src="{{ asset('img/syffa.jpg') }}" alt="Syffa"
                        style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                </div>
                <p style="font-weight: 600;">Syffa Firdausyah<br><span style="font-weight: 400;">2241760005</span></p>
            </div>
        </div>
    </section>


    <footer>
        &copy; {{ date('Y') }} Nyam Baby CRM
    </footer>

</body>

</html>

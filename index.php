<?php
require_once 'config/config.php';
// If user is already logged in, they can go to dashboard directly via the "Masuk" button.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leka Caffee - Solusi Kasir Modern Anda</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-body: #FDFBF7;
            --bg-surface: #FFFFFF;
            --primary: #9C7A63;
            --primary-light: #B89C88;
            --primary-dark: #7A5C4A;
            --accent: #F4C430;
            --accent-hover: #DCAE26;
            --text-heading: #2D2622;
            --text-body: #5A524D;
            --text-muted: #8C827A;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.8);
            --radius-xl: 24px;
        }

        body {
            background-color: var(--bg-body);
            background-image: radial-gradient(circle at top right, rgba(156, 122, 99, 0.05) 0%, transparent 40%),
                              radial-gradient(circle at bottom left, rgba(244, 196, 48, 0.05) 0%, transparent 40%);
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-body);
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: rgba(45, 38, 34, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-sizing: border-box;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: #FFFFFF;
            font-size: 22px;
            font-weight: 800;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            color: var(--accent);
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        .navbar-menu a {
            color: #D1C7C0;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
        }

        .navbar-menu a:hover:not(.btn-masuk) {
            color: var(--accent);
        }

        .btn-masuk {
            background: var(--accent);
            color: #2D2622 !important;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 700 !important;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(244, 196, 48, 0.2);
        }

        .btn-masuk:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 196, 48, 0.4);
        }

        /* Hero */
        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 150px 5% 80px;
            max-width: 1400px;
            margin: 0 auto;
            gap: 60px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .hero-text {
            flex: 1;
            animation: slideUp 0.8s ease forwards;
        }

        .badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(156, 122, 99, 0.1);
            color: var(--primary);
            border-radius: 30px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 56px;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1.15;
            margin-bottom: 24px;
            letter-spacing: -1.5px;
        }

        .hero-title span {
            color: var(--primary);
            position: relative;
        }

        .hero-subtitle {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 90%;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .btn-primary-l {
            background: var(--accent);
            color: #2D2622;
            padding: 16px 36px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(244, 196, 48, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary-l:hover {
            background: var(--accent-hover);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(244, 196, 48, 0.4);
        }

        .btn-secondary-l {
            background: transparent;
            color: var(--text-heading);
            padding: 14px 34px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid var(--text-heading);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-secondary-l:hover {
            background: rgba(45, 38, 34, 0.05);
            transform: translateY(-3px);
        }

        .hero-image {
            flex: 1;
            position: relative;
            animation: fadeIn 1s ease forwards;
            animation-delay: 0.3s;
            opacity: 0;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px rgba(156, 122, 99, 0.2);
            object-fit: cover;
            aspect-ratio: 4/3;
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.5s ease;
        }

        .hero-image:hover img {
            transform: perspective(1000px) rotateY(0deg) translateY(-10px);
        }

        .floating-card {
            position: absolute;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            animation: float 6s ease-in-out infinite;
        }

        .floating-card.top {
            top: 20px;
            right: -30px;
            animation-delay: 0s;
        }

        .floating-card.bottom {
            bottom: 40px;
            left: -40px;
            animation-delay: 2s;
        }

        .floating-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .floating-text h4 {
            margin: 0 0 5px 0;
            color: var(--text-heading);
            font-size: 16px;
        }
        
        .floating-text p {
            margin: 0;
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Features Section */
        .features {
            padding: 100px 5%;
            background: #FFFFFF;
            position: relative;
        }

        .section-header {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 60px;
        }

        .section-header h2 {
            font-size: 38px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 15px;
            letter-spacing: -1px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--bg-body);
            border-radius: var(--radius-xl);
            padding: 40px;
            text-align: left;
            transition: all 0.4s ease;
            border: 1px solid rgba(156, 122, 99, 0.1);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.2));
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(156, 122, 99, 0.1);
            border-color: rgba(156, 122, 99, 0.3);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 25px;
        }

        .feature-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-heading);
            margin-bottom: 15px;
        }

        .feature-card p {
            line-height: 1.7;
            margin: 0;
            font-size: 15px;
        }

        /* Metrics */
        .metrics {
            padding: 60px 5%;
            background: var(--primary-dark);
            color: white;
            display: flex;
            justify-content: center;
            gap: 100px;
            flex-wrap: wrap;
        }

        .metric-item {
            text-align: center;
        }

        .metric-value {
            font-size: 48px;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 10px;
        }
        
        .metric-label {
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }

        /* Benefit Section */
        .benefit-section {
            padding: 100px 5%;
            background: #FDFBF7;
        }

        .benefit-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .benefit-content {
            flex: 1;
        }

        .benefit-content h2 {
            font-size: 38px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 25px;
            letter-spacing: -1px;
            line-height: 1.25;
        }

        .benefit-content p {
            font-size: 17px;
            color: var(--text-body);
            line-height: 1.7;
            margin-bottom: 40px;
        }

        .benefit-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .benefit-list li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .benefit-list-icon {
            color: #10b981;
            background: rgba(16, 185, 129, 0.15);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .benefit-list-text h4 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-heading);
            margin: 0 0 5px 0;
        }

        .benefit-list-text p {
            font-size: 15px;
            margin: 0;
            line-height: 1.6;
            color: var(--text-muted);
        }

        .benefit-image {
            flex: 1;
            position: relative;
        }

        .benefit-image img {
            width: 100%;
            height: auto;
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 50px rgba(156, 122, 99, 0.15);
        }

        @media (max-width: 992px) {
            .benefit-container {
                flex-direction: column-reverse;
                text-align: center;
            }
            .benefit-list li {
                text-align: left;
            }
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 5%;
            text-align: center;
            background: var(--bg-body);
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(156, 122, 99, 0.1), rgba(244, 196, 48, 0.1));
            border-radius: 30px;
            padding: 80px 40px;
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(156, 122, 99, 0.2);
        }

        .cta-box h2 {
            font-size: 40px;
            font-weight: 800;
            color: var(--text-heading);
            margin-bottom: 20px;
        }

        .cta-box p {
            font-size: 18px;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        footer {
            background: #2D2622;
            color: #D1C7C0;
            padding: 60px 5% 30px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            max-width: 1400px;
            margin: 0 auto 40px;
            flex-wrap: wrap;
            gap: 40px;
        }

        .footer-brand h3 {
            color: white;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .footer-links {
            display: flex;
            gap: 80px;
        }

        .link-group h4 {
            color: white;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .link-group a {
            display: block;
            color: #8C827A;
            text-decoration: none;
            margin-bottom: 12px;
            transition: color 0.3s;
        }

        .link-group a:hover {
            color: var(--accent);
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            font-size: 14px;
        }

        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding-top: 130px;
            }
            .hero-subtitle {
                margin: 0 auto 40px;
            }
            .hero-buttons {
                justify-content: center;
            }
            .floating-card {
                display: none; /* Hide on mobile to save space */
            }
            .metrics {
                gap: 50px;
            }
            .footer-links {
                gap: 40px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 40px; }
            .cta-box h2 { font-size: 30px; }
            .navbar-menu { display: none; } /* Could add hamburger menu here */
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">
            <i class="fa-solid fa-mug-hot"></i> Leka Caffee
        </a>
        <div class="navbar-menu">
            <a href="#fitur">Fitur Unggulan</a>
            <a href="#benefit">Keuntungan</a>
            <a href="logout.php" class="btn-masuk">Log In <i class="fa-solid fa-arrow-right-to-bracket" style="margin-left: 5px;"></i></a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-text">
            <div class="badge">🚀 POS Generasi Baru</div>
            <h1 class="hero-title">Sistem Cerdas untuk <br><span>Bisnis Caffe</span> Anda</h1>
            <p class="hero-subtitle">
                Tinggalkan cara lama. Kelola transaksi, pantau stok bahan baku, hingga ciptakan laporan penjualan dan pembelian secara real-time dengan aplikasi POS antarmuka elegan, dirancang khusus untuk kedai kopi dan bakery.
            </p>
            <div class="hero-buttons">
                <!-- Send to logout then login -->
                <a href="logout.php" class="btn-primary-l">Coba Sekarang <i class="fa-solid fa-arrow-right"></i></a>
                <a href="#fitur" class="btn-secondary-l"><i class="fa-regular fa-circle-play"></i> Pelajari Fitur</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=1447&auto=format&fit=crop" alt="Coffee Shop POS Interface">
            
            <!-- Floating cards simulating UI Elements -->
            <div class="floating-card top">
                <div class="floating-icon" style="background: rgba(16, 185, 129, 0.2); color: #10b981;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="floating-text">
                    <h4>Transaksi Berhasil</h4>
                    <p>Rp 45.000</p>
                </div>
            </div>

            <div class="floating-card bottom">
                <div class="floating-icon" style="background: rgba(244, 196, 48, 0.2); color: #DCAE26;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="floating-text">
                    <h4>Penjualan Hari Ini</h4>
                    <p>+24% vs Kemarin</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Metrics Strip -->
    <section class="metrics">
        <div class="metric-item">
            <div class="metric-value">1rb+</div>
            <div class="metric-label">Transaksi Harian</div>
        </div>
        <div class="metric-item">
            <div class="metric-value">99.9%</div>
            <div class="metric-label">Uptime Sistem</div>
        </div>
        <div class="metric-item">
            <div class="metric-value">24/7</div>
            <div class="metric-label">Sinkronisasi Data</div>
        </div>
    </section>

    <!-- Fitur Unggulan Section -->
    <section class="features" id="fitur">
        <div class="section-header">
            <h2>Fitur Unggulan</h2>
            <p>Leka Caffee kami dirancang dengan seperangkat alat lengkap untuk mengoptimalkan operasional dan meningkatkan keuntungan bisnis Anda.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon-wrapper" style="color: #6366f1; background: rgba(99, 102, 241, 0.1);">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h3>Point of Sale Modern</h3>
                <p>Antarmuka kasir yang dirancang intuitif untuk mempercepat proses pencatatan pesanan. Minimalisir antrean, tingkatkan kepuasan pelanggan.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon-wrapper" style="color: #10b981; background: rgba(16, 185, 129, 0.1);">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
                <h3>Laporan Otomatis</h3>
                <p>Tak perlu lagi rekap manual. Dapatkan wawasan mendalam mengenai tren penjualan, laporan pembelian, dan performa bisnis dalam satu klik.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon-wrapper" style="color: #d97706; background: rgba(217, 119, 6, 0.1);">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3>Manajemen Stok Akurat</h3>
                <p>Kontrol inventaris secara real-time. Sistem terintegrasi dari pembelian supplier ke gudang hingga pengurangan stok otomatis saat penjualan.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon-wrapper" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3>Akses Multi-Role</h3>
                <p>Aman dan terkendali. Batasi akses menu sesuai tupoksi karyawan dengan role: Admin Utama, Kasir (Penjualan), dan Gudang (Pembelian).</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon-wrapper" style="color: #f43f5e; background: rgba(244, 63, 94, 0.1);">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3>Kalkulasi PPN & Diskon</h3>
                <p>Manajemen perpajakan tak lagi memusingkan. Sistem memproses PPN secara otomatis ke dalam harga dan menampilkan cetak struk berstandar.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon-wrapper" style="color: #0ea5e9; background: rgba(14, 165, 233, 0.1);">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <h3>Arus Kas Terpantau</h3>
                <p>Pantau dengan jelas rekam jejak pemasukan dari kasir dan uang keluar untuk supplier untuk memastikan neraca finansial tetap sehat.</p>
            </div>
        </div>
    </section>

    <!-- Keuntungan Section -->
    <section class="benefit-section" id="benefit">
        <div class="benefit-container">
            <div class="benefit-image">
                <!-- Using another realistic cafe image to illustrate success/benefits -->
                <img src="https://images.unsplash.com/photo-1497935586351-b67a49e012bf?q=80&w=1000&auto=format&fit=crop" alt="Cafe Owner smiling and using POS">
            </div>
            <div class="benefit-content">
                <h2>Fokus Pada Pelanggan, <br>Biar Sistem Mengurus Bisnisnya.</h2>
                <p>Mengelola kafe bisa jadi melelahkan tanpa alat yang tepat. Leka Caffee dirancang khusus untuk menangani kerumitan operasional, memberikan Anda ketenangan untuk fokus pada hal yang terpenting: Kualitas kopi dan kepuasan pelanggan.</p>
                <ul class="benefit-list">
                    <li>
                        <div class="benefit-list-icon"><i class="fa-solid fa-check"></i></div>
                        <div class="benefit-list-text">
                            <h4>Eksekusi Lebih Cepat</h4>
                            <p>Proses pemesanan tidak akan membuat antrean panjang. Interaksi kasir yang mulus dari pesan hingga bayar.</p>
                        </div>
                    </li>
                    <li>
                        <div class="benefit-list-icon"><i class="fa-solid fa-check"></i></div>
                        <div class="benefit-list-text">
                            <h4>Data Anti Meleset</h4>
                            <p>Tinggalkan kalkulator dan pembukuan kertas. Keuangan, HPP (Harga Pokok Penjualan), dan keuntungan ditarik presisi dari database.</p>
                        </div>
                    </li>
                    <li>
                        <div class="benefit-list-icon"><i class="fa-solid fa-check"></i></div>
                        <div class="benefit-list-text">
                            <h4>Skalabilitas Nyata</h4>
                            <p>Mudah dikembangkan jika ada penambahan menu, stok, karyawan baru, maupun modifikasi struk sesuai kebutuhan operasional sehari-hari.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-box">
            <h2>Siap Tingkatkan Penjualan Anda?</h2>
            <p>Bergabunglah dengan pengusaha caffe lainnya yang telah menyederhanakan bisnis mereka menggunakan sistem POS pintar kami.</p>
            <a href="logout.php" class="btn-primary-l" style="font-size: 18px; padding: 18px 45px;">Masuk ke Dashboard Sekarang</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <h3><i class="fa-solid fa-mug-hot"></i> POS Caffe</h3>
                <p style="color: #8C827A; max-width: 300px; line-height: 1.6;">
                    Platform digital pintar untuk menyederhanakan operasional harian kafe dan restoran Anda.
                </p>
            </div>
            <div class="footer-links">
                <div class="link-group">
                    <h4>Produk</h4>
                    <a href="#">Fitur</a>
                    <a href="#">Harga</a>
                    <a href="#">Keamanan</a>
                </div>
                <div class="link-group">
                    <h4>Bantuan</h4>
                    <a href="#">Pusat Bantuan</a>
                    <a href="#">Tutorial System</a>
                    <a href="#">Hubungi Kami</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> Leka Caffee. All rights reserved.
        </div>
    </footer>

</body>
</html>

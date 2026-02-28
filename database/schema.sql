-- Database schema untuk Sistem POS caffe
CREATE DATABASE IF NOT EXISTS pos_caffe;
USE pos_caffe;

-- Tabel Users (Admin, Kasir, Gudang)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'kasir', 'gudang') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori menu
CREATE TABLE kategori_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel menu
CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_menu VARCHAR(20) UNIQUE NOT NULL,
    nama_menu VARCHAR(200) NOT NULL,
    kategori_id INT,
    satuan VARCHAR(20) NOT NULL,
    harga_beli DECIMAL(10,2) NOT NULL,
    harga_jual DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    stok_minimum INT DEFAULT 0,
    tanggal_expired DATE,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori_menu(id) ON DELETE SET NULL
);

-- Tabel Supplier
CREATE TABLE supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(200) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pembelian (Masuk dari Gudang)
CREATE TABLE pembelian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_faktur VARCHAR(50) UNIQUE NOT NULL,
    supplier_id INT,
    user_id INT,
    total_harga DECIMAL(12,2) NOT NULL,
    tanggal_pembelian DATE NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel Detail Pembelian
CREATE TABLE detail_pembelian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pembelian_id INT,
    menu_id INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (pembelian_id) REFERENCES pembelian(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);

-- Tabel Penjualan (Kasir)
CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_transaksi VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    total_harga DECIMAL(12,2) NOT NULL,
    total_bayar DECIMAL(12,2) NOT NULL,
    kembalian DECIMAL(12,2) NOT NULL,
    tanggal_penjualan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel Detail Penjualan
CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penjualan_id INT,
    menu_id INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (penjualan_id) REFERENCES penjualan(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);

-- Tabel Laporan Pembelian
CREATE TABLE laporan_pembelian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    periode_mulai DATE NOT NULL,
    periode_selesai DATE NOT NULL,
    total_transaksi INT DEFAULT 0,
    total_pengeluaran DECIMAL(15,2) DEFAULT 0.00,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert data awal
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@caffe.com', 'admin'),
('kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kasir Utama', 'kasir@caffe.com', 'kasir'),
('gudang1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Gudang', 'gudang@caffe.com', 'gudang');

INSERT INTO kategori_menu (nama_kategori, deskripsi) VALUES
('menu Bebas', 'menu yang dapat dibeli tanpa resep dokter'),
('menu Keras', 'menu yang memerlukan resep dokter'),
('menu Herbal', 'menu tradisional berbahan herbal'),
('Alat Kesehatan', 'Peralatan medis dan kesehatan'),
('Vitamin & Suplemen', 'Vitamin dan suplemen kesehatan');

INSERT INTO supplier (nama_supplier, alamat, telepon, email) VALUES
('PT. Farmasi Sehat', 'Jl. Raya Jakarta No. 123', '021-1234567', 'info@farmasehat.com'),
('CV. Medika Jaya', 'Jl. Sudirman No. 456', '021-7654321', 'contact@medikajaya.com');

<?php
require_once 'config/config.php';
requireRole(['admin', 'gudang']);

require_once 'models/Menu.php';
require_once 'models/KategoriMenu.php';

$database = new Database();
$db = $database->getConnection();

$menu = new Menu($db);
$kategori = new KategoriMenu($db);

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $menu->kode_menu = sanitizeInput($_POST['kode_menu']);
                $menu->nama_menu = sanitizeInput($_POST['nama_menu']);
                $menu->kategori_id = sanitizeInput($_POST['kategori_id']);
                $menu->satuan = sanitizeInput($_POST['satuan']);
                $menu->harga_beli = sanitizeInput($_POST['harga_beli']);
                $menu->harga_jual = sanitizeInput($_POST['harga_jual']);
                $menu->stok = sanitizeInput($_POST['stok']);
                $menu->stok_minimum = sanitizeInput($_POST['stok_minimum']);
                $menu->tanggal_expired = sanitizeInput($_POST['tanggal_expired']);
                $menu->deskripsi = sanitizeInput($_POST['deskripsi']);

                if ($menu->create()) {
                    $message = 'Data menu berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan data menu!';
                    $message_type = 'error';
                }
                break;

            case 'update':
                $menu->id = sanitizeInput($_POST['id']);
                $menu->kode_menu = sanitizeInput($_POST['kode_menu']);
                $menu->nama_menu = sanitizeInput($_POST['nama_menu']);
                $menu->kategori_id = sanitizeInput($_POST['kategori_id']);
                $menu->satuan = sanitizeInput($_POST['satuan']);
                $menu->harga_beli = sanitizeInput($_POST['harga_beli']);
                $menu->harga_jual = sanitizeInput($_POST['harga_jual']);
                $menu->stok = sanitizeInput($_POST['stok']);
                $menu->stok_minimum = sanitizeInput($_POST['stok_minimum']);
                $menu->tanggal_expired = sanitizeInput($_POST['tanggal_expired']);
                $menu->deskripsi = sanitizeInput($_POST['deskripsi']);

                if ($menu->update()) {
                    $message = 'Data menu berhasil diperbarui!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal memperbarui data menu!';
                    $message_type = 'error';
                }
                break;

            case 'delete':
                $menu->id = sanitizeInput($_POST['id']);
                if ($menu->delete()) {
                    $message = 'Data menu berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus data menu!';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Get all menu
$stmt = $menu->readAll();

// Get all kategori for dropdown
$kategori_stmt = $kategori->readAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data menu - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fa-solid fa-mug-hot"></i> <?php echo APP_NAME; ?></h2>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fa-solid fa-chart-pie"></i> Dashboard
                    </a>
                </li>
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'kasir'): ?>
                <li class="nav-item">
                    <a href="penjualan.php" class="nav-link">
                        <i class="fa-solid fa-cart-shopping"></i> Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan_penjualan.php" class="nav-link">
                        <i class="fa-solid fa-chart-line"></i> Laporan Penjualan
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'gudang'): ?>
                <li class="nav-item">
                    <a href="menu.php" class="nav-link active">
                        <i class="fa-solid fa-mug-hot"></i> Data menu
                    </a>
                </li>
                <li class="nav-item">
                    <a href="pembelian.php" class="nav-link">
                        <i class="fa-solid fa-box"></i> Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan_pembelian.php" class="nav-link">
                        <i class="fa-solid fa-chart-bar"></i> Laporan Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="stok.php" class="nav-link">
                        <i class="fa-solid fa-clipboard-list"></i> Manajemen Stok
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a href="kategori.php" class="nav-link">
                        <i class="fa-solid fa-tags"></i> Kategori menu
                    </a>
                </li>
                <li class="nav-item">
                    <a href="supplier.php" class="nav-link">
                        <i class="fa-solid fa-building"></i> Supplier
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i class="fa-solid fa-users"></i> Manajemen User
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="top-nav">
                <h1>Data menu</h1>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <!-- Add menu Form -->
                <div class="form-container">
                    <h2>Tambah menu Baru</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kode_menu">Kode menu</label>
                                <input type="text" id="kode_menu" name="kode_menu" required>
                            </div>
                            <div class="form-group">
                                <label for="nama_menu">Nama menu</label>
                                <input type="text" id="nama_menu" name="nama_menu" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="kategori_id">Kategori</label>
                                <select id="kategori_id" name="kategori_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($row = $kategori_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nama_kategori']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" id="satuan" name="satuan" required placeholder="cth: Pcs, Cup, Porsi">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="harga_beli">Harga Beli</label>
                                <input type="number" id="harga_beli" name="harga_beli" required min="0" step="100">
                            </div>
                            <div class="form-group">
                                <label for="harga_jual">Harga Jual</label>
                                <input type="number" id="harga_jual" name="harga_jual" required min="0" step="100">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" id="stok" name="stok" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="stok_minimum">Stok Minimum</label>
                                <input type="number" id="stok_minimum" name="stok_minimum" required min="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tanggal_expired">Tanggal Expired</label>
                                <input type="date" id="tanggal_expired" name="tanggal_expired">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah menu</button>
                    </form>
                </div>

                <!-- Data menu Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">Daftar menu</h3>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama menu</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Stok Min</th>
                                <th>Expired</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['kode_menu']; ?></td>
                                <td><?php echo $row['nama_menu']; ?></td>
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td><?php echo $row['satuan']; ?></td>
                                <td><?php echo formatCurrency($row['harga_beli']); ?></td>
                                <td><?php echo formatCurrency($row['harga_jual']); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['stok'] <= $row['stok_minimum'] ? 'badge-danger' : 'badge-success'; ?>">
                                        <?php echo $row['stok']; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['stok_minimum']; ?></td>
                                <td>
                                    <?php 
                                    if ($row['tanggal_expired']) {
                                        $expired = strtotime($row['tanggal_expired']);
                                        $now = time();
                                        $days_left = ($expired - $now) / (60 * 60 * 24);
                                        
                                        if ($days_left < 0) {
                                            echo '<span class="badge badge-danger">Expired</span>';
                                        } elseif ($days_left <= 30) {
                                            echo '<span class="badge badge-warning">' . date('d/m/Y', $expired) . '</span>';
                                        } else {
                                            echo date('d/m/Y', $expired);
                                        }
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button onclick="editmenu(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                            class="btn btn-warning btn-sm">Edit</button>
                                    <button onclick="deletemenu(<?php echo $row['id']; ?>)" 
                                            class="btn btn-danger btn-sm">Hapus</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.85); backdrop-filter: blur(24px) saturate(130%); border: 1px solid rgba(255,255,255,0.8); box-shadow: 0 30px 60px rgba(0,0,0,0.12); padding: 40px; border-radius: 32px; width: 90%; max-width: 600px; max-height: 90%; overflow-y: auto;">
            <h2>Edit menu</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_kode_menu">Kode menu</label>
                        <input type="text" id="edit_kode_menu" name="kode_menu" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_menu">Nama menu</label>
                        <input type="text" id="edit_nama_menu" name="nama_menu" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_kategori_id">Kategori</label>
                        <select id="edit_kategori_id" name="kategori_id" required>
                            <?php 
                            $kategori_stmt = $kategori->readAll();
                            while ($row = $kategori_stmt->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_satuan">Satuan</label>
                        <input type="text" id="edit_satuan" name="satuan" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_harga_beli">Harga Beli</label>
                        <input type="number" id="edit_harga_beli" name="harga_beli" required min="0" step="100">
                    </div>
                    <div class="form-group">
                        <label for="edit_harga_jual">Harga Jual</label>
                        <input type="number" id="edit_harga_jual" name="harga_jual" required min="0" step="100">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_stok">Stok</label>
                        <input type="number" id="edit_stok" name="stok" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="edit_stok_minimum">Stok Minimum</label>
                        <input type="number" id="edit_stok_minimum" name="stok_minimum" required min="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_tanggal_expired">Tanggal Expired</label>
                        <input type="date" id="edit_tanggal_expired" name="tanggal_expired">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_deskripsi">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editmenu(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_kode_menu').value = data.kode_menu;
            document.getElementById('edit_nama_menu').value = data.nama_menu;
            document.getElementById('edit_kategori_id').value = data.kategori_id;
            document.getElementById('edit_satuan').value = data.satuan;
            document.getElementById('edit_harga_beli').value = data.harga_beli;
            document.getElementById('edit_harga_jual').value = data.harga_jual;
            document.getElementById('edit_stok').value = data.stok;
            document.getElementById('edit_stok_minimum').value = data.stok_minimum;
            document.getElementById('edit_tanggal_expired').value = data.tanggal_expired;
            document.getElementById('edit_deskripsi').value = data.deskripsi;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function deletemenu(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data menu ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        document.getElementById('editModal').onclick = function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>

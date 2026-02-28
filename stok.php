<?php
require_once 'config/config.php';
requireRole(['admin', 'gudang']);

require_once 'models/Menu.php';

$database = new Database();
$db = $database->getConnection();

$menu = new Menu($db);

$message = '';
$message_type = '';

// Handle stock adjustment
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'adjust_stock') {
    $menu_id = sanitizeInput($_POST['menu_id']);
    $adjustment = sanitizeInput($_POST['adjustment']);
    $reason = sanitizeInput($_POST['reason']);
    
    if ($menu->updateStok($menu_id, $adjustment)) {
        // Log stock adjustment (optional)
        $message = 'Stok berhasil disesuaikan!';
        $message_type = 'success';
    } else {
        $message = 'Gagal menyesuaikan stok!';
        $message_type = 'error';
    }
}

// Get filter parameters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Get menu data based on filter
switch ($filter) {
    case 'low_stock':
        $query = "SELECT o.*, k.nama_kategori 
                  FROM menu o
                  LEFT JOIN kategori_menu k ON o.kategori_id = k.id
                  WHERE o.stok <= o.stok_minimum
                  ORDER BY o.nama_menu";
        break;
    case 'expired':
        $query = "SELECT o.*, k.nama_kategori 
                  FROM menu o
                  LEFT JOIN kategori_menu k ON o.kategori_id = k.id
                  WHERE o.tanggal_expired <= CURDATE()
                  ORDER BY o.nama_menu";
        break;
    case 'expiring_soon':
        $query = "SELECT o.*, k.nama_kategori 
                  FROM menu o
                  LEFT JOIN kategori_menu k ON o.kategori_id = k.id
                  WHERE o.tanggal_expired <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                  AND o.tanggal_expired > CURDATE()
                  ORDER BY o.tanggal_expired";
        break;
    default:
        if (!empty($search)) {
            $stmt = $menu->search($search);
        } else {
            $stmt = $menu->readAll();
        }
        break;
}

if (isset($query)) {
    $stmt = $db->prepare($query);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok - <?php echo APP_NAME; ?></title>
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
                    <a href="menu.php" class="nav-link">
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
                    <a href="stok.php" class="nav-link active">
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
                <h1>Manajemen Stok</h1>
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

                <!-- Filter Section -->
                <div class="form-container">
                    <h3>Filter Stok</h3>
                    <form method="GET" style="display: flex; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="filter">Filter:</label>
                            <select id="filter" name="filter" onchange="this.form.submit()">
                                <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Semua menu</option>
                                <option value="low_stock" <?php echo $filter === 'low_stock' ? 'selected' : ''; ?>>Stok Minimum</option>
                                <option value="expired" <?php echo $filter === 'expired' ? 'selected' : ''; ?>>Sudah Expired</option>
                                <option value="expiring_soon" <?php echo $filter === 'expiring_soon' ? 'selected' : ''; ?>>Akan Expired (30 hari)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="search">Cari:</label>
                            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Nama atau kode menu">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="stok.php" class="btn btn-secondary">Reset</a>
                    </form>
                </div>

                <!-- Stock Adjustment Form -->
                <div class="form-container">
                    <h3>Penyesuaian Stok</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="adjust_stock">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="menu_id">Pilih menu</label>
                                <select id="menu_id" name="menu_id" required>
                                    <option value="">Pilih menu</option>
                                    <?php 
                                    $menu_stmt = $menu->readAll();
                                    while ($row = $menu_stmt->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                        <option value="<?php echo $row['id']; ?>">
                                            <?php echo $row['nama_menu'] . ' (Stok: ' . $row['stok'] . ')'; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="adjustment">Penyesuaian</label>
                                <input type="number" id="adjustment" name="adjustment" required 
                                       placeholder="+/- jumlah stok" step="1">
                                <small>Gunakan tanda + untuk menambah, - untuk mengurangi</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="reason">Alasan Penyesuaian</label>
                            <textarea id="reason" name="reason" rows="2" required 
                                      placeholder="Contoh: Koreksi stok, kerusakan barang, dll"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Sesuaikan Stok</button>
                    </form>
                </div>

                <!-- Stock Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">Daftar Stok menu</h3>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama menu</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Stok Min</th>
                                <th>Status</th>
                                <th>Expired</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['kode_menu']; ?></td>
                                <td><?php echo $row['nama_menu']; ?></td>
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td>
                                    <span class="badge <?php echo $row['stok'] <= $row['stok_minimum'] ? 'badge-danger' : 'badge-success'; ?>">
                                        <?php echo $row['stok']; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['stok_minimum']; ?></td>
                                <td>
                                    <?php
                                    if ($row['stok'] <= 0) {
                                        echo '<span class="badge badge-danger">Habis</span>';
                                    } elseif ($row['stok'] <= $row['stok_minimum']) {
                                        echo '<span class="badge badge-warning">Minimal</span>';
                                    } else {
                                        echo '<span class="badge badge-success">Aman</span>';
                                    }
                                    ?>
                                </td>
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
                                <td><?php echo formatCurrency($row['harga_beli']); ?></td>
                                <td><?php echo formatCurrency($row['harga_jual']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

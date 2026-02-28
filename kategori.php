<?php
require_once 'config/config.php';
requireRole(['admin']);

require_once 'models/KategoriMenu.php';

$database = new Database();
$db = $database->getConnection();

$kategori = new KategoriMenu($db);

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $kategori->nama_kategori = sanitizeInput($_POST['nama_kategori']);
                $kategori->deskripsi = sanitizeInput($_POST['deskripsi']);

                if ($kategori->create()) {
                    $message = 'Kategori menu berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan kategori menu!';
                    $message_type = 'error';
                }
                break;

            case 'update':
                $kategori->id = sanitizeInput($_POST['id']);
                $kategori->nama_kategori = sanitizeInput($_POST['nama_kategori']);
                $kategori->deskripsi = sanitizeInput($_POST['deskripsi']);

                if ($kategori->update()) {
                    $message = 'Kategori menu berhasil diperbarui!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal memperbarui kategori menu!';
                    $message_type = 'error';
                }
                break;

            case 'delete':
                $kategori->id = sanitizeInput($_POST['id']);
                if ($kategori->delete()) {
                    $message = 'Kategori menu berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus kategori menu!';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Get all kategori
$stmt = $kategori->readAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori menu - <?php echo APP_NAME; ?></title>
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
                    <a href="stok.php" class="nav-link">
                        <i class="fa-solid fa-clipboard-list"></i> Manajemen Stok
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="kategori.php" class="nav-link active">
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
                <h1>Kategori menu</h1>
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

                <!-- Add Kategori Form -->
                <div class="form-container">
                    <h2>Tambah Kategori Baru</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama_kategori">Nama Kategori</label>
                                <input type="text" id="nama_kategori" name="nama_kategori" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah Kategori</button>
                    </form>
                </div>

                <!-- Data Kategori Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">Daftar Kategori menu</h3>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Terakhir Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td><?php echo $row['deskripsi'] ?: '-'; ?></td>
                                <td><?php 
                                    $dateToShow = !empty($row['updated_at']) ? $row['updated_at'] : $row['created_at'];
                                    echo date('d/m/Y', strtotime($dateToShow)); 
                                ?></td>
                                <td>
                                    <button onclick="editKategori(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                            class="btn btn-warning btn-sm">Edit</button>
                                    <button onclick="deleteKategori(<?php echo $row['id']; ?>)" 
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
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(255,255,255,0.85); backdrop-filter: blur(24px) saturate(130%); border: 1px solid rgba(255,255,255,0.8); box-shadow: 0 30px 60px rgba(0,0,0,0.12); padding: 40px; border-radius: 32px; width: 90%; max-width: 500px;">
            <h2>Edit Kategori</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_nama_kategori">Nama Kategori</label>
                    <input type="text" id="edit_nama_kategori" name="nama_kategori" required>
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
        function editKategori(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_nama_kategori').value = data.nama_kategori;
            document.getElementById('edit_deskripsi').value = data.deskripsi;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function deleteKategori(id) {
            if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
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

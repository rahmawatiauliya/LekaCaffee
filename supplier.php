<?php
require_once 'config/config.php';
requireRole(['admin']);

require_once 'models/Supplier.php';

$database = new Database();
$db = $database->getConnection();

$supplier = new Supplier($db);

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $supplier->nama_supplier = sanitizeInput($_POST['nama_supplier']);
                $supplier->alamat = sanitizeInput($_POST['alamat']);
                $supplier->telepon = sanitizeInput($_POST['telepon']);
                $supplier->email = sanitizeInput($_POST['email']);

                if ($supplier->create()) {
                    $message = 'Supplier berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan supplier!';
                    $message_type = 'error';
                }
                break;

            case 'update':
                $supplier->id = sanitizeInput($_POST['id']);
                $supplier->nama_supplier = sanitizeInput($_POST['nama_supplier']);
                $supplier->alamat = sanitizeInput($_POST['alamat']);
                $supplier->telepon = sanitizeInput($_POST['telepon']);
                $supplier->email = sanitizeInput($_POST['email']);

                if ($supplier->update()) {
                    $message = 'Supplier berhasil diperbarui!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal memperbarui supplier!';
                    $message_type = 'error';
                }
                break;

            case 'delete':
                $supplier->id = sanitizeInput($_POST['id']);
                if ($supplier->delete()) {
                    $message = 'Supplier berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus supplier!';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Get all supplier
$stmt = $supplier->readAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier - <?php echo APP_NAME; ?></title>
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
                        <i class="fa-solid fa-mug-hot"></i> Data Menu
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
                    <a href="kategori.php" class="nav-link">
                        <i class="fa-solid fa-tags"></i> Kategori menu
                    </a>
                </li>
                <li class="nav-item">
                    <a href="supplier.php" class="nav-link active">
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
                <h1>Supplier</h1>
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

                <!-- Add Supplier Form -->
                <div class="form-container">
                    <h2>Tambah Supplier Baru</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama_supplier">Nama Supplier</label>
                                <input type="text" id="nama_supplier" name="nama_supplier" required>
                            </div>
                            <div class="form-group">
                                <label for="telepon">Telepon</label>
                                <input type="text" id="telepon" name="telepon">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah Supplier</button>
                    </form>
                </div>

                <!-- Data Supplier Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">Daftar Supplier</h3>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Supplier</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Terakhir Diubah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nama_supplier']; ?></td>
                                <td><?php echo $row['alamat'] ?: '-'; ?></td>
                                <td><?php echo $row['telepon'] ?: '-'; ?></td>
                                <td><?php echo $row['email'] ?: '-'; ?></td>
                                <td><?php 
                                    $dateToShow = !empty($row['updated_at']) ? $row['updated_at'] : $row['created_at'];
                                    echo date('d/m/Y', strtotime($dateToShow)); 
                                ?></td>
                                <td>
                                    <button onclick="editSupplier(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                            class="btn btn-warning btn-sm">Edit</button>
                                    <button onclick="deleteSupplier(<?php echo $row['id']; ?>)" 
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
            <h2>Edit Supplier</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_nama_supplier">Nama Supplier</label>
                        <input type="text" id="edit_nama_supplier" name="nama_supplier" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_telepon">Telepon</label>
                        <input type="text" id="edit_telepon" name="telepon">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" id="edit_email" name="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_alamat">Alamat</label>
                    <textarea id="edit_alamat" name="alamat" rows="3"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editSupplier(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_nama_supplier').value = data.nama_supplier;
            document.getElementById('edit_alamat').value = data.alamat;
            document.getElementById('edit_telepon').value = data.telepon;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function deleteSupplier(id) {
            if (confirm('Apakah Anda yakin ingin menghapus supplier ini?')) {
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

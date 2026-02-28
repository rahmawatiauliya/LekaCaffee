<?php
require_once 'config/config.php';
requireRole(['admin', 'kasir']);

require_once 'models/Menu.php';
require_once 'models/Penjualan.php';

$database = new Database();
$db = $database->getConnection();

$menu = new Menu($db);
$penjualan = new Penjualan($db);

$message = '';
$message_type = '';

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'search_menu':
            $keyword = sanitizeInput($_GET['keyword']);
            $stmt = $menu->search($keyword);
            $results = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
            echo json_encode($results);
            exit;
            
        case 'get_menu':
            $menu_id = sanitizeInput($_GET['menu_id']);
            $menu->id = $menu_id;
            if ($menu->readOne()) {
                echo json_encode([
                    'id' => $menu->id,
                    'kode_menu' => $menu->kode_menu,
                    'nama_menu' => $menu->nama_menu,
                    'harga_jual' => $menu->harga_jual,
                    'stok' => $menu->stok
                ]);
            } else {
                echo json_encode(['error' => 'menu tidak ditemukan']);
            }
            exit;
    }
}

// Handle transaction submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'process_transaction') {
    try {
        $db->beginTransaction();
        
        // Create penjualan record
        $penjualan->no_transaksi = $penjualan->generateNoTransaksi();
        $penjualan->user_id = $_SESSION['user_id'];
        $penjualan->total_harga = sanitizeInput($_POST['total_harga']);
        $penjualan->total_bayar = sanitizeInput($_POST['total_bayar']);
        $penjualan->kembalian = sanitizeInput($_POST['kembalian']);
        
        if (!$penjualan->create()) {
            throw new Exception('Gagal membuat transaksi');
        }
        
        $penjualan_id = $db->lastInsertId();
        
        // Process detail penjualan
        $items = json_decode($_POST['items'], true);
        foreach ($items as $item) {
            // Insert detail penjualan
            $detail_query = "INSERT INTO detail_penjualan (penjualan_id, menu_id, jumlah, harga_satuan, subtotal) 
                             VALUES (:penjualan_id, :menu_id, :jumlah, :harga_satuan, :subtotal)";
            $detail_stmt = $db->prepare($detail_query);
            $detail_stmt->bindParam(':penjualan_id', $penjualan_id);
            $detail_stmt->bindParam(':menu_id', $item['id']);
            $detail_stmt->bindParam(':jumlah', $item['quantity']);
            $detail_stmt->bindParam(':harga_satuan', $item['price']);
            $detail_stmt->bindParam(':subtotal', $item['subtotal']);
            
            if (!$detail_stmt->execute()) {
                throw new Exception('Gagal menyimpan detail penjualan');
            }
            
            // Update stok menu
            $menu->updateStok($item['id'], -$item['quantity']);
        }
        
        $db->commit();
        
        // Redirect to receipt
        header('Location: struk.php?id=' . $penjualan_id);
        exit();
        
    } catch (Exception $e) {
        $db->rollBack();
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        .pos-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            height: calc(100vh - 120px);
        }
        
        .product-section {
            background: rgba(255, 255, 255, 0.82);
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.12), inset 0 2px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.8);
            padding: 24px;
            overflow-y: auto;
            backdrop-filter: blur(24px) saturate(130%);
            -webkit-backdrop-filter: blur(24px) saturate(130%);
        }
        
        .cart-section {
            background: rgba(255, 255, 255, 0.82);
            border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.12), inset 0 2px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.8);
            padding: 24px;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(24px) saturate(130%);
            -webkit-backdrop-filter: blur(24px) saturate(130%);
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid var(--border-color);
            background: var(--bg-body);
            border-radius: var(--radius-md);
            font-size: 15px;
            transition: all 0.3s ease;
            color: var(--text-heading);
            font-family: inherit;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--bg-surface);
            box-shadow: 0 0 0 4px var(--primary-transparent);
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .product-card {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.6);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .product-card:hover {
            border-color: var(--primary-light);
            background: #FFFFFF;
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .product-card.selected {
            border-color: var(--primary);
            background: var(--primary-bg);
            box-shadow: 0 0 0 2px var(--primary-transparent);
        }
        
        .product-name {
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-heading);
            font-size: 15px;
        }
        
        .product-price {
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 6px;
            font-size: 16px;
        }
        
        .product-stock {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
        }
        
        .cart-items {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid var(--border-color);
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-heading);
            font-size: 14px;
        }
        
        .item-price {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-control button {
            width: 28px;
            height: 28px;
            border: 1px solid var(--border-color);
            background: var(--bg-body);
            color: var(--text-heading);
            cursor: pointer;
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .quantity-control button:hover {
            background: var(--primary-light);
            color: #fff;
            border-color: var(--primary-light);
        }
        
        .quantity-control input {
            width: 44px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 4px;
            font-weight: 600;
            color: var(--text-heading);
            background: #fff;
        }
        
        .cart-summary {
            border-top: 1px solid var(--border-light);
            padding-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--text-body);
            font-weight: 500;
        }
        
        .summary-row.total {
            font-weight: 800;
            font-size: 20px;
            color: var(--text-heading);
            border-top: 1px solid var(--border-color);
            padding-top: 16px;
            margin-top: 12px;
        }
        
        .payment-section {
            margin-top: 20px;
        }
        
        .payment-section input {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid var(--border-color);
            background: var(--bg-body);
            border-radius: var(--radius-md);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            transition: all 0.3s ease;
            color: var(--text-heading);
            font-family: inherit;
        }

        .payment-section input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--bg-surface);
            box-shadow: 0 0 0 4px var(--primary-transparent);
        }
        
        .btn-process {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 16px var(--primary-transparent);
        }
        
        .btn-process:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(156, 122, 99, 0.25);
        }
        
        .btn-process:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
    </style>
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
                    <a href="penjualan.php" class="nav-link active">
                        <i class="fa-solid fa-cart-shopping"></i> Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan_penjualan.php" class="nav-link">
                        <i class="fa-solid fa-chart-line"></i> Laporan Penjualan
                    </a>
                </li>
                
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
                <h1>Point of Sale (POS)</h1>
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

                <div class="pos-container">
                    <!-- Product Section -->
                    <div class="product-section">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari menu..." onkeyup="searchProducts()">
                        </div>
                        
                        <div id="productGrid" class="product-grid">
                            <!-- Products will be loaded here -->
                        </div>
                    </div>

                    <!-- Cart Section -->
                    <div class="cart-section">
                        <h3>Keranjang Belanja</h3>
                        
                        <div class="cart-items" id="cartItems">
                            <p style="text-align: center; color: #7f8c8d; padding: 20px;">
                                Keranjang kosong
                            </p>
                        </div>
                        
                        <div class="cart-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal">Rp 0</span>
                            </div>
                            <div class="summary-row">
                                <span>PPN (10%):</span>
                                <span id="ppn">Rp 0</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="total">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="payment-section">
                            <input type="number" id="paymentInput" placeholder="Jumlah Bayar" onkeyup="calculateChange()">
                            <div class="summary-row">
                                <span>Kembalian:</span>
                                <span id="change">Rp 0</span>
                            </div>
                            <button class="btn-process" id="processBtn" onclick="processTransaction()" disabled>
                                Proses Transaksi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let cart = [];
        let products = [];

        // Load products on page load
        window.onload = function() {
            searchProducts();
        };

        function searchProducts() {
            const keyword = document.getElementById('searchInput').value;
            
            fetch(`penjualan.php?action=search_menu&keyword=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {
                    products = data;
                    displayProducts(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function displayProducts(products) {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '';

            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.onclick = () => addToCart(product);
                
                productCard.innerHTML = `
                    <div class="product-name">${product.nama_menu}</div>
                    <div class="product-price">${formatCurrency(product.harga_jual)}</div>
                    <div class="product-stock">Stok: ${product.stok} ${product.satuan}</div>
                `;
                
                grid.appendChild(productCard);
            });
        }

        function addToCart(product) {
            if (product.stok <= 0) {
                alert('Stok menu habis!');
                return;
            }

            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                if (existingItem.quantity < product.stok) {
                    existingItem.quantity++;
                } else {
                    alert('Stok tidak mencukupi!');
                    return;
                }
            } else {
                cart.push({
                    id: product.id,
                    kode_menu: product.kode_menu,
                    nama_menu: product.nama_menu,
                    price: parseFloat(product.harga_jual),
                    quantity: 1,
                    max_stock: product.stok
                });
            }
            
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItems = document.getElementById('cartItems');
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 20px;">Keranjang kosong</p>';
                updateSummary();
                return;
            }

            cartItems.innerHTML = '';
            
            cart.forEach((item, index) => {
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                
                cartItem.innerHTML = `
                    <div class="item-info">
                        <div class="item-name">${item.nama_menu}</div>
                        <div class="item-price">${formatCurrency(item.price)}</div>
                    </div>
                    <div class="item-controls">
                        <div class="quantity-control">
                            <button onclick="updateQuantity(${index}, -1)">-</button>
                            <input type="number" value="${item.quantity}" min="1" max="${item.max_stock}" 
                                   onchange="setQuantity(${index}, this.value)">
                            <button onclick="updateQuantity(${index}, 1)">+</button>
                        </div>
                        <button onclick="removeFromCart(${index})" class="btn btn-danger btn-sm">Hapus</button>
                    </div>
                `;
                
                cartItems.appendChild(cartItem);
            });
            
            updateSummary();
        }

        function updateQuantity(index, change) {
            const item = cart[index];
            const newQuantity = item.quantity + change;
            
            if (newQuantity >= 1 && newQuantity <= item.max_stock) {
                item.quantity = newQuantity;
                updateCartDisplay();
            }
        }

        function setQuantity(index, value) {
            const item = cart[index];
            const newQuantity = parseInt(value);
            
            if (newQuantity >= 1 && newQuantity <= item.max_stock) {
                item.quantity = newQuantity;
                updateCartDisplay();
            }
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

        function updateSummary() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const ppn = subtotal * 0.1; // 10% PPN
            const total = subtotal + ppn;
            
            document.getElementById('subtotal').textContent = formatCurrency(subtotal);
            document.getElementById('ppn').textContent = formatCurrency(ppn);
            document.getElementById('total').textContent = formatCurrency(total);
            
            calculateChange();
        }

        function calculateChange() {
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const totalWithPPN = total * 1.1; // 10% PPN
            const payment = parseFloat(document.getElementById('paymentInput').value) || 0;
            const change = payment - totalWithPPN;
            
            document.getElementById('change').textContent = formatCurrency(Math.max(0, change));
            
            const processBtn = document.getElementById('processBtn');
            processBtn.disabled = cart.length === 0 || payment < totalWithPPN;
        }

        function processTransaction() {
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const totalWithPPN = total * 1.1;
            const payment = parseFloat(document.getElementById('paymentInput').value);
            const change = payment - totalWithPPN;
            
            if (payment < totalWithPPN) {
                alert('Jumlah bayar kurang!');
                return;
            }
            
            // Calculate subtotal for each item (without PPN)
            cart.forEach(item => {
                item.subtotal = item.price * item.quantity;
            });
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="process_transaction">
                <input type="hidden" name="items" value='${JSON.stringify(cart)}'>
                <input type="hidden" name="total_harga" value="${totalWithPPN}">
                <input type="hidden" name="total_bayar" value="${payment}">
                <input type="hidden" name="kembalian" value="${change}">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
    </script>
</body>
</html>

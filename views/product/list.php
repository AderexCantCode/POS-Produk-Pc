<?php
session_start();
include '../../system/koneksi.php';
cekLogin();

// Inisialisasi cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Mendapatkan produk dengan search dan filter(category)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$customers = getCustomers();

$query = "SELECT p.*, c.category_name FROM product p JOIN category c ON p.category_id = c.category_id";
$params = [];

if (!empty($search)) {
    $query .= " WHERE p.product_name LIKE ?";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= empty($params) ? " WHERE" : " AND";
    $query .= " p.category_id = ?";
    $params[] = $category;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getCustomers() {
    global $pdo;
    return $pdo->query("SELECT * FROM customer")->fetchAll(PDO::FETCH_ASSOC);
}

function addToCart($productId) {
    global $pdo;
    $query = $pdo->prepare("SELECT * FROM product WHERE product_id = ?");
    $query->execute([$productId]);
    $product = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($product && $product['stock'] > 0) {
        $existingItemKey = array_search($productId, array_column($_SESSION['cart'], 'product_id'));

        if ($existingItemKey !== false) {
            if ($_SESSION['cart'][$existingItemKey]['quantity'] < $product['stock']) {
                $_SESSION['cart'][$existingItemKey]['quantity']++;
            }
            return true;
        } else {
            $_SESSION['cart'][] = [
                'product_id' => $product['product_id'],
                'product_name' => $product['product_name'],
                'price' => $product['price'],
                'quantity' => 1,
                'image' => $product['gambar'] 
            ];
            return true;
        }
    }
    return false;
}

function removeFromCart($productId) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['product_id'] == $productId) {
            if ($_SESSION['cart'][$key]['quantity'] > 1) {
                $_SESSION['cart'][$key]['quantity']--;
            } else {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            return true;
        }
    }
    return false;
}

function processOrder($customerId, $adminId) {
    global $pdo;
    
    if (empty($_SESSION['cart'])) {
        return ['success' => false, 'message' => 'Keranjang Kosong'];
    }

    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (admin_id, customer_id, order_date, total_amount, status) VALUES (?, ?, NOW(), ?, 'Pending')");
        $totalAmount = array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $_SESSION['cart']));
        $stmt->execute([$adminId, $customerId, $totalAmount]);
        $orderId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO orders_detail (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $updateStock = $pdo->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ?");

        foreach ($_SESSION['cart'] as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price'], $subtotal]);
            $updateStock->execute([$item['quantity'], $item['product_id']]);
        }

        $pdo->commit();
        $_SESSION['cart'] = [];
        return ['success' => true, 'message' => 'Order processed successfully'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error processing order: ' . $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_to_cart':
                $result = addToCart($_POST['product_id']);
                echo json_encode(['success' => $result, 'cartCount' => count($_SESSION['cart'])]);
                exit;
            case 'remove_from_cart':
                $result = removeFromCart($_POST['product_id']);
                echo json_encode(['success' => $result, 'cartCount' => count($_SESSION['cart'])]);
                exit;
            case 'process_order':
                if (isset($_POST['customer_id'])) {
                    $result = processOrder($_POST['customer_id'], $_SESSION['admin_id']);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Please select a customer to process the order.']);
                }
                exit;
            case 'get_cart':
                echo json_encode([
                    'items' => $_SESSION['cart'],
                    'totals' => [
                        'subtotal' => array_sum(array_map(function($item) { 
                            return $item['price'] * $item['quantity']; 
                        }, $_SESSION['cart'])),
                        'discount' => 2.00, 
                        'tax' => 1.00 
                    ]
                ]);
                exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
          html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        .sidebar { 
            width: 4rem;
            background-color: #2563eb;
            height: 100vh; 
            position: fixed;
            z-index: 1000;
            transition: width 0.3s ease;
        }

        .sidebar:hover {
            width: 16rem;
        }

        .main-content {
            margin-left: 4rem;
            height: 100vh;
            overflow-y: auto;
            background-color: #f3f4f6;
            transition: margin-left 0.3s ease;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 16rem;
        }

        .cart-sidebar {
            width: 400px;
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            background: white;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }


        .main-content {
            margin-right: 400px;
        }

        .cart-header {
            padding: 1rem;
            background: #2563eb;
            color: white;
        }

        .cart-items {
            padding: 1rem;
            overflow-y: auto;
            max-height: calc(100vh - 200px);
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 1rem;
        }

        .cart-item-details {
            flex-grow: 1;
        }

        .cart-item-price {
            color: #2563eb;
            font-weight: bold;
        }

        .cart-quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity-btn {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background: #f3f4f6;
            cursor: pointer;
        }

        .cart-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 1rem;
            background: white;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }

        .cart-totals {
            margin-bottom: 1rem;
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .cart-total-row.final {
            border-top: 1px solid #eee;
            padding-top: 0.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php include '../templates/sidebar.php'; ?>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 main-content">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-8">Product Catalog</h1>

                <!-- Search and Filter -->
                <div class="mb-8">
                    <form action="" method="GET" class="flex space-x-4">
                        <input type="text" name="search" placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="p-2 border rounded">
                        <select name="category" class="p-2 border rounded">
                            <option value="">All Categories</option>
                            <?php
                            $categoryQuery = $pdo->query("SELECT * FROM category");
                            while ($cat = $categoryQuery->fetch(PDO::FETCH_ASSOC)) {
                                $selected = ($cat['category_id'] == $category) ? 'selected' : '';
                                echo "<option value='{$cat['category_id']}' $selected>{$cat['category_name']}</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
                    </form>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                            <img src="<?php echo htmlspecialchars($product['gambar']); ?>" 
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                </p>
                                <p class="text-gray-600 mb-2">Stock: <?php echo $product['stock']; ?></p>
                                <p class="text-gray-600 mb-2">Category: <?php echo htmlspecialchars($product['category_name']); ?></p> <!-- Display category name -->
                                <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 add-to-cart" 
                                        data-product-id="<?php echo $product['product_id']; ?>">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>

        <!-- Cart Sidebar -->
        <div id="cartSidebar" class="cart-sidebar">
            <div class="cart-header">
                <h2 class="text-xl font-bold">Current Order (<span id="cartCount"><?php echo count($_SESSION['cart']); ?></span>)</h2>
            </div>
            
            <div id="cartItems" class="cart-items">
            </div>

            <div class="cart-footer">
                <select id="customerSelect" class="w-full p-2 mb-4 border rounded">
                    <option value="">-- Select Customer --</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['customer_id']; ?>">
                            <?php echo htmlspecialchars($customer['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button id="checkoutButton" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Continue To Payment ($<span id="totalAmount">0.00</span>)
                </button>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    let cartVisible = false;
    const cartSidebar = document.getElementById('cartSidebar');
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const totalAmount = document.getElementById('totalAmount');
    const checkoutButton = document.getElementById('checkoutButton');
    const customerSelect = document.getElementById('customerSelect');

    // Inisialisasi cart
    updateCart();

    // add to cart listener
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            addToCart(productId);
        });
    });

    // listener checkout
    checkoutButton.addEventListener('click', processOrder);

    // Cart functions
    async function addToCart(productId) {
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add_to_cart&product_id=${productId}`
            });
            
            const data = await response.json();
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Menambahkan Ke Keranjang!',
                    showConfirmButton: false,
                    timer: 1500
                });
                updateCart();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Maaf',
                    text: 'Tidak Bisa Menambahkan Produk, Mungkin Stocknya Habis.'
                });
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function removeFromCart(productId) {
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove_from_cart&product_id=${productId}`
            });
            
            const data = await response.json();
            if (data.success) {
                updateCart();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function updateCart() {
        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_cart'
            });
            
            const data = await response.json();
            
            // mengupdate jumlah pada cart
            cartCount.textContent = data.items.length;
            
            // menghapus cart item
            cartItems.innerHTML = '';
            
            // menambahkan item ke cart
            data.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item p-4 border-b';
                itemElement.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img src="${item.image}" alt="${item.product_name}" class="w-16 h-16 object-cover rounded">
                            <div class="ml-4">
                                <h3 class="font-semibold">${item.product_name}</h3>
                                <p class="text-gray-600">Rp ${formatNumber(item.price)}</p>
                                <div class="flex items-center mt-2">
                                    <button class="decrease-quantity px-2 py-1 bg-gray-200 rounded-l" data-id="${item.product_id}">-</button>
                                    <span class="px-4 py-1 bg-gray-100">${item.quantity}</span>
                                    <button class="increase-quantity px-2 py-1 bg-gray-200 rounded-r" data-id="${item.product_id}">+</button>
                                </div>
                            </div>
                        </div>
                        <p class="font-semibold">Rp ${formatNumber(item.price * item.quantity)}</p>
                    </div>
                `;
                cartItems.appendChild(itemElement);
            });

            // Update total pembelian
            const subtotal = data.totals.subtotal;
            const tax = data.totals.tax;
            const discount = data.totals.discount;
            const total = subtotal + tax - discount;
            
            totalAmount.textContent = formatNumber(total);

            // menambahkan event listener untuk quantity pada cart
            document.querySelectorAll('.decrease-quantity').forEach(button => {
                button.addEventListener('click', () => removeFromCart(button.dataset.id));
            });
            
            document.querySelectorAll('.increase-quantity').forEach(button => {
                button.addEventListener('click', () => addToCart(button.dataset.id));
            });
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function processOrder() {
        const customerId = customerSelect.value;
        
        if (!customerId) {
            Swal.fire({
                icon: 'Warning',
                title: 'Harap Memilih Customer Terlebih Dahulu',
                text: 'Customer Harus Dipilih Jika Melakukan Order'
            });
            return;
        }

        try {
            const response = await fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=process_order&customer_id=${customerId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Di Proses!',
                    text: data.message
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
    </script>
</body>
</html>
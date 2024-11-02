<?php
include '../system/koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    // Jika belum login, mengarahkan ke halaman login
    header('Location: ../views/auth/login.php');
    exit();
}


// Query untuk mengambil data produk
$sql_products = "SELECT product_id, product_name, category_id, price, stock, created_at, gambar FROM product";
$stmt_products = $pdo->prepare($sql_products);
$stmt_products->execute();
$products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data customer
$sql_customers = "SELECT customer_id, name, email, phone, address, created_at FROM customer";
$stmt_customers = $pdo->prepare($sql_customers);
$stmt_customers->execute();
$customers = $stmt_customers->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data kategori
$sql_categories = "SELECT category_id, category_name, created_at FROM category";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data dari tabel admin
$sql_admins = "SELECT admin_id, username, password, email, created_at FROM admin"; 
$stmt_admins = $pdo->prepare($sql_admins);
$stmt_admins->execute();
$admins = $stmt_admins->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Data Display</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            transition: width 0.3s ease;
            width: 16rem;
        }
        .content {
            margin-left: 16rem;
            transition: margin-left 0.3s ease;
        }
        .sidebar:hover + .content {
            margin-left: 16rem;
        }
        .hidden {
            display: none;
        }
        .tab-active {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
<a href="../../../Industri/POS_ProjekAkhirV2/views/users/index.php" class="bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-md hover:bg-gray-300 transition duration-200 flex items-center">
    <i class="fas fa-arrow-left mr-2"></i> Go Back
</a>


    <!-- Konten utama -->
    <div class="content container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8">Master Data RexPart</h1>
        <p>Tempat Dimana Mengedit Data, Menambah Data Maupun Menghapus.</p>

        <!-- Tabs -->
        <div class="mb-4">
            <ul class="flex border-b">
                <li class="mr-1">
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold cursor-pointer" onclick="showTab('products')">Products</a>
                </li>
                <li class="mr-1">
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold cursor-pointer" onclick="showTab('customers')">Customers</a>
                </li>
                <li class="mr-1">
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold cursor-pointer" onclick="showTab('categories')">Categories</a>
                </li>
                <li class="mr-1">
                    <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold cursor-pointer" onclick="showTab('admin')">Admin</a>
                </li>
            </ul>
        </div>

        <!-- Products Table -->
        <div id="products" class="tab-content tab-active" data-aos="fade-up">
            <h2 class="text-2xl font-semibold mb-4">Product Data</h2>
            <a href="../admin/add/product.php" class="bg-blue-500 text-white font-semibold py-2 px-3 rounded-md hover:bg-blue-600 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Data
            </a>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Product ID</th>
                            <th class="px-4 py-2 text-left">Product Name</th>
                            <th class="px-4 py-2 text-left">Category ID</th>
                            <th class="px-4 py-2 text-left">Price</th>
                            <th class="px-4 py-2 text-left">Stock</th>
                            <th class="px-4 py-2 text-left">Created At</th>
                            <th class="px-4 py-2 text-left">Image</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $index => $product): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : '' ?>">
                                <td class="px-4 py-2"><?= htmlspecialchars($product['product_id']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['product_name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['category_id']) ?></td>
                                <td class="px-4 py-2">Rp <?= number_format($product['price'], 0) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['stock']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($product['created_at']) ?></td>
                                <td class="px-4 py-2">
                                    <?php if ($product['gambar']): ?>
                                        <img src="<?= htmlspecialchars($product['gambar']) ?>" alt="Product Image" class="w-10 h-10 object-cover">
                                    <?php else: ?>
                                        <i class="fas fa-image text-gray-400"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2">
                                    <a href="edit/product.php?id=<?= htmlspecialchars($product['product_id']) ?>" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete/product.php?id=<?= htmlspecialchars($product['product_id']) ?>" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Customers Table -->
        <div id="customers" class="tab-content hidden" data-aos="fade-up">
            <h2 class="text-2xl font-semibold mb-4">Customer Data</h2>
            <a href="../admin/add/customer.php" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Data
            </a>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Customer ID</th>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2 text-left">Address</th>
                            <th class="px-4 py-2 text-left">Created At</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $index => $customer): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : '' ?>">
                                <td class="px-4 py-2"><?= htmlspecialchars($customer['customer_id']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($customer['name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($customer['email']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($customer['phone']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($customer['address']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($customer['created_at']) ?></td>
                                <td class="px-4 py-2">
                                <a href="edit/customer.php?id=<?= htmlspecialchars($customer['customer_id']) ?>" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete/customer.php?id=<?= htmlspecialchars($customer['customer_id']) ?>" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Categories Table -->
        <div id="categories" class="tab-content hidden" data-aos="fade-up">
            <h2 class="text-2xl font-semibold mb-4">Category Data</h2>
            <a href="../admin/add/category.php" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Data
            </a>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Category ID</th>
                            <th class="px-4 py-2 text-left">Category Name</th>
                            <th class="px-4 py-2 text-left">Created At</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $index => $category): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : '' ?>">
                                <td class="px-4 py-2"><?= htmlspecialchars($category['category_id']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($category['category_name']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($category['created_at']) ?></td>
                                <td class="px-4 py-2">
                                    <a href="edit/category.php?id=<?= htmlspecialchars($category['category_id']) ?>" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete/category.php?id=<?= htmlspecialchars($category['category_id']) ?>" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

                <!-- Admin Table -->
        <div id="admin" class="tab-content hidden" data-aos="fade-up">
            <h2 class="text-2xl font-semibold mb-4">Admin Data</h2>
            <a href="../admin/add/admin.php" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i> Tambah Data
            </a>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full table-auto">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-2 py-1 text-left text-sm">Admin ID</th>
                            <th class="px-2 py-1 text-left text-sm">Username</th>
                            <th class="px-2 py-1 text-left text-sm">Email</th>
                            <th class="px-2 py-1 text-left text-sm">Password</th>
                            <th class="px-2 py-1 text-left text-sm">Created At</th>
                            <th class="px-2 py-1 text-left text-sm">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $index => $admin): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : '' ?>">
                                <td class="px-2 py-1 text-sm"><?= htmlspecialchars($admin['admin_id']) ?></td>
                                <td class="px-2 py-1 text-sm"><?= htmlspecialchars($admin['username']) ?></td>
                                <td class="px-2 py-1 text-sm"><?= htmlspecialchars($admin['email']) ?></td>
                                <td class="px-2 py-1 text-sm"><?= htmlspecialchars($admin['password']) ?></td>
                                <td class="px-2 py-1 text-sm"><?= htmlspecialchars($admin['created_at']) ?></td>
                                <td class="px-2 py-1 text-sm">
                                    <a href="edit/admin.php?id=<?= htmlspecialchars($admin['admin_id']) ?>" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete/admin.php?id=<?= htmlspecialchars($admin['admin_id']) ?>" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
        
        function showTab(tabName) {
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.add('hidden'));

            const activeTab = document.getElementById(tabName);
            activeTab.classList.remove('hidden');
        }
    </script>
</body>
</html>

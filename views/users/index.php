<?php
include '../../system/koneksi.php'; 
cekLogin();


try {
    $pdo = new PDO('mysql:host=localhost;port=3050;dbname=ki_posv2', 'root', 'Panzerfaust6187');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mendapat total produk
    $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM product");
    $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

    // Mendapat total transaksi
    $stmt = $pdo->query("SELECT COUNT(*) as total_transactions FROM orders");
    $total_transactions = $stmt->fetch(PDO::FETCH_ASSOC)['total_transactions'];

    // Mendapat total customer
    $stmt = $pdo->query("SELECT COUNT(*) as total_customers FROM customer");
    $total_customers = $stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];

    // Mendapat total Penjualan
    $stmt = $pdo->query("SELECT SUM(total_amount) as total_revenue FROM orders");
    $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'];
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RexParts - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            transition: width 0.3s ease;
        }
        .sidebar:hover {
            width: 16rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="sidebar w-16 bg-blue-600 text-white fixed h-full hover:w-64 z-50">
            <?php include '../templates/sidebar.php'; ?>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow ml-16 p-6">
            <header class="bg-white shadow-md rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                </div>
            </header>

                <!-- Dashboard Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
             <!-- Total Produk -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Produk</h3>
                    <i class="fas fa-microchip text-2xl text-blue-500"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?php echo number_format($total_products); ?></p>
                <p class="text-sm text-gray-500 mt-2">Komponen komputer</p>
            </div>
            <!-- Total Transaksi -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Transaksi</h3>
                    <i class="fas fa-shopping-cart text-2xl text-green-500"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?php echo number_format($total_transactions); ?></p>
                <p class="text-sm text-gray-500 mt-2">Total Transaksi</p>
            </div>
            <!-- Total Pelanggan -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Pelanggan</h3>
                    <i class="fas fa-users text-2xl text-purple-500"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?php echo number_format($total_customers); ?></p>
                <p class="text-sm text-gray-500 mt-2">Pelanggan aktif</p>
            </div>
            <!-- Total Pendapatan -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Total Pendapatan</h3>
                    <i class="fas fa-dollar-sign text-2xl text-yellow-500"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800">Rp.<?php echo number_format($total_revenue, 2); ?></p>
                <p class="text-sm text-gray-500 mt-2">Dari Seluruh Transaksi</p>
            </div>
        </div>
            <!-- About RexParts -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Tentang RexParts</h2>
                <p class="text-gray-600 mb-4">
                    RexParts adalah toko komponen komputer terkemuka yang menyediakan berbagai macam part komputer berkualitas tinggi. Kami berkomitmen untuk memberikan produk terbaik dan layanan pelanggan yang unggul kepada para penggemar komputer, profesional IT, dan pelanggan umum.
                </p>
                <p class="text-gray-600 mb-4">
                    Dengan produk yang berkualitas dalam inventaris kami, RexParts menawarkan segala hal mulai dari prosesor dan motherboard hingga kartu grafis, RAM, storage, power supply, dan aksesoris komputer lainnya. Kami bekerja sama dengan merek-merek terkemuka di industri untuk memastikan kualitas dan kinerja terbaik untuk setiap komponen yang kami jual.
                </p>
                <p class="text-gray-600">
                    Sistem POS kami yang canggih memungkinkan kami untuk mengelola inventaris dengan efisien, memproses pesanan dengan cepat, dan memberikan pengalaman berbelanja yang mulus bagi pelanggan kami, baik online maupun di toko fisik kami.
                </p>
            </div>

            <!-- Product Categories -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Kategori Produk Utama</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="bg-blue-100 p-4 rounded-lg text-center">
                        <i class="fas fa-microchip text-3xl text-blue-500 mb-2"></i>
                        <p class="font-semibold">Prosesor</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg text-center">
                        <i class="fas fa-memory text-3xl text-green-500 mb-2"></i>
                        <p class="font-semibold">RAM</p>
                    </div>
                    <div class="bg-purple-100 p-4 rounded-lg text-center">
                        <i class="fas fa-hdd text-3xl text-purple-500 mb-2"></i>
                        <p class="font-semibold">Storage</p>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-lg text-center">
                        <i class="fas fa-desktop text-3xl text-yellow-500 mb-2"></i>
                        <p class="font-semibold">Monitor</p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-lg text-center">
                        <i class="fas fa-gamepad text-3xl text-red-500 mb-2"></i>
                        <p class="font-semibold">Kartu Grafis</p>
                    </div>
                    <div class="bg-indigo-100 p-4 rounded-lg text-center">
                        <i class="fas fa-fan text-3xl text-indigo-500 mb-2"></i>
                        <p class="font-semibold">Cooler</p>
                    </div>
                    <div class="bg-pink-100 p-4 rounded-lg text-center">
                        <i class="fas fa-plug text-3xl text-pink-500 mb-2"></i>
                        <p class="font-semibold">Power Supply</p>
                    </div>
                    <div class="bg-teal-100 p-4 rounded-lg text-center">
                        <i class="fas fa-keyboard text-3xl text-teal-500 mb-2"></i>
                        <p class="font-semibold">Perlengkapan</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    </script>
</body>
</html>
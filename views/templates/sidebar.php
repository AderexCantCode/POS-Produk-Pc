
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set Nama Admin Cihuyy
$adminName = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sidebar dan Footer</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .sidebar { transition: width 0.3s ease; }
        .sidebar:hover { width: 16rem; }
        body {
            font-family: 'Poppins', sans-serif;
        }
        #sidebar {
            width: 4rem;
            transition: width 0.3s ease-in-out;
        }
        #sidebar:hover {
            width: 16rem;
        }
        #sidebar:hover .sidebar-text {
            display: inline;
            opacity: 1;
        }
        .sidebar-text {
            display: none;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        .sidebar-item {
            padding: 0.75rem;
            display: flex;
            align-items: center;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }
        .sidebar-item:hover {
            background-color: rgba(37, 99, 235, 0.8);
        }
        .sidebar-item i {
            font-size: 1.25rem;
            width: 1.5rem;
            text-align: center;
        }
        .sidebar-item .sidebar-text {
            margin-left: 1rem;
            font-size: 1rem;
            font-weight: 500;
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-blue-800 text-white min-h-screen p-4 fixed flex flex-col justify-between overflow-x-hidden">
            <div>
                <!-- Logo -->
                <div class="flex items-center justify-center mb-8">
                    <i class="fas fa-cash-register text-3xl"></i>
                    <span class="ml-4 sidebar-text text-xl font-bold">RexParts</span>
                </div>

                <!-- User Info -->
                <div class="flex items-center mb-8">
                    <i class="fas fa-user-circle text-3xl"></i>
                    <span class="ml-4 sidebar-text"><?php echo htmlspecialchars($adminName); ?></span>
                </div>

                <!-- Navigation Menu -->
                <nav>
                    <ul>
                        <li class="mb-4">
                            <a href="../users/index.php" class="sidebar-item">
                                <i class="fas fa-home"></i>
                                <span class="sidebar-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="mb-4">
                            <a href="../product/list.php" class="sidebar-item">
                                <i class="fas fa-box"></i>
                                <span class="sidebar-text">Produk</span>
                            </a>
                        </li>
                        <li class="mb-4">
                            <a href="../laporan/orders.php" class="sidebar-item">
                                <i class="fas fa-chart-bar"></i>
                                <span class="sidebar-text">Laporan</span>
                            </a>
                        </li>

                        <li class="mb-4">
                            <a href="../../admin/masterdata.php" class="sidebar-item">
                                <i class="fas fa-database"></i>
                                <span class="sidebar-text">Master Data</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Logout -->
            <div>
                <a href="../../views/auth/system/logout.php" class="sidebar-item text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="sidebar-text">Keluar</span>
                </a>
            </div>
        </aside>
    </div>
</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600);
    session_set_cookie_params(3600); 
    session_start(); 
}

$host = 'localhost';
$db   = 'ki_posv2';
$user = 'root';
$pass = 'Panzerfaust6187';
$charset = 'utf8mb4';
$port = 3050;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Tangkap error jika koneksi gagal
    echo "Koneksi gagal: " . $e->getMessage();
}
function cekLogin() {
    // Memulai Sesi Jika Belum Dimulai
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $currentPath = $_SERVER['REQUEST_URI'];

    // Mencheck sessi admin jika ada
    if (!isset($_SESSION['admin_id'])) {
        if (strpos($currentPath, 'views/orders/list.php') !== false) {
            $redirectPath = '../../auth/login.php'; //redirect untuk list.php
        } else {
            $redirectPath = '../auth/login.php'; // redirect untuk yang lain
        }

        // redirect
        header("Location: $redirectPath");
        exit;
    } else {
        if (isset($_SESSION['username'])) {
            error_log("Admin ID: " . $_SESSION['admin_id']);
            error_log("Username: " . $_SESSION['username']);
        }
    }
}



?>

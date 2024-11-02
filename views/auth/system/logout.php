<?php
session_start(); // Memulai sesi

// Cek apakah admin sudah login
if (isset($_SESSION['admin_id'])) {
    // Hapus semua sesi
    session_unset();
    session_destroy();
    
    // Redirect ke halaman login setelah logout
    header("Location: ../login.php");
    exit;
} else {
    // Jika user belum login, redirect langsung ke halaman login
    header("Location: ../login.php");
    exit;
}
?>

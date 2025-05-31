<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password_input = $_POST['password'] ?? '';

    if (empty($email) || empty($password_input)) {
        header("Location: login.php?error=2"); // Error: Field kosong
        exit;
    }

    // Menggunakan prepared statement untuk email dan password (teks biasa)
    $stmt = mysqli_prepare($koneksi, "SELECT id_admin, nama, email FROM admin WHERE email = ? AND password = ?");
    if (!$stmt) {
        error_log("MySQLi prepare failed: " . mysqli_error($koneksi));
        header("Location: login.php?error=3"); // Error database umum
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $email, $password_input);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($admin = mysqli_fetch_assoc($result)) {
        mysqli_stmt_close($stmt);
        // Password cocok (karena database yang melakukan perbandingan teks biasa)
        $_SESSION['email_admin'] = $admin['email']; 
        $_SESSION['nama_admin'] = $admin['nama']; 
        $_SESSION['id_admin'] = $admin['id_admin'];
        
        header("Location: menu.php");
        exit;
    } else {
        // Email atau password salah, atau kombinasi tidak ditemukan
        if ($stmt) mysqli_stmt_close($stmt);
        header("Location: login.php?error=1");
        exit;
    }
} else {
    // Jika bukan POST request, redirect ke login
    header("Location: login.php");
    exit;
}
?>
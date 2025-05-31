<?php
$host = "localhost";
$user = "root";
$password_db = "";
$database = "kospawawan1";

$koneksi = mysqli_connect($host, $user, $password_db, $database);

if (!$koneksi){
    error_log("Koneksi database gagal: " . mysqli_connect_error());
    die("Koneksi database gagal. Silakan coba lagi nanti."); // Pesan untuk user bisa lebih umum
}
?>  
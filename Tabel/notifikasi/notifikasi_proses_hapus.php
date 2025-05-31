<?php
include '../../koneksi.php';
include '../../Template/template_header.php'; 

$id_hapus = $_GET['id'] ?? null;

if (!$id_hapus) {
    header("Location: notifikasi_daftar.php?feedback=" . urlencode("ID Notifikasi untuk hapus tidak valid.") . "&type=error");
    exit;
}

$stmt_delete = mysqli_prepare($koneksi, "DELETE FROM notifikasi WHERE id_notifikasi = ?");
mysqli_stmt_bind_param($stmt_delete, "s", $id_hapus);

if (mysqli_stmt_execute($stmt_delete)) {
    mysqli_stmt_close($stmt_delete);
    header("Location: notifikasi_daftar.php?feedback=" . urlencode("Data notifikasi berhasil dihapus.") . "&type=sukses");
    exit;
} else {
    $error_msg = mysqli_error($koneksi);
    mysqli_stmt_close($stmt_delete);
    header("Location: notifikasi_daftar.php?feedback=" . urlencode("Gagal menghapus data notifikasi: " . $error_msg) . "&type=error");
    exit;
}
?>
<?php
include '../../koneksi.php';
include '../../Template/template_header.php'; // Untuk session check dan konsistensi

$id_hapus = $_GET['id'] ?? null;

if (!$id_hapus) {
    header("Location: pengguna_daftar.php?feedback=" . urlencode("ID Pengguna untuk hapus tidak valid.") . "&type=error");
    exit;
}

$stmt_delete = mysqli_prepare($koneksi, "DELETE FROM pengguna WHERE id_pengguna = ?");
mysqli_stmt_bind_param($stmt_delete, "s", $id_hapus);

if (mysqli_stmt_execute($stmt_delete)) {
    mysqli_stmt_close($stmt_delete);
    header("Location: pengguna_daftar.php?feedback=" . urlencode("Data pengguna berhasil dihapus. PERHATIAN: Ini bisa mempengaruhi data terkait lainnya.") . "&type=sukses");
    exit;
} else {
    $error_msg = mysqli_error($koneksi);
    mysqli_stmt_close($stmt_delete);
    // Tambahkan logging error jika perlu: error_log("Gagal hapus pengguna: " . $error_msg);
    $pesan_feedback = "Gagal menghapus pengguna. Kemungkinan pengguna ini memiliki data terkait di tabel lain yang mencegah penghapusan (Error: " . $error_msg . ").";
    header("Location: pengguna_daftar.php?feedback=" . urlencode($pesan_feedback) . "&type=error");
    exit;
}
?>
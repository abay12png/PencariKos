<?php
include '../../koneksi.php';
include '../../Template/template_header.php'; 

$id_hapus = $_GET['id'] ?? null;

if (!$id_hapus) {
    header("Location: fasilitas_daftar.php?feedback=" . urlencode("ID Fasilitas untuk hapus tidak valid.") . "&type=error");
    exit;
}

$stmt_check = mysqli_prepare($koneksi, "SELECT COUNT(*) as total FROM kos WHERE id_fasilitas = ?");
mysqli_stmt_bind_param($stmt_check, "s", $id_hapus);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$row_check = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check);

if ($row_check['total'] > 0) {
    $feedback_msg = "Gagal menghapus: Paket fasilitas ini masih digunakan oleh " . $row_check['total'] . " data kos. Ubah dulu ID Fasilitas pada data kos terkait.";
    header("Location: fasilitas_daftar.php?feedback=" . urlencode($feedback_msg) . "&type=error");
    exit;
}

$stmt_delete = mysqli_prepare($koneksi, "DELETE FROM fasilitas WHERE id_fasilitas = ?");
mysqli_stmt_bind_param($stmt_delete, "s", $id_hapus);

if (mysqli_stmt_execute($stmt_delete)) {
    mysqli_stmt_close($stmt_delete);
    header("Location: fasilitas_daftar.php?feedback=" . urlencode("Data paket fasilitas berhasil dihapus.") . "&type=sukses");
    exit;
} else {
    $error_msg = mysqli_error($koneksi);
    mysqli_stmt_close($stmt_delete);
    header("Location: fasilitas_daftar.php?feedback=" . urlencode("Gagal menghapus data paket fasilitas: " . $error_msg) . "&type=error");
    exit;
}
?>
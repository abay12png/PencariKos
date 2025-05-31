<?php
include '../../koneksi.php';
include '../../Template/template_header.php'; 

$id_hapus = $_GET['id'] ?? null;

if (!$id_hapus) {
    header("Location: review_rating_daftar.php?feedback=" . urlencode("ID Review untuk hapus tidak valid.") . "&type=error");
    exit;
}

$stmt_delete = mysqli_prepare($koneksi, "DELETE FROM review_rating WHERE id_review = ?");
mysqli_stmt_bind_param($stmt_delete, "s", $id_hapus);

if (mysqli_stmt_execute($stmt_delete)) {
    mysqli_stmt_close($stmt_delete);
    header("Location: review_rating_daftar.php?feedback=" . urlencode("Data review berhasil dihapus.") . "&type=sukses");
    exit;
} else {
    $error_msg = mysqli_error($koneksi);
    mysqli_stmt_close($stmt_delete);
    header("Location: review_rating_daftar.php?feedback=" . urlencode("Gagal menghapus data review: " . $error_msg) . "&type=error");
    exit;
}
?>
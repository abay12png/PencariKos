<?php
include '../../koneksi.php';
include '../../Template/template_header.php'; 

$id_hapus = $_GET['id'] ?? null;
$pesan_feedback = '';
$feedback_type = 'sukses';

if (!$id_hapus) {
    header("Location: pemilik_kos_daftar.php?feedback=" . urlencode("ID Pemilik Kos untuk hapus tidak valid.") . "&type=error");
    exit;
}

$stmt_check_kos = mysqli_prepare($koneksi, "SELECT COUNT(*) as total_kos FROM kos WHERE id_pemilik = ?");
mysqli_stmt_bind_param($stmt_check_kos, "s", $id_hapus);
mysqli_stmt_execute($stmt_check_kos);
$result_check_kos = mysqli_stmt_get_result($stmt_check_kos);
$row_check_kos = mysqli_fetch_assoc($result_check_kos);
mysqli_stmt_close($stmt_check_kos);

if ($row_check_kos['total_kos'] > 0) {
    $pesan_feedback = "Gagal menghapus: Pemilik ini masih memiliki data kos terdaftar (" . $row_check_kos['total_kos'] . " unit). Hapus dulu data kos terkait atau ubah kepemilikannya.";
    $feedback_type = 'error';
    header("Location: pemilik_kos_daftar.php?feedback=" . urlencode($pesan_feedback) . "&type=" . $feedback_type);
    exit;
} else {
    $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM pemilik_kos WHERE id_pemilik = ?");
    mysqli_stmt_bind_param($stmt_delete, "s", $id_hapus);

    if (mysqli_stmt_execute($stmt_delete)) {
        mysqli_stmt_close($stmt_delete);
        header("Location: pemilik_kos_daftar.php?feedback=" . urlencode("Data pemilik kos berhasil dihapus.") . "&type=sukses");
        exit;
    } else {
        $error_msg = mysqli_error($koneksi);
        mysqli_stmt_close($stmt_delete);
        $pesan_feedback = "Gagal menghapus data pemilik kos: " . $error_msg;
        $feedback_type = 'error';
        header("Location: pemilik_kos_daftar.php?feedback=" . urlencode($pesan_feedback) . "&type=" . $feedback_type);
        exit;
    }
}
?>
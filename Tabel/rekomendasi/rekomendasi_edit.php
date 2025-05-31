<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;

if (!$id_edit) {
    header("Location: rekomendasi_daftar.php?feedback=" . urlencode("ID Rekomendasi untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM rekomendasi WHERE id_rekomendasi = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: rekomendasi_daftar.php?feedback=" . urlencode("Data rekomendasi tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna = $_POST['id_pengguna'] ?? $data_edit['id_pengguna'];
    $id_kos = $_POST['id_kos'] ?? $data_edit['id_kos'];
    $skor_relevansi = $_POST['skor_relevansi'] ?? $data_edit['skor_relevansi'];
    
    $skor_relevansi_float = filter_var($skor_relevansi, FILTER_VALIDATE_FLOAT);

    if (empty($id_pengguna) || empty($id_kos) || $skor_relevansi_float === false) {
        $pesan_feedback = "ID Pengguna, ID Kos, dan Skor Relevansi (angka) wajib diisi dengan benar.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST); 
        if ($skor_relevansi_float !== false) $data_edit['skor_relevansi'] = $skor_relevansi_float; else $data_edit['skor_relevansi'] = $skor_relevansi;
    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE rekomendasi SET id_pengguna = ?, id_kos = ?, skor_relevansi = ? WHERE id_rekomendasi = ?");
        mysqli_stmt_bind_param($stmt_update, "ssds", $id_pengguna, $id_kos, $skor_relevansi_float, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: rekomendasi_daftar.php?feedback=" . urlencode("Data rekomendasi berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data rekomendasi: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST);
            if ($skor_relevansi_float !== false) $data_edit['skor_relevansi'] = $skor_relevansi_float; else $data_edit['skor_relevansi'] = $skor_relevansi;
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Rekomendasi';
    document.title = 'Edit Data Rekomendasi - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Data Rekomendasi (ID: <?= htmlspecialchars($data_edit['id_rekomendasi']) ?>)
    </h2>
    <form method="POST" action="rekomendasi_edit.php?id=<?= htmlspecialchars($data_edit['id_rekomendasi']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Contoh: P001"
                   value="<?= htmlspecialchars($data_edit['id_pengguna'] ?? '') ?>" required pattern="P[0-9]{3}" title="Format ID Pengguna: P diikuti 3 angka"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="id_kos" class="block text-sm font-medium text-green-200 mb-1">ID Kos</label>
            <input type="text" name="id_kos" id="id_kos" placeholder="Contoh: K001"
                   value="<?= htmlspecialchars($data_edit['id_kos'] ?? '') ?>" required pattern="K[0-9]{3}" title="Format ID Kos: K diikuti 3 angka"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="skor_relevansi" class="block text-sm font-medium text-green-200 mb-1">Skor Relevansi</label>
            <input type="number" step="0.1" name="skor_relevansi" id="skor_relevansi" placeholder="Contoh: 4.5"
                   value="<?= htmlspecialchars($data_edit['skor_relevansi'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="rekomendasi_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-yellow-600 hover:bg-yellow-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="save" class="w-5 h-5 mr-2"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
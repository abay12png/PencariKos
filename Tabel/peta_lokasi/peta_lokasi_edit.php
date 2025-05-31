<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;

if (!$id_edit) {
    header("Location: peta_lokasi_daftar.php?feedback=" . urlencode("ID Lokasi untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM peta_lokasi WHERE id_lokasi = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: peta_lokasi_daftar.php?feedback=" . urlencode("Data lokasi tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kos = $_POST['id_kos'] ?? '';
    $koordinat = $_POST['koordinat'] ?? '';

    if (empty($id_kos) || empty($koordinat)) {
        $pesan_feedback = "ID Kos dan Koordinat wajib diisi.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST); 
    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE peta_lokasi SET id_kos = ?, koordinat = ? WHERE id_lokasi = ?");
        mysqli_stmt_bind_param($stmt_update, "sss", $id_kos, $koordinat, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: peta_lokasi_daftar.php?feedback=" . urlencode("Data lokasi berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data lokasi: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST);
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Lokasi Kos';
    document.title = 'Edit Lokasi Kos - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Data Lokasi Kos (ID: <?= htmlspecialchars($data_edit['id_lokasi']) ?>)
    </h2>
    <form method="POST" action="peta_lokasi_edit.php?id=<?= htmlspecialchars($data_edit['id_lokasi']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_kos" class="block text-sm font-medium text-green-200 mb-1">ID Kos Terkait</label>
            <input type="text" name="id_kos" id="id_kos" placeholder="Contoh: K001"
                   value="<?= htmlspecialchars($data_edit['id_kos'] ?? '') ?>" required pattern="K[0-9]{3}" title="Format ID Kos: K diikuti 3 angka (misal K001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
             <p class="mt-1 text-xs text-green-400">Masukkan ID Kos yang valid.</p>
        </div>
        <div>
            <label for="koordinat" class="block text-sm font-medium text-green-200 mb-1">Koordinat (Latitude, Longitude)</label>
            <input type="text" name="koordinat" id="koordinat" placeholder="Contoh: -6.200000, 106.816666"
                   value="<?= htmlspecialchars($data_edit['koordinat'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Format: Teks bebas, misal: Latitude: -7.123, Longitude: 110.123 atau cukup -7.123,110.123</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="peta_lokasi_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
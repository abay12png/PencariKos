<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;

if (!$id_edit) {
    header("Location: fasilitas_daftar.php?feedback=" . urlencode("ID Fasilitas untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM fasilitas WHERE id_fasilitas = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: fasilitas_daftar.php?feedback=" . urlencode("Data paket fasilitas tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_fasilitas = $_POST['nama_fasilitas'] ?? null;
    $nama_fasilitas2 = $_POST['nama_fasilitas2'] ?? null;
    $nama_fasilitas3 = $_POST['nama_fasilitas3'] ?? null;
    
    $data_edit['nama_fasilitas'] = $nama_fasilitas;
    $data_edit['nama_fasilitas2'] = $nama_fasilitas2;
    $data_edit['nama_fasilitas3'] = $nama_fasilitas3;


    if (empty($nama_fasilitas) && empty($nama_fasilitas2) && empty($nama_fasilitas3)) {
        $pesan_feedback = "Minimal satu nama fasilitas harus diisi.";
        $feedback_type = 'error';
    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE fasilitas SET nama_fasilitas = ?, nama_fasilitas2 = ?, nama_fasilitas3 = ? WHERE id_fasilitas = ?");
        mysqli_stmt_bind_param($stmt_update, "ssss", $nama_fasilitas, $nama_fasilitas2, $nama_fasilitas3, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: fasilitas_daftar.php?feedback=" . urlencode("Data paket fasilitas berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data paket fasilitas: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Paket Fasilitas';
    document.title = 'Edit Paket Fasilitas - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Paket Fasilitas (ID: <?= htmlspecialchars($data_edit['id_fasilitas']) ?>)
    </h2>
    <form method="POST" action="fasilitas_edit.php?id=<?= htmlspecialchars($data_edit['id_fasilitas']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama_fasilitas" class="block text-sm font-medium text-green-200 mb-1">Nama Fasilitas 1</label>
            <input type="text" name="nama_fasilitas" id="nama_fasilitas" placeholder="Contoh: AC (Wajib isi minimal 1)"
                   value="<?= htmlspecialchars($data_edit['nama_fasilitas'] ?? '') ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="nama_fasilitas2" class="block text-sm font-medium text-green-200 mb-1">Nama Fasilitas 2 (Opsional)</label>
            <input type="text" name="nama_fasilitas2" id="nama_fasilitas2" placeholder="Contoh: WiFi"
                   value="<?= htmlspecialchars($data_edit['nama_fasilitas2'] ?? '') ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="nama_fasilitas3" class="block text-sm font-medium text-green-200 mb-1">Nama Fasilitas 3 (Opsional)</label>
            <input type="text" name="nama_fasilitas3" id="nama_fasilitas3" placeholder="Contoh: Kamar Mandi Dalam"
                   value="<?= htmlspecialchars($data_edit['nama_fasilitas3'] ?? '') ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="fasilitas_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
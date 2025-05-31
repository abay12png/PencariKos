<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;

if (!$id_edit) {
    header("Location: pembayaran_daftar.php?feedback=" . urlencode("ID Pembayaran untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM pembayaran WHERE id_pembayaran = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: pembayaran_daftar.php?feedback=" . urlencode("Data pembayaran tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_reservasi = $_POST['id_reservasi'] ?? $data_edit['id_reservasi'];
    $total_pembayaran = $_POST['total_pembayaran'] ?? $data_edit['total_pembayaran'];
    $id_pengguna = $_POST['id_pengguna'] ?? $data_edit['id_pengguna'];
    
    $total_pembayaran_int = filter_var($total_pembayaran, FILTER_VALIDATE_INT);

    if (empty($id_reservasi) || $total_pembayaran_int === false || $total_pembayaran_int <= 0 || empty($id_pengguna)) {
        $pesan_feedback = "ID Reservasi, Total Pembayaran (angka positif), dan ID Pengguna wajib diisi.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST); 
        $data_edit['total_pembayaran'] = $total_pembayaran; 
    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE pembayaran SET id_reservasi = ?, total_pembayaran = ?, id_pengguna = ? WHERE id_pembayaran = ?");
        mysqli_stmt_bind_param($stmt_update, "siss", $id_reservasi, $total_pembayaran_int, $id_pengguna, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: pembayaran_daftar.php?feedback=" . urlencode("Data pembayaran berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data pembayaran: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST); 
            $data_edit['total_pembayaran'] = $total_pembayaran; 
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Pembayaran';
    document.title = 'Edit Data Pembayaran - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Data Pembayaran (ID: <?= htmlspecialchars($data_edit['id_pembayaran']) ?>)
    </h2>
    <form method="POST" action="pembayaran_edit.php?id=<?= htmlspecialchars($data_edit['id_pembayaran']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_reservasi" class="block text-sm font-medium text-green-200 mb-1">ID Reservasi</label>
            <input type="text" name="id_reservasi" id="id_reservasi" placeholder="Contoh: RSV001"
                   value="<?= htmlspecialchars($data_edit['id_reservasi'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna (Pembayar)</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Contoh: P001"
                   value="<?= htmlspecialchars($data_edit['id_pengguna'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="total_pembayaran" class="block text-sm font-medium text-green-200 mb-1">Total Pembayaran (Rp)</label>
            <input type="number" name="total_pembayaran" id="total_pembayaran" placeholder="Contoh: 500000"
                   value="<?= htmlspecialchars($data_edit['total_pembayaran'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="pembayaran_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
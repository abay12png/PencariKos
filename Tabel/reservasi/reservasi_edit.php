<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;
$status_options = ['pending', 'disetujui', 'ditolak', 'dibatalkan', 'selesai', 'menunggu_pembayaran'];


if (!$id_edit) {
    header("Location: reservasi_daftar.php?feedback=" . urlencode("ID Reservasi untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM reservasi WHERE id_reservasi = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: reservasi_daftar.php?feedback=" . urlencode("Data reservasi tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna = $_POST['id_pengguna'] ?? $data_edit['id_pengguna'];
    $id_kos = $_POST['id_kos'] ?? $data_edit['id_kos'];
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? $data_edit['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? $data_edit['tanggal_selesai'];
    $status = $_POST['status'] ?? $data_edit['status'];

    if (empty($id_pengguna) || empty($id_kos) || empty($tanggal_mulai) || empty($tanggal_selesai) || empty($status)) {
        $pesan_feedback = "Semua field (ID Pengguna, ID Kos, Tanggal Mulai, Tanggal Selesai, Status) wajib diisi.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST); 
    } elseif (strtotime($tanggal_selesai) <= strtotime($tanggal_mulai)) {
        $pesan_feedback = "Tanggal Selesai harus setelah Tanggal Mulai.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST);
    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE reservasi SET id_pengguna = ?, id_kos = ?, tanggal_mulai = ?, tanggal_selesai = ?, status = ? WHERE id_reservasi = ?");
        mysqli_stmt_bind_param($stmt_update, "ssssss", $id_pengguna, $id_kos, $tanggal_mulai, $tanggal_selesai, $status, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: reservasi_daftar.php?feedback=" . urlencode("Data reservasi berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data reservasi: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST); 
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Reservasi';
    document.title = 'Edit Data Reservasi - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-xl mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Data Reservasi (ID: <?= htmlspecialchars($data_edit['id_reservasi']) ?>)
    </h2>
    <form method="POST" action="reservasi_edit.php?id=<?= htmlspecialchars($data_edit['id_reservasi']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna</label>
            <input type="text" name="id_pengguna" id="id_pengguna"
                   value="<?= htmlspecialchars($data_edit['id_pengguna'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="id_kos" class="block text-sm font-medium text-green-200 mb-1">ID Kos</label>
            <input type="text" name="id_kos" id="id_kos"
                   value="<?= htmlspecialchars($data_edit['id_kos'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="tanggal_mulai" class="block text-sm font-medium text-green-200 mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                       value="<?= htmlspecialchars($data_edit['tanggal_mulai'] ?? '') ?>" required
                       class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
            <div>
                <label for="tanggal_selesai" class="block text-sm font-medium text-green-200 mb-1">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                       value="<?= htmlspecialchars($data_edit['tanggal_selesai'] ?? '') ?>" required
                       class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
        </div>
         <div>
            <label for="status" class="block text-sm font-medium text-green-200 mb-1">Status Reservasi</label>
            <select name="status" id="status" required
                    class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <?php
                $current_status_val = $data_edit['status'] ?? '';
                foreach ($status_options as $status_val) {
                    $selected = ($current_status_val == $status_val) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($status_val) . "\" $selected>" . ucfirst(str_replace('_', ' ', htmlspecialchars($status_val))) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="reservasi_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
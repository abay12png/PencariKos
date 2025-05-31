<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;
$status_options = ['belum_dibaca', 'sudah_dibaca', 'penting', 'info'];


if (!$id_edit) {
    header("Location: notifikasi_daftar.php?feedback=" . urlencode("ID Notifikasi untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM notifikasi WHERE id_notifikasi = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: notifikasi_daftar.php?feedback=" . urlencode("Data notifikasi tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna_form = !empty($_POST['id_pengguna']) ? $_POST['id_pengguna'] : null;
    $id_pemilik_form = !empty($_POST['id_pemilik']) ? $_POST['id_pemilik'] : null;
    $pesan_form = $_POST['pesan'] ?? '';
    $status_form = $_POST['status'] ?? $data_edit['status'];

    if (empty($pesan_form)) {
        $pesan_feedback = "Pesan notifikasi wajib diisi.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST); 
    } elseif (empty($id_pengguna_form) && empty($id_pemilik_form)) {
        $pesan_feedback = "Minimal salah satu ID (Pengguna atau Pemilik) harus diisi.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST);
    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE notifikasi SET id_pengguna = ?, id_pemilik = ?, pesan = ?, status = ? WHERE id_notifikasi = ?");
        mysqli_stmt_bind_param($stmt_update, "sssss", $id_pengguna_form, $id_pemilik_form, $pesan_form, $status_form, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: notifikasi_daftar.php?feedback=" . urlencode("Data notifikasi berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data notifikasi: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST); 
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Notifikasi';
    document.title = 'Edit Data Notifikasi - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-xl mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Notifikasi (ID: <?= htmlspecialchars($data_edit['id_notifikasi']) ?>)
    </h2>
    <form method="POST" action="notifikasi_edit.php?id=<?= htmlspecialchars($data_edit['id_notifikasi']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna (Opsional)</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Kosongkan jika tidak ditujukan ke pengguna spesifik"
                   value="<?= htmlspecialchars($data_edit['id_pengguna'] ?? '') ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="id_pemilik" class="block text-sm font-medium text-green-200 mb-1">ID Pemilik Kos (Opsional)</label>
            <input type="text" name="id_pemilik" id="id_pemilik" placeholder="Kosongkan jika tidak ditujukan ke pemilik spesifik"
                   value="<?= htmlspecialchars($data_edit['id_pemilik'] ?? '') ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="pesan" class="block text-sm font-medium text-green-200 mb-1">Pesan Notifikasi</label>
            <textarea name="pesan" id="pesan" rows="4" placeholder="Tulis isi pesan notifikasi di sini..." required
                      class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"><?= htmlspecialchars($data_edit['pesan'] ?? '') ?></textarea>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-green-200 mb-1">Status Notifikasi</label>
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
            <a href="notifikasi_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
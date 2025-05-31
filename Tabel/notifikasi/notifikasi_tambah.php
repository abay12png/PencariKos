<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'id_pengguna' => '',
    'id_pemilik' => '',
    'pesan' => '',
    'status' => 'belum_dibaca'
];
$status_options = ['belum_dibaca', 'sudah_dibaca', 'penting', 'info'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['id_pengguna'] = !empty($_POST['id_pengguna']) ? $_POST['id_pengguna'] : null;
    $form_data['id_pemilik'] = !empty($_POST['id_pemilik']) ? $_POST['id_pemilik'] : null;
    $form_data['pesan'] = $_POST['pesan'] ?? '';
    $form_data['status'] = $_POST['status'] ?? 'belum_dibaca';

    if (empty($form_data['pesan'])) {
        $pesan_feedback = "Pesan notifikasi wajib diisi.";
        $feedback_type = 'error';
    } elseif (empty($form_data['id_pengguna']) && empty($form_data['id_pemilik'])) {
        $pesan_feedback = "Minimal salah satu ID (Pengguna atau Pemilik) harus diisi sebagai target notifikasi.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_notifikasi FROM notifikasi ORDER BY id_notifikasi DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_notifikasi'] ?? 'N000';
        $number = (int) substr($lastID, 1) + 1;
        $newIDNotifikasi = 'N' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO notifikasi (id_notifikasi, id_pengguna, id_pemilik, pesan, status) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "sssss", $newIDNotifikasi, $form_data['id_pengguna'], $form_data['id_pemilik'], $form_data['pesan'], $form_data['status']);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: notifikasi_daftar.php?feedback=" . urlencode("Notifikasi berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah notifikasi: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Notifikasi';
    document.title = 'Tambah Notifikasi Baru - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-xl mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Notifikasi Baru
    </h2>
    <form method="POST" action="notifikasi_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna (Opsional)</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Kosongkan jika notif untuk pemilik/umum"
                   value="<?= htmlspecialchars($form_data['id_pengguna']) ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Untuk notifikasi ke pengguna spesifik.</p>
        </div>
        <div>
            <label for="id_pemilik" class="block text-sm font-medium text-green-200 mb-1">ID Pemilik Kos (Opsional)</label>
            <input type="text" name="id_pemilik" id="id_pemilik" placeholder="Kosongkan jika notif untuk pengguna/umum"
                   value="<?= htmlspecialchars($form_data['id_pemilik']) ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Untuk notifikasi ke pemilik kos spesifik.</p>
        </div>
        <div>
            <label for="pesan" class="block text-sm font-medium text-green-200 mb-1">Pesan Notifikasi</label>
            <textarea name="pesan" id="pesan" rows="4" placeholder="Tulis isi pesan notifikasi di sini..." required
                      class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"><?= htmlspecialchars($form_data['pesan']) ?></textarea>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-green-200 mb-1">Status Notifikasi</label>
            <select name="status" id="status" required
                    class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <?php
                foreach ($status_options as $status_val) {
                    $selected = ($form_data['status'] == $status_val) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($status_val) . "\" $selected>" . ucfirst(str_replace('_', ' ', htmlspecialchars($status_val))) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="notifikasi_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="send" class="w-5 h-5 mr-2"></i> Kirim Notifikasi
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
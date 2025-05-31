<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'id_pengguna' => '',
    'id_kos' => '',
    'skor_relevansi' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['id_pengguna'] = $_POST['id_pengguna'] ?? '';
    $form_data['id_kos'] = $_POST['id_kos'] ?? '';
    $form_data['skor_relevansi'] = $_POST['skor_relevansi'] ?? '';
    
    $skor_relevansi_float = filter_var($form_data['skor_relevansi'], FILTER_VALIDATE_FLOAT);

    if (empty($form_data['id_pengguna']) || empty($form_data['id_kos']) || $skor_relevansi_float === false) {
        $pesan_feedback = "ID Pengguna, ID Kos, dan Skor Relevansi (angka) wajib diisi dengan benar.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_rekomendasi FROM rekomendasi ORDER BY id_rekomendasi DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_rekomendasi'] ?? 'R000';
        $number = (int) substr($lastID, 1) + 1;
        $newIDRekomendasi = 'R' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO rekomendasi (id_rekomendasi, id_pengguna, id_kos, skor_relevansi) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "sssd", $newIDRekomendasi, $form_data['id_pengguna'], $form_data['id_kos'], $skor_relevansi_float);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: rekomendasi_daftar.php?feedback=" . urlencode("Data rekomendasi berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah data rekomendasi: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Rekomendasi';
    document.title = 'Tambah Rekomendasi Baru - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Rekomendasi Baru
    </h2>
    <form method="POST" action="rekomendasi_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Contoh: P001"
                   value="<?= htmlspecialchars($form_data['id_pengguna']) ?>" required pattern="P[0-9]{3}" title="Format ID Pengguna: P diikuti 3 angka"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID Pengguna yang valid.</p>
        </div>
        <div>
            <label for="id_kos" class="block text-sm font-medium text-green-200 mb-1">ID Kos</label>
            <input type="text" name="id_kos" id="id_kos" placeholder="Contoh: K001"
                   value="<?= htmlspecialchars($form_data['id_kos']) ?>" required pattern="K[0-9]{3}" title="Format ID Kos: K diikuti 3 angka"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID Kos yang valid.</p>
        </div>
        <div>
            <label for="skor_relevansi" class="block text-sm font-medium text-green-200 mb-1">Skor Relevansi</label>
            <input type="number" step="0.1" name="skor_relevansi" id="skor_relevansi" placeholder="Contoh: 4.5"
                   value="<?= htmlspecialchars($form_data['skor_relevansi']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan skor berupa angka (bisa desimal, misal 3.5 atau 4.0).</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="rekomendasi_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="list-plus" class="w-5 h-5 mr-2"></i> Tambah Rekomendasi
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
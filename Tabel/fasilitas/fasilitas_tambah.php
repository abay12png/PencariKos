<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'nama_fasilitas' => '',
    'nama_fasilitas2' => '',
    'nama_fasilitas3' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['nama_fasilitas'] = $_POST['nama_fasilitas'] ?? null;
    $form_data['nama_fasilitas2'] = $_POST['nama_fasilitas2'] ?? null;
    $form_data['nama_fasilitas3'] = $_POST['nama_fasilitas3'] ?? null;

    if (empty($form_data['nama_fasilitas']) && empty($form_data['nama_fasilitas2']) && empty($form_data['nama_fasilitas3'])) {
        $pesan_feedback = "Minimal satu nama fasilitas harus diisi.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_fasilitas FROM fasilitas ORDER BY id_fasilitas DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_fasilitas'] ?? 'F000';
        $number = (int) substr($lastID, 1) + 1;
        $newIDFasilitas = 'F' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO fasilitas (id_fasilitas, nama_fasilitas, nama_fasilitas2, nama_fasilitas3) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ssss", $newIDFasilitas, $form_data['nama_fasilitas'], $form_data['nama_fasilitas2'], $form_data['nama_fasilitas3']);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: fasilitas_daftar.php?feedback=" . urlencode("Paket fasilitas berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah paket fasilitas: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Paket Fasilitas';
    document.title = 'Tambah Paket Fasilitas - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Paket Fasilitas Baru
    </h2>
    <form method="POST" action="fasilitas_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama_fasilitas" class="block text-sm font-medium text-green-200 mb-1">Nama Fasilitas 1</label>
            <input type="text" name="nama_fasilitas" id="nama_fasilitas" placeholder="Contoh: AC (Wajib isi minimal 1)"
                   value="<?= htmlspecialchars($form_data['nama_fasilitas']) ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="nama_fasilitas2" class="block text-sm font-medium text-green-200 mb-1">Nama Fasilitas 2 (Opsional)</label>
            <input type="text" name="nama_fasilitas2" id="nama_fasilitas2" placeholder="Contoh: WiFi"
                   value="<?= htmlspecialchars($form_data['nama_fasilitas2']) ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="nama_fasilitas3" class="block text-sm font-medium text-green-200 mb-1">Nama Fasilitas 3 (Opsional)</label>
            <input type="text" name="nama_fasilitas3" id="nama_fasilitas3" placeholder="Contoh: Kamar Mandi Dalam"
                   value="<?= htmlspecialchars($form_data['nama_fasilitas3']) ?>"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="fasilitas_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i> Tambah Paket
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
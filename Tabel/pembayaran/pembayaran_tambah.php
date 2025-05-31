<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'id_reservasi' => '',
    'total_pembayaran' => '',
    'id_pengguna' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['id_reservasi'] = $_POST['id_reservasi'] ?? '';
    $form_data['total_pembayaran'] = $_POST['total_pembayaran'] ?? '';
    $form_data['id_pengguna'] = $_POST['id_pengguna'] ?? '';
    
    $total_pembayaran_int = filter_var($form_data['total_pembayaran'], FILTER_VALIDATE_INT);

    if (empty($form_data['id_reservasi']) || $total_pembayaran_int === false || $total_pembayaran_int <= 0 || empty($form_data['id_pengguna'])) {
        $pesan_feedback = "ID Reservasi, Total Pembayaran (angka positif), dan ID Pengguna wajib diisi.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_pembayaran FROM pembayaran ORDER BY id_pembayaran DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_pembayaran'] ?? 'PB000';
        $number = (int) substr($lastID, 2) + 1;
        $newIDPembayaran = 'PB' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO pembayaran (id_pembayaran, id_reservasi, total_pembayaran, id_pengguna) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ssis", $newIDPembayaran, $form_data['id_reservasi'], $total_pembayaran_int, $form_data['id_pengguna']);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: pembayaran_daftar.php?feedback=" . urlencode("Data pembayaran berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah data pembayaran: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Pembayaran';
    document.title = 'Tambah Pembayaran Manual - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Catat Pembayaran Manual
    </h2>
    <form method="POST" action="pembayaran_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_reservasi" class="block text-sm font-medium text-green-200 mb-1">ID Reservasi</label>
            <input type="text" name="id_reservasi" id="id_reservasi" placeholder="Contoh: RSV001"
                   value="<?= htmlspecialchars($form_data['id_reservasi']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID Reservasi yang terkait.</p>
        </div>
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna (Pembayar)</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Contoh: P001"
                   value="<?= htmlspecialchars($form_data['id_pengguna']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID Pengguna yang melakukan pembayaran.</p>
        </div>
        <div>
            <label for="total_pembayaran" class="block text-sm font-medium text-green-200 mb-1">Total Pembayaran (Rp)</label>
            <input type="number" name="total_pembayaran" id="total_pembayaran" placeholder="Contoh: 500000"
                   value="<?= htmlspecialchars($form_data['total_pembayaran']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="pembayaran_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i> Catat Pembayaran
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
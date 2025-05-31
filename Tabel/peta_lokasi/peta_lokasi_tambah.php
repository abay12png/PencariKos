<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'id_kos' => '',
    'koordinat' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['id_kos'] = $_POST['id_kos'] ?? '';
    $form_data['koordinat'] = $_POST['koordinat'] ?? '';

    if (empty($form_data['id_kos']) || empty($form_data['koordinat'])) {
        $pesan_feedback = "ID Kos dan Koordinat wajib diisi.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_lokasi FROM peta_lokasi ORDER BY id_lokasi DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_lokasi'] ?? 'L000';
        $number = (int) substr($lastID, 1) + 1;
        $newIDLokasi = 'L' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO peta_lokasi (id_lokasi, id_kos, koordinat) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "sss", $newIDLokasi, $form_data['id_kos'], $form_data['koordinat']);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: peta_lokasi_daftar.php?feedback=" . urlencode("Data lokasi berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah data lokasi: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Lokasi Kos';
    document.title = 'Tambah Lokasi Kos - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Data Lokasi Kos
    </h2>
    <form method="POST" action="peta_lokasi_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_kos" class="block text-sm font-medium text-green-200 mb-1">ID Kos Terkait</label>
            <input type="text" name="id_kos" id="id_kos" placeholder="Contoh: K001"
                   value="<?= htmlspecialchars($form_data['id_kos']) ?>" required pattern="K[0-9]{3}" title="Format ID Kos: K diikuti 3 angka (misal K001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID Kos yang valid.</p>
        </div>
        <div>
            <label for="koordinat" class="block text-sm font-medium text-green-200 mb-1">Koordinat (Latitude, Longitude)</label>
            <input type="text" name="koordinat" id="koordinat" placeholder="Contoh: -6.200000, 106.816666"
                   value="<?= htmlspecialchars($form_data['koordinat']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Format: Teks bebas, misal: Latitude: -7.123, Longitude: 110.123 atau cukup -7.123,110.123</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="peta_lokasi_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="map-pin-plus" class="w-5 h-5 mr-2"></i> Tambah Lokasi
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
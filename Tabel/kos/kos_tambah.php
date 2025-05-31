<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'nama_kos' => '',
    'alamat' => '',
    'harga' => '',
    'tipe_kos' => 'Putra',
    'status' => 'Tersedia',
    'id_fasilitas' => '', // Menggantikan fasilitas teks
    'id_pemilik' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['nama_kos'] = $_POST['nama_kos'] ?? '';
    $form_data['alamat'] = $_POST['alamat'] ?? '';
    $form_data['harga'] = $_POST['harga'] ?? '';
    $form_data['tipe_kos'] = $_POST['tipe_kos'] ?? 'Putra';
    $form_data['status'] = $_POST['status'] ?? 'Tersedia';
    $form_data['id_fasilitas'] = $_POST['id_fasilitas'] ?? ''; // Mengambil id_fasilitas
    $form_data['id_pemilik'] = $_POST['id_pemilik'] ?? '';

    $harga_int = filter_var($form_data['harga'], FILTER_VALIDATE_INT);

    if (empty($form_data['nama_kos']) || empty($form_data['alamat']) || $harga_int === false || $harga_int <= 0 || empty($form_data['tipe_kos']) || empty($form_data['status']) || empty($form_data['id_fasilitas']) || empty($form_data['id_pemilik'])) {
        $pesan_feedback = "Semua field wajib diisi dengan benar. Harga harus berupa angka positif. Pastikan ID Fasilitas juga diisi.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_kos FROM kos ORDER BY id_kos DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_kos'] ?? 'K000';
        $number = (int) substr($lastID, 1) + 1;
        $newID = 'K' . str_pad($number, 3, '0', STR_PAD_LEFT);

        // Query INSERT diubah untuk menggunakan id_fasilitas
        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO kos (id_kos, nama_kos, alamat, harga, tipe_kos, status, id_pemilik, id_fasilitas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        // Bind parameter disesuaikan, urutan id_pemilik dan id_fasilitas disesuaikan dengan tabel kos baru
        mysqli_stmt_bind_param($stmt_insert, "sssissss", $newID, $form_data['nama_kos'], $form_data['alamat'], $harga_int, $form_data['tipe_kos'], $form_data['status'], $form_data['id_pemilik'], $form_data['id_fasilitas']);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: kos_daftar.php?feedback=" . urlencode("Data kos berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah data kos: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Data Kos';
    document.title = 'Tambah Data Kos - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-2xl mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Data Kos Baru
    </h2>
    <form method="POST" action="kos_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama_kos" class="block text-sm font-medium text-green-200 mb-1">Nama Kos</label>
            <input type="text" name="nama_kos" id="nama_kos" placeholder="Contoh: Kos Melati Indah"
                   value="<?= htmlspecialchars($form_data['nama_kos']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="alamat" class="block text-sm font-medium text-green-200 mb-1">Alamat</label>
            <input type="text" name="alamat" id="alamat" placeholder="Contoh: Jl. Kenanga No. 10"
                   value="<?= htmlspecialchars($form_data['alamat']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="harga" class="block text-sm font-medium text-green-200 mb-1">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" placeholder="Contoh: 750000"
                       value="<?= htmlspecialchars($form_data['harga']) ?>" required
                       class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
            <div>
                <label for="tipe_kos" class="block text-sm font-medium text-green-200 mb-1">Tipe Kos</label>
                <select name="tipe_kos" id="tipe_kos" required
                        class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                    <option value="Putra" <?= ($form_data['tipe_kos'] == 'Putra') ? 'selected' : '' ?>>Putra</option>
                    <option value="Putri" <?= ($form_data['tipe_kos'] == 'Putri') ? 'selected' : '' ?>>Putri</option>
                    <option value="Campur" <?= ($form_data['tipe_kos'] == 'Campur') ? 'selected' : '' ?>>Campur</option>
                </select>
            </div>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-green-200 mb-1">Status Ketersediaan</label>
            <select name="status" id="status" required
                    class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <option value="Tersedia" <?= ($form_data['status'] == 'Tersedia') ? 'selected' : '' ?>>Tersedia</option>
                <option value="Penuh" <?= ($form_data['status'] == 'Penuh') ? 'selected' : '' ?>>Penuh</option>
            </select>
        </div>
        <div>
            <label for="id_fasilitas" class="block text-sm font-medium text-green-200 mb-1">ID Fasilitas</label>
            <input type="text" name="id_fasilitas" id="id_fasilitas" placeholder="Contoh: F001 (dari tabel fasilitas)"
                   value="<?= htmlspecialchars($form_data['id_fasilitas']) ?>" required pattern="F[0-9]{3}" title="Format ID Fasilitas: F diikuti 3 angka (misal F001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID dari tabel Fasilitas.</p>
        </div>
         <div>
            <label for="id_pemilik" class="block text-sm font-medium text-green-200 mb-1">ID Pemilik</label>
            <input type="text" name="id_pemilik" id="id_pemilik" placeholder="Contoh: PK001"
                   value="<?= htmlspecialchars($form_data['id_pemilik']) ?>" required pattern="PK[0-9]{3}" title="Format ID Pemilik: PK diikuti 3 angka (misal PK001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID Pemilik yang valid.</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="kos_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i> Tambah Kos
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
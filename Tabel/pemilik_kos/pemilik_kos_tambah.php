<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'nama_pemilik' => '',
    'kontak' => '',
    'id_pengguna' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['nama_pemilik'] = $_POST['nama_pemilik'] ?? '';
    $form_data['kontak'] = $_POST['kontak'] ?? '';
    $form_data['id_pengguna'] = $_POST['id_pengguna'] ?? '';

    if (empty($form_data['nama_pemilik']) || empty($form_data['kontak']) || empty($form_data['id_pengguna'])) {
        $pesan_feedback = "Semua field (Nama Pemilik, Kontak, ID Pengguna) wajib diisi.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_pemilik FROM pemilik_kos ORDER BY id_pemilik DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_pemilik'] ?? 'PK000';
        $number = (int) substr($lastID, 2) + 1;
        $newIDPemilik = 'PK' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO pemilik_kos (id_pemilik, nama_pemmilik, kontak, id_pengguna) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ssss", $newIDPemilik, $form_data['nama_pemilik'], $form_data['kontak'], $form_data['id_pengguna']);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: pemilik_kos_daftar.php?feedback=" . urlencode("Data pemilik kos berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah data pemilik kos: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Pemilik Kos';
    document.title = 'Tambah Pemilik Kos - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Pemilik Kos Baru
    </h2>
    <form method="POST" action="pemilik_kos_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama_pemilik" class="block text-sm font-medium text-green-200 mb-1">Nama Pemilik</label>
            <input type="text" name="nama_pemilik" id="nama_pemilik" placeholder="Contoh: Budi Santoso"
                   value="<?= htmlspecialchars($form_data['nama_pemilik']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="kontak" class="block text-sm font-medium text-green-200 mb-1">Nomor Kontak (WA)</label>
            <input type="tel" name="kontak" id="kontak" placeholder="Contoh: 081234567890"
                   value="<?= htmlspecialchars($form_data['kontak']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Akun Pengguna Terkait</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Contoh: P001 (dari tabel pengguna)"
                   value="<?= htmlspecialchars($form_data['id_pengguna']) ?>" required pattern="P[0-9]{3}" title="Format ID Pengguna: P diikuti 3 angka (misal P001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID dari tabel Pengguna yang valid.</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="pemilik_kos_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="user-plus" class="w-5 h-5 mr-2"></i> Tambah Pemilik
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
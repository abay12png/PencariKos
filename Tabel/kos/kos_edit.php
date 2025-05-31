<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;

if (!$id_edit) {
    header("Location: kos_daftar.php?feedback=" . urlencode("ID Kos untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM kos WHERE id_kos = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: kos_daftar.php?feedback=" . urlencode("Data kos tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kos = $_POST['nama_kos'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $harga = $_POST['harga'] ?? '';
    $tipe_kos = $_POST['tipe_kos'] ?? 'Putra';
    $status = $_POST['status'] ?? 'Tersedia';
    $id_fasilitas = $_POST['id_fasilitas'] ?? ''; // Mengambil id_fasilitas
    $id_pemilik = $_POST['id_pemilik'] ?? '';

    $harga_int = filter_var($harga, FILTER_VALIDATE_INT);

    if (empty($nama_kos) || empty($alamat) || $harga_int === false || $harga_int <= 0 || empty($tipe_kos) || empty($status) || empty($id_fasilitas) || empty($id_pemilik)) {
        $pesan_feedback = "Semua field wajib diisi dengan benar. Harga harus berupa angka positif. Pastikan ID Fasilitas juga diisi.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST); 
        $data_edit['harga'] = $harga; 
    } else {
        // Query UPDATE diubah untuk menggunakan id_fasilitas
        $stmt_update = mysqli_prepare($koneksi, "UPDATE kos SET nama_kos=?, alamat=?, harga=?, tipe_kos=?, status=?, id_pemilik=?, id_fasilitas=? WHERE id_kos=?");
        // Bind parameter disesuaikan
        mysqli_stmt_bind_param($stmt_update, "ssisssss", $nama_kos, $alamat, $harga_int, $tipe_kos, $status, $id_pemilik, $id_fasilitas, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: kos_daftar.php?feedback=" . urlencode("Data kos berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data kos: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST);
            $data_edit['harga'] = $harga;
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Data Kos';
    document.title = 'Edit Data Kos - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-2xl mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Data Kos (ID: <?= htmlspecialchars($data_edit['id_kos']) ?>)
    </h2>
    <form method="POST" action="kos_edit.php?id=<?= htmlspecialchars($data_edit['id_kos']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama_kos" class="block text-sm font-medium text-green-200 mb-1">Nama Kos</label>
            <input type="text" name="nama_kos" id="nama_kos" placeholder="Contoh: Kos Melati Indah"
                   value="<?= htmlspecialchars($data_edit['nama_kos'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="alamat" class="block text-sm font-medium text-green-200 mb-1">Alamat</label>
            <input type="text" name="alamat" id="alamat" placeholder="Contoh: Jl. Kenanga No. 10"
                   value="<?= htmlspecialchars($data_edit['alamat'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="harga" class="block text-sm font-medium text-green-200 mb-1">Harga (Rp)</label>
                <input type="number" name="harga" id="harga" placeholder="Contoh: 750000"
                       value="<?= htmlspecialchars($data_edit['harga'] ?? '') ?>" required
                       class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            </div>
            <div>
                <label for="tipe_kos" class="block text-sm font-medium text-green-200 mb-1">Tipe Kos</label>
                <select name="tipe_kos" id="tipe_kos" required
                        class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                    <option value="Putra" <?= ($data_edit['tipe_kos'] ?? '') == 'Putra' ? 'selected' : '' ?>>Putra</option>
                    <option value="Putri" <?= ($data_edit['tipe_kos'] ?? '') == 'Putri' ? 'selected' : '' ?>>Putri</option>
                    <option value="Campur" <?= ($data_edit['tipe_kos'] ?? '') == 'Campur' ? 'selected' : '' ?>>Campur</option>
                </select>
            </div>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-green-200 mb-1">Status Ketersediaan</label>
            <select name="status" id="status" required
                    class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <option value="Tersedia" <?= ($data_edit['status'] ?? '') == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                <option value="Penuh" <?= ($data_edit['status'] ?? '') == 'Penuh' ? 'selected' : '' ?>>Penuh</option>
            </select>
        </div>
        <div>
            <label for="id_fasilitas" class="block text-sm font-medium text-green-200 mb-1">ID Fasilitas</label>
            <input type="text" name="id_fasilitas" id="id_fasilitas" placeholder="Contoh: F001 (dari tabel fasilitas)"
                   value="<?= htmlspecialchars($data_edit['id_fasilitas'] ?? '') ?>" required pattern="F[0-9]{3}" title="Format ID Fasilitas: F diikuti 3 angka (misal F001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID dari tabel Fasilitas. </p>
        </div>
         <div>
            <label for="id_pemilik" class="block text-sm font-medium text-green-200 mb-1">ID Pemilik</label>
            <input type="text" name="id_pemilik" id="id_pemilik" placeholder="Contoh: PK001"
                   value="<?= htmlspecialchars($data_edit['id_pemilik'] ?? '') ?>" required pattern="PK[0-9]{3}" title="Format ID Pemilik: PK diikuti 3 angka (misal PK001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
             <p class="mt-1 text-xs text-green-400">Masukkan ID Pemilik yang valid..</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="kos_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
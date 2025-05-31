<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;

if (!$id_edit) {
    header("Location: pemilik_kos_daftar.php?feedback=" . urlencode("ID Pemilik Kos untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT id_pemilik, nama_pemmilik, kontak, id_pengguna FROM pemilik_kos WHERE id_pemilik = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: pemilik_kos_daftar.php?feedback=" . urlencode("Data pemilik kos tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pemilik = $_POST['nama_pemilik'] ?? '';
    $kontak = $_POST['kontak'] ?? '';
    $id_pengguna = $_POST['id_pengguna'] ?? '';

    if (empty($nama_pemilik) || empty($kontak) || empty($id_pengguna)) {
        $pesan_feedback = "Semua field (Nama Pemilik, Kontak, ID Pengguna) wajib diisi.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST);
    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE pemilik_kos SET nama_pemmilik = ?, kontak = ?, id_pengguna = ? WHERE id_pemilik = ?");
        mysqli_stmt_bind_param($stmt_update, "ssss", $nama_pemilik, $kontak, $id_pengguna, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: pemilik_kos_daftar.php?feedback=" . urlencode("Data pemilik kos berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data pemilik kos: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST);
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Pemilik Kos';
    document.title = 'Edit Pemilik Kos - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Data Pemilik Kos (ID: <?= htmlspecialchars($data_edit['id_pemilik']) ?>)
    </h2>
    <form method="POST" action="pemilik_kos_edit.php?id=<?= htmlspecialchars($data_edit['id_pemilik']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama_pemilik" class="block text-sm font-medium text-green-200 mb-1">Nama Pemilik</label>
            <input type="text" name="nama_pemilik" id="nama_pemilik" placeholder="Contoh: Budi Santoso"
                   value="<?= htmlspecialchars($data_edit['nama_pemmilik'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="kontak" class="block text-sm font-medium text-green-200 mb-1">Nomor Kontak (WA)</label>
            <input type="tel" name="kontak" id="kontak" placeholder="Contoh: 081234567890"
                   value="<?= htmlspecialchars($data_edit['kontak'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Akun Pengguna Terkait</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Contoh: P001 (dari tabel pengguna)"
                   value="<?= htmlspecialchars($data_edit['id_pengguna'] ?? '') ?>" required pattern="P[0-9]{3}" title="Format ID Pengguna: P diikuti 3 angka (misal P001)"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Masukkan ID dari tabel Pengguna yang valid.</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="pemilik_kos_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
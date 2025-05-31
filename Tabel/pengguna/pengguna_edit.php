<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;

if (!$id_edit) {
    header("Location: pengguna_daftar.php?feedback=" . urlencode("ID Pengguna untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT id_pengguna, nama, email FROM pengguna WHERE id_pengguna = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: pengguna_daftar.php?feedback=" . urlencode("Data pengguna tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email_user = $_POST['email_user'] ?? '';
    $password_input = $_POST['password'] ?? '';

    $data_edit['nama'] = $nama; 
    $data_edit['email'] = $email_user;

    if (empty($nama) || empty($email_user)) {
        $pesan_feedback = "Nama dan Email wajib diisi.";
        $feedback_type = 'error';
    } elseif (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
        $pesan_feedback = "Format email tidak valid.";
        $feedback_type = 'error';
    } else {
        // Cek apakah email diubah dan jika ya, apakah email baru sudah dipakai pengguna lain
        $stmt_original_email = mysqli_prepare($koneksi, "SELECT email FROM pengguna WHERE id_pengguna = ?");
        mysqli_stmt_bind_param($stmt_original_email, "s", $id_edit);
        mysqli_stmt_execute($stmt_original_email);
        $result_original_email = mysqli_stmt_get_result($stmt_original_email);
        $original_data = mysqli_fetch_assoc($result_original_email);
        mysqli_stmt_close($stmt_original_email);

        $email_changed = ($email_user !== $original_data['email']);

        if ($email_changed) {
            $stmt_check_email_edit = mysqli_prepare($koneksi, "SELECT id_pengguna FROM pengguna WHERE email = ? AND id_pengguna != ?");
            mysqli_stmt_bind_param($stmt_check_email_edit, "ss", $email_user, $id_edit);
            mysqli_stmt_execute($stmt_check_email_edit);
            mysqli_stmt_store_result($stmt_check_email_edit);
            if (mysqli_stmt_num_rows($stmt_check_email_edit) > 0) {
                $pesan_feedback = "Email sudah terdaftar untuk pengguna lain.";
                $feedback_type = 'error';
            }
            mysqli_stmt_close($stmt_check_email_edit);
        }

        if ($feedback_type == 'sukses') { 
            if (!empty($password_input)) {
                $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
                $stmt_update = mysqli_prepare($koneksi, "UPDATE pengguna SET nama = ?, email = ?, password = ? WHERE id_pengguna = ?");
                mysqli_stmt_bind_param($stmt_update, "ssss", $nama, $email_user, $hashed_password, $id_edit);
            } else {
                $stmt_update = mysqli_prepare($koneksi, "UPDATE pengguna SET nama = ?, email = ? WHERE id_pengguna = ?");
                mysqli_stmt_bind_param($stmt_update, "sss", $nama, $email_user, $id_edit);
            }

            if (mysqli_stmt_execute($stmt_update)) {
                mysqli_stmt_close($stmt_update);
                header("Location: pengguna_daftar.php?feedback=" . urlencode("Data pengguna berhasil diperbarui.") . "&type=sukses");
                exit;
            } else {
                $pesan_feedback = "Gagal memperbarui data pengguna: " . mysqli_error($koneksi);
                $feedback_type = 'error';
                mysqli_stmt_close($stmt_update);
            }
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Pengguna';
    document.title = 'Edit Data Pengguna - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Data Pengguna (ID: <?= htmlspecialchars($data_edit['id_pengguna']) ?>)
    </h2>
    <form method="POST" action="pengguna_edit.php?id=<?= htmlspecialchars($data_edit['id_pengguna']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama" class="block text-sm font-medium text-green-200 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" placeholder="Masukkan nama lengkap"
                   value="<?= htmlspecialchars($data_edit['nama'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="email_user" class="block text-sm font-medium text-green-200 mb-1">Alamat Email</label>
            <input type="email" name="email_user" id="email_user" placeholder="contoh@email.com"
                   value="<?= htmlspecialchars($data_edit['email'] ?? '') ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-green-200 mb-1">Password Baru (Opsional)</label>
            <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin diubah"
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">Kosongkan field ini jika Anda tidak ingin mengubah password pengguna.</p>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="pengguna_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
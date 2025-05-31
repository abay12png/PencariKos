<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'nama' => '',
    'email_user' => '',
    'password' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['nama'] = $_POST['nama'] ?? '';
    $form_data['email_user'] = $_POST['email_user'] ?? '';
    $password_input = $_POST['password'] ?? ''; // Password tidak disimpan kembali ke form_data untuk keamanan

    if (empty($form_data['nama']) || empty($form_data['email_user']) || empty($password_input)) {
        $pesan_feedback = "Nama, Email, dan Password wajib diisi.";
        $feedback_type = 'error';
    } elseif (!filter_var($form_data['email_user'], FILTER_VALIDATE_EMAIL)) {
        $pesan_feedback = "Format email tidak valid.";
        $feedback_type = 'error';
    } else {
        $stmt_check_email = mysqli_prepare($koneksi, "SELECT id_pengguna FROM pengguna WHERE email = ?");
        mysqli_stmt_bind_param($stmt_check_email, "s", $form_data['email_user']);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);

        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $pesan_feedback = "Email sudah terdaftar. Gunakan email lain.";
            $feedback_type = 'error';
        } else {
            mysqli_stmt_close($stmt_check_email); // Tutup statement cek email sebelum lanjut

            $queryLastID = mysqli_query($koneksi, "SELECT id_pengguna FROM pengguna ORDER BY id_pengguna DESC LIMIT 1");
            $rowLastID = mysqli_fetch_assoc($queryLastID);
            $lastID = $rowLastID['id_pengguna'] ?? 'P000';
            $number = (int) substr($lastID, 1) + 1;
            $newIDPengguna = 'P' . str_pad($number, 3, '0', STR_PAD_LEFT);

            $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);

            $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO pengguna (id_pengguna, nama, email, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "ssss", $newIDPengguna, $form_data['nama'], $form_data['email_user'], $hashed_password);

            if (mysqli_stmt_execute($stmt_insert)) {
                mysqli_stmt_close($stmt_insert);
                header("Location: pengguna_daftar.php?feedback=" . urlencode("Pengguna baru berhasil ditambahkan.") . "&type=sukses");
                exit;
            } else {
                $pesan_feedback = "Gagal menambah pengguna: " . mysqli_error($koneksi);
                $feedback_type = 'error';
                mysqli_stmt_close($stmt_insert);
            }
        }
        if (isset($stmt_check_email) && mysqli_stmt_num_rows($stmt_check_email) <= 0) {
             // Hanya ditutup jika belum ditutup di blok 'else' sebelumnya
        } else if (isset($stmt_check_email)) {
             mysqli_stmt_close($stmt_check_email);
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Pengguna';
    document.title = 'Tambah Pengguna Baru - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Pengguna Baru
    </h2>
    <form method="POST" action="pengguna_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="nama" class="block text-sm font-medium text-green-200 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" placeholder="Masukkan nama lengkap"
                   value="<?= htmlspecialchars($form_data['nama']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="email_user" class="block text-sm font-medium text-green-200 mb-1">Alamat Email</label>
            <input type="email" name="email_user" id="email_user" placeholder="contoh@email.com"
                   value="<?= htmlspecialchars($form_data['email_user']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-green-200 mb-1">Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan password" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="pengguna_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="user-plus" class="w-5 h-5 mr-2"></i> Tambah Pengguna
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
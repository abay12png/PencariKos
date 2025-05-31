<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$form_data = [
    'id_pengguna' => '',
    'id_kos' => '',
    'nilai_rating' => ''
];
$rating_options = [1, 2, 3, 4, 5];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data['id_pengguna'] = $_POST['id_pengguna'] ?? '';
    $form_data['id_kos'] = $_POST['id_kos'] ?? '';
    $form_data['nilai_rating'] = $_POST['nilai_rating'] ?? '';
    
    $nilai_rating_int = filter_var($form_data['nilai_rating'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]]);

    if (empty($form_data['id_pengguna']) || empty($form_data['id_kos']) || $nilai_rating_int === false) {
        $pesan_feedback = "ID Pengguna, ID Kos, dan Nilai Rating (1-5) wajib diisi dengan benar.";
        $feedback_type = 'error';
    } else {
        $queryLastID = mysqli_query($koneksi, "SELECT id_review FROM review_rating ORDER BY id_review DESC LIMIT 1");
        $rowLastID = mysqli_fetch_assoc($queryLastID);
        $lastID = $rowLastID['id_review'] ?? 'RW000';
        $number = (int) substr($lastID, 2) + 1;
        $newIDReview = 'RW' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO review_rating (id_review, id_pengguna, id_kos, nilai_rating) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "sssi", $newIDReview, $form_data['id_pengguna'], $form_data['id_kos'], $nilai_rating_int);

        if (mysqli_stmt_execute($stmt_insert)) {
            mysqli_stmt_close($stmt_insert);
            header("Location: review_rating_daftar.php?feedback=" . urlencode("Rating berhasil ditambahkan.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_insert);
            $pesan_feedback = "Gagal menambah Rating: " . $error_msg;
            $feedback_type = 'error';
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Tambah Rating';
    document.title = 'Tambah Rating Baru - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Tambah Rating Baru
    </h2>
    <form method="POST" action="review_rating_tambah.php" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna</label>
            <input type="text" name="id_pengguna" id="id_pengguna" placeholder="Contoh: P001"
                   value="<?= htmlspecialchars($form_data['id_pengguna']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">ID Pengguna pemberi rating.</p>
        </div>
        <div>
            <label for="id_kos" class="block text-sm font-medium text-green-200 mb-1">ID Kos</label>
            <input type="text" name="id_kos" id="id_kos" placeholder="Contoh: K001"
                   value="<?= htmlspecialchars($form_data['id_kos']) ?>" required
                   class="w-full bg-green-700 text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-green-400">ID Kos yang dirating. </p>
        </div>
        <div>
            <label for="nilai_rating" class="block text-sm font-medium text-green-200 mb-1">Nilai Rating (1-5)</label>
            <select name="nilai_rating" id="nilai_rating" required class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <option value="">Pilih Rating</option>
                <?php
                foreach ($rating_options as $rating) {
                    $selected = ($form_data['nilai_rating'] == $rating) ? 'selected' : '';
                    echo "<option value=\"$rating\" $selected>$rating Bintang</option>";
                }
                ?>
            </select>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="review_rating_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
                <i data-lucide="star-half" class="w-5 h-5 mr-2"></i> Tambah Rating
            </button>
        </div>
    </form>
</div>

<?php
include '../../Template/template_footer.php';
?>
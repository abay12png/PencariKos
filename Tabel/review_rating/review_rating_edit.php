<?php
include '../../koneksi.php';

$pesan_feedback = '';
$feedback_type = 'sukses';
$data_edit = null;
$id_edit = $_GET['id'] ?? null;
$rating_options = [1, 2, 3, 4, 5];

if (!$id_edit) {
    header("Location: review_rating_daftar.php?feedback=" . urlencode("ID Review untuk edit tidak valid.") . "&type=error");
    exit;
}

$stmt_fetch = mysqli_prepare($koneksi, "SELECT * FROM review_rating WHERE id_review = ?");
mysqli_stmt_bind_param($stmt_fetch, "s", $id_edit);
mysqli_stmt_execute($stmt_fetch);
$result_fetch = mysqli_stmt_get_result($stmt_fetch);
$data_edit = mysqli_fetch_assoc($result_fetch);
mysqli_stmt_close($stmt_fetch);

if (!$data_edit) {
    header("Location: review_rating_daftar.php?feedback=" . urlencode("Data review tidak ditemukan.") . "&type=error");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna = $_POST['id_pengguna'] ?? $data_edit['id_pengguna']; 
    $id_kos = $_POST['id_kos'] ?? $data_edit['id_kos']; 
    $nilai_rating = $_POST['nilai_rating'] ?? $data_edit['nilai_rating'];

    $nilai_rating_int = filter_var($nilai_rating, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]]);

    if (empty($id_pengguna) || empty($id_kos) || $nilai_rating_int === false) {
        $pesan_feedback = "ID Pengguna, ID Kos, dan Nilai Rating (1-5) wajib diisi dengan benar.";
        $feedback_type = 'error';
        $data_edit = array_merge($data_edit, $_POST); 
        if($nilai_rating_int !== false) $data_edit['nilai_rating'] = $nilai_rating_int; else $data_edit['nilai_rating'] = $nilai_rating;

    } else {
        $stmt_update = mysqli_prepare($koneksi, "UPDATE review_rating SET id_pengguna = ?, id_kos = ?, nilai_rating = ? WHERE id_review = ?");
        mysqli_stmt_bind_param($stmt_update, "ssis", $id_pengguna, $id_kos, $nilai_rating_int, $id_edit);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: review_rating_daftar.php?feedback=" . urlencode("Data review berhasil diperbarui.") . "&type=sukses");
            exit;
        } else {
            $error_msg = mysqli_error($koneksi);
            mysqli_stmt_close($stmt_update);
            $pesan_feedback = "Gagal memperbarui data review: " . $error_msg;
            $feedback_type = 'error';
            $data_edit = array_merge($data_edit, $_POST);
            if($nilai_rating_int !== false) $data_edit['nilai_rating'] = $nilai_rating_int; else $data_edit['nilai_rating'] = $nilai_rating;
        }
    }
}

include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Edit Rating';
    document.title = 'Edit Data Rating - KosApp';
</script>

<?php if ($pesan_feedback): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="bg-green-800 shadow-2xl rounded-xl p-6 md:p-8 max-w-lg mx-auto">
    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-8 text-center">
        Edit Rating (ID: <?= htmlspecialchars($data_edit['id_review']) ?>)
    </h2>
    <form method="POST" action="review_rating_edit.php?id=<?= htmlspecialchars($data_edit['id_review']) ?>" autocomplete="off" class="space-y-6">
        <div>
            <label for="id_pengguna" class="block text-sm font-medium text-green-200 mb-1">ID Pengguna (Read-only)</label>
            <input type="text" name="id_pengguna" id="id_pengguna"
                   value="<?= htmlspecialchars($data_edit['id_pengguna'] ?? '') ?>" readonly
                   class="w-full bg-green-900/50 cursor-not-allowed text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="id_kos" class="block text-sm font-medium text-green-200 mb-1">ID Kos (Read-only)</label>
            <input type="text" name="id_kos" id="id_kos"
                   value="<?= htmlspecialchars($data_edit['id_kos'] ?? '') ?>" readonly
                   class="w-full bg-green-900/50 cursor-not-allowed text-white placeholder-green-400 border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
        </div>
        <div>
            <label for="nilai_rating" class="block text-sm font-medium text-green-200 mb-1">Nilai Rating (1-5)</label>
            <select name="nilai_rating" id="nilai_rating" required class="w-full bg-green-700 text-white border border-green-600 rounded-lg shadow-sm py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                <option value="">Pilih Rating</option>
                <?php
                $current_rating = $data_edit['nilai_rating'] ?? null;
                foreach ($rating_options as $rating) {
                    $selected = ($current_rating == $rating) ? 'selected' : '';
                    echo "<option value=\"$rating\" $selected>$rating Bintang</option>";
                }
                ?>
            </select>
        </div>
        <div class="flex justify-end pt-4 space-x-3">
            <a href="review_rating_daftar.php" class="px-6 py-2.5 rounded-lg bg-gray-600 hover:bg-gray-700 text-white font-medium transition-colors">Batal</a>
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
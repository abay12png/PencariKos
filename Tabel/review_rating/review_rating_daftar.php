<?php
include '../../koneksi.php';
include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Manajemen Rating';
    document.title = 'Manajemen Rating Kos - KosApp';
</script>

<?php
$pesan_feedback = '';
$feedback_type = 'sukses';

if (isset($_GET['feedback'])) {
    $pesan_feedback = htmlspecialchars($_GET['feedback']);
    $feedback_type = htmlspecialchars($_GET['type'] ?? 'sukses');
}
?>

<?php if ($pesan_feedback): ?>
    <div class="mb-8 p-4 rounded-lg <?php echo ($feedback_type == 'sukses') ? 'bg-green-600 border border-green-700' : 'bg-red-600 border border-red-700'; ?> text-white text-sm shadow-lg">
        <?= $pesan_feedback ?>
    </div>
<?php endif; ?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Daftar Rating Kos</h1>
    <a href="review_rating_tambah.php"
       class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
        <i data-lucide="star-half" class="w-5 h-5 mr-2"></i> Tambah Rating
    </a>
</div>

<div class="bg-green-800 shadow-2xl rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-green-100">
            <thead class="text-xs text-green-300 uppercase bg-green-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Review</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Pengguna</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Kos</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Nilai Rating</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-700">
                <?php
                $stmt_tampil = mysqli_prepare($koneksi, "SELECT rr.id_review, rr.id_pengguna, p.nama AS nama_pengguna, rr.id_kos, k.nama_kos, rr.nilai_rating 
                                                      FROM review_rating rr
                                                      JOIN pengguna p ON rr.id_pengguna = p.id_pengguna
                                                      JOIN kos k ON rr.id_kos = k.id_kos
                                                      ORDER BY rr.id_review DESC");
                mysqli_stmt_execute($stmt_tampil);
                $result_review = mysqli_stmt_get_result($stmt_tampil);

                if ($result_review && mysqli_num_rows($result_review) > 0):
                    while ($row = mysqli_fetch_assoc($result_review)):
                ?>
                        <tr class="hover:bg-green-700/60 transition-colors duration-150 align-middle">
                            <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($row['id_review']) ?></td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white"><?= htmlspecialchars($row['nama_pengguna']) ?></div>
                                <div class="text-xs text-green-400">(<?= htmlspecialchars($row['id_pengguna']) ?>)</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white"><?= htmlspecialchars($row['nama_kos']) ?></div>
                                <div class="text-xs text-green-400">(<?= htmlspecialchars($row['id_kos']) ?>)</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center text-yellow-400">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i data-lucide="star" class="w-4 h-4 <?= $i <= ($row['nilai_rating'] ?? 0) ? 'fill-current' : 'text-gray-500' ?>"></i>
                                    <?php endfor; ?>
                                    <span class="ml-1.5 text-xs text-green-200">(<?= htmlspecialchars($row['nilai_rating'] ?? 0) ?>)</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-1">
                                <a href="review_rating_edit.php?id=<?= $row['id_review'] ?>"
                                   class="inline-flex items-center justify-center p-2.5 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Edit Rating</span>
                                </a>
                                <a href="review_rating_proses_hapus.php?id=<?= $row['id_review'] ?>"
                                   onclick="return confirm('Yakin ingin hapus rating ID: <?= htmlspecialchars(addslashes($row['id_review'])) ?>?')"
                                   class="inline-flex items-center justify-center p-2.5 text-red-400 hover:text-red-300 hover:bg-red-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Hapus Rating</span>
                                </a>
                            </td>
                        </tr>
                    <?php 
                    endwhile;
                else: 
                ?>
                    <tr>
                        <td colspan="5" class="text-center py-16 text-gray-400">
                            <div class="flex flex-col items-center">
                                <i data-lucide="star-off" class="w-20 h-20 mb-4 text-green-600 opacity-75"></i>
                                <p class="text-xl">Belum ada data rating.</p>
                            </div>
                        </td>
                    </tr>
                <?php 
                endif; 
                if ($result_review && isset($stmt_tampil)) {
                    mysqli_stmt_close($stmt_tampil);
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include '../../Template/template_footer.php';
?>
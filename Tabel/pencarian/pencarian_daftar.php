<?php
include '../../koneksi.php';
include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Riwayat Pencarian';
    document.title = 'Riwayat Pencarian Pengguna - KosApp';
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
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Log Aktivitas Pencarian</h1>
    
</div>

<div class="bg-green-800 shadow-2xl rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-green-100">
            <thead class="text-xs text-green-300 uppercase bg-green-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Pencarian</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Pengguna</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Preferensi Pencarian</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-700">
                <?php
                $stmt_tampil = mysqli_prepare($koneksi, "SELECT pc.id_pencarian, pc.id_pengguna, pg.nama AS nama_pengguna, pc.preferensi, pc.tanggal_pencarian
                                                      FROM pencarian pc
                                                      JOIN pengguna pg ON pc.id_pengguna = pg.id_pengguna
                                                      ORDER BY pc.tanggal_pencarian DESC, pc.id_pencarian DESC");
                mysqli_stmt_execute($stmt_tampil);
                $result_pencarian = mysqli_stmt_get_result($stmt_tampil);

                if ($result_pencarian && mysqli_num_rows($result_pencarian) > 0):
                    while ($row = mysqli_fetch_assoc($result_pencarian)):
                ?>
                        <tr class="hover:bg-green-700/60 transition-colors duration-150 align-top">
                            <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($row['id_pencarian']) ?></td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white"><?= htmlspecialchars($row['nama_pengguna']) ?></div>
                                <div class="text-xs text-green-400">(<?= htmlspecialchars($row['id_pengguna']) ?>)</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-md max-h-24 overflow-y-auto p-1 rounded bg-green-900/30 custom-scrollbar">
                                    <p class="text-xs leading-relaxed whitespace-pre-wrap break-words text-green-200">
                                        <?= nl2br(htmlspecialchars($row['preferensi'])) ?>
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-green-300">
                                <?= htmlspecialchars(date("d M Y", strtotime($row['tanggal_pencarian']))) ?>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="pencarian_proses_hapus.php?id=<?= $row['id_pencarian'] ?>"
                                   onclick="return confirm('Yakin ingin hapus riwayat pencarian ID: <?= htmlspecialchars(addslashes($row['id_pencarian'])) ?> ini?')"
                                   class="inline-flex items-center justify-center p-2.5 text-red-400 hover:text-red-300 hover:bg-red-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    <span class="tooltip-text">Hapus Riwayat</span>
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
                                <i data-lucide="search-slash" class="w-20 h-20 mb-4 text-green-600 opacity-75"></i>
                                <p class="text-xl">Tidak ada riwayat pencarian ditemukan.</p>
                                <p class="text-sm mt-1">Belum ada pengguna yang melakukan pencarian.</p>
                            </div>
                        </td>
                    </tr>
                <?php 
                endif; 
                if ($result_pencarian && isset($stmt_tampil)) {
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
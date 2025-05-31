<?php
include '../../koneksi.php';
include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Manajemen Fasilitas';
    document.title = 'Manajemen Fasilitas Kos - KosApp';
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
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Daftar Paket Fasilitas</h1>
    <a href="fasilitas_tambah.php"
       class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
        <i data-lucide="list-plus" class="w-5 h-5 mr-2"></i> Tambah Paket Fasilitas
    </a>
</div>

<div class="bg-green-800 shadow-2xl rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-green-100">
            <thead class="text-xs text-green-300 uppercase bg-green-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Fasilitas</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Fasilitas 1</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Fasilitas 2</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Fasilitas 3</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-700">
                <?php
                $stmt_tampil = mysqli_prepare($koneksi, "SELECT id_fasilitas, nama_fasilitas, nama_fasilitas2, nama_fasilitas3 FROM fasilitas ORDER BY id_fasilitas ASC");
                mysqli_stmt_execute($stmt_tampil);
                $result_fasilitas = mysqli_stmt_get_result($stmt_tampil);

                if ($result_fasilitas && mysqli_num_rows($result_fasilitas) > 0):
                    while ($row = mysqli_fetch_assoc($result_fasilitas)):
                ?>
                        <tr class="hover:bg-green-700/60 transition-colors duration-150 align-middle">
                            <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($row['id_fasilitas']) ?></td>
                            <td class="px-6 py-4 text-green-200"><?= htmlspecialchars($row['nama_fasilitas'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-green-200"><?= htmlspecialchars($row['nama_fasilitas2'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-green-200"><?= htmlspecialchars($row['nama_fasilitas3'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-1">
                                <a href="fasilitas_edit.php?id=<?= $row['id_fasilitas'] ?>"
                                   class="inline-flex items-center justify-center p-2.5 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Edit Fasilitas</span>
                                </a>
                                <a href="fasilitas_proses_hapus.php?id=<?= $row['id_fasilitas'] ?>"
                                   onclick="return confirm('Yakin ingin hapus paket fasilitas ID: <?= htmlspecialchars(addslashes($row['id_fasilitas'])) ?>? Perhatian: Ini bisa gagal jika fasilitas ini masih digunakan oleh data kos.')"
                                   class="inline-flex items-center justify-center p-2.5 text-red-400 hover:text-red-300 hover:bg-red-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Hapus Fasilitas</span>
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
                                <i data-lucide="layout-list" class="w-20 h-20 mb-4 text-green-600 opacity-75"></i>
                                <p class="text-xl">Belum ada data paket fasilitas.</p>
                                <p class="text-sm mt-1">Silakan tambahkan data paket fasilitas baru.</p>
                            </div>
                        </td>
                    </tr>
                <?php 
                endif; 
                if ($result_fasilitas && isset($stmt_tampil)) {
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
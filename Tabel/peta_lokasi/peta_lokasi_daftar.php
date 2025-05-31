<?php
include '../../koneksi.php';
include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Manajemen Peta Lokasi';
    document.title = 'Manajemen Peta Lokasi Kos - KosApp';
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
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Data Peta Lokasi Kos</h1>
    <a href="peta_lokasi_tambah.php"
       class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
        <i data-lucide="map-pin-plus" class="w-5 h-5 mr-2"></i> Tambah Lokasi Kos
    </a>
</div>

<div class="bg-green-800 shadow-2xl rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-green-100">
            <thead class="text-xs text-green-300 uppercase bg-green-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Lokasi</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Kos</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Kos</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Koordinat</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-700">
                <?php
                $stmt_tampil = mysqli_prepare($koneksi, "SELECT pl.id_lokasi, pl.id_kos, pl.koordinat, k.nama_kos 
                                                      FROM peta_lokasi pl
                                                      JOIN kos k ON pl.id_kos = k.id_kos
                                                      ORDER BY pl.id_lokasi ASC");
                mysqli_stmt_execute($stmt_tampil);
                $result_lokasi = mysqli_stmt_get_result($stmt_tampil);

                if ($result_lokasi && mysqli_num_rows($result_lokasi) > 0):
                    while ($row = mysqli_fetch_assoc($result_lokasi)):
                ?>
                        <tr class="hover:bg-green-700/60 transition-colors duration-150 align-middle">
                            <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($row['id_lokasi']) ?></td>
                            <td class="px-6 py-4 text-green-300"><?= htmlspecialchars($row['id_kos']) ?></td>
                            <td class="px-6 py-4 text-white"><?= htmlspecialchars($row['nama_kos'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-green-200 font-mono text-xs"><?= htmlspecialchars($row['koordinat']) ?></td>
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-1">
                                <a href="peta_lokasi_edit.php?id=<?= $row['id_lokasi'] ?>"
                                   class="inline-flex items-center justify-center p-2.5 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                    <i data-lucide="map-pin-off" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Edit Lokasi</span>
                                </a>
                                <a href="peta_lokasi_proses_hapus.php?id=<?= $row['id_lokasi'] ?>"
                                   onclick="return confirm('Yakin ingin hapus data lokasi ID: <?= htmlspecialchars(addslashes($row['id_lokasi'])) ?> untuk Kos: <?= htmlspecialchars(addslashes($row['nama_kos'] ?? $row['id_kos'])) ?>?')"
                                   class="inline-flex items-center justify-center p-2.5 text-red-400 hover:text-red-300 hover:bg-red-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Hapus Lokasi</span>
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
                                <i data-lucide="map-pin-off" class="w-20 h-20 mb-4 text-green-600 opacity-75"></i>
                                <p class="text-xl">Belum ada data lokasi kos.</p>
                                <p class="text-sm mt-1">Silakan tambahkan data lokasi baru.</p>
                            </div>
                        </td>
                    </tr>
                <?php 
                endif; 
                if ($result_lokasi && isset($stmt_tampil)) {
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
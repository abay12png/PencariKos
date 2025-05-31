<?php
include '../../koneksi.php';
include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Daftar Data Kos';
    document.title = 'Daftar Data Kos - KosApp';
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
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Daftar Data Kos</h1>
    <a href="kos_tambah.php"
       class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
        <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i> Tambah Data Kos
    </a>
</div>

<div class="bg-green-800 shadow-2xl rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-green-100">
            <thead class="text-xs text-green-300 uppercase bg-green-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Kos</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Kos</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Alamat</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Harga/bln</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Tipe</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-4 tracking-wider min-w-[250px]">Fasilitas (ID: Nama1, Nama2, Nama3)</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Pemilik</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-700">
                <?php
                $query_str = "SELECT k.id_kos, k.nama_kos, k.alamat, k.harga, k.tipe_kos, k.status, k.id_pemilik, k.id_fasilitas, 
                                     f.nama_fasilitas, f.nama_fasilitas2, f.nama_fasilitas3
                              FROM kos k
                              LEFT JOIN fasilitas f ON k.id_fasilitas = f.id_fasilitas 
                              ORDER BY k.id_kos DESC";
                $stmt_tampil = mysqli_prepare($koneksi, $query_str);
                mysqli_stmt_execute($stmt_tampil);
                $result_tampil = mysqli_stmt_get_result($stmt_tampil);

                if ($result_tampil && mysqli_num_rows($result_tampil) > 0):
                    while ($row = mysqli_fetch_assoc($result_tampil)):
                        $fasilitas_display = [];
                        if (!empty($row['nama_fasilitas'])) $fasilitas_display[] = htmlspecialchars($row['nama_fasilitas']);
                        if (!empty($row['nama_fasilitas2'])) $fasilitas_display[] = htmlspecialchars($row['nama_fasilitas2']);
                        if (!empty($row['nama_fasilitas3'])) $fasilitas_display[] = htmlspecialchars($row['nama_fasilitas3']);
                        $fasilitas_str = !empty($fasilitas_display) ? implode(', ', $fasilitas_display) : 'N/A';
                ?>
                        <tr class="hover:bg-green-700/60 transition-colors duration-150 align-middle">
                            <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($row['id_kos']) ?></td>
                            <td class="px-6 py-4 text-white"><?= htmlspecialchars($row['nama_kos']) ?></td>
                            <td class="px-6 py-4 text-xs text-green-300 max-w-xs break-words"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td class="px-6 py-4 text-green-200">Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4 text-green-200"><?= htmlspecialchars($row['tipe_kos']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                $status_val = strtolower($row['status'] ?? '');
                                $status_class = 'bg-gray-500 text-gray-100';
                                $status_icon = 'help-circle';
                                if ($status_val === 'tersedia') {
                                    $status_class = 'bg-green-200 text-green-800'; $status_icon = 'check-circle-2';
                                } elseif ($status_val === 'penuh') {
                                    $status_class = 'bg-red-200 text-red-800'; $status_icon = 'x-circle';
                                }
                                ?>
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full <?= $status_class ?>">
                                    <i data-lucide="<?= $status_icon ?>" class="w-3.5 h-3.5 mr-1.5"></i>
                                    <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-green-300">
                                <span class="font-semibold text-green-100"><?= htmlspecialchars($row['id_fasilitas'] ?? 'N/A') ?>:</span> <?= $fasilitas_str ?>
                            </td>
                            <td class="px-6 py-4 text-green-300"><?= htmlspecialchars($row['id_pemilik']) ?></td>
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-1">
                                <a href="kos_edit.php?id=<?= $row['id_kos'] ?>"
                                   class="inline-flex items-center justify-center p-2.5 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Edit Kos</span>
                                </a>
                                <a href="kos_proses_hapus.php?id=<?= $row['id_kos'] ?>"
                                   onclick="return confirm('Yakin ingin hapus data kos <?= htmlspecialchars(addslashes($row['nama_kos'])) ?>?')"
                                   class="inline-flex items-center justify-center p-2.5 text-red-400 hover:text-red-300 hover:bg-red-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Hapus Kos</span>
                                </a>
                            </td>
                        </tr>
                    <?php 
                    endwhile;
                else: 
                ?>
                    <tr>
                        <td colspan="9" class="text-center py-16 text-gray-400">
                            <div class="flex flex-col items-center">
                                <i data-lucide="home-off" class="w-20 h-20 mb-4 text-green-600 opacity-75"></i>
                                <p class="text-xl">Belum ada data kos.</p>
                                <p class="text-sm mt-1">Silakan tambahkan data kos baru.</p>
                            </div>
                        </td>
                    </tr>
                <?php 
                endif; 
                if ($result_tampil && isset($stmt_tampil)) {
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
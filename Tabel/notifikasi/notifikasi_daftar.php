<?php
include '../../koneksi.php';
include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Manajemen Notifikasi';
    document.title = 'Manajemen Notifikasi - KosApp';
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
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Daftar Notifikasi</h1>
    <a href="notifikasi_tambah.php"
       class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
        <i data-lucide="bell-plus" class="w-5 h-5 mr-2"></i> Tambah Notifikasi
    </a>
</div>

<div class="bg-green-800 shadow-2xl rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-green-100">
            <thead class="text-xs text-green-300 uppercase bg-green-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Notifikasi</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Pengguna</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Pengguna</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Pemilik</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Pemilik</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Pesan</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Status</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-700">
                <?php
                $stmt_tampil = mysqli_prepare($koneksi, "SELECT n.id_notifikasi, n.id_pengguna, p.nama AS nama_pengguna, n.id_pemilik, pk.nama_pemmilik AS nama_pemilik, n.pesan, n.status 
                                                      FROM notifikasi n
                                                      LEFT JOIN pengguna p ON n.id_pengguna = p.id_pengguna
                                                      LEFT JOIN pemilik_kos pk ON n.id_pemilik = pk.id_pemilik
                                                      ORDER BY n.id_notifikasi DESC");
                mysqli_stmt_execute($stmt_tampil);
                $result_notifikasi = mysqli_stmt_get_result($stmt_tampil);

                if ($result_notifikasi && mysqli_num_rows($result_notifikasi) > 0):
                    while ($row = mysqli_fetch_assoc($result_notifikasi)):
                        $status_val = strtolower($row['status'] ?? 'belum_dibaca');
                        $status_class = 'bg-gray-200 text-gray-800';
                        $status_icon = 'mail-question';
                        switch ($status_val) {
                            case 'sudah_dibaca': $status_class = 'bg-green-200 text-green-800'; $status_icon = 'mail-check'; break;
                            case 'belum_dibaca': $status_class = 'bg-yellow-200 text-yellow-800'; $status_icon = 'mail'; break;
                            default: $status_class = 'bg-blue-200 text-blue-800'; $status_icon = 'info'; break; 
                        }
                ?>
                        <tr class="hover:bg-green-700/60 transition-colors duration-150 align-top">
                            <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($row['id_notifikasi']) ?></td>
                            <td class="px-6 py-4 text-green-300"><?= htmlspecialchars($row['id_pengguna'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-white"><?= htmlspecialchars($row['nama_pengguna'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-green-300"><?= htmlspecialchars($row['id_pemilik'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-white"><?= htmlspecialchars($row['nama_pemilik'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4">
                                <div class="max-w-sm max-h-20 overflow-y-auto p-1 rounded bg-green-900/30 custom-scrollbar">
                                    <p class="text-xs leading-relaxed whitespace-pre-wrap break-words text-green-200">
                                        <?= nl2br(htmlspecialchars($row['pesan'])) ?>
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full <?= $status_class ?>">
                                    <i data-lucide="<?= $status_icon ?>" class="w-3.5 h-3.5 mr-1.5"></i>
                                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($row['status'] ?? 'Belum Dibaca'))) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-1">
                                <a href="notifikasi_edit.php?id=<?= $row['id_notifikasi'] ?>"
                                   class="inline-flex items-center justify-center p-2.5 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Edit Notifikasi</span>
                                </a>
                                <a href="notifikasi_proses_hapus.php?id=<?= $row['id_notifikasi'] ?>"
                                   onclick="return confirm('Yakin ingin hapus notifikasi ID: <?= htmlspecialchars(addslashes($row['id_notifikasi'])) ?>?')"
                                   class="inline-flex items-center justify-center p-2.5 text-red-400 hover:text-red-300 hover:bg-red-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Hapus Notifikasi</span>
                                </a>
                            </td>
                        </tr>
                    <?php 
                    endwhile;
                else: 
                ?>
                    <tr>
                        <td colspan="8" class="text-center py-16 text-gray-400">
                            <div class="flex flex-col items-center">
                                <i data-lucide="bell-off" class="w-20 h-20 mb-4 text-green-600 opacity-75"></i>
                                <p class="text-xl">Belum ada data notifikasi.</p>
                            </div>
                        </td>
                    </tr>
                <?php 
                endif; 
                if ($result_notifikasi && isset($stmt_tampil)) {
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
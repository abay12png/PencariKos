<?php
include '../../koneksi.php';
include '../../Template/template_header.php';
?>

<script>
    document.getElementById('pageTitle').innerText = 'Manajemen Pemilik Kos';
    document.title = 'Manajemen Pemilik Kos - KosApp';
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
    <h1 class="text-3xl sm:text-4xl font-bold text-white">Daftar Pemilik Kos</h1>
    <a href="pemilik_kos_tambah.php"
       class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center">
        <i data-lucide="user-plus" class="w-5 h-5 mr-2"></i> Tambah Pemilik Kos
    </a>
</div>

<div class="bg-green-800 shadow-2xl rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-green-100">
            <thead class="text-xs text-green-300 uppercase bg-green-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Pemilik</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Pemilik</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Kontak</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">ID Pengguna Akun</th>
                    <th scope="col" class="px-6 py-4 tracking-wider">Nama Akun (Pengguna)</th>
                    <th scope="col" class="px-6 py-4 tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-green-700">
                <?php
                $query_str = "SELECT pk.id_pemilik, pk.nama_pemmilik, pk.kontak, pk.id_pengguna, p.nama AS nama_akun 
                              FROM pemilik_kos pk 
                              LEFT JOIN pengguna p ON pk.id_pengguna = p.id_pengguna 
                              ORDER BY pk.id_pemilik ASC";
                $result_pemilik = mysqli_query($koneksi, $query_str);

                if ($result_pemilik && mysqli_num_rows($result_pemilik) > 0):
                    while ($row = mysqli_fetch_assoc($result_pemilik)):
                ?>
                        <tr class="hover:bg-green-700/60 transition-colors duration-150 align-middle">
                            <td class="px-6 py-4 font-medium text-white"><?= htmlspecialchars($row['id_pemilik']) ?></td>
                            <td class="px-6 py-4 text-white"><?= htmlspecialchars($row['nama_pemmilik']) ?></td>
                            <td class="px-6 py-4 text-green-300"><?= htmlspecialchars($row['kontak']) ?></td>
                            <td class="px-6 py-4 text-green-300"><?= htmlspecialchars($row['id_pengguna']) ?></td>
                            <td class="px-6 py-4 text-green-200"><?= htmlspecialchars($row['nama_akun'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4 text-center whitespace-nowrap space-x-1">
                                <a href="pemilik_kos_edit.php?id=<?= $row['id_pemilik'] ?>"
                                   class="inline-flex items-center justify-center p-2.5 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Edit Pemilik</span>
                                </a>
                                <a href="pemilik_kos_proses_hapus.php?id=<?= $row['id_pemilik'] ?>"
                                   onclick="return confirm('Yakin ingin hapus data pemilik: <?= htmlspecialchars(addslashes($row['nama_pemmilik'])) ?> (ID: <?= htmlspecialchars(addslashes($row['id_pemilik'])) ?>)? Perhatian: Ini bisa gagal jika pemilik masih memiliki data kos terdaftar.')"
                                   class="inline-flex items-center justify-center p-2.5 text-red-400 hover:text-red-300 hover:bg-red-700/30 rounded-lg transition-all group relative focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    <span class="tooltip-text">Hapus Pemilik</span>
                                </a>
                            </td>
                        </tr>
                    <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="6" class="text-center py-16 text-gray-400">
                            <div class="flex flex-col items-center">
                                <i data-lucide="users-round" class="w-20 h-20 mb-4 text-green-600 opacity-75"></i>
                                <p class="text-xl">Belum ada data pemilik kos.</p>
                                <p class="text-sm mt-1">Silakan tambahkan data pemilik kos baru.</p>
                            </div>
                        </td>
                    </tr>
                <?php 
                endif; 
                if ($result_pemilik) {
                    mysqli_free_result($result_pemilik);
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include '../../Template/template_footer.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Interaktif - Boarding House</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .fade-in {
            animation: fadeIn 0.7s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom scrollbar (opsional) */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #14532d; /* Ganti dengan warna tema Anda jika perlu */
        }
        ::-webkit-scrollbar-thumb {
            background: #22c55e; /* Ganti dengan warna tema Anda jika perlu */
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #16a34a; /* Ganti dengan warna tema Anda jika perlu */
        }
    </style>
</head>

<body class="bg-green-900 text-white transition-colors duration-300">
    <header class="flex items-center justify-between px-6 py-4 bg-green-800 shadow-lg">
        <div class="text-2xl font-bold">Boarding House Admin</div>
        <nav class="space-x-6">
            </nav>
        <div class="flex items-center space-x-4">
            <a href="header.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-colors shadow-md">Log out</a>
        </div>
    </header>

    <main class="flex flex-col items-center text-center px-4 py-20 sm:py-24 bg-green-700 rounded-b-3xl shadow-xl fade-in">
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold leading-tight max-w-3xl">
            Kelola Data Kos <span class="text-green-300">Mudah & Interaktif</span>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-gray-200 max-w-2xl">
            Semua yang Anda butuhkan untuk manajemen kos yang efisien ada di sini. Temukan, kelola reservasi, dan pantau pembayaran dengan cepat.
        </p>
        <div class="mt-10">
            <a href="#menu"
               class="bg-white text-green-700 font-semibold px-8 py-3 rounded-lg shadow-md hover:bg-gray-100 transform hover:scale-105 transition duration-300 text-lg">
                Mulai Kelola
            </a>
        </div>
    </main>

    <section id="menu" class="py-16 sm:py-20 bg-gray-50 text-gray-800 fade-in">
        <h2 class="text-3xl sm:text-4xl font-bold text-center mb-12 sm:mb-16 text-green-700">Menu Database</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8 max-w-7xl mx-auto px-6">
            
            <a href="Tabel/kos/kos_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="home" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Data Kos</span>
            </a>
            <a href="Tabel/pengguna/pengguna_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="users" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Pengguna</span>
            </a>
            <a href="Tabel/pemilik_kos/pemilik_kos_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="user-check" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Pemilik Kos</span>
            </a>
            <a href="Tabel/fasilitas/fasilitas_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="layout-list" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Fasilitas</span>
            </a>
            <a href="Tabel/reservasi/reservasi_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="calendar-check" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Reservasi</span>
            </a>
            <a href="Tabel/pembayaran/pembayaran_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="credit-card" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Pembayaran</span>
            </a>
            <a href="Tabel/review_rating/review_rating_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="message-square-text" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Review & Rating</span>
            </a>
            <a href="Tabel/notifikasi/notifikasi_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="bell" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Notifikasi</span>
            </a>
            <a href="Tabel/pencarian/pencarian_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="search" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Riwayat Pencarian</span>
            </a>
            <a href="Tabel/peta_lokasi/peta_lokasi_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="map-pin" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Peta Lokasi</span>
            </a>
             <a href="Tabel/rekomendasi/rekomendasi_daftar.php" class="p-6 bg-white hover:bg-green-50 rounded-xl shadow-lg text-center font-semibold transition-colors duration-300 flex flex-col items-center justify-center gap-3 aspect-square">
                <i data-lucide="star" class="w-10 h-10 text-green-600"></i>
                <span class="text-lg">Rekomendasi</span>
            </a>
            </div>
    </section>

    <footer class="bg-green-800 text-white py-10 text-center">
        <p class="mb-2 text-lg font-semibold">Boarding House App</p>
        <p class="text-sm text-green-300">&copy; <span id="currentYear"></span> KosApp by Salis. All rights reserved.</p>
        <div class="mt-4 space-x-4 text-sm"> <a href="#" class="hover:text-green-300 transition-colors">Tentang Kami</a> <a href="#" class="hover:text-green-300 transition-colors">Kebijakan Privasi</a>
            <a href="#" class="hover:text-green-300 transition-colors">Syarat & Ketentuan</a>
        </div>
    </footer>

    <script>
        lucide.createIcons();
        document.getElementById('currentYear').textContent = new Date().getFullYear();
    </script>
</body>
</html>
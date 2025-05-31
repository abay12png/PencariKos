<?php
session_start();
if (!isset($_SESSION['email_admin'])) { // Menggunakan 'email_admin' sesuai admin.php terakhir
    header("Location: ../../login.php"); // Dari Tabel/entity/ ke root Pencarikos/login.php
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #14532d; border-radius: 4px;}
        ::-webkit-scrollbar-thumb { background: #22c55e; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #16a34a; }
        .group:hover .tooltip-text { display: block; opacity: 1; transform: translateX(-50%) translateY(0); } 
        .tooltip-text { 
            display: block; opacity: 0; position: absolute; 
            bottom: 100%; left: 50%; transform: translateX(-50%) translateY(5px); 
            margin-bottom: 6px; background-color: #1f2937; color: white; 
            font-size: 0.75rem; padding: 6px 10px; border-radius: 0.375rem; 
            white-space: nowrap; z-index: 50; pointer-events: none;
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
        }
    </style>
    <title>Manajemen Data - KosApp</title>
</head>
<body class="bg-green-900 text-gray-200 antialiased flex flex-col min-h-screen">

    <header class="flex items-center justify-between px-6 py-4 bg-green-800 shadow-xl sticky top-0 z-40">
        <div id="pageTitle" class="text-xl sm:text-2xl font-bold text-white">Manajemen Data</div> 
        <nav class="space-x-4">
            <a href="../../menu.php" class="text-green-300 hover:text-white transition-colors duration-200 flex items-center px-3 py-2 rounded-md hover:bg-green-700 text-sm sm:text-base">
                <i data-lucide="layout-dashboard" class="w-4 h-4 sm:w-5 sm:h-5 mr-2"></i> Menu Utama
            </a>
        </nav>
        <div class="min-w-[80px] sm:min-w-[100px] flex justify-end">
            <a href="../../logout.php" class="bg-red-600 hover:bg-red-700 px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold text-white transition-colors shadow-md">Log out</a>
        </div>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow">
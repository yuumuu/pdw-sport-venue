<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Admin - SportVenue' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sport: { 25: '#fff8f0', 50: '#fff0e0', 100: '#ffe0c0', 200: '#ffc18a', 300: '#ffa255', 400: '#fc8320', 500: '#f97316', 600: '#ea580c', 700: '#c2410c', 800: '#9a3412', 900: '#7c2d12' },
                        success: '#03ca77', danger: '#e31748', 'dark-navy': '#001f3e',
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'] },
                    boxShadow: {
                        'zp-sm': '0 2px 8px rgba(0,0,0,0.04)',
                        'zp': '0 8px 24px rgba(0,0,0,0.08)',
                        'zp-glow': '0 0 20px rgba(249, 115, 22, 0.3)',
                    },
                },
            },
        };
        (function() {
            if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
        })();
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
</head>
<body class="bg-sport-25 dark:bg-gray-950 font-sans">
    <div class="flex h-screen">
        <aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 hidden lg:flex flex-col">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800">
                <a href="<?= base_url('admin') ?>" class="flex items-center gap-2 font-bold text-lg text-sport-500">
                    <i data-lucide="trophy" class="w-6 h-6"></i>
                    <span>SportVenue</span>
                </a>
            </div>
            <nav class="flex-1 p-3 space-y-1">
                <?php 
                    $currentUrl = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
                    // simple active state check
                    $isActive = function($path) use ($currentUrl) {
                        return strpos($currentUrl, trim($path, '/')) !== false && ($path !== 'admin' || $currentUrl === 'admin');
                    };
                    $activeClass = 'bg-sport-500 text-white shadow-sm';
                    $inactiveClass = 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200';
                ?>
                <a href="<?= base_url('admin') ?>" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium transition-colors <?= $isActive('admin') && $currentUrl === 'admin' ? $activeClass : $inactiveClass ?>">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                </a>
                <a href="<?= base_url('admin/fields') ?>" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium transition-colors <?= $isActive('admin/fields') ? $activeClass : $inactiveClass ?>">
                    <i data-lucide="trophy" class="w-4 h-4"></i> Kelola Lapangan
                </a>
                <a href="<?= base_url('admin/bookings') ?>" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium transition-colors <?= $isActive('admin/bookings') ? $activeClass : $inactiveClass ?>">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Reservasi
                </a>
                <a href="<?= base_url('admin/reports') ?>" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium transition-colors <?= $isActive('admin/reports') ? $activeClass : $inactiveClass ?>">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Laporan
                </a>
                <a href="<?= base_url('admin/users') ?>" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium transition-colors <?= $isActive('admin/users') ? $activeClass : $inactiveClass ?>">
                    <i data-lucide="users" class="w-4 h-4"></i> Manajemen Admin
                </a>
            </nav>
            <div class="p-3 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span class="flex-1 truncate"><?= e(auth_user()['display_name'] ?? 'Admin') ?></span>
                </div>
                <a href="<?= base_url('admin/logout') ?>" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-danger hover:bg-danger/5 transition-colors mt-1">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800 px-4 sm:px-6 py-3 flex items-center justify-between">
                <h1 class="text-lg font-bold"><?= $title ?? 'Dashboard' ?></h1>
                <div class="flex items-center gap-2">
                    <a href="<?= base_url('admin/logout') ?>" class="text-sm text-danger hover:underline flex items-center gap-1 lg:hidden">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                    </a>
                    <button onclick="toggleDarkMode()" class="w-9 h-9 flex items-center justify-center border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-all">
                        <i data-lucide="moon" class="w-4 h-4 dark:hidden"></i>
                        <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">
                <?php if ($msg = flash('success')): ?>
                <div class="flex items-center gap-2 px-4 py-3 bg-success/10 text-success text-sm font-medium border border-success/20">
                    <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i> <?= $msg ?>
                </div>
                <?php endif; ?>
                <?php if ($msg = flash('error')): ?>
                <div class="flex items-center gap-2 px-4 py-3 bg-danger/10 text-danger text-sm font-medium border border-danger/20">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i> <?= $msg ?>
                </div>
                <?php endif; ?>

<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SportVenue - Booking Lapangan Olahraga Premium' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sport: {
                            25: '#fff8f0', 50: '#fff0e0', 100: '#ffe0c0', 200: '#ffc18a',
                            300: '#ffa255', 400: '#fc8320', 500: '#f97316', 600: '#ea580c',
                            700: '#c2410c', 800: '#9a3412', 900: '#7c2d12',
                        },
                        success: '#03ca77',
                        danger: '#e31748',
                        'dark-navy': '#001f3e',
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
    </script>

    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(249, 115, 22, 0.08);
        }
        .dark .glass-nav {
            background: rgba(17, 24, 39, 0.85);
            border-bottom: 1px solid rgba(249, 115, 22, 0.06);
        }
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #f97316;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 80%;
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background-color: #cbd5e1; }
        .dark ::-webkit-scrollbar-thumb { background-color: #475569; }
    </style>
    <script>
        (function() {
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="bg-slate-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 min-h-screen flex flex-col font-sans transition-colors duration-300">

<!-- Animated Background elements for public pages -->
<div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
    <div class="absolute top-[-20%] right-[-10%] w-[500px] h-[500px] bg-sport-400/20 dark:bg-sport-600/10 rounded-none mix-blend-multiply dark:mix-blend-lighten filter blur-[80px] opacity-70"></div>
    <div class="absolute top-[40%] left-[-10%] w-[400px] h-[400px] bg-blue-300/20 dark:bg-blue-900/10 rounded-none mix-blend-multiply dark:mix-blend-lighten filter blur-[80px] opacity-70"></div>
</div>

<header class="glass-nav sticky top-0 z-50 shadow-zp-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="<?= base_url('home') ?>" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-gradient-to-tr from-sport-600 to-sport-400 flex items-center justify-center shadow-lg border border-sport-500/20 group-hover:rotate-12 transition-transform duration-300">
                    <i data-lucide="trophy" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-2xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-sport-600 to-sport-400 tracking-tight">SportVenue</span>
            </a>
            
            <!-- Desktop Nav -->
            <nav class="hidden md:flex items-center gap-1">
                <a href="<?= base_url('home') ?>" class="nav-link px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-sport-600 dark:hover:text-sport-400 transition-colors">Katalog Lapangan</a>
                <a href="<?= base_url('history') ?>" class="nav-link px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-sport-600 dark:hover:text-sport-400 transition-colors">Riwayat Reservasi</a>
                <a href="<?= base_url('payment') ?>" class="nav-link px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-sport-600 dark:hover:text-sport-400 transition-colors">Upload Pembayaran</a>
            </nav>

            <div class="flex items-center gap-3">
                <!-- Theme Toggle -->
                <button onclick="toggleDarkMode()" class="w-10 h-10 flex items-center justify-center border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-all duration-300 hover:scale-105">
                    <i data-lucide="moon" class="w-4 h-4 dark:hidden"></i>
                    <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
                </button>

                <!-- Admin Button -->
                <a href="<?= base_url('admin') ?>" class="hidden sm:flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-gradient-to-r from-sport-500 to-sport-600 text-white hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    <span>Admin Portal</span>
                </a>

                <!-- Mobile Menu Button -->
                <button onclick="toggleMobileMenu()" class="md:hidden w-10 h-10 flex items-center justify-center border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition-colors">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-gray-100 dark:border-gray-800 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl">
        <div class="px-4 py-4 flex flex-col gap-2">
            <a href="<?= base_url('home') ?>" class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-none hover:bg-sport-50 dark:hover:bg-sport-900/20 hover:text-sport-600 dark:hover:text-sport-400 transition-colors">Katalog Lapangan</a>
            <a href="<?= base_url('history') ?>" class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-none hover:bg-sport-50 dark:hover:bg-sport-900/20 hover:text-sport-600 dark:hover:text-sport-400 transition-colors">Riwayat Reservasi</a>
            <a href="<?= base_url('payment') ?>" class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200 rounded-none hover:bg-sport-50 dark:hover:bg-sport-900/20 hover:text-sport-600 dark:hover:text-sport-400 transition-colors">Upload Pembayaran</a>
            <div class="h-px bg-gray-100 dark:bg-gray-800 my-2"></div>
            <a href="<?= base_url('admin') ?>" class="px-4 py-3 text-sm font-bold text-sport-600 dark:text-sport-400 rounded-none hover:bg-sport-50 dark:hover:bg-sport-900/20 flex items-center gap-2">
                <i data-lucide="shield" class="w-4 h-4"></i> Admin Portal
            </a>
        </div>
    </div>
</header>

<?php if (flash_has('success') || flash_has('error')): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 relative z-10">
    <?php if ($msg = flash('success')): ?>
    <div class="flex items-center gap-3 px-5 py-4 bg-success/10 border border-success/20 text-success text-sm font-bold shadow-sm">
        <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i>
        <span><?= $msg ?></span>
    </div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
    <div class="flex items-center gap-3 px-5 py-4 bg-danger/10 border border-danger/20 text-danger text-sm font-bold shadow-sm">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
        <span><?= $msg ?></span>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<main class="flex-1 relative z-10 w-full">

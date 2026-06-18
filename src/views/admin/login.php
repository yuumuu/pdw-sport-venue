<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SportVenue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
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
                    borderRadius: { 'zp': '0.75rem', 'zp-lg': '1rem', 'zp-pill': '9999px' },
                    animation: {
                        'blob': 'blob 7s infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                },
            },
        };
        (function() {
            if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
        })();
    </script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .input-focus-effect:focus-within label {
            color: #f97316;
        }
        .dark .input-focus-effect:focus-within label {
            color: #fb923c;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4 relative overflow-hidden text-gray-800 dark:text-gray-200">
    
    <!-- Animated Background Blobs -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-sport-400/30 dark:bg-sport-600/20 rounded-none mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute top-[20%] right-[-5%] w-96 h-96 bg-yellow-300/30 dark:bg-yellow-600/20 rounded-none mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-pink-300/30 dark:bg-pink-900/20 rounded-none mix-blend-multiply dark:mix-blend-lighten filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="w-full max-w-md w-full relative z-10">
        <!-- Login Card -->
        <div class="glass-panel rounded-none shadow-2xl p-8 transform transition-all duration-500 hover:-translate-y-1">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Portal</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Silakan masuk untuk melanjutkan</p>
            </div>

            <?php if ($msg = flash('error')): ?>
            <div class="flex items-center gap-3 px-4 py-3 mb-6 rounded-none bg-danger/10 border border-danger/20 text-danger text-sm font-medium animate-pulse">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <span><?= $msg ?></span>
            </div>
            <?php endif; if ($msg = flash('success')): ?>
            <div class="flex items-center gap-3 px-4 py-3 mb-6 rounded-none bg-success/10 border border-success/20 text-success text-sm font-medium">
                <i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i>
                <span><?= $msg ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('admin/login') ?>" class="space-y-6">
                <?= csrf_field() ?>
                
                <div class="input-focus-effect relative">
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700 dark:text-gray-300 transition-colors">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text" name="username" required autocomplete="username" placeholder="Masukkan username" class="w-full pl-10 pr-4 py-3 text-sm border-2 border-gray-200 dark:border-gray-700 rounded-none bg-white/50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-0 focus:border-sport-500 transition-all outline-none">
                    </div>
                </div>

                <div class="input-focus-effect relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 transition-colors">Password</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="w-full pl-10 pr-4 py-3 text-sm border-2 border-gray-200 dark:border-gray-700 rounded-none bg-white/50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-0 focus:border-sport-500 transition-all outline-none">
                    </div>
                </div>

                <button type="submit" class="group w-full relative inline-flex items-center justify-center px-4 py-3.5 bg-gradient-to-r from-sport-500 to-sport-600 text-white text-sm font-bold rounded-none overflow-hidden shadow-lg hover:shadow-sport-500/30 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sport-500 dark:focus:ring-offset-gray-900">
                    <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-none group-hover:w-56 group-hover:h-56 opacity-10"></span>
                    <span class="relative flex items-center gap-2">
                        Masuk ke Dashboard
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <a href="<?= base_url('home') ?>" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-sport-600 dark:text-gray-400 dark:hover:text-sport-400 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }
    </script>
</body>
</html>

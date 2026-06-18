</main>

<footer class="mt-auto relative z-10">
    <div class="border-t border-sport-500/10 dark:border-sport-500/5 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="flex flex-col md:flex-row justify-between items-start gap-10">
                <div class="max-w-sm">
                    <a href="<?= base_url('home') ?>" class="flex items-center gap-2 text-sport-500 font-bold text-xl mb-3">
                        <i data-lucide="trophy" class="w-6 h-6"></i>
                        SportVenue
                    </a>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        Platform reservasi lapangan olahraga. Pesan lapangan kapan saja, di mana saja.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="#" class="w-10 h-10 flex items-center justify-center border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-sport-500 hover:border-sport-500/30 transition-all bg-white dark:bg-gray-800">
                        <i data-lucide="instagram" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-sport-500 hover:border-sport-500/30 transition-all bg-white dark:bg-gray-800">
                        <i data-lucide="facebook" class="w-5 h-5"></i>
                    </a>
                    <a href="#" class="w-10 h-10 flex items-center justify-center border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-sport-500 hover:border-sport-500/30 transition-all bg-white dark:bg-gray-800">
                        <i data-lucide="twitter" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
            
            <div class="border-t border-gray-100 dark:border-gray-800 mt-10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                <p>&copy; <?= date('Y') ?> SportVenue. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-sport-500 transition-colors">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-sport-500 transition-colors">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
lucide.createIcons();

function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
}

function toggleMobileMenu() {
    document.getElementById('mobileMenu')?.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('mobileMenu');
    if (menu && !menu.classList.contains('hidden') && !e.target.closest('header')) {
        menu.classList.add('hidden');
    }
});
</script>
</body>
</html>

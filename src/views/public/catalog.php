<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    
    <!-- Hero Section -->
    <div class="relative rounded-none md:rounded-none overflow-hidden bg-gray-900 mb-12 shadow-zp">
        <div class="absolute inset-0 bg-gradient-to-r from-sport-900 to-sport-800 opacity-90"></div>
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1543326727-cf6c39e8f84c?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center mix-blend-overlay opacity-50"></div>
        
        <div class="relative z-10 p-8 md:p-16 flex flex-col md:flex-row items-center justify-between gap-8 text-white">
            <div class="max-w-xl">
                <h1 class="text-3xl md:text-5xl font-extrabold mb-4 tracking-tight">Temukan Lapangan Favoritmu</h1>
                <p class="text-sport-100 text-lg md:text-xl font-medium mb-8">Pesan sekarang dan mulai permainan. Tersedia <?= count($fields) ?> lapangan untuk Anda.</p>
                
                <div class="flex items-center bg-white/10 backdrop-blur-md border border-white/20 rounded-none p-2 max-w-sm">
                    <div class="pl-3 pr-2 text-white/70">
                        <i data-lucide="filter" class="w-5 h-5"></i>
                    </div>
                    <select id="sportFilter" onchange="applyFilters()" class="w-full bg-transparent text-white border-none focus:ring-0 appearance-none py-2 font-medium cursor-pointer outline-none">
                        <option value="" class="text-gray-900">Semua Olahraga</option>
                        <?php foreach ($sports as $s): ?>
                        <option value="<?= e($s) ?>" <?= $selectedSport === $s ? 'selected' : '' ?> class="text-gray-900"><?= e($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="hidden md:block">
                <div class="w-32 h-32 bg-gradient-to-tr from-sport-500 to-sport-300 rounded-none blur-2xl opacity-60 absolute top-10 right-20 animate-blob"></div>
                <i data-lucide="goal" class="w-40 h-40 text-white/20 transform rotate-12 relative z-10"></i>
            </div>
        </div>
    </div>

    <!-- Catalog Grid -->
    <?php if (empty($fields)): ?>
    <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-none border border-gray-100 dark:border-gray-800 shadow-zp-sm">
        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-none flex items-center justify-center mx-auto mb-4">
            <i data-lucide="search-x" class="w-10 h-10 text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Tidak Ditemukan</h3>
        <p class="text-gray-500 dark:text-gray-400">Maaf, kami tidak dapat menemukan lapangan sesuai filter Anda.</p>
        <button onclick="document.getElementById('sportFilter').value=''; applyFilters();" class="mt-6 px-6 py-2.5 bg-sport-50 dark:bg-sport-900/20 text-sport-600 dark:text-sport-400 font-semibold rounded-none hover:bg-sport-100 dark:hover:bg-sport-900/40 transition-colors">Lihat Semua Lapangan</button>
    </div>
    <?php else: ?>
    
    <div id="fieldGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        <?php foreach ($fields as $field): ?>
        <div class="group bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col">
            
            <!-- Image Area -->
            <div class="relative h-56 w-full bg-gray-200 dark:bg-gray-800 overflow-hidden border-b border-gray-200 dark:border-gray-700">
                <?php if (!empty($field['image']) && file_exists(__DIR__ . '/../../../public/assets/uploads/' . $field['image'])): ?>
                    <img src="<?= base_url('assets/uploads/' . $field['image']) ?>" alt="<?= e($field['name']) ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                <?php else: ?>
                    <div class="absolute inset-0 bg-gradient-to-br from-sport-100 to-sport-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center transform group-hover:scale-110 transition-transform duration-700">
                        <i data-lucide="image" class="w-12 h-12 text-sport-300 dark:text-gray-600"></i>
                    </div>
                <?php endif; ?>
                
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold bg-white/90 dark:bg-gray-900/90 text-sport-600 dark:text-sport-400 backdrop-blur-md shadow-sm border border-white/20 dark:border-gray-800/50">
                        <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                        <?= e($field['sport']) ?>
                    </span>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="p-6 flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight group-hover:text-sport-500 transition-colors"><?= e($field['name']) ?></h3>
                </div>
                
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4 font-medium">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    Kapasitas: <?= e($field['capacity']) ?>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 line-clamp-2 flex-1 leading-relaxed">
                    <?= e($field['description']) ?>
                </p>
                
                <!-- Footer / Action -->
                <div class="flex items-center justify-between pt-5 border-t border-gray-200 dark:border-gray-700 mt-auto">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Harga Sewa</p>
                        <p class="text-lg font-extrabold text-sport-600 dark:text-sport-400 tracking-tight">
                            Rp<?= number_format($field['price_per_hour'], 0, ',', '.') ?><span class="text-sm font-medium text-gray-400">/jam</span>
                        </p>
                    </div>
                    <a href="<?= base_url('schedule?id=' . $field['id']) ?>" class="inline-flex items-center justify-center w-12 h-12 bg-sport-500 text-white hover:bg-sport-600 transition-all duration-300 shadow-sm hover:shadow-md">
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function applyFilters() {
    var sport = document.getElementById('sportFilter').value;
    var params = new URLSearchParams(window.location.search);
    if (sport) params.set('sport', sport);
    else params.delete('sport');
    window.location.search = params.toString();
}
</script>

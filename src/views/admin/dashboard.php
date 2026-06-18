<?php 
$title = 'Dashboard Admin - SportVenue';
include __DIR__ . '/../layouts/admin_header.php'; 
?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white dark:bg-gray-900 rounded-none border border-gray-100 dark:border-gray-800 p-4 shadow-zp-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Total Reservasi</p>
                <p class="text-2xl font-bold mt-1"><?= e($stats['total'] ?? 0) ?></p>
            </div>
            <div class="w-10 h-10 bg-sport-25 dark:bg-sport-900/20 border border-sport-500/20 flex items-center justify-center">
                <i data-lucide="calendar" class="w-5 h-5 text-sport-500"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-none border border-gray-100 dark:border-gray-800 p-4 shadow-zp-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Pending</p>
                <p class="text-2xl font-bold mt-1"><?= e($stats['pending'] ?? 0) ?></p>
            </div>
            <div class="w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-500/20 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-none border border-gray-100 dark:border-gray-800 p-4 shadow-zp-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Menunggu Verifikasi</p>
                <p class="text-2xl font-bold mt-1"><?= e($stats['pending_validation'] ?? 0) ?></p>
            </div>
            <div class="w-10 h-10 bg-sport-25 dark:bg-sport-900/20 border border-sport-500/20 flex items-center justify-center">
                <i data-lucide="search" class="w-5 h-5 text-sport-500"></i>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-none border border-gray-100 dark:border-gray-800 p-4 shadow-zp-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase">Pendapatan</p>
                <p class="text-2xl font-bold mt-1 text-success">Rp<?= number_format($stats['revenue'] ?? 0, 0, ',', '.') ?></p>
            </div>
            <div class="w-10 h-10 bg-success/10 border border-success/20 flex items-center justify-center">
                <i data-lucide="wallet" class="w-5 h-5 text-success"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <div class="bg-white dark:bg-gray-900 rounded-none border border-gray-100 dark:border-gray-800 p-5 shadow-zp-sm">
        <h3 class="font-bold mb-4">Reservasi per Lapangan</h3>
        <canvas id="fieldChart" height="200"></canvas>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-none border border-gray-100 dark:border-gray-800 p-5 shadow-zp-sm">
        <h3 class="font-bold mb-4">Verifikasi Pembayaran Terbaru</h3>
        <?php if (empty($recentPayments)): ?>
        <p class="text-sm text-gray-400 py-4 text-center">Belum ada pembayaran yang perlu diverifikasi.</p>
        <?php else: ?>
        <div class="space-y-2">
            <?php foreach (array_slice($recentPayments, 0, 5) as $p): ?>
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/50 p-2 rounded-none transition-colors">
                <div>
                    <p class="text-sm font-medium">#<?= $p['id'] ?> — <?= e($p['field_name']) ?></p>
                    <p class="text-xs text-gray-400"><?= e($p['customer_name']) ?></p>
                </div>
                <?php if ($p['payment_status'] === 'pending_validation'): ?>
                <span class="text-xs px-2 py-0.5 rounded-none bg-yellow-50 text-yellow-600 font-semibold border border-yellow-200">Verifikasi</span>
                <?php elseif ($p['payment_status'] === 'paid'): ?>
                <span class="text-xs px-2 py-0.5 rounded-none bg-success/10 text-success font-semibold border border-green-200">Lunas</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$extraScripts = '
<script>
    var fieldChart;
    function initCharts() {
        if (!document.getElementById("fieldChart")) return;
        var ctx = document.getElementById("fieldChart").getContext("2d");
        var labels = ' . json_encode(array_map(function($b) { return $b["name"]; }, $bookingsByField)) . ';
        var data = ' . json_encode(array_map(function($b) { return (int)$b["total"]; }, $bookingsByField)) . ';
        var textColor = isDark() ? "#9ca3af" : "#6b7280";
        var gridColor = isDark() ? "#374151" : "#e5e7eb";

        fieldChart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Reservasi",
                    data: data,
                    backgroundColor: "#f97316",
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function updateCharts() {
        if (fieldChart) {
            var textColor = isDark() ? "#9ca3af" : "#6b7280";
            var gridColor = isDark() ? "#374151" : "#e5e7eb";
            fieldChart.options.scales.y.ticks.color = textColor;
            fieldChart.options.scales.y.grid.color = gridColor;
            fieldChart.options.scales.x.ticks.color = textColor;
            fieldChart.update();
        }
    }

    document.addEventListener("DOMContentLoaded", initCharts);
</script>
';
include __DIR__ . '/../layouts/admin_footer.php'; 
?>

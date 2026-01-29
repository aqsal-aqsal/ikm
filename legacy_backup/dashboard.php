<?php 
require_once 'includes/config.php';
requireAuth();

// Handle Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $stmt = $pdo->query("
        SELECT 
            u.nama as unit_nama, 
            COUNT(s.id) as total_responden, 
            AVG(s.nilai_rata) as avg_nilai 
        FROM survey s
        JOIN units u ON s.unit_id = u.id
        GROUP BY u.nama
    ");
    $stats = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="dashboard_ikm_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Unit Layanan', 'Total Responden', 'Rata-rata IKM', 'Target Achievement']);
    
    foreach ($stats as $row) {
        $avgIkm = $row['avg_nilai'] * 25; // Scale 4 to 100
        $target = min(100, round($avgIkm));
        fputcsv($output, [
            $row['unit_nama'], 
            $row['total_responden'], 
            number_format($avgIkm, 2), 
            $target . '%'
        ]);
    }
    fclose($output);
    exit;
}

// Fetch Data for Dashboard
try {
    // Total Responden
    $stmt = $pdo->query("SELECT COUNT(*) FROM survey");
    $totalResponden = $stmt->fetchColumn();

    // Average IKM (Global)
    $stmt = $pdo->query("SELECT AVG(indeks) FROM survey");
    $avgNilai = $stmt->fetchColumn();
    $ikmScore = $avgNilai ? ($avgNilai * 25) : 0; // Assuming indeks is 1-4 scale. If it's 25-100, remove *25. 
    // Wait, typical IKM indeks is 25-100 (converted) or 1-4.
    // Let's assume 'indeks' in DB is the final score (0-100) or close to it.
    // If previous code used `nilai_rata * 25`, it implies `nilai_rata` was 1-4.
    // I will assume `indeks` is the raw average (1-4).
    $ikmScore = $avgNilai ? ($avgNilai * 25) : 0;

    // Unit Stats
    $stmt = $pdo->query("
        SELECT 
            u.nama as unit_nama, 
            COUNT(s.id) as count, 
            AVG(s.indeks) as avg_nilai 
        FROM survey s
        JOIN units u ON s.unit_id = u.id
        GROUP BY u.nama
        ORDER BY count DESC
    ");
    $unitStats = $stmt->fetchAll();

    // Total Units with Data
    $totalUnits = count($unitStats);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Dashboard IKM</h1>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="alert('Fitur Switch Dashboard akan segera hadir!')" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                Switch dashboard
            </button>
            <a href="?export=csv" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
                Export report
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="#" class="border-slate-500 text-slate-900 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page">
                Overview
            </a>
            <a href="#" class="border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Unit Layanan
            </a>
            <a href="#" class="border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Responden
            </a>
        </nav>
    </div>
    
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Total Responden -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-slate-900">Total Responden</h3>
                <button class="text-slate-400 hover:text-slate-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-semibold text-slate-900"><?php echo number_format($totalResponden, 0, ',', '.'); ?></p>
                <span class="inline-flex items-baseline px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 md:mt-2 lg:mt-0">
                    <svg class="-ml-1 mr-0.5 h-3 w-3 flex-shrink-0 self-center text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    12%
                </span>
                <span class="text-sm text-slate-500">vs last month</span>
            </div>
            <div class="mt-4 h-10 w-full">
                <svg class="h-full w-full text-sky-100" fill="none" viewBox="0 0 300 50" preserveAspectRatio="none">
                    <path d="M0 40 Q 30 35, 60 42 T 120 30 T 180 35 T 240 20 T 300 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                    <path d="M0 40 Q 30 35, 60 42 T 120 30 T 180 35 T 240 20 T 300 10 V 50 H 0 Z" fill="url(#gradient-sky)" opacity="0.2" />
                    <defs>
                        <linearGradient id="gradient-sky" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#0ea5e9" />
                            <stop offset="100%" stop-color="#fff" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <!-- Card 2: Indeks Kepuasan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-slate-900">Indeks Kepuasan</h3>
                <button class="text-slate-400 hover:text-slate-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-semibold text-slate-900"><?php echo number_format($ikmScore, 2); ?></p>
                <span class="inline-flex items-baseline px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 md:mt-2 lg:mt-0">
                    <svg class="-ml-1 mr-0.5 h-3 w-3 flex-shrink-0 self-center text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    4.5%
                </span>
                <span class="text-sm text-slate-500">vs last month</span>
            </div>
            <div class="mt-4 h-10 w-full">
                <svg class="h-full w-full text-sky-100" fill="none" viewBox="0 0 300 50" preserveAspectRatio="none">
                    <path d="M0 35 Q 40 40, 80 30 T 160 35 T 240 25 T 300 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                    <path d="M0 35 Q 40 40, 80 30 T 160 35 T 240 25 T 300 15 V 50 H 0 Z" fill="url(#gradient-sky)" opacity="0.2" />
                </svg>
            </div>
        </div>

        <!-- Card 3: Unit Layanan -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-slate-900">Unit Layanan</h3>
                <button class="text-slate-400 hover:text-slate-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-semibold text-slate-900"><?php echo $totalUnits; ?></p>
                <span class="inline-flex items-baseline px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 md:mt-2 lg:mt-0">
                    <svg class="-ml-1 mr-0.5 h-3 w-3 flex-shrink-0 self-center text-emerald-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    2
                </span>
                <span class="text-sm text-slate-500">active units</span>
            </div>
            <div class="mt-4 h-10 w-full flex items-end gap-1">
                <div class="w-1/6 bg-sky-200 rounded-t h-[40%]"></div>
                <div class="w-1/6 bg-sky-300 rounded-t h-[60%]"></div>
                <div class="w-1/6 bg-sky-400 rounded-t h-[50%]"></div>
                <div class="w-1/6 bg-sky-500 rounded-t h-[80%]"></div>
                <div class="w-1/6 bg-sky-600 rounded-t h-[70%]"></div>
                <div class="w-1/6 bg-sky-700 rounded-t h-[90%]"></div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-slate-900">Performa Unit Layanan</h3>
            <button class="text-sm text-sky-600 hover:text-sky-700 font-medium">View All</button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Unit Layanan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Responden</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">IKM</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Target</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php if (empty($unitStats)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-slate-500">Belum ada data survei</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($unitStats as $stat): 
                            $avgIkmUnit = $stat['avg_nilai'] * 25;
                            $targetPercent = min(100, round($avgIkmUnit));
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"><?php echo htmlspecialchars($stat['unit_nama']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo number_format($stat['count'], 0, ',', '.'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo number_format($avgIkmUnit, 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm text-slate-600 mr-2"><?php echo $targetPercent; ?>%</span>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5 max-w-[100px]">
                                        <div class="bg-sky-600 h-1.5 rounded-full" style="width: <?php echo $targetPercent; ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-slate-400 hover:text-slate-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
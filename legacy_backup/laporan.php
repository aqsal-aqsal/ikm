<?php 
require_once 'includes/config.php';
requireAuth();
requireRole(['SUPERADMIN', 'ADMIN_PROVINSI', 'ADMIN_UPPD']);

// Export CSV Logic
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $sql = "
        SELECT 
            s.tanggal,
            r.nama as responden_nama,
            r.umur as responden_umur,
            r.jk as responden_jk,
            r.pendidikan as responden_pendidikan,
            r.pekerjaan as responden_pekerjaan,
            u.nama as unit_nama,
            s.indeks as nilai_rata,
            s.saran
        FROM survey s
        JOIN responden r ON s.responden_id = r.id
        JOIN units u ON s.unit_id = u.id
    ";
    
    // Add filtering logic here if needed (e.g. by unit for admin_uppd)
    if ($_SESSION['user']['role'] === 'ADMIN_UPPD') {
        $sql .= " WHERE u.id = " . $_SESSION['user']['unit_id'];
    }
    
    $sql .= " ORDER BY s.tanggal DESC";

    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_survey_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Tanggal', 'Nama Responden', 'Umur', 'JK', 'Pendidikan', 'Pekerjaan', 'Unit Layanan', 'Nilai Rata-rata', 'Saran']);
    
    foreach ($data as $row) {
        fputcsv($output, [
            $row['tanggal'],
            $row['responden_nama'],
            $row['responden_umur'],
            $row['responden_jk'],
            $row['responden_pendidikan'],
            $row['responden_pekerjaan'],
            $row['unit_nama'],
            number_format((float)($row['nilai_rata'] ?? 0), 2),
            $row['saran']
        ]);
    }
    fclose($output);
    exit;
}

// Fetch Data for Display
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

if ($_SESSION['user']['role'] === 'ADMIN_UPPD') {
    $whereClauses[] = "u.id = ?";
    $params[] = $_SESSION['user']['unit_id'];
}

if ($search) {
    $whereClauses[] = "(r.nama LIKE ? OR u.nama LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereSQL = "";
if (!empty($whereClauses)) {
    $whereSQL = " WHERE " . implode(" AND ", $whereClauses);
}

// Count Total
$countSql = "SELECT COUNT(*) FROM survey s JOIN responden r ON s.responden_id = r.id JOIN units u ON s.unit_id = u.id" . $whereSQL;
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRecords = $stmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch Records
$sql = "
    SELECT 
        s.tanggal,
        r.nama as responden_nama,
        r.umur as responden_umur,
        r.jk as responden_jk,
        r.pendidikan as responden_pendidikan,
        r.pekerjaan as responden_pekerjaan,
        u.nama as unit_nama,
        s.indeks as nilai_rata,
        s.saran
    FROM survey s
    JOIN responden r ON s.responden_id = r.id
    JOIN units u ON s.unit_id = u.id
    $whereSQL
    ORDER BY s.tanggal DESC
    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$surveys = $stmt->fetchAll();

include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Laporan Survey</h1>
            <p class="mt-1 text-sm text-slate-500">Rekap data respon survei masyarakat.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="?export=csv" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-base font-semibold text-slate-900">Data Masuk</h3>
             <form method="GET" class="relative max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="block w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" placeholder="Search survey..." onchange="this.form.submit()">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Responden</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nilai Rata-rata</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php if (empty($surveys)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-xs text-slate-400">
                            Tidak ada data survey.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($surveys as $s): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500"><?php echo date('d M Y', strtotime($s['tanggal'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-900 font-medium"><?php echo htmlspecialchars($s['responden_nama']); ?></div>
                                <div class="text-xs text-slate-500"><?php echo htmlspecialchars($s['responden_pekerjaan']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500"><?php echo htmlspecialchars($s['unit_nama']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $s['nilai_rata'] >= 3 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo number_format($s['nilai_rata'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <button class="text-sky-600 hover:text-sky-900" onclick="alert('Detail saran: <?php echo htmlspecialchars($s['saran'] ?? '-'); ?>')">Lihat</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-slate-200 flex justify-between items-center">
            <div class="text-sm text-slate-500">
                Page <?php echo $page; ?> of <?php echo $totalPages; ?>
            </div>
            <div class="flex space-x-2">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="px-3 py-1 border border-slate-300 rounded-md text-sm hover:bg-slate-50">Prev</a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="px-3 py-1 border border-slate-300 rounded-md text-sm hover:bg-slate-50">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

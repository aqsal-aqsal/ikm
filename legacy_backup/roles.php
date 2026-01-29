<?php 
require_once 'includes/config.php';
requireRole(['SUPERADMIN']);

$success = '';
$error = '';

$permissions = [
    'manage_users' => 'Kelola Pengguna',
    'manage_units' => 'Kelola Layanan',
    'view_reports' => 'Lihat Laporan'
];

$roles = ['SUPERADMIN', 'ADMIN_PROVINSI', 'ADMIN_UPPD', 'OPERATOR'];

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Clear existing permissions
        $pdo->exec("DELETE FROM role_permissions");
        
        // Insert new permissions
        $stmt = $pdo->prepare("INSERT INTO role_permissions (role, permission_key) VALUES (?, ?)");
        
        $matrix = $_POST['matrix'] ?? [];
        
        foreach ($roles as $role) {
            if (isset($matrix[$role])) {
                foreach ($matrix[$role] as $perm => $val) {
                    if ($val === '1' && array_key_exists($perm, $permissions)) {
                        $stmt->execute([$role, $perm]);
                    }
                }
            }
        }
        
        $pdo->commit();
        $success = 'Pengaturan role berhasil disimpan.';
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
    }
}

// Fetch current permissions
$matrix = [];
foreach ($roles as $role) {
    $matrix[$role] = array_fill_keys(array_keys($permissions), false);
}

// Ensure table exists (just in case)
$pdo->exec("CREATE TABLE IF NOT EXISTS role_permissions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    role ENUM('SUPERADMIN','ADMIN_PROVINSI','ADMIN_UPPD','OPERATOR') NOT NULL,
    permission_key VARCHAR(50) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$stmt = $pdo->query("SELECT role, permission_key FROM role_permissions");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (isset($matrix[$row['role']][$row['permission_key']])) {
        $matrix[$row['role']][$row['permission_key']] = true;
    }
}

include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Role & Hak Akses</h1>
            <p class="mt-1 text-sm text-slate-500">Atur fungsi apa saja yang boleh diakses oleh setiap role.</p>
        </div>
        <div class="flex items-center gap-3">
             <button type="submit" form="roles-form" class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-sky-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Pengaturan
            </button>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($success): ?>
    <div class="bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3"><p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3"><p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-base font-semibold text-slate-900">Daftar Role</h3>
        </div>
        <div class="overflow-x-auto">
            <form id="roles-form" method="POST">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                            <?php foreach ($permissions as $key => $label): ?>
                            <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider"><?php echo htmlspecialchars($label); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-900 font-medium"><?php echo $role; ?></td>
                            <?php foreach ($permissions as $key => $label): 
                                $checked = $matrix[$role][$key] ? 'checked' : '';
                            ?>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <input type="checkbox" name="matrix[<?php echo $role; ?>][<?php echo $key; ?>]" value="1" <?php echo $checked; ?> 
                                    class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Informasi</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Pengaturan ini sebagai template hak akses. Implementasi pengecekan di setiap fitur berjalan secara otomatis berdasarkan role pengguna.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

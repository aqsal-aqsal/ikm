<?php 
require_once 'includes/config.php';
requireRole(['SUPERADMIN']);

$error = '';
$success = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;
    $nama = $_POST['nama'] ?? '';
    $jenis = $_POST['jenis'] ?? 'UPPD';

    try {
        if ($action === 'create') {
            $stmt = $pdo->prepare("INSERT INTO units (nama, jenis) VALUES (?, ?)");
            $stmt->execute([$nama, $jenis]);
            $success = 'Unit berhasil ditambahkan.';
        } elseif ($action === 'update' && $id) {
            $stmt = $pdo->prepare("UPDATE units SET nama = ?, jenis = ? WHERE id = ?");
            $stmt->execute([$nama, $jenis, $id]);
            $success = 'Unit berhasil diperbarui.';
        } elseif ($action === 'delete' && $id) {
            // Check dependency
            $check = $pdo->prepare("SELECT COUNT(*) FROM survey WHERE unit_id = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                $error = 'Unit tidak bisa dihapus karena sudah memiliki data survei.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM units WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Unit berhasil dihapus.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch Units
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM units";
$params = [];
if ($search) {
    $sql .= " WHERE nama LIKE ? OR jenis LIKE ?";
    $params = ["%$search%", "%$search%"];
}
$sql .= " ORDER BY nama ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$units = $stmt->fetchAll();

include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Master Layanan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola daftar unit layanan Bapenda & UPPD.</p>
        </div>
        <div class="flex items-center gap-3">
             <button onclick="openModal('create')" class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-sky-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Unit
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
            <h3 class="text-base font-semibold text-slate-900">Daftar Unit</h3>
             <form method="GET" class="relative max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="block w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" placeholder="Search unit..." onchange="this.form.submit()">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php if (empty($units)): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-center text-xs text-slate-400">Tidak ada data unit.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($units as $unit): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-900"><?php echo htmlspecialchars($unit['nama']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $unit['jenis'] === 'BAPENDA' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo htmlspecialchars($unit['jenis']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button onclick='openModal("update", <?php echo json_encode($unit); ?>)' class="text-sky-600 hover:text-sky-900">Edit</button>
                                <button onclick='confirmDelete(<?php echo $unit["id"]; ?>)' class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Drawer / Modal -->
    <div id="unit-modal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full max-w-md rounded-xl shadow-xl border border-slate-200 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 id="modal-title" class="text-lg font-semibold text-slate-900">Tambah Unit</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" class="px-6 py-4 space-y-4">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="form-id">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Nama Unit</label>
                    <input name="nama" id="form-nama" type="text" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Jenis</label>
                    <select name="jenis" id="form-jenis" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                        <option value="BAPENDA">BAPENDA</option>
                        <option value="UPPD">UPPD</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 border border-slate-300 shadow-sm">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 shadow-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Form (Hidden) -->
    <form id="delete-form" method="POST" class="hidden">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete-id">
    </form>
</div>

<script>
function openModal(mode, data = null) {
    const modal = document.getElementById('unit-modal');
    const title = document.getElementById('modal-title');
    const action = document.getElementById('form-action');
    const idInput = document.getElementById('form-id');
    const namaInput = document.getElementById('form-nama');
    const jenisInput = document.getElementById('form-jenis');

    modal.classList.remove('hidden');

    if (mode === 'create') {
        title.textContent = 'Tambah Unit';
        action.value = 'create';
        idInput.value = '';
        namaInput.value = '';
        jenisInput.value = 'UPPD';
    } else {
        title.textContent = 'Edit Unit';
        action.value = 'update';
        idInput.value = data.id;
        namaInput.value = data.nama;
        jenisInput.value = data.jenis;
    }
}

function closeModal() {
    document.getElementById('unit-modal').classList.add('hidden');
}

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus unit ini?')) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-form').submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
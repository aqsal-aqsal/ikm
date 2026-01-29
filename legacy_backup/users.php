<?php 
require_once 'includes/config.php';
requireRole(['SUPERADMIN']);

$error = '';
$success = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'OPERATOR';
    $unit_id = !empty($_POST['unit_id']) ? $_POST['unit_id'] : null;

    try {
        if ($action === 'create') {
            // Check username exists
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $check->execute([$username]);
            if ($check->fetchColumn() > 0) {
                $error = 'Username sudah digunakan.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role, unit_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hash, $role, $unit_id]);
                $success = 'Pengguna berhasil ditambahkan.';
            }
        } elseif ($action === 'update' && $id) {
            $sql = "UPDATE users SET username = ?, role = ?, unit_id = ?";
            $params = [$username, $role, $unit_id];
            
            if (!empty($password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($password, PASSWORD_BCRYPT);
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = 'Pengguna berhasil diperbarui.';
        } elseif ($action === 'delete' && $id) {
            // Prevent deleting self
            if ($id == $_SESSION['user']['id']) {
                $error = 'Anda tidak dapat menghapus akun Anda sendiri.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Pengguna berhasil dihapus.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch Users
$search = $_GET['search'] ?? '';
$sql = "SELECT u.*, un.nama as unit_nama FROM users u LEFT JOIN units un ON u.unit_id = un.id";
$params = [];
if ($search) {
    $sql .= " WHERE u.username LIKE ?";
    $params[] = "%$search%";
}
$sql .= " ORDER BY u.username ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

// Fetch Units for Dropdown
$stmt = $pdo->query("SELECT * FROM units ORDER BY nama ASC");
$units = $stmt->fetchAll();

include 'includes/header.php'; 
include 'includes/sidebar.php'; 
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Kelola Pengguna</h1>
            <p class="mt-1 text-sm text-slate-500">Atur akun admin provinsi, admin UPPD, dan operator.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openModal('create')" class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-sky-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengguna
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
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-base font-semibold text-slate-900">Daftar Pengguna</h3>
            <form method="GET" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="block w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" placeholder="Cari pengguna..." onchange="this.form.submit()">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada data pengguna.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-900 font-medium"><?php echo htmlspecialchars($u['username']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">
                                    <?php echo htmlspecialchars($u['role']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500"><?php echo htmlspecialchars($u['unit_nama'] ?? '-'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button onclick='openModal("update", <?php echo json_encode($u); ?>)' class="text-sky-600 hover:text-sky-900">Edit</button>
                                <button onclick='confirmDelete(<?php echo $u["id"]; ?>)' class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="user-modal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full md:max-w-md rounded-xl shadow-xl border border-slate-200 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 id="modal-title" class="text-lg font-semibold text-slate-900">Tambah Pengguna</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form method="POST" class="px-6 py-4 space-y-4">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="form-id">

                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Username</label>
                    <input name="username" id="form-username" type="text" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Password</label>
                    <input name="password" id="form-password" type="password" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" placeholder="Kosongkan jika tidak mengganti password">
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Role</label>
                    <select name="role" id="form-role" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                        <option value="SUPERADMIN">SUPERADMIN</option>
                        <option value="ADMIN_PROVINSI">ADMIN_PROVINSI</option>
                        <option value="ADMIN_UPPD">ADMIN_UPPD</option>
                        <option value="OPERATOR">OPERATOR</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Unit Layanan</label>
                    <select name="unit_id" id="form-unit" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">Tanpa unit</option>
                        <?php foreach ($units as $unit): ?>
                        <option value="<?php echo $unit['id']; ?>"><?php echo htmlspecialchars($unit['nama']); ?></option>
                        <?php endforeach; ?>
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
    const modal = document.getElementById('user-modal');
    const title = document.getElementById('modal-title');
    const action = document.getElementById('form-action');
    const idInput = document.getElementById('form-id');
    const usernameInput = document.getElementById('form-username');
    const passwordInput = document.getElementById('form-password');
    const roleInput = document.getElementById('form-role');
    const unitInput = document.getElementById('form-unit');

    modal.classList.remove('hidden');

    if (mode === 'create') {
        title.textContent = 'Tambah Pengguna';
        action.value = 'create';
        idInput.value = '';
        usernameInput.value = '';
        passwordInput.value = '';
        passwordInput.required = true;
        passwordInput.placeholder = 'Masukkan password';
        roleInput.value = 'OPERATOR';
        unitInput.value = '';
    } else {
        title.textContent = 'Edit Pengguna';
        action.value = 'update';
        idInput.value = data.id;
        usernameInput.value = data.username;
        passwordInput.value = '';
        passwordInput.required = false;
        passwordInput.placeholder = 'Kosongkan jika tidak mengganti password';
        roleInput.value = data.role;
        unitInput.value = data.unit_id || '';
    }
}

function closeModal() {
    document.getElementById('user-modal').classList.add('hidden');
}

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
        document.getElementById('delete-id').value = id;
        document.getElementById('delete-form').submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
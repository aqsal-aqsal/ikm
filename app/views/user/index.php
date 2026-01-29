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
    <?php if ($data['success']): ?>
    <div class="bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <!-- Heroicons Check Circle -->
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3"><p class="text-sm text-green-700"><?php echo htmlspecialchars($data['success']); ?></p></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($data['error']): ?>
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3"><p class="text-sm text-red-700"><?php echo htmlspecialchars($data['error']); ?></p></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-base font-semibold text-slate-900">Daftar Pengguna</h3>
            <form action="<?= BASEURL; ?>/user" method="GET" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" placeholder="Cari pengguna..." onchange="this.form.submit()">
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
                    <?php if (empty($data['users'])): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada data pengguna.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($data['users'] as $u): ?>
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
                                <button onclick="openDeleteModal(<?= $u['id']; ?>)" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($data['totalPages'] > 1): ?>
        <div class="px-6 py-4 border-t border-slate-200 flex justify-between items-center">
            <div class="text-sm text-slate-500">
                Page <?php echo $data['page']; ?> of <?php echo $data['totalPages']; ?>
            </div>
            <div class="flex space-x-2">
                <?php if ($data['page'] > 1): ?>
                    <a href="<?= BASEURL; ?>/user?page=<?php echo $data['page'] - 1; ?>&search=<?php echo urlencode($data['search']); ?>" class="px-3 py-1 border border-slate-300 rounded-md text-sm hover:bg-slate-50 text-slate-600">Previous</a>
                <?php else: ?>
                    <span class="px-3 py-1 border border-slate-200 rounded-md text-sm text-slate-300 cursor-not-allowed bg-slate-50">Previous</span>
                <?php endif; ?>
                
                <?php if ($data['page'] < $data['totalPages']): ?>
                    <a href="<?= BASEURL; ?>/user?page=<?php echo $data['page'] + 1; ?>&search=<?php echo urlencode($data['search']); ?>" class="px-3 py-1 border border-slate-300 rounded-md text-sm hover:bg-slate-50 text-slate-600">Next</a>
                <?php else: ?>
                    <span class="px-3 py-1 border border-slate-200 rounded-md text-sm text-slate-300 cursor-not-allowed bg-slate-50">Next</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    

    <!-- Modal Form -->
    <div id="user-modal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full md:max-w-md rounded-xl shadow-xl border border-slate-200 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 id="modal-title" class="text-lg font-semibold text-slate-900">Tambah Pengguna</h2>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= BASEURL; ?>/user" method="POST" class="px-6 py-4 space-y-4">
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
                        <?php foreach ($data['roles'] as $role): ?>
                        <option value="<?= $role; ?>"><?= $role; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Unit Layanan</label>
                    <select name="unit_id" id="form-unit" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <option value="">Tanpa unit</option>
                        <?php foreach ($data['units'] as $unit): ?>
                        <option value="<?= $unit['id']; ?>"><?= htmlspecialchars($unit['nama']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 border border-slate-300 shadow-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Delete Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full md:max-w-md rounded-xl shadow-xl border border-slate-200 mx-4 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Hapus Pengguna</h2>
                <button onclick="closeDeleteModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <p class="text-slate-600">Apakah kamu ingin menghapus pengguna ini ?</p>
            </div>
            <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3">
                 <form action="<?= BASEURL; ?>/user" method="POST" id="delete-form">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete-id">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 border border-slate-300 shadow-sm bg-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 shadow-sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(id) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-modal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
}

// Close delete modal when clicking outside
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

function openModal(mode, data = null) {
    const modal = document.getElementById('user-modal');
    const title = document.getElementById('modal-title');
    const formAction = document.getElementById('form-action');
    const formId = document.getElementById('form-id');
    const formUsername = document.getElementById('form-username');
    const formRole = document.getElementById('form-role');
    const formUnit = document.getElementById('form-unit');
    const formPassword = document.getElementById('form-password');

    modal.classList.remove('hidden');

    if (mode === 'create') {
        title.textContent = 'Tambah Pengguna';
        formAction.value = 'create';
        formId.value = '';
        formUsername.value = '';
        formRole.value = 'OPERATOR';
        formUnit.value = '';
        formPassword.required = true;
        formPassword.placeholder = '';
    } else {
        title.textContent = 'Edit Pengguna';
        formAction.value = 'update';
        formId.value = data.id;
        formUsername.value = data.username;
        formRole.value = data.role;
        formUnit.value = data.unit_id || '';
        formPassword.required = false;
        formPassword.placeholder = 'Kosongkan jika tidak mengganti password';
    }
}

function closeModal() {
    document.getElementById('user-modal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('user-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

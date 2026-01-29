<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pengaturan Role & Akses</h1>
            <p class="mt-1 text-sm text-slate-500">Atur hak akses setiap role secara konsisten.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openRoleModal('create')" class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-sky-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Role
            </button>
        </div>
    </div>

    <?php if ($data['success']): ?>
    <div class="bg-green-50 border-l-4 border-green-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
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
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900">Daftar Role & Hak Akses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Kelola Pengguna</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Kelola Unit</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Hapus Survey</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Export CSV</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Lihat Semua Unit (Dashboard)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    <?php foreach ($data['roles'] as $role): ?>
                    <?php $perm = $data['role_permissions'][$role] ?? []; ?>
                    <tr>
                        <td class="px-6 py-3 font-medium text-slate-900"><?php echo htmlspecialchars($role); ?></td>
                        <td class="px-6 py-3 text-center">
                            <input type="checkbox" <?= !empty($perm['manage_users']) ? 'checked' : '' ?> disabled>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <input type="checkbox" <?= !empty($perm['manage_units']) ? 'checked' : '' ?> disabled>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <input type="checkbox" <?= !empty($perm['delete_surveys']) ? 'checked' : '' ?> disabled>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <input type="checkbox" <?= !empty($perm['export_csv']) ? 'checked' : '' ?> disabled>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <input type="checkbox" <?= !empty($perm['view_all_units_dashboard']) ? 'checked' : '' ?> disabled>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <button onclick='openRoleModal("update", <?php echo json_encode(["role" => $role, "permissions" => $perm]); ?>)' class="text-sky-600 hover:text-sky-900">Edit</button>
                            <button onclick='openDeleteModal("<?= $role ?>")' class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="role-modal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full md:max-w-md rounded-xl shadow-xl border border-slate-200 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 id="role-modal-title" class="text-lg font-semibold text-slate-900">Tambah Role</h2>
                <button onclick="closeRoleModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= BASEURL; ?>/role" method="POST" class="px-6 py-4 space-y-4">
                <input type="hidden" name="action" id="role-form-action" value="create">
                <input type="hidden" name="original_role" id="role-form-original">
                <div class="space-y-1.5">
                    <label class="text-sm font-medium text-slate-700">Nama Role</label>
                    <input name="role_name" id="role-form-name" type="text" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" required>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="permissions[manage_users]" id="perm-manage-users" class="rounded">
                        Kelola Pengguna
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="permissions[manage_units]" id="perm-manage-units" class="rounded">
                        Kelola Unit
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="permissions[delete_surveys]" id="perm-delete-surveys" class="rounded">
                        Hapus Survey
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="permissions[export_csv]" id="perm-export-csv" class="rounded">
                        Export CSV
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="permissions[view_all_units_dashboard]" id="perm-view-all" class="rounded">
                        Lihat Semua Unit (Dashboard)
                    </label>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeRoleModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 border border-slate-300 shadow-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-semibold hover:bg-sky-700 shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full md:max-w-md rounded-xl shadow-xl border border-slate-200 mx-4 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Hapus Role</h2>
                <button onclick="closeDeleteModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <p class="text-slate-600">Apakah Anda yakin ingin menghapus role ini?</p>
            </div>
            <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3">
                 <form action="<?= BASEURL; ?>/role" method="POST" id="delete-form">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="role_name" id="delete-role-name">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 border border-slate-300 shadow-sm bg-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 shadow-sm">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openRoleModal(mode, data = null) {
        const modal = document.getElementById('role-modal');
        const title = document.getElementById('role-modal-title');
        const formAction = document.getElementById('role-form-action');
        const formOriginal = document.getElementById('role-form-original');
        const formName = document.getElementById('role-form-name');
        const pUsers = document.getElementById('perm-manage-users');
        const pUnits = document.getElementById('perm-manage-units');
        const pDelete = document.getElementById('perm-delete-surveys');
        const pExport = document.getElementById('perm-export-csv');
        const pViewAll = document.getElementById('perm-view-all');
        modal.classList.remove('hidden');
        if (mode === 'create') {
            title.textContent = 'Tambah Role';
            formAction.value = 'create';
            formOriginal.value = '';
            formName.value = '';
            pUsers.checked = false;
            pUnits.checked = false;
            pDelete.checked = false;
            pExport.checked = false;
            pViewAll.checked = false;
        } else {
            title.textContent = 'Edit Role';
            formAction.value = 'update';
            formOriginal.value = data.role;
            formName.value = data.role;
            pUsers.checked = !!data.permissions.manage_users;
            pUnits.checked = !!data.permissions.manage_units;
            pDelete.checked = !!data.permissions.delete_surveys;
            pExport.checked = !!data.permissions.export_csv;
            pViewAll.checked = !!data.permissions.view_all_units_dashboard;
        }
    }
    function closeRoleModal() {
        document.getElementById('role-modal').classList.add('hidden');
    }
    function openDeleteModal(roleName) {
        document.getElementById('delete-role-name').value = roleName;
        document.getElementById('delete-modal').classList.remove('hidden');
    }
    function closeDeleteModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
    document.getElementById('delete-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    document.getElementById('role-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRoleModal();
        }
    });
    </script>
</div>

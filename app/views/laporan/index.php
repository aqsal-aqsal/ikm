<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Laporan Survey</h1>
            <p class="mt-1 text-sm text-slate-500">Rekap data respon survei masyarakat.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="<?= BASEURL; ?>/laporan?export=csv" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </a>
        </div>
    </div>

    <?php if (isset($data['success']) && $data['success']): ?>
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

    <?php if (isset($data['error']) && $data['error']): ?>
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
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h3 class="text-base font-semibold text-slate-900">Data Masuk</h3>
            
            <form action="<?= BASEURL; ?>/laporan" method="GET" class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <?php if (!empty($data['units'])): ?>
                <div class="relative w-full sm:w-48">
                    <select name="unit_filter" class="block w-full pl-3 pr-8 py-1.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" onchange="this.form.submit()">
                        <option value="">Semua UPPD</option>
                        <?php foreach ($data['units'] as $u): ?>
                        <option value="<?= $u['id']; ?>" <?= ($data['unit_filter'] == $u['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($u['nama']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($data['search']); ?>" class="block w-full pl-10 pr-3 py-1.5 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" placeholder="Cari Nopol..." onchange="this.form.submit()">
                </div>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nomor Polisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nilai Rata-rata</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Detail</th>
                        <?php if ($_SESSION['user']['role'] === 'SUPERADMIN'): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <?php if (empty($data['surveys'])): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-6 text-center text-xs text-slate-400">
                            Tidak ada data survey.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($data['surveys'] as $s): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500"><?php echo date('d M Y', strtotime($s['tanggal'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-900 font-medium font-mono"><?php echo htmlspecialchars($s['responden_nopol'] ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500"><?php echo htmlspecialchars($s['unit_nama']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $s['indeks'] >= 3 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo number_format($s['indeks'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <button class="text-sky-600 hover:text-sky-900" onclick="alert('Detail saran: <?php echo htmlspecialchars($s['saran'] ?? '-'); ?>')">Lihat</button>
                            </td>
                            <?php if ($_SESSION['user']['role'] === 'SUPERADMIN'): ?>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <button class="text-red-600 hover:text-red-900" onclick="openDeleteModal(<?= $s['id']; ?>)">Hapus</button>
                            </td>
                            <?php endif; ?>
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
                    <a href="<?= BASEURL; ?>/laporan?page=<?php echo $data['page'] - 1; ?>&search=<?php echo urlencode($data['search']); ?>&unit_filter=<?php echo urlencode($data['unit_filter'] ?? ''); ?>" class="px-3 py-1 border border-slate-300 rounded-md text-sm hover:bg-slate-50 text-slate-600">Previous</a>
                <?php else: ?>
                    <span class="px-3 py-1 border border-slate-200 rounded-md text-sm text-slate-300 cursor-not-allowed bg-slate-50">Previous</span>
                <?php endif; ?>
                
                <?php if ($data['page'] < $data['totalPages']): ?>
                    <a href="<?= BASEURL; ?>/laporan?page=<?php echo $data['page'] + 1; ?>&search=<?php echo urlencode($data['search']); ?>&unit_filter=<?php echo urlencode($data['unit_filter'] ?? ''); ?>" class="px-3 py-1 border border-slate-300 rounded-md text-sm hover:bg-slate-50 text-slate-600">Next</a>
                <?php else: ?>
                    <span class="px-3 py-1 border border-slate-200 rounded-md text-sm text-slate-300 cursor-not-allowed bg-slate-50">Next</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
        <div class="bg-white w-full md:max-w-md rounded-xl shadow-xl border border-slate-200 mx-4 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">Hapus Data Survey</h2>
                <button onclick="closeDeleteModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <p class="text-slate-600">Apakah Anda yakin ingin menghapus data survey ini? Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3">
                 <form action="<?= BASEURL; ?>/laporan" method="POST" id="delete-form">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete-id">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 border border-slate-300 shadow-sm bg-white">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 shadow-sm">Hapus</button>
                </form>
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
    </script>
</div>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Desktop Sidebar -->
<aside class="hidden md:flex md:w-72 flex-col border-r border-slate-200 bg-white">
    <!-- Sidebar Header -->
    <div class="h-16 flex items-center px-6 border-b border-slate-100">
        <div class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-sky-100 flex items-center justify-center">
                <img src="assets/img/bapendalogo.png" alt="Logo" class="h-5 w-auto">
            </div>
            <span class="text-base font-semibold text-slate-900">Bapenda Kalsel</span>
        </div>
    </div>
    
    <!-- Sidebar Search -->
    <div class="p-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500" placeholder="Search">
        </div>
    </div>

    <!-- Sidebar Links -->
    <div class="flex-1 overflow-y-auto px-4 py-2 space-y-6">
        <div>
            <p class="px-3 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Menu</p>
            <nav class="space-y-1">
                <a href="dashboard.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $current_page == 'dashboard.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <?= $current_page == 'dashboard.php' ? '<span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>' : '' ?>
                    Dashboard
                </a>
                <a href="laporan.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $current_page == 'laporan.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <?= $current_page == 'laporan.php' ? '<span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>' : '' ?>
                    Laporan
                </a>
                
                <?php if ($_SESSION['user']['role'] === 'SUPERADMIN'): ?>
                <a href="units.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $current_page == 'units.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <?= $current_page == 'units.php' ? '<span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>' : '' ?>
                    Master Layanan
                </a>
                <a href="roles.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $current_page == 'roles.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <?= $current_page == 'roles.php' ? '<span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>' : '' ?>
                    Role & Hak Akses
                </a>
                <a href="users.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors <?= $current_page == 'users.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                    <?= $current_page == 'users.php' ? '<span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>' : '' ?>
                    Kelola Pengguna
                </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <!-- Sidebar Footer (User) -->
    <div class="p-4 border-t border-slate-100">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-sky-100 flex items-center justify-center text-sky-600 font-bold">
                <?= substr($_SESSION['user']['username'] ?? 'U', 0, 1) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Guest') ?></p>
                <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></p>
            </div>
            <a href="logout.php" class="text-slate-400 hover:text-slate-600" title="Logout">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </div>
</aside>

<!-- Main Content Wrapper -->
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">
    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between px-4 h-16 border-b border-slate-100 bg-white">
        <div class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-sky-100 flex items-center justify-center">
                <img src="assets/img/bapendalogo.png" alt="Logo" class="h-5 w-auto">
            </div>
            <span class="text-base font-semibold text-slate-900">Bapenda</span>
        </div>
        <button id="mobile-menu-btn" class="p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </div>

    <!-- Mobile Menu (Hidden by default) -->
    <div id="mobile-menu" class="md:hidden hidden border-b border-slate-100 bg-white px-4 py-4 space-y-4 absolute top-16 left-0 right-0 z-50 shadow-lg">
         <!-- Similar links as sidebar but for mobile -->
         <nav class="space-y-1">
            <a href="dashboard.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $current_page == 'dashboard.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' ?>">Dashboard</a>
            <a href="laporan.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $current_page == 'laporan.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' ?>">Laporan</a>
            
            <?php if ($_SESSION['user']['role'] === 'SUPERADMIN'): ?>
            <a href="units.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $current_page == 'units.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' ?>">Layanan</a>
            <a href="roles.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $current_page == 'roles.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' ?>">Role</a>
            <a href="users.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $current_page == 'users.php' ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:text-slate-900 hover:bg-slate-50' ?>">Pengguna</a>
            <?php endif; ?>
         </nav>
         <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-900 truncate"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Guest') ?></p>
                    <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($_SESSION['user']['role'] ?? '') ?></p>
                </div>
                <a href="logout.php" class="text-slate-400 hover:text-slate-600">
                    Logout
                </a>
            </div>
         </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-auto bg-slate-50 p-4 md:p-8">

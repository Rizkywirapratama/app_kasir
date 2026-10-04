<?php
$role        = $this->session->userdata('role');
$current_uri = uri_string();
$user_name   = $this->session->userdata('nama') ? $this->session->userdata('nama') : 'User';
$initial     = strtoupper(substr($user_name, 0, 1));
?>
<!-- Sidebar -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme w-64 min-h-screen text-slate-200">
    <!-- Brand Header -->
    <div class="app-brand flex items-center px-4 py-3">
        <a href="<?= base_url('dashboard') ?>" class="app-brand-link mr-2">
            <img src="<?= base_url('assets/images/logo-ndc.png') ?>" alt="Nol Derajat Coffee" class="h-15 w-auto object-contain">
        </a>
        <button type="button" class="inline-flex items-center text-white xl:hidden ml-auto bg-white/10 rounded-md px-2 py-1" id="mobileMenuClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Navigation -->
    <ul class="menu-inner py-2 space-y-1 list-none p-0 m-0">
        <!-- Menu Utama -->
        <li class="menu-header px-4 py-2 text-xs uppercase text-slate-400">Menu Utama</li>
        <li class="menu-item <?= ($current_uri == 'dashboard' || $current_uri == '') ? 'active' : '' ?>">
            <a href="<?= base_url('dashboard') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-chart-pie"></i>
                <div>Dashboard</div>
            </a>
        </li>
        <li class="menu-item <?= (strpos($current_uri,'penjualan/buat') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('penjualan/buat') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-calculator"></i>
                <div>Kasir (POS)</div>
            </a>
        </li>

        <!-- Data & Transaksi -->
        <li class="menu-header px-4 py-2 text-xs uppercase text-slate-400">Data &amp; Transaksi</li>
        <li class="menu-item <?= ($current_uri == 'penjualan') ? 'active' : '' ?>">
            <a href="<?= base_url('penjualan') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-receipt"></i>
                <div>Riwayat Transaksi</div>
            </a>
        </li>
        <li class="menu-item <?= (strpos($current_uri,'menu') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('menu') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-mug-hot"></i>
                <div>Data Menu</div>
            </a>
        </li>
        <li class="menu-item <?= (strpos($current_uri,'kategori') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('kategori') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-tags"></i>
                <div>Kategori</div>
            </a>
        </li>
        <li class="menu-item <?= (strpos($current_uri,'meja') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('meja') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-chair"></i>
                <div>Kelola Meja</div>
            </a>
        </li>
        <li class="menu-item <?= (strpos($current_uri,'member') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('member') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-users"></i>
                <div>Pelanggan / Member</div>
            </a>
        </li>

        <!-- Analisis & Sesi -->
        <li class="menu-header px-4 py-2 text-xs uppercase text-slate-400">Analisis &amp; Sesi</li>
        <?php if ($role === 'admin'): ?>
        <li class="menu-item <?= (strpos($current_uri,'laporan') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('laporan') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-chart-line"></i>
                <div>Laporan Penjualan</div>
            </a>
        </li>
        <?php endif; ?>
        <li class="menu-item <?= ($current_uri == 'shift' || $current_uri == 'shift/index') ? 'active' : '' ?>">
            <a href="<?= base_url('shift') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-clock-rotate-left"></i>
                <div>Riwayat Shift</div>
            </a>
        </li>
        <?php
            $id_shift_menu = $this->session->userdata('id_shift');
        ?>
        <?php if ($id_shift_menu): ?>
        <li class="menu-item <?= (strpos($current_uri,'shift/tutup') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('shift/tutup') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-lock-open"></i>
                <div>Tutup Shift</div>
            </a>
        </li>
        <?php else: ?>
        <li class="menu-item <?= (strpos($current_uri,'shift/buka') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('shift/buka') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-door-open"></i>
                <div>Buka Shift</div>
            </a>
        </li>
        <?php endif; ?>

        <!-- Administrasi (admin only) -->
        <?php if ($role === 'admin'): ?>
        <li class="menu-header px-4 py-2 text-xs uppercase text-slate-400">Administrasi</li>
        <li class="menu-item <?= (strpos($current_uri,'pengguna') !== false) ? 'active' : '' ?>">
            <a href="<?= base_url('pengguna') ?>" class="menu-link flex items-center gap-3 px-4 py-2 rounded hover:bg-white/5">
                <i class="menu-icon tf-icons fas fa-user-shield"></i>
                <div>Kelola Pengguna</div>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- User Info Footer -->
    <div class="sidebar-user flex items-center gap-3 px-4 py-3 border-t border-white/5">
        <div class="sidebar-user-avatar bg-slate-700 rounded-full h-10 w-10 flex items-center justify-center font-bold"><?= $initial ?></div>
        <div class="sidebar-user-info flex-1">
            <div class="sidebar-user-name text-sm font-semibold"><?= htmlspecialchars($user_name) ?></div>
            <div class="sidebar-user-role text-xs text-slate-400"><?= htmlspecialchars($role) ?></div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" title="Logout" class="text-slate-400 hover:text-red-500 transition-colors">
            <i class="fas fa-right-from-bracket"></i>
        </a>
    </div>
</aside>
<!-- / Sidebar -->

<!-- Layout Page -->
<div class="layout-page">
    <!-- Topbar Navbar -->
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-transparent px-4 py-3 flex items-center justify-between" id="layout-navbar">
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center mr-1 xl:hidden text-slate-400" id="mobileMenuToggle" aria-label="Buka menu">
                <i class="fas fa-bars fs-5"></i>
            </button>
            <div>
                <h5 class="mb-0 font-semibold text-slate-900 text-base">
                    <?= isset($title) ? htmlspecialchars($title) : 'Dashboard' ?>
                </h5>
                <p class="mb-0 text-sm text-slate-400 mt-0.5">
                    <?= date('l, d F Y') ?>
                </p>
            </div>
        </div>

        <div class="navbar-nav-right flex items-center gap-2">
            <!-- Realtime Clock -->
            <div class="topbar-clock-pill hidden lg:flex items-center gap-2 text-sm text-slate-400">
                <i class="fas fa-clock text-primary text-sm"></i>
                <span id="topbarClock"><?= date('H:i:s') ?></span>
            </div>

            <!-- Shift Badge -->
            <?php
                $id_shift = $this->session->userdata('id_shift');
                if ($id_shift):
            ?>
                                         <a href="<?= base_url('shift/tutup') ?>" class="inline-flex items-center gap-2 px-3 py-1 rounded-full font-bold text-sm bg-emerald-900/20 text-emerald-400 border border-emerald-800" style="text-decoration:none;">
                                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Shift Aktif
                                </a>
            <?php else: ?>
                                         <a href="<?= base_url('shift/buka') ?>" class="inline-flex items-center gap-2 px-3 py-1 rounded-full font-bold text-sm bg-amber-900/20 text-amber-400 border border-amber-800" style="text-decoration:none;">
                                        <i class="fas fa-lock text-xs"></i>
                                        Shift Tutup
                                </a>
            <?php endif; ?>

            <!-- User Dropdown -->
            <div class="dropdown">
                     <a class="nav-link dropdown-toggle hide-arrow flex items-center gap-2" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center">
                        <span class="avatar-initial font-extrabold text-sm text-slate-900"><?= $initial ?></span>
                    </div>
                    <div class="hidden md:block text-start leading-tight">
                        <span class="d-block font-semibold text-sm text-slate-900"><?= htmlspecialchars($user_name) ?></span>
                        <small class="text-uppercase text-xs text-slate-400 font-medium"><?= htmlspecialchars($role) ?></small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 210px;">
                    <li class="px-3 py-2" style="border-bottom:1px solid rgba(255,255,255,0.07);">
                        <div class="font-semibold text-sm" style="color: #f1f5f9;"><?= htmlspecialchars($user_name) ?></div>
                        <small class="text-uppercase text-xs" style="color: #4b5563; font-weight: 600;"><?= htmlspecialchars($role) ?></small>
                    </li>
                    <li><a class="dropdown-item text-danger py-2 mt-1" href="<?= base_url('auth/logout') ?>">
                        <i class="fas fa-right-from-bracket me-2"></i> Keluar
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- / Topbar -->

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.5; transform: scale(0.75); }
}
</style>

<?php
$toko_nama = isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama
    : (isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Toko POS');
$toko_logo_data = isset($profil_toko) ? $profil_toko : (isset($profil_apotek) ? $profil_apotek : null);
$toko_logo = ($toko_logo_data && !empty($toko_logo_data->logo) && is_file(FCPATH . 'assets/uploads/logo/' . $toko_logo_data->logo))
    ? base_url('assets/uploads/logo/' . $toko_logo_data->logo) : '';
?>
<!-- Header / Navbar -->
<nav class="navbar">
    <div class="navbar-left">
        <a href="<?= base_url('dashboard') ?>" class="navbar-brand-mobile">
            <?php if ($toko_logo): ?>
                <img src="<?= $toko_logo ?>" alt="<?= htmlspecialchars($toko_nama) ?>" class="brand-logo brand-logo-mobile">
            <?php else: ?>
                <i class="fas fa-store"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($toko_nama) ?></span>
        </a>
        <button class="navbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-desktop-breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
                        <?php foreach ($breadcrumb as $item): ?>
                            <?php if (isset($item['url'])): ?>
                                <li class="breadcrumb-item"><a href="<?= $item['url'] ?>"><?= $item['label'] ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active"><?= $item['label'] ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
    </div>
    <div class="navbar-right">
        <div class="user-menu" id="userMenu">
            <div class="user-avatar">
                <?= strtoupper(substr(isset($user->nama_lengkap) ? $user->nama_lengkap : 'U', 0, 1)) ?>
            </div>
            <div class="user-menu-info">
                <div style="font-weight: 600; font-size: 14px;"><?= isset($user->nama_lengkap) ? $user->nama_lengkap : 'User' ?></div>
                <div style="font-size: 12px; color: #64748b;"><?= isset($peran->nama) ? $peran->nama : 'No Role' ?></div>
            </div>
            <i class="fas fa-chevron-down ms-1 d-none d-md-inline" style="font-size:0.75rem;color:#94a3b8;"></i>
        </div>
    </div>
</nav>

<!-- User Dropdown Menu (desktop) -->
<div id="userDropdown">
    <a class="dropdown-item" href="<?= base_url('pengaturan/profil') ?>">
        <i class="fas fa-user"></i> Profil
    </a>
    <a class="dropdown-item" href="<?= base_url('pengaturan') ?>">
        <i class="fas fa-cog"></i> Pengaturan
    </a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$current_uri = uri_string();
$is_home = (strpos($current_uri, 'dashboard') !== false || $current_uri === '');
$is_kasir = (strpos($current_uri, 'penjualan/kasir') !== false || $current_uri === 'penjualan/kasir');
$is_riwayat = (strpos($current_uri, 'penjualan/daftar') !== false || strpos($current_uri, 'daftarpenjualan') !== false);
?>
<nav class="mobile-bottom-nav" aria-label="Navigasi mobile">
    <a href="<?= base_url('dashboard') ?>" class="mobile-nav-item <?= $is_home ? 'active' : '' ?>">
        <span class="mobile-nav-icon"><i class="fas fa-home"></i></span>
        <span class="mobile-nav-label">Home</span>
    </a>
    <button type="button" class="mobile-nav-item" id="mobileNavMenu" aria-label="Menu Modul">
        <span class="mobile-nav-icon"><i class="fas fa-th-large"></i></span>
        <span class="mobile-nav-label">Menu</span>
    </button>
    <a href="<?= base_url('penjualan/kasir') ?>" class="mobile-nav-item mobile-nav-item--cart <?= $is_kasir ? 'active' : '' ?>">
        <span class="mobile-nav-fab">
            <span class="mobile-nav-icon"><i class="fas fa-cash-register"></i></span>
            <span class="mobile-nav-badge" id="mobileNavCartBadge" data-count="0"></span>
        </span>
        <span class="mobile-nav-label">Kasir</span>
    </a>
    <a href="<?= base_url('penjualan/daftar') ?>" class="mobile-nav-item <?= $is_riwayat ? 'active' : '' ?>">
        <span class="mobile-nav-icon"><i class="fas fa-receipt"></i></span>
        <span class="mobile-nav-label">Riwayat</span>
    </a>
    <button type="button" class="mobile-nav-item" id="mobileNavAccount" aria-label="Akun">
        <span class="mobile-nav-icon"><i class="fas fa-user"></i></span>
        <span class="mobile-nav-label">Account</span>
    </button>
</nav>

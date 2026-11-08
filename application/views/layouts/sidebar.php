<?php
$toko_nama = isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama : (isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Toko POS');
$toko_logo = (isset($profil_toko) && !empty($profil_toko->logo) && is_file(FCPATH . 'assets/uploads/logo/' . $profil_toko->logo))
    ? base_url('assets/uploads/logo/' . $profil_toko->logo)
    : ((isset($profil_apotek) && !empty($profil_apotek->logo) && is_file(FCPATH . 'assets/uploads/logo/' . $profil_apotek->logo))
        ? base_url('assets/uploads/logo/' . $profil_apotek->logo) : '');
?>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3>
            <?php if ($toko_logo): ?>
                <img src="<?= $toko_logo ?>" alt="<?= htmlspecialchars($toko_nama) ?>" class="brand-logo brand-logo-sidebar">
            <?php else: ?>
                <i class="fas fa-store"></i>
            <?php endif; ?>
            <span><?= htmlspecialchars($toko_nama) ?></span>
        </h3>
    </div>
    <nav class="sidebar-menu">
        <ul>
            <?php if (can_view('dashboard')): ?>
            <li>
                <a href="<?= base_url('dashboard') ?>" class="<?= (strtolower($this->router->class) == 'dashboard' || strtolower($this->router->directory) == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            

            <!-- Master Data -->
             <?php if (can_view('master')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-database"></i>
                    <span>Master Data</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('masterdata/masterproduk') ?>">Master Produk</a></li>
                    <li><a href="<?= base_url('masterdata/masterkategori') ?>">Master Kategori</a></li>
                    <li><a href="<?= base_url('masterdata/mastersatuan') ?>">Master Satuan</a></li>
                    <li><a href="<?= base_url('masterdata/masterrak') ?>">Master Rak</a></li>
                    <li><a href="<?= base_url('masterdata/mastergudang') ?>">Master Gudang</a></li>
                    <li><a href="<?= base_url('masterdata/masterpajak') ?>">Master Pajak</a></li>
                    <li><a href="<?= base_url('masterdata/mastermembership') ?>">Master Membership</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <!-- Penjualan -->
            <?php if (can_view('penjualan') || can_create('penjualan')): ?>
            <?php $is_kasir_role = ($this->session->userdata('peran_kode') === 'kasir'); ?>
            <li>
                <a href="#" class="<?= strpos($this->router->class, 'penjualan') !== false || $this->router->class == 'kasir' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Penjualan</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('penjualan/kasir') ?>">Kasir</a></li>
                    <?php if (!$is_kasir_role): ?>
                    <li><a href="<?= base_url('penjualan/daftar') ?>">Daftar Penjualan</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Pelayanan (dihapus - bukan fitur POS standard) -->
            
            <!-- Persediaan -->
            <?php if (can_view('persediaan')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-boxes"></i>
                    <span>Persediaan</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('persediaan/produk') ?>">Daftar Produk</a></li>
                    <li><a href="<?= base_url('persediaan/kadaluarsa') ?>">Stok Kadaluarsa</a></li>
                    <li><a href="<?= base_url('persediaan/opname') ?>">Stok Opname</a></li>
                    <li><a href="<?= base_url('persediaan/penyesuaian') ?>">Penyesuaian Stok</a></li>
                    <li><a href="<?= base_url('persediaan/perpindahan') ?>">Perpindahan Stok</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Pembelian -->
            <?php if (can_view('pembelian')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Pembelian</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">

                    <li><a href="<?= base_url('pembelian/pesanan') ?>">Pesanan Pembelian</a></li>
                    <li><a href="<?= base_url('pembelian/faktur') ?>">Faktur Pembelian</a></li>
                    <li><a href="<?= base_url('pembelian/retur') ?>">Retur Pembelian</a></li>

                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Konsinyasi -->
            <?php if (menu_visible('konsinyasi')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-handshake"></i>
                    <span>Konsinyasi</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('konsinyasi/masuk') ?>">Konsinyasi Masuk</a></li>
                    <li><a href="<?= base_url('konsinyasi/status') ?>">Status Konsinyasi</a></li>
                    <li><a href="<?= base_url('konsinyasi/retur') ?>">Retur Konsinyasi</a></li>
                    <li><a href="<?= base_url('konsinyasi/stok') ?>">Stok Konsinyasi</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Program Promo -->
            <?php if (menu_visible('promo')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-gift"></i>
                    <span>Program Promo</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('promo/bundel') ?>">Bundel / Paket</a></li>
                    <li><a href="<?= base_url('promo/diskon') ?>">Diskon Produk</a></li>
                    <li><a href="<?= base_url('promo/poin') ?>">Member Poin</a></li>
                    <li><a href="<?= base_url('promo/voucher') ?>">Voucher Diskon</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Keuangan -->
            <?php if (can_view('keuangan')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Keuangan</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('keuangan/akun-kas') ?>">Daftar Akun Kas</a></li>
                    <li><a href="<?= base_url('keuangan/buku-kas') ?>">Buku Kas</a></li>
                    <li><a href="<?= base_url('keuangan/utang') ?>">Utang Usaha</a></li>
                    <li><a href="<?= base_url('keuangan/piutang') ?>">Piutang Usaha</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Akuntansi -->
            <?php if (menu_visible('akuntansi')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-book"></i>
                    <span>Akuntansi</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('akuntansi/coa') ?>">Master CoA</a></li>
                    <li><a href="<?= base_url('akuntansi/buku-besar') ?>">Buku Besar</a></li>
                    <li><a href="<?= base_url('akuntansi/aktiva') ?>">Aktiva Tetap</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Kontak -->
            <?php if (can_view('kontak')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-address-book"></i>
                    <span>Kontak</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('kontak/pelanggan') ?>">Pelanggan</a></li>
                    <li><a href="<?= base_url('kontak/sales') ?>">Sales / Pelayan</a></li>
                    <li><a href="<?= base_url('kontak/supplier') ?>">Supplier</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Laporan -->
            <?php if (can_view('laporan')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-file-alt"></i>
                    <span>Laporan</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('laporan/penjualan') ?>">Laporan Penjualan</a></li>
                    <li><a href="<?= base_url('laporan/pembelian') ?>">Laporan Pembelian</a></li>
                    <li><a href="<?= base_url('laporan/persediaan') ?>">Laporan Persediaan</a></li>
                    <li><a href="<?= base_url('laporan/keuangan') ?>">Laporan Keuangan</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Analisis -->
            <?php if (menu_visible('analisis')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-chart-line"></i>
                    <span>Analisis</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('analisis/pareto') ?>">Analisis Pareto</a></li>
                    <li><a href="<?= base_url('analisis/pembelian') ?>">Analisis Pembelian</a></li>
                    <li><a href="<?= base_url('analisis/harga') ?>">Analisis Harga</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Manajemen Pengguna -->
            <?php if (can_view('manajemen')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-users"></i>
                    <span>Manajemen Pengguna</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('manajemen/pengguna') ?>">Daftar Pengguna</a></li>
                    <li><a href="<?= base_url('manajemen/peran') ?>">Peran & Hak Akses</a></li>
                    <li><a href="<?= base_url('manajemen/log') ?>">Log Aktivitas</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- KPI -->
            <?php if (menu_visible('kpi')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-tasks"></i>
                    <span>KPI</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('kpi/parameter') ?>">Parameter KPI</a></li>
                    <li><a href="<?= base_url('kpi/karyawan') ?>">KPI Karyawan</a></li>
                    <li><a href="<?= base_url('kpi/pencapaian') ?>">Pencapaian KPI</a></li>
                    <li><a href="<?= base_url('kpi/laporan') ?>">Laporan KPI</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Gaji -->
            <?php if (menu_visible('gaji')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-money-check-alt"></i>
                    <span>Gaji</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('gaji/pokok') ?>">Gaji Pokok</a></li>
                    <li><a href="<?= base_url('gaji/tunjangan') ?>">Tunjangan</a></li>
                    <li><a href="<?= base_url('gaji/potongan') ?>">Potongan Gaji</a></li>
                    <li><a href="<?= base_url('gaji/perhitungan') ?>">Perhitungan Gaji</a></li>
                    <li><a href="<?= base_url('gaji/pembayaran') ?>">Pembayaran Gaji</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Pengaturan -->
            <?php if (can_view('pengaturan')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('pengaturan/profil') ?>">Profil Toko</a></li>
                    <li><a href="<?= base_url('pengaturan/metode-pembayaran') ?>">Metode Pembayaran</a></li>
                    <li><a href="<?= base_url('pengaturan/konfigurasi') ?>">Konfigurasi</a></li>
                    <?php if ($this->session->userdata('peran_kode') === 'admin'): ?>
                    <li><a href="<?= base_url('pengaturan/reset-data') ?>" class="text-danger">Reset Data</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Multi Outlet -->
            <?php if (menu_visible('outlet')): ?>
            <li>
                <a href="#">
                    <i class="fas fa-store"></i>
                    <span>Multi Outlet</span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('outlet/permintaan') ?>">Permintaan Mutasi</a></li>
                    <li><a href="<?= base_url('outlet/mutasi') ?>">Mutasi Antar Outlet</a></li>
                    <li><a href="<?= base_url('outlet/produk-mitra') ?>">Produk Mitra</a></li>
                    <li><a href="<?= base_url('outlet/outlet-mitra') ?>">Outlet Mitra</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <!-- Cara Penggunaan -->
            <li>
                <a href="<?= base_url('tutorial') ?>" class="<?= strtolower($this->router->class) == 'tutorial' ? 'active' : '' ?>">
                    <i class="fas fa-question-circle"></i>
                    <span>Cara Penggunaan</span>
                </a>
            </li>
           
        </ul>
    </nav>
</aside>


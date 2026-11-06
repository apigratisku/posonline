<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$is_kasir_role = ($this->session->userdata('peran_kode') === 'kasir');
?>
<div class="mobile-sheet-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>
<div class="mobile-layanan-sheet" id="mobileMenuSheet" role="dialog" aria-label="Menu Modul">
    <div class="mobile-sheet-handle"></div>
    <div class="mobile-sheet-header">
        <h2 class="mobile-sheet-title">Menu Modul</h2>
        <button type="button" class="mobile-sheet-close" id="mobileMenuClose" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-sheet-body">
        <?php if (can_view('dashboard')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Utama</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('dashboard') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-home"></i></span>
                    <span class="mobile-sheet-item-label">Dashboard</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('master')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Master Data</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('masterdata/masterproduk') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-capsules"></i></span>
                    <span class="mobile-sheet-item-label">Produk</span>
                </a>
                <a href="<?= base_url('masterdata/masterkategori') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-tags"></i></span>
                    <span class="mobile-sheet-item-label">Kategori</span>
                </a>
                <a href="<?= base_url('masterdata/mastersatuan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-ruler"></i></span>
                    <span class="mobile-sheet-item-label">Satuan</span>
                </a>
                <a href="<?= base_url('masterdata/masterrak') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-th-large"></i></span>
                    <span class="mobile-sheet-item-label">Rak</span>
                </a>
                <a href="<?= base_url('masterdata/mastergudang') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-warehouse"></i></span>
                    <span class="mobile-sheet-item-label">Gudang</span>
                </a>
                <a href="<?= base_url('masterdata/masterpajak') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-percentage"></i></span>
                    <span class="mobile-sheet-item-label">Pajak</span>
                </a>
                <a href="<?= base_url('masterdata/mastermembership') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-id-card"></i></span>
                    <span class="mobile-sheet-item-label">Membership</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('penjualan') || can_create('penjualan')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Penjualan</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('penjualan/kasir') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-cash-register"></i></span>
                    <span class="mobile-sheet-item-label">Kasir</span>
                </a>
                <?php if (!$is_kasir_role): ?>
                <a href="<?= base_url('penjualan/daftar') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-receipt"></i></span>
                    <span class="mobile-sheet-item-label">Daftar Penjualan</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('persediaan')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Persediaan</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('persediaan/produk') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-boxes"></i></span>
                    <span class="mobile-sheet-item-label">Daftar Produk</span>
                </a>
                <a href="<?= base_url('persediaan/kadaluarsa') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-calendar-times"></i></span>
                    <span class="mobile-sheet-item-label">Kadaluarsa</span>
                </a>
                <a href="<?= base_url('persediaan/opname') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-clipboard-check"></i></span>
                    <span class="mobile-sheet-item-label">Stok Opname</span>
                </a>
                <a href="<?= base_url('persediaan/penyesuaian') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-sliders-h"></i></span>
                    <span class="mobile-sheet-item-label">Penyesuaian</span>
                </a>
                <a href="<?= base_url('persediaan/perpindahan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-exchange-alt"></i></span>
                    <span class="mobile-sheet-item-label">Perpindahan</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('pembelian')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Pembelian</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('pembelian/pesanan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-shopping-bag"></i></span>
                    <span class="mobile-sheet-item-label">Pesanan</span>
                </a>
                <a href="<?= base_url('pembelian/faktur') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="mobile-sheet-item-label">Faktur</span>
                </a>
                <a href="<?= base_url('pembelian/retur') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-undo"></i></span>
                    <span class="mobile-sheet-item-label">Retur</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (menu_visible('konsinyasi')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Konsinyasi</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('konsinyasi/masuk') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-handshake"></i></span>
                    <span class="mobile-sheet-item-label">Masuk</span>
                </a>
                <a href="<?= base_url('konsinyasi/status') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-info-circle"></i></span>
                    <span class="mobile-sheet-item-label">Status</span>
                </a>
                <a href="<?= base_url('konsinyasi/retur') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-undo"></i></span>
                    <span class="mobile-sheet-item-label">Retur</span>
                </a>
                <a href="<?= base_url('konsinyasi/stok') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-box"></i></span>
                    <span class="mobile-sheet-item-label">Stok</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (menu_visible('promo')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Program Promo</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('promo/bundel') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-gift"></i></span>
                    <span class="mobile-sheet-item-label">Bundel</span>
                </a>
                <a href="<?= base_url('promo/diskon') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-percent"></i></span>
                    <span class="mobile-sheet-item-label">Diskon</span>
                </a>
                <a href="<?= base_url('promo/poin') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-coins"></i></span>
                    <span class="mobile-sheet-item-label">Poin</span>
                </a>
                <a href="<?= base_url('promo/voucher') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-ticket-alt"></i></span>
                    <span class="mobile-sheet-item-label">Voucher</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('keuangan')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Keuangan</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('keuangan/akun-kas') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-wallet"></i></span>
                    <span class="mobile-sheet-item-label">Akun Kas</span>
                </a>
                <a href="<?= base_url('keuangan/buku-kas') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-book"></i></span>
                    <span class="mobile-sheet-item-label">Buku Kas</span>
                </a>
                <a href="<?= base_url('keuangan/utang') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                    <span class="mobile-sheet-item-label">Utang Usaha</span>
                </a>
                <a href="<?= base_url('keuangan/piutang') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-hand-holding-usd"></i></span>
                    <span class="mobile-sheet-item-label">Piutang Usaha</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (menu_visible('akuntansi')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Akuntansi</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('akuntansi/coa') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-book"></i></span>
                    <span class="mobile-sheet-item-label">CoA</span>
                </a>
                <a href="<?= base_url('akuntansi/buku-besar') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-book-open"></i></span>
                    <span class="mobile-sheet-item-label">Buku Besar</span>
                </a>
                <a href="<?= base_url('akuntansi/aktiva') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-building"></i></span>
                    <span class="mobile-sheet-item-label">Aktiva Tetap</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('kontak')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Kontak</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('kontak/pelanggan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-users"></i></span>
                    <span class="mobile-sheet-item-label">Pelanggan</span>
                </a>
                <a href="<?= base_url('kontak/sales') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-user-friends"></i></span>
                    <span class="mobile-sheet-item-label">Sales</span>
                </a>
                <a href="<?= base_url('kontak/supplier') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-truck"></i></span>
                    <span class="mobile-sheet-item-label">Supplier</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('laporan')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Laporan</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('laporan/penjualan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-file-alt"></i></span>
                    <span class="mobile-sheet-item-label">Penjualan</span>
                </a>
                <a href="<?= base_url('laporan/pembelian') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-file-import"></i></span>
                    <span class="mobile-sheet-item-label">Pembelian</span>
                </a>
                <a href="<?= base_url('laporan/persediaan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-file-invoice"></i></span>
                    <span class="mobile-sheet-item-label">Persediaan</span>
                </a>
                <a href="<?= base_url('laporan/keuangan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-chart-bar"></i></span>
                    <span class="mobile-sheet-item-label">Keuangan</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (menu_visible('analisis')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Analisis</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('analisis/pareto') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-chart-line"></i></span>
                    <span class="mobile-sheet-item-label">Pareto</span>
                </a>
                <a href="<?= base_url('analisis/pembelian') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-chart-pie"></i></span>
                    <span class="mobile-sheet-item-label">Pembelian</span>
                </a>
                <a href="<?= base_url('analisis/harga') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-dollar-sign"></i></span>
                    <span class="mobile-sheet-item-label">Harga</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('manajemen')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Manajemen Pengguna</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('manajemen/pengguna') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-users"></i></span>
                    <span class="mobile-sheet-item-label">Pengguna</span>
                </a>
                <a href="<?= base_url('manajemen/peran') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-user-shield"></i></span>
                    <span class="mobile-sheet-item-label">Peran</span>
                </a>
                <a href="<?= base_url('manajemen/log') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-history"></i></span>
                    <span class="mobile-sheet-item-label">Log</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (menu_visible('kpi')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">KPI</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('kpi/parameter') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-list-alt"></i></span>
                    <span class="mobile-sheet-item-label">Parameter</span>
                </a>
                <a href="<?= base_url('kpi/karyawan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-users-cog"></i></span>
                    <span class="mobile-sheet-item-label">Karyawan</span>
                </a>
                <a href="<?= base_url('kpi/pencapaian') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-trophy"></i></span>
                    <span class="mobile-sheet-item-label">Pencapaian</span>
                </a>
                <a href="<?= base_url('kpi/laporan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-file-signature"></i></span>
                    <span class="mobile-sheet-item-label">Laporan</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (menu_visible('gaji')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Gaji</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('gaji/pokok') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-money-bill-alt"></i></span>
                    <span class="mobile-sheet-item-label">Gaji Pokok</span>
                </a>
                <a href="<?= base_url('gaji/tunjangan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-hand-holding-usd"></i></span>
                    <span class="mobile-sheet-item-label">Tunjangan</span>
                </a>
                <a href="<?= base_url('gaji/potongan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-percent"></i></span>
                    <span class="mobile-sheet-item-label">Potongan</span>
                </a>
                <a href="<?= base_url('gaji/perhitungan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-calculator"></i></span>
                    <span class="mobile-sheet-item-label">Perhitungan</span>
                </a>
                <a href="<?= base_url('gaji/pembayaran') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-receipt"></i></span>
                    <span class="mobile-sheet-item-label">Pembayaran</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (can_view('pengaturan')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Pengaturan</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('pengaturan/profil') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-cog"></i></span>
                    <span class="mobile-sheet-item-label">Profil Toko</span>
                </a>
                <a href="<?= base_url('pengaturan/metode-pembayaran') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-credit-card"></i></span>
                    <span class="mobile-sheet-item-label">Metode Bayar</span>
                </a>
                <a href="<?= base_url('pengaturan/konfigurasi') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-sliders-h"></i></span>
                    <span class="mobile-sheet-item-label">Konfigurasi</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (menu_visible('outlet')): ?>
        <div class="mobile-sheet-group">
            <div class="mobile-sheet-group-title">Multi Outlet</div>
            <div class="mobile-sheet-grid">
                <a href="<?= base_url('outlet/permintaan') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-truck-loading"></i></span>
                    <span class="mobile-sheet-item-label">Permintaan</span>
                </a>
                <a href="<?= base_url('outlet/mutasi') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-exchange-alt"></i></span>
                    <span class="mobile-sheet-item-label">Mutasi</span>
                </a>
                <a href="<?= base_url('outlet/produk-mitra') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-box-open"></i></span>
                    <span class="mobile-sheet-item-label">Produk Mitra</span>
                </a>
                <a href="<?= base_url('outlet/outlet-mitra') ?>" class="mobile-sheet-item">
                    <span class="mobile-sheet-item-icon"><i class="fas fa-store"></i></span>
                    <span class="mobile-sheet-item-label">Outlet Mitra</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Account Sheet -->
<div class="mobile-sheet-overlay" id="mobileAccountOverlay" aria-hidden="true"></div>
<div class="mobile-layanan-sheet" id="mobileAccountSheet" role="dialog" aria-label="Akun">
    <div class="mobile-sheet-handle"></div>
    <div class="mobile-sheet-header">
        <h2 class="mobile-sheet-title">Akun Saya</h2>
        <button type="button" class="mobile-sheet-close" id="mobileAccountClose" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-sheet-body">
        <div class="text-center mb-3">
            <div class="user-avatar mx-auto mb-2" style="width:56px;height:56px;font-size:1.25rem;">
                <?= strtoupper(substr(isset($user->nama_lengkap) ? $user->nama_lengkap : 'U', 0, 1)) ?>
            </div>
            <div style="font-weight:700;color:#1e293b;"><?= isset($user->nama_lengkap) ? htmlspecialchars($user->nama_lengkap) : 'User' ?></div>
            <div style="font-size:0.8125rem;color:#64748b;"><?= isset($peran->nama) ? htmlspecialchars($peran->nama) : '' ?></div>
        </div>
        <div class="mobile-account-actions">
            <a href="<?= base_url('pengaturan/profil') ?>">
                <i class="fas fa-user"></i> Profil
            </a>
            <a href="<?= base_url('pengaturan') ?>">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
            <a href="<?= base_url('auth/logout') ?>" class="danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

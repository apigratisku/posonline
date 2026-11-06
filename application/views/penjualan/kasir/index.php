<div class="pos-page">
    <?php if (!empty($assigned_gudang)): ?>
        <input type="hidden" id="gudang_id" name="gudang_id" value="<?= (int) $assigned_gudang_id ?>">
    <?php endif; ?>

    <?php if (empty($assigned_gudang)): ?>
        <div class="alert alert-danger m-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Gudang transaksi belum ditetapkan untuk akun Anda. Hubungi administrator untuk mengatur lokasi gudang penjualan.
        </div>
    <?php endif; ?>

    <?php
    $toko_nama = isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama : (isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Toko POS');
    $toko_alamat = isset($profil_toko) && !empty($profil_toko->alamat) ? $profil_toko->alamat : (isset($profil_apotek) && !empty($profil_apotek->alamat) ? $profil_apotek->alamat : '');
    $toko_telepon = isset($profil_toko) && !empty($profil_toko->telepon) ? $profil_toko->telepon : (isset($profil_apotek) && !empty($profil_apotek->telepon) ? $profil_apotek->telepon : '');
    ?>
    <input type="hidden" id="posStoreName" value="<?= htmlspecialchars($toko_nama) ?>">
    <input type="hidden" id="posStoreAddress" value="<?= htmlspecialchars($toko_alamat) ?>">
    <input type="hidden" id="posStorePhone" value="<?= htmlspecialchars($toko_telepon) ?>">

    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Kasir</h1>
            <p class="page-subtitle">Transaksi penjualan</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="printerMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-print"></i> Printer: <span id="activePrinterLabel">Browser</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="printerMenuBtn">
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="setPrinterType('browser')">Browser Print (Default)</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="setPrinterType('usb')">Direct USB Printer</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="setPrinterType('bluetooth')">Direct Bluetooth (BLE Only)</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="setPrinterType('serial')">Direct Bluetooth / Serial (COM Port)</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="connectPrinter()"><i class="fas fa-link"></i> Hubungkan / Connect</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="pos-layout<?= empty($assigned_gudang) ? ' pos-disabled' : '' ?>">
        <!-- Left: Search + Cart -->
        <div class="pos-cart-section">
            <div class="pos-search-bar">
                <div class="pos-search-input-wrap">
                    <input type="text" class="form-control pos-search-input" id="searchProduk" placeholder="Cari obat atau scan barcode..." autocomplete="off" autofocus<?= empty($assigned_gudang) ? ' disabled' : '' ?>>
                    <button type="button" class="pos-search-scan" onclick="searchProduk()" aria-label="Cari"<?= empty($assigned_gudang) ? ' disabled' : '' ?>>
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="pos-search-dropdown" id="posSearchDropdown" hidden></div>
                </div>
                <div class="pos-filters">
                    <?php if (!empty($assigned_gudang)): ?>
                        <div class="pos-gudang-label">
                            <span class="badge bg-primary"><i class="fas fa-warehouse me-1"></i><?= htmlspecialchars($assigned_gudang->nama) ?></span>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" id="pelanggan_id" name="pelanggan_id" value="">
                    <div class="pos-pelanggan-label">
                        <span class="badge bg-secondary"><i class="fas fa-user me-1"></i>Pelanggan Umum</span>
                    </div>
                </div>
            </div>

            <div class="pos-cart-header">
                <h5><i class="fas fa-shopping-basket me-1"></i> Keranjang</h5>
                <span class="pos-cart-count" id="posCartCount">0</span>
            </div>

            <div class="pos-cart-list" id="posCartList">
                <div class="pos-empty">
                    <i class="fas fa-barcode"></i>
                    <h6>Keranjang Kosong</h6>
                    <p>Scan barcode atau cari nama obat untuk memulai transaksi</p>
                </div>
            </div>
        </div>

        <!-- Payment panel (desktop sticky / mobile bottom sheet) -->
        <div class="pos-payment-sheet-overlay" id="posPaymentOverlay"></div>
        <div class="pos-payment-sheet" id="posPaymentSheet">
            <div class="mobile-sheet-handle d-lg-none"></div>
            <div class="pos-payment-card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-credit-card me-2"></i> Pembayaran</span>
                <button type="button" class="btn btn-sm btn-light d-lg-none" id="posPaymentClose" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="pos-payment-sheet-body">
                <?php $this->load->view('penjualan/kasir/_payment_form'); ?>
            </div>
        </div>
    </div>

    <!-- Mobile checkout bar -->
    <div class="pos-checkout-bar">
        <div>
            <div class="pos-checkout-total-label">Total</div>
            <div class="pos-checkout-total-value" id="posCheckoutTotal">Rp 0</div>
        </div>
        <button type="button" class="pos-checkout-btn" id="posCheckoutBtn" disabled>
            <i class="fas fa-cash-register"></i> Bayar
        </button>
    </div>
</div>

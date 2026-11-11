<?php
$toko_nama = isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama : (isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Toko POS');
$toko_alamat = isset($profil_toko) && !empty($profil_toko->alamat) ? $profil_toko->alamat : (isset($profil_apotek) && !empty($profil_apotek->alamat) ? $profil_apotek->alamat : '');
$toko_telepon = isset($profil_toko) && !empty($profil_toko->telepon) ? $profil_toko->telepon : (isset($profil_apotek) && !empty($profil_apotek->telepon) ? $profil_apotek->telepon : '');
?>
<input type="hidden" id="posStoreName" value="<?= htmlspecialchars($toko_nama) ?>">
<input type="hidden" id="posStoreAddress" value="<?= htmlspecialchars($toko_alamat) ?>">
<input type="hidden" id="posStorePhone" value="<?= htmlspecialchars($toko_telepon) ?>">
<input type="hidden" id="isAdminCancel" value="<?= !empty($is_admin) ? '1' : '0' ?>">

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Daftar Penjualan</h1>
        <p class="page-subtitle">Riwayat transaksi penjualan</p>
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

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Penjualan</h5>
        <a href="<?= base_url('penjualan/kasir') ?>" class="btn btn-primary">
            <i class="fas fa-cash-register"></i> Kasir Baru
        </a>
    </div>
    <div class="card-body">
        <table id="penjualanTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <?php if (!empty($is_admin)) { ?>
                    <th class="no-sort text-center" style="width:38px">
                        <input type="checkbox" id="checkAllPenjualan" title="Pilih semua">
                    </th>
                    <?php } ?>
                    <th>No Penjualan</th>
                    <th>Kode &amp; Nama Produk</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                    <th class="d-none">ID</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Toolbar aksi massal (admin only) -->
<?php if (!empty($is_admin)) { ?>
<div class="card mt-2 d-none" id="bulkActionBar">
    <div class="card-body d-flex flex-wrap align-items-center gap-2 py-2">
        <span class="fw-semibold"><span id="bulkSelectedCount">0</span> transaksi dipilih</span>
        <button type="button" class="btn btn-sm btn-danger" onclick="confirmCancelBulk()">
            <i class="fas fa-times"></i> Batalkan Transaksi
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearBulkSelection()">
            Bersihkan Pilihan
        </button>
    </div>
</div>
<?php } ?>

<!-- Modal alasan pembatalan -->
<div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="cancelReasonInfo" class="mb-2"></p>
                <div class="alert alert-warning d-none" id="cancelStockAlert">
                    <i class="fas fa-exclamation-triangle"></i>
                    Transaksi ini sudah <strong>completed</strong>. Pembatalan akan
                    <strong>mengembalikan stok barang</strong> ke gudang dan
                    <strong>mengurangi poin member</strong> jika ada.
                </div>
                <label class="form-label">Alasan Pembatalan <span class="text-muted">(opsional)</span></label>
                <textarea class="form-control" id="cancelReason" rows="3" maxlength="255" placeholder="Contoh: salah input, barang dikembalikan pelanggan, dll."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="btnConfirmCancel" onclick="doCancel()">
                    <i class="fas fa-times"></i> Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal detail penjualan -->
<div class="modal fade" id="detailPenjualanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Detail Penjualan <span id="detailNoPenjualan" class="text-muted fw-normal"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3" id="detailHeaderInfo"></div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Diskon</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    <table class="table table-sm table-borderless w-auto mb-0" id="detailSummaryTable"></table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" id="btnPrintDetail">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var isAdminCancel = $('#isAdminCancel').val() === '1';
    var checkboxCol = [];
    if (isAdminCancel) {
        checkboxCol.push({
            // Kolom checkbox: data = id penjualan (kolom terkirim terakhir dari server)
            data: 6,
            orderable: false,
            searchable: false,
            className: 'text-center',
            render: function(data, type, row) {
                if (String(row[4]).indexOf('CANCELLED') > -1) {
                    return '<input type="checkbox" class="row-check" disabled title="Sudah dibatalkan">';
                }
                return '<input type="checkbox" class="row-check" value="' + data + '">';
            }
        });
    }

    var table = initDataTable('penjualanTable', {
        ajax: {
            url: baseUrl + 'penjualan/daftar-penjualan/ajax_list',
            type: 'POST'
        },
        columns: checkboxCol.concat([
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, orderable: false, searchable: false },
            { data: 6, visible: false, searchable: false }
        ]),
        drawCallback: function() {
            // Setiap kali DataTables render ulang (pindah halaman / cari / reload)
            refreshBulkBar();
        }
    });

    // Pilih semua (halaman saat ini)
    $('#checkAllPenjualan').on('change', function() {
        var checked = $(this).prop('checked');
        $('#penjualanTable tbody .row-check').prop('checked', checked);
        refreshBulkBar();
    });

    // Delegasi karena DataTables re-render tbody setiap pindah halaman/cari
    $('#penjualanTable tbody').on('change', '.row-check', function() {
        refreshBulkBar();
    });

    // Load printer setting
    var savedPrinterType = localStorage.getItem('pos_printer_type') || 'browser';
    setPrinterType(savedPrinterType);
});

/** Update toolbar aksi massal sesuai jumlah checkbox tercentang */
function refreshBulkBar() {
    var total = $('#penjualanTable tbody .row-check').length;
    var checked = $('#penjualanTable tbody .row-check:checked').length;
    $('#bulkSelectedCount').text(checked);
    // Non-admin: toolbar tidak dirender sama sekali
    if ($('#bulkActionBar').length) {
        $('#bulkActionBar').toggleClass('d-none', checked === 0);
    }
    if ($('#checkAllPenjualan').length) {
        $('#checkAllPenjualan').prop('checked', total > 0 && checked === total);
    }
}

function clearBulkSelection() {
    $('#penjualanTable tbody .row-check').prop('checked', false);
    $('#checkAllPenjualan').prop('checked', false);
    refreshBulkBar();
}

function getSelectedIds() {
    return $('#penjualanTable tbody .row-check:checked').map(function() {
        return $(this).val();
    }).get();
}

// State modal pembatalan
var cancelMode = 'single'; // 'single' | 'bulk'
var cancelTargetId = null;

/** Modal alasan pembatalan untuk 1 transaksi */
function cancelData(id, isCompleted) {
    cancelMode = 'single';
    cancelTargetId = id;

    $('#cancelReasonInfo').html('Batalkan penjualan <strong>ID ' + id + '</strong>?');
    $('#cancelStockAlert').toggleClass('d-none', isCompleted != 1);
    $('#cancelReason').val('');
    $('#cancelReasonModal').modal('show');
}

/** Modal alasan pembatalan massal */
function confirmCancelBulk() {
    var ids = getSelectedIds();
    if (!ids.length) {
        showNotification('warning', 'Pilih transaksi terlebih dahulu');
        return;
    }

    cancelMode = 'bulk';
    cancelTargetId = ids;
    $('#cancelReasonInfo').html('Batalkan <strong>' + ids.length + ' transaksi</strong> sekaligus?');
    $('#cancelStockAlert').removeClass('d-none');
    $('#cancelReason').val('');
    $('#cancelReasonModal').modal('show');
}

/** Eksekusi pembatalan (tunggu modal alasan ditutup dulu) */
function doCancel() {
    var alasan = $('#cancelReason').val();
    var url, payload;

    if (cancelMode === 'single') {
        url = baseUrl + 'penjualan/daftar-penjualan/ajax_cancel/' + cancelTargetId;
        payload = { alasan: alasan };
    } else {
        url = baseUrl + 'penjualan/daftar-penjualan/ajax_cancel_bulk';
        payload = { ids: cancelTargetId, alasan: alasan };
    }

    showLoading();
    $.ajax({
        url: url,
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.status) {
                $('#cancelReasonModal').modal('hide');
                showNotification('success', response.message);
                clearBulkSelection();
                reloadDataTable('penjualanTable');
            } else {
                showNotification('error', response.message);
            }
        },
        error: function(xhr, status) {
            hideLoading();
            showNotification('error', 'Gagal membatalkan penjualan' + (xhr.status ? ' (HTTP ' + xhr.status + ')' : ''));
        }
    });
}

function viewData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + 'penjualan/daftar-penjualan/ajax_get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.status && response.data) {
                renderDetailPenjualan(response.data);
            } else {
                showNotification('error', response.message || 'Detail penjualan tidak ditemukan');
            }
        },
        error: function(xhr) {
            hideLoading();
            showNotification('error', 'Gagal memuat data' + (xhr.status ? ' (HTTP ' + xhr.status + ')' : ''));
        }
    });
}

/** Escape teks sebelum dimasukkan ke HTML */
function detailEsc(value) {
    return $('<div>').text(value === null || value === undefined ? '' : value).html();
}

function detailMoney(value) {
    var num = parseFloat(value);
    return (isNaN(num) ? 0 : num).toLocaleString('id-ID');
}

function detailQty(value) {
    var num = parseFloat(value);
    if (isNaN(num)) num = 0;
    return num.toLocaleString('id-ID', { maximumFractionDigits: 3 });
}

function detailDateTime(value) {
    if (!value) return '-';
    var d = new Date(String(value).replace(' ', 'T'));
    if (isNaN(d.getTime())) return String(value);
    var pad = function(n) { return n < 10 ? '0' + n : n; };
    return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() +
        ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
}

/** Render isi modal detail penjualan */
function renderDetailPenjualan(data) {
    var p = data.penjualan || {};
    var details = data.details || [];

    $('#detailNoPenjualan').text(p.no_penjualan || '');

    var status = String(p.status || '').toUpperCase();
    var statusClass = p.status === 'completed' ? 'success' : (p.status === 'cancelled' ? 'danger' : 'warning');
    var statusHtml = '<span class="badge bg-' + statusClass + '">' + detailEsc(status) + '</span>';

    var info = [
        ['Pelanggan', detailEsc(p.pelanggan_nama || 'Umum')],
        ['Gudang', detailEsc(p.gudang_nama || '-')]
    ];
    if (p.tanggal) info.push(['Tanggal', detailEsc(detailDateTime(p.tanggal))]);
    if (p.sales_nama) info.push(['Sales', detailEsc(p.sales_nama)]);
    if (p.shift_nama) info.push(['Shift', detailEsc(p.shift_nama)]);
    if (p.metode_pembayaran_nama) info.push(['Metode Pembayaran', detailEsc(p.metode_pembayaran_nama)]);
    info.push(['Status', statusHtml]);
    if (p.keterangan) info.push(['Keterangan', detailEsc(p.keterangan)]);

    var headerHtml = '';
    info.forEach(function(item) {
        headerHtml += '<div class="col-sm-6">' +
            '<small class="text-muted d-block">' + item[0] + '</small>' +
            '<div class="fw-semibold">' + item[1] + '</div>' +
            '</div>';
    });
    $('#detailHeaderInfo').html(headerHtml);

    var rows = '';
    if (!details.length) {
        rows = '<tr><td colspan="5" class="text-center text-muted">Tidak ada item</td></tr>';
    } else {
        details.forEach(function(d) {
            var satuan = d.satuan_nama ? ' ' + detailEsc(d.satuan_nama) : '';
            var diskon = parseFloat(d.diskon || 0);
            rows += '<tr>' +
                '<td>' + (d.produk_kode ? '<span class="text-muted">' + detailEsc(d.produk_kode) + '</span> - ' : '') + detailEsc(d.produk_nama || '-') + '</td>' +
                '<td class="text-end">' + detailQty(d.qty) + satuan + '</td>' +
                '<td class="text-end">' + detailMoney(d.harga) + '</td>' +
                '<td class="text-end">' + (diskon > 0 ? detailMoney(diskon) : '-') + '</td>' +
                '<td class="text-end">' + detailMoney(d.subtotal) + '</td>' +
                '</tr>';
        });
    }
    $('#detailItemsBody').html(rows);

    var summary = '';
    var sumRow = function(label, value, bold) {
        summary += '<tr' + (bold ? ' class="fw-bold"' : '') + '>' +
            '<td class="text-end pe-3">' + label + '</td>' +
            '<td class="text-end">' + value + '</td>' +
            '</tr>';
    };
    sumRow('Subtotal', detailMoney(p.subtotal));
    if (parseFloat(p.diskon || 0) > 0) sumRow('Diskon', '-' + detailMoney(p.diskon));
    if (parseFloat(p.pajak || 0) > 0) sumRow('Pajak', detailMoney(p.pajak));
    sumRow('Total', detailMoney(p.total), true);
    sumRow('Bayar', detailMoney(p.bayar));
    sumRow('Kembalian', detailMoney(p.kembalian));
    $('#detailSummaryTable').html(summary);

    $('#btnPrintDetail').off('click').on('click', function() {
        printData(p.id);
    });

    $('#detailPenjualanModal').modal('show');
}

function printData(id) {
    printSaleReceipt(id);
}

// Thermal Printer Direct Integration via WebUSB, Web Bluetooth, and Web Serial
var ThermalPrinter = {
    connectionType: 'browser', // 'browser', 'usb', 'bluetooth', 'serial'
    device: null,
    characteristic: null,
    endpointOut: null,
    interfaceNumber: null,
    
    connectUSB: async function() {
        try {
            if (!navigator.usb) {
                alert('WebUSB tidak didukung di browser ini. Gunakan Google Chrome atau Edge.');
                return false;
            }
            this.device = await navigator.usb.requestDevice({
                filters: [{ classCode: 7 }] // printer class
            });
            showLoading('Menghubungkan Printer USB...');
            await this.device.open();
            await this.device.selectConfiguration(1);
            
            var interfaces = this.device.configuration.interfaces;
            var found = false;
            for (var iface of interfaces) {
                for (var alt of iface.alternates) {
                    if (alt.interfaceClass === 7) {
                        this.interfaceNumber = iface.interfaceNumber;
                        for (var ep of alt.endpoints) {
                            if (ep.direction === 'out' && ep.type === 'bulk') {
                                this.endpointOut = ep.endpointNumber;
                                found = true;
                                break;
                            }
                        }
                    }
                    if (found) break;
                }
                if (found) break;
            }
            hideLoading();
            
            if (!found) {
                alert('Tidak menemukan bulk OUT endpoint untuk printer.');
                return false;
            }
            
            await this.device.claimInterface(this.interfaceNumber);
            localStorage.setItem('pos_printer_type', 'usb');
            this.connectionType = 'usb';
            $('#activePrinterLabel').text('Direct USB');
            showNotification('success', 'Printer USB terhubung: ' + this.device.productName);
            return true;
        } catch (e) {
            hideLoading();
            console.error(e);
            showNotification('error', 'Gagal menghubungkan printer USB: ' + e.message);
            return false;
        }
    },
    
    connectBluetooth: async function() {
        try {
            if (!navigator.bluetooth) {
                alert('Web Bluetooth tidak didukung di browser ini atau sedang dinonaktifkan.');
                return false;
            }
            this.device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [
                    '000018f0-0000-1000-8000-00805f9b34fb', // Standard thermal printer service
                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2', // ESC/POS service
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455', // Other printer service
                    '00001101-0000-1000-8000-00805f9b34fb'  // SPP UUID
                ]
            });
            
            showLoading('Menghubungkan Printer Bluetooth...');
            var server = await this.device.gatt.connect();
            
            let service;
            try {
                service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
            } catch (e) {
                try {
                    service = await server.getPrimaryService('e7810a71-73ae-499d-8c15-faa9aef0c3f2');
                } catch (e) {
                    try {
                        service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                    } catch (e) {
                        var services = await server.getPrimaryServices();
                        if (services.length > 0) {
                            service = services[0];
                        } else {
                            throw new Error('Tidak dapat menemukan layanan printer yang sesuai');
                        }
                    }
                }
            }
            
            var characteristics = await service.getCharacteristics();
            var found = false;
            for (var char of characteristics) {
                var properties = char.properties;
                if (properties.write || properties.writeWithoutResponse) {
                    this.characteristic = char;
                    found = true;
                    break;
                }
            }
            
            if (!found) {
                try {
                    this.characteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                    found = true;
                } catch (e) {
                    try {
                        this.characteristic = await service.getCharacteristic('49535343-8841-43f4-a8d4-ecbe34729bb3');
                        found = true;
                    } catch (e) {
                        try {
                            this.characteristic = await service.getCharacteristic('00000000-0000-1000-8000-00805f9b34fb');
                            found = true;
                        } catch (e) {}
                    }
                }
            }
            
            hideLoading();
            
            if (!found) {
                alert('Tidak menemukan karakteristik printer yang sesuai.');
                return false;
            }
            
            localStorage.setItem('pos_printer_type', 'bluetooth');
            this.connectionType = 'bluetooth';
            $('#activePrinterLabel').text('Direct BT (BLE)');
            showNotification('success', 'Printer Bluetooth terhubung: ' + this.device.name);
            return true;
        } catch (e) {
            hideLoading();
            console.error(e);
            showNotification('error', 'Gagal menghubungkan printer Bluetooth: ' + e.message);
            return false;
        }
    },
    
    connectSerial: async function() {
        try {
            if (!navigator.serial) {
                alert('Web Serial tidak didukung di browser ini. Gunakan Google Chrome atau Edge.');
                return false;
            }
            this.device = await navigator.serial.requestPort();
            localStorage.setItem('pos_printer_type', 'serial');
            this.connectionType = 'serial';
            $('#activePrinterLabel').text('COM Port / Serial BT');
            showNotification('success', 'Printer Serial / Bluetooth (COM Port) dipilih.');
            return true;
        } catch (e) {
            console.error(e);
            showNotification('error', 'Gagal memilih port serial: ' + e.message);
            return false;
        }
    },
    
    printReceipt: async function(saleData) {
        try {
            var dataBytes = this.generateEscPos(saleData);
            if (this.connectionType === 'usb') {
                if (!this.device || !this.device.opened) {
                    var connected = await this.connectUSB();
                    if (!connected) return;
                }
                await this.device.transferOut(this.endpointOut, dataBytes);
                showNotification('success', 'Struk tercetak di printer USB');
            } else if (this.connectionType === 'bluetooth') {
                if (!this.device || !this.device.gatt.connected) {
                    var connected = await this.connectBluetooth();
                    if (!connected) return;
                }
                var chunkSize = 512;
                for (var i = 0; i < dataBytes.length; i += chunkSize) {
                    var chunk = dataBytes.slice(i, i + chunkSize);
                    await this.characteristic.writeValue(chunk);
                }
                showNotification('success', 'Struk tercetak di printer Bluetooth');
            } else if (this.connectionType === 'serial') {
                if (!this.device) {
                    var connected = await this.connectSerial();
                    if (!connected) return;
                }
                showLoading('Mengirim data ke COM Port...');
                await this.device.open({ baudRate: 9600 });
                const writer = this.device.writable.getWriter();
                await writer.write(dataBytes);
                writer.releaseLock();
                await this.device.close();
                hideLoading();
                showNotification('success', 'Struk tercetak di printer Serial/COM');
            }
        } catch (e) {
            hideLoading();
            console.error(e);
            showNotification('error', 'Gagal mencetak secara langsung, beralih ke print browser: ' + e.message);
            window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleData.penjualan.id, '_blank');
        }
    },
    
    generateEscPos: function(data) {
        var enc = new TextEncoder();
        var esc = new Uint8Array([0x1B, 0x40]);
        var alignCenter = new Uint8Array([0x1B, 0x61, 0x01]);
        var alignLeft = new Uint8Array([0x1B, 0x61, 0x00]);
        var alignRight = new Uint8Array([0x1B, 0x61, 0x02]);
        var boldOn = new Uint8Array([0x1B, 0x45, 0x01]);
        var boldOff = new Uint8Array([0x1B, 0x45, 0x00]);
        var fontDoubleSize = new Uint8Array([0x1D, 0x21, 0x11]);
        var fontNormalSize = new Uint8Array([0x1D, 0x21, 0x00]);
        var lineFeed = new Uint8Array([0x0A]);
        var paperCut = new Uint8Array([0x1D, 0x56, 0x41, 0x03]);
        
        var p = data.penjualan;
        var items = data.details;
        var buffer = [];
        
        function append(arr) { for (var b of arr) buffer.push(b); }
        function appendText(t) { var bytes = enc.encode(t); for (var b of bytes) buffer.push(b); }
        
        append(esc);
        append(alignCenter);
        append(boldOn);
        append(fontDoubleSize);
        
        var storeName = $('#posStoreName').val() || 'TOKO POS';
        var storeAddress = $('#posStoreAddress').val() || '';
        var storePhone = $('#posStorePhone').val() || '';
        
        appendText(storeName + "\n");
        append(fontNormalSize);
        append(boldOff);
        
        if (storeAddress) appendText(storeAddress + "\n");
        if (storePhone) appendText("Telp: " + storePhone + "\n");
        
        appendText("================================\n");
        
        append(alignLeft);
        appendText("No. Trans : " + p.no_penjualan + "\n");
        appendText("Tanggal   : " + p.tanggal + "\n");
        if (p.pelanggan_nama) appendText("Pelanggan : " + p.pelanggan_nama + "\n");
        if (p.metode_pembayaran_nama) appendText("Metode    : " + p.metode_pembayaran_nama + "\n");
        
        appendText("--------------------------------\n");
        
        items.forEach(function(item) {
            appendText(item.produk_nama + "\n");
            var qtyStr = item.qty + " x " + parseFloat(item.harga).toLocaleString('id-ID');
            var subtotalStr = parseFloat(item.subtotal).toLocaleString('id-ID');
            var spaces = 32 - qtyStr.length - subtotalStr.length;
            if (spaces < 1) spaces = 1;
            appendText(qtyStr + " ".repeat(spaces) + subtotalStr + "\n");
            if (parseFloat(item.diskon) > 0) {
                appendText("  Diskon: -" + parseFloat(item.diskon).toLocaleString('id-ID') + "\n");
            }
        });
        
        appendText("--------------------------------\n");
        
        function appendTotalRow(label, amount) {
            var amountStr = parseFloat(amount).toLocaleString('id-ID');
            var spaces = 32 - label.length - amountStr.length;
            if (spaces < 1) spaces = 1;
            appendText(label + " ".repeat(spaces) + amountStr + "\n");
        }
        
        appendTotalRow("Subtotal", p.subtotal);
        if (parseFloat(p.diskon) > 0) appendTotalRow("Diskon", -p.diskon);
        if (parseFloat(p.pajak) > 0) appendTotalRow("Pajak", p.pajak);
        
        append(boldOn);
        appendTotalRow("Total", p.total);
        append(boldOff);
        
        appendTotalRow("Bayar", p.bayar);
        appendTotalRow("Kembali", p.kembalian);
        
        appendText("================================\n");
        append(alignCenter);
        appendText("Terima Kasih telah berbelanja\n");
        
        append(lineFeed);
        append(lineFeed);
        append(lineFeed);
        append(paperCut);
        
        return new Uint8Array(buffer);
    }
};

function printSaleReceipt(saleId) {
    if (ThermalPrinter.connectionType === 'browser') {
        window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleId, '_blank');
        return;
    }
    
    showLoading('Mengambil data cetak...');
    $.ajax({
        url: baseUrl + 'penjualan/kasir/ajax_get_print/' + saleId,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status && res.data) {
                ThermalPrinter.printReceipt(res.data);
            } else {
                showNotification('error', 'Gagal mengambil data cetak: ' + res.message);
                window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleId, '_blank');
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal mengambil data cetak');
            window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleId, '_blank');
        }
    });
}

function setPrinterType(type) {
    ThermalPrinter.connectionType = type;
    localStorage.setItem('pos_printer_type', type);
    var labels = {
        'browser': 'Browser',
        'usb': 'Direct USB',
        'bluetooth': 'Direct BT (BLE)',
        'serial': 'COM Port / Serial BT'
    };
    $('#activePrinterLabel').text(labels[type] || 'Browser');
}

function connectPrinter() {
    var type = ThermalPrinter.connectionType;
    if (type === 'browser') {
        showNotification('warning', 'Pilih tipe printer Direct USB, Bluetooth (BLE), atau Serial (COM Port) terlebih dahulu.');
        return;
    }
    if (type === 'usb') {
        ThermalPrinter.connectUSB();
    } else if (type === 'bluetooth') {
        ThermalPrinter.connectBluetooth();
    } else if (type === 'serial') {
        ThermalPrinter.connectSerial();
    }
}

function completeData(id) {
    showConfirm('Selesaikan penjualan ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'penjualan/daftar-penjualan/ajax_complete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('penjualanTable');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menyelesaikan penjualan');
            }
        });
    });
}
</script>


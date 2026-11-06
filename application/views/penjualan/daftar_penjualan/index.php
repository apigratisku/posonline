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
                    <th>No Penjualan</th>
                    <th>Pelanggan</th>
                    <th>Gudang</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = initDataTable('penjualanTable', {
        ajax: {
            url: baseUrl + 'penjualan/daftar-penjualan/ajax_list',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false, searchable: false }
        ]
    });
    
    // Load printer setting
    var savedPrinterType = localStorage.getItem('pos_printer_type') || 'browser';
    setPrinterType(savedPrinterType);
});

function viewData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + 'penjualan/daftar-penjualan/ajax_get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            hideLoading();
            if (response.status) {
                // Show detail modal
                showNotification('info', 'Detail penjualan: ' + response.data.penjualan.no_penjualan);
            } else {
                showNotification('error', response.message);
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal memuat data');
        }
    });
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

function cancelData(id) {
    showConfirm('Batalkan penjualan ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'penjualan/daftar-penjualan/ajax_cancel/' + id,
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
                showNotification('error', 'Gagal membatalkan penjualan');
            }
        });
    });
}
</script>


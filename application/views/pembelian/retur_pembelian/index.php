<div class="page-header">
    <h1 class="page-title">Retur Pembelian</h1>
    <p class="page-subtitle">Kelola pengembalian barang ke supplier</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Daftar Retur Pembelian</h5>
        <?php if (can_create('pembelian')): ?>
            <button type="button" class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Retur
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="returPembelianTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Retur</th>
                        <th>No. Faktur</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Subtotal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Retur -->
<div class="modal fade" id="returPembelianModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returModalTitle">Tambah Retur Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="returPembelianForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="retur_id">
                    <input type="hidden" name="no_retur" id="no_retur">
                    <input type="hidden" name="status" id="retur_status" value="draft">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Faktur Pembelian</label>
                            <select class="form-select" name="faktur_pembelian_id" id="faktur_pembelian_id">
                                <option value="">Tanpa Faktur</option>
                                <?php foreach ($fakturs as $faktur): ?>
                                    <option value="<?= $faktur->id ?>"><?= htmlspecialchars($faktur->no_faktur) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" name="supplier_id" id="supplier_id" required>
                                <option value="">Pilih Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier->id ?>"><?= htmlspecialchars($supplier->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Keterangan / Alasan Retur</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Item Retur</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="returItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:200px">Produk</th>
                                    <th style="width:100px">Qty Retur</th>
                                    <th style="width:150px">Harga</th>
                                    <th style="width:150px">Subtotal</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="returItemsBody"></tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-3">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span><strong>Total Retur</strong></span>
                                <strong class="text-primary" id="returTotalDisplay">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <?php if (can_create('pembelian') || can_update('pembelian')): ?>
                        <button type="button" class="btn btn-outline-primary" onclick="submitReturForm('draft')">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        <button type="button" class="btn btn-primary" onclick="submitReturForm('sent')">
                            <i class="fas fa-paper-plane"></i> Konfirmasi Retur
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Retur -->
<div class="modal fade" id="returPembelianViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Retur Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>No. Retur:</strong> <span id="viewNoRetur">-</span></div>
                    <div class="col-sm-6"><strong>No. Faktur:</strong> <span id="viewNoFaktur">-</span></div>
                    <div class="col-sm-6"><strong>Status:</strong> <span id="viewStatus">-</span></div>
                    <div class="col-sm-6"><strong>Supplier:</strong> <span id="viewSupplier">-</span></div>
                    <div class="col-sm-6"><strong>Tanggal:</strong> <span id="viewTanggal">-</span></div>
                    <div class="col-sm-12"><strong>Keterangan:</strong> <span id="viewKeterangan">-</span></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty Retur</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="viewItemsBody"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-end" id="viewTotal">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="returProdukOptions"><?= json_encode(array_map(function ($p) {
    return array('id' => (int) $p->id, 'kode' => $p->kode, 'nama' => $p->nama, 'harga_beli' => (float) ($p->harga_beli ?? 0));
}, $produks)) ?></script>

<script>
function formatCurrency(amount) {
    return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
}
function formatNumber(amount) {
    return Number(amount || 0).toLocaleString('id-ID');
}
function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID');
}

let produks = [];
try {
    produks = JSON.parse($('#returProdukOptions').text());
} catch (e) {
    console.error("Failed to parse produks JSON", e);
}

$(document).ready(function() {
    initDataTable('returPembelianTable', {
        ajax: { url: baseUrl + 'pembelian/retur-pembelian/ajax_list', type: 'POST' },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false, searchable: false },
        ]
    });
    
    // Sinkronisasi dengan Faktur
    $('#faktur_pembelian_id').on('change', function() {
        let fakturId = $(this).val();
        if (fakturId) {
            showLoading();
            $.ajax({
                url: baseUrl + 'pembelian/faktur/ajax_get/' + fakturId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    hideLoading();
                    if (res.status) {
                        let faktur = res.data.faktur;
                        let details = res.data.details;
                        
                        $('#supplier_id').val(faktur.supplier_id);
                        
                        $('#returItemsBody').empty();
                        if (details && details.length > 0) {
                            details.forEach(function(d) {
                                // Pada retur, qty_retur default 0 atau sesuai faktur (kita set default max sesuai faktur)
                                addItemRow(d.produk_id, d.qty, d.harga, d.subtotal);
                            });
                        } else {
                            addItemRow();
                        }
                    } else {
                        showNotification('error', 'Gagal memuat data Faktur');
                    }
                },
                error: function() {
                    hideLoading();
                    showNotification('error', 'Gagal memuat data Faktur');
                }
            });
        } else {
            // Reset
            $('#supplier_id').val('');
            $('#returItemsBody').empty();
            addItemRow();
        }
    });
});

function getProdukOptionsHTML(selectedId = null) {
    let html = '<option value="">Pilih Produk</option>';
    produks.forEach(p => {
        let selected = selectedId == p.id ? 'selected' : '';
        html += `<option value="${p.id}" data-harga="${p.harga_beli}" ${selected}>${p.kode} - ${p.nama}</option>`;
    });
    return html;
}

function addItemRow(produkId = '', qty = 1, harga = 0, subtotal = 0) {
    let options = getProdukOptionsHTML(produkId);
    let tr = `
        <tr class="item-row">
            <td>
                <select class="form-select form-select-sm item-produk" onchange="calcRow(this)">
                    ${options}
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm item-qty text-end" value="${qty}" min="1" onchange="calcRow(this)"></td>
            <td><input type="number" class="form-control form-control-sm item-harga text-end" value="${harga}" min="0" onchange="calcRow(this)"></td>
            <td><input type="text" class="form-control form-control-sm item-subtotal text-end" value="${formatCurrency(subtotal)}" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove(); calcTotal();"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    $('#returItemsBody').append(tr);
    calcTotal();
}

function calcRow(el) {
    let tr = $(el).closest('tr');
    let produkSelect = tr.find('.item-produk');
    if ($(el).hasClass('item-produk')) {
        let harga = produkSelect.find('option:selected').data('harga') || 0;
        tr.find('.item-harga').val(harga);
    }
    let qty = parseFloat(tr.find('.item-qty').val()) || 0;
    let harga = parseFloat(tr.find('.item-harga').val()) || 0;
    let subtotal = qty * harga;
    tr.find('.item-subtotal').val(formatCurrency(subtotal));
    tr.data('subtotal', subtotal);
    calcTotal();
}

function calcTotal() {
    let total = 0;
    $('#returItemsBody .item-row').each(function() {
        let qty = parseFloat($(this).find('.item-qty').val()) || 0;
        let harga = parseFloat($(this).find('.item-harga').val()) || 0;
        total += (qty * harga);
    });
    $('#returTotalDisplay').text(formatCurrency(total));
}

function addData() {
    $('#returPembelianForm')[0].reset();
    $('#retur_id').val('');
    $('#retur_status').val('draft');
    $('#returModalTitle').text('Tambah Retur Pembelian');
    $('#returItemsBody').empty();
    addItemRow();
    $('#returPembelianModal').modal('show');
}

function editData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/retur-pembelian/ajax_get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status) {
                $('#returPembelianForm')[0].reset();
                let data = res.data.retur;
                $('#retur_id').val(data.id);
                $('#no_retur').val(data.no_retur);
                $('#faktur_pembelian_id').val(data.faktur_pembelian_id);
                $('#supplier_id').val(data.supplier_id);
                $('#tanggal').val(data.tanggal);
                $('#keterangan').val(data.keterangan);
                $('#retur_status').val(data.status);
                
                $('#returModalTitle').text('Edit Retur Pembelian: ' + data.no_retur);
                
                $('#returItemsBody').empty();
                if (res.data.details && res.data.details.length > 0) {
                    res.data.details.forEach(item => {
                        addItemRow(item.produk_id, item.qty, item.harga, item.subtotal);
                    });
                } else {
                    addItemRow();
                }
                
                $('#returPembelianModal').modal('show');
            } else {
                showNotification('error', res.message);
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal memuat data');
        }
    });
}

function viewData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/retur-pembelian/ajax_get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status) {
                let data = res.data.retur;
                $('#viewNoRetur').text(data.no_retur);
                $('#viewNoFaktur').text(data.no_faktur ? data.no_faktur : '-');
                let statusBadge = '<span class="badge bg-' + (data.status == 'sent' ? 'success' : (data.status == 'cancelled' ? 'danger' : 'secondary')) + '">' + data.status.toUpperCase() + '</span>';
                $('#viewStatus').html(statusBadge);
                $('#viewSupplier').text(data.supplier_nama);
                $('#viewTanggal').text(formatDate(data.tanggal));
                $('#viewKeterangan').text(data.keterangan || '-');
                
                $('#viewItemsBody').empty();
                let total = 0;
                if (res.data.details && res.data.details.length > 0) {
                    res.data.details.forEach(item => {
                        let subtotal = parseFloat(item.subtotal);
                        total += subtotal;
                        $('#viewItemsBody').append(`
                            <tr>
                                <td>${item.produk_kode} - ${item.produk_nama}</td>
                                <td class="text-end">${formatNumber(item.qty)}</td>
                                <td class="text-end">${formatCurrency(item.harga)}</td>
                                <td class="text-end">${formatCurrency(subtotal)}</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#viewItemsBody').append('<tr><td colspan="4" class="text-center">Tidak ada item</td></tr>');
                }
                $('#viewTotal').text(formatCurrency(total));
                $('#returPembelianViewModal').modal('show');
            } else {
                showNotification('error', res.message);
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal memuat data');
        }
    });
}

function submitReturForm(status) {
    if (!$('#returPembelianForm')[0].checkValidity()) {
        $('#returPembelianForm')[0].reportValidity();
        return;
    }
    
    let items = [];
    let valid = true;
    $('#returItemsBody .item-row').each(function() {
        let produk_id = $(this).find('.item-produk').val();
        let qty = $(this).find('.item-qty').val();
        let harga = $(this).find('.item-harga').val();
        
        if (!produk_id || qty <= 0) {
            valid = false;
        } else {
            items.push({ produk_id: produk_id, qty: qty, harga: harga });
        }
    });
    
    if (!valid || items.length === 0) {
        showNotification('warning', 'Pilih produk dan tentukan qty lebih dari 0 untuk semua item');
        return;
    }
    
    $('#retur_status').val(status);
    
    let formData = new FormData($('#returPembelianForm')[0]);
    formData.append('items', JSON.stringify(items));
    
    let confirmMsg = status == 'sent' ? 'Simpan dan konfirmasi retur? Stok akan dikurangi dan tidak dapat diubah lagi.' : 'Simpan sebagai draft?';
    
    showConfirm(confirmMsg, function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/retur-pembelian/ajax_save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    $('#returPembelianModal').modal('hide');
                    reloadDataTable('returPembelianTable');
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menyimpan data');
            }
        });
    });
}

function sendData(id) {
    showConfirm('Konfirmasi retur ini? Stok barang akan dikurangi.', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/retur-pembelian/ajax_send/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    reloadDataTable('returPembelianTable');
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal mengonfirmasi retur');
            }
        });
    });
}
</script>

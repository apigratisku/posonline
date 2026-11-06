<div class="page-header">
    <h1 class="page-title">Faktur Pembelian</h1>
    <p class="page-subtitle">Kelola Faktur Pembelian</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Daftar Faktur Pembelian</h5>
        <?php if (can_create('pembelian')): ?>
            <button type="button" class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Faktur
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="fakturpembelianTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Faktur -->
<div class="modal fade" id="fakturPembelianModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fakturModalTitle">Tambah Faktur Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fakturPembelianForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="faktur_id">
                    <input type="hidden" name="no_faktur" id="no_faktur">

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Pesanan Pembelian (PO)</label>
                            <select class="form-select" name="po_id" id="po_id">
                                <option value="">Tanpa PO</option>
                                <?php foreach ($pos as $po): ?>
                                    <option value="<?= $po->id ?>"><?= htmlspecialchars($po->no_po) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" name="supplier_id" id="supplier_id" required>
                                <option value="">Pilih Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier->id ?>"><?= htmlspecialchars($supplier->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" class="form-control" name="jatuh_tempo" id="jatuh_tempo">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Item Barang</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                            <i class="fas fa-plus"></i> Tambah Baris
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="fakturItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:200px">Produk</th>
                                    <th style="width:100px">Qty</th>
                                    <th style="width:130px">Harga Beli</th>
                                    <th style="width:110px">Diskon</th>
                                    <th style="width:130px">Subtotal</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="fakturItemsBody"></tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-3">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <strong id="fakturSubtotalDisplay">Rp 0</strong>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small">Diskon Global</label>
                                    <input type="number" class="form-control form-control-sm" name="diskon" id="diskon" value="0" min="0" step="1" onchange="calcTotal()">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Pajak</label>
                                    <input type="number" class="form-control form-control-sm" name="pajak" id="pajak" value="0" min="0" step="1" onchange="calcTotal()">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span><strong>Total</strong></span>
                                <strong class="text-primary" id="fakturTotalDisplay">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <?php if (can_create('pembelian') || can_update('pembelian')): ?>
                        <button type="button" class="btn btn-primary" onclick="submitFakturForm()">
                            <i class="fas fa-save"></i> Simpan Faktur
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Faktur -->
<div class="modal fade" id="fakturPembelianViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Faktur Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>No. Faktur:</strong> <span id="viewNoFaktur">-</span></div>
                    <div class="col-sm-6"><strong>Status:</strong> <span id="viewStatus">-</span></div>
                    <div class="col-sm-6"><strong>Supplier:</strong> <span id="viewSupplier">-</span></div>
                    <div class="col-sm-6"><strong>PO Ref:</strong> <span id="viewPoRef">-</span></div>
                    <div class="col-sm-6"><strong>Tanggal:</strong> <span id="viewTanggal">-</span></div>
                    <div class="col-sm-6"><strong>Jatuh Tempo:</strong> <span id="viewJatuhTempo">-</span></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Diskon</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="viewItemsBody"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total</th>
                                <th class="text-end" id="viewTotal">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success d-none" id="btnPayFromView" onclick="payFromView()">
                    <i class="fas fa-check-circle"></i> Tandai Lunas
                </button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="fakturProdukOptions"><?= json_encode(array_map(function ($p) {
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

$(document).ready(function() {
    initDataTable('fakturpembelianTable', {
        ajax: { url: baseUrl + 'pembelian/faktur/ajax_list', type: 'POST' },
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
    
    // Sinkronisasi dengan PO
    $('#po_id').on('change', function() {
        let poId = $(this).val();
        if (poId) {
            showLoading();
            $.ajax({
                url: baseUrl + 'pembelian/pesanan/ajax_get/' + poId,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    hideLoading();
                    if (res.status) {
                        let po = res.data.po;
                        let details = res.data.details;
                        
                        $('#supplier_id').val(po.supplier_id);
                        $('#diskon').val(po.diskon || 0);
                        $('#pajak').val(po.pajak || 0);
                        
                        $('#fakturItemsBody').empty();
                        if (details && details.length > 0) {
                            details.forEach(function(d) {
                                addItemRow(d.produk_id, d.qty, d.harga, d.diskon || 0, d.subtotal);
                            });
                        } else {
                            addItemRow();
                        }
                    } else {
                        showNotification('error', 'Gagal memuat data PO');
                    }
                },
                error: function() {
                    hideLoading();
                    showNotification('error', 'Gagal memuat data PO');
                }
            });
        } else {
            // Jika Tanpa PO, biarkan kosong atau reset
            $('#supplier_id').val('');
            $('#diskon').val(0);
            $('#pajak').val(0);
            $('#fakturItemsBody').empty();
            addItemRow();
        }
    });
});

let produks = [];
try {
    produks = JSON.parse($('#fakturProdukOptions').text());
} catch (e) {
    console.error("Failed to parse produks JSON", e);
}

function getProdukOptionsHTML(selectedId = null) {
    let html = '<option value="">Pilih Produk</option>';
    produks.forEach(p => {
        let selected = selectedId == p.id ? 'selected' : '';
        html += `<option value="${p.id}" data-harga="${p.harga_beli}" ${selected}>${p.kode} - ${p.nama}</option>`;
    });
    return html;
}

function addItemRow(produkId = '', qty = 1, harga = 0, diskon = 0, subtotal = 0) {
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
            <td><input type="number" class="form-control form-control-sm item-diskon text-end" value="${diskon}" min="0" onchange="calcRow(this)"></td>
            <td><input type="text" class="form-control form-control-sm item-subtotal text-end" value="${formatCurrency(subtotal)}" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove(); calcTotal();"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    $('#fakturItemsBody').append(tr);
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
    let diskon = parseFloat(tr.find('.item-diskon').val()) || 0;
    let subtotal = (qty * harga) - diskon;
    tr.find('.item-subtotal').val(formatCurrency(subtotal));
    calcTotal();
}

function calcTotal() {
    let subtotal = 0;
    $('#fakturItemsBody .item-row').each(function() {
        let qty = parseFloat($(this).find('.item-qty').val()) || 0;
        let harga = parseFloat($(this).find('.item-harga').val()) || 0;
        let diskon = parseFloat($(this).find('.item-diskon').val()) || 0;
        subtotal += (qty * harga) - diskon;
    });
    $('#fakturSubtotalDisplay').text(formatCurrency(subtotal));
    
    let diskonGlb = parseFloat($('#diskon').val()) || 0;
    let pajak = parseFloat($('#pajak').val()) || 0;
    let total = subtotal - diskonGlb + pajak;
    $('#fakturTotalDisplay').text(formatCurrency(total));
}

function addData() {
    $('#fakturPembelianForm')[0].reset();
    $('#faktur_id').val('');
    $('#fakturModalTitle').text('Tambah Faktur Pembelian');
    $('#fakturItemsBody').empty();
    addItemRow();
    $('#fakturPembelianModal').modal('show');
}

function submitFakturForm() {
    if (!$('#fakturPembelianForm')[0].checkValidity()) {
        $('#fakturPembelianForm')[0].reportValidity();
        return;
    }
    
    let items = [];
    let valid = true;
    $('#fakturItemsBody .item-row').each(function() {
        let produk_id = $(this).find('.item-produk').val();
        let qty = $(this).find('.item-qty').val();
        let harga = $(this).find('.item-harga').val();
        let diskon = $(this).find('.item-diskon').val();
        
        if (!produk_id || qty <= 0) {
            valid = false;
        } else {
            items.push({ produk_id: produk_id, qty: qty, harga: harga, diskon: diskon });
        }
    });
    
    if (!valid || items.length === 0) {
        showNotification('warning', 'Pilih produk dan tentukan qty lebih dari 0 untuk semua item');
        return;
    }
    
    let formData = new FormData($('#fakturPembelianForm')[0]);
    formData.append('items', JSON.stringify(items));
    
    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/faktur/ajax_save',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status) {
                showNotification('success', res.message);
                $('#fakturPembelianModal').modal('hide');
                reloadDataTable('fakturpembelianTable');
            } else {
                showNotification('error', res.message);
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal menyimpan data');
        }
    });
}

let viewFakturId = null;

function viewData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/faktur/ajax_get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status) {
                let data = res.data.faktur;
                viewFakturId = data.id;
                
                $('#viewNoFaktur').text(data.no_faktur);
                $('#viewPoRef').text(data.no_po ? data.no_po : '-');
                let statusBadge = '<span class="badge bg-' + (data.status == 'paid' ? 'success' : (data.status == 'partial' ? 'warning' : 'danger')) + '">' + data.status.toUpperCase() + '</span>';
                $('#viewStatus').html(statusBadge);
                $('#viewSupplier').text(data.supplier_nama);
                $('#viewTanggal').text(formatDate(data.tanggal));
                $('#viewJatuhTempo').text(data.jatuh_tempo ? formatDate(data.jatuh_tempo) : '-');
                
                if (data.status === 'unpaid') {
                    $('#btnPayFromView').removeClass('d-none');
                } else {
                    $('#btnPayFromView').addClass('d-none');
                }

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
                                <td class="text-end">${formatCurrency(item.diskon)}</td>
                                <td class="text-end">${formatCurrency(subtotal)}</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#viewItemsBody').append('<tr><td colspan="5" class="text-center">Tidak ada item</td></tr>');
                }
                $('#viewTotal').text(formatCurrency(total));
                $('#fakturPembelianViewModal').modal('show');
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

function payInvoice(id) {
    showConfirm('Tandai faktur ini sebagai Lunas?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/faktur/ajax_pay/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    reloadDataTable('fakturpembelianTable');
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal memproses pembayaran');
            }
        });
    });
}

function payFromView() {
    if (!viewFakturId) return;
    showConfirm('Tandai faktur ini sebagai Lunas?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/faktur/ajax_pay/' + viewFakturId,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    $('#fakturPembelianViewModal').modal('hide');
                    reloadDataTable('fakturpembelianTable');
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal memproses pembayaran');
            }
        });
    });
}

function deleteData(id) {
    showConfirm('Hapus data ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/faktur/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('fakturpembelianTable');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menghapus data');
            }
        });
    });
}
</script>

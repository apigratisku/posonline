<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Detail Stok Opname</h1>
        <p class="page-subtitle text-muted">Auditing dan penyesuaian stok fisik produk</p>
    </div>
    <div>
        <a href="<?= base_url('persediaan/opname') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
</div>

<div class="row">
    <!-- Info Card -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0 text-primary"><i class="fas fa-info-circle"></i> Informasi Opname</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="ps-0 py-2 text-muted" width="120">No. Opname</th>
                        <td class="py-2"><strong><?= htmlspecialchars($opname->no_opname) ?></strong></td>
                    </tr>
                    <tr>
                        <th class="ps-0 py-2 text-muted">Gudang</th>
                        <td class="py-2"><?= htmlspecialchars($opname->gudang_nama) ?></td>
                    </tr>
                    <tr>
                        <th class="ps-0 py-2 text-muted">Tanggal</th>
                        <td class="py-2"><?= date('d/m/Y', strtotime($opname->tanggal)) ?></td>
                    </tr>
                    <tr>
                        <th class="ps-0 py-2 text-muted">Status</th>
                        <td class="py-2">
                            <?php
                            $status_badge = '';
                            if ($opname->status == 'completed') {
                                $status_badge = '<span class="badge bg-success px-3 py-2">COMPLETED</span>';
                            } elseif ($opname->status == 'cancelled') {
                                $status_badge = '<span class="badge bg-danger px-3 py-2">CANCELLED</span>';
                            } else {
                                $status_badge = '<span class="badge bg-warning px-3 py-2 text-dark">PROCESS</span>';
                            }
                            echo $status_badge;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="ps-0 py-2 text-muted">Keterangan</th>
                        <td class="py-2"><?= htmlspecialchars($opname->keterangan ?: '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if ($opname->status == 'process'): ?>
            <!-- Add Item Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 text-primary"><i class="fas fa-plus"></i> Tambah Produk</h5>
                </div>
                <div class="card-body">
                    <form id="addProductForm">
                        <div class="mb-3">
                            <label for="produk_id" class="form-label">Pilih Produk <span class="text-danger">*</span></label>
                            <select class="form-select select2-produk" id="produk_id" name="produk_id" required style="width: 100%;">
                                <option value="">Cari Kode / Nama Produk...</option>
                                <?php foreach ($produks as $produk): ?>
                                    <option value="<?= $produk->id ?>">
                                        <?= htmlspecialchars($produk->kode . ' - ' . $produk->nama) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-plus"></i> Tambahkan ke Daftar
                        </button>
                        <hr class="my-2">
                        <button type="button" class="btn btn-outline-info w-100" onclick="loadAllProducts()">
                            <i class="fas fa-sync-alt"></i> Muat Semua Produk Gudang
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Details Table -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary"><i class="fas fa-list"></i> Daftar Item Audit</h5>
                <?php if ($opname->status == 'process'): ?>
                    <button type="button" class="btn btn-sm btn-info text-white" onclick="saveDraft()">
                        <i class="fas fa-save"></i> Simpan Draft
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <form id="detailsForm">
                    <input type="hidden" name="opname_id" value="<?= $opname->id ?>">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="40" class="text-center">No</th>
                                    <th>Produk</th>
                                    <th width="120" class="text-center">Stok Sistem</th>
                                    <th width="120" class="text-center">Stok Fisik</th>
                                    <th width="100" class="text-center">Selisih</th>
                                    <th>Keterangan</th>
                                    <?php if ($opname->status == 'process'): ?>
                                        <th width="60" class="text-center">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($details)): ?>
                                    <tr>
                                        <td colspan="<?= $opname->status == 'process' ? 7 : 6 ?>" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2 d-block"></i> Belum ada produk dalam audit opname ini.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($details as $detail): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($detail->produk_kode) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($detail->produk_nama) ?></small>
                                            </td>
                                            <td class="text-center stok-sistem" data-id="<?= $detail->id ?>"><?= number_format($detail->stok_sistem, 2, ',', '.') ?></td>
                                            <td class="text-center">
                                                <?php if ($opname->status == 'process'): ?>
                                                    <input type="number" 
                                                           name="items[<?= $detail->id ?>][stok_fisik]" 
                                                           value="<?= floatval($detail->stok_fisik) ?>" 
                                                           class="form-control form-control-sm text-center input-stok-fisik" 
                                                           data-id="<?= $detail->id ?>" 
                                                           min="0" 
                                                           step="0.01" 
                                                           required>
                                                <?php else: ?>
                                                    <?= number_format($detail->stok_fisik, 2, ',', '.') ?>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center font-weight-bold selisih-badge" id="selisih_<?= $detail->id ?>">
                                                <?= number_format($detail->selisih, 2, ',', '.') ?>
                                            </td>
                                            <td>
                                                <?php if ($opname->status == 'process'): ?>
                                                    <input type="text" 
                                                           name="items[<?= $detail->id ?>][keterangan]" 
                                                           value="<?= htmlspecialchars($detail->keterangan ?: '') ?>" 
                                                           class="form-control form-control-sm">
                                                <?php else: ?>
                                                    <?= htmlspecialchars($detail->keterangan ?: '-') ?>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($opname->status == 'process'): ?>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete-item" onclick="deleteItem(<?= $detail->id ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            
            <?php if ($opname->status == 'process'): ?>
                <div class="card-footer bg-light border-0 d-flex justify-content-end gap-2 py-3">
                    <button type="button" class="btn btn-danger" onclick="cancelOpname(<?= $opname->id ?>)">
                        <i class="fas fa-times"></i> Batalkan Opname
                    </button>
                    <button type="button" class="btn btn-success" onclick="completeOpname(<?= $opname->id ?>)">
                        <i class="fas fa-check"></i> Selesaikan Opname
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2 if available
    if ($.fn.select2) {
        $('.select2-produk').select2({
            placeholder: "Cari Kode / Nama Produk...",
            allowClear: true
        });
    }

    // Auto-calculate difference on physical stock change
    $('.input-stok-fisik').on('input change', function() {
        var id = $(this).data('id');
        var stokFisik = parseFloat($(this).val()) || 0;
        
        // Find corresponding system stock row
        var parentRow = $(this).closest('tr');
        var stokSistemText = parentRow.find('.stok-sistem').text().replace(/\./g, '').replace(',', '.');
        var stokSistem = parseFloat(stokSistemText) || 0;
        
        var selisih = stokFisik - stokSistem;
        var selisihCell = $('#selisih_' + id);
        
        // Format display
        var formattedSelisih = selisih.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        selisihCell.text(formattedSelisih);
        
        // Apply color code classes
        if (selisih > 0) {
            selisihCell.removeClass('text-danger text-dark').addClass('text-success').text('+' + formattedSelisih);
        } else if (selisih < 0) {
            selisihCell.removeClass('text-success text-dark').addClass('text-danger');
        } else {
            selisihCell.removeClass('text-success text-danger').addClass('text-dark');
        }
    }).trigger('input');

    // Add Product to audit list
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        var produkId = $('#produk_id').val();
        if (!produkId) return;

        showLoading();
        $.ajax({
            url: baseUrl + 'persediaan/stok_opname/ajax_add_item',
            type: 'POST',
            data: {
                opname_id: <?= $opname->id ?>,
                produk_id: produkId
            },
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    location.reload();
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menambahkan produk');
            }
        });
    });
});

function saveDraft(callback) {
    showLoading();
    $.ajax({
        url: baseUrl + 'persediaan/stok_opname/ajax_save_details',
        type: 'POST',
        data: $('#detailsForm').serialize(),
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status) {
                showNotification('success', res.message);
                if (typeof callback === 'function') {
                    callback();
                } else {
                    location.reload();
                }
            } else {
                showNotification('error', res.message);
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal menyimpan draft');
        }
    });
}

function deleteItem(detailId) {
    showConfirm('Apakah Anda yakin ingin menghapus produk ini dari daftar audit?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'persediaan/stok_opname/ajax_delete_item/' + detailId,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    location.reload();
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menghapus item');
            }
        });
    });
}

function completeOpname(id) {
    var totalItems = $('.input-stok-fisik').length;
    if (totalItems === 0) {
        showNotification('error', 'Daftar item audit kosong. Harap tambahkan produk terlebih dahulu.');
        return;
    }
    // Save draft first then complete
    saveDraft(function() {
        showConfirm('Apakah Anda yakin ingin menyelesaikan stok opname ini? Stok produk di sistem akan diperbarui secara fisik dan tidak dapat diubah lagi.', function() {
            showLoading();
            $.ajax({
                url: baseUrl + 'persediaan/stok_opname/ajax_complete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.status) {
                        showNotification('success', response.message);
                        window.location.href = baseUrl + 'persediaan/stok_opname/view/' + id;
                    } else {
                        showNotification('error', response.message);
                    }
                },
                error: function() {
                    hideLoading();
                    showNotification('error', 'Gagal menyelesaikan stok opname');
                }
            });
        });
    });
}

function cancelOpname(id) {
    showConfirm('Apakah Anda yakin ingin membatalkan stok opname ini? Semua data draft audit ini akan dikunci dan diabaikan.', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'persediaan/stok_opname/ajax_cancel/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    window.location.href = baseUrl + 'persediaan/stok_opname/view/' + id;
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal membatalkan stok opname');
            }
        });
    });
}

function loadAllProducts() {
    showConfirm('Apakah Anda yakin ingin memuat semua produk aktif dari gudang ini ke daftar opname? Stok fisik awal akan disesuaikan otomatis dengan stok sistem.', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'persediaan/stok_opname/ajax_load_all_products',
            type: 'POST',
            data: {
                opname_id: <?= $opname->id ?>
            },
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    location.reload();
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal memuat semua produk');
            }
        });
    });
}
</script>

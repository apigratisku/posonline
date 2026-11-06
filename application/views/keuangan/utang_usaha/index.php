<div class="page-header">
    <h1 class="page-title">Utang Usaha</h1>
    <p class="page-subtitle">Kelola utang kepada supplier</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Utang Usaha</h5>
        <?php if (can_create('keuangan')): ?>
            <button class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Utang
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="utangUsahaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No Faktur</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                    <th>Jatuh Tempo</th>
                    <th>Nominal</th>
                    <th>Terbayar</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Utang (Tambah/Edit) -->
<div class="modal fade" id="utangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="utangModalTitle">Tambah Utang Usaha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="utangForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="utang_id">
                    
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                            <option value="">Pilih Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier->id ?>"><?= htmlspecialchars($supplier->nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="faktur_pembelian_id" class="form-label">Link Faktur Pembelian (Opsional)</label>
                        <select class="form-select" id="faktur_pembelian_id" name="faktur_pembelian_id">
                            <option value="">Tanpa Faktur (Manual)</option>
                            <?php foreach ($fakturs as $faktur): ?>
                                <option value="<?= $faktur->id ?>"><?= htmlspecialchars($faktur->no_faktur) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="jatuh_tempo" class="form-label">Jatuh Tempo</label>
                        <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo">
                    </div>

                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nominal" name="nominal" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bayar Utang -->
<div class="modal fade" id="bayarUtangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bayar Utang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bayarUtangForm">
                <div class="modal-body">
                    <input type="hidden" name="utang_id" id="bayar_utang_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Sisa Utang</label>
                        <input type="text" class="form-control" id="sisa_utang" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="akun_kas_id" class="form-label">Akun Kas <span class="text-danger">*</span></label>
                        <select class="form-select" id="akun_kas_id" name="akun_kas_id" required>
                            <option value="">Pilih Akun</option>
                            <?php foreach ($akun_kas_list as $akun): ?>
                                <option value="<?= $akun->id ?>"><?= htmlspecialchars($akun->nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal Pembayaran <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nominal" name="nominal" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Bayar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    var table = initDataTable('utangUsahaTable', {
        ajax: {
            url: baseUrl + 'keuangan/utang-usaha/ajax_list',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8, orderable: false, searchable: false }
        ]
    });
    
    window.addData = function() {
        $('#utangForm')[0].reset();
        $('#utang_id').val('');
        $('#utangModalTitle').text('Tambah Utang Usaha');
        $('#utangModal').modal('show');
    };

    window.editData = function(id) {
        showLoading();
        $.ajax({
            url: baseUrl + 'keuangan/utang-usaha/ajax_get/' + id,
            type: 'GET',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    $('#utangForm')[0].reset();
                    $('#utang_id').val(res.data.id);
                    $('#supplier_id').val(res.data.supplier_id);
                    $('#faktur_pembelian_id').val(res.data.faktur_pembelian_id || '');
                    $('#tanggal').val(res.data.tanggal);
                    $('#jatuh_tempo').val(res.data.jatuh_tempo || '');
                    $('#nominal').val(res.data.nominal);
                    
                    $('#utangModalTitle').text('Edit Utang Usaha');
                    $('#utangModal').modal('show');
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal memuat data');
            }
        });
    };

    $('#utangForm').on('submit', function(e) {
        e.preventDefault();
        showLoading();
        $.ajax({
            url: baseUrl + 'keuangan/utang-usaha/ajax_save',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    $('#utangModal').modal('hide');
                    reloadDataTable('utangUsahaTable');
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

    window.deleteData = function(id) {
        showConfirm('Hapus data utang ini?', function() {
            showLoading();
            $.ajax({
                url: baseUrl + 'keuangan/utang-usaha/ajax_delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    hideLoading();
                    if (res.status) {
                        showNotification('success', res.message);
                        reloadDataTable('utangUsahaTable');
                    } else {
                        showNotification('error', res.message);
                    }
                },
                error: function() {
                    hideLoading();
                    showNotification('error', 'Gagal menghapus data');
                }
            });
        });
    };
    
    window.bayarUtang = function(id) {
        $.ajax({
            url: baseUrl + 'keuangan/utang-usaha/ajax_get/' + id,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    var data = response.data;
                    $('#bayar_utang_id').val(id);
                    $('#sisa_utang').val('Rp ' + parseFloat(data.sisa).toLocaleString('id-ID'));
                    $('#nominal').val(data.sisa);
                    $('#nominal').attr('max', data.sisa);
                    $('#bayarUtangModal').modal('show');
                }
            }
        });
    };
    
    $('#bayarUtangForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#bayar_utang_id').val();
        var formData = $(this).serialize();
        
        showLoading();
        $.ajax({
            url: baseUrl + 'keuangan/utang-usaha/ajax_bayar/' + id,
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', 'Pembayaran berhasil diproses');
                    $('#bayarUtangModal').modal('hide');
                    reloadDataTable('utangUsahaTable');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal memproses pembayaran');
            }
        });
    });
});
</script>

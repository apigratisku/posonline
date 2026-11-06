<div class="page-header">
    <h1 class="page-title">Piutang Usaha</h1>
    <p class="page-subtitle">Kelola piutang dari pelanggan</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Piutang Usaha</h5>
        <?php if (can_create('keuangan')): ?>
            <button class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Piutang
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="piutangUsahaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No Faktur</th>
                    <th>Pelanggan</th>
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

<!-- Modal Form Piutang (Tambah/Edit) -->
<div class="modal fade" id="piutangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="piutangModalTitle">Tambah Piutang Usaha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="piutangForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="piutang_id">
                    
                    <div class="mb-3">
                        <label for="pelanggan_id" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                        <select class="form-select" id="pelanggan_id" name="pelanggan_id" required>
                            <option value="">Pilih Pelanggan</option>
                            <?php foreach ($pelanggans as $pelanggan): ?>
                                <option value="<?= $pelanggan->id ?>"><?= htmlspecialchars($pelanggan->nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="faktur_penjualan_id" class="form-label">Link Faktur Penjualan (Opsional)</label>
                        <select class="form-select" id="faktur_penjualan_id" name="faktur_penjualan_id">
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

<!-- Modal Terima Bayar -->
<div class="modal fade" id="terimaBayarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terima Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="terimaBayarForm">
                <div class="modal-body">
                    <input type="hidden" name="piutang_id" id="bayar_piutang_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Sisa Piutang</label>
                        <input type="text" class="form-control" id="sisa_piutang" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="akun_kas_id_piutang" class="form-label">Akun Kas <span class="text-danger">*</span></label>
                        <select class="form-select" id="akun_kas_id_piutang" name="akun_kas_id" required>
                            <option value="">Pilih Akun</option>
                            <?php foreach ($akun_kas_list as $akun): ?>
                                <option value="<?= $akun->id ?>"><?= htmlspecialchars($akun->nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nominal_piutang" class="form-label">Nominal Pembayaran <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nominal_piutang" name="nominal" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Terima Bayar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    var table = initDataTable('piutangUsahaTable', {
        ajax: {
            url: baseUrl + 'keuangan/piutang-usaha/ajax_list',
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
        $('#piutangForm')[0].reset();
        $('#piutang_id').val('');
        $('#piutangModalTitle').text('Tambah Piutang Usaha');
        $('#piutangModal').modal('show');
    };

    window.editData = function(id) {
        showLoading();
        $.ajax({
            url: baseUrl + 'keuangan/piutang-usaha/ajax_get/' + id,
            type: 'GET',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    $('#piutangForm')[0].reset();
                    $('#piutang_id').val(res.data.id);
                    $('#pelanggan_id').val(res.data.pelanggan_id);
                    $('#faktur_penjualan_id').val(res.data.faktur_penjualan_id || '');
                    $('#tanggal').val(res.data.tanggal);
                    $('#jatuh_tempo').val(res.data.jatuh_tempo || '');
                    $('#nominal').val(res.data.nominal);
                    
                    $('#piutangModalTitle').text('Edit Piutang Usaha');
                    $('#piutangModal').modal('show');
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

    $('#piutangForm').on('submit', function(e) {
        e.preventDefault();
        showLoading();
        $.ajax({
            url: baseUrl + 'keuangan/piutang-usaha/ajax_save',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    $('#piutangModal').modal('hide');
                    reloadDataTable('piutangUsahaTable');
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
        showConfirm('Hapus data piutang ini?', function() {
            showLoading();
            $.ajax({
                url: baseUrl + 'keuangan/piutang-usaha/ajax_delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    hideLoading();
                    if (res.status) {
                        showNotification('success', res.message);
                        reloadDataTable('piutangUsahaTable');
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
    
    window.terimaBayar = function(id) {
        $.ajax({
            url: baseUrl + 'keuangan/piutang-usaha/ajax_get/' + id,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    var data = response.data;
                    $('#bayar_piutang_id').val(id);
                    $('#sisa_piutang').val('Rp ' + parseFloat(data.sisa).toLocaleString('id-ID'));
                    $('#nominal_piutang').val(data.sisa);
                    $('#nominal_piutang').attr('max', data.sisa);
                    $('#terimaBayarModal').modal('show');
                }
            }
        });
    };
    
    $('#terimaBayarForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#bayar_piutang_id').val();
        var formData = $(this).serialize();
        
        showLoading();
        $.ajax({
            url: baseUrl + 'keuangan/piutang-usaha/ajax_terima_bayar/' + id,
            type: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', 'Pembayaran berhasil diproses');
                    $('#terimaBayarModal').modal('hide');
                    reloadDataTable('piutangUsahaTable');
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

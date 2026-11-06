<div class="page-header">
    <h1 class="page-title">Buku Kas</h1>
    <p class="page-subtitle">Transaksi kas masuk dan keluar</p>
</div>

<?php if ($akun_kas): ?>
<div class="alert alert-info">
    <strong>Akun:</strong> <?php echo $akun_kas->nama; ?> (<?php echo $akun_kas->kode; ?>)<br>
    <strong>Saldo:</strong> Rp <?php echo number_format($akun_kas->saldo_akhir, 0, ',', '.'); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Transaksi Buku Kas</h5>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="filterAkunKas" style="width: 200px;">
                <option value="">Semua Akun</option>
                <?php foreach ($akun_kas_list as $akun): ?>
                    <option value="<?php echo $akun->id; ?>" <?php echo ($akun_kas && $akun_kas->id == $akun->id) ? 'selected' : ''; ?>>
                        <?php echo $akun->nama; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" class="form-control form-control-sm" id="startDate" style="width: 150px;" value="<?php echo date('Y-m-01'); ?>">
            <input type="date" class="form-control form-control-sm" id="endDate" style="width: 150px;" value="<?php echo date('Y-m-d'); ?>">
            <?php if (can_create('keuangan')): ?>
                <button class="btn btn-primary btn-sm" onclick="addData()">
                    <i class="fas fa-plus"></i> Tambah Transaksi
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <table id="bukuKasTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Akun</th>
                    <th>Tipe</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Referensi</th>
                    <th>Saldo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="bukuKasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bukuKasForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="buku_kas_id">
                    
                    <div class="mb-3">
                        <label for="akun_kas_id" class="form-label">Akun Kas <span class="text-danger">*</span></label>
                        <select class="form-select" id="akun_kas_id" name="akun_kas_id" required>
                            <option value="">Pilih Akun</option>
                            <?php foreach ($akun_kas_list as $akun): ?>
                                <option value="<?php echo $akun->id; ?>" <?php echo ($akun_kas && $akun_kas->id == $akun->id) ? 'selected' : ''; ?>>
                                    <?php echo $akun->nama; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipe" name="tipe" required>
                            <option value="debit">Debit (Masuk)</option>
                            <option value="kredit">Kredit (Keluar)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nominal" name="nominal" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="referensi" class="form-label">Referensi</label>
                        <input type="text" class="form-control" id="referensi" name="referensi">
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

<script>
$(document).ready(function() {
    var table = initDataTable('bukuKasTable', {
        ajax: {
            url: baseUrl + 'keuangan/buku-kas/ajax_list',
            type: 'POST',
            data: function(d) {
                d.akun_kas_id = $('#filterAkunKas').val();
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable: false, searchable: false }
        ]
    });
    
    $('#filterAkunKas, #startDate, #endDate').on('change', function() {
        reloadDataTable(table);
    });
    
    handleFormSubmit('bukuKasForm', {
        url: baseUrl + 'keuangan/buku-kas/ajax_save',
        modal: '#bukuKasModal',
        table: table,
        onSuccess: function(response) {
            showNotification('success', 'Data berhasil disimpan');
        }
    });
    
    window.addData = function() {
        $('#bukuKasForm')[0].reset();
        $('#buku_kas_id').val('');
        $('#tanggal').val('<?php echo date('Y-m-d'); ?>');
        $('#modalTitle').text('Tambah Transaksi');
        $('#bukuKasModal').modal('show');
    };
    
    window.deleteData = function(id) {
        showConfirm('Apakah Anda yakin ingin menghapus transaksi ini?', function() {
            $.ajax({
                url: baseUrl + 'keuangan/buku-kas/ajax_delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.status) {
                        showNotification('success', 'Data berhasil dihapus');
                        reloadDataTable(table);
                    } else {
                        showNotification('error', response.message);
                    }
                }
            });
        });
    };
});
</script>


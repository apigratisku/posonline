<div class="page-header">
    <h1 class="page-title">Perpindahan Stok</h1>
    <p class="page-subtitle">Kelola perpindahan stok antar gudang</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Perpindahan Stok</h5>
        <?php if (can_create('persediaan')): ?>
            <button class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Perpindahan
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="perpindahanStokTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No Mutasi</th>
                    <th>Gudang Asal</th>
                    <th>Gudang Tujuan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="perpindahanStokModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Perpindahan Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="perpindahanStokForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="perpindahan_stok_id">
                    
                    <div class="mb-3">
                        <label for="gudang_asal_id" class="form-label">Gudang Asal <span class="text-danger">*</span></label>
                        <select class="form-select" id="gudang_asal_id" name="gudang_asal_id" required>
                            <option value="">Pilih Gudang Asal</option>
                            <?php foreach ($gudangs as $gudang): ?>
                                <option value="<?php echo $gudang->id; ?>">
                                    <?php echo $gudang->nama; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gudang_tujuan_id" class="form-label">Gudang Tujuan <span class="text-danger">*</span></label>
                        <select class="form-select" id="gudang_tujuan_id" name="gudang_tujuan_id" required>
                            <option value="">Pilih Gudang Tujuan</option>
                            <?php foreach ($gudangs as $gudang): ?>
                                <option value="<?php echo $gudang->id; ?>">
                                    <?php echo $gudang->nama; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
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
    var table = initDataTable('perpindahanStokTable', {
        ajax: {
            url: baseUrl + 'persediaan/perpindahan_stok/ajax_list',
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
    
    handleFormSubmit('perpindahanStokForm', {
        url: baseUrl + 'persediaan/perpindahan_stok/ajax_save',
        modal: '#perpindahanStokModal',
        table: table,
        onSuccess: function(response) {
            showNotification('success', 'Data berhasil disimpan');
        }
    });
    
    window.addData = function() {
        $('#perpindahanStokForm')[0].reset();
        $('#perpindahan_stok_id').val('');
        $('#tanggal').val('<?php echo date('Y-m-d'); ?>');
        $('#modalTitle').text('Tambah Perpindahan Stok');
        $('#perpindahanStokModal').modal('show');
    };
    
    window.completeMutasi = function(id) {
        showConfirm('Apakah Anda yakin ingin menyelesaikan perpindahan stok ini?', function() {
            $.ajax({
                url: baseUrl + 'persediaan/perpindahan_stok/ajax_complete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.status) {
                        showNotification('success', response.message);
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


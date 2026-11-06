<div class="page-header">
    <h1 class="page-title">Stok Opname</h1>
    <p class="page-subtitle">Kelola stok opname untuk pengecekan fisik stok</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Stok Opname</h5>
        <?php if (can_create('persediaan')): ?>
            <button class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Stok Opname
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="stokOpnameTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No Opname</th>
                    <th>Gudang</th>
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
<div class="modal fade" id="stokOpnameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Stok Opname</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stokOpnameForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="stok_opname_id">
                    
                    <div class="mb-3">
                        <label for="gudang_id" class="form-label">Gudang <span class="text-danger">*</span></label>
                        <select class="form-select" id="gudang_id" name="gudang_id" required>
                            <option value="">Pilih Gudang</option>
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
    var table = initDataTable('stokOpnameTable', {
        ajax: {
            url: baseUrl + 'persediaan/stok_opname/ajax_list',
            type: 'POST'
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, orderable: false, searchable: false }
        ]
    });
    
    handleFormSubmit('stokOpnameForm', {
        url: baseUrl + 'persediaan/stok_opname/ajax_save',
        modal: '#stokOpnameModal',
        table: table,
        onSuccess: function(response) {
            showNotification('success', 'Data berhasil disimpan');
            if (response.status && response.data && response.data.id) {
                window.location.href = baseUrl + 'persediaan/stok_opname/view/' + response.data.id;
            }
        }
    });
    
    window.addData = function() {
        $('#stokOpnameForm')[0].reset();
        $('#stok_opname_id').val('');
        $('#tanggal').val('<?php echo date('Y-m-d'); ?>');
        $('#modalTitle').text('Tambah Stok Opname');
        $('#stokOpnameModal').modal('show');
    };
    
    window.completeOpname = function(id) {
        showConfirm('Apakah Anda yakin ingin menyelesaikan stok opname ini? Stok produk akan disesuaikan secara fisik.', function() {
            $.ajax({
                url: baseUrl + 'persediaan/stok_opname/ajax_complete/' + id,
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
    
    window.cancelOpname = function(id) {
        showConfirm('Apakah Anda yakin ingin membatalkan stok opname ini?', function() {
            $.ajax({
                url: baseUrl + 'persediaan/stok_opname/ajax_cancel/' + id,
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


<div class="page-header">
    <h1 class="page-title">Defecta</h1>
    <p class="page-subtitle">Kelola data produk defecta</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Defecta</h5>
        <?php if (can_create('persediaan')): ?>
            <button class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Defecta
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="defectaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Gudang</th>
                    <th>Qty</th>
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
<div class="modal fade" id="defectaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Defecta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="defectaForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="defecta_id">
                    
                    <div class="mb-3">
                        <label for="produk_id" class="form-label">Produk <span class="text-danger">*</span></label>
                        <select class="form-select" id="produk_id" name="produk_id" required>
                            <option value="">Pilih Produk</option>
                            <?php foreach ($produks as $produk): ?>
                                <option value="<?php echo $produk->id; ?>">
                                    <?php echo $produk->kode . ' - ' . $produk->nama; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
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
                        <label for="qty" class="form-label">Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="qty" name="qty" step="1" min="1" required>
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
    var table = initDataTable('defectaTable', {
        ajax: {
            url: baseUrl + 'persediaan/defecta/ajax_list',
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
    
    handleFormSubmit('defectaForm', {
        url: baseUrl + 'persediaan/defecta/ajax_save',
        modal: '#defectaModal',
        table: table,
        onSuccess: function(response) {
            showNotification('success', 'Data berhasil disimpan');
        }
    });
    
    window.addData = function() {
        $('#defectaForm')[0].reset();
        $('#defecta_id').val('');
        $('#tanggal').val('<?php echo date('Y-m-d'); ?>');
        $('#modalTitle').text('Tambah Defecta');
        $('#defectaModal').modal('show');
    };
    
    window.approveData = function(id) {
        showConfirm('Apakah Anda yakin ingin menyetujui defecta ini?', function() {
            $.ajax({
                url: baseUrl + 'persediaan/defecta/ajax_approve/' + id,
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
    
    window.rejectData = function(id) {
        showConfirm('Apakah Anda yakin ingin menolak defecta ini?', function() {
            // Implement reject functionality if needed
            showNotification('info', 'Fitur reject belum diimplementasikan');
        });
    };
});
</script>


<div class="page-header">
    <h1 class="page-title">Master CoA</h1>
    <p class="page-subtitle">Kelola Chart of Accounts</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar CoA</h5>
        <?php if (can_create('akuntansi')): ?>
            <a href="<?= base_url('akuntansi/coa/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah CoA
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="coaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
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
    // Initialize DataTable
    var table = initDataTable('coaTable', {
        ajax: {
            url: baseUrl + 'akuntansi/coa/ajax_list',
            type: 'POST'
        },
        order: [[5, 'desc']],
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
});

function deleteData(id) {
    showConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'akuntansi/coa/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('coaTable');
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


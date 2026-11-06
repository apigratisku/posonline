<div class="page-header">
    <h1 class="page-title">Konsinyasi Masuk</h1>
    <p class="page-subtitle">Kelola konsinyasi masuk dari supplier</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Konsinyasi Masuk</h5>
        <?php if (can_create('konsinyasi')): ?>
            <a href="<?= base_url('konsinyasi/masuk/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Konsinyasi Masuk
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="masukTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>No. Konsinyasi</th>
                    <th>Supplier</th>
                    <th>Gudang</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Keterangan</th>
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
    var table = initDataTable('masukTable', {
        ajax: {
            url: baseUrl + 'konsinyasi/masuk/ajax_list',
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
            { data: 6 },
            { data: 7 },
            { data: 8, orderable: false, searchable: false }
        ]
    });
});

function deleteData(id) {
    showConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'konsinyasi/masuk/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('masukTable');
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


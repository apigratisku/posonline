<div class="page-header">
    <h1 class="page-title">Jenis Pelayanan</h1>
    <p class="page-subtitle">Kelola data Jenis Pelayanan</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Jenis Pelayanan</h5>
        <?php if (can_create('master')): ?>
            <a href="<?= base_url('masterdata/masterpelayanan/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Jenis Pelayanan
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="jenisPelayananTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    initDataTable('jenisPelayananTable', {
        ajax: { url: baseUrl + 'masterdata/masterpelayanan/ajax_list', type: 'POST' },
        order: [[0, 'asc']],
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
            url: baseUrl + 'masterdata/masterpelayanan/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('jenisPelayananTable');
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

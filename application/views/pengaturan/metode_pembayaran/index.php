<div class="page-header">
    <h1 class="page-title">Metode Pembayaran</h1>
    <p class="page-subtitle">Kelola metode pembayaran</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Metode Pembayaran</h5>
        <?php if (can_create('pengaturan')): ?>
            <a href="<?= base_url('pengaturan/metode-pembayaran/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="metodeTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    initDataTable('metodeTable', {
        ajax: { url: baseUrl + 'pengaturan/metode-pembayaran/ajax_list', type: 'POST' },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5, orderable: false, searchable: false },
        ]
    });
});

function deleteData(id) {
    showConfirm('Hapus data ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pengaturan/metode-pembayaran/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('metodeTable');
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

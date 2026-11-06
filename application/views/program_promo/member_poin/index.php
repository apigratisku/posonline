<div class="page-header">
    <h1 class="page-title">Member Poin</h1>
    <p class="page-subtitle">Kelola Member Poin</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Member Poin</h5>
        <?php if (can_create('promo')): ?>
            <a href="<?= base_url('promo/poin/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="member_poinTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Poin</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    initDataTable('member_poinTable', {
        ajax: { url: baseUrl + 'promo/poin/ajax_list', type: 'POST' },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false, searchable: false },
        ]
    });
});

function deleteData(id) {
    showConfirm('Hapus data ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'promo/poin/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('member_poinTable');
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

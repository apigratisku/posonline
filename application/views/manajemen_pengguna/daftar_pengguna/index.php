<div class="page-header">
    <h1 class="page-title">Daftar Pengguna</h1>
    <p class="page-subtitle">Kelola data pengguna sistem</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Pengguna</h5>
        <?php if (can_create('manajemen')): ?>
            <a href="<?= base_url('manajemen/pengguna/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Pengguna
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="penggunaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Peran</th>
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
    var table = initDataTable('penggunaTable', {
        ajax: {
            url: baseUrl + 'manajemen/pengguna/ajax_list',
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
    
    window.deleteData = function(id) {
        showConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
            $.ajax({
                url: baseUrl + 'manajemen/pengguna/ajax_delete/' + id,
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


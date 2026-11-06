<div class="page-header">
    <h1 class="page-title">Daftar Akun Kas</h1>
    <p class="page-subtitle">Kelola akun kas dan bank</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Akun Kas</h5>
        <?php if (can_create('keuangan')): ?>
            <a href="<?= base_url('keuangan/akun-kas/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Akun Kas
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="akunKasTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Saldo</th>
                    <th>Status</th>
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
    var table = initDataTable('akunKasTable', {
        ajax: {
            url: baseUrl + 'keuangan/akun-kas/ajax_list',
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
    
    window.deleteData = function(id) {
        showConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
            $.ajax({
                url: baseUrl + 'keuangan/akun-kas/ajax_delete/' + id,
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
    
    window.viewBukuKas = function(id) {
        window.location.href = baseUrl + 'keuangan/buku-kas/' + id;
    };
});
</script>


<div class="page-header">
    <h1 class="page-title">Kunjungan Pasien</h1>
    <p class="page-subtitle">Kelola data kunjungan pasien</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Kunjungan Pasien</h5>
        <?php if (can_create('pelayanan')): ?>
            <a href="<?= base_url('pelayanan/kunjungan-pasien/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kunjungan
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="kunjunganTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No. Kunjungan</th>
                    <th>Pelanggan</th>
                    <th>Dokter</th>
                    <th>Tanggal</th>
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
    var table = initDataTable('kunjunganTable', {
        ajax: {
            url: baseUrl + 'pelayanan/kunjungan-pasien/ajax_list',
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
                url: baseUrl + 'pelayanan/kunjungan-pasien/ajax_delete/' + id,
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


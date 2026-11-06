<div class="page-header">
    <h1 class="page-title">KPI Karyawan</h1>
    <p class="page-subtitle">Kelola KPI karyawan</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar KPI Karyawan</h5>
        <?php if (can_create('kpi')): ?>
            <a href="<?= base_url('kpi/karyawan/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="kpiTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Karyawan</th>
                    <th>Periode</th>
                    <th>Target</th>
                    <th>Pencapaian</th>
                    <th>%</th>
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
    initDataTable('kpiTable', {
        ajax: { url: baseUrl + 'kpi/karyawan/ajax_list', type: 'POST' },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable: false, searchable: false },
        ]
    });
});

function deleteData(id) {
    showConfirm('Hapus data ini?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'kpi/karyawan/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('kpiTable');
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

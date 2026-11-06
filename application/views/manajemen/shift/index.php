<div class="page-header">
    <h1 class="page-title">Riwayat Shift</h1>
    <p class="page-subtitle">Lihat riwayat shift karyawan</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Shift</h5>
        <?php if (can_create('manajemen')): ?>
            <a href="<?= base_url('manajemen/shift/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Riwayat Shift
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="filter_user" class="form-label">Filter Karyawan</label>
                <select class="form-select" id="filter_user">
                    <option value="">Semua Karyawan</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user->id ?>"><?= $user->nama_lengkap ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_shift" class="form-label">Filter Shift</label>
                <select class="form-select" id="filter_shift">
                    <option value="">Semua Shift</option>
                    <?php foreach ($shifts as $shift): ?>
                        <option value="<?= $shift->id ?>"><?= $shift->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-secondary d-block" onclick="applyFilter()">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </div>
        <table id="shiftTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Shift</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
var shiftTable;

$(document).ready(function() {
    shiftTable = initDataTable('shiftTable', {
        ajax: {
            url: baseUrl + 'manajemen/shift/ajax_list',
            type: 'POST',
            data: function(d) {
                d.user_id = $('#filter_user').val();
                d.shift_id = $('#filter_shift').val();
            }
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
    
    window.deleteData = function(id) {
        showConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
            $.ajax({
                url: baseUrl + 'manajemen/shift/ajax_delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.status) {
                        showNotification('success', 'Data berhasil dihapus');
                        reloadDataTable(shiftTable);
                    } else {
                        showNotification('error', response.message);
                    }
                }
            });
        });
    };
    
    window.applyFilter = function() {
        reloadDataTable(shiftTable);
    };
    
    window.editData = function(id) {
        window.location.href = baseUrl + 'manajemen/shift/edit/' + id;
    };
    
    window.viewData = function(id) {
        window.location.href = baseUrl + 'manajemen/shift/view/' + id;
    };
});
</script>

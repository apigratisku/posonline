<div class="page-header">
    <h1 class="page-title">Presensi Karyawan</h1>
    <p class="page-subtitle">Kelola data presensi karyawan</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Presensi</h5>
        <?php if (can_create('manajemen')): ?>
            <a href="<?= base_url('manajemen/presensi/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Presensi
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
                <label for="filter_tanggal" class="form-label">Filter Tanggal</label>
                <input type="date" class="form-control" id="filter_tanggal" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-secondary d-block" onclick="applyFilter()">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </div>
        <table id="presensiTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Shift</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
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
var presensiTable;

$(document).ready(function() {
    presensiTable = initDataTable('presensiTable', {
        ajax: {
            url: baseUrl + 'manajemen/presensi/ajax_list',
            type: 'POST',
            data: function(d) {
                d.user_id = $('#filter_user').val();
                d.tanggal = $('#filter_tanggal').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable: false, searchable: false }
        ]
    });
    
    window.deleteData = function(id) {
        showConfirm('Apakah Anda yakin ingin menghapus data ini?', function() {
            $.ajax({
                url: baseUrl + 'manajemen/presensi/ajax_delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.status) {
                        showNotification('success', 'Data berhasil dihapus');
                        reloadDataTable(presensiTable);
                    } else {
                        showNotification('error', response.message);
                    }
                }
            });
        });
    };
    
    window.checkIn = function(id) {
        $.ajax({
            url: baseUrl + 'manajemen/presensi/ajax_get/' + id,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    var data = response.data;
                    $.ajax({
                        url: baseUrl + 'manajemen/presensi/ajax_checkin',
                        type: 'POST',
                        data: {
                            user_id: data.user_id,
                            tanggal: data.tanggal
                        },
                        success: function(response) {
                            if (response.status) {
                                showNotification('success', 'Check in berhasil');
                                reloadDataTable(presensiTable);
                            } else {
                                showNotification('error', response.message);
                            }
                        }
                    });
                }
            }
        });
    };
    
    window.checkOut = function(id) {
        $.ajax({
            url: baseUrl + 'manajemen/presensi/ajax_checkout/' + id,
            type: 'POST',
            success: function(response) {
                if (response.status) {
                    showNotification('success', 'Check out berhasil');
                    reloadDataTable(presensiTable);
                } else {
                    showNotification('error', response.message);
                }
            }
        });
    };
    
    window.applyFilter = function() {
        reloadDataTable(presensiTable);
    };
    
    window.editData = function(id) {
        window.location.href = baseUrl + 'manajemen/presensi/edit/' + id;
    };
    
    window.viewData = function(id) {
        window.location.href = baseUrl + 'manajemen/presensi/view/' + id;
    };
});
</script>

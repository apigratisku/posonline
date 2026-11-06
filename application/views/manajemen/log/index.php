<div class="page-header">
    <h1 class="page-title">Log Aktivitas</h1>
    <p class="page-subtitle">Lihat log aktivitas sistem</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Log Aktivitas</h5>
        <button class="btn btn-success" onclick="exportLog()">
            <i class="fas fa-download"></i> Export CSV
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filter_user" class="form-label">Filter Pengguna</label>
                <select class="form-select" id="filter_user">
                    <option value="">Semua Pengguna</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user->id ?>"><?= $user->nama_lengkap ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_modul" class="form-label">Filter Modul</label>
                <select class="form-select" id="filter_modul">
                    <option value="">Semua Modul</option>
                    <?php foreach ($moduls as $modul): ?>
                        <option value="<?= $modul->kode ?>"><?= $modul->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="filter_start_date" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
            </div>
            <div class="col-md-2">
                <label for="filter_end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="filter_end_date" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-secondary d-block" onclick="applyFilter()">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
        <table id="logTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal/Waktu</th>
                    <th>User</th>
                    <th>Modul</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="logDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Log Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logDetailContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
var logTable;

// Define functions globally (before document ready)
window.exportLog = function() {
    try {
        var params = new URLSearchParams();
        if (typeof $ !== 'undefined' && $('#filter_user').length && $('#filter_user').val()) {
            params.append('user_id', $('#filter_user').val());
        }
        if (typeof $ !== 'undefined' && $('#filter_modul').length && $('#filter_modul').val()) {
            params.append('modul', $('#filter_modul').val());
        }
        if (typeof $ !== 'undefined' && $('#filter_start_date').length && $('#filter_start_date').val()) {
            params.append('start_date', $('#filter_start_date').val());
        }
        if (typeof $ !== 'undefined' && $('#filter_end_date').length && $('#filter_end_date').val()) {
            params.append('end_date', $('#filter_end_date').val());
        }
        
        window.location.href = baseUrl + 'manajemen/log/ajax_export?' + params.toString();
    } catch (e) {
        console.error('Error in exportLog:', e);
    }
};

window.applyFilter = function() {
    if (typeof logTable !== 'undefined' && logTable) {
        reloadDataTable(logTable);
    } else if (typeof $ !== 'undefined') {
        // Fallback: reload by table ID
        reloadDataTable('logTable');
    }
};

$(document).ready(function() {
    logTable = initDataTable('logTable', {
        ajax: {
            url: baseUrl + 'manajemen/log/ajax_list',
            type: 'POST',
            data: function(d) {
                d.user_id = $('#filter_user').val();
                d.modul = $('#filter_modul').val();
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
        ],
        order: [[0, 'desc']]
    });
    
    window.viewDetail = function(id) {
        $('#logDetailContent').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        // Show modal using Bootstrap 5 API
        var modalElement = document.getElementById('logDetailModal');
        if (modalElement) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                $('#logDetailModal').modal('show');
            }
        }
        
        $.ajax({
            url: baseUrl + 'manajemen/log/ajax_get/' + id,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    var data = response.data;
                    var html = '<div class="row">';
                    html += '<div class="col-md-6 mb-3"><strong>Tanggal/Waktu:</strong><br>' + (data.created_at ? new Date(data.created_at).toLocaleString('id-ID') : '-') + '</div>';
                    html += '<div class="col-md-6 mb-3"><strong>User:</strong><br>' + (data.nama_lengkap || data.username || 'System') + '</div>';
                    html += '<div class="col-md-6 mb-3"><strong>Modul:</strong><br>' + (data.modul ? data.modul.charAt(0).toUpperCase() + data.modul.slice(1) : '-') + '</div>';
                    html += '<div class="col-md-6 mb-3"><strong>Aksi:</strong><br><span class="badge bg-info">' + (data.aksi ? data.aksi.charAt(0).toUpperCase() + data.aksi.slice(1) : '-') + '</span></div>';
                    html += '<div class="col-12 mb-3"><strong>Deskripsi:</strong><br>' + (data.deskripsi || '-') + '</div>';
                    html += '<div class="col-md-6 mb-3"><strong>IP Address:</strong><br>' + (data.ip_address || '-') + '</div>';
                    html += '<div class="col-md-6 mb-3"><strong>User Agent:</strong><br><small>' + (data.user_agent || '-') + '</small></div>';
                    html += '</div>';
                    $('#logDetailContent').html(html);
                } else {
                    $('#logDetailContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#logDetailContent').html('<div class="alert alert-danger">Gagal memuat data</div>');
            }
        });
    };
    
    window.deleteData = function(id) {
        showConfirm('Hapus Log', 'Apakah Anda yakin ingin menghapus log ini?', function() {
            $.ajax({
                url: baseUrl + 'manajemen/log/ajax_delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.status) {
                        showNotification('success', 'Log berhasil dihapus');
                        reloadDataTable(logTable);
                    } else {
                        showNotification('error', response.message);
                    }
                }
            });
        });
    };
    
    // Override with enhanced version after document ready
    window.applyFilter = function() {
        if (logTable) {
            reloadDataTable(logTable);
        } else {
            reloadDataTable('logTable');
        }
    };
    
    // Override with enhanced version after document ready
    window.exportLog = function() {
        try {
            var params = new URLSearchParams();
            if ($('#filter_user').val()) {
                params.append('user_id', $('#filter_user').val());
            }
            if ($('#filter_modul').val()) {
                params.append('modul', $('#filter_modul').val());
            }
            if ($('#filter_start_date').val()) {
                params.append('start_date', $('#filter_start_date').val());
            }
            if ($('#filter_end_date').val()) {
                params.append('end_date', $('#filter_end_date').val());
            }
            
            window.location.href = baseUrl + 'manajemen/log/ajax_export?' + params.toString();
        } catch (e) {
            console.error('Error in exportLog:', e);
        }
    };
});
</script>

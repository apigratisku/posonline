<div class="page-header">
    <h1 class="page-title">Peran & Hak Akses</h1>
    <p class="page-subtitle">Kelola peran dan hak akses pengguna</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Peran</h5>
        <?php if (can_create('manajemen')): ?>
            <a href="<?= base_url('manajemen-pengguna/peran-hak-akses/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Peran
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="peranTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
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

<!-- Modal Permissions -->
<div class="modal fade" id="permissionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kelola Hak Akses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="peran_id_permissions">
                <div id="permissionsContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="savePermissions()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = initDataTable('peranTable', {
        ajax: {
            url: baseUrl + 'manajemen-pengguna/peran-hak-akses/ajax_list',
            type: 'POST'
        },
        order: [[5, 'desc']],
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
                url: baseUrl + 'manajemen-pengguna/peran-hak-akses/ajax_delete/' + id,
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
    
    window.managePermissions = function(id) {
        $('#peran_id_permissions').val(id);
        $.ajax({
            url: baseUrl + 'manajemen-pengguna/peran-hak-akses/ajax_get_permissions/' + id,
            type: 'GET',
            success: function(response) {
                if (response.status) {
                    var html = '<div class="row">';
                    var moduls = <?= json_encode($modul_list) ?>;
                    var permissions = {};
                    
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(perm) {
                            permissions[perm.modul_id] = perm;
                        });
                    }
                    
                    moduls.forEach(function(modul) {
                        var perm = permissions[modul.id] || {};
                        html += '<div class="col-md-6 mb-3">';
                        html += '<div class="card">';
                        html += '<div class="card-header"><strong>' + modul.nama + '</strong></div>';
                        html += '<div class="card-body">';
                        html += '<div class="form-check"><input class="form-check-input" type="checkbox" name="permissions[' + modul.id + '][view]" id="perm_' + modul.id + '_view" ' + (perm.view ? 'checked' : '') + '><label class="form-check-label" for="perm_' + modul.id + '_view">Lihat</label></div>';
                        html += '<div class="form-check"><input class="form-check-input" type="checkbox" name="permissions[' + modul.id + '][create]" id="perm_' + modul.id + '_create" ' + (perm.create ? 'checked' : '') + '><label class="form-check-label" for="perm_' + modul.id + '_create">Tambah</label></div>';
                        html += '<div class="form-check"><input class="form-check-input" type="checkbox" name="permissions[' + modul.id + '][update]" id="perm_' + modul.id + '_update" ' + (perm.update ? 'checked' : '') + '><label class="form-check-label" for="perm_' + modul.id + '_update">Ubah</label></div>';
                        html += '<div class="form-check"><input class="form-check-input" type="checkbox" name="permissions[' + modul.id + '][delete]" id="perm_' + modul.id + '_delete" ' + (perm.delete ? 'checked' : '') + '><label class="form-check-label" for="perm_' + modul.id + '_delete">Hapus</label></div>';
                        html += '</div></div></div>';
                    });
                    
                    html += '</div>';
                    $('#permissionsContainer').html(html);
                    
                    // Show modal using Bootstrap 5 API
                    var modalElement = document.getElementById('permissionsModal');
                    if (modalElement) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var modal = new bootstrap.Modal(modalElement);
                            modal.show();
                        } else if (typeof $ !== 'undefined' && $.fn.modal) {
                            $('#permissionsModal').modal('show');
                        }
                    }
                }
            }
        });
    };
    
    window.savePermissions = function() {
        var peran_id = $('#peran_id_permissions').val();
        var permissions = {};
        
        $('#permissionsContainer input[type="checkbox"]').each(function() {
            var name = $(this).attr('name');
            if (name) {
                var matches = name.match(/permissions\[(\d+)\]\[(\w+)\]/);
                if (matches) {
                    var modul_id = matches[1];
                    var action = matches[2];
                    if (!permissions[modul_id]) {
                        permissions[modul_id] = {};
                    }
                    permissions[modul_id][action] = $(this).is(':checked') ? 1 : 0;
                }
            }
        });
        
        $.ajax({
            url: baseUrl + 'manajemen-pengguna/peran-hak-akses/ajax_save_permissions/' + peran_id,
            type: 'POST',
            data: { permissions: permissions },
            success: function(response) {
                if (response.status) {
                    showNotification('success', 'Hak akses berhasil disimpan');
                    
                    // Hide modal using Bootstrap 5 API
                    var modalElement = document.getElementById('permissionsModal');
                    if (modalElement) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        } else if (typeof $ !== 'undefined' && $.fn.modal) {
                            $('#permissionsModal').modal('hide');
                        }
                    }
                } else {
                    showNotification('error', response.message);
                }
            }
        });
    };
});
</script>


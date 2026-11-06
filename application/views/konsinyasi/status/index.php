<div class="page-header">
    <h1 class="page-title">Status Konsinyasi</h1>
    <p class="page-subtitle">Monitor status konsinyasi</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Status Konsinyasi</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Filter Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="diterima">Diterima</option>
                    <option value="retur">Retur</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button>
            </div>
        </div>
        <table id="statusTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>No. Konsinyasi</th>
                    <th>Supplier</th>
                    <th>Gudang</th>
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
    var table = initDataTable('statusTable', {
        ajax: {
            url: baseUrl + 'konsinyasi/status/ajax_list',
            type: 'POST',
            data: function(d) {
                d.status = $('#filterStatus').val();
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
    
    window.filterTable = function() {
        reloadDataTable('statusTable');
    };
});
</script>


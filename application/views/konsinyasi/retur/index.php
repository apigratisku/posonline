<div class="page-header">
    <h1 class="page-title">Retur Konsinyasi</h1>
    <p class="page-subtitle">Daftar retur konsinyasi</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Retur Konsinyasi</h5>
    </div>
    <div class="card-body">
        <table id="returTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>No. Konsinyasi</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
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
    var table = initDataTable('returTable', {
        ajax: {
            url: baseUrl + 'konsinyasi/retur/ajax_list',
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
});
</script>


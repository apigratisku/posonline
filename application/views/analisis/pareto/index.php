<div class="page-header"><h1 class="page-title">Analisis Pareto</h1><p class="page-subtitle">Analisis data pareto</p></div>
<div class="card">
    <div class="card-header"><h5 class="mb-0">Analisis Pareto</h5></div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3"><label class="form-label">Tanggal Mulai</label><input type="date" id="startDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Tanggal Akhir</label><input type="date" id="endDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button></div>
        </div>
        <table id="analisisparetoTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Qty Terjual</th>
                    <th>Nilai Penjualan</th>
                    <th>% Kontribusi</th>
                    <th>% Kumulatif</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
    initDataTable('analisisparetoTable', {
        ajax: { url: baseUrl + 'analisis/pareto/ajax_list', type: 'POST', data: function(d) { d.start_date = $('#startDate').val(); d.end_date = $('#endDate').val(); } },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 }
        ]
    });
});
function filterTable() { reloadDataTable('analisisparetoTable'); }
</script>

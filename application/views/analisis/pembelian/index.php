<div class="page-header"><h1 class="page-title">Analisis Pembelian</h1><p class="page-subtitle">Analisis data pembelian</p></div>
<div class="card">
    <div class="card-header"><h5 class="mb-0">Analisis Pembelian</h5></div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3"><label class="form-label">Tanggal Mulai</label><input type="date" id="startDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Tanggal Akhir</label><input type="date" id="endDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button></div>
        </div>
        <table id="analisispembelianTable" class="table table-striped table-bordered" style="width:100%"><thead><tr><th>Data</th><th>Nilai</th><th>Persentase</th></tr></thead><tbody></tbody></table>
    </div>
</div>
<script>
$(document).ready(function() {
    initDataTable('analisispembelianTable', {
        ajax: { url: baseUrl + 'analisis/pembelian/ajax_list', type: 'POST', data: function(d) { d.start_date = $('#startDate').val(); d.end_date = $('#endDate').val(); } },
        columns: [{ data: 0 }, { data: 1 }, { data: 2 }]
    });
});
function filterTable() { reloadDataTable('analisispembelianTable'); }
</script>
<div class="page-header">
    <h1 class="page-title">Laporan Penjualan</h1>
    <p class="page-subtitle">Laporan data penjualan</p>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Laporan Penjualan</h5>
        <button type="button" class="btn btn-success" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3"><label class="form-label">Tanggal Mulai</label><input type="date" id="startDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Tanggal Akhir</label><input type="date" id="endDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button></div>
        </div>
        <table id="laporanpenjualanTable" class="table table-striped table-bordered" style="width:100%">
            <thead><tr><th>ID</th><th>No. Penjualan</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Kasir</th></tr></thead><tbody></tbody></table>
    </div>
</div>
<script>
$(document).ready(function() {
    initDataTable('laporanpenjualanTable', {
        ajax: {
            url: baseUrl + 'laporan/penjualan/ajax_list', type: 'POST',
            data: function(d) { d.start_date = $('#startDate').val(); d.end_date = $('#endDate').val(); }
        },
        columns: [ { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
             ]
    });
});
function filterTable() { reloadDataTable('laporanpenjualanTable'); }
function exportData() { window.location.href = baseUrl + 'laporan/penjualan/ajax_export?start_date=' + $('#startDate').val() + '&end_date=' + $('#endDate').val(); }
</script>
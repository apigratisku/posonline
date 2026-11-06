<div class="page-header">
    <h1 class="page-title">Buku Besar</h1>
    <p class="page-subtitle">Laporan buku besar akuntansi</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Buku Besar</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">CoA</label>
                <select id="filterCoa" class="form-select">
                    <option value="">Semua CoA</option>
                    <?php foreach ($coa_list as $coa): ?>
                        <option value="<?= $coa->id ?>"><?= $coa->kode ?> - <?= $coa->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" id="filterStartDate" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" id="filterEndDate" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button>
            </div>
        </div>
        <table id="bukuBesarTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Bukti</th>
                    <th>CoA</th>
                    <th>Keterangan</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = initDataTable('bukuBesarTable', {
        ajax: {
            url: baseUrl + 'akuntansi/buku-besar/ajax_list',
            type: 'POST',
            data: function(d) {
                d.coa_id = $('#filterCoa').val();
                d.start_date = $('#filterStartDate').val();
                d.end_date = $('#filterEndDate').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 }
        ]
    });
    
    window.filterTable = function() {
        reloadDataTable('bukuBesarTable');
    };
});
</script>


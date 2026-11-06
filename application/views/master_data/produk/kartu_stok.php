<div class="page-header">
    <h1 class="page-title">Kartu Stok - <?= $produk->nama ?></h1>
    <p class="page-subtitle">Riwayat pergerakan stok produk</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Kartu Stok</h5>
        <div>
            <select id="filterGudang" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                <option value="">Semua Gudang</option>
                <?php foreach ($gudangs as $gudang): ?>
                    <option value="<?= $gudang->id ?>"><?= $gudang->nama ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="card-body">
        <table id="kartuStokTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Referensi</th>
                    <th>Tipe</th>
                    <th>Qty Masuk</th>
                    <th>Qty Keluar</th>
                    <th>Saldo</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
var produkId = <?= $produk->id ?>;
$(document).ready(function() {
    var table = initDataTable('kartuStokTable', {
        ajax: {
            url: baseUrl + 'masterdata/masterproduk/ajax_list_kartu_stok',
            type: 'POST',
            data: function(d) {
                d.produk_id = produkId;
                d.gudang_id = $('#filterGudang').val();
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
    
    $('#filterGudang').on('change', function() {
        reloadDataTable('kartuStokTable');
    });
});
</script>


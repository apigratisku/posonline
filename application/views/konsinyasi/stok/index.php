<div class="page-header">
    <h1 class="page-title">Stok Konsinyasi</h1>
    <p class="page-subtitle">Monitor stok konsinyasi</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Stok Konsinyasi</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Produk</label>
                <select id="filterProduk" class="form-select">
                    <option value="">Semua Produk</option>
                    <?php foreach ($produks as $produk): ?>
                        <option value="<?= $produk->id ?>"><?= $produk->kode ?> - <?= $produk->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Gudang</label>
                <select id="filterGudang" class="form-select">
                    <option value="">Semua Gudang</option>
                    <?php foreach ($gudangs as $gudang): ?>
                        <option value="<?= $gudang->id ?>"><?= $gudang->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button>
            </div>
        </div>
        <table id="stokTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produk</th>
                    <th>Gudang</th>
                    <th>Tanggal</th>
                    <th>Referensi</th>
                    <th>Qty Masuk</th>
                    <th>Qty Keluar</th>
                    <th>Qty Saldo</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = initDataTable('stokTable', {
        ajax: {
            url: baseUrl + 'konsinyasi/stok/ajax_list',
            type: 'POST',
            data: function(d) {
                d.produk_id = $('#filterProduk').val();
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
            { data: 6 },
            { data: 7 },
            { data: 8 }
        ]
    });
    
    window.filterTable = function() {
        reloadDataTable('stokTable');
    };
});
</script>


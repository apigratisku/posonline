<div class="page-header"><h1 class="page-title">Analisis Harga</h1><p class="page-subtitle">Analisis data harga</p></div>
<div class="card">
    <div class="card-header"><h5 class="mb-0">Analisis Harga</h5></div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Produk</label>
                <select id="produkId" class="form-select">
                    <option value="">-- Pilih Produk --</option>
                    <?php if (!empty($produk_list)): ?>
                        <?php foreach ($produk_list as $produk): ?>
                            <option value="<?= $produk->id ?>"><?= htmlspecialchars($produk->kode . ' - ' . $produk->nama) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary w-100" onclick="filterTable()">Tampilkan</button>
            </div>
        </div>
        <table id="analisishargaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Rata-rata Beli</th>
                    <th>Rata-rata Jual</th>
                    <th>Margin</th>
                    <th>Margin %</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<script>
$(document).ready(function() {
    initDataTable('analisishargaTable', {
        ajax: {
            url: baseUrl + 'analisis/harga/ajax_list',
            type: 'POST',
            data: function(d) {
                d.produk_id = $('#produkId').val();
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
            { data: 7 }
        ]
    });
});
function filterTable() {
    if (!$('#produkId').val()) {
        showNotification('warning', 'Pilih produk terlebih dahulu');
        return;
    }
    reloadDataTable('analisishargaTable');
}
</script>

<div class="page-header">
    <h1 class="page-title">Master Produk</h1>
    <p class="page-subtitle">Kelola data produk dan stok</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Produk</h5>
        <?php if (can_create('master')): ?>
            <a href="<?= base_url('masterdata/masterproduk/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Produk
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filterKategori" class="form-label">Filter Kategori</label>
                <?= form_dropdown('filterKategori', ['' => 'Semua Kategori'] + rows_to_dropdown($kategoris), '', ['id' => 'filterKategori', 'class' => 'form-select']) ?>
            </div>
            <div class="col-md-3">
                <label for="filterGudang" class="form-label">Filter Gudang</label>
                <?= form_dropdown('filterGudang', ['' => 'Semua Gudang'] + rows_to_dropdown($gudangs), '', ['id' => 'filterGudang', 'class' => 'form-select']) ?>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-secondary" id="btnResetFilter">Reset Filter</button>
            </div>
        </div>
        <table id="produkTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Barcode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Gudang</th>
                    <th>Harga Beli</th>
                    <th>% Untung</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
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
    var table = initDataTable('produkTable', {
        ajax: {
            url: baseUrl + 'masterdata/masterproduk/ajax_list',
            type: 'POST',
            data: function(d) {
                d.kategori_id = $('#filterKategori').val();
                d.gudang_id = $('#filterGudang').val();
                return d;
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
            { data: 8 },
            { data: 9 },
            { data: 10 },
            { data: 11 },
            { data: 12, orderable: false, searchable: false }
        ]
    });

    $('#filterKategori, #filterGudang').on('change', function() {
        reloadDataTable(table);
    });

    $('#btnResetFilter').on('click', function() {
        $('#filterKategori, #filterGudang').val('');
        reloadDataTable(table);
    });
});

var isAdminProduk = <?php echo !empty($is_admin) ? 'true' : 'false'; ?>;

function deleteData(id) {
    var pesan = isAdminProduk
        ? 'PERINGATAN: Anda login sebagai Admin. Menghapus produk ini akan MENGHAPUS PERMANEN semua data terkait (kartu stok, penjualan, pembelian, mutasi, stok opname, retur, dan riwayat lain). Lanjutkan?'
        : 'Apakah Anda yakin ingin menghapus data ini?';
    showConfirm(pesan, function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'masterdata/masterproduk/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('produkTable');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menghapus data');
            }
        });
    });
}

function editData(id) {
    window.location.href = baseUrl + 'masterdata/masterproduk/edit/' + id;
}

function viewData(id) {
    window.location.href = baseUrl + 'masterdata/masterproduk/view/' + id;
}

function viewKartuStok(id) {
    window.location.href = baseUrl + 'masterdata/masterproduk/kartu_stok/' + id;
}
</script>

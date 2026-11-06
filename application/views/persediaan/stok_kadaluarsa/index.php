<div class="page-header">
    <h1 class="page-title">Stok Kadaluarsa</h1>
    <p class="page-subtitle">Monitor stok produk yang akan atau sudah kadaluarsa</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Stok Kadaluarsa</h5>
    </div>
    <div class="card-body">
        <table id="stokKadaluarsaTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Produk</th>
                    <th>Gudang</th>
                    <th>Batch No</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Sisa Hari</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = initDataTable('stokKadaluarsaTable', {
        ajax: {
            url: baseUrl + 'persediaan/stok_kadaluarsa/ajax_list',
            type: 'POST'
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
});
</script>


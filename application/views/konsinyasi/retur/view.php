<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Retur Konsinyasi</h5>
        <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">ID</th>
                <td><?= $data->id ?></td>
            </tr>
            <tr>
                <th>No. Konsinyasi</th>
                <td><?= htmlspecialchars($data->no_konsinyasi) ?></td>
            </tr>
            <tr>
                <th>Supplier</th>
                <td><?= htmlspecialchars($data->supplier_nama ?: '-') ?></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td><?= date('d/m/Y', strtotime($data->tanggal)) ?></td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td><?= htmlspecialchars($data->keterangan ?: '-') ?></td>
            </tr>
        </table>
    </div>
</div>


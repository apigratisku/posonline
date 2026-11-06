<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Akun Kas</h5>
        <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">ID</th>
                <td><?= $data->id ?></td>
            </tr>
            <tr>
                <th>Kode</th>
                <td><?= htmlspecialchars($data->kode) ?></td>
            </tr>
            <tr>
                <th>Nama</th>
                <td><?= htmlspecialchars($data->nama) ?></td>
            </tr>
            <tr>
                <th>Tipe</th>
                <td>
                    <span class="badge bg-<?= $data->tipe == 'kas' ? 'primary' : 'info' ?>">
                        <?= strtoupper($data->tipe) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Saldo Awal</th>
                <td>Rp <?= number_format($data->saldo_awal, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <th>Saldo Saat Ini</th>
                <td><strong>Rp <?= number_format($data->saldo, 0, ',', '.') ?></strong></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php if ($data->is_active): ?>
                        <span class="badge bg-success">Aktif</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Tidak Aktif</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Tanggal Dibuat</th>
                <td><?= date('d/m/Y H:i', strtotime($data->created_at)) ?></td>
            </tr>
            <?php if ($data->updated_at): ?>
            <tr>
                <th>Tanggal Diupdate</th>
                <td><?= date('d/m/Y H:i', strtotime($data->updated_at)) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>


<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail CoA</h5>
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
                    <?php
                    $tipe_badge = 'secondary';
                    switch($data->tipe) {
                        case 'aktiva': $tipe_badge = 'primary'; break;
                        case 'pasiva': $tipe_badge = 'warning'; break;
                        case 'modal': $tipe_badge = 'success'; break;
                        case 'pendapatan': $tipe_badge = 'info'; break;
                        case 'beban': $tipe_badge = 'danger'; break;
                    }
                    ?>
                    <span class="badge bg-<?= $tipe_badge ?>"><?= ucfirst($data->tipe ?: '-') ?></span>
                </td>
            </tr>
            <tr>
                <th>Parent CoA</th>
                <td><?= htmlspecialchars($data->parent_id ? 'ID: ' . $data->parent_id : '-') ?></td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td><?= htmlspecialchars($data->deskripsi ?: '-') ?></td>
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


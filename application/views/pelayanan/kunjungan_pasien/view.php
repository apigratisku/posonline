<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Kunjungan Pasien</h5>
        <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">No Kunjungan</th>
                <td><?= htmlspecialchars($data->no_kunjungan) ?></td>
            </tr>
            <tr>
                <th>Pelanggan</th>
                <td><?= htmlspecialchars($data->pelanggan_nama) ?></td>
            </tr>
            <tr>
                <th>Dokter</th>
                <td><?= isset($data->dokter_nama) ? htmlspecialchars($data->dokter_nama) : '-' ?></td>
            </tr>
            <tr>
                <th>Jenis Pelayanan</th>
                <td><?= isset($data->jenis_pelayanan_nama) ? htmlspecialchars($data->jenis_pelayanan_nama) : '-' ?></td>
            </tr>
            <tr>
                <th>Jenis Antrian</th>
                <td><?= isset($data->jenis_antrian_nama) ? htmlspecialchars($data->jenis_antrian_nama) : '-' ?></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td><?= date('d/m/Y H:i', strtotime($data->tanggal)) ?></td>
            </tr>
            <tr>
                <th>Keluhan</th>
                <td><?= htmlspecialchars($data->keluhan ?: '-') ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php
                    $status_class = 'secondary';
                    if ($data->status == 'completed') $status_class = 'success';
                    elseif ($data->status == 'process') $status_class = 'warning';
                    elseif ($data->status == 'cancelled') $status_class = 'danger';
                    ?>
                    <span class="badge bg-<?= $status_class ?>">
                        <?= strtoupper($data->status) ?>
                    </span>
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


<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Presensi</h5>
        <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">ID</th>
                <td><?= $data->id ?></td>
            </tr>
            <tr>
                <th>Karyawan</th>
                <td><?= isset($data->nama_lengkap) ? htmlspecialchars($data->nama_lengkap) : '-' ?></td>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td><?= $data->tanggal ? date('d/m/Y', strtotime($data->tanggal)) : '-' ?></td>
            </tr>
            <tr>
                <th>Shift</th>
                <td><?= isset($data->shift_nama) ? htmlspecialchars($data->shift_nama) : '-' ?></td>
            </tr>
            <tr>
                <th>Jam Masuk</th>
                <td><?= $data->jam_masuk ? date('H:i', strtotime($data->jam_masuk)) : '-' ?></td>
            </tr>
            <tr>
                <th>Jam Keluar</th>
                <td><?= $data->jam_keluar ? date('H:i', strtotime($data->jam_keluar)) : '-' ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php
                    $status_class = 'secondary';
                    if ($data->status == 'hadir') $status_class = 'success';
                    elseif ($data->status == 'terlambat') $status_class = 'warning';
                    elseif ($data->status == 'tidak_hadir') $status_class = 'danger';
                    ?>
                    <span class="badge bg-<?= $status_class ?>">
                        <?= ucfirst(str_replace('_', ' ', $data->status)) ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td><?= htmlspecialchars($data->keterangan ?: '-') ?></td>
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


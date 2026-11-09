<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Peran</h5>
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
        
        <?php if (!empty($permissions)): ?>
        <div class="mt-4">
            <h5>Hak Akses</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Modul</th>
                        <th>View</th>
                        <th>Create</th>
                        <th>Update</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modul_list as $modul): ?>
                        <?php
                        $perm = null;
                        foreach ($permissions as $p) {
                            if ($p->modul_id == $modul->id) {
                                $perm = $p;
                                break;
                            }
                        }
                        $checks = array(
                            'can_view' => 'Lihat',
                            'can_create' => 'Tambah',
                            'can_update' => 'Ubah',
                            'can_delete' => 'Hapus'
                        );
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($modul->nama) ?></td>
                            <?php foreach ($checks as $key => $label): ?>
                                <td class="text-center">
                                    <?php if ($perm && $perm->$key): ?>
                                        <i class="fas fa-check-circle text-success fa-lg" title="Boleh <?= $label ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-muted" title="Tidak boleh <?= $label ?>"></i>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>


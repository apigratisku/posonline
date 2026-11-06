<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Jadwal Kerja</h5>
        <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered mb-4">
            <tr>
                <th width="200">Karyawan</th>
                <td><?= htmlspecialchars($user->nama_lengkap) ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($user->username) ?></td>
            </tr>
        </table>
        
        <h5>Jadwal Mingguan</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Hari</th>
                    <th>Shift</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $hari_list = array(
                    'senin' => 'Senin',
                    'selasa' => 'Selasa',
                    'rabu' => 'Rabu',
                    'kamis' => 'Kamis',
                    'jumat' => 'Jumat',
                    'sabtu' => 'Sabtu',
                    'minggu' => 'Minggu'
                );
                foreach ($hari_list as $key => $label): 
                    $jadwal_hari = null;
                    foreach ($jadwal as $j) {
                        if ($j->hari == $key) {
                            $jadwal_hari = $j;
                            break;
                        }
                    }
                ?>
                <tr>
                    <td><strong><?= $label ?></strong></td>
                    <td><?= $jadwal_hari && $jadwal_hari->shift_nama ? htmlspecialchars($jadwal_hari->shift_nama) . ' (' . $jadwal_hari->jam_mulai . ' - ' . $jadwal_hari->jam_selesai . ')' : '-' ?></td>
                    <td>
                        <?php if ($jadwal_hari && $jadwal_hari->is_active == 1): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


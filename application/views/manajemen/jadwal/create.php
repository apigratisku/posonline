<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'jadwalForm']) ?>
            <div class="mb-3">
                <label for="user_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                <?= form_dropdown('user_id', ['' => 'Pilih Karyawan'] + array_column($users, 'nama_lengkap', 'id'), set_value('user_id'), ['class' => 'form-select' . (form_error('user_id') ? ' is-invalid' : ''), 'id' => 'user_id', 'required' => true]) ?>
                <?= form_error('user_id', '<div class="invalid-feedback">', '</div>') ?>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Shift</th>
                            <th>Aktif</th>
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
                        foreach ($hari_list as $key => $label): ?>
                        <tr>
                            <td><strong><?= $label ?></strong></td>
                            <td>
                                <?= form_dropdown('schedule[' . $key . '][shift_id]', ['' => 'Tidak Ada Shift'] + array_column($shifts, function($s) { return $s->nama . ' (' . $s->jam_mulai . ' - ' . $s->jam_selesai . ')'; }, 'id'), set_value('schedule[' . $key . '][shift_id]'), ['class' => 'form-select', 'id' => 'shift_' . $key]) ?>
                            </td>
                            <td>
                                <div class="form-check">
                                    <?= form_checkbox(['name' => 'schedule[' . $key . '][is_active]', 'id' => 'active_' . $key, 'class' => 'form-check-input', 'value' => 1, 'checked' => set_checkbox('schedule[' . $key . '][is_active]', 1, true)]) ?>
                                    <label class="form-check-label" for="active_<?= $key ?>">
                                        Aktif
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>


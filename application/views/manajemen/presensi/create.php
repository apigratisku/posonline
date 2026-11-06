<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'presensiForm']) ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <?= form_dropdown('user_id', ['' => 'Pilih Karyawan'] + array_column($users, 'nama_lengkap', 'id'), set_value('user_id'), ['class' => 'form-select' . (form_error('user_id') ? ' is-invalid' : ''), 'id' => 'user_id', 'required' => true]) ?>
                        <?= form_error('user_id', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <?= form_input(['type' => 'date', 'name' => 'tanggal', 'id' => 'tanggal', 'class' => 'form-control' . (form_error('tanggal') ? ' is-invalid' : ''), 'value' => set_value('tanggal', date('Y-m-d')), 'required' => true]) ?>
                        <?= form_error('tanggal', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="shift_id" class="form-label">Shift</label>
                        <?= form_dropdown('shift_id', ['' => 'Pilih Shift'] + array_column($shifts, function($s) { return $s->nama . ' (' . $s->jam_mulai . ' - ' . $s->jam_selesai . ')'; }, 'id'), set_value('shift_id'), ['class' => 'form-select', 'id' => 'shift_id']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <?= form_dropdown('status', ['tidak_hadir' => 'Tidak Hadir', 'hadir' => 'Hadir', 'terlambat' => 'Terlambat'], set_value('status', 'tidak_hadir'), ['class' => 'form-select', 'id' => 'status']) ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jam_masuk" class="form-label">Jam Masuk</label>
                        <?= form_input(['type' => 'time', 'name' => 'jam_masuk', 'id' => 'jam_masuk', 'class' => 'form-control', 'value' => set_value('jam_masuk')]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jam_keluar" class="form-label">Jam Keluar</label>
                        <?= form_input(['type' => 'time', 'name' => 'jam_keluar', 'id' => 'jam_keluar', 'class' => 'form-control', 'value' => set_value('jam_keluar')]) ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <?= form_textarea(['name' => 'keterangan', 'id' => 'keterangan', 'class' => 'form-control', 'rows' => 3, 'value' => set_value('keterangan')]) ?>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>


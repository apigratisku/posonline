<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'shiftForm']) ?>
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
                        <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                        <?= form_dropdown('shift_id', ['' => 'Pilih Shift'] + array_column($shifts, 'nama', 'id'), set_value('shift_id'), ['class' => 'form-select' . (form_error('shift_id') ? ' is-invalid' : ''), 'id' => 'shift_id', 'required' => true]) ?>
                        <?= form_error('shift_id', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
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
                        <label for="jam_mulai" class="form-label">Jam Mulai</label>
                        <?= form_input(['type' => 'time', 'name' => 'jam_mulai', 'id' => 'jam_mulai', 'class' => 'form-control', 'value' => set_value('jam_mulai')]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai</label>
                        <?= form_input(['type' => 'time', 'name' => 'jam_selesai', 'id' => 'jam_selesai', 'class' => 'form-control', 'value' => set_value('jam_selesai')]) ?>
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


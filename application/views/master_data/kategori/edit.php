<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'kategoriForm']) ?>
            <div class="mb-3">
                <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'kode', 'id' => 'kode', 'class' => 'form-control' . (form_error('kode') ? ' is-invalid' : ''), 'value' => set_value('kode', $data->kode), 'required' => true]) ?>
                <?= form_error('kode', '<div class="invalid-feedback">', '</div>') ?>
            </div>
            
            <div class="mb-3">
                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'nama', 'id' => 'nama', 'class' => 'form-control' . (form_error('nama') ? ' is-invalid' : ''), 'value' => set_value('nama', $data->nama), 'required' => true]) ?>
                <?= form_error('nama', '<div class="invalid-feedback">', '</div>') ?>
            </div>
            
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <?= form_textarea(['name' => 'deskripsi', 'id' => 'deskripsi', 'class' => 'form-control', 'rows' => 3, 'value' => set_value('deskripsi', $data->deskripsi)]) ?>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <?= form_checkbox(['name' => 'is_active', 'id' => 'is_active', 'class' => 'form-check-input', 'value' => 1, 'checked' => set_checkbox('is_active', 1, $data->is_active == 1)]) ?>
                    <label class="form-check-label" for="is_active">
                        Aktif
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>


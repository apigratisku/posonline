<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'aktivaForm']) ?>
            <div class="mb-3">
                <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'kode', 'id' => 'kode', 'class' => 'form-control' . (form_error('kode') ? ' is-invalid' : ''), 'value' => set_value('kode'), 'required' => true]) ?>
                <?= form_error('kode', '<div class="invalid-feedback">', '</div>') ?>
            </div>
            
            <div class="mb-3">
                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'nama', 'id' => 'nama', 'class' => 'form-control' . (form_error('nama') ? ' is-invalid' : ''), 'value' => set_value('nama'), 'required' => true]) ?>
                <?= form_error('nama', '<div class="invalid-feedback">', '</div>') ?>
            </div>
            
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <?= form_input(['name' => 'kategori', 'id' => 'kategori', 'class' => 'form-control', 'value' => set_value('kategori')]) ?>
            </div>
            
            <div class="mb-3">
                <label for="tanggal_beli" class="form-label">Tanggal Beli <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'tanggal_beli', 'id' => 'tanggal_beli', 'type' => 'date', 'class' => 'form-control' . (form_error('tanggal_beli') ? ' is-invalid' : ''), 'value' => set_value('tanggal_beli'), 'required' => true]) ?>
                <?= form_error('tanggal_beli', '<div class="invalid-feedback">', '</div>') ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli <span class="text-danger">*</span></label>
                        <?= form_input(['name' => 'harga_beli', 'id' => 'harga_beli', 'type' => 'number', 'step' => '0.01', 'class' => 'form-control' . (form_error('harga_beli') ? ' is-invalid' : ''), 'value' => set_value('harga_beli'), 'required' => true]) ?>
                        <?= form_error('harga_beli', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nilai_residu" class="form-label">Nilai Residu</label>
                        <?= form_input(['name' => 'nilai_residu', 'id' => 'nilai_residu', 'type' => 'number', 'step' => '0.01', 'class' => 'form-control', 'value' => set_value('nilai_residu', 0)]) ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="umur_ekonomis" class="form-label">Umur Ekonomis (Tahun)</label>
                        <?= form_input(['name' => 'umur_ekonomis', 'id' => 'umur_ekonomis', 'type' => 'number', 'class' => 'form-control', 'value' => set_value('umur_ekonomis', 0)]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="metode_depresiasi" class="form-label">Metode Depresiasi</label>
                        <?= form_dropdown('metode_depresiasi', [
                            'garis_lurus' => 'Garis Lurus',
                            'saldo_menurun' => 'Saldo Menurun'
                        ], set_value('metode_depresiasi', 'garis_lurus'), ['id' => 'metode_depresiasi', 'class' => 'form-select']) ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <?= form_textarea(['name' => 'deskripsi', 'id' => 'deskripsi', 'class' => 'form-control', 'rows' => 3, 'value' => set_value('deskripsi')]) ?>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <?= form_checkbox(['name' => 'is_active', 'id' => 'is_active', 'class' => 'form-check-input', 'value' => 1, 'checked' => set_checkbox('is_active', 1, true)]) ?>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>


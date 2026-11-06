<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'pelangganForm']) ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                        <?= form_input(['name' => 'kode', 'id' => 'kode', 'class' => 'form-control' . (form_error('kode') ? ' is-invalid' : ''), 'value' => set_value('kode'), 'required' => true]) ?>
                        <?= form_error('kode', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                        <?= form_input(['name' => 'nama', 'id' => 'nama', 'class' => 'form-control' . (form_error('nama') ? ' is-invalid' : ''), 'value' => set_value('nama'), 'required' => true]) ?>
                        <?= form_error('nama', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <?= form_input(['name' => 'telepon', 'id' => 'telepon', 'class' => 'form-control', 'value' => set_value('telepon')]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <?= form_input(['type' => 'email', 'name' => 'email', 'id' => 'email', 'class' => 'form-control', 'value' => set_value('email')]) ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <?= form_textarea(['name' => 'alamat', 'id' => 'alamat', 'class' => 'form-control', 'rows' => 3, 'value' => set_value('alamat')]) ?>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <?= form_input(['type' => 'date', 'name' => 'tanggal_lahir', 'id' => 'tanggal_lahir', 'class' => 'form-control', 'value' => set_value('tanggal_lahir')]) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <?= form_dropdown('jenis_kelamin', ['' => 'Pilih', 'L' => 'Laki-laki', 'P' => 'Perempuan'], set_value('jenis_kelamin'), ['class' => 'form-select', 'id' => 'jenis_kelamin']) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="membership_id" class="form-label">Membership</label>
                        <?= form_dropdown('membership_id', ['' => 'Pilih Membership'] + array_column($memberships, 'nama', 'id'), set_value('membership_id'), ['class' => 'form-select', 'id' => 'membership_id']) ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="poin" class="form-label">Poin</label>
                        <?= form_input(['type' => 'number', 'name' => 'poin', 'id' => 'poin', 'class' => 'form-control', 'value' => set_value('poin', 0)]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check mt-4">
                            <?= form_checkbox(['name' => 'is_active', 'id' => 'is_active', 'class' => 'form-check-input', 'value' => 1, 'checked' => set_checkbox('is_active', 1, true)]) ?>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>


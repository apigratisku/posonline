<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'penggunaForm']) ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                        <?= form_input(['name' => 'username', 'id' => 'username', 'class' => 'form-control' . (form_error('username') ? ' is-invalid' : ''), 'value' => set_value('username', $data->username), 'required' => true] + (strtolower($data->username) === 'demo' ? ['readonly' => 'readonly'] : [])) ?>
                        <?= form_error('username', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <?= form_input(['name' => 'nama_lengkap', 'id' => 'nama_lengkap', 'class' => 'form-control' . (form_error('nama_lengkap') ? ' is-invalid' : ''), 'value' => set_value('nama_lengkap', $data->nama_lengkap), 'required' => true]) ?>
                        <?= form_error('nama_lengkap', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <?= form_input(['type' => 'email', 'name' => 'email', 'id' => 'email', 'class' => 'form-control' . (form_error('email') ? ' is-invalid' : ''), 'value' => set_value('email', $data->email), 'required' => true]) ?>
                        <?= form_error('email', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <?= form_input(['name' => 'telepon', 'id' => 'telepon', 'class' => 'form-control', 'value' => set_value('telepon', $data->telepon)]) ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <?= form_textarea(['name' => 'alamat', 'id' => 'alamat', 'class' => 'form-control', 'rows' => 3, 'value' => set_value('alamat', $data->alamat)]) ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="peran_id" class="form-label">Peran</label>
                        <?= form_dropdown('peran_id', ['' => 'Pilih Peran'] + array_column($peran_list, 'nama', 'id'), set_value('peran_id', $data->peran_id), ['class' => 'form-select', 'id' => 'peran_id']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="gudang_id" class="form-label">Gudang Transaksi POS</label>
                        <?= form_dropdown('gudang_id', ['' => 'Pilih Gudang'] + array_column($gudang_list, 'nama', 'id'), set_value('gudang_id', $data->gudang_id), ['class' => 'form-select', 'id' => 'gudang_id']) ?>
                        <?= form_error('gudang_id', '<div class="invalid-feedback d-block">', '</div>') ?>
                        <small class="form-text text-muted">Wajib untuk peran Kasir. Menentukan lokasi gudang stok saat transaksi POS.</small>
                    </div>
                </div>
            </div>

            <?php if (strtolower($data->username) === 'demo'): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i> Akun demo dilindungi. Password dan username tidak dapat diubah.
            </div>
            <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <?= form_password(['name' => 'password', 'id' => 'password', 'class' => 'form-control' . (form_error('password') ? ' is-invalid' : ''), 'minlength' => 6]) ?>
                        <?= form_error('password', '<div class="invalid-feedback">', '</div>') ?>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
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


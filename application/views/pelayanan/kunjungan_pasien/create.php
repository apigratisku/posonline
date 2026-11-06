<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'kunjunganForm']) ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pelanggan_id" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                        <?= form_dropdown('pelanggan_id', ['' => 'Pilih Pelanggan'] + array_column($pelanggan_list, 'nama', 'id'), set_value('pelanggan_id'), ['class' => 'form-select' . (form_error('pelanggan_id') ? ' is-invalid' : ''), 'id' => 'pelanggan_id', 'required' => true]) ?>
                        <?= form_error('pelanggan_id', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <?= form_input(['type' => 'datetime-local', 'name' => 'tanggal', 'id' => 'tanggal', 'class' => 'form-control' . (form_error('tanggal') ? ' is-invalid' : ''), 'value' => set_value('tanggal', date('Y-m-d\TH:i')), 'required' => true]) ?>
                        <?= form_error('tanggal', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="dokter_id" class="form-label">Dokter</label>
                        <?= form_dropdown('dokter_id', ['' => 'Pilih Dokter'] + array_column($dokter_list, 'nama', 'id'), set_value('dokter_id'), ['class' => 'form-select', 'id' => 'dokter_id']) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <?= form_dropdown('status', ['pending' => 'Pending', 'process' => 'Process', 'completed' => 'Completed', 'cancelled' => 'Cancelled'], set_value('status', 'pending'), ['class' => 'form-select', 'id' => 'status']) ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="keluhan" class="form-label">Keluhan</label>
                <?= form_textarea(['name' => 'keluhan', 'id' => 'keluhan', 'class' => 'form-control', 'rows' => 4, 'value' => set_value('keluhan')]) ?>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>


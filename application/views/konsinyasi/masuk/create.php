<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'masukForm']) ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <?= form_dropdown('supplier_id', ['' => 'Pilih Supplier'] + array_column($suppliers, 'nama', 'id'), set_value('supplier_id'), ['id' => 'supplier_id', 'class' => 'form-select' . (form_error('supplier_id') ? ' is-invalid' : ''), 'required' => true]) ?>
                        <?= form_error('supplier_id', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="gudang_id" class="form-label">Gudang <span class="text-danger">*</span></label>
                        <?= form_dropdown('gudang_id', ['' => 'Pilih Gudang'] + array_column($gudangs, 'nama', 'id'), set_value('gudang_id'), ['id' => 'gudang_id', 'class' => 'form-select' . (form_error('gudang_id') ? ' is-invalid' : ''), 'required' => true]) ?>
                        <?= form_error('gudang_id', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'tanggal', 'id' => 'tanggal', 'type' => 'date', 'class' => 'form-control' . (form_error('tanggal') ? ' is-invalid' : ''), 'value' => set_value('tanggal', date('Y-m-d')), 'required' => true]) ?>
                <?= form_error('tanggal', '<div class="invalid-feedback">', '</div>') ?>
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


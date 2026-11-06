<?php
$isEdit = isset($data) && !empty($data->id);
$val = function($field) use ($isEdit) {
    return $isEdit ? ($data->$field ?? '') : set_value($field);
};
?>
<div class="page-header"><h1 class="page-title"><?= $title ?></h1></div>
<div class="card">
    <div class="card-header"><h5 class="mb-0"><?= $form_title ?></h5></div>
    <div class="card-body">
        <?= form_open($form_action) ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Kode <span class="text-danger">*</span></label>
                <input type="text" name="kode" class="form-control" value="<?= htmlspecialchars($val('kode')) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($val('nama')) ?>" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($val('deskripsi')) ?></textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?= htmlspecialchars($val('tanggal_mulai')) ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" value="<?= htmlspecialchars($val('tanggal_selesai')) ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Harga/Nilai</label>
                <input type="number" name="harga_bundel" class="form-control" value="<?= htmlspecialchars($val('harga_bundel') ?: $val('nilai') ?: $val('poin')) ?>">
            </div>
            <div class="col-md-12 mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" <?= ($isEdit ? ($data->is_active ?? 0) : 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
        <?= form_close() ?>
    </div>
</div>

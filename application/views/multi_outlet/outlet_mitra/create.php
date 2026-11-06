<div class="page-header"><h1 class="page-title"><?= $title ?></h1></div>
<div class="card"><div class="card-header"><h5 class="mb-0"><?= $form_title ?></h5></div><div class="card-body">
<?= form_open($form_action) ?>
<div class="mb-3"><label class="form-label">Kode *</label><input name="kode" class="form-control" value="<?= set_value('kode') ?>" required></div>
<div class="mb-3"><label class="form-label">Nama *</label><input name="nama" class="form-control" value="<?= set_value('nama') ?>" required></div>
<div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"><?= set_value('alamat') ?></textarea></div>
<div class="row"><div class="col-md-6 mb-3"><label class="form-label">Telepon</label><input name="telepon" class="form-control" value="<?= set_value('telepon') ?>"></div>
<div class="col-md-6 mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" value="<?= set_value('email') ?>"></div></div>
<div class="mb-3 form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" checked> <label class="form-check-label">Aktif</label></div>
<div class="d-flex justify-content-end gap-2"><a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary">Simpan</button></div>
<?= form_close() ?></div></div>

<div class="page-header"><h1 class="page-title">Konfigurasi Sistem</h1><p class="page-subtitle">Pengaturan parameter sistem</p></div>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('pengaturan/konfigurasi/update') ?>">
    <?php if (!empty($configs)): foreach ($configs as $cfg): ?>
    <div class="mb-3"><label class="form-label"><?= htmlspecialchars($cfg->key) ?></label><input type="text" name="config[<?= htmlspecialchars($cfg->key) ?>]" class="form-control" value="<?= htmlspecialchars($cfg->value ?? '') ?>"></div>
    <?php endforeach; else: ?><p class="text-muted">Belum ada konfigurasi.</p><?php endif; ?>
    <?php if (can_update('pengaturan')): ?><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><?php endif; ?>
</form>
</div></div>
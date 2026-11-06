<div class="page-header">
    <h1 class="page-title">Profil Toko</h1>
    <p class="page-subtitle">Kelola logo, alamat, dan informasi kontak toko</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= base_url('pengaturan/profil/update') ?>" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <label class="form-label">Logo Perusahaan</label>
                    <div class="border rounded p-3 text-center bg-light">
                        <?php if (!empty($data->logo) && is_file(FCPATH . 'assets/uploads/logo/' . $data->logo)): ?>
                            <img src="<?= base_url('assets/uploads/logo/' . $data->logo) ?>" alt="Logo" class="img-fluid mb-3 brand-logo-preview">
                            <p class="text-muted small mb-2">Logo saat ini</p>
                        <?php else: ?>
                            <div class="text-muted mb-3 py-4">
                                <i class="fas fa-image fa-3x mb-2 d-block"></i>
                                <span>Belum ada logo</span>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/gif,image/jpeg,image/png">
                        <small class="text-muted d-block mt-2">JPG, PNG, GIF. Maks. 2MB</small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Toko <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data->nama ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($data->telepon ?? '') ?>" maxlength="30" placeholder="Contoh: 021-1234567">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" maxlength="500" placeholder="Alamat lengkap toko"><?= htmlspecialchars($data->alamat ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data->email ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">NPWP</label>
                            <input type="text" name="npwp" class="form-control" value="<?= htmlspecialchars($data->npwp ?? '') ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">SIUP</label>
                            <input type="text" name="siup" class="form-control" value="<?= htmlspecialchars($data->siup ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php if (can_update('pengaturan')): ?>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>

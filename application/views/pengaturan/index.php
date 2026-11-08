<div class="page-header"><h1 class="page-title">Pengaturan</h1><p class="page-subtitle">Konfigurasi sistem toko</p></div>
<div class="row">
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-store fa-2x mb-3 text-primary"></i><h5>Profil Toko</h5><a href="<?= base_url('pengaturan/profil') ?>" class="btn btn-primary btn-sm">Buka</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-credit-card fa-2x mb-3 text-primary"></i><h5>Metode Pembayaran</h5><a href="<?= base_url('pengaturan/metode-pembayaran') ?>" class="btn btn-primary btn-sm">Buka</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-sliders-h fa-2x mb-3 text-primary"></i><h5>Konfigurasi</h5><a href="<?= base_url('pengaturan/konfigurasi') ?>" class="btn btn-primary btn-sm">Buka</a></div></div></div>
    <?php if ($this->session->userdata('peran_kode') === 'admin'): ?>
    <div class="col-md-4 mb-3"><div class="card h-100 border-danger"><div class="card-body text-center"><i class="fas fa-triangle-exclamation fa-2x mb-3 text-danger"></i><h5>Reset Data</h5><a href="<?= base_url('pengaturan/reset-data') ?>" class="btn btn-danger btn-sm">Buka</a></div></div></div>
    <?php endif; ?>
</div>
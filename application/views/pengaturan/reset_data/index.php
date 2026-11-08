<div class="page-header">
    <h1 class="page-title">Reset Data</h1>
    <p class="page-subtitle">Hapus data penjualan, stok, produk, pembelian, dan keuangan</p>
</div>

<div class="alert alert-danger d-flex gap-2" role="alert">
    <i class="fas fa-triangle-exclamation fa-lg mt-1"></i>
    <div>
        <strong>Zona berbahaya.</strong>
        Semua data pada tabel di bawah akan dihapus permanen dan tidak dapat dikembalikan.
        Pastikan Anda sudah memiliki backup database sebelum melanjutkan.
        Konfigurasi toko dan data master tidak ikut terhapus.
    </div>
</div>

<form id="resetDataForm" method="post" action="<?= base_url('pengaturan/reset-data/execute') ?>">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <div class="row">
        <?php foreach ($groups as $key => $group): ?>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-database me-1"></i> <?= htmlspecialchars($group['label']) ?></span>
                    <span class="badge bg-danger"><?= number_format($group['total'], 0, ',', '.') ?> baris</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small"><?= htmlspecialchars($group['description']) ?></p>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach ($group['tables'] as $table): ?>
                        <li class="d-flex justify-content-between border-bottom py-1">
                            <code><?= htmlspecialchars($table['name']) ?></code>
                            <?php if ($table['rows'] === null): ?>
                                <span class="text-warning">tidak ditemukan</span>
                            <?php else: ?>
                                <span class="text-muted"><?= number_format($table['rows'], 0, ',', '.') ?></span>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-lock me-1"></i> Tidak ikut direset</div>
        <div class="card-body">
            <?php foreach ($protected_tables as $table): ?>
                <span class="badge bg-light text-dark border me-1 mb-1"><?= htmlspecialchars($table) ?></span>
            <?php endforeach; ?>
            <p class="text-muted small mb-0 mt-2">Konfigurasi toko, pengguna, hak akses, dan seluruh data master tetap utuh.</p>
        </div>
    </div>

    <div class="card border-danger">
        <div class="card-body">
            <label for="konfirmasi" class="form-label">
                Ketik <code>RESET</code> untuk mengonfirmasi tindakan ini
            </label>
            <input type="text" id="konfirmasi" name="konfirmasi" class="form-control"
                   autocomplete="off" autocapitalize="characters" placeholder="RESET">
            <button type="submit" class="btn btn-danger mt-3">
                <i class="fas fa-trash-can me-1"></i> Reset Semua Data
            </button>
        </div>
    </div>
</form>

<script>
(function () {
    var form = document.getElementById('resetDataForm');
    var input = document.getElementById('konfirmasi');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (input.value.trim().toUpperCase() !== 'RESET') {
            showNotification('error', 'Ketik RESET (huruf besar) untuk melanjutkan.');
            input.focus();
            return;
        }

        showConfirm('Semua data penjualan, stok, produk, pembelian, dan keuangan akan dihapus permanen. Lanjutkan?', function () {
            showLoading();
            form.submit();
        }, 'Konfirmasi Reset Data');
    });
})();
</script>

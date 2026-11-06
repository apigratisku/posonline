<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$shot_dir = FCPATH . 'assets/uploads/tutorial/';
?>
<div class="page-header">
    <h1 class="page-title">Cara Penggunaan Aplikasi</h1>
    <p class="page-subtitle">Panduan lengkap untuk pemula &mdash; dari login sampai membuat laporan</p>
</div>

<div class="card mb-4">
    <div class="card-body d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div>
            <h5 class="mb-1"><i class="fas fa-book-open text-primary me-2"></i>Panduan 20 Langkah</h5>
            <p class="text-muted mb-0 small">
                Panduan ini disusun khusus untuk pengguna awam. Tidak ada istilah teknis &mdash;
                cukup ikuti urutan langkah berikut.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Cetak
            </button>
            <a href="<?= base_url('tutorial/export_pdf') ?>" class="btn btn-primary">
                <i class="fas fa-file-pdf me-1"></i> Export ke PDF
            </a>
        </div>
    </div>
</div>

<div class="card mb-4 border-primary">
    <div class="card-body">
        <h5 class="mb-2"><i class="fas fa-key text-primary me-2"></i>Akun Percobaan (Demo)</h5>
        <p class="mb-1">Nama Pengguna : <strong>demo</strong></p>
        <p class="mb-0">Kata Sandi : <strong>demo123</strong></p>
    </div>
</div>

<div class="row">
    <?php foreach ($steps as $step): ?>
    <div class="col-12 mb-4">
        <div class="card h-100 tutorial-step">
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="tutorial-badge me-3"><?= $step['no'] ?></div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1"><?= htmlspecialchars($step['judul']) ?></h5>
                        <p class="text-muted mb-0 small"><?= htmlspecialchars($step['ringkas']) ?></p>
                    </div>
                    <?php if (!empty($step['url'])): ?>
                    <a href="<?= $step['url'] ?>" class="btn btn-sm btn-outline-secondary ms-2 d-none d-md-inline-block" target="_blank" rel="noopener">
                        <i class="fas fa-external-link-alt me-1"></i>Buka
                    </a>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-lg-7">
                        <ol class="tutorial-langkah mb-3">
                            <?php foreach ($step['langkah'] as $poin): ?>
                            <li><?= $poin ?></li>
                            <?php endforeach; ?>
                        </ol>
                        <?php if (!empty($step['tips'])): ?>
                        <div class="alert alert-info py-2 px-3 mb-0 small">
                            <i class="fas fa-lightbulb me-1"></i><strong>Tips:</strong> <?= htmlspecialchars($step['tips']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-5">
                        <?php
                        $file = basename($step['screenshot']);
                        $exists = is_file($shot_dir . $file);
                        ?>
                        <?php if ($exists): ?>
                        <a href="<?= $step['screenshot'] ?>" target="_blank" rel="noopener" title="Klik untuk memperbesar">
                            <img src="<?= $step['screenshot'] ?>" alt="Tampilan <?= htmlspecialchars($step['judul']) ?>" class="img-fluid rounded border tutorial-shot">
                        </a>
                        <div class="text-muted small text-center mt-1">Gambar: <?= htmlspecialchars($step['judul']) ?></div>
                        <?php else: ?>
                        <div class="tutorial-shot-missing text-center p-4">
                            <i class="fas fa-image fa-2x text-muted mb-2"></i>
                            <div class="small text-muted">Screenshot halaman ini belum tersedia.</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card mb-4">
    <div class="card-body text-center py-4">
        <h5 class="mb-2">Sudah selesai membaca?</h5>
        <p class="text-muted mb-3">Unduh panduan ini dalam bentuk PDF agar bisa dibaca kapan saja, bahkan tanpa internet.</p>
        <a href="<?= base_url('tutorial/export_pdf') ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-file-pdf me-2"></i>Export Tutorial ke PDF
        </a>
    </div>
</div>

<style>
.tutorial-badge {
    width: 44px; height: 44px; flex: 0 0 44px;
    border-radius: 50%;
    background: var(--bs-primary, #0d6efd);
    color: #fff;
    font-weight: 700; font-size: 1.1rem;
    display: flex; align-items: center; justify-content: center;
}
.tutorial-langkah { padding-left: 1.25rem; }
.tutorial-langkah li { margin-bottom: .35rem; }
.tutorial-shot { border: 1px solid #dee2e6; }
.tutorial-shot-missing {
    border: 2px dashed #dee2e6; border-radius: .5rem;
    background: #f8f9fa;
}
@media print {
    .sidebar, .main-content > .header, .mobile-bottom-nav, .mobile-menu-sheet,
    .page-header, .card .btn, .btn { display: none !important; }
}
</style>

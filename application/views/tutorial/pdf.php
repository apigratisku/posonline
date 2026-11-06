<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$toko_nama = isset($profil_toko) && !empty($profil_toko->nama) ? $profil_toko->nama : 'Toko POS';
$printed_by_name = isset($printed_by) && is_object($printed_by) && !empty($printed_by->nama) ? $printed_by->nama : (isset($printed_by->username) ? $printed_by->username : 'Pengguna');
$shot_dir = FCPATH . 'assets/uploads/tutorial/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Cara Penggunaan Aplikasi &mdash; <?= htmlspecialchars($toko_nama) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #fff; font-size: 12.5px; color: #212529; }
        .pdf-header { border-bottom: 3px solid #0d6efd; padding-bottom: 12px; margin-bottom: 20px; }
        .pdf-header h1 { font-size: 22px; font-weight: 700; margin: 0; }
        .pdf-meta { font-size: 11px; color: #6c757d; }
        .credential-box {
            border: 1px solid #0d6efd; background: #f8f9ff;
            border-radius: 6px; padding: 10px 14px; margin-bottom: 18px;
        }
        .step { page-break-inside: avoid; break-inside: avoid; border: 1px solid #dee2e6; border-radius: 6px; padding: 12px 14px; margin-bottom: 14px; }
        .step-head { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .step-no {
            width: 30px; height: 30px; flex: 0 0 30px; border-radius: 50%;
            background: #0d6efd; color: #fff; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center; font-size: 13px;
        }
        .step-title { font-size: 15px; font-weight: 700; margin: 0; }
        .step-ringkas { font-size: 11.5px; color: #6c757d; margin: 0; }
        .step ol { margin: 6px 0 6px 0; padding-left: 20px; }
        .step ol li { margin-bottom: 3px; }
        .tips { background: #e7f1ff; border-left: 3px solid #0d6efd; padding: 6px 10px; font-size: 11px; margin-top: 6px; }
        .shot { width: 100%; border: 1px solid #dee2e6; border-radius: 4px; }
        .shot-cap { font-size: 10px; color: #6c757d; text-align: center; margin-top: 3px; }
        .shot-missing { border: 2px dashed #dee2e6; border-radius: 4px; padding: 18px; text-align: center; color: #adb5bd; font-size: 10.5px; }
        .pdf-footer { border-top: 1px solid #dee2e6; margin-top: 16px; padding-top: 8px; font-size: 10px; color: #6c757d; }
        .toc { font-size: 11.5px; }
        .toc li { margin-bottom: 2px; }
        .print-bar { margin-bottom: 14px; }
        @media print {
            .print-bar { display: none !important; }
            body { font-size: 11px; }
            .step { page-break-inside: avoid; }
        }
        @page { size: A4; margin: 14mm 12mm; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="print-bar no-print">
        <button class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Cetak / Simpan sebagai PDF
        </button>
        <span class="text-muted small ms-2">Pada dialog cetak, pilih tujuan <strong>"Save as PDF"</strong>, lalu simpan.</span>
    </div>

    <div class="pdf-header">
        <h1>Panduan Cara Penggunaan Aplikasi</h1>
        <div class="pdf-meta">
            <?= htmlspecialchars($toko_nama) ?> &middot;
            Dicetak oleh <?= htmlspecialchars($printed_by_name) ?> &middot;
            <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <div class="credential-box">
        <strong><i class="fas fa-key me-1"></i>Akun untuk mencoba:</strong>
        Nama Pengguna <strong>demo</strong> &nbsp;|&nbsp; Kata Sandi <strong>demo123</strong>
    </div>

    <div class="mb-4">
        <h6 class="fw-bold mb-2">Daftar Isi</h6>
        <ol class="toc">
            <?php foreach ($steps as $s): ?>
            <li><?= htmlspecialchars($s['judul']) ?></li>
            <?php endforeach; ?>
        </ol>
    </div>

    <?php foreach ($steps as $step): ?>
    <div class="step">
        <div class="step-head">
            <span class="step-no"><?= $step['no'] ?></span>
            <div>
                <p class="step-title"><?= htmlspecialchars($step['judul']) ?></p>
                <p class="step-ringkas"><?= htmlspecialchars($step['ringkas']) ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-7">
                <ol>
                    <?php foreach ($step['langkah'] as $poin): ?>
                    <li><?= $poin ?></li>
                    <?php endforeach; ?>
                </ol>
                <?php if (!empty($step['tips'])): ?>
                <div class="tips"><strong>Tips:</strong> <?= htmlspecialchars($step['tips']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-5">
                <?php $file = basename($step['screenshot']); ?>
                <?php if (is_file($shot_dir . $file)): ?>
                <img src="<?= $step['screenshot'] ?>" alt="<?= htmlspecialchars($step['judul']) ?>" class="shot">
                <div class="shot-cap"><?= htmlspecialchars($step['judul']) ?></div>
                <?php else: ?>
                <div class="shot-missing">Screenshot belum tersedia</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="pdf-footer">
        Dokumen ini dibuat otomatis dari menu <strong>Cara Penggunaan</strong> di dalam aplikasi.
        Untuk versi terbaru, buka menu <strong>Cara Penggunaan</strong> lalu klik <strong>Export ke PDF</strong>.
    </div>
</div>
</body>
</html>

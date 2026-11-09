<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk <?= htmlspecialchars($penjualan->no_penjualan) ?></title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            max-width: 300px;
            margin: 10px auto;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .double-size {
            font-size: 16px;
        }
        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .brand-logo {
            max-height: 48px;
            margin-bottom: 8px;
        }
        @media print {
            body {
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <?php
    $apotek_nama = isset($profil_apotek) && !empty($profil_apotek->nama) ? $profil_apotek->nama : 'Point Of Sales';
    $apotek_logo = (isset($profil_apotek) && !empty($profil_apotek->logo) && is_file(FCPATH . 'assets/uploads/logo/' . $profil_apotek->logo))
        ? base_url('assets/uploads/logo/' . $profil_apotek->logo) : '';
    ?>
    
    <div class="text-center">
        <?php if ($apotek_logo): ?>
            <img src="<?= $apotek_logo ?>" alt="<?= htmlspecialchars($apotek_nama) ?>" class="brand-logo"><br>
        <?php endif; ?>
        <span class="bold double-size"><?= htmlspecialchars($apotek_nama) ?></span>
        <?php if (!empty($profil_apotek->alamat ?? '')): ?>
            <br><?= htmlspecialchars($profil_apotek->alamat) ?>
        <?php endif; ?>
        <?php if (!empty($profil_apotek->telepon ?? '')): ?>
            <br>Telp: <?= htmlspecialchars($profil_apotek->telepon) ?>
        <?php endif; ?>
    </div>
    
    <hr>
    
    <table>
        <tr>
            <td width="100">No. Trans</td>
            <td>: <?= htmlspecialchars($penjualan->no_penjualan) ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: <?= date('d/m/Y H:i', strtotime($penjualan->tanggal)) ?></td>
        </tr>
        <?php if (!empty($penjualan->pelanggan_nama)): ?>
            <tr>
                <td>Pelanggan</td>
                <td>: <?= htmlspecialchars($penjualan->pelanggan_nama) ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td>Pelanggan</td>
                <td>: Umum</td>
            </tr>
        <?php endif; ?>
        <?php if (!empty($penjualan->metode_pembayaran_nama)): ?>
            <tr>
                <td>Metode</td>
                <td>: <?= htmlspecialchars($penjualan->metode_pembayaran_nama) ?></td>
            </tr>
        <?php endif; ?>
    </table>
    
    <hr>
    
    <table>
        <?php if (!empty($details)): foreach ($details as $d): ?>
            <tr>
                <td colspan="2"><?= htmlspecialchars($d->produk_nama ?? '') ?></td>
            </tr>
            <tr>
                <td><?= number_format($d->qty, 0) ?><?= !empty($d->satuan_nama) ? ' ' . htmlspecialchars($d->satuan_nama) : '' ?> x <?= number_format($d->harga, 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($d->subtotal, 0, ',', '.') ?></td>
            </tr>
            <?php if ($d->diskon > 0): ?>
                <tr>
                    <td colspan="2" style="padding-left: 10px; font-style: italic; color: #555;">
                        Diskon: -<?= number_format($d->diskon, 0, ',', '.') ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; endif; ?>
    </table>
    
    <hr>
    
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right"><?= number_format($penjualan->subtotal, 0, ',', '.') ?></td>
        </tr>
        <?php if ($penjualan->diskon > 0): ?>
            <tr>
                <td>Diskon</td>
                <td class="text-right">-<?= number_format($penjualan->diskon, 0, ',', '.') ?></td>
            </tr>
        <?php endif; ?>
        <?php if ($penjualan->pajak > 0): ?>
            <tr>
                <td>Pajak</td>
                <td class="text-right"><?= number_format($penjualan->pajak, 0, ',', '.') ?></td>
            </tr>
        <?php endif; ?>
        <tr class="bold">
            <td>Total</td>
            <td class="text-right"><?= number_format($penjualan->total, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="text-right"><?= number_format($penjualan->bayar, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right"><?= number_format($penjualan->kembalian, 0, ',', '.') ?></td>
        </tr>
    </table>
    
    <hr>
    
    <div class="text-center">
        Terima Kasih telah berbelanja
    </div>
</body>
</html>

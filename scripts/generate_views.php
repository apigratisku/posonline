<?php
/**
 * Generate missing index views for demo/apotik
 */
$base = dirname(__DIR__) . '/application/views';

function write_file($path, $content) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($path, $content);
    echo "Created: $path\n";
}

function datatable_index($title, $subtitle, $url, $perm, $tableId, $headers, $colCount) {
    $ths = '';
    foreach ($headers as $h) {
        $ths .= "                    <th>$h</th>\n";
    }
    $cols = '';
    for ($i = 0; $i < $colCount; $i++) {
        $extra = ($i === $colCount - 1) ? ", orderable: false, searchable: false" : '';
        $cols .= "            { data: $i$extra },\n";
    }
    return <<<HTML
<div class="page-header">
    <h1 class="page-title">$title</h1>
    <p class="page-subtitle">$subtitle</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar $title</h5>
        <?php if (can_create('$perm')): ?>
            <a href="<?= base_url('$url/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <table id="$tableId" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
$ths                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
\$(document).ready(function() {
    initDataTable('$tableId', {
        ajax: { url: baseUrl + '$url/ajax_list', type: 'POST' },
        columns: [
$cols        ]
    });
});

function deleteData(id) {
    showConfirm('Hapus data ini?', function() {
        showLoading();
        \$.ajax({
            url: baseUrl + '$url/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('$tableId');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal menghapus data');
            }
        });
    });
}
</script>

HTML;
}

// Program Promo
$promo = [
    ['bundel', 'Program Promo Bundel', 'promo/bundel', 'promo', ['ID', 'Kode', 'Nama', 'Mulai', 'Selesai', 'Status', 'Dibuat', 'Aksi'], 8],
    ['diskon', 'Diskon Produk', 'promo/diskon', 'promo', ['ID', 'Kode', 'Nama', 'Mulai', 'Selesai', 'Status', 'Dibuat', 'Aksi'], 8],
    ['member_poin', 'Member Poin', 'promo/poin', 'promo', ['ID', 'Kode', 'Nama', 'Poin', 'Status', 'Dibuat', 'Aksi'], 7],
    ['voucher', 'Voucher Diskon', 'promo/voucher', 'promo', ['ID', 'Kode', 'Nama', 'Nilai', 'Mulai', 'Selesai', 'Status', 'Aksi'], 8],
];
foreach ($promo as $p) {
    write_file("$base/program_promo/{$p[0]}/index.php", datatable_index($p[1], "Kelola {$p[1]}", $p[2], $p[3], $p[0] . 'Table', $p[4], $p[5]));
}

// Multi Outlet
$outlet = [
    ['outlet_mitra', 'Outlet Mitra', 'outlet/outlet-mitra', 'outlet', ['ID', 'Kode', 'Nama', 'Alamat', 'Telepon', 'Status', 'Dibuat', 'Aksi'], 8],
    ['permintaan_mutasi', 'Permintaan Mutasi', 'outlet/permintaan', 'outlet', ['ID', 'No. Permintaan', 'Outlet', 'Gudang', 'Tanggal', 'Status', 'Dibuat', 'Aksi'], 8],
    ['mutasi_antar_outlet', 'Mutasi Antar Outlet', 'outlet/mutasi', 'outlet', ['ID', 'No. Mutasi', 'Asal', 'Tujuan', 'Tanggal', 'Status', 'Dibuat', 'Aksi'], 8],
    ['produk_mitra', 'Produk Mitra', 'outlet/produk-mitra', 'outlet', ['ID', 'Produk', 'Outlet', 'Harga', 'Stok', 'Status', 'Aksi'], 7],
];
foreach ($outlet as $o) {
    $noCreate = in_array($o[0], ['mutasi_antar_outlet', 'produk_mitra']);
    $content = datatable_index($o[1], "Kelola {$o[1]}", $o[2], $o[3], str_replace('_', '', $o[0]) . 'Table', $o[4], $o[5]);
    if ($noCreate) {
        $content = str_replace("<?php if (can_create('$o[3]')): ?>\n            <a href", "<?php if (false): ?>\n            <a href", $content);
    }
    write_file("$base/multi_outlet/{$o[0]}/index.php", $content);
}

// Laporan
$laporan = [
    ['penjualan', 'Laporan Penjualan', 'laporan/penjualan', ['ID', 'No. Penjualan', 'Pelanggan', 'Tanggal', 'Total', 'Kasir'], 6],
    ['pembelian', 'Laporan Pembelian', 'laporan/pembelian', ['ID', 'No. Pembelian', 'Supplier', 'Tanggal', 'Total', 'Status'], 6],
    ['persediaan', 'Laporan Persediaan', 'laporan/persediaan', ['Produk', 'Gudang', 'Stok', 'Harga', 'Nilai'], 5],
    ['keuangan', 'Laporan Keuangan', 'laporan/keuangan', ['Tanggal', 'Akun', 'Keterangan', 'Debit', 'Kredit', 'Saldo'], 6],
];
foreach ($laporan as $l) {
    $content = <<<HTML
<div class="page-header">
    <h1 class="page-title">{$l[1]}</h1>
    <p class="page-subtitle">Laporan data {$l[0]}</p>
</div>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{$l[1]}</h5>
        <button type="button" class="btn btn-success" onclick="exportData()"><i class="fas fa-file-export"></i> Export</button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3"><label class="form-label">Tanggal Mulai</label><input type="date" id="startDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Tanggal Akhir</label><input type="date" id="endDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button></div>
        </div>
        <table id="laporan{$l[0]}Table" class="table table-striped table-bordered" style="width:100%">
            <thead><tr>
HTML;
    foreach ($l[3] as $h) $content .= "<th>$h</th>";
    $cols = '';
    for ($i = 0; $i < $l[4]; $i++) $cols .= "{ data: $i },\n            ";
    $content .= <<<HTML
</tr></thead><tbody></tbody></table>
    </div>
</div>
<script>
\$(document).ready(function() {
    initDataTable('laporan{$l[0]}Table', {
        ajax: {
            url: baseUrl + '{$l[2]}/ajax_list', type: 'POST',
            data: function(d) { d.start_date = \$('#startDate').val(); d.end_date = \$('#endDate').val(); }
        },
        columns: [ $cols ]
    });
});
function filterTable() { reloadDataTable('laporan{$l[0]}Table'); }
function exportData() { window.location.href = baseUrl + '{$l[2]}/ajax_export?start_date=' + \$('#startDate').val() + '&end_date=' + \$('#endDate').val(); }
</script>
HTML;
    write_file("$base/laporan/{$l[0]}/index.php", $content);
}

// Analisis
foreach (['pareto' => 'Analisis Pareto', 'pembelian' => 'Analisis Pembelian', 'harga' => 'Analisis Harga'] as $key => $title) {
    write_file("$base/analisis/$key/index.php", <<<HTML
<div class="page-header"><h1 class="page-title">$title</h1><p class="page-subtitle">Analisis data $key</p></div>
<div class="card">
    <div class="card-header"><h5 class="mb-0">$title</h5></div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3"><label class="form-label">Tanggal Mulai</label><input type="date" id="startDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Tanggal Akhir</label><input type="date" id="endDate" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">&nbsp;</label><button type="button" class="btn btn-primary w-100" onclick="filterTable()">Filter</button></div>
        </div>
        <table id="analisis{$key}Table" class="table table-striped table-bordered" style="width:100%"><thead><tr><th>Data</th><th>Nilai</th><th>Persentase</th></tr></thead><tbody></tbody></table>
    </div>
</div>
<script>
\$(document).ready(function() {
    initDataTable('analisis{$key}Table', {
        ajax: { url: baseUrl + 'analisis/$key/ajax_list', type: 'POST', data: function(d) { d.start_date = \$('#startDate').val(); d.end_date = \$('#endDate').val(); } },
        columns: [{ data: 0 }, { data: 1 }, { data: 2 }]
    });
});
function filterTable() { reloadDataTable('analisis{$key}Table'); }
</script>
HTML);
}

// Penjualan simple pages
foreach ([
    'pesanan_penjualan' => ['Pesanan Penjualan', 'penjualan/pesanan', 'pesanan', ['No. Pesanan', 'Pelanggan', 'Tanggal', 'Total', 'Status', 'Aksi'], 6],
    'retur_penjualan' => ['Retur Penjualan', 'penjualan/retur', 'retur', ['No. Retur', 'Penjualan', 'Tanggal', 'Total', 'Status', 'Aksi'], 6],
    'tertolak' => ['Penjualan Tertolak', 'penjualan/tertolak', 'tertolak', ['No. Penjualan', 'Pelanggan', 'Tanggal', 'Total', 'Alasan', 'Aksi'], 6],
    'faktur_penjualan' => ['Faktur Penjualan', 'penjualan/faktur', 'faktur', ['No. Faktur', 'Penjualan', 'Tanggal', 'Total', 'Status', 'Aksi'], 6],
] as $folder => $info) {
    $c = datatable_index($info[0], "Kelola {$info[0]}", $info[1], 'penjualan', $info[2] . 'Table', $info[3], $info[4]);
    $c = preg_replace('/<a href.*?Tambah.*?<\/a>\s*/s', '', $c);
    $c = str_replace("can_create('penjualan')): ?>\n        ", "false): ?>\n        ", $c);
    write_file("$base/penjualan/$folder/index.php", $c);
}

// Pembelian
foreach ([
    'pesanan_pembelian' => ['Pesanan Pembelian', 'pembelian/pesanan', ['No. Pesanan', 'Supplier', 'Tanggal', 'Total', 'Status', 'Aksi'], 6],
    'faktur_pembelian' => ['Faktur Pembelian', 'pembelian/faktur', ['No. Faktur', 'Pesanan', 'Tanggal', 'Total', 'Status', 'Aksi'], 6],
] as $folder => $info) {
    write_file("$base/pembelian/$folder/index.php", datatable_index($info[0], "Kelola {$info[0]}", $info[1], 'pembelian', str_replace('_', '', $folder) . 'Table', $info[2], $info[3]));
}

// Pelayanan penerimaan resep
write_file("$base/pelayanan/penerimaan_resep/index.php", datatable_index('Penerimaan Resep', 'Kelola penerimaan resep', 'pelayanan/resep', 'pelayanan', 'resepTable', ['ID', 'No. Resep', 'Pasien', 'Dokter', 'Tanggal', 'Status', 'Aksi'], 7));

// KPI & Gaji
write_file("$base/kpi/index.php", datatable_index('KPI Karyawan', 'Kelola KPI karyawan', 'kpi/karyawan', 'kpi', 'kpiTable', ['ID', 'Karyawan', 'Periode', 'Target', 'Pencapaian', '%', 'Status', 'Aksi'], 8));
write_file("$base/gaji/index.php", datatable_index('Perhitungan Gaji', 'Kelola perhitungan gaji karyawan', 'gaji/perhitungan', 'gaji', 'gajiTable', ['ID', 'Karyawan', 'Periode', 'Gaji Pokok', 'Tunjangan', 'Potongan', 'Total', 'Status', 'Aksi'], 9));

// Pengaturan
write_file("$base/pengaturan/index.php", <<<HTML
<div class="page-header"><h1 class="page-title">Pengaturan</h1><p class="page-subtitle">Konfigurasi sistem apotek</p></div>
<div class="row">
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-store fa-2x mb-3 text-primary"></i><h5>Profil Apotek</h5><a href="<?= base_url('pengaturan/profil') ?>" class="btn btn-primary btn-sm">Buka</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-credit-card fa-2x mb-3 text-primary"></i><h5>Metode Pembayaran</h5><a href="<?= base_url('pengaturan/metode-pembayaran') ?>" class="btn btn-primary btn-sm">Buka</a></div></div></div>
    <div class="col-md-4 mb-3"><div class="card h-100"><div class="card-body text-center"><i class="fas fa-sliders-h fa-2x mb-3 text-primary"></i><h5>Konfigurasi</h5><a href="<?= base_url('pengaturan/konfigurasi') ?>" class="btn btn-primary btn-sm">Buka</a></div></div></div>
</div>
HTML);

write_file("$base/pengaturan/profil_apotek/index.php", <<<HTML
<div class="page-header"><h1 class="page-title">Profil Apotek</h1><p class="page-subtitle">Informasi apotek</p></div>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('pengaturan/profil/update') ?>" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Nama Apotek</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars(\$data->nama ?? '') ?>" required></div>
        <div class="col-md-6 mb-3"><label class="form-label">Telepon</label><input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars(\$data->telepon ?? '') ?>"></div>
        <div class="col-md-12 mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"><?= htmlspecialchars(\$data->alamat ?? '') ?></textarea></div>
        <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars(\$data->email ?? '') ?>"></div>
        <div class="col-md-3 mb-3"><label class="form-label">NPWP</label><input type="text" name="npwp" class="form-control" value="<?= htmlspecialchars(\$data->npwp ?? '') ?>"></div>
        <div class="col-md-3 mb-3"><label class="form-label">SIUP</label><input type="text" name="siup" class="form-control" value="<?= htmlspecialchars(\$data->siup ?? '') ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Logo</label><input type="file" name="logo" class="form-control"></div>
    </div>
    <?php if (can_update('pengaturan')): ?><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><?php endif; ?>
</form>
</div></div>
HTML);

write_file("$base/pengaturan/konfigurasi/index.php", <<<HTML
<div class="page-header"><h1 class="page-title">Konfigurasi Sistem</h1><p class="page-subtitle">Pengaturan parameter sistem</p></div>
<div class="card"><div class="card-body">
<form method="post" action="<?= base_url('pengaturan/konfigurasi/update') ?>">
    <?php if (!empty(\$configs)): foreach (\$configs as \$cfg): ?>
    <div class="mb-3"><label class="form-label"><?= htmlspecialchars(\$cfg->key) ?></label><input type="text" name="config[<?= htmlspecialchars(\$cfg->key) ?>]" class="form-control" value="<?= htmlspecialchars(\$cfg->value ?? '') ?>"></div>
    <?php endforeach; else: ?><p class="text-muted">Belum ada konfigurasi.</p><?php endif; ?>
    <?php if (can_update('pengaturan')): ?><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><?php endif; ?>
</form>
</div></div>
HTML);

write_file("$base/pengaturan/metode_pembayaran/index.php", datatable_index('Metode Pembayaran', 'Kelola metode pembayaran', 'pengaturan/metode-pembayaran', 'pengaturan', 'metodeTable', ['ID', 'Kode', 'Nama', 'Tipe', 'Status', 'Aksi'], 6));

// Print view
write_file("$base/penjualan/daftar_penjualan/print.php", <<<HTML
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Struk <?= htmlspecialchars(\$penjualan->no_penjualan) ?></title>
<style>body{font-family:monospace;font-size:12px;max-width:300px;margin:20px auto}table{width:100%}td{padding:2px 0}.text-right{text-align:right}hr{border:1px dashed #000}</style>
</head><body onload="window.print()">
<h3 style="text-align:center">STRUK PENJUALAN</h3>
<p>No: <?= htmlspecialchars(\$penjualan->no_penjualan) ?><br>Tgl: <?= date('d/m/Y H:i', strtotime(\$penjualan->tanggal)) ?></p>
<hr><table>
<?php if (!empty(\$details)): foreach (\$details as \$d): ?>
<tr><td><?= htmlspecialchars(\$d->produk_nama ?? '') ?></td><td class="text-right"><?= number_format(\$d->qty, 0) ?> x <?= number_format(\$d->harga, 0, ',', '.') ?></td></tr>
<?php endforeach; endif; ?>
</table><hr>
<p class="text-right"><strong>Total: Rp <?= number_format(\$penjualan->total, 0, ',', '.') ?></strong></p>
</body></html>
HTML);

echo "Done.\n";

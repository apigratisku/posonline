<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title"><?= $title ?></h1>
    <a href="<?= base_url('masterdata/masterproduk/print_barcode/' . $data->id) ?>" class="btn btn-dark" target="_blank">
        <i class="fas fa-barcode me-1"></i> Cetak Label Barcode
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Produk</h5>
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Kembali</a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">ID</th>
                        <td><?= $data->id ?></td>
                    </tr>
                    <tr>
                        <th>Kode</th>
                        <td><?= htmlspecialchars($data->kode) ?></td>
                    </tr>
                    <tr>
                        <th>Barcode</th>
                        <td><?= htmlspecialchars($data->barcode ?: '-') ?></td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td><?= htmlspecialchars($data->nama) ?></td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td><?= isset($data->kategori_nama) ? htmlspecialchars($data->kategori_nama) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Satuan</th>
                        <td><?= isset($data->satuan_nama) ? htmlspecialchars($data->satuan_nama) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Rak</th>
                        <td><?= isset($data->rak_nama) ? htmlspecialchars($data->rak_nama) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Gudang</th>
                        <td><?= isset($data->gudang_nama) ? htmlspecialchars($data->gudang_nama) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Pajak</th>
                        <td><?= isset($data->pajak_nama) ? htmlspecialchars($data->pajak_nama) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>Rp <?= number_format($data->harga_beli, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>% Keuntungan</th>
                        <td><?= number_format($data->persentase_keuntungan, 2, ',', '.') ?>%</td>
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        <td>Rp <?= number_format($data->harga_jual, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Stok Minimal</th>
                        <td><?= number_format($data->stok_minimal, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Stok Maksimal</th>
                        <td><?= number_format($data->stok_maksimal, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <th>Stok Akhir</th>
                        <td><strong><?= number_format($data->stok_akhir, 0, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal Kadaluarsa</th>
                        <td><?= $data->expired_date ? date('d/m/Y', strtotime($data->expired_date)) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td><?= htmlspecialchars($data->deskripsi ?: '-') ?></td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if ($data->is_active): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td><?= date('d/m/Y H:i', strtotime($data->created_at)) ?></td>
                    </tr>
                    <?php if ($data->updated_at): ?>
                    <tr>
                        <th>Tanggal Diupdate</th>
                        <td><?= date('d/m/Y H:i', strtotime($data->updated_at)) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Barcode Preview Panel -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-barcode me-2"></i>Barcode Produk</h5>
            </div>
            <div class="card-body text-center">
                <?php
                // Gunakan KODE BARANG sebagai nilai barcode (tidak perlu field tambahan)
                $barcode_val = $data->kode;
                ?>
                <svg id="barcodePreview"></svg>
                <div class="mt-2">
                    <code class="text-muted" style="font-size:13px;"><?= htmlspecialchars($barcode_val) ?></code>
                </div>
                <div class="mt-1 text-muted" style="font-size:11px;">
                    <span class="badge bg-primary">Menggunakan Kode Barang</span>
                </div>
                <div class="mt-3">
                    <a href="<?= base_url('masterdata/masterproduk/print_barcode/' . $data->id) ?>" class="btn btn-dark w-100" target="_blank">
                        <i class="fas fa-print me-1"></i> Cetak Label Barcode
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Cara Scan di Kasir</h5>
            </div>
            <div class="card-body">
                <ol class="mb-0" style="font-size:13px; padding-left:18px;">
                    <li>Buka halaman <strong>Kasir</strong></li>
                    <li>Klik pada kolom pencarian produk</li>
                    <li>Arahkan scanner barcode ke label yang sudah dicetak</li>
                    <li>Produk akan otomatis ditambahkan ke keranjang</li>
                </ol>
                <div class="alert alert-info mt-3 mb-0 p-2" style="font-size:12px;">
                    <i class="fas fa-lightbulb me-1"></i>
                    Scanner barcode bekerja seperti keyboard — scan akan mengisi kolom pencarian otomatis.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var barcodeValue = <?= json_encode($barcode_val) ?>;
    var svgEl = document.getElementById('barcodePreview');
    if (typeof JsBarcode !== 'undefined' && svgEl) {
        try {
            JsBarcode(svgEl, barcodeValue, {
                format: 'CODE128',
                width: 2,
                height: 60,
                displayValue: true,
                fontSize: 12,
                margin: 8,
                textMargin: 4
            });
        } catch (e) {
            try {
                JsBarcode(svgEl, barcodeValue, {
                    format: 'CODE39',
                    width: 1.5,
                    height: 60,
                    displayValue: true,
                    fontSize: 12,
                    margin: 8
                });
            } catch (e2) {
                svgEl.parentNode.innerHTML = '<div class="alert alert-warning p-2" style="font-size:12px;"><i class="fas fa-exclamation-triangle me-1"></i>Barcode tidak dapat dirender. Pastikan nilai barcode hanya berisi karakter yang valid.</div>';
            }
        }
    }
});
</script>

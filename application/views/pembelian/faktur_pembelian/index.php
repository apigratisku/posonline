<div class="page-header">
    <h1 class="page-title">Faktur Pembelian</h1>
    <p class="page-subtitle">Kelola Faktur Pembelian</p>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Daftar Faktur Pembelian</h5>
        <?php if (can_create('pembelian')): ?>
            <button type="button" class="btn btn-primary" onclick="addData()">
                <i class="fas fa-plus"></i> Tambah Faktur
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="fakturpembelianTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Supplier</th>
                        <th>Tanggal</th>
                        <th>Jatuh Tempo</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Faktur -->
<div class="modal fade" id="fakturPembelianModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fakturModalTitle">Tambah Faktur Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fakturPembelianForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="faktur_id">

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">No. Faktur</label>
                            <input type="text" class="form-control" name="no_faktur" id="no_faktur" maxlength="50" placeholder="Otomatis jika dikosongkan">
                            <div class="form-text">Kosongkan untuk nomor otomatis.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pesanan Pembelian (PO)</label>
                            <select class="form-select" name="po_id" id="po_id">
                                <option value="">Tanpa PO</option>
                                <?php foreach ($pos as $po): ?>
                                    <option value="<?= $po->id ?>"><?= htmlspecialchars($po->no_po) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" name="supplier_id" id="supplier_id" required>
                                <option value="">Pilih Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                    <option value="<?= $supplier->id ?>"><?= htmlspecialchars($supplier->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Jenis Pembayaran</label>
                            <select class="form-select" name="jenis_pembayaran_id" id="jenis_pembayaran_id">
                                <option value="">Pilih Jenis Pembayaran</option>
                                <?php foreach ($metode_pembayarans as $mp): ?>
                                    <option value="<?= $mp->id ?>"><?= htmlspecialchars($mp->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Pembelian <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" class="form-control" name="jatuh_tempo" id="jatuh_tempo">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Item Barang</h6>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="openObatBaruModal()">
                                <i class="fas fa-capsules"></i> Tambah Obat Baru
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                                <i class="fas fa-plus"></i> Tambah Baris
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="fakturItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:220px">Nama Obat</th>
                                    <th style="min-width:110px">Satuan Beli</th>
                                    <th style="width:110px">Qty</th>
                                    <th style="min-width:110px">No. Batch</th>
                                    <th style="min-width:130px">Expired Date</th>
                                    <th style="width:120px">Harga Beli</th>
                                    <th style="min-width:145px">Diskon</th>
                                    <th style="width:120px">Subtotal</th>
                                    <th style="width:45px"></th>
                                </tr>
                            </thead>
                            <tbody id="fakturItemsBody"></tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-3">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <strong id="fakturSubtotalDisplay">Rp 0</strong>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small">Tipe Diskon Global</label>
                                    <select class="form-select form-select-sm" id="diskon_tipe" onchange="toggleDiskonTipe(); calcTotal();">
                                        <option value="persen">Persen (%)</option>
                                        <option value="nominal">Nominal (Rp)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small" id="diskonLabel">Diskon Global (%)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control form-control-sm" name="diskon" id="diskon" value="0" min="0" max="100" step="0.01" oninput="calcTotal()">
                                        <span class="input-group-text" id="diskonSuffix">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6 offset-6">
                                    <label class="form-label small">Pajak</label>
                                    <input type="number" class="form-control form-control-sm" name="pajak" id="pajak" value="0" min="0" step="1" oninput="calcTotal()">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span><strong>Total</strong></span>
                                <strong class="text-primary" id="fakturTotalDisplay">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <?php if (can_create('pembelian') || can_update('pembelian')): ?>
                        <button type="button" class="btn btn-primary" onclick="submitFakturForm()">
                            <i class="fas fa-save"></i> Simpan Faktur
                        </button>
                    <?php endif; ?>
                    <?php if ($this->auth->has_permission('pembelian', 'delete')): ?>
                        <button type="button" class="btn btn-outline-danger" id="btnDeleteFromForm" onclick="deleteDataFromForm()">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Obat Baru -->
<div class="modal fade" id="obatBaruModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Obat Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="obatBaruForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Obat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="kode" id="ob_kode" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama" id="ob_nama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pabrik</label>
                            <input type="text" class="form-control" name="pabrik" id="ob_pabrik">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Golongan</label>
                            <select class="form-select" name="golongan_id" id="ob_golongan_id">
                                <option value="">Pilih Golongan</option>
                                <?php foreach ($golongs as $g): ?>
                                    <option value="<?= $g->id ?>"><?= htmlspecialchars($g->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="kategori_id" id="ob_kategori_id">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategoris as $k): ?>
                                    <option value="<?= $k->id ?>"><?= htmlspecialchars($k->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pajak</label>
                            <select class="form-select" name="pajak_id" id="ob_pajak_id">
                                <option value="">Pilih Pajak</option>
                                <?php foreach ($pajaks as $pj): ?>
                                    <option value="<?= $pj->id ?>"><?= htmlspecialchars($pj->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan Jual Terkecil</label>
                            <select class="form-select" name="satuan_id" id="ob_satuan_id">
                                <option value="">Pilih Satuan</option>
                                <?php foreach ($satuans as $s): ?>
                                    <option value="<?= $s->id ?>"><?= htmlspecialchars($s->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Satuan Pembelian</label>
                            <select class="form-select" name="satuan_beli_id" id="ob_satuan_beli_id">
                                <option value="">Pilih Satuan</option>
                                <?php foreach ($satuans as $s): ?>
                                    <option value="<?= $s->id ?>"><?= htmlspecialchars($s->nama) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konversi (isi per kemasan)</label>
                            <input type="number" class="form-control" name="satuan_konversi" id="ob_satuan_konversi" value="1" min="1" step="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Beli per Kemasan</label>
                            <input type="number" class="form-control" name="harga_beli_kemasan" id="ob_harga_beli_kemasan" value="0" min="0" step="1">
                            <div class="form-text">Otomatis dibagi konversi saat disimpan.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Margin Non-Resep (%)</label>
                            <input type="number" class="form-control" name="persentase_keuntungan" id="ob_persentase" value="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Margin Resep (%)</label>
                            <input type="number" class="form-control" name="margin_resep" id="ob_margin_resep" value="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Margin Pelanggan (%)</label>
                            <input type="number" class="form-control" name="margin_pelanggan" id="ob_margin_pelanggan" value="0" min="0" step="0.01">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Indikasi</label>
                            <textarea class="form-control" name="indikasi" id="ob_indikasi" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="submitObatBaru()">
                        <i class="fas fa-save"></i> Simpan &amp; Tambah ke Faktur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Faktur -->
<div class="modal fade" id="fakturPembelianViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Faktur Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-sm-6"><strong>No. Faktur:</strong> <span id="viewNoFaktur">-</span></div>
                    <div class="col-sm-6"><strong>Status:</strong> <span id="viewStatus">-</span></div>
                    <div class="col-sm-6"><strong>Supplier:</strong> <span id="viewSupplier">-</span></div>
                    <div class="col-sm-6"><strong>PO Ref:</strong> <span id="viewPoRef">-</span></div>
                    <div class="col-sm-6"><strong>Tanggal:</strong> <span id="viewTanggal">-</span></div>
                    <div class="col-sm-6"><strong>Jatuh Tempo:</strong> <span id="viewJatuhTempo">-</span></div>
                    <div class="col-sm-6"><strong>Jenis Pembayaran:</strong> <span id="viewJenisPembayaran">-</span></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Satuan</th>
                                <th>No. Batch</th>
                                <th>Expired</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Diskon</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="viewItemsBody"></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-end">Total</th>
                                <th class="text-end" id="viewTotal">Rp 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success d-none" id="btnPayFromView" onclick="payFromView()">
                    <i class="fas fa-check-circle"></i> Tandai Lunas
                </button>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="fakturProdukOptions"><?= json_encode(array_map(function ($p) {
    return array(
        'id' => (int) $p->id,
        'kode' => $p->kode,
        'nama' => $p->nama,
        'harga_beli' => (float) ($p->harga_beli ?? 0),
        'satuan_beli_id' => $p->satuan_beli_id ? (int) $p->satuan_beli_id : null,
        'satuan_konversi' => (int) ($p->satuan_konversi ?? 1)
    );
}, $produks)) ?></script>

<script type="application/json" id="fakturSatuanOptions"><?= json_encode(array_map(function ($s) {
    return array('id' => (int) $s->id, 'nama' => $s->nama);
}, $satuans)) ?></script>

<script>
function formatCurrency(amount) {
    return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
}
function formatNumber(amount) {
    return Number(amount || 0).toLocaleString('id-ID');
}
function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID');
}

$(document).ready(function() {
    initDataTable('fakturpembelianTable', {
        ajax: { url: baseUrl + 'pembelian/faktur/ajax_list', type: 'POST' },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false, searchable: false },
        ]
    });
    
    // Sinkronisasi dengan PO
    $('#po_id').on('change', function() {
        let poId = $(this).val();
        if (poId) {
            showLoading();
            $.ajax({
                url: baseUrl + 'pembelian/pesanan/ajax_get/' + poId,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    hideLoading();
                    if (res.status) {
                        let po = res.data.po;
                        let details = res.data.details;
                        
                        $('#supplier_id').val(po.supplier_id);
                        // Diskon dari PO disimpan nominal di DB -> tampilkan sebagai nominal
                        $('#diskon_tipe').val('nominal');
                        toggleDiskonTipe();
                        $('#diskon').val(po.diskon || 0);
                        $('#pajak').val(po.pajak || 0);
                        
                        $('#fakturItemsBody').empty();
                        if (details && details.length > 0) {
                            details.forEach(function(d) {
                                addItemRow({
                                    produkId: d.produk_id,
                                    produkLabel: d.produk_kode && d.produk_nama ? (d.produk_kode + ' - ' + d.produk_nama) : undefined,
                                    qty: d.qty,
                                    harga: d.harga,
                                    diskon: d.diskon || 0,
                                    subtotal: d.subtotal
                                });
                            });
                        } else {
                            addItemRow();
                        }
                    } else {
                        showNotification('error', 'Gagal memuat data PO');
                    }
                },
                error: function() {
                    hideLoading();
                    showNotification('error', 'Gagal memuat data PO');
                }
            });
        } else {
            // Jika Tanpa PO, biarkan kosong atau reset
            $('#supplier_id').val('');
            $('#diskon').val(0);
            $('#pajak').val(0);
            $('#fakturItemsBody').empty();
            addItemRow();
        }
    });
});

let produks = [];
let satuans = [];
try {
    produks = JSON.parse($('#fakturProdukOptions').text());
} catch (e) {
    console.error("Failed to parse produks JSON", e);
}
try {
    satuans = JSON.parse($('#fakturSatuanOptions').text());
} catch (e) {
    console.error("Failed to parse satuans JSON", e);
}

function getSatuanOptionsHTML(selectedId = null) {
    let html = '<option value="">-</option>';
    satuans.forEach(s => {
        let selected = selectedId == s.id ? 'selected' : '';
        html += `<option value="${s.id}" ${selected}>${s.nama}</option>`;
    });
    return html;
}

function findProduk(id) {
    let found = produks.find(p => p.id == id);
    if (!found) {
        return { id: '', kode: '', nama: '', label: '', harga_beli: 0, satuan_konversi: 1, satuan_beli_id: null };
    }
    found.label = found.kode + ' - ' + found.nama;
    return found;
}

/**
 * Autocomplete nama obat untuk satu baris item.
 * Input: ketik nama/kode -> dropdown daftar produk -> pilih -> isi harga + satuan.
 */
function bindProdukAutocomplete($row) {
    let $input = $row.find('.item-produk-nama');
    let $hidden = $row.find('.item-produk');
    let $menu = $row.find('.ac-menu');
    let activeIndex = -1;
    let matches = [];

    // Menu dipindah ke <body> agar tidak terpotong scroll modal/table.
    $menu = $menu.detach().appendTo('body');

    function positionMenu() {
        if (!$menu.hasClass('ac-open')) return;
        let rect = $input[0].getBoundingClientRect();
        let top = rect.bottom + 2;
        let height = Math.min(260, window.innerHeight - top - 12);
        // Kalau ruang bawah kurang, buka ke atas
        if (height < 120) {
            height = Math.min(260, rect.top - 12);
            top = rect.top - height - 2;
        }
        $menu.css({
            top: top + 'px',
            left: rect.left + 'px',
            minWidth: Math.max(rect.width, 220) + 'px',
            maxHeight: height + 'px'
        });
    }

    function closeMenu() {
        $menu.removeClass('ac-open').empty();
        activeIndex = -1;
        $(window).off('resize.acmenu scroll.acmenu');
    }

    function renderMenu(list) {
        matches = list;
        activeIndex = list.length ? 0 : -1;

        if (!list.length) {
            $menu.html('<div class="ac-empty">Obat tidak ditemukan</div>');
            $menu.addClass('ac-open');
            positionMenu();
            $(window).on('resize.acmenu scroll.acmenu', positionMenu);
            return;
        }

        let html = '';
        list.forEach(function(p, i) {
            html += `<div class="ac-item${i === activeIndex ? ' ac-active' : ''}" data-index="${i}">`
                + `<span class="ac-code">${escapeHtml(p.kode)}</span>${escapeHtml(p.nama)}`
                + `</div>`;
        });
        $menu.html(html).addClass('ac-open');
        positionMenu();
        $(window).on('resize.acmenu scroll.acmenu', positionMenu);
    }

    function highlight() {
        $menu.find('.ac-item').each(function(i) {
            $(this).toggleClass('ac-active', i === activeIndex);
        });
        let $active = $menu.find('.ac-item.ac-active');
        if ($active.length) {
            $active[0].scrollIntoView({ block: 'nearest' });
        }
    }

    function choose(index) {
        let p = matches[index];
        if (!p) return;

        $hidden.val(p.id);
        $input.val(p.kode + ' - ' + p.nama);
        closeMenu();

        let konversi = p.satuan_konversi > 0 ? p.satuan_konversi : 1;
        $row.find('.item-harga').val(Math.round((p.harga_beli || 0) * konversi));
        if (p.satuan_beli_id) {
            $row.find('.item-satuan').val(p.satuan_beli_id);
        }
        calcRow($input);
    }

    $input.on('input', function() {
        let q = $(this).val().trim().toLowerCase();
        if (!q) {
            $hidden.val('');
            closeMenu();
            return;
        }
        renderMenu(produks.filter(p =>
            p.nama.toLowerCase().includes(q) || p.kode.toLowerCase().includes(q)
        ).slice(0, 50));
    })
    .on('focus', function() {
        // Tampilkan semua obat sebagai titik awal
        $(this).select();
        renderMenu(produks.slice(0, 50));
    })
    .on('keydown', function(e) {
        if (!$menu.hasClass('ac-open')) return;

        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if (!matches.length) return;
            activeIndex += (e.key === 'ArrowDown' ? 1 : -1);
            if (activeIndex < 0) activeIndex = matches.length - 1;
            if (activeIndex >= matches.length) activeIndex = 0;
            highlight();
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0) {
                e.preventDefault();
                choose(activeIndex);
            }
        } else if (e.key === 'Escape') {
            closeMenu();
        }
    })
    .on('blur', function() {
        // Beri jeda supaya klik item dropdown sempat diproses
        setTimeout(function() {
            closeMenu();
            if (!$hidden.val()) {
                $input.val('');
            }
        }, 180);
    });

    $menu.on('mousedown', '.ac-item', function(e) {
        e.preventDefault(); // cegah input kehilangan fokus sebelum klik diproses
        choose(parseInt($(this).data('index'), 10));
    });
}

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// Tutup semua dropdown autocomplete yang masih terbuka
$(document).on('click', function(e) {
    if (!$(e.target).closest('.ac-wrap').length && !$(e.target).closest('.ac-menu').length) {
        $('.ac-menu.ac-open').removeClass('ac-open').empty();
    }
});

// Tutup saat modal ditutup supaya menu tidak nyangkut di layar
$(document).on('hidden.bs.modal', function() {
    $('.ac-menu.ac-open').removeClass('ac-open').empty();
});

function addItemRow(opts = {}) {
    opts = opts || {};
    const produkId = opts.produkId || '';
    const qty = opts.qty !== undefined ? opts.qty : 1;
    const harga = opts.harga !== undefined ? opts.harga : 0;
    const diskon = opts.diskon !== undefined ? opts.diskon : 0;
    const subtotal = opts.subtotal !== undefined ? opts.subtotal : 0;
    const satuanBeliId = opts.satuanBeliId || '';
    const noBatch = opts.noBatch || '';
    const expired = opts.expiredDate || '';
    const produkLabel = opts.produkLabel || findProduk(produkId).label;
    const diskonTipe = opts.diskonTipe || 'nominal';

    let tr = `
        <tr class="item-row">
            <td>
                <div class="ac-wrap">
                    <input type="text" class="form-control form-control-sm item-produk-nama" placeholder="Ketik nama obat..." autocomplete="off" value="${escapeHtml(produkLabel)}">
                    <input type="hidden" class="item-produk" value="${produkId}">
                    <div class="ac-menu"></div>
                </div>
            </td>
            <td><select class="form-select form-select-sm item-satuan">${getSatuanOptionsHTML(satuanBeliId)}</select></td>
            <td><input type="number" class="form-control form-control-sm item-qty text-end" value="${qty}" min="1" oninput="calcRow(this)"></td>
            <td><input type="text" class="form-control form-control-sm item-batch" value="${noBatch}" placeholder="No. Batch"></td>
            <td><input type="date" class="form-control form-control-sm item-expired" value="${expired}"></td>
            <td><input type="number" class="form-control form-control-sm item-harga text-end" value="${harga}" min="0" oninput="calcRow(this)"></td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control form-control-sm item-diskon text-end" value="${diskon}" min="0" step="0.01" oninput="calcRow(this)">
                    <select class="form-select form-select-sm item-diskon-tipe" style="max-width:78px" onchange="calcRow(this)">
                        <option value="nominal" ${diskonTipe === 'nominal' ? 'selected' : ''}>Rp</option>
                        <option value="persen" ${diskonTipe === 'persen' ? 'selected' : ''}>%</option>
                    </select>
                </div>
            </td>
            <td><input type="text" class="form-control form-control-sm item-subtotal text-end" value="${formatCurrency(subtotal)}" readonly></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="$(this).closest('tr').remove(); calcTotal();"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    $('#fakturItemsBody').append(tr);
    bindProdukAutocomplete($('#fakturItemsBody tr').last());
    if (produkId && !satuanBeliId) {
        let sb = findProduk(produkId).satuan_beli_id;
        if (sb) {
            $('#fakturItemsBody tr').last().find('.item-satuan').val(sb);
        }
    }
    calcTotal();
}

/** Diskon item: clamp 0-100 kalau tipe persen */
function getRowDiskonPersen($row) {
    if ($row.find('.item-diskon-tipe').val() !== 'persen') return 0;
    return Math.min(Math.max(parseFloat($row.find('.item-diskon').val()) || 0, 0), 100);
}

/** Diskon item dalam rupiah (hasil konversi dari persen kalau perlu) */
function getRowDiskonNominal($row) {
    let tipe = $row.find('.item-diskon-tipe').val();
    let nilai = parseFloat($row.find('.item-diskon').val()) || 0;
    if (tipe === 'persen') {
        let qty = parseFloat($row.find('.item-qty').val()) || 0;
        let harga = parseFloat($row.find('.item-harga').val()) || 0;
        return (qty * harga) * (Math.min(Math.max(nilai, 0), 100) / 100);
    }
    return Math.max(nilai, 0);
}

/** Diskon global dalam rupiah, mengikuti tipe yang dipilih */
function getGlobalDiskonNominal(subtotal) {
    let nilai = parseFloat($('#diskon').val()) || 0;
    if ($('#diskon_tipe').val() === 'persen') {
        return subtotal * (Math.min(Math.max(nilai, 0), 100) / 100);
    }
    return Math.max(nilai, 0);
}

/** Ganti label/suffix/limit input diskon global saat tipe berubah */
function toggleDiskonTipe() {
    let isPersen = $('#diskon_tipe').val() === 'persen';
    $('#diskonLabel').text(isPersen ? 'Diskon Global (%)' : 'Diskon Global (Rp)');
    $('#diskonSuffix').text(isPersen ? '%' : 'Rp');
    $('#diskon').attr('max', isPersen ? 100 : '');
    $('#diskon').attr('step', isPersen ? '0.01' : '1');
    if (isPersen) {
        $('#diskon').val(Math.min(Math.max(parseFloat($('#diskon').val()) || 0, 0), 100));
    }
}

function calcRow(el) {
    let tr = $(el).closest('tr');
    let qty = parseFloat(tr.find('.item-qty').val()) || 0;
    let harga = parseFloat(tr.find('.item-harga').val()) || 0;
    let diskon = getRowDiskonNominal(tr);
    let subtotal = (qty * harga) - diskon;
    tr.find('.item-subtotal').val(formatCurrency(subtotal));
    calcTotal();
}

function calcTotal() {
    let subtotal = 0;
    $('#fakturItemsBody .item-row').each(function() {
        let $row = $(this);
        let qty = parseFloat($row.find('.item-qty').val()) || 0;
        let harga = parseFloat($row.find('.item-harga').val()) || 0;
        subtotal += (qty * harga) - getRowDiskonNominal($row);
    });
    $('#fakturSubtotalDisplay').text(formatCurrency(subtotal));

    let diskonGlb = getGlobalDiskonNominal(subtotal);
    let pajak = parseFloat($('#pajak').val()) || 0;
    let total = subtotal - diskonGlb + pajak;
    $('#fakturTotalDisplay').text(formatCurrency(total));
}

function addData() {
    $('#fakturPembelianForm')[0].reset();
    $('#faktur_id').val('');
    $('#fakturModalTitle').text('Tambah Faktur Pembelian');
    // Tombol hapus hanya relevan saat edit
    $('#btnDeleteFromForm').addClass('d-none');
    $('#fakturItemsBody').empty();
    $('#diskon_tipe').val('nominal');
    toggleDiskonTipe();
    addItemRow();
    $('#fakturPembelianModal').modal('show');
}

/**
 * Load data faktur untuk diedit.
 * Item direkonstruksi dari diskon nominal yang tersimpan (tipe nominal).
 */
function editData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/faktur/ajax_get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (!res.status) {
                showNotification('error', res.message);
                return;
            }

            let data = res.data.faktur;
            let details = res.data.details || [];

            $('#fakturPembelianForm')[0].reset();
            $('#faktur_id').val(data.id);
            $('#fakturModalTitle').text('Edit Faktur Pembelian');
            $('#btnDeleteFromForm').removeClass('d-none');

            $('#no_faktur').val(data.no_faktur);
            // PO lama mungkin sudah tidak ada di daftar (status berubah) -> tambahkan opsi
            if (data.po_id && !$('#po_id option[value="' + data.po_id + '"]').length) {
                $('#po_id').append('<option value="' + data.po_id + '">' + escapeHtml(data.no_po || ('PO #' + data.po_id)) + '</option>');
            }
            $('#po_id').val(data.po_id || '');
            $('#supplier_id').val(data.supplier_id);
            $('#jenis_pembayaran_id').val(data.jenis_pembayaran_id || '');
            $('#tanggal').val(data.tanggal);
            $('#jatuh_tempo').val(data.jatuh_tempo || '');

            // Diskon global disimpan nominal di DB; pajak juga diisi dari data tersimpan
            $('#diskon_tipe').val('nominal');
            toggleDiskonTipe();
            $('#diskon').val(data.diskon || 0);
            $('#pajak').val(data.pajak || 0);
            // Pastikan total ikut terhitung ulang dari nilai yang baru dimuat
            calcTotal();

            $('#fakturItemsBody').empty();
            if (details.length > 0) {
                details.forEach(function(d) {
                    addItemRow({
                        produkId: d.produk_id,
                        produkLabel: (d.produk_kode || '') + ' - ' + (d.produk_nama || ''),
                        satuanBeliId: d.satuan_beli_id || '',
                        qty: d.qty,
                        harga: d.harga,
                        diskon: d.diskon || 0,
                        diskonTipe: 'nominal',
                        noBatch: d.no_batch || '',
                        expiredDate: d.expired_date || ''
                    });
                });
            } else {
                addItemRow();
            }

            $('#fakturPembelianModal').modal('show');
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal memuat data');
        }
    });
}

function submitFakturForm() {
    if (!$('#fakturPembelianForm')[0].checkValidity()) {
        $('#fakturPembelianForm')[0].reportValidity();
        return;
    }
    
    let items = [];
    let valid = true;
    $('#fakturItemsBody .item-row').each(function() {
        let $row = $(this);
        let produk_id = $row.find('.item-produk').val();
        let satuan_beli_id = $row.find('.item-satuan').val();
        let qty = $row.find('.item-qty').val();
        let no_batch = $row.find('.item-batch').val();
        let expired_date = $row.find('.item-expired').val();
        let harga = $row.find('.item-harga').val();
        let diskon = getRowDiskonNominal($row);
        let diskon_persen = getRowDiskonPersen($row);
        
        if (!produk_id || qty <= 0) {
            valid = false;
        } else {
            items.push({
                produk_id: produk_id,
                satuan_beli_id: satuan_beli_id,
                qty: qty,
                no_batch: no_batch,
                expired_date: expired_date,
                harga: harga,
                diskon: diskon,
                diskon_persen: diskon_persen
            });
        }
    });
    
    if (!valid || items.length === 0) {
        showNotification('warning', 'Pilih produk dan tentukan qty lebih dari 0 untuk semua item');
        return;
    }
    
    let formData = new FormData($('#fakturPembelianForm')[0]);
    formData.append('items', JSON.stringify(items));
    
    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/faktur/ajax_save',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status) {
                showNotification('success', res.message);
                $('#fakturPembelianModal').modal('hide');
                reloadDataTable('fakturpembelianTable');
            } else {
                showNotification('error', res.message);
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal menyimpan data');
        }
    });
}

function openObatBaruModal() {
    $('#obatBaruForm')[0].reset();
    $('#ob_satuan_konversi').val(1);
    $('#ob_harga_beli_kemasan').val(0);
    $('#obatBaruModal').modal('show');
}

function submitObatBaru() {
    if (!$('#obatBaruForm')[0].checkValidity()) {
        $('#obatBaruForm')[0].reportValidity();
        return;
    }

    let konversi = parseFloat($('#ob_satuan_konversi').val()) || 1;
    let hargaKemasan = parseFloat($('#ob_harga_beli_kemasan').val()) || 0;
    let hargaUnit = konversi > 0 ? (hargaKemasan / konversi) : hargaKemasan;

    let payload = {
        kode: $('#ob_kode').val(),
        nama: $('#ob_nama').val(),
        pabrik: $('#ob_pabrik').val(),
        golongan_id: $('#ob_golongan_id').val(),
        kategori_id: $('#ob_kategori_id').val(),
        pajak_id: $('#ob_pajak_id').val(),
        satuan_id: $('#ob_satuan_id').val(),
        satuan_beli_id: $('#ob_satuan_beli_id').val(),
        satuan_konversi: konversi,
        harga_beli: hargaUnit,
        persentase_keuntungan: $('#ob_persentase').val(),
        margin_resep: $('#ob_margin_resep').val(),
        margin_pelanggan: $('#ob_margin_pelanggan').val(),
        indikasi: $('#ob_indikasi').val()
    };

    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/faktur/ajax_add_produk',
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (!res.status) {
                showNotification('error', res.message);
                return;
            }
            let p = res.data;
            let konv = parseInt(p.satuan_konversi) || 1;
            produks.push({
                id: parseInt(p.id),
                kode: p.kode,
                nama: p.nama,
                harga_beli: parseFloat(p.harga_beli) || 0,
                satuan_beli_id: p.satuan_beli_id ? parseInt(p.satuan_beli_id) : null,
                satuan_konversi: konv
            });
            $('#obatBaruModal').modal('hide');
            showNotification('success', res.message);
            addItemRow({
                produkId: p.id,
                satuanBeliId: p.satuan_beli_id,
                harga: Math.round((parseFloat(p.harga_beli) || 0) * konv)
            });
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal menambahkan obat baru');
        }
    });
}

let viewFakturId = null;

function viewData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + 'pembelian/faktur/ajax_get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status) {
                let data = res.data.faktur;
                viewFakturId = data.id;
                
                $('#viewNoFaktur').text(data.no_faktur);
                $('#viewPoRef').text(data.no_po ? data.no_po : '-');
                let statusBadge = '<span class="badge bg-' + (data.status == 'paid' ? 'success' : (data.status == 'partial' ? 'warning' : 'danger')) + '">' + data.status.toUpperCase() + '</span>';
                $('#viewStatus').html(statusBadge);
                $('#viewSupplier').text(data.supplier_nama);
                $('#viewTanggal').text(formatDate(data.tanggal));
                $('#viewJatuhTempo').text(data.jatuh_tempo ? formatDate(data.jatuh_tempo) : '-');
                $('#viewJenisPembayaran').text(data.jenis_pembayaran_nama || '-');
                
                if (data.status === 'unpaid') {
                    $('#btnPayFromView').removeClass('d-none');
                } else {
                    $('#btnPayFromView').addClass('d-none');
                }

                $('#viewItemsBody').empty();
                let total = 0;
                if (res.data.details && res.data.details.length > 0) {
                    res.data.details.forEach(item => {
                        let subtotal = parseFloat(item.subtotal);
                        total += subtotal;
                        $('#viewItemsBody').append(`
                            <tr>
                                <td>${item.produk_kode} - ${item.produk_nama}</td>
                                <td>${item.satuan_beli_nama || '-'}</td>
                                <td>${item.no_batch || '-'}</td>
                                <td>${item.expired_date ? formatDate(item.expired_date) : '-'}</td>
                                <td class="text-end">${formatNumber(item.qty)}</td>
                                <td class="text-end">${formatCurrency(item.harga)}</td>
                                <td class="text-end">${formatCurrency(item.diskon)}</td>
                                <td class="text-end">${formatCurrency(subtotal)}</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#viewItemsBody').append('<tr><td colspan="8" class="text-center">Tidak ada item</td></tr>');
                }
                $('#viewTotal').text(formatCurrency(total));
                $('#fakturPembelianViewModal').modal('show');
            } else {
                showNotification('error', res.message);
            }
        },
        error: function() {
            hideLoading();
            showNotification('error', 'Gagal memuat data');
        }
    });
}

function payInvoice(id) {
    showConfirm('Tandai faktur ini sebagai Lunas?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/faktur/ajax_pay/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    reloadDataTable('fakturpembelianTable');
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal memproses pembayaran');
            }
        });
    });
}

function payFromView() {
    if (!viewFakturId) return;
    showConfirm('Tandai faktur ini sebagai Lunas?', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/faktur/ajax_pay/' + viewFakturId,
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                hideLoading();
                if (res.status) {
                    showNotification('success', res.message);
                    $('#fakturPembelianViewModal').modal('hide');
                    reloadDataTable('fakturpembelianTable');
                } else {
                    showNotification('error', res.message);
                }
            },
            error: function() {
                hideLoading();
                showNotification('error', 'Gagal memproses pembayaran');
            }
        });
    });
}

function deleteData(id) {
    showConfirm('Hapus data ini? Data item dan mutasi kartu stok terkait juga akan dihapus.', function() {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/faktur/ajax_delete/' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    reloadDataTable('fakturpembelianTable');
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

/** Hapus dari dalam modal edit (pakai id tersembunyi di form) */
function deleteDataFromForm() {
    let id = $('#faktur_id').val();
    if (!id) return;
    deleteData(id);
    $('#fakturPembelianModal').modal('hide');
}
</script>

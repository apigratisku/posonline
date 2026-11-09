<div class="page-header">
    <h1 class="page-title"><?= $title ?></h1>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?= $form_title ?></h5>
    </div>
    <div class="card-body">
        <?= form_open($form_action, ['id' => 'produkForm']) ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                        <?= form_input(['name' => 'kode', 'id' => 'kode', 'class' => 'form-control' . (form_error('kode') ? ' is-invalid' : ''), 'value' => set_value('kode', $data->kode), 'required' => true]) ?>
                        <?= form_error('kode', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="barcode" class="form-label">Barcode (Scan atau Ketik)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                            <?= form_input(['name' => 'barcode', 'id' => 'barcode', 'class' => 'form-control' . (form_error('barcode') ? ' is-invalid' : ''), 'value' => set_value('barcode', $data->barcode), 'placeholder' => 'Arahkan scanner ke barcode...']) ?>
                            <button type="button" class="btn btn-outline-secondary" id="btnFocusBarcode" title="Fokus ke input barcode">Fokus Scan</button>
                        </div>
                        <?= form_error('barcode', '<div class="invalid-feedback">', '</div>') ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                <?= form_input(['name' => 'nama', 'id' => 'nama', 'class' => 'form-control' . (form_error('nama') ? ' is-invalid' : ''), 'value' => set_value('nama', $data->nama), 'required' => true]) ?>
                <?= form_error('nama', '<div class="invalid-feedback">', '</div>') ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pabrik" class="form-label">Pabrik</label>
                        <?= form_input(['name' => 'pabrik', 'id' => 'pabrik', 'class' => 'form-control', 'value' => set_value('pabrik', $data->pabrik)]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="golongan_id" class="form-label">Golongan</label>
                        <?= form_dropdown('golongan_id', ['' => 'Pilih Golongan'] + rows_to_dropdown($golongs), set_value('golongan_id', $data->golongan_id), ['class' => 'form-select', 'id' => 'golongan_id']) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori</label>
                        <?= form_dropdown('kategori_id', ['' => 'Pilih Kategori'] + rows_to_dropdown($kategoris), set_value('kategori_id', $data->kategori_id), ['class' => 'form-select', 'id' => 'kategori_id']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="satuan_id" class="form-label">Satuan Jual Terkecil</label>
                        <?= form_dropdown('satuan_id', ['' => 'Pilih Satuan'] + rows_to_dropdown($satuans), set_value('satuan_id', $data->satuan_id), ['class' => 'form-select', 'id' => 'satuan_id']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="satuan_beli_id" class="form-label">Satuan Pembelian</label>
                        <?= form_dropdown('satuan_beli_id', ['' => 'Pilih Satuan'] + rows_to_dropdown($satuans), set_value('satuan_beli_id', $data->satuan_beli_id), ['class' => 'form-select', 'id' => 'satuan_beli_id']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="satuan_konversi" class="form-label">Konversi Satuan</label>
                        <div class="input-group">
                            <?= form_input(['type' => 'number', 'min' => '1', 'name' => 'satuan_konversi', 'id' => 'satuan_konversi', 'class' => 'form-control', 'value' => set_value('satuan_konversi', $data->satuan_konversi ? $data->satuan_konversi : 1)]) ?>
                            <span class="input-group-text" id="konversiInfo">/ kemasan</span>
                        </div>
                        <div class="form-text">Isi satuan jual terkecil dalam 1 satuan pembelian.</div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="rak_id" class="form-label">Rak</label>
                        <?= form_dropdown('rak_id', ['' => 'Pilih Rak'] + rows_to_dropdown($raks), set_value('rak_id', $data->rak_id), ['class' => 'form-select', 'id' => 'rak_id']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="gudang_id" class="form-label">Gudang</label>
                        <?= form_dropdown('gudang_id', ['' => 'Pilih Gudang'] + rows_to_dropdown($gudangs), set_value('gudang_id', $data->gudang_id), ['class' => 'form-select', 'id' => 'gudang_id']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="pajak_id" class="form-label">Pajak</label>
                        <?= form_dropdown('pajak_id', ['' => 'Pilih Pajak'] + rows_to_dropdown($pajaks), set_value('pajak_id', $data->pajak_id), ['class' => 'form-select', 'id' => 'pajak_id']) ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="indikasi" class="form-label">Indikasi</label>
                <?= form_textarea(['name' => 'indikasi', 'id' => 'indikasi', 'class' => 'form-control', 'rows' => 2, 'value' => set_value('indikasi', $data->indikasi)]) ?>
            </div>

            <hr>
            <h6 class="mb-3">Harga &amp; Margin</h6>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="harga_beli_kemasan" class="form-label">Harga Beli per <span class="label-sat-beli">Kemasan</span></label>
                        <?= form_input(['type' => 'number', 'step' => '0.01', 'min' => '0', 'name' => 'harga_beli_kemasan', 'id' => 'harga_beli_kemasan', 'class' => 'form-control']) ?>
                        <div class="form-text">Opsional, otomatis dibagi konversi.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli per <span class="label-sat-jual">Satuan</span></label>
                        <?= form_input(['type' => 'number', 'step' => '0.01', 'min' => '0', 'name' => 'harga_beli', 'id' => 'harga_beli', 'class' => 'form-control', 'value' => set_value('harga_beli', $data->harga_beli)]) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Total Beli per <span class="label-sat-beli">Kemasan</span></label>
                        <div class="form-control-plaintext fw-bold" id="hargaBeliInfo">-</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Non-Resep</label>
                        <?= form_input(['type' => 'number', 'step' => '1', 'min' => '0', 'name' => 'harga_jual', 'id' => 'harga_jual', 'class' => 'form-control', 'value' => set_value('harga_jual', $data->harga_jual)]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="persentase_keuntungan" class="form-label">Margin Non-Resep</label>
                        <div class="input-group">
                            <?= form_input(['type' => 'number', 'step' => '0.01', 'min' => '0', 'name' => 'persentase_keuntungan', 'id' => 'persentase_keuntungan', 'class' => 'form-control', 'value' => set_value('persentase_keuntungan', $data->persentase_keuntungan)]) ?>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="harga_resep" class="form-label">Harga Resep</label>
                        <?= form_input(['type' => 'number', 'step' => '1', 'min' => '0', 'name' => 'harga_resep', 'id' => 'harga_resep', 'class' => 'form-control', 'value' => set_value('harga_resep', $data->harga_resep)]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="margin_resep" class="form-label">Margin Resep</label>
                        <div class="input-group">
                            <?= form_input(['type' => 'number', 'step' => '0.01', 'min' => '0', 'name' => 'margin_resep', 'id' => 'margin_resep', 'class' => 'form-control', 'value' => set_value('margin_resep', $data->margin_resep)]) ?>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="harga_pelanggan" class="form-label">Harga Pelanggan</label>
                        <?= form_input(['type' => 'number', 'step' => '1', 'min' => '0', 'name' => 'harga_pelanggan', 'id' => 'harga_pelanggan', 'class' => 'form-control', 'value' => set_value('harga_pelanggan', $data->harga_pelanggan)]) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="margin_pelanggan" class="form-label">Margin Pelanggan</label>
                        <div class="input-group">
                            <?= form_input(['type' => 'number', 'step' => '0.01', 'min' => '0', 'name' => 'margin_pelanggan', 'id' => 'margin_pelanggan', 'class' => 'form-control', 'value' => set_value('margin_pelanggan', $data->margin_pelanggan)]) ?>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="expired_date" class="form-label">Tanggal Kadaluarsa</label>
                        <?php
                        $expired_value = $data->expired_date ? date('Y-m-d', strtotime($data->expired_date)) : '';
                        ?>
                        <?= form_input(['type' => 'date', 'name' => 'expired_date', 'id' => 'expired_date', 'class' => 'form-control', 'value' => set_value('expired_date', $expired_value)]) ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="stok_minimal" class="form-label">Stok Minimal</label>
                        <?= form_input(['type' => 'number', 'min' => '0', 'name' => 'stok_minimal', 'id' => 'stok_minimal', 'class' => 'form-control', 'value' => set_value('stok_minimal', $data->stok_minimal)]) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="stok_maksimal" class="form-label">Stok Maksimal</label>
                        <?= form_input(['type' => 'number', 'min' => '0', 'name' => 'stok_maksimal', 'id' => 'stok_maksimal', 'class' => 'form-control', 'value' => set_value('stok_maksimal', $data->stok_maksimal)]) ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <?= form_textarea(['name' => 'deskripsi', 'id' => 'deskripsi', 'class' => 'form-control', 'rows' => 3, 'value' => set_value('deskripsi', $data->deskripsi)]) ?>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <?= form_checkbox(['name' => 'is_active', 'id' => 'is_active', 'class' => 'form-check-input', 'value' => 1, 'checked' => set_checkbox('is_active', 1, $data->is_active == 1)]) ?>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url($back_url) ?>" class="btn btn-secondary">Batal</a>
                <?= form_submit('submit', 'Simpan', ['class' => 'btn btn-primary']) ?>
            </div>
        <?= form_close() ?>
    </div>
</div>

<script>
function loadRakByGudang(gudangId, selectedRakId) {
    var $rak = $('#rak_id');
    $rak.html('<option value="">Pilih Rak</option>');
    if (!gudangId) {
        return;
    }
    $.getJSON(baseUrl + 'masterdata/masterproduk/ajax_rak_by_gudang/' + gudangId, function(res) {
        if (res.status && res.data) {
            $.each(res.data, function(i, rak) {
                var selected = selectedRakId && String(selectedRakId) === String(rak.id) ? ' selected' : '';
                $rak.append('<option value="' + rak.id + '"' + selected + '>' + rak.nama + '</option>');
            });
        }
    });
}

$(document).ready(function() {
    function angka(sel) {
        return parseFloat($(sel).val()) || 0;
    }
    function konversi() {
        var k = angka('#satuan_konversi');
        return k > 0 ? k : 1;
    }

    function updateHargaBeliInfo() {
        var total = angka('#harga_beli') * konversi();
        $('#hargaBeliInfo').text(total > 0 ? 'Rp ' + total.toLocaleString('id-ID') : '-');
    }

    function updateLabelSatuan() {
        var jual = $('#satuan_id option:selected').text();
        var beli = $('#satuan_beli_id option:selected').text();
        $('.label-sat-jual').text($('#satuan_id').val() ? jual : 'Satuan');
        $('.label-sat-beli').text($('#satuan_beli_id').val() ? beli : 'Kemasan');
        $('#konversiInfo').text('/ ' + ($('#satuan_id').val() ? jual : 'satuan'));
        updateHargaBeliInfo();
    }

    // Sinkronisasi dua arah harga <-> margin (dasar: harga beli per satuan terkecil)
    function bindHargaMargin(hargaSel, marginSel) {
        $(marginSel).on('input change', function() {
            var beli = angka('#harga_beli');
            var m = angka(marginSel);
            if (beli > 0) {
                $(hargaSel).val(Math.round(beli + (beli * m / 100)));
            }
        });
        $(hargaSel).on('input change', function() {
            var beli = angka('#harga_beli');
            var harga = angka(hargaSel);
            if (beli > 0 && harga > 0) {
                $(marginSel).val(Math.round(((harga - beli) / beli) * 10000) / 100);
            }
        });
    }

    var pasanganHarga = [
        ['#harga_jual', '#persentase_keuntungan'],
        ['#harga_resep', '#margin_resep'],
        ['#harga_pelanggan', '#margin_pelanggan']
    ];
    $.each(pasanganHarga, function(i, pair) {
        bindHargaMargin(pair[0], pair[1]);
    });

    // Harga beli berubah -> hitung ulang semua harga jual dari margin
    $('#harga_beli').on('input change', function() {
        var beli = angka('#harga_beli');
        if (beli > 0) {
            $.each(pasanganHarga, function(i, pair) {
                var m = angka(pair[1]);
                if (m > 0) {
                    $(pair[0]).val(Math.round(beli + (beli * m / 100)));
                }
            });
        }
        updateHargaBeliInfo();
    });

    // Harga beli per kemasan -> harga beli per satuan terkecil
    $('#harga_beli_kemasan').on('input change', function() {
        var kemasan = angka('#harga_beli_kemasan');
        if (kemasan > 0) {
            $('#harga_beli').val(Math.round((kemasan / konversi()) * 100) / 100).trigger('change');
        }
    });

    $('#satuan_id, #satuan_beli_id, #satuan_konversi').on('input change', updateLabelSatuan);
    updateLabelSatuan();

    $('#gudang_id').on('change', function() {
        loadRakByGudang($(this).val(), '');
    });
    loadRakByGudang($('#gudang_id').val(), '<?= set_value('rak_id', $data->rak_id) ?>');

    // Focus Scan Button
    $('#btnFocusBarcode').on('click', function() {
        $('#barcode').val('').focus();
    });

    // Barcode Scanner listener
    var pressed = false;
    var chars = [];
    
    $(window).on('keypress', function(e) {
        // Intercept keys for barcode scanners (typically fast alphanumeric input)
        if (e.which >= 32 && e.which <= 126) {
            chars.push(String.fromCharCode(e.which));
        }
        
        if (pressed == false) {
            setTimeout(function() {
                // If 5 or more characters are entered in 250ms, it is a barcode scanner
                if (chars.length >= 5) {
                    var barcode = chars.join("");
                    $('#barcode').val(barcode);
                    
                    // Flash notification visual effect
                    var originalBg = $('#barcode').css('background-color');
                    $('#barcode').css('background-color', '#d4edda').focus();
                    setTimeout(function() {
                        $('#barcode').css('background-color', originalBg);
                    }, 1000);

                    // Clean suffix from other active inputs if the scanner typed into them
                    var activeEl = document.activeElement;
                    if (activeEl && activeEl.id !== 'barcode' && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
                        var val = $(activeEl).val();
                        if (val.endsWith(barcode)) {
                            $(activeEl).val(val.substring(0, val.length - barcode.length));
                        }
                    }
                }
                chars = [];
                pressed = false;
            }, 250);
        }
        pressed = true;
    });

    // Prevent enter key inside barcode field from submitting the form (scanner finishes with Enter)
    $('#barcode').on('keydown keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            return false;
        }
    });
    
    // Also globally prevent enter key from submitting the form if it was sent by scanner
    $(window).on('keydown', function(e) {
        if (e.which === 13) {
            if (document.activeElement.id === 'barcode') {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>


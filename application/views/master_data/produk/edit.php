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
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori</label>
                        <?= form_dropdown('kategori_id', ['' => 'Pilih Kategori'] + rows_to_dropdown($kategoris), set_value('kategori_id', $data->kategori_id), ['class' => 'form-select', 'id' => 'kategori_id']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="satuan_id" class="form-label">Satuan</label>
                        <?= form_dropdown('satuan_id', ['' => 'Pilih Satuan'] + rows_to_dropdown($satuans), set_value('satuan_id', $data->satuan_id), ['class' => 'form-select', 'id' => 'satuan_id']) ?>
                    </div>
                </div>
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
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="pajak_id" class="form-label">Pajak</label>
                        <?= form_dropdown('pajak_id', ['' => 'Pilih Pajak'] + rows_to_dropdown($pajaks), set_value('pajak_id', $data->pajak_id), ['class' => 'form-select', 'id' => 'pajak_id']) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="harga_beli" class="form-label">Harga Beli</label>
                        <?= form_input(['type' => 'number', 'step' => '0.01', 'min' => '0', 'name' => 'harga_beli', 'id' => 'harga_beli', 'class' => 'form-control', 'value' => set_value('harga_beli', $data->harga_beli)]) ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="persentase_keuntungan" class="form-label">% Keuntungan</label>
                        <div class="input-group">
                            <?= form_input(['type' => 'number', 'step' => '0.01', 'min' => '0', 'name' => 'persentase_keuntungan', 'id' => 'persentase_keuntungan', 'class' => 'form-control', 'value' => set_value('persentase_keuntungan', $data->persentase_keuntungan)]) ?>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="harga_jual" class="form-label">Harga Jual</label>
                        <?= form_input(['type' => 'number', 'step' => '1', 'min' => '0', 'name' => 'harga_jual', 'id' => 'harga_jual', 'class' => 'form-control', 'value' => set_value('harga_jual', $data->harga_jual), 'readonly' => true]) ?>
                        <div class="form-text">Otomatis: Harga Beli + % Keuntungan (dibulatkan)</div>
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
    // Auto hitung harga jual dari harga beli + persentase keuntungan
    function hitungHargaJual() {
        var beli = parseFloat($('#harga_beli').val()) || 0;
        var persen = parseFloat($('#persentase_keuntungan').val()) || 0;
        var jual = beli + (beli * persen / 100);
        jual = Math.round(jual);
        $('#harga_jual').val(jual);
    }

    $('#harga_beli, #persentase_keuntungan').on('input change', hitungHargaJual);

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


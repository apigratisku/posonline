/**
 * Pesanan Pembelian — form multi-item, alur draft → sent → receive
 */
(function ($) {
    'use strict';

    var poTable = null;
    var produkOptions = [];
    var viewPoId = null;
    var viewPoStatus = null;

    function formatRupiah(num) {
        return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
    }

    function getProdukSelectHtml(selectedId) {
        var html = '<select class="form-select form-select-sm item-produk" required>';
        html += '<option value="">Pilih Produk</option>';
        produkOptions.forEach(function (p) {
            var sel = String(p.id) === String(selectedId) ? ' selected' : '';
            html += '<option value="' + p.id + '" data-harga="' + p.harga_beli + '"' + sel + '>' +
                escapeHtml(p.kode + ' - ' + p.nama) + '</option>';
        });
        html += '</select>';
        return html;
    }

    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function buildItemRow(data) {
        data = data || {};
        var qty = data.qty || 1;
        var harga = data.harga || 0;
        // diskon diisi sebagai persentase (0-100), subtotal dihitung dari nilai rupiahnya
        var diskonPersen = parseFloat(data.diskon_persen) || 0;
        var diskonNominal = (qty * harga) * (diskonPersen / 100);
        var subtotal = (qty * harga) - diskonNominal;

        var row = '<tr class="po-item-row">' +
            '<td>' + getProdukSelectHtml(data.produk_id) + '</td>' +
            '<td><input type="number" class="form-control form-control-sm item-qty" min="1" step="1" value="' + qty + '" required></td>' +
            '<td><input type="number" class="form-control form-control-sm item-harga" min="0" step="1" value="' + harga + '" required></td>' +
            '<td><div class="input-group input-group-sm"><input type="number" class="form-control item-diskon" min="0" max="100" step="0.01" value="' + diskonPersen + '"><span class="input-group-text">%</span></div></td>' +
            '<td class="item-subtotal text-end align-middle">' + formatRupiah(subtotal) + '</td>' +
            '<td class="text-center align-middle"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" title="Hapus"><i class="fas fa-times"></i></button></td>' +
            '</tr>';
        return row;
    }

    // Ambil persentase diskon sebuah baris (0-100)
    function getRowDiskonPersen($row) {
        return Math.min(Math.max(parseFloat($row.find('.item-diskon').val()) || 0, 0), 100);
    }

    // Konversi persentase diskon baris ke nominal rupiah
    function getRowDiskonNominal($row) {
        var qty = parseFloat($row.find('.item-qty').val()) || 0;
        var harga = parseFloat($row.find('.item-harga').val()) || 0;
        return (qty * harga) * (getRowDiskonPersen($row) / 100);
    }

    // Ambil persentase diskon global (0-100)
    function getGlobalDiskonPersen() {
        return Math.min(Math.max(parseFloat($('#diskon').val()) || 0, 0), 100);
    }

    function recalcTotals() {
        var subtotal = 0;
        $('#poItemsBody .po-item-row').each(function () {
            var $row = $(this);
            var qty = parseFloat($row.find('.item-qty').val()) || 0;
            var harga = parseFloat($row.find('.item-harga').val()) || 0;
            var lineSub = (qty * harga) - getRowDiskonNominal($row);
            $row.find('.item-subtotal').text(formatRupiah(lineSub));
            subtotal += lineSub;
        });

        var diskonGlobal = subtotal * (getGlobalDiskonPersen() / 100);
        var pajak = parseFloat($('#pajak').val()) || 0;
        var total = subtotal - diskonGlobal + pajak;

        $('#poSubtotalDisplay').text(formatRupiah(subtotal));
        $('#poTotalDisplay').text(formatRupiah(total));
    }

    function collectItems() {
        var items = [];
        var valid = true;

        $('#poItemsBody .po-item-row').each(function () {
            var $row = $(this);
            var produkId = $row.find('.item-produk').val();
            var qty = parseFloat($row.find('.item-qty').val()) || 0;
            var harga = parseFloat($row.find('.item-harga').val()) || 0;
            var diskon = getRowDiskonNominal($row);

            if (!produkId || qty <= 0) {
                valid = false;
                return false;
            }

            items.push({
                produk_id: parseInt(produkId, 10),
                qty: qty,
                harga: harga,
                diskon: diskon,
                diskon_persen: getRowDiskonPersen($row)
            });
        });

        if (!valid || items.length === 0) {
            return null;
        }
        return items;
    }

    function resetPoForm() {
        $('#pesananPembelianForm')[0].reset();
        $('#po_id').val('');
        $('#no_po').val('');
        $('#po_status').val('draft');
        $('#tanggal').val(new Date().toISOString().split('T')[0]);
        $('#poItemsBody').empty();
        addItemRow();
        recalcTotals();
    }

    window.addItemRow = function (data) {
        $('#poItemsBody').append(buildItemRow(data));
        recalcTotals();
    };

    window.addData = function () {
        $('#poModalTitle').text('Tambah Pesanan Pembelian');
        resetPoForm();
        $('#pesananPembelianModal').modal('show');
    };

    window.editData = function (id) {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/pesanan/ajax_get/' + id,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                hideLoading();
                if (!response.status) {
                    showNotification('error', response.message);
                    return;
                }

                var po = response.data.po;
                var details = response.data.details;

                if (po.status !== 'draft') {
                    showNotification('error', 'Hanya pesanan draft yang dapat diedit');
                    return;
                }

                $('#poModalTitle').text('Edit Pesanan Pembelian');
                $('#po_id').val(po.id);
                $('#no_po').val(po.no_po);
                $('#supplier_id').val(po.supplier_id);
                $('#gudang_id').val(po.gudang_id);
                $('#tanggal').val(po.tanggal);
                $('#keterangan').val(po.keterangan || '');
                $('#diskon').val(po.subtotal > 0 ? ((parseFloat(po.diskon) || 0) / parseFloat(po.subtotal)) * 100 : 0);
                $('#pajak').val(po.pajak || 0);

                $('#poItemsBody').empty();
                if (details.length) {
                    details.forEach(function (d) {
                        addItemRow({
                            produk_id: d.produk_id,
                            qty: d.qty,
                            harga: d.harga,
                            // DB menyimpan nominal, tampilkan kembali sebagai persentase dari harga beli
                            diskon_persen: (parseFloat(d.harga) > 0 && parseFloat(d.qty) > 0)
                                ? ((parseFloat(d.diskon) || 0) / (parseFloat(d.harga) * parseFloat(d.qty))) * 100
                                : 0
                        });
                    });
                } else {
                    addItemRow();
                }
                recalcTotals();
                $('#pesananPembelianModal').modal('show');
            },
            error: function () {
                hideLoading();
                showNotification('error', 'Gagal mengambil data');
            }
        });
    };

    window.viewData = function (id) {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/pesanan/ajax_get/' + id,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                hideLoading();
                if (!response.status) {
                    showNotification('error', response.message);
                    return;
                }

                var po = response.data.po;
                var details = response.data.details;
                viewPoId = po.id;
                viewPoStatus = po.status;

                $('#viewNoPo').text(po.no_po);
                $('#viewStatus').html('<span class="badge bg-secondary">' + String(po.status).toUpperCase() + '</span>');
                $('#viewSupplier').text(po.supplier_nama || '-');
                $('#viewGudang').text(po.gudang_nama || '-');
                $('#viewTanggal').text(po.tanggal ? new Date(po.tanggal).toLocaleDateString('id-ID') : '-');
                $('#viewKeterangan').text(po.keterangan || '-');
                $('#viewTotal').text(formatRupiah(po.total));

                var rows = '';
                details.forEach(function (d) {
                    rows += '<tr>' +
                        '<td>' + escapeHtml((d.produk_kode || '') + ' - ' + (d.produk_nama || '')) + '</td>' +
                        '<td class="text-end">' + Number(d.qty).toLocaleString('id-ID') + '</td>' +
                        '<td class="text-end">' + formatRupiah(d.harga) + '</td>' +
                        '<td class="text-end">' + (parseFloat(d.harga) > 0 && parseFloat(d.qty) > 0
                            ? (((parseFloat(d.diskon) || 0) / (parseFloat(d.harga) * parseFloat(d.qty))) * 100).toFixed(2) + '%'
                            : '0%') + '</td>' +
                        '<td class="text-end">' + formatRupiah(d.subtotal) + '</td>' +
                        '</tr>';
                });
                $('#viewItemsBody').html(rows);

                if (po.status === 'sent') {
                    $('#btnReceiveFromView').removeClass('d-none');
                } else {
                    $('#btnReceiveFromView').addClass('d-none');
                }

                $('#pesananPembelianViewModal').modal('show');
            },
            error: function () {
                hideLoading();
                showNotification('error', 'Gagal mengambil data');
            }
        });
    };

    window.submitPoForm = function (status) {
        var items = collectItems();
        if (!items) {
            showNotification('error', 'Lengkapi minimal satu item barang dengan qty valid');
            return;
        }

        $('#po_status').val(status);

        var formData = new FormData($('#pesananPembelianForm')[0]);
        formData.set('items', JSON.stringify(items));

        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/pesanan/ajax_save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    $('#pesananPembelianModal').modal('hide');
                    reloadDataTable('pesananpembelianTable');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function () {
                hideLoading();
                showNotification('error', 'Gagal menyimpan data');
            }
        });
    };

    window.sendData = function (id) {
        showConfirm('Kirim pesanan pembelian ini? Setelah dikirim, pesanan siap diterima.', function () {
            showLoading();
            $.ajax({
                url: baseUrl + 'pembelian/pesanan/ajax_send/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    hideLoading();
                    if (response.status) {
                        showNotification('success', response.message);
                        reloadDataTable('pesananpembelianTable');
                    } else {
                        showNotification('error', response.message);
                    }
                },
                error: function () {
                    hideLoading();
                    showNotification('error', 'Gagal mengirim pesanan');
                }
            });
        });
    };

    window.receiveData = function (id) {
        showConfirm('Terima barang pembelian ini? Stok akan ditambahkan ke gudang.', function () {
            doReceive(id);
        });
    };

    window.receiveFromView = function () {
        if (!viewPoId) return;
        showConfirm('Terima barang pembelian ini? Stok akan ditambahkan ke gudang.', function () {
            doReceive(viewPoId, true);
        });
    };

    function doReceive(id, closeViewModal) {
        showLoading();
        $.ajax({
            url: baseUrl + 'pembelian/pesanan/ajax_receive/' + id,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                hideLoading();
                if (response.status) {
                    showNotification('success', response.message);
                    if (closeViewModal) {
                        $('#pesananPembelianViewModal').modal('hide');
                    }
                    reloadDataTable('pesananpembelianTable');
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function () {
                hideLoading();
                showNotification('error', 'Gagal menerima pesanan');
            }
        });
    }

    $(document).ready(function () {
        var produkJson = $('#poProdukOptions').text();
        if (produkJson) {
            try {
                produkOptions = JSON.parse(produkJson);
            } catch (e) {
                produkOptions = [];
            }
        }

        poTable = initDataTable('pesananpembelianTable', {
            ajax: { url: baseUrl + 'pembelian/pesanan/ajax_list', type: 'POST' },
            columns: [
                { data: 0 },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4, className: 'text-end' },
                { data: 5 },
                { data: 6, orderable: false, searchable: false }
            ]
        });

        $(document).on('change', '.item-produk', function () {
            var harga = $(this).find(':selected').data('harga') || 0;
            $(this).closest('tr').find('.item-harga').val(harga);
            recalcTotals();
        });

        $(document).on('input', '.item-qty, .item-harga, .item-diskon, #diskon, #pajak', recalcTotals);

        $(document).on('click', '.btn-remove-item', function () {
            if ($('#poItemsBody .po-item-row').length <= 1) {
                showNotification('warning', 'Minimal satu item diperlukan');
                return;
            }
            $(this).closest('tr').remove();
            recalcTotals();
        });

        $('#pesananPembelianModal').on('hidden.bs.modal', function () {
            resetPoForm();
        });
    });
})(jQuery);

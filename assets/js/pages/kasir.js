var cart = [];
var gudangId = null;
var searchResults = [];
var searchActiveIndex = -1;
var searchDebounceTimer = null;
var searchRequestId = 0;

// Hardware barcode scanner tracking
// Scanner types very fast (< 50ms between keystrokes) then sends Enter.
var _lastKeyTime = 0;
var _scanKeyCount = 0;
var _scanMode = false; // true saat deteksi input dari scanner

function formatCurrency(amount) {
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
}

// Deteksi apakah input berasal dari hardware scanner.
// Scanner mengetik sangat cepat (< 80ms per karakter) lalu Enter.
// Kode produk bisa berupa alfanumerik dengan panjang berapapun.
function isBarcodeInput(term) {
    // Jika scan mode aktif (terdeteksi dari kecepatan ketik), anggap barcode
    if (_scanMode) return true;
    // Numeric barcode panjang (EAN-8, EAN-13, UPC)
    if (/^\d{8,}$/.test(term)) return true;
    // Kode alfanumerik tanpa spasi >= 3 karakter juga bisa langsung dicari by kode
    if (/^[A-Za-z0-9\-\_]{3,}$/.test(term)) return true;
    return false;
}

function hideSearchDropdown() {
    searchActiveIndex = -1;
    $('#posSearchDropdown').attr('hidden', true).empty().removeClass('is-loading is-empty is-hint');
}

function showSearchDropdownMessage(type, message) {
    var dropdown = $('#posSearchDropdown');
    dropdown.removeClass('is-loading is-empty is-hint').addClass(type);
    dropdown.html(escapeHtml(message));
    dropdown.removeAttr('hidden');
}

function renderSearchDropdown(results) {
    searchResults = results || [];
    searchActiveIndex = searchResults.length > 0 ? 0 : -1;

    var dropdown = $('#posSearchDropdown');
    dropdown.removeClass('is-loading is-empty is-hint');

    if (!searchResults.length) {
        showSearchDropdownMessage('is-empty', 'Produk tidak ditemukan');
        return;
    }

    var html = '';
    searchResults.forEach(function (produk, index) {
        var stok = parseInt(produk.stok, 10) || 0;
        var satuan = produk.satuan_nama || produk.satuan || '-';
        var outOfStock = stok <= 0;
        var activeClass = index === searchActiveIndex ? ' is-active' : '';
        var stockClass = outOfStock ? ' is-empty' : '';
        var konversi = parseInt(produk.satuan_konversi, 10) || 1;
        var satuanBeli = produk.satuan_beli_nama || '';

        html += '<div class="pos-search-item' + activeClass + (outOfStock ? ' is-out-of-stock' : '') + '" data-index="' + index + '" role="option">';
        html += '<div class="pos-search-item-main">';
        html += '<div class="pos-search-item-name">' + escapeHtml(produk.nama || 'Produk') + '</div>';
        html += '<div class="pos-search-item-code">' + escapeHtml(produk.kode || '-') + '</div>';
        html += '</div>';
        html += '<div class="pos-search-item-meta">';
        html += '<div class="pos-search-item-price">' + formatCurrency(getProdukHarga(produk)) + '</div>';
        html += '<span class="pos-search-item-stock' + stockClass + '">Stok: ' + stok + ' ' + escapeHtml(satuan) + '</span>';
        if (konversi > 1 && satuanBeli && satuanBeli !== '-') {
            html += '<span class="pos-search-item-konversi">1 ' + escapeHtml(satuanBeli) + ' = ' + konversi + ' ' + escapeHtml(satuan) + '</span>';
        }
        html += '</div>';
        html += '</div>';
    });

    dropdown.html(html);
    dropdown.removeAttr('hidden');
}

function highlightSearchItem(index) {
    if (!searchResults.length) return;
    if (index < 0) index = searchResults.length - 1;
    if (index >= searchResults.length) index = 0;
    searchActiveIndex = index;
    $('#posSearchDropdown .pos-search-item').removeClass('is-active');
    var activeItem = $('#posSearchDropdown .pos-search-item[data-index="' + index + '"]');
    activeItem.addClass('is-active');
    if (activeItem.length) {
        var dropdown = $('#posSearchDropdown')[0];
        var item = activeItem[0];
        if (item.offsetTop < dropdown.scrollTop) {
            dropdown.scrollTop = item.offsetTop;
        } else if (item.offsetTop + item.offsetHeight > dropdown.scrollTop + dropdown.clientHeight) {
            dropdown.scrollTop = item.offsetTop + item.offsetHeight - dropdown.clientHeight;
        }
    }
}

function selectSearchResult(index) {
    if (index < 0 || index >= searchResults.length) return;
    addToCart(searchResults[index]);
    hideSearchDropdown();
    searchResults = [];
}

function searchProdukLive(term, force) {
    term = (term || '').trim();
    if (!term) {
        hideSearchDropdown();
        return;
    }
    if (!gudangId) {
        showSearchDropdownMessage('is-hint', 'Gudang transaksi belum ditetapkan');
        return;
    }
    if (!force && term.length < 2) {
        hideSearchDropdown();
        return;
    }

    var requestId = ++searchRequestId;
    showSearchDropdownMessage('is-loading', 'Mencari produk...');

    $.ajax({
        url: baseUrl + 'penjualan/kasir/ajax_search_produk',
        type: 'GET',
        data: { q: term, gudang_id: gudangId },
        dataType: 'json',
        timeout: 10000,
        success: function (response) {
            if (requestId !== searchRequestId) return;
            if (response && response.results) {
                renderSearchDropdown(response.results);
            } else {
                showSearchDropdownMessage('is-empty', 'Produk tidak ditemukan');
            }
        },
        error: function (xhr, status) {
            if (requestId !== searchRequestId) return;
            var errorMsg = status === 'timeout' ? 'Waktu pencarian habis.' : 'Gagal mencari produk';
            if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
            showSearchDropdownMessage('is-empty', errorMsg);
        }
    });
}

function scheduleSearchProdukLive(term) {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(function () {
        searchProdukLive(term, false);
    }, 300);
}

function syncPosCartUI() {
    var count = cart.reduce(function (sum, item) { return sum + item.qty; }, 0);
    $(document).trigger('cart:updated', [count]);
    $('#posCartCount').text(count);
    $('#posCheckoutTotal').text(formatCurrency(getGrandTotal()));
    $('#posCheckoutBtn').prop('disabled', cart.length === 0);
}

function getSubtotal() {
    return cart.reduce(function (sum, item) {
        return sum + (parseFloat(item.subtotal) || 0);
    }, 0);
}

function getGrandTotal() {
    var subtotal = getSubtotal();
    var diskon = parseFloat($('#diskon').val()) || 0;
    var pajak = parseFloat($('#pajak').val()) || 0;
    var total = subtotal - diskon + pajak;
    return total < 0 ? 0 : total;
}

function searchProduk() {
    var term = $('#searchProduk').val().trim();
    if (!term) {
        showNotification('warning', 'Masukkan kode atau nama produk');
        $('#searchProduk').focus();
        return;
    }
    if (!gudangId) {
        showNotification('warning', 'Gudang transaksi belum ditetapkan. Hubungi administrator.');
        return;
    }
    searchProdukLive(term, true);
}

function searchByBarcode(barcode, options) {
    options = options || {};
    if (!barcode || barcode.trim() === '') return;
    if (!gudangId) {
        showNotification('warning', 'Gudang transaksi belum ditetapkan. Hubungi administrator.');
        return;
    }
    if (!options.silent) showLoading();
    $.ajax({
        url: baseUrl + 'penjualan/kasir/ajax_get_produk_by_barcode',
        type: 'POST',
        data: { barcode: barcode.trim(), gudang_id: gudangId },
        dataType: 'json',
        timeout: 10000,
        success: function (response) {
            if (!options.silent) hideLoading();
            if (response && response.status && response.data) {
                addToCart(response.data);
                hideSearchDropdown();
            } else if (!options.silent) {
                showNotification('error', response.message || 'Produk tidak ditemukan');
                $('#searchProduk').focus();
            } else if (options.fallbackSearch) {
                searchProdukLive(barcode, true);
            }
        },
        error: function (xhr, status) {
            if (!options.silent) hideLoading();
            if (options.fallbackSearch) {
                searchProdukLive(barcode, true);
                return;
            }
            var errorMsg = status === 'timeout' ? 'Waktu pencarian habis.' : 'Gagal mencari produk';
            if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
            showNotification('error', errorMsg);
            $('#searchProduk').focus();
        }
    });
}

function handleSearchEnter() {
    var term = $('#searchProduk').val().trim();
    if (!term) return;

    if (isBarcodeInput(term)) {
        searchByBarcode(term, { silent: true, fallbackSearch: true });
        return;
    }

    if (!$('#posSearchDropdown').is('[hidden]') && searchResults.length > 0) {
        var index = searchActiveIndex >= 0 ? searchActiveIndex : 0;
        selectSearchResult(index);
        return;
    }

    searchProdukLive(term, true);
}

/**
 * Susun daftar satuan jual yang tersedia untuk sebuah produk.
 * Harga tiap satuan dihitung otomatis dari harga satuan terkecil.
 */
function buildSatuanOptions(produk) {
    var konversi = parseInt(produk.satuan_konversi, 10) || 1;
    if (konversi < 1) konversi = 1;
    var satuanId = produk.satuan_id ? parseInt(produk.satuan_id, 10) : null;
    var satuanBeliId = produk.satuan_beli_id ? parseInt(produk.satuan_beli_id, 10) : null;
    var options = [];

    options.push({
        id: satuanId,
        nama: produk.satuan_nama || produk.satuan || 'Satuan',
        konversi: 1
    });

    if (satuanBeliId && konversi > 1 && satuanBeliId !== satuanId) {
        options.push({
            id: satuanBeliId,
            nama: produk.satuan_beli_nama || 'Kemasan',
            konversi: konversi
        });
    }

    return options;
}

/**
 * Jenis harga aktif pada halaman kasir.
 */
function getTipeHarga() {
    return $('#tipeHarga').val() || 'non_resep';
}

/**
 * Ambil harga dasar (satuan terkecil) sesuai jenis harga terpilih.
 * Jika harga jenis tersebut kosong/0, fallback ke harga non-resep.
 */
function getHargaBase(item, tipe) {
    tipe = tipe || getTipeHarga();
    var map = item.harga_base || {};
    var harga = parseFloat(map[tipe]);
    if (!harga || harga <= 0) {
        harga = parseFloat(map.non_resep) || 0;
    }
    return harga;
}

/**
 * Harga produk (satuan terkecil) sesuai jenis harga terpilih.
 */
function getProdukHarga(produk, tipe) {
    return getHargaBase({
        harga_base: {
            non_resep: parseFloat(produk.harga_jual) || 0,
            resep: parseFloat(produk.harga_resep) || 0,
            pelanggan: parseFloat(produk.harga_pelanggan) || 0
        }
    }, tipe);
}

/**
 * Harga per satuan terpilih = harga dasar x konversi satuan.
 */
function getHargaSatuan(item, konversi, tipe) {
    konversi = parseInt(konversi, 10) || 1;
    if (konversi < 1) konversi = 1;
    return getHargaBase(item, tipe) * konversi;
}

/**
 * Terapkan jenis harga ke seluruh item keranjang dan hitung ulang total.
 */
function applyTipeHarga(tipe) {
    tipe = tipe || getTipeHarga();
    cart.forEach(function (item) {
        item.harga = getHargaSatuan(item, item.konversi, tipe);
        item.subtotal = (item.qty * item.harga) - item.diskon;
    });
    renderCart();
    calculateTotal();
}

function findSatuanOption(item, satuanId) {
    var options = item.satuan_options || [];
    for (var i = 0; i < options.length; i++) {
        if (String(options[i].id) === String(satuanId)) return options[i];
    }
    return null;
}

function maxQtyForSatuan(stok, konversi) {
    konversi = parseInt(konversi, 10) || 1;
    if (konversi < 1) konversi = 1;
    return Math.floor((parseInt(stok, 10) || 0) / konversi);
}

function addToCart(produk) {
    if (!produk || !produk.id) {
        showNotification('error', 'Data produk tidak valid');
        return;
    }
    if (!produk.stok || produk.stok <= 0) {
        showNotification('error', 'Stok produk habis');
        return;
    }
    if (!getProdukHarga(produk) || getProdukHarga(produk) <= 0) {
        showNotification('error', 'Harga produk tidak valid');
        return;
    }
    var options = buildSatuanOptions(produk);
    var defaultSatuan = options[0];
    var existingIndex = cart.findIndex(function (item) {
        return item.produk_id === produk.id && String(item.satuan_id) === String(defaultSatuan.id);
    });
    if (existingIndex >= 0) {
        var existing = cart[existingIndex];
        var maxQty = maxQtyForSatuan(existing.stok, existing.konversi);
        var newQty = existing.qty + 1;
        if (newQty > maxQty) {
            showNotification('error', 'Stok tidak mencukupi. Stok tersedia: ' + maxQty + ' ' + (existing.satuan_nama || ''));
            return;
        }
        existing.qty = newQty;
        existing.subtotal = (existing.qty * existing.harga) - existing.diskon;
    } else {
        var harga_base = {
            non_resep: parseFloat(produk.harga_jual) || 0,
            resep: parseFloat(produk.harga_resep) || 0,
            pelanggan: parseFloat(produk.harga_pelanggan) || 0
        };
        var newItem = {
            produk_id: produk.id,
            kode: produk.kode || '',
            nama: produk.nama || 'Produk',
            satuan_id: defaultSatuan.id,
            satuan_nama: defaultSatuan.nama,
            konversi: defaultSatuan.konversi,
            satuan_options: options,
            harga_base: harga_base,
            qty: 1,
            harga: 0,
            diskon: 0,
            subtotal: 0,
            stok: parseInt(produk.stok) || 0
        };
        newItem.harga = getHargaSatuan(newItem, newItem.konversi);
        newItem.subtotal = newItem.harga;
        cart.push(newItem);
    }
    renderCart();
    calculateTotal();
    $('#searchProduk').val('').focus();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
    calculateTotal();
}

function updateQty(index, qty) {
    if (index < 0 || index >= cart.length) return;
    qty = parseInt(qty) || 0;
    if (qty <= 0) {
        removeFromCart(index);
        return;
    }
    var maxQty = maxQtyForSatuan(cart[index].stok, cart[index].konversi);
    if (qty > maxQty) {
        showNotification('error', 'Stok tidak mencukupi. Stok tersedia: ' + maxQty + ' ' + (cart[index].satuan_nama || ''));
        qty = maxQty;
        if (qty <= 0) {
            removeFromCart(index);
            return;
        }
    }
    cart[index].qty = qty;
    cart[index].subtotal = (cart[index].qty * cart[index].harga) - cart[index].diskon;
    renderCart();
    calculateTotal();
}

/**
 * Ganti satuan jual item keranjang (mis. Strip -> Box).
 * Harga, subtotal, dan batas stok langsung menyesuaikan satuan terpilih.
 */
function changeSatuan(index, satuanId) {
    if (index < 0 || index >= cart.length) return;
    var item = cart[index];
    var opt = findSatuanOption(item, satuanId);
    if (!opt) return;
    if (String(opt.id) === String(item.satuan_id)) return;

    var maxQty = maxQtyForSatuan(item.stok, opt.konversi);
    if (maxQty < 1) {
        showNotification('error', 'Stok tidak mencukupi untuk satuan ' + opt.nama);
        renderCart();
        return;
    }

    // Gabung bila sudah ada baris lain dengan produk & satuan yang sama
    var dupIndex = -1;
    for (var i = 0; i < cart.length; i++) {
        if (i !== index && cart[i].produk_id === item.produk_id && String(cart[i].satuan_id) === String(opt.id)) {
            dupIndex = i;
            break;
        }
    }

    item.satuan_id = opt.id;
    item.satuan_nama = opt.nama;
    item.konversi = opt.konversi;
    item.harga = getHargaSatuan(item, opt.konversi);
    item.qty = Math.min(item.qty, maxQty);
    item.subtotal = (item.qty * item.harga) - item.diskon;

    if (dupIndex >= 0) {
        var dup = cart[dupIndex];
        var dupMax = maxQtyForSatuan(dup.stok, dup.konversi);
        var mergedQty = Math.min(dup.qty + item.qty, dupMax);
        dup.qty = mergedQty;
        dup.subtotal = (dup.qty * dup.harga) - dup.diskon;
        cart.splice(index, 1);
    }

    renderCart();
    calculateTotal();
}

function renderCart() {
    var container = $('#posCartList');
    var html = '';
    if (cart.length === 0) {
        html = '<div class="pos-empty">' +
            '<i class="fas fa-barcode"></i>' +
            '<h6>Keranjang Kosong</h6>' +
            '<p>Scan barcode atau cari nama obat untuk memulai transaksi</p>' +
            '</div>';
    } else {
        cart.forEach(function (item, index) {
            var satuanNama = item.satuan_nama || '-';
            var options = item.satuan_options || [];
            html += '<div class="pos-cart-item" data-index="' + index + '">';
            html += '<div>';
            html += '<div class="pos-cart-item-name">' + escapeHtml(item.nama) + '</div>';
            if (options.length > 1) {
                html += '<select class="pos-cart-item-satuan" onchange="changeSatuan(' + index + ', this.value)" aria-label="Satuan">';
                options.forEach(function (opt) {
                    var selected = String(opt.id) === String(item.satuan_id) ? ' selected' : '';
                    html += '<option value="' + opt.id + '"' + selected + '>' +
                        escapeHtml(opt.nama) + ' &middot; ' + formatCurrency(getHargaSatuan(item, opt.konversi)) + '</option>';
                });
                html += '</select>';
            } else {
                html += '<div class="pos-cart-item-code">Satuan: ' + escapeHtml(satuanNama) + '</div>';
            }
            html += '<div class="pos-cart-item-code">' + escapeHtml(item.kode) + ' &middot; ' + formatCurrency(item.harga) + ' / ' + escapeHtml(satuanNama) + '</div>';
            html += '</div>';
            html += '<button type="button" class="pos-cart-item-delete" onclick="removeFromCart(' + index + ')" aria-label="Hapus"><i class="fas fa-trash"></i></button>';
            html += '<div class="pos-cart-item-footer">';
            html += '<div class="pos-qty-control">';
            html += '<button type="button" class="pos-qty-btn" onclick="updateQty(' + index + ', ' + (item.qty - 1) + ')">−</button>';
            html += '<span class="pos-qty-value">' + item.qty + '</span>';
            html += '<button type="button" class="pos-qty-btn" onclick="updateQty(' + index + ', ' + (item.qty + 1) + ')">+</button>';
            html += '</div>';
            html += '<div class="pos-cart-item-subtotal">' + formatCurrency(item.subtotal) + '</div>';
            html += '</div>';
            html += '</div>';
        });
    }
    container.html(html);
    syncPosCartUI();
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function calculateTotal() {
    var subtotal = getSubtotal();
    var diskon = parseFloat($('#diskon').val()) || 0;
    var pajak = parseFloat($('#pajak').val()) || 0;
    if (diskon < 0) { diskon = 0; $('#diskon').val(0); }
    if (pajak < 0) { pajak = 0; $('#pajak').val(0); }
    if (diskon > subtotal) {
        diskon = subtotal;
        $('#diskon').val(diskon);
        showNotification('warning', 'Diskon disesuaikan ke subtotal');
    }
    var total = subtotal - diskon + pajak;
    if (total < 0) total = 0;
    $('#subtotalDisplay').text(formatCurrency(subtotal));
    $('#totalDisplay').text(formatCurrency(total));
    $('#posCheckoutTotal').text(formatCurrency(total));
    calculateKembalian();
    syncPosCartUI();
}

function calculateKembalian() {
    var total = getGrandTotal();
    var bayar = parseFloat($('#bayar').val()) || 0;
    var kembalian = bayar - total;
    $('#kembalianDisplay').text(formatCurrency(kembalian));
    var row = $('#kembalianRow');
    row.removeClass('positive negative');
    if (kembalian < 0) {
        row.addClass('negative');
    } else {
        row.addClass('positive');
    }
}

function handleBayarFocus() {
    var val = $('#bayar').val();
    if (val === '0' || parseFloat(val) === 0) {
        $('#bayar').val('');
    }
}

function handleBayarInput() {
    var $el = $('#bayar');
    var val = $el.val();
    if (val.length > 1 && /^0\d/.test(val)) {
        val = val.replace(/^0+/, '') || '';
        $el.val(val);
    }
    calculateKembalian();
}

function openPaymentSheet() {
    if (cart.length === 0) {
        showNotification('error', 'Keranjang masih kosong');
        return;
    }
    calculateTotal();
    $('#bayar').val('');
    calculateKembalian();
    $('#posPaymentOverlay').addClass('is-open');
    $('#posPaymentSheet').addClass('is-open');
    $('body').css('overflow', 'hidden');
}

function closePaymentSheet() {
    $('#posPaymentOverlay').removeClass('is-open');
    $('#posPaymentSheet').removeClass('is-open');
    $('body').css('overflow', '');
}

function setQuickPay(amount) {
    $('#bayar').val(amount);
    calculateKembalian();
}

function setExactPay() {
    var total = getGrandTotal();
    $('#bayar').val(total);
    calculateKembalian();
}

function processPayment() {
    if (cart.length === 0) {
        showNotification('error', 'Keranjang masih kosong');
        return;
    }
    var gudangIdVal = $('#gudang_id').val();
    if (!gudangIdVal) {
        showNotification('error', 'Gudang transaksi belum ditetapkan. Hubungi administrator.');
        return;
    }
    var metodePembayaranId = $('#metode_pembayaran_id').val();
    if (!metodePembayaranId) {
        showNotification('error', 'Pilih metode pembayaran terlebih dahulu');
        $('#metode_pembayaran_id').focus();
        return;
    }
    for (var i = 0; i < cart.length; i++) {
        if (!cart[i].produk_id || !cart[i].qty || cart[i].qty <= 0) {
            showNotification('error', 'Data item tidak valid pada item ke-' + (i + 1));
            return;
        }
        var konversi = parseInt(cart[i].konversi, 10) || 1;
        if (konversi < 1) konversi = 1;
        if (cart[i].qty * konversi > cart[i].stok) {
            showNotification('error', 'Stok tidak mencukupi untuk produk: ' + cart[i].nama);
            return;
        }
    }
    var total = getGrandTotal();
    var bayar = parseFloat($('#bayar').val()) || 0;
    if (bayar <= 0) {
        showNotification('error', 'Jumlah pembayaran harus lebih dari 0');
        $('#bayar').focus();
        return;
    }
    if (bayar < total) {
        showNotification('error', 'Jumlah pembayaran kurang. Total: ' + formatCurrency(total));
        $('#bayar').focus();
        return;
    }
    var formData = {
        gudang_id: gudangIdVal,
        pelanggan_id: $('#pelanggan_id').val() || null,
        sales_id: $('#sales_id').val() || null,
        shift_id: $('#shift_id').val() || null,
        metode_pembayaran_id: metodePembayaranId,
        diskon: parseFloat($('#diskon').val()) || 0,
        pajak: parseFloat($('#pajak').val()) || 0,
        bayar: bayar,
        keterangan: $('#keterangan').val() || '',
        items: JSON.stringify(cart)
    };
    showLoading();
    $('#btnProcessPayment').prop('disabled', true);
    $.ajax({
        url: baseUrl + 'penjualan/kasir/ajax_save',
        type: 'POST',
        data: formData,
        dataType: 'json',
        timeout: 30000,
        success: function (response) {
            hideLoading();
            $('#btnProcessPayment').prop('disabled', false);
            if (response.status) {
                showNotification('success', response.message);
                closePaymentSheet();
                if (response.data && response.data.id) {
                    var saleId = response.data.id;
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Cetak struk?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#667eea',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            printSaleReceipt(saleId);
                        }
                        resetCart();
                    });
                } else {
                    resetCart();
                }
            } else {
                var errorMsg = response.message || 'Gagal memproses pembayaran';
                if (response.errors) errorMsg += ': ' + Object.values(response.errors).join(', ');
                showNotification('error', errorMsg);
            }
        },
        error: function (xhr, status) {
            hideLoading();
            $('#btnProcessPayment').prop('disabled', false);
            var errorMsg = status === 'timeout' ? 'Waktu permintaan habis.' : 'Gagal memproses pembayaran';
            if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
            showNotification('error', errorMsg);
        }
    });
}

function resetCart() {
    cart = [];
    renderCart();
    calculateTotal();
    $('#paymentForm')[0].reset();
    $('#searchProduk').focus();
}

$(document).ready(function () {
    var assignedGudang = $('#gudang_id').val();
    if (assignedGudang) {
        gudangId = assignedGudang;
    }
    $('#searchProduk').on('keydown', function (e) {
        var dropdownOpen = !$('#posSearchDropdown').is('[hidden]');

        // Deteksi hardware scanner: jika interval antar tombol < 80ms, aktifkan scan mode
        var now = Date.now();
        var interval = now - _lastKeyTime;
        _lastKeyTime = now;

        if (e.which !== 13 && e.which !== 27 && e.which !== 38 && e.which !== 40) {
            if (interval < 80) {
                _scanKeyCount++;
                if (_scanKeyCount >= 2) {
                    _scanMode = true;
                }
            } else {
                // Ketik lambat = manual, reset scan mode
                _scanKeyCount = 0;
                _scanMode = false;
            }
            // Reset scan mode setelah 300ms tidak ada ketukan
            clearTimeout(window._scanModeTimer);
            window._scanModeTimer = setTimeout(function () {
                _scanMode = false;
                _scanKeyCount = 0;
            }, 300);
        }

        if (e.which === 40 && dropdownOpen && searchResults.length > 0) {
            e.preventDefault();
            highlightSearchItem(searchActiveIndex + 1);
            return;
        }
        if (e.which === 38 && dropdownOpen && searchResults.length > 0) {
            e.preventDefault();
            highlightSearchItem(searchActiveIndex - 1);
            return;
        }
        if (e.which === 27) {
            hideSearchDropdown();
            _scanMode = false;
            _scanKeyCount = 0;
            return;
        }
        if (e.which === 13) {
            e.preventDefault();
            handleSearchEnter();
            // Reset scan mode setelah Enter
            setTimeout(function () { _scanMode = false; _scanKeyCount = 0; }, 50);
        }
    });
    $('#searchProduk').on('input', function () {
        if (!_scanMode) {
            scheduleSearchProdukLive($(this).val());
        }
    });

    $('#posSearchDropdown').on('click', '.pos-search-item', function () {
        var index = parseInt($(this).data('index'), 10);
        selectSearchResult(index);
    });
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.pos-search-input-wrap').length) {
            hideSearchDropdown();
        }
    });
    $('#posCheckoutBtn').on('click', openPaymentSheet);
    $('#posPaymentClose, #posPaymentOverlay').on('click', closePaymentSheet);
    $('#btnProcessPayment').on('click', processPayment);
    $('#btnResetCart').on('click', resetCart);
    $('#bayar').on('focus', handleBayarFocus);
    $('#bayar').on('input', handleBayarInput);
    $('#bayar').on('change', calculateKembalian);

    // Ganti jenis harga (Non-Resep / Resep / Pelanggan)
    $('#tipeHarga').on('change', function () {
        var tipe = $(this).val();
        localStorage.setItem('pos_tipe_harga', tipe);
        applyTipeHarga(tipe);
        // Perbarui nominal pada dropdown hasil pencarian bila sedang terbuka
        if (!$('#posSearchDropdown').is('[hidden]') && searchResults.length > 0) {
            renderSearchDropdown(searchResults);
        }
    });

    // Load printer setting
    var savedPrinterType = localStorage.getItem('pos_printer_type') || 'browser';
    setPrinterType(savedPrinterType);

    // Pulihkan jenis harga terakhir yang dipilih kasir
    var savedTipeHarga = localStorage.getItem('pos_tipe_harga');
    if (savedTipeHarga) {
        $('#tipeHarga').val(savedTipeHarga);
    }

    renderCart();
    calculateTotal();
});

// Thermal Printer Direct Integration via WebUSB and Web Bluetooth
var ThermalPrinter = {
    connectionType: 'browser', // 'browser', 'usb', 'bluetooth', 'serial'
    device: null,
    characteristic: null,
    endpointOut: null,
    interfaceNumber: null,

    connectUSB: async function () {
        try {
            if (!navigator.usb) {
                alert('WebUSB tidak didukung di browser ini. Gunakan Google Chrome atau Edge.');
                return false;
            }
            this.device = await navigator.usb.requestDevice({
                filters: [{ classCode: 7 }] // printer class
            });
            showLoading('Menghubungkan Printer USB...');
            await this.device.open();
            await this.device.selectConfiguration(1);

            var interfaces = this.device.configuration.interfaces;
            var found = false;
            for (var iface of interfaces) {
                for (var alt of iface.alternates) {
                    if (alt.interfaceClass === 7) {
                        this.interfaceNumber = iface.interfaceNumber;
                        for (var ep of alt.endpoints) {
                            if (ep.direction === 'out' && ep.type === 'bulk') {
                                this.endpointOut = ep.endpointNumber;
                                found = true;
                                break;
                            }
                        }
                    }
                    if (found) break;
                }
                if (found) break;
            }
            hideLoading();

            if (!found) {
                alert('Tidak menemukan bulk OUT endpoint untuk printer.');
                return false;
            }

            await this.device.claimInterface(this.interfaceNumber);
            localStorage.setItem('pos_printer_type', 'usb');
            this.connectionType = 'usb';
            $('#activePrinterLabel').text('Direct USB');
            showNotification('success', 'Printer USB terhubung: ' + this.device.productName);
            return true;
        } catch (e) {
            hideLoading();
            console.error(e);
            showNotification('error', 'Gagal menghubungkan printer USB: ' + e.message);
            return false;
        }
    },
    connectBluetooth: async function () {
        try {
            if (!navigator.bluetooth) {
                alert('Web Bluetooth tidak didukung di browser ini atau sedang dinonaktifkan.');
                return false;
            }
            this.device = await navigator.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [
                    '000018f0-0000-1000-8000-00805f9b34fb', // Standard thermal printer service
                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2', // ESC/POS service
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455', // Other printer service
                    '00001101-0000-1000-8000-00805f9b34fb'  // SPP UUID
                ]
            });

            showLoading('Menghubungkan Printer Bluetooth...');
            var server = await this.device.gatt.connect();

            let service;
            try {
                service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
            } catch (e) {
                try {
                    service = await server.getPrimaryService('e7810a71-73ae-499d-8c15-faa9aef0c3f2');
                } catch (e) {
                    try {
                        service = await server.getPrimaryService('49535343-fe7d-4ae5-8fa9-9fafd205e455');
                    } catch (e) {
                        var services = await server.getPrimaryServices();
                        if (services.length > 0) {
                            service = services[0];
                        } else {
                            throw new Error('Tidak dapat menemukan layanan printer yang sesuai');
                        }
                    }
                }
            }

            var characteristics = await service.getCharacteristics();
            var found = false;
            for (var char of characteristics) {
                var properties = char.properties;
                if (properties.write || properties.writeWithoutResponse) {
                    this.characteristic = char;
                    found = true;
                    break;
                }
            }

            if (!found) {
                try {
                    this.characteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                    found = true;
                } catch (e) {
                    try {
                        this.characteristic = await service.getCharacteristic('49535343-8841-43f4-a8d4-ecbe34729bb3');
                        found = true;
                    } catch (e) {
                        try {
                            this.characteristic = await service.getCharacteristic('00000000-0000-1000-8000-00805f9b34fb');
                            found = true;
                        } catch (e) { }
                    }
                }
            }

            hideLoading();

            if (!found) {
                alert('Tidak menemukan karakteristik printer yang sesuai.');
                return false;
            }

            localStorage.setItem('pos_printer_type', 'bluetooth');
            this.connectionType = 'bluetooth';
            $('#activePrinterLabel').text('Direct BT (BLE)');
            showNotification('success', 'Printer Bluetooth terhubung: ' + this.device.name);
            return true;
        } catch (e) {
            hideLoading();
            console.error(e);
            showNotification('error', 'Gagal menghubungkan printer Bluetooth: ' + e.message);
            return false;
        }
    },

    connectSerial: async function () {
        try {
            if (!navigator.serial) {
                alert('Web Serial tidak didukung di browser ini. Gunakan Google Chrome atau Edge.');
                return false;
            }
            this.device = await navigator.serial.requestPort();
            localStorage.setItem('pos_printer_type', 'serial');
            this.connectionType = 'serial';
            $('#activePrinterLabel').text('COM Port / Serial BT');
            showNotification('success', 'Printer Serial / Bluetooth (COM Port) dipilih.');
            return true;
        } catch (e) {
            console.error(e);
            showNotification('error', 'Gagal memilih port serial: ' + e.message);
            return false;
        }
    },

    printReceipt: async function (saleData) {
        try {
            var dataBytes = this.generateEscPos(saleData);
            if (this.connectionType === 'usb') {
                if (!this.device || !this.device.opened) {
                    var connected = await this.connectUSB();
                    if (!connected) return;
                }
                await this.device.transferOut(this.endpointOut, dataBytes);
                showNotification('success', 'Struk tercetak di printer USB');
            } else if (this.connectionType === 'bluetooth') {
                if (!this.device || !this.device.gatt.connected) {
                    var connected = await this.connectBluetooth();
                    if (!connected) return;
                }
                var chunkSize = 512;
                for (var i = 0; i < dataBytes.length; i += chunkSize) {
                    var chunk = dataBytes.slice(i, i + chunkSize);
                    await this.characteristic.writeValue(chunk);
                }
                showNotification('success', 'Struk tercetak di printer Bluetooth');
            } else if (this.connectionType === 'serial') {
                if (!this.device) {
                    var connected = await this.connectSerial();
                    if (!connected) return;
                }
                showLoading('Mengirim data ke COM Port...');
                await this.device.open({ baudRate: 9600 });
                const writer = this.device.writable.getWriter();
                await writer.write(dataBytes);
                writer.releaseLock();
                await this.device.close();
                hideLoading();
                showNotification('success', 'Struk tercetak di printer Serial/COM');
            }
        } catch (e) {
            hideLoading();
            console.error(e);
            showNotification('error', 'Gagal mencetak secara langsung, beralih ke print browser: ' + e.message);
            window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleData.penjualan.id, '_blank');
        }
    },

    generateEscPos: function (data) {
        var enc = new TextEncoder();
        var esc = new Uint8Array([0x1B, 0x40]);
        var alignCenter = new Uint8Array([0x1B, 0x61, 0x01]);
        var alignLeft = new Uint8Array([0x1B, 0x61, 0x00]);
        var alignRight = new Uint8Array([0x1B, 0x61, 0x02]);
        var boldOn = new Uint8Array([0x1B, 0x45, 0x01]);
        var boldOff = new Uint8Array([0x1B, 0x45, 0x00]);
        var fontDoubleSize = new Uint8Array([0x1D, 0x21, 0x11]);
        var fontNormalSize = new Uint8Array([0x1D, 0x21, 0x00]);
        var lineFeed = new Uint8Array([0x0A]);
        var paperCut = new Uint8Array([0x1D, 0x56, 0x41, 0x03]);

        var p = data.penjualan;
        var items = data.details;
        var buffer = [];

        function append(arr) { for (var b of arr) buffer.push(b); }
        function appendText(t) { var bytes = enc.encode(t); for (var b of bytes) buffer.push(b); }

        append(esc);
        append(alignCenter);
        append(boldOn);
        append(fontDoubleSize);

        var storeName = $('#posStoreName').val() || 'TOKO POS';
        var storeAddress = $('#posStoreAddress').val() || '';
        var storePhone = $('#posStorePhone').val() || '';

        appendText(storeName + "\n");
        append(fontNormalSize);
        append(boldOff);

        if (storeAddress) appendText(storeAddress + "\n");
        if (storePhone) appendText("Telp: " + storePhone + "\n");

        appendText("================================\n");

        append(alignLeft);
        appendText("No. Trans : " + p.no_penjualan + "\n");
        appendText("Tanggal   : " + p.tanggal + "\n");
        if (p.pelanggan_nama) appendText("Pelanggan : " + p.pelanggan_nama + "\n");
        if (p.metode_pembayaran_nama) appendText("Metode    : " + p.metode_pembayaran_nama + "\n");

        appendText("--------------------------------\n");

        items.forEach(function (item) {
            appendText(item.produk_nama + "\n");
            var satuanLabel = item.satuan_nama ? ' ' + item.satuan_nama : '';
            var qtyStr = item.qty + satuanLabel + " x " + parseFloat(item.harga).toLocaleString('id-ID');
            var subtotalStr = parseFloat(item.subtotal).toLocaleString('id-ID');
            var spaces = 32 - qtyStr.length - subtotalStr.length;
            if (spaces < 1) spaces = 1;
            appendText(qtyStr + " ".repeat(spaces) + subtotalStr + "\n");
            if (parseFloat(item.diskon) > 0) {
                appendText("  Diskon: -" + parseFloat(item.diskon).toLocaleString('id-ID') + "\n");
            }
        });

        appendText("--------------------------------\n");

        function appendTotalRow(label, amount) {
            var amountStr = parseFloat(amount).toLocaleString('id-ID');
            var spaces = 32 - label.length - amountStr.length;
            if (spaces < 1) spaces = 1;
            appendText(label + " ".repeat(spaces) + amountStr + "\n");
        }

        appendTotalRow("Subtotal", p.subtotal);
        if (parseFloat(p.diskon) > 0) appendTotalRow("Diskon", -p.diskon);
        if (parseFloat(p.pajak) > 0) appendTotalRow("Pajak", p.pajak);

        append(boldOn);
        appendTotalRow("Total", p.total);
        append(boldOff);

        appendTotalRow("Bayar", p.bayar);
        appendTotalRow("Kembali", p.kembalian);

        appendText("================================\n");
        append(alignCenter);
        appendText("Terima Kasih telah berbelanja\n");

        append(lineFeed);
        append(lineFeed);
        append(lineFeed);
        append(paperCut);

        return new Uint8Array(buffer);
    }
};

function printSaleReceipt(saleId) {
    if (ThermalPrinter.connectionType === 'browser') {
        window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleId, '_blank');
        return;
    }

    showLoading('Mengambil data cetak...');
    $.ajax({
        url: baseUrl + 'penjualan/kasir/ajax_get_print/' + saleId,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            hideLoading();
            if (res.status && res.data) {
                ThermalPrinter.printReceipt(res.data);
            } else {
                showNotification('error', 'Gagal mengambil data cetak: ' + res.message);
                window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleId, '_blank');
            }
        },
        error: function () {
            hideLoading();
            showNotification('error', 'Gagal mengambil data cetak');
            window.open(baseUrl + 'penjualan/daftar-penjualan/print/' + saleId, '_blank');
        }
    });
}

function setPrinterType(type) {
    ThermalPrinter.connectionType = type;
    localStorage.setItem('pos_printer_type', type);
    var labels = {
        'browser': 'Browser',
        'usb': 'Direct USB',
        'bluetooth': 'Direct BT (BLE)',
        'serial': 'COM Port / Serial BT'
    };
    $('#activePrinterLabel').text(labels[type] || 'Browser');
}

function connectPrinter() {
    var type = ThermalPrinter.connectionType;
    if (type === 'browser') {
        showNotification('warning', 'Pilih tipe printer Direct USB, Bluetooth (BLE), atau Serial (COM Port) terlebih dahulu.');
        return;
    }
    if (type === 'usb') {
        ThermalPrinter.connectUSB();
    } else if (type === 'bluetooth') {
        ThermalPrinter.connectBluetooth();
    } else if (type === 'serial') {
        ThermalPrinter.connectSerial();
    }
}

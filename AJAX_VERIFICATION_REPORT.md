# Laporan Verifikasi AJAX/JavaScript

**Tanggal:** 2025-12-17 04:14:31

## Ringkasan

- **Total View:** 28
- **Menggunakan DataTable:** 14
- **Menggunakan Form Handler:** 16
- **Menggunakan AJAX:** 17
- **Memiliki Error Handling:** 7
- **Memiliki Issues:** 10

## Detail View

### auth/login.php

- **DataTable:** ❌
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ✅
- **AJAX URLs:**
  - `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`
  - `, loginUrl);
            
            $.ajax({
                url: loginUrl,
                type: `

### dashboard/umum.php

- **DataTable:** ❌
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ✅
- **AJAX URLs:**
  - `dashboard/ajax_statistik`
  - `;
    }
    
    loadStatistik();
    
    function loadStatistik() {
        $.ajax({
            url: baseUrl + `

### keuangan/akun_kas/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `keuangan/akun-kas/ajax_list`
  - `keuangan/akun-kas/ajax_save`
  - `keuangan/akun-kas/ajax_get/`
  - `keuangan/akun-kas/ajax_delete/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
    };
    
    window.editData = function(id) {
        $.ajax({
            url: baseUrl + `
  - `, function() {
            $.ajax({
                url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### keuangan/buku_kas/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `keuangan/buku-kas/ajax_list`
  - `keuangan/buku-kas/ajax_save`
  - `keuangan/buku-kas/ajax_delete/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `, function() {
            $.ajax({
                url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### keuangan/piutang_usaha/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `keuangan/akun-kas/ajax_list`
  - `keuangan/piutang-usaha/ajax_list`
  - `keuangan/piutang-usaha/ajax_get/`
  - `keuangan/piutang-usaha/ajax_terima_bayar/`
  - `>Terima Bayar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load akun kas untuk dropdown
    $.ajax({
        url: baseUrl + `
  - `, {
        ajax: {
            url: baseUrl + `
  - `
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8, orderable: false, searchable: false }
        ]
    });
    
    window.terimaBayar = function(id) {
        $.ajax({
            url: baseUrl + `
  - `).val();
        var formData = $(this).serialize();
        
        $.ajax({
            url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### keuangan/utang_usaha/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `keuangan/akun-kas/ajax_list`
  - `keuangan/utang-usaha/ajax_list`
  - `keuangan/utang-usaha/ajax_get/`
  - `keuangan/utang-usaha/ajax_bayar/`
  - `>Bayar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load akun kas untuk dropdown
    $.ajax({
        url: baseUrl + `
  - `, {
        ajax: {
            url: baseUrl + `
  - `
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8, orderable: false, searchable: false }
        ]
    });
    
    window.bayarUtang = function(id) {
        $.ajax({
            url: baseUrl + `
  - `).val();
        var formData = $(this).serialize();
        
        $.ajax({
            url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### manajemen/jadwal/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `manajemen/jadwal/ajax_list`
  - `manajemen/jadwal/ajax_save`
  - `manajemen/jadwal/ajax_get/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `,
        table: table
    });
    
    window.editJadwal = function(user_id) {
        $.ajax({
            url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### manajemen/log/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ✅
- **AJAX URLs:**
  - `manajemen/log/ajax_export?`
  - `manajemen/log/ajax_list`
  - `manajemen/log/ajax_get/`
  - `manajemen/log/ajax_delete/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
            }
        }
        
        $.ajax({
            url: baseUrl + `
  - `, function() {
            $.ajax({
                url: baseUrl + `

### manajemen/peran_hak_akses/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ✅
- **AJAX URLs:**
  - `manajemen-pengguna/peran-hak-akses/ajax_list`
  - `manajemen-pengguna/peran-hak-akses/ajax_save`
  - `manajemen-pengguna/peran-hak-akses/ajax_get/`
  - `manajemen-pengguna/peran-hak-akses/ajax_delete/`
  - `manajemen-pengguna/peran-hak-akses/ajax_get_permissions/`
  - `manajemen-pengguna/peran-hak-akses/ajax_save_permissions/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
            }
        }
    };
    
    window.editData = function(id) {
        $.ajax({
            url: baseUrl + `
  - `, function() {
            $.ajax({
                url: baseUrl + `
  - `).val(id);
        $.ajax({
            url: baseUrl + `
  - `) ? 1 : 0;
                }
            }
        });
        
        $.ajax({
            url: baseUrl + `

### manajemen/presensi/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ✅
- **AJAX URLs:**
  - `manajemen/presensi/ajax_list`
  - `manajemen/presensi/ajax_save`
  - `manajemen/presensi/ajax_get/`
  - `manajemen/presensi/ajax_delete/`
  - `manajemen/presensi/ajax_checkin`
  - `manajemen/presensi/ajax_checkout/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
            }
        }
    };
    
    window.editData = function(id) {
        $.ajax({
            url: baseUrl + `
  - `, function() {
            $.ajax({
                url: baseUrl + `
  - `, response.message);
                    }
                }
            });
        });
    };
    
    window.checkIn = function(id) {
        $.ajax({
            url: baseUrl + `
  - `,
            success: function(response) {
                if (response.status) {
                    var data = response.data;
                    $.ajax({
                        url: baseUrl + `
  - `, response.message);
                            }
                        }
                    });
                }
            }
        });
    };
    
    window.checkOut = function(id) {
        $.ajax({
            url: baseUrl + `

### manajemen/shift/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ✅
- **AJAX URLs:**
  - `manajemen/shift/ajax_list`
  - `manajemen/shift/ajax_save`
  - `manajemen/shift/ajax_get/`
  - `manajemen/shift/ajax_delete/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
            }
        }
    };
    
    window.editData = function(id) {
        $.ajax({
            url: baseUrl + `
  - `, function() {
            $.ajax({
                url: baseUrl + `

### manajemen_pengguna/daftar_pengguna/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ✅
- **AJAX URLs:**
  - `manajemen-pengguna/daftar-pengguna/ajax_list`
  - `manajemen-pengguna/daftar-pengguna/ajax_save`
  - `manajemen-pengguna/daftar-pengguna/ajax_get/`
  - `manajemen-pengguna/daftar-pengguna/ajax_delete/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
            }
        }
    };
    
    window.editData = function(id) {
        $.ajax({
            url: baseUrl + `
  - `, function() {
            $.ajax({
                url: baseUrl + `

### master_data/kategori/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `masterdata/masterkategori/ajax_list`
  - `masterdata/masterkategori/ajax_save`
  - `masterdata/masterkategori/ajax_get/`
  - `masterdata/masterkategori/ajax_delete/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
}

function editData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + `
  - `, function() {
        showLoading();
        $.ajax({
            url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### master_data/produk/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `masterdata/masterproduk/ajax_list`
  - `masterdata/masterproduk/ajax_save`
  - `masterdata/masterproduk/ajax_get/`
  - `masterdata/masterproduk/ajax_delete/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `);
}

function editData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + `
  - `, function() {
        showLoading();
        $.ajax({
            url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### master_data/produk/kartu_stok.php

- **DataTable:** ✅
- **Form Handler:** ❌
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `masterdata/masterproduk/ajax_list_kartu_stok`
  - `, {
        ajax: {
            url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### penjualan/daftar_penjualan/index.php

- **DataTable:** ✅
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `penjualan/daftarpenjualan/ajax_list`
  - `penjualan/daftarpenjualan/ajax_get/`
  - `penjualan/daftarpenjualan/ajax_complete/`
  - `penjualan/daftarpenjualan/ajax_cancel/`
  - `, {
        ajax: {
            url: baseUrl + `
  - `
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false, searchable: false }
        ]
    });
});

function viewData(id) {
    showLoading();
    $.ajax({
        url: baseUrl + `
  - `, function() {
        showLoading();
        $.ajax({
            url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling

### penjualan/kasir/index.php

- **DataTable:** ❌
- **Form Handler:** ✅
- **AJAX Calls:** ✅
- **jQuery:** ✅
- **Error Handling:** ❌
- **AJAX URLs:**
  - `penjualan/kasir/ajax_search_produk`
  - `penjualan/kasir/ajax_get_produk_by_barcode`
  - `penjualan/kasir/ajax_save`
  - `);
        return;
    }
    
    showLoading();
    $.ajax({
        url: baseUrl + `
  - `).val(),
        items: JSON.stringify(cart)
    };
    
    showLoading();
    $.ajax({
        url: baseUrl + `
- **⚠️ Issues:**
  - AJAX calls tanpa error handling


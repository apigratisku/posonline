# Tools Verifikasi dan Testing Aplikasi Apotek

Dokumen ini menjelaskan tools yang telah dibuat untuk verifikasi CRUD, AJAX/JavaScript, dan testing menu URLs.

## File yang Dibuat

### 1. Script Verifikasi
- **`verify_crud.php`** - Script untuk verifikasi semua method CRUD pada controller
- **`verify_ajax.php`** - Script untuk verifikasi AJAX/JavaScript pada views
- **`test_menu_urls.php`** - Script CLI untuk testing semua menu URLs
- **`test_menu_interface.php`** - Interface web untuk testing menu URLs secara interaktif
- **`generate_comprehensive_report.php`** - Script untuk generate laporan komprehensif

### 2. Laporan yang Dihasilkan
- **`CRUD_VERIFICATION_REPORT.md`** - Laporan verifikasi CRUD operations
- **`AJAX_VERIFICATION_REPORT.md`** - Laporan verifikasi AJAX/JavaScript
- **`MENU_TESTING_REPORT.md`** - Laporan hasil testing menu URLs (akan di-generate saat testing)
- **`COMPREHENSIVE_REPORT.md`** - Laporan komprehensif yang menggabungkan semua hasil

## Cara Menggunakan

### 1. Verifikasi CRUD Operations

Jalankan script untuk memverifikasi semua method CRUD pada controller:

```bash
php verify_crud.php
```

Script ini akan:
- Scan semua controller di `application/controllers/`
- Memeriksa keberadaan method: `ajax_list`, `ajax_get`, `ajax_save`, `ajax_delete`
- Memeriksa permission check, validation, dan penggunaan `ajax_response`
- Generate laporan di `CRUD_VERIFICATION_REPORT.md`

### 2. Verifikasi AJAX/JavaScript

Jalankan script untuk memverifikasi AJAX dan JavaScript pada views:

```bash
php verify_ajax.php
```

Script ini akan:
- Scan semua view files di `application/views/`
- Memeriksa penggunaan DataTable, form handlers, dan AJAX calls
- Memeriksa error handling dan jQuery usage
- Generate laporan di `AJAX_VERIFICATION_REPORT.md`

### 3. Testing Menu URLs

#### Option 1: CLI Testing (Batch)

Jalankan script untuk testing semua menu URLs sekaligus:

```bash
php test_menu_urls.php
```

Script ini akan:
- Test semua 91 menu URLs dari sidebar
- Memeriksa HTTP response code
- Mendeteksi error (404, 403, 500, dll)
- Generate laporan di `MENU_TESTING_REPORT.md` dan `menu_test_results.json`

#### Option 2: Web Interface (Interaktif)

Buka browser dan akses:
```
http://ntbdev.local/ntbdev.my.id/demo/apotik/test_menu_interface.php
```

Interface ini menyediakan:
- Testing menu per kategori
- Real-time status update
- Progress bar untuk batch testing
- Export hasil testing ke JSON
- Link langsung ke setiap menu

### 4. Generate Laporan Komprehensif

Jalankan script untuk menggabungkan semua laporan:

```bash
php generate_comprehensive_report.php
```

Script ini akan:
- Menggabungkan hasil dari CRUD, AJAX, dan Menu testing
- Menampilkan statistik aplikasi
- Memberikan rekomendasi perbaikan
- Generate laporan di `COMPREHENSIVE_REPORT.md`

## Hasil Verifikasi Saat Ini

### CRUD Operations
- **Total Controller:** 46
- **CRUD Lengkap:** 13 controller
- **CRUD Sebagian:** 13 controller
- **Basic (Index saja):** 19 controller
- **Kosong:** 1 controller

### AJAX/JavaScript
- **Total View:** 28
- **Menggunakan DataTable:** 14 views
- **Menggunakan Form Handler:** 16 views
- **Menggunakan AJAX:** 17 views
- **Memiliki Error Handling:** 7 views
- **Memiliki Issues:** 10 views

### Menu URLs
- **Total Menu:** 91 menu
- Testing dapat dilakukan melalui CLI atau web interface

## Rekomendasi

1. **CRUD Operations:**
   - Tambahkan method CRUD lengkap pada controller yang masih partial
   - Pastikan semua controller memiliki permission check
   - Tambahkan validasi form di semua `ajax_save` method

2. **AJAX/JavaScript:**
   - Tambahkan error handling pada semua AJAX calls
   - Verifikasi semua URL AJAX sesuai dengan routes
   - Pastikan baseUrl didefinisikan dengan benar

3. **Menu URLs:**
   - Perbaiki URL mismatch antara sidebar dan routes
   - Buat controller untuk menu yang belum ada
   - Pastikan semua menu memiliki route yang valid

## Catatan

- Semua script menggunakan base_url dari `application/config/config.php`
- Testing menu memerlukan server yang berjalan
- Pastikan curl extension aktif untuk testing menu URLs
- Laporan di-generate dalam format Markdown untuk kemudahan membaca

## Troubleshooting

### Error: "Cannot find path"
- Pastikan script dijalankan dari direktori root aplikasi
- Gunakan path lengkap jika diperlukan

### Error: "curl extension not loaded"
- Aktifkan extension curl di php.ini
- Restart web server setelah mengaktifkan

### Menu testing tidak berjalan
- Pastikan server web berjalan
- Periksa base_url di config.php
- Pastikan aplikasi dapat diakses dari browser


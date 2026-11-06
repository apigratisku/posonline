# Sistem Manajemen Apotek - Dokumentasi Implementasi

## Status Implementasi

### ✅ Telah Selesai

1. **Database Schema** (`database/schema.sql`)
   - Semua tabel dengan foreign keys dan indexes
   - Relasi lengkap antar tabel
   - Support untuk semua modul

2. **Core Structure**
   - `MY_Controller` - Base controller dengan auth check
   - `MY_Model` - Base model dengan CRUD generik
   - `Auth Library` - Sistem autentikasi lengkap
   - Helpers: `permission_helper.php`, `ajax_helper.php`

3. **UI Template**
   - Bootstrap 5 dengan custom gradient CSS
   - Layout: header, sidebar, footer, main
   - Responsive design
   - Loading overlay
   - Custom color scheme dengan gradient cerah

4. **Auth System**
   - Login/Logout
   - Session management
   - Permission checking
   - Activity logging

5. **JavaScript Modules**
   - `app.js` - Main application
   - `datatable.js` - DataTables integration
   - `form-handler.js` - Form AJAX handler
   - `modal.js` - Modal management
   - `notification.js` - Toast notifications

6. **Master Data Example**
   - Complete CRUD untuk Master Kategori
   - Model, Controller, View
   - AJAX handlers lengkap
   - Dapat digunakan sebagai template untuk modul lain

7. **Dashboard**
   - Basic dashboard structure
   - Stats cards
   - Chart integration ready

8. **Seeders** (`database/seeders.sql`)
   - Default admin user
   - Default peran dan modul
   - Master data awal

## Pola Implementasi Modul

Setiap modul mengikuti pola yang sama seperti Master Kategori:

### 1. Model (`application/models/`)
```php
class Nama_model extends MY_Model {
    protected $table = 'nama_tabel';
    protected $fillable = array('field1', 'field2');
    // Custom methods
}
```

### 2. Controller (`application/controllers/`)
```php
class NamaController extends MY_Controller {
    public function index() { }
    public function ajax_list() { }
    public function ajax_get($id) { }
    public function ajax_save() { }
    public function ajax_delete($id) { }
    public function ajax_search() { }
}
```

### 3. View (`application/views/`)
- `index.php` - List dengan DataTable
- Form modal untuk create/edit
- JavaScript untuk AJAX handling

## Modul yang Perlu Dibuat

Ikuti pola Master Kategori untuk modul berikut:

### Master Data
- [x] Master Kategori (contoh)
- [ ] Master Satuan
- [ ] Master Rak
- [ ] Master Gudang
- [ ] Master Pajak
- [ ] Master Shift
- [ ] Master Membership
- [ ] Jenis Pelayanan
- [ ] Jenis Antrian
- [ ] Produk Lab
- [ ] Master Item Pemeriksaan

### Produk & Persediaan
- [ ] Master Produk
- [ ] Kartu Stok (otomatis)
- [ ] Defecta
- [ ] Stok Kadaluarsa
- [ ] Stok Opname
- [ ] Penyesuaian Stok
- [ ] Perpindahan Stok

### Kontak
- [ ] Pelanggan
- [ ] Sales/Pelayan
- [ ] Dokter
- [ ] Supplier

### Penjualan
- [ ] Kasir
- [ ] Pesanan Penjualan
- [ ] Daftar Penjualan
- [ ] Retur Penjualan
- [ ] Penjualan Tertolak
- [ ] Faktur Penjualan

### Pembelian
- [ ] Rencana Pembelian
- [ ] Pesanan Pembelian
- [ ] Faktur Pembelian
- [ ] Retur Pembelian
- [ ] Faktur AI

### Pelayanan
- [ ] Kunjungan Pasien
- [ ] Rekam Medis
- [ ] Pemeriksaan Awal
- [ ] Pemeriksaan Dokter
- [ ] Penerimaan Resep
- [ ] Penebusan Resep
- [ ] Template Racikan
- [ ] Pemeriksaan Lab

### Konsinyasi
- [ ] Konsinyasi Masuk
- [ ] Status Konsinyasi
- [ ] Retur Konsinyasi
- [ ] Stok Konsinyasi

### Program Promo
- [ ] Bundel/Paket Produk
- [ ] Diskon Produk
- [ ] Member Poin
- [ ] Voucher Diskon

### Keuangan
- [ ] Daftar Akun Kas
- [ ] Buku Kas
- [ ] Utang Usaha
- [ ] Piutang Usaha

### Akuntansi
- [ ] Master CoA
- [ ] Buku Besar
- [ ] Aktiva Tetap

### Laporan
- [ ] Laporan Penjualan
- [ ] Laporan Pembelian
- [ ] Laporan Persediaan
- [ ] Laporan Keuangan

### Analisis
- [ ] Analisis Pareto
- [ ] Analisis Pembelian
- [ ] Analisis Harga

### Manajemen Pengguna
- [ ] Daftar Pengguna
- [ ] Peran & Hak Akses
- [ ] Jadwal Kerja
- [ ] Presensi Karyawan
- [ ] Riwayat Shift
- [ ] Log Aktivitas

### KPI
- [ ] Parameter KPI
- [ ] KPI Karyawan
- [ ] Pencapaian KPI
- [ ] Laporan KPI

### Gaji
- [ ] Gaji Pokok
- [ ] Tunjangan
- [ ] Potongan Gaji
- [ ] Perhitungan Gaji
- [ ] Pembayaran Gaji

### Pengaturan
- [ ] Profil Apotek
- [ ] Metode Pembayaran
- [ ] Konfigurasi
- [ ] Integrasi Platform
- [ ] Apotek Online
- [ ] Website
- [ ] Katalog Online
- [ ] WhatsApp Tools

### Multi Outlet
- [ ] Permintaan Mutasi
- [ ] Mutasi Antar Outlet
- [ ] Produk Mitra
- [ ] Outlet Mitra

## Instalasi

1. **Import Database**
   ```sql
   CREATE DATABASE apotik_db;
   USE apotik_db;
   SOURCE database/schema.sql;
   SOURCE database/seeders.sql;
   ```

2. **Konfigurasi Database**
   Edit `application/config/database.php`:
   ```php
   'hostname' => 'localhost',
   'username' => 'your_username',
   'password' => 'your_password',
   'database' => 'apotik_db',
   ```

3. **Konfigurasi Base URL**
   Edit `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost/apotik/';
   ```

4. **Default Login**
   - Username: `admin`
   - Password: `admin123`

## Fitur Khusus

### Kartu Stok Otomatis
- Auto-update saat transaksi penjualan/pembelian
- History tracking lengkap
- Stock alert untuk stok minimal

### AJAX Full
- Semua operasi CRUD menggunakan AJAX
- Real-time updates
- No page reload

### Permission System
- Role-based access control
- Per-modul permission (view, create, update, delete)
- Automatic permission check

## Catatan Penting

1. **CSRF Protection**: Sudah diaktifkan, semua form harus include CSRF token
2. **Password Hashing**: Menggunakan bcrypt
3. **Timestamps**: Auto-managed untuk created_at, updated_at, created_by, updated_by
4. **Soft Deletes**: Dapat diaktifkan di model dengan `protected $soft_deletes = true;`

## Next Steps

1. Buat modul-modul yang tersisa mengikuti pola Master Kategori
2. Implementasikan fitur khusus seperti:
   - Barcode scanner untuk kasir
   - Print receipt
   - Export Excel/PDF
   - Real-time notifications
3. Testing dan debugging
4. Deployment

## Support

Untuk pertanyaan atau bantuan, silakan hubungi developer.


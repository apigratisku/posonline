# Permission Module Codes Mapping

Dokumentasi ini menjelaskan mapping antara module codes yang digunakan di controller dengan module codes di database.

## Module Codes di Database (seeders.sql)

| Module Code | Nama | Controller | Icon |
|------------|------|------------|------|
| dashboard | Dashboard | dashboard | fa-home |
| penjualan | Penjualan | penjualan | fa-shopping-cart |
| pelayanan | Pelayanan | pelayanan | fa-user-md |
| persediaan | Persediaan | persediaan | fa-boxes |
| pembelian | Pembelian | pembelian | fa-shopping-bag |
| konsinyasi | Konsinyasi | konsinyasi | fa-handshake |
| promo | Program Promo | promo | fa-gift |
| keuangan | Keuangan | keuangan | fa-money-bill-wave |
| akuntansi | Akuntansi | akuntansi | fa-book |
| kontak | Kontak | kontak | fa-address-book |
| laporan | Laporan | laporan | fa-file-alt |
| analisis | Analisis | analisis | fa-chart-line |
| **manajemen** | Manajemen Pengguna | manajemen | fa-users |
| kpi | KPI | kpi | fa-tasks |
| gaji | Gaji | gaji | fa-money-check-alt |
| pengaturan | Pengaturan | pengaturan | fa-cog |
| outlet | Multi Outlet | outlet | fa-store |
| master | Master Data | master | fa-database |

## Penggunaan di Controller

Semua controller harus menggunakan module code yang sesuai dengan database. Contoh:

```php
// ✅ Benar
$this->check_permission('dashboard', 'view');
$this->check_permission('manajemen', 'view');  // Bukan 'manajemen_pengguna'
$this->check_permission('master', 'view');

// ❌ Salah
$this->check_permission('manajemen_pengguna', 'view');  // Tidak ada di database
```

## Actions yang Tersedia

- `view` - Melihat data
- `create` - Membuat data baru
- `update` - Mengupdate data
- `delete` - Menghapus data

## Catatan Penting

1. **Admin Role**: Role dengan kode 'admin' selalu memiliki full access ke semua modul (fallback logic)
2. **Module Code**: Harus sesuai persis dengan kode di database (case-sensitive)
3. **Default Permissions**: Admin role otomatis mendapat full access untuk semua modul via seeders.sql


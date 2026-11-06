# Laporan Verifikasi CRUD

**Tanggal:** 2025-12-17 04:13:58

## Ringkasan

- **Total Controller:** 46
- **CRUD Lengkap:** 13
- **CRUD Sebagian:** 13
- **Basic (Index saja):** 19
- **Kosong:** 1

## Detail Controller

### ℹ️ Akuntansi/AktivaTetap

- **Class:** `AktivaTetap`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Akuntansi/BukuBesar

- **Class:** `BukuBesar`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Akuntansi/CoA

- **Class:** `CoA`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Analisis

- **Class:** `Analisis`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ❌ Auth

- **Class:** `Auth`
- **Status:** empty
- **Index:** ❌
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ❌
- **Validation:** ❌
- **Uses ajax_response:** ✅
- **Methods:** __construct, login, logout

### ℹ️ Dashboard/Dashboard

- **Class:** `Dashboard`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, penjualan, pembelian, persediaan, keuangan, ajax_statistik

### ℹ️ Gaji

- **Class:** `Gaji`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ KPI

- **Class:** `KPI`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ⚠️ Keuangan/BukuKas

- **Class:** `BukuKas`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ❌
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_delete

### ✅ Keuangan/DaftarAkunKas

- **Class:** `DaftarAkunKas`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ⚠️ Keuangan/PiutangUsaha

- **Class:** `PiutangUsaha`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_terima_bayar

### ⚠️ Keuangan/UtangUsaha

- **Class:** `UtangUsaha`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_bayar

### ℹ️ Konsinyasi/Masuk

- **Class:** `Masuk`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Konsinyasi/Retur

- **Class:** `Retur`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Konsinyasi/Status

- **Class:** `Status`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Konsinyasi/Stok

- **Class:** `Stok`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ✅ Kontak/Dokter

- **Class:** `Dokter`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ✅ Kontak/Pelanggan

- **Class:** `Pelanggan`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ✅ Kontak/Sales

- **Class:** `Sales`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ✅ Kontak/Supplier

- **Class:** `Supplier`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ✅ Manajemen/Jadwal

- **Class:** `Jadwal`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ⚠️ Manajemen/Log

- **Class:** `Log`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ❌
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_delete, ajax_export

### ✅ Manajemen/Presensi

- **Class:** `Presensi`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_checkin, ajax_checkout, ajax_delete

### ✅ Manajemen/Shift

- **Class:** `Shift`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ℹ️ Manajemen

- **Class:** `Manajemen`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ✅ ManajemenPengguna/DaftarPengguna

- **Class:** `DaftarPengguna`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ✅ ManajemenPengguna/PeranHakAkses

- **Class:** `PeranHakAkses`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_get_permissions, ajax_save, ajax_save_permissions, ajax_delete

### ✅ MasterData/MasterKategori

- **Class:** `MasterKategori`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete, ajax_search

### ✅ MasterData/MasterProduk

- **Class:** `MasterProduk`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete, ajax_search, kartu_stok, ajax_list_kartu_stok

### ✅ Pelayanan/KunjunganPasien

- **Class:** `KunjunganPasien`
- **Status:** complete
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ✅
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save, ajax_delete

### ⚠️ Pelayanan/PenerimaanResep

- **Class:** `PenerimaanResep`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_save

### ⚠️ Pembelian/FakturPembelian

- **Class:** `FakturPembelian`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_get

### ⚠️ Pembelian/PesananPembelian

- **Class:** `PesananPembelian`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_get, ajax_receive

### ℹ️ Pengaturan

- **Class:** `Pengaturan`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ⚠️ Penjualan/DaftarPenjualan

- **Class:** `DaftarPenjualan`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_get, ajax_complete, ajax_cancel

### ℹ️ Penjualan/FakturPenjualan

- **Class:** `FakturPenjualan`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Penjualan/Kasir

- **Class:** `Kasir`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_search_produk, ajax_get_produk_by_barcode, ajax_save, ajax_get_print

### ⚠️ Penjualan/PesananPenjualan

- **Class:** `PesananPenjualan`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ✅
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_get, ajax_confirm

### ℹ️ Penjualan/ReturPenjualan

- **Class:** `ReturPenjualan`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ℹ️ Penjualan/Tertolak

- **Class:** `Tertolak`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index

### ⚠️ Persediaan/Defecta

- **Class:** `Defecta`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ❌
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_approve

### ⚠️ Persediaan/PenyesuaianStok

- **Class:** `PenyesuaianStok`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ❌
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_approve

### ⚠️ Persediaan/PerpindahanStok

- **Class:** `PerpindahanStok`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ❌
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_complete

### ℹ️ Persediaan/StokKadaluarsa

- **Class:** `StokKadaluarsa`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** __construct, index, ajax_list

### ⚠️ Persediaan/StokOpname

- **Class:** `StokOpname`
- **Status:** partial
- **Index:** ✅
- **ajax_list:** ✅
- **ajax_get:** ❌
- **ajax_save:** ✅
- **ajax_delete:** ❌
- **Permission Check:** ✅
- **Validation:** ✅
- **Uses ajax_response:** ✅
- **Methods:** __construct, index, ajax_list, ajax_save, ajax_complete

### ℹ️ Welcome

- **Class:** `Welcome`
- **Status:** basic
- **Index:** ✅
- **ajax_list:** ❌
- **ajax_get:** ❌
- **ajax_save:** ❌
- **ajax_delete:** ❌
- **Permission Check:** ❌
- **Validation:** ❌
- **Uses ajax_response:** ❌
- **Methods:** index


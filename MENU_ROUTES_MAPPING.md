# Mapping Menu Sidebar ke Routes dan Controllers

## Status: ✅ = Ada, ❌ = Belum Ada, ⚠️ = Mismatch URL

### 1. Dashboard
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Dashboard | `dashboard` | `dashboard` → `dashboard/dashboard/index` | `Dashboard/Dashboard.php` | ✅ |

### 2. Penjualan
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Kasir | `penjualan/kasir` | `penjualan/kasir` → `penjualan/kasir/index` | `Penjualan/Kasir.php` | ✅ |
| Pesanan Penjualan | `penjualan/pesanan` | `penjualan/pesanan-penjualan` → `penjualan/pesananpenjualan/index` | `Penjualan/PesananPenjualan.php` | ⚠️ URL mismatch |
| Daftar Penjualan | `penjualan/daftar` | `penjualan/daftar-penjualan` → `penjualan/daftarpenjualan/index` | `Penjualan/DaftarPenjualan.php` | ⚠️ URL mismatch |
| Retur Penjualan | `penjualan/retur` | - | - | ❌ |
| Penjualan Tertolak | `penjualan/tertolak` | - | - | ❌ |
| Faktur Penjualan | `penjualan/faktur` | - | - | ❌ |

### 3. Pelayanan
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Kunjungan Pasien | `pelayanan/kunjungan` | `pelayanan/kunjungan-pasien` → `pelayanan/kunjunganpasien/index` | `Pelayanan/KunjunganPasien.php` | ⚠️ URL mismatch |
| Rekam Medis | `pelayanan/rekam-medis` | - | - | ❌ |
| Pemeriksaan Awal | `pelayanan/pemeriksaan-awal` | - | - | ❌ |
| Pemeriksaan Dokter | `pelayanan/pemeriksaan-dokter` | - | - | ❌ |
| Penerimaan Resep | `pelayanan/resep` | `pelayanan/penerimaan-resep` → `pelayanan/penerimaanresep/index` | `Pelayanan/PenerimaanResep.php` | ⚠️ URL mismatch |
| Penebusan Resep | `pelayanan/penebusan` | - | - | ❌ |
| Template Racikan | `pelayanan/racikan` | - | - | ❌ |
| Pemeriksaan Lab | `pelayanan/lab` | - | - | ❌ |

### 4. Persediaan
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Daftar Produk | `persediaan/produk` | - | - | ❌ |
| Defecta | `persediaan/defecta` | `persediaan/defecta` → `persediaan/defecta/index` | `Persediaan/Defecta.php` | ✅ |
| Stok Kadaluarsa | `persediaan/kadaluarsa` | `persediaan/stok-kadaluarsa` → `persediaan/stokkadaluarsa/index` | `Persediaan/StokKadaluarsa.php` | ⚠️ URL mismatch |
| Stok Opname | `persediaan/opname` | `persediaan/stok-opname` → `persediaan/stokopname/index` | `Persediaan/StokOpname.php` | ⚠️ URL mismatch |
| Penyesuaian Stok | `persediaan/penyesuaian` | `persediaan/penyesuaian-stok` → `persediaan/penyesuaianstok/index` | `Persediaan/PenyesuaianStok.php` | ⚠️ URL mismatch |
| Perpindahan Stok | `persediaan/perpindahan` | `persediaan/perpindahan-stok` → `persediaan/perpindahanstok/index` | `Persediaan/PerpindahanStok.php` | ⚠️ URL mismatch |

### 5. Pembelian
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Rencana Pembelian | `pembelian/rencana` | - | - | ❌ |
| Pesanan Pembelian | `pembelian/pesanan` | `pembelian/pesanan-pembelian` → `pembelian/pesananpembelian/index` | `Pembelian/PesananPembelian.php` | ⚠️ URL mismatch |
| Faktur Pembelian | `pembelian/faktur` | `pembelian/faktur-pembelian` → `pembelian/fakturpembelian/index` | `Pembelian/FakturPembelian.php` | ⚠️ URL mismatch |
| Retur Pembelian | `pembelian/retur` | - | - | ❌ |
| Faktur AI | `pembelian/faktur-ai` | - | - | ❌ |

### 6. Konsinyasi
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Konsinyasi Masuk | `konsinyasi/masuk` | - | - | ❌ |
| Status Konsinyasi | `konsinyasi/status` | - | - | ❌ |
| Retur Konsinyasi | `konsinyasi/retur` | - | - | ❌ |
| Stok Konsinyasi | `konsinyasi/stok` | - | - | ❌ |

### 7. Program Promo
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Bundel / Paket | `promo/bundel` | - | - | ❌ |
| Diskon Produk | `promo/diskon` | - | - | ❌ |
| Member Poin | `promo/poin` | - | - | ❌ |
| Voucher Diskon | `promo/voucher` | - | - | ❌ |

### 8. Keuangan
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Daftar Akun Kas | `keuangan/akun-kas` | `keuangan/akun-kas` → `keuangan/daftarakunkas/index` | `Keuangan/DaftarAkunKas.php` | ✅ |
| Buku Kas | `keuangan/buku-kas` | `keuangan/buku-kas` → `keuangan/bukukas/index` | `Keuangan/BukuKas.php` | ✅ |
| Utang Usaha | `keuangan/utang` | `keuangan/utang-usaha` → `keuangan/utangusaha/index` | `Keuangan/UtangUsaha.php` | ⚠️ URL mismatch |
| Piutang Usaha | `keuangan/piutang` | `keuangan/piutang-usaha` → `keuangan/piutangusaha/index` | `Keuangan/PiutangUsaha.php` | ⚠️ URL mismatch |

### 9. Akuntansi
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Master CoA | `akuntansi/coa` | - | - | ❌ |
| Buku Besar | `akuntansi/buku-besar` | - | - | ❌ |
| Aktiva Tetap | `akuntansi/aktiva` | - | - | ❌ |

### 10. Kontak
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Pelanggan | `kontak/pelanggan` | `kontak/pelanggan` → `kontak/pelanggan/index` | `Kontak/Pelanggan.php` | ✅ |
| Sales / Pelayan | `kontak/sales` | `kontak/sales` → `kontak/sales/index` | `Kontak/Sales.php` | ✅ |
| Dokter | `kontak/dokter` | `kontak/dokter` → `kontak/dokter/index` | `Kontak/Dokter.php` | ✅ |
| Supplier | `kontak/supplier` | `kontak/supplier` → `kontak/supplier/index` | `Kontak/Supplier.php` | ✅ |

### 11. Laporan
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Laporan Penjualan | `laporan/penjualan` | - | - | ❌ |
| Laporan Pembelian | `laporan/pembelian` | - | - | ❌ |
| Laporan Persediaan | `laporan/persediaan` | - | - | ❌ |
| Laporan Keuangan | `laporan/keuangan` | - | - | ❌ |

### 12. Analisis
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Analisis Pareto | `analisis/pareto` | - | - | ❌ |
| Analisis Pembelian | `analisis/pembelian` | - | - | ❌ |
| Analisis Harga | `analisis/harga` | - | - | ❌ |

### 13. Manajemen Pengguna
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Daftar Pengguna | `manajemen/pengguna` | `manajemen-pengguna/daftar-pengguna` → `manajemenpengguna/daftarpengguna/index` | `ManajemenPengguna/DaftarPengguna.php` | ⚠️ URL mismatch |
| Peran & Hak Akses | `manajemen/peran` | `manajemen-pengguna/peran-hak-akses` → `manajemenpengguna/peranhakakses/index` | `ManajemenPengguna/PeranHakAkses.php` | ⚠️ URL mismatch |
| Jadwal Kerja | `manajemen/jadwal` | - | - | ❌ |
| Presensi Karyawan | `manajemen/presensi` | - | - | ❌ |
| Riwayat Shift | `manajemen/shift` | - | - | ❌ |
| Log Aktivitas | `manajemen/log` | - | - | ❌ |

### 14. KPI
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Parameter KPI | `kpi/parameter` | - | - | ❌ |
| KPI Karyawan | `kpi/karyawan` | - | - | ❌ |
| Pencapaian KPI | `kpi/pencapaian` | - | - | ❌ |
| Laporan KPI | `kpi/laporan` | - | - | ❌ |

### 15. Gaji
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Gaji Pokok | `gaji/pokok` | - | - | ❌ |
| Tunjangan | `gaji/tunjangan` | - | - | ❌ |
| Potongan Gaji | `gaji/potongan` | - | - | ❌ |
| Perhitungan Gaji | `gaji/perhitungan` | - | - | ❌ |
| Pembayaran Gaji | `gaji/pembayaran` | - | - | ❌ |

### 16. Pengaturan
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Profil Apotek | `pengaturan/profil` | - | - | ❌ |
| Metode Pembayaran | `pengaturan/metode-pembayaran` | - | - | ❌ |
| Konfigurasi | `pengaturan/konfigurasi` | - | - | ❌ |
| Integrasi Platform | `pengaturan/integrasi` | - | - | ❌ |
| Apotek Online | `pengaturan/online` | - | - | ❌ |
| Website | `pengaturan/website` | - | - | ❌ |
| Katalog Online | `pengaturan/katalog` | - | - | ❌ |
| WhatsApp Tools | `pengaturan/whatsapp` | - | - | ❌ |

### 17. Multi Outlet
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Permintaan Mutasi | `outlet/permintaan` | - | - | ❌ |
| Mutasi Antar Outlet | `outlet/mutasi` | - | - | ❌ |
| Produk Mitra | `outlet/produk-mitra` | - | - | ❌ |
| Outlet Mitra | `outlet/outlet-mitra` | - | - | ❌ |

### 18. Master Data
| Menu | Sidebar URL | Route | Controller | Status |
|------|-------------|-------|------------|--------|
| Master Produk | `master/produk` | `masterdata/masterproduk` → `masterdata/masterproduk/index` | `MasterData/MasterProduk.php` | ⚠️ URL mismatch |
| Master Kategori | `master/kategori` | `masterdata/masterkategori` → `masterdata/masterkategori/index` | `MasterData/MasterKategori.php` | ⚠️ URL mismatch |
| Master Satuan | `master/satuan` | - | - | ❌ |
| Master Rak | `master/rak` | - | - | ❌ |
| Master Gudang | `master/gudang` | - | - | ❌ |
| Master Pajak | `master/pajak` | - | - | ❌ |
| Master Shift | `master/shift` | - | - | ❌ |
| Master Membership | `master/membership` | - | - | ❌ |
| Jenis Pelayanan | `master/pelayanan` | - | - | ❌ |
| Jenis Antrian | `master/antrian` | - | - | ❌ |
| Produk Lab | `master/produk-lab` | - | - | ❌ |
| Item Pemeriksaan | `master/item-pemeriksaan` | - | - | ❌ |

## Ringkasan

### Total Menu: 91
- ✅ **Berfungsi dengan benar**: 18 menu (19.8%)
- ⚠️ **Ada controller tapi URL mismatch**: 12 menu (13.2%)
- ❌ **Belum ada controller/routes**: 61 menu (67.0%)

### Masalah yang Ditemukan:

1. **URL Mismatch** (12 menu):
   - Sidebar menggunakan URL singkat, tapi routes menggunakan URL lengkap dengan dash
   - Contoh: `penjualan/pesanan` vs `penjualan/pesanan-penjualan`
   - Contoh: `master/produk` vs `masterdata/masterproduk`

2. **Menu Belum Ada** (61 menu):
   - Sebagian besar modul belum memiliki controller dan routes
   - Perlu dibuat controller, routes, dan views untuk modul-modul ini

### Rekomendasi:

1. **Perbaiki URL Mismatch**: Update sidebar.php atau routes.php agar konsisten
2. **Buat Controller yang Belum Ada**: Prioritaskan modul yang sering digunakan
3. **Tambahkan Routes**: Pastikan semua menu memiliki route yang valid


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth Routes (eksplisit - diperlukan agar AJAX login tidak 404)
$route['auth/login'] = 'Auth/login';
$route['auth/logout'] = 'Auth/logout';

// Master Data Routes
$route['masterdata/masterkategori'] = 'MasterData/MasterKategori/index';
$route['masterdata/masterkategori/create'] = 'MasterData/MasterKategori/create';
$route['masterdata/masterkategori/store'] = 'MasterData/MasterKategori/store';
$route['masterdata/masterkategori/edit/(:num)'] = 'MasterData/MasterKategori/edit/$1';
$route['masterdata/masterkategori/update/(:num)'] = 'MasterData/MasterKategori/update/$1';
$route['masterdata/masterkategori/view/(:num)'] = 'MasterData/MasterKategori/view/$1';
$route['masterdata/masterkategori/ajax_list'] = 'MasterData/MasterKategori/ajax_list';
$route['masterdata/masterkategori/ajax_get/(:num)'] = 'MasterData/MasterKategori/ajax_get/$1';
$route['masterdata/masterkategori/ajax_save'] = 'MasterData/MasterKategori/ajax_save';
$route['masterdata/masterkategori/ajax_delete/(:num)'] = 'MasterData/MasterKategori/ajax_delete/$1';
$route['masterdata/masterkategori/ajax_search'] = 'MasterData/MasterKategori/ajax_search';

// Master Produk Routes
$route['masterdata/masterproduk'] = 'MasterData/MasterProduk/index';
$route['masterdata/masterproduk/create'] = 'MasterData/MasterProduk/create';
$route['masterdata/masterproduk/store'] = 'MasterData/MasterProduk/store';
$route['masterdata/masterproduk/edit/(:num)'] = 'MasterData/MasterProduk/edit/$1';
$route['masterdata/masterproduk/update/(:num)'] = 'MasterData/MasterProduk/update/$1';
$route['masterdata/masterproduk/view/(:num)'] = 'MasterData/MasterProduk/view/$1';
$route['masterdata/masterproduk/ajax_list'] = 'MasterData/MasterProduk/ajax_list';
$route['masterdata/masterproduk/ajax_get/(:num)'] = 'MasterData/MasterProduk/ajax_get/$1';
$route['masterdata/masterproduk/ajax_save'] = 'MasterData/MasterProduk/ajax_save';
$route['masterdata/masterproduk/ajax_delete/(:num)'] = 'MasterData/MasterProduk/ajax_delete/$1';
$route['masterdata/masterproduk/ajax_search'] = 'MasterData/MasterProduk/ajax_search';
$route['masterdata/masterproduk/kartu_stok/(:num)'] = 'MasterData/MasterProduk/kartu_stok/$1';
$route['masterdata/masterproduk/ajax_list_kartu_stok'] = 'MasterData/MasterProduk/ajax_list_kartu_stok';

// Persediaan Routes
$route['Persediaan/Produk'] = 'Persediaan/Produk/index';
$route['Persediaan/Produk/ajax_list'] = 'Persediaan/Produk/ajax_list';

$route['Persediaan/Defecta'] = 'Persediaan/Defecta/index';
$route['Persediaan/Defecta/ajax_list'] = 'Persediaan/Defecta/ajax_list';
$route['Persediaan/Defecta/ajax_save'] = 'Persediaan/Defecta/ajax_save';
$route['Persediaan/Defecta/ajax_approve/(:num)'] = 'Persediaan/Defecta/ajax_approve/$1';

$route['persediaan/stok-kadaluarsa'] = 'Persediaan/StokKadaluarsa/index';
$route['persediaan/stok-kadaluarsa/ajax_list'] = 'Persediaan/StokKadaluarsa/ajax_list';
$route['persediaan/stok_kadaluarsa'] = 'Persediaan/StokKadaluarsa/index';
$route['persediaan/stok_kadaluarsa/ajax_list'] = 'Persediaan/StokKadaluarsa/ajax_list';

$route['persediaan/stok-opname'] = 'Persediaan/StokOpname/index';
$route['persediaan/stok-opname/ajax_list'] = 'Persediaan/StokOpname/ajax_list';
$route['persediaan/stok-opname/ajax_save'] = 'Persediaan/StokOpname/ajax_save';
$route['persediaan/stok-opname/ajax_complete/(:num)'] = 'Persediaan/StokOpname/ajax_complete/$1';

$route['persediaan/penyesuaian-stok'] = 'Persediaan/PenyesuaianStok/index';
$route['persediaan/penyesuaian-stok/ajax_list'] = 'Persediaan/PenyesuaianStok/ajax_list';
$route['persediaan/penyesuaian-stok/ajax_save'] = 'Persediaan/PenyesuaianStok/ajax_save';
$route['persediaan/penyesuaian-stok/ajax_approve/(:num)'] = 'Persediaan/PenyesuaianStok/ajax_approve/$1';
$route['persediaan/penyesuaian_stok'] = 'Persediaan/PenyesuaianStok/index';
$route['persediaan/penyesuaian_stok/ajax_list'] = 'Persediaan/PenyesuaianStok/ajax_list';
$route['persediaan/penyesuaian_stok/ajax_save'] = 'Persediaan/PenyesuaianStok/ajax_save';
$route['persediaan/penyesuaian_stok/ajax_approve/(:num)'] = 'Persediaan/PenyesuaianStok/ajax_approve/$1';

$route['persediaan/perpindahan-stok'] = 'Persediaan/PerpindahanStok/index';
$route['persediaan/perpindahan-stok/ajax_list'] = 'Persediaan/PerpindahanStok/ajax_list';
$route['persediaan/perpindahan-stok/ajax_save'] = 'Persediaan/PerpindahanStok/ajax_save';
$route['persediaan/perpindahan-stok/ajax_complete/(:num)'] = 'Persediaan/PerpindahanStok/ajax_complete/$1';
$route['persediaan/perpindahan_stok'] = 'Persediaan/PerpindahanStok/index';
$route['persediaan/perpindahan_stok/ajax_list'] = 'Persediaan/PerpindahanStok/ajax_list';
$route['persediaan/perpindahan_stok/ajax_save'] = 'Persediaan/PerpindahanStok/ajax_save';
$route['persediaan/perpindahan_stok/ajax_complete/(:num)'] = 'Persediaan/PerpindahanStok/ajax_complete/$1';

// Kontak Routes
$route['Kontak/Pelanggan'] = 'Kontak/Pelanggan/index';
$route['Kontak/Pelanggan/create'] = 'Kontak/Pelanggan/create';
$route['Kontak/Pelanggan/store'] = 'Kontak/Pelanggan/store';
$route['Kontak/Pelanggan/edit/(:num)'] = 'Kontak/Pelanggan/edit/$1';
$route['Kontak/Pelanggan/update/(:num)'] = 'Kontak/Pelanggan/update/$1';
$route['Kontak/Pelanggan/view/(:num)'] = 'Kontak/Pelanggan/view/$1';
$route['Kontak/Pelanggan/ajax_list'] = 'Kontak/Pelanggan/ajax_list';
$route['Kontak/Pelanggan/ajax_get/(:num)'] = 'Kontak/Pelanggan/ajax_get/$1';
$route['Kontak/Pelanggan/ajax_save'] = 'Kontak/Pelanggan/ajax_save';
$route['Kontak/Pelanggan/ajax_delete/(:num)'] = 'Kontak/Pelanggan/ajax_delete/$1';

$route['Kontak/Sales'] = 'Kontak/Sales/index';
$route['Kontak/Sales/create'] = 'Kontak/Sales/create';
$route['Kontak/Sales/store'] = 'Kontak/Sales/store';
$route['Kontak/Sales/edit/(:num)'] = 'Kontak/Sales/edit/$1';
$route['Kontak/Sales/update/(:num)'] = 'Kontak/Sales/update/$1';
$route['Kontak/Sales/view/(:num)'] = 'Kontak/Sales/view/$1';
$route['Kontak/Sales/ajax_list'] = 'Kontak/Sales/ajax_list';
$route['Kontak/Sales/ajax_get/(:num)'] = 'Kontak/Sales/ajax_get/$1';
$route['Kontak/Sales/ajax_save'] = 'Kontak/Sales/ajax_save';
$route['Kontak/Sales/ajax_delete/(:num)'] = 'Kontak/Sales/ajax_delete/$1';

// Kontak Routes - Dokter dihapus (bukan fitur POS standard)
$route['Kontak/Supplier'] = 'Kontak/Supplier/index';
$route['Kontak/Supplier/create'] = 'Kontak/Supplier/create';
$route['Kontak/Supplier/store'] = 'Kontak/Supplier/store';
$route['Kontak/Supplier/edit/(:num)'] = 'Kontak/Supplier/edit/$1';
$route['Kontak/Supplier/update/(:num)'] = 'Kontak/Supplier/update/$1';
$route['Kontak/Supplier/view/(:num)'] = 'Kontak/Supplier/view/$1';
$route['Kontak/Supplier/ajax_list'] = 'Kontak/Supplier/ajax_list';
$route['Kontak/Supplier/ajax_get/(:num)'] = 'Kontak/Supplier/ajax_get/$1';
$route['Kontak/Supplier/ajax_save'] = 'Kontak/Supplier/ajax_save';
$route['Kontak/Supplier/ajax_delete/(:num)'] = 'Kontak/Supplier/ajax_delete/$1';

// Penjualan Routes
$route['Penjualan/Kasir'] = 'Penjualan/Kasir/index';
$route['Penjualan/Kasir/ajax_search_produk'] = 'Penjualan/Kasir/ajax_search_produk';
$route['Penjualan/Kasir/ajax_get_produk_by_barcode'] = 'Penjualan/Kasir/ajax_get_produk_by_barcode';
$route['Penjualan/Kasir/ajax_save'] = 'Penjualan/Kasir/ajax_save';
$route['Penjualan/Kasir/ajax_get_print/(:num)'] = 'Penjualan/Kasir/ajax_get_print/$1';

$route['penjualan/daftar-penjualan'] = 'Penjualan/DaftarPenjualan/index';
$route['penjualan/daftar-penjualan/ajax_list'] = 'Penjualan/DaftarPenjualan/ajax_list';
$route['penjualan/daftar-penjualan/ajax_get/(:num)'] = 'Penjualan/DaftarPenjualan/ajax_get/$1';
$route['penjualan/daftar-penjualan/ajax_complete/(:num)'] = 'Penjualan/DaftarPenjualan/ajax_complete/$1';
$route['penjualan/daftar-penjualan/ajax_cancel/(:num)'] = 'Penjualan/DaftarPenjualan/ajax_cancel/$1';

$route['penjualan/pesanan-penjualan'] = 'Penjualan/PesananPenjualan/index';
$route['penjualan/pesanan-penjualan/ajax_list'] = 'Penjualan/PesananPenjualan/ajax_list';
$route['penjualan/pesanan-penjualan/ajax_get/(:num)'] = 'Penjualan/PesananPenjualan/ajax_get/$1';
$route['penjualan/pesanan-penjualan/ajax_save'] = 'Penjualan/PesananPenjualan/ajax_save';
$route['penjualan/pesanan-penjualan/ajax_confirm/(:num)'] = 'Penjualan/PesananPenjualan/ajax_confirm/$1';

// Penjualan Routes - URL Shortcuts
$route['penjualan/pesanan'] = 'Penjualan/PesananPenjualan/index';
$route['penjualan/daftar'] = 'Penjualan/DaftarPenjualan/index';
$route['penjualan/retur'] = 'Penjualan/ReturPenjualan/index';
$route['Penjualan/Tertolak'] = 'Penjualan/Tertolak/index';
$route['penjualan/faktur'] = 'Penjualan/FakturPenjualan/index';

// Pembelian Routes
$route['pembelian/pesanan-pembelian'] = 'Pembelian/PesananPembelian/index';
$route['pembelian/pesanan-pembelian/ajax_list'] = 'Pembelian/PesananPembelian/ajax_list';
$route['pembelian/pesanan-pembelian/ajax_get/(:num)'] = 'Pembelian/PesananPembelian/ajax_get/$1';
$route['pembelian/pesanan-pembelian/ajax_save'] = 'Pembelian/PesananPembelian/ajax_save';
$route['pembelian/pesanan-pembelian/ajax_receive/(:num)'] = 'Pembelian/PesananPembelian/ajax_receive/$1';
$route['pembelian/pesanan-pembelian/ajax_send/(:num)'] = 'Pembelian/PesananPembelian/ajax_send/$1';

$route['pembelian/faktur-pembelian'] = 'Pembelian/FakturPembelian/index';
$route['pembelian/faktur-pembelian/ajax_list'] = 'Pembelian/FakturPembelian/ajax_list';
$route['pembelian/faktur-pembelian/ajax_get/(:num)'] = 'Pembelian/FakturPembelian/ajax_get/$1';
$route['pembelian/faktur-pembelian/ajax_save'] = 'Pembelian/FakturPembelian/ajax_save';

// Pembelian Routes - URL Shortcuts
$route['pembelian/pesanan'] = 'Pembelian/PesananPembelian/index';
$route['pembelian/faktur'] = 'Pembelian/FakturPembelian/index';
$route['pembelian/faktur/ajax_list'] = 'Pembelian/FakturPembelian/ajax_list';
$route['pembelian/faktur/ajax_get/(:num)'] = 'Pembelian/FakturPembelian/ajax_get/$1';
$route['pembelian/faktur/ajax_save'] = 'Pembelian/FakturPembelian/ajax_save';
$route['pembelian/faktur/ajax_pay/(:num)'] = 'Pembelian/FakturPembelian/ajax_pay/$1';
$route['pembelian/faktur/ajax_delete/(:num)'] = 'Pembelian/FakturPembelian/ajax_delete/$1';
$route['pembelian/faktur-pembelian/ajax_pay/(:num)'] = 'Pembelian/FakturPembelian/ajax_pay/$1';

// Retur Pembelian Routes
$route['pembelian/retur-pembelian'] = 'Pembelian/ReturPembelian/index';
$route['pembelian/retur-pembelian/ajax_list'] = 'Pembelian/ReturPembelian/ajax_list';
$route['pembelian/retur-pembelian/ajax_get/(:num)'] = 'Pembelian/ReturPembelian/ajax_get/$1';
$route['pembelian/retur-pembelian/ajax_save'] = 'Pembelian/ReturPembelian/ajax_save';
$route['pembelian/retur'] = 'Pembelian/ReturPembelian/index';

// Keuangan Routes
$route['keuangan/akun-kas'] = 'Keuangan/DaftarAkunKas/index';
$route['keuangan/akun-kas/create'] = 'Keuangan/DaftarAkunKas/create';
$route['keuangan/akun-kas/store'] = 'Keuangan/DaftarAkunKas/store';
$route['keuangan/akun-kas/edit/(:num)'] = 'Keuangan/DaftarAkunKas/edit/$1';
$route['keuangan/akun-kas/update/(:num)'] = 'Keuangan/DaftarAkunKas/update/$1';
$route['keuangan/akun-kas/view/(:num)'] = 'Keuangan/DaftarAkunKas/view/$1';
$route['keuangan/akun-kas/ajax_list'] = 'Keuangan/DaftarAkunKas/ajax_list';
$route['keuangan/akun-kas/ajax_get/(:num)'] = 'Keuangan/DaftarAkunKas/ajax_get/$1';
$route['keuangan/akun-kas/ajax_save'] = 'Keuangan/DaftarAkunKas/ajax_save';
$route['keuangan/akun-kas/ajax_delete/(:num)'] = 'Keuangan/DaftarAkunKas/ajax_delete/$1';

$route['keuangan/buku-kas'] = 'Keuangan/BukuKas/index';
$route['keuangan/buku-kas/(:num)'] = 'Keuangan/BukuKas/index/$1';
$route['keuangan/buku-kas/ajax_list'] = 'Keuangan/BukuKas/ajax_list';
$route['keuangan/buku-kas/ajax_save'] = 'Keuangan/BukuKas/ajax_save';
$route['keuangan/buku-kas/ajax_delete/(:num)'] = 'Keuangan/BukuKas/ajax_delete/$1';

$route['keuangan/utang-usaha'] = 'Keuangan/UtangUsaha/index';
$route['keuangan/utang-usaha/ajax_list'] = 'Keuangan/UtangUsaha/ajax_list';
$route['keuangan/utang-usaha/ajax_get/(:num)'] = 'Keuangan/UtangUsaha/ajax_get/$1';
$route['keuangan/utang-usaha/ajax_bayar/(:num)'] = 'Keuangan/UtangUsaha/ajax_bayar/$1';
$route['keuangan/utang-usaha/ajax_save'] = 'Keuangan/UtangUsaha/ajax_save';
$route['keuangan/utang-usaha/ajax_delete/(:num)'] = 'Keuangan/UtangUsaha/ajax_delete/$1';

$route['keuangan/piutang-usaha'] = 'Keuangan/PiutangUsaha/index';
$route['keuangan/piutang-usaha/ajax_list'] = 'Keuangan/PiutangUsaha/ajax_list';
$route['keuangan/piutang-usaha/ajax_get/(:num)'] = 'Keuangan/PiutangUsaha/ajax_get/$1';
$route['keuangan/piutang-usaha/ajax_terima_bayar/(:num)'] = 'Keuangan/PiutangUsaha/ajax_terima_bayar/$1';
$route['keuangan/piutang-usaha/ajax_save'] = 'Keuangan/PiutangUsaha/ajax_save';
$route['keuangan/piutang-usaha/ajax_delete/(:num)'] = 'Keuangan/PiutangUsaha/ajax_delete/$1';

// Keuangan Routes - URL Shortcuts
$route['keuangan/utang'] = 'Keuangan/UtangUsaha/index';
$route['keuangan/piutang'] = 'Keuangan/PiutangUsaha/index';

// Manajemen Pengguna Routes
$route['manajemen-pengguna/daftar-pengguna'] = 'ManajemenPengguna/DaftarPengguna/index';
$route['manajemen-pengguna/daftar-pengguna/create'] = 'ManajemenPengguna/DaftarPengguna/create';
$route['manajemen-pengguna/daftar-pengguna/store'] = 'ManajemenPengguna/DaftarPengguna/store';
$route['manajemen-pengguna/daftar-pengguna/edit/(:num)'] = 'ManajemenPengguna/DaftarPengguna/edit/$1';
$route['manajemen-pengguna/daftar-pengguna/update/(:num)'] = 'ManajemenPengguna/DaftarPengguna/update/$1';
$route['manajemen-pengguna/daftar-pengguna/view/(:num)'] = 'ManajemenPengguna/DaftarPengguna/view/$1';
$route['manajemen-pengguna/daftar-pengguna/ajax_list'] = 'ManajemenPengguna/DaftarPengguna/ajax_list';
$route['manajemen-pengguna/daftar-pengguna/ajax_get/(:num)'] = 'ManajemenPengguna/DaftarPengguna/ajax_get/$1';
$route['manajemen-pengguna/daftar-pengguna/ajax_save'] = 'ManajemenPengguna/DaftarPengguna/ajax_save';
$route['manajemen-pengguna/daftar-pengguna/ajax_delete/(:num)'] = 'ManajemenPengguna/DaftarPengguna/ajax_delete/$1';

$route['manajemen-pengguna/peran-hak-akses'] = 'ManajemenPengguna/PeranHakAkses/index';
$route['manajemen-pengguna/peran-hak-akses/create'] = 'ManajemenPengguna/PeranHakAkses/create';
$route['manajemen-pengguna/peran-hak-akses/store'] = 'ManajemenPengguna/PeranHakAkses/store';
$route['manajemen-pengguna/peran-hak-akses/edit/(:num)'] = 'ManajemenPengguna/PeranHakAkses/edit/$1';
$route['manajemen-pengguna/peran-hak-akses/update/(:num)'] = 'ManajemenPengguna/PeranHakAkses/update/$1';
$route['manajemen-pengguna/peran-hak-akses/view/(:num)'] = 'ManajemenPengguna/PeranHakAkses/view/$1';
$route['manajemen-pengguna/peran-hak-akses/ajax_list'] = 'ManajemenPengguna/PeranHakAkses/ajax_list';
$route['manajemen-pengguna/peran-hak-akses/ajax_get/(:num)'] = 'ManajemenPengguna/PeranHakAkses/ajax_get/$1';
$route['manajemen-pengguna/peran-hak-akses/ajax_get_permissions/(:num)'] = 'ManajemenPengguna/PeranHakAkses/ajax_get_permissions/$1';
$route['manajemen-pengguna/peran-hak-akses/ajax_save'] = 'ManajemenPengguna/PeranHakAkses/ajax_save';
$route['manajemen-pengguna/peran-hak-akses/ajax_save_permissions/(:num)'] = 'ManajemenPengguna/PeranHakAkses/ajax_save_permissions/$1';
$route['manajemen-pengguna/peran-hak-akses/ajax_delete/(:num)'] = 'ManajemenPengguna/PeranHakAkses/ajax_delete/$1';

// Pelayanan Routes - dihapus (bukan fitur POS standard)

// Persediaan Routes - URL Shortcuts
$route['persediaan/kadaluarsa'] = 'Persediaan/StokKadaluarsa/index';
$route['persediaan/opname'] = 'Persediaan/StokOpname/index';
$route['persediaan/penyesuaian'] = 'Persediaan/PenyesuaianStok/index';
$route['persediaan/perpindahan'] = 'Persediaan/PerpindahanStok/index';

// Konsinyasi Routes
$route['Konsinyasi/Masuk'] = 'Konsinyasi/Masuk/index';
$route['Konsinyasi/Status'] = 'Konsinyasi/Status/index';
$route['Konsinyasi/Retur'] = 'Konsinyasi/Retur/index';
$route['Konsinyasi/Stok'] = 'Konsinyasi/Stok/index';

// Akuntansi Routes
$route['Akuntansi/CoA'] = 'Akuntansi/CoA/index';
$route['akuntansi/buku-besar'] = 'Akuntansi/BukuBesar/index';
$route['akuntansi/aktiva'] = 'Akuntansi/AktivaTetap/index';

// Manajemen Routes
$route['manajemen'] = 'Manajemen/index';
$route['manajemen/pengguna'] = 'ManajemenPengguna/DaftarPengguna/index';
$route['manajemen/pengguna/create'] = 'ManajemenPengguna/DaftarPengguna/create';
$route['manajemen/pengguna/store'] = 'ManajemenPengguna/DaftarPengguna/store';
$route['manajemen/pengguna/edit/(:num)'] = 'ManajemenPengguna/DaftarPengguna/edit/$1';
$route['manajemen/pengguna/update/(:num)'] = 'ManajemenPengguna/DaftarPengguna/update/$1';
$route['manajemen/pengguna/view/(:num)'] = 'ManajemenPengguna/DaftarPengguna/view/$1';
$route['manajemen/pengguna/ajax_list'] = 'ManajemenPengguna/DaftarPengguna/ajax_list';
$route['manajemen/pengguna/ajax_get/(:num)'] = 'ManajemenPengguna/DaftarPengguna/ajax_get/$1';
$route['manajemen/pengguna/ajax_save'] = 'ManajemenPengguna/DaftarPengguna/ajax_save';
$route['manajemen/pengguna/ajax_delete/(:num)'] = 'ManajemenPengguna/DaftarPengguna/ajax_delete/$1';
$route['manajemen/peran'] = 'ManajemenPengguna/PeranHakAkses/index';
// Manajemen HR Routes - Jadwal/Presensi/Shift dihapus (bukan fitur POS standard)
$route['Manajemen/Log'] = 'Manajemen/Log/index';
$route['Manajemen/Log/ajax_list'] = 'Manajemen/Log/ajax_list';
$route['Manajemen/Log/ajax_get/(:num)'] = 'Manajemen/Log/ajax_get/$1';
$route['Manajemen/Log/ajax_delete/(:num)'] = 'Manajemen/Log/ajax_delete/$1';
$route['Manajemen/Log/ajax_export'] = 'Manajemen/Log/ajax_export';

// Analisis Routes
$route['analisis'] = 'Analisis/index';
$route['Analisis/Pareto'] = 'Analisis/Pareto/index';
$route['Analisis/Pembelian'] = 'Analisis/Pembelian/index';
$route['Analisis/Harga'] = 'Analisis/Harga/index';

// KPI Routes
$route['kpi'] = 'KPI/index';

// Gaji Routes
$route['gaji'] = 'Gaji/index';

// Pengaturan Routes
$route['pengaturan'] = 'Pengaturan/index';
$route['pengaturan/profil'] = 'Pengaturan/ProfilApotek/index';
$route['pengaturan/profil/update'] = 'Pengaturan/ProfilApotek/update';
$route['pengaturan/konfigurasi'] = 'Pengaturan/Konfigurasi/index';
$route['pengaturan/konfigurasi/update'] = 'Pengaturan/Konfigurasi/update';
$route['pengaturan/metode-pembayaran'] = 'Pengaturan/MetodePembayaran/index';
$route['Pengaturan/Konfigurasi'] = 'Pengaturan/Konfigurasi/index';

// Dashboard Routes
$route['dashboard'] = 'Dashboard/Dashboard/index';
$route['dashboard/penjualan'] = 'Dashboard/Dashboard/penjualan';
$route['dashboard/pembelian'] = 'Dashboard/Dashboard/pembelian';
$route['dashboard/persediaan'] = 'Dashboard/Dashboard/persediaan';
$route['dashboard/keuangan'] = 'Dashboard/Dashboard/keuangan';
$route['dashboard/ajax_statistik'] = 'Dashboard/Dashboard/ajax_statistik';

require APPPATH . 'config/routes_extended.php';

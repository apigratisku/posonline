<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extended routes - CRUD aliases, shortcuts, laporan, konsinyasi, placeholder
 */

if (!function_exists('apotik_register_crud_routes')) {
    function apotik_register_crud_routes(&$route, $url_prefix, $controller_path, $with_search = false) {
        $route[$url_prefix] = $controller_path . '/index';
        $route[$url_prefix . '/create'] = $controller_path . '/create';
        $route[$url_prefix . '/store'] = $controller_path . '/store';
        $route[$url_prefix . '/edit/(:num)'] = $controller_path . '/edit/$1';
        $route[$url_prefix . '/update/(:num)'] = $controller_path . '/update/$1';
        $route[$url_prefix . '/view/(:num)'] = $controller_path . '/view/$1';
        $route[$url_prefix . '/ajax_list'] = $controller_path . '/ajax_list';
        $route[$url_prefix . '/ajax_get/(:num)'] = $controller_path . '/ajax_get/$1';
        $route[$url_prefix . '/ajax_save'] = $controller_path . '/ajax_save';
        $route[$url_prefix . '/ajax_delete/(:num)'] = $controller_path . '/ajax_delete/$1';
        if ($with_search) {
            $route[$url_prefix . '/ajax_search'] = $controller_path . '/ajax_search';
        }
    }
}

if (!function_exists('apotik_register_crud_routes_with_aliases')) {
    function apotik_register_crud_routes_with_aliases(&$route, $url_prefix, $controller_path, $with_search = false) {
        apotik_register_crud_routes($route, $url_prefix, $controller_path, $with_search);
        $lower = strtolower($url_prefix);
        if ($lower !== $url_prefix) {
            apotik_register_crud_routes($route, $lower, $controller_path, $with_search);
        }
    }
}

if (!function_exists('apotik_register_report_routes')) {
    function apotik_register_report_routes(&$route, $url_prefix, $controller_path) {
        $route[$url_prefix] = $controller_path . '/index';
        $route[$url_prefix . '/ajax_list'] = $controller_path . '/ajax_list';
        $route[$url_prefix . '/ajax_export'] = $controller_path . '/ajax_export';
        $lower = strtolower($url_prefix);
        if ($lower !== $url_prefix) {
            $route[$lower] = $controller_path . '/index';
            $route[$lower . '/ajax_list'] = $controller_path . '/ajax_list';
            $route[$lower . '/ajax_export'] = $controller_path . '/ajax_export';
        }
    }
}

// Master Data - full routes for modules without explicit routes
$master_crud = array(
    array('masterdata/mastersatuan', 'MasterData/MasterSatuan', 'master/satuan'),
    array('masterdata/masterrak', 'MasterData/MasterRak', 'master/rak'),
    array('masterdata/mastergudang', 'MasterData/MasterGudang', 'master/gudang'),
    array('masterdata/masterpajak', 'MasterData/MasterPajak', 'master/pajak'),
    array('masterdata/mastershift', 'MasterData/MasterShift', 'master/shift'),
    array('masterdata/mastermembership', 'MasterData/MasterMembership', 'master/membership'),
);
foreach ($master_crud as $item) {
    apotik_register_crud_routes($route, $item[0], $item[1]);
    apotik_register_crud_routes($route, $item[2], $item[1]);
}
// Master Data - 4 modul baru
$new_master_crud = array(
    array('masterdata/masterpelayanan', 'MasterData/MasterJenisPelayanan', 'master/pelayanan'),
    array('masterdata/masterantrian', 'MasterData/MasterJenisAntrian', 'master/antrian'),
    array('masterdata/masterproduklab', 'MasterData/MasterProdukLab', 'master/produk-lab'),
    array('masterdata/masteritempemeriksaan', 'MasterData/MasterItemPemeriksaan', 'master/item-pemeriksaan'),
);
foreach ($new_master_crud as $item) {
    apotik_register_crud_routes($route, $item[0], $item[1], true);
    apotik_register_crud_routes($route, $item[2], $item[1], true);
}
$route['masterdata/masterproduk/ajax_rak_by_gudang/(:num)'] = 'MasterData/MasterProduk/ajax_rak_by_gudang/$1';
$route['masterdata/masterproduk/print_barcode/(:num)'] = 'MasterData/MasterProduk/print_barcode/$1';
$route['master/produk/print_barcode/(:num)'] = 'MasterData/MasterProduk/print_barcode/$1';
// Master shortcuts for produk & kategori (already have masterdata routes)
apotik_register_crud_routes($route, 'master/produk', 'MasterData/MasterProduk', true);
$route['master/produk/kartu_stok/(:num)'] = 'MasterData/MasterProduk/kartu_stok/$1';
$route['master/produk/ajax_list_kartu_stok'] = 'MasterData/MasterProduk/ajax_list_kartu_stok';
apotik_register_crud_routes($route, 'master/kategori', 'MasterData/MasterKategori', true);

// Program Promo shortcuts
$promo_modules = array(
    array('promo/bundel', 'ProgramPromo/Bundel'),
    array('promo/diskon', 'ProgramPromo/Diskon'),
    array('promo/poin', 'ProgramPromo/MemberPoin'),
    array('promo/voucher', 'ProgramPromo/Voucher'),
);
foreach ($promo_modules as $item) {
    apotik_register_crud_routes($route, $item[0], $item[1]);
}

// Multi Outlet shortcuts
$outlet_modules = array(
    array('outlet/permintaan', 'MultiOutlet/PermintaanMutasi'),
    array('outlet/mutasi', 'MultiOutlet/MutasiAntarOutlet'),
    array('outlet/produk-mitra', 'MultiOutlet/ProdukMitra'),
    array('outlet/outlet-mitra', 'MultiOutlet/OutletMitra'),
);
foreach ($outlet_modules as $item) {
    apotik_register_crud_routes($route, $item[0], $item[1]);
}

// Laporan routes
$laporan_modules = array(
    'Laporan/Penjualan' => 'Laporan/Penjualan',
    'Laporan/Pembelian' => 'Laporan/Pembelian',
    'Laporan/Persediaan' => 'Laporan/Persediaan',
    'Laporan/Keuangan' => 'Laporan/Keuangan',
);
foreach ($laporan_modules as $url => $ctrl) {
    apotik_register_report_routes($route, $url, $ctrl);
}

// Konsinyasi CRUD routes
$konsinyasi_modules = array(
    'Konsinyasi/Masuk' => 'Konsinyasi/Masuk',
    'Konsinyasi/Status' => 'Konsinyasi/Status',
    'Konsinyasi/Retur' => 'Konsinyasi/Retur',
    'Konsinyasi/Stok' => 'Konsinyasi/Stok',
);
foreach ($konsinyasi_modules as $url => $ctrl) {
    apotik_register_crud_routes_with_aliases($route, $url, $ctrl);
}

// Akuntansi extended routes
apotik_register_crud_routes_with_aliases($route, 'Akuntansi/CoA', 'Akuntansi/CoA', true);
apotik_register_crud_routes($route, 'akuntansi/aktiva', 'Akuntansi/AktivaTetap');
apotik_register_crud_routes_with_aliases($route, 'Akuntansi/AktivaTetap', 'Akuntansi/AktivaTetap');
$route['akuntansi/buku-besar/ajax_list'] = 'Akuntansi/BukuBesar/ajax_list';
$route['Akuntansi/BukuBesar/ajax_list'] = 'Akuntansi/BukuBesar/ajax_list';

// Penjualan daftarpenjualan aliases (backward compat)
$route['Penjualan/DaftarPenjualan'] = 'Penjualan/DaftarPenjualan/index';
$route['Penjualan/DaftarPenjualan/ajax_list'] = 'Penjualan/DaftarPenjualan/ajax_list';
$route['Penjualan/DaftarPenjualan/ajax_get/(:num)'] = 'Penjualan/DaftarPenjualan/ajax_get/$1';
$route['Penjualan/DaftarPenjualan/ajax_complete/(:num)'] = 'Penjualan/DaftarPenjualan/ajax_complete/$1';
$route['Penjualan/DaftarPenjualan/ajax_cancel/(:num)'] = 'Penjualan/DaftarPenjualan/ajax_cancel/$1';
$route['Penjualan/DaftarPenjualan/print/(:num)'] = 'Penjualan/DaftarPenjualan/print_receipt/$1';
$route['penjualan/daftar-penjualan/print/(:num)'] = 'Penjualan/DaftarPenjualan/print_receipt/$1';

// Penjualan additional pages
$route['penjualan/pesanan-penjualan'] = 'Penjualan/PesananPenjualan/index';
$route['penjualan/pesanan/ajax_list'] = 'Penjualan/PesananPenjualan/ajax_list';
$route['penjualan/pesanan/ajax_get/(:num)'] = 'Penjualan/PesananPenjualan/ajax_get/$1';
$route['penjualan/pesanan/ajax_save'] = 'Penjualan/PesananPenjualan/ajax_save';
$route['penjualan/pesanan/ajax_confirm/(:num)'] = 'Penjualan/PesananPenjualan/ajax_confirm/$1';

// Pembelian shortcuts ajax
$route['pembelian/pesanan/ajax_list'] = 'Pembelian/PesananPembelian/ajax_list';
$route['pembelian/pesanan/ajax_get/(:num)'] = 'Pembelian/PesananPembelian/ajax_get/$1';
$route['pembelian/pesanan/ajax_save'] = 'Pembelian/PesananPembelian/ajax_save';
$route['pembelian/pesanan/ajax_receive/(:num)'] = 'Pembelian/PesananPembelian/ajax_receive/$1';
$route['pembelian/pesanan/ajax_send/(:num)'] = 'Pembelian/PesananPembelian/ajax_send/$1';
$route['pembelian/faktur/ajax_list'] = 'Pembelian/FakturPembelian/ajax_list';
$route['pembelian/faktur/ajax_get/(:num)'] = 'Pembelian/FakturPembelian/ajax_get/$1';
$route['pembelian/faktur/ajax_save'] = 'Pembelian/FakturPembelian/ajax_save';

// Persediaan shortcut ajax aliases
$route['persediaan/kadaluarsa/ajax_list'] = 'Persediaan/StokKadaluarsa/ajax_list';
$route['persediaan/opname/ajax_list'] = 'Persediaan/StokOpname/ajax_list';
$route['persediaan/opname/ajax_save'] = 'Persediaan/StokOpname/ajax_save';
$route['persediaan/opname/ajax_complete/(:num)'] = 'Persediaan/StokOpname/ajax_complete/$1';
$route['persediaan/opname/view/(:num)'] = 'Persediaan/StokOpname/view/$1';
$route['persediaan/stok_opname'] = 'Persediaan/StokOpname/index';
$route['persediaan/stok_opname/view/(:num)'] = 'Persediaan/StokOpname/view/$1';
$route['persediaan/stok_opname/ajax_list'] = 'Persediaan/StokOpname/ajax_list';
$route['persediaan/stok_opname/ajax_save'] = 'Persediaan/StokOpname/ajax_save';
$route['persediaan/stok_opname/ajax_complete/(:num)'] = 'Persediaan/StokOpname/ajax_complete/$1';
$route['persediaan/stok_opname/ajax_cancel/(:num)'] = 'Persediaan/StokOpname/ajax_cancel/$1';
$route['persediaan/stok_opname/ajax_add_item'] = 'Persediaan/StokOpname/ajax_add_item';
$route['persediaan/stok_opname/ajax_load_all_products'] = 'Persediaan/StokOpname/ajax_load_all_products';
$route['persediaan/stok_opname/ajax_save_details'] = 'Persediaan/StokOpname/ajax_save_details';
$route['persediaan/stok_opname/ajax_delete_item/(:num)'] = 'Persediaan/StokOpname/ajax_delete_item/$1';
$route['persediaan/penyesuaian/ajax_list'] = 'Persediaan/PenyesuaianStok/ajax_list';
$route['persediaan/penyesuaian/ajax_save'] = 'Persediaan/PenyesuaianStok/ajax_save';
$route['persediaan/penyesuaian/ajax_approve/(:num)'] = 'Persediaan/PenyesuaianStok/ajax_approve/$1';
$route['persediaan/perpindahan/ajax_list'] = 'Persediaan/PerpindahanStok/ajax_list';
$route['persediaan/perpindahan/ajax_save'] = 'Persediaan/PerpindahanStok/ajax_save';
$route['persediaan/perpindahan/ajax_complete/(:num)'] = 'Persediaan/PerpindahanStok/ajax_complete/$1';

// Pelayanan routes - dihapus (bukan fitur POS standard)

// KPI & Gaji - map to existing CRUD controllers
$route['kpi/karyawan'] = 'KPI/KPI/index';
$route['kpi/karyawan/create'] = 'KPI/KPI/create';
$route['kpi/karyawan/store'] = 'KPI/KPI/store';
$route['kpi/karyawan/edit/(:num)'] = 'KPI/KPI/edit/$1';
$route['kpi/karyawan/update/(:num)'] = 'KPI/KPI/update/$1';
$route['kpi/karyawan/view/(:num)'] = 'KPI/KPI/view/$1';
$route['kpi/karyawan/ajax_list'] = 'KPI/KPI/ajax_list';
$route['kpi/karyawan/ajax_get/(:num)'] = 'KPI/KPI/ajax_get/$1';
$route['kpi/karyawan/ajax_delete/(:num)'] = 'KPI/KPI/ajax_delete/$1';
$route['Pengaturan/Konfigurasi/update'] = 'Pengaturan/Konfigurasi/update';

// KPI & Gaji full CRUD aliases (kpi index stays on root KPI landing via routes.php)
$route['kpi/view/(:num)'] = 'KPI/KPI/view/$1';
$route['kpi/edit/(:num)'] = 'KPI/KPI/edit/$1';
$route['kpi/create'] = 'KPI/KPI/create';
$route['kpi/store'] = 'KPI/KPI/store';
$route['kpi/update/(:num)'] = 'KPI/KPI/update/$1';
$route['kpi/ajax_list'] = 'KPI/KPI/ajax_list';
$route['kpi/ajax_delete/(:num)'] = 'KPI/KPI/ajax_delete/$1';

$route['gaji/perhitungan'] = 'Gaji/Gaji/index';
$route['gaji/perhitungan/create'] = 'Gaji/Gaji/create';
$route['gaji/perhitungan/store'] = 'Gaji/Gaji/store';
$route['gaji/perhitungan/edit/(:num)'] = 'Gaji/Gaji/edit/$1';
$route['gaji/perhitungan/update/(:num)'] = 'Gaji/Gaji/update/$1';
$route['gaji/perhitungan/view/(:num)'] = 'Gaji/Gaji/view/$1';
$route['gaji/perhitungan/ajax_list'] = 'Gaji/Gaji/ajax_list';
$route['gaji/perhitungan/ajax_get/(:num)'] = 'Gaji/Gaji/ajax_get/$1';
$route['gaji/perhitungan/ajax_save'] = 'Gaji/Gaji/ajax_save';
$route['gaji/perhitungan/ajax_delete/(:num)'] = 'Gaji/Gaji/ajax_delete/$1';

// Pengaturan - fix broken routes
$route['pengaturan/profil'] = 'Pengaturan/ProfilApotek/index';
$route['pengaturan/profil/update'] = 'Pengaturan/ProfilApotek/update';
$route['pengaturan/konfigurasi'] = 'Pengaturan/Konfigurasi/index';
$route['pengaturan/konfigurasi/update'] = 'Pengaturan/Konfigurasi/update';
$route['pengaturan/metode-pembayaran'] = 'Pengaturan/MetodePembayaran/index';
apotik_register_crud_routes($route, 'pengaturan/metode-pembayaran', 'Pengaturan/MetodePembayaran');
$route['pengaturan/metodepembayaran'] = 'Pengaturan/MetodePembayaran/index';
apotik_register_crud_routes($route, 'pengaturan/metodepembayaran', 'Pengaturan/MetodePembayaran');

// Pengaturan - reset data (khusus admin)
$route['pengaturan/reset-data'] = 'Pengaturan/ResetData/index';
$route['pengaturan/reset-data/execute'] = 'Pengaturan/ResetData/execute';

// Analisis ajax
$analisis_modules = array(
    'Analisis/Pareto' => 'Analisis/Pareto',
    'Analisis/Pembelian' => 'Analisis/Pembelian',
    'Analisis/Harga' => 'Analisis/Harga',
);
foreach ($analisis_modules as $url => $ctrl) {
    $route[$url . '/ajax_list'] = $ctrl . '/ajax_list';
    $lower = strtolower($url);
    if ($lower !== $url) {
        $route[$lower . '/ajax_list'] = $ctrl . '/ajax_list';
    }
}

// Kontak lowercase aliases (PascalCase CRUD ada di routes.php)
// Dokter dihapus - bukan fitur POS standard
$kontak_controllers = array(
    'Kontak/Pelanggan',
    'Kontak/Sales',
    'Kontak/Supplier',
);
foreach ($kontak_controllers as $ctrl) {
    apotik_register_crud_routes($route, strtolower($ctrl), $ctrl);
}

// Persediaan Produk & Defecta lowercase aliases
$route['persediaan/produk'] = 'Persediaan/Produk/index';
$route['persediaan/produk/ajax_list'] = 'Persediaan/Produk/ajax_list';
$route['persediaan/defecta'] = 'Persediaan/Defecta/index';
$route['persediaan/defecta/ajax_list'] = 'Persediaan/Defecta/ajax_list';

// Manajemen lowercase aliases untuk DataTables
// Jadwal/Presensi/Shift dihapus - bukan fitur POS standard
$manajemen_controllers = array(
    'Manajemen/Log',
);
foreach ($manajemen_controllers as $ctrl) {
    $route[strtolower($ctrl) . '/ajax_list'] = $ctrl . '/ajax_list';
    $route[strtolower($ctrl)] = $ctrl . '/index';
}

// Placeholder pages (unimplemented modules)
$placeholder_pages = array(
    'pelayanan/rekam-medis' => 'pelayanan',
    'pelayanan/pemeriksaan-awal' => 'pelayanan',
    'pelayanan/pemeriksaan-dokter' => 'pelayanan',
    'pelayanan/penebusan' => 'pelayanan',
    'pelayanan/racikan' => 'pelayanan',
    'pelayanan/lab' => 'pelayanan',
    'pembelian/rencana' => 'pembelian',
    'pembelian/retur' => 'pembelian',
    'pembelian/faktur-ai' => 'pembelian',
    'kpi/parameter' => 'kpi',
    'kpi/pencapaian' => 'kpi',
    'kpi/laporan' => 'kpi',
    'gaji/pokok' => 'gaji',
    'gaji/tunjangan' => 'gaji',
    'gaji/potongan' => 'gaji',
    'gaji/pembayaran' => 'gaji',
    'pengaturan/integrasi' => 'pengaturan',
    'pengaturan/online' => 'pengaturan',
    'pengaturan/website' => 'pengaturan',
    'pengaturan/katalog' => 'pengaturan',
    'pengaturan/whatsapp' => 'pengaturan',
);
foreach ($placeholder_pages as $url => $perm) {
    $route[$url] = 'Placeholder/page/' . str_replace('/', '_', $url);
}

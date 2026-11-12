<?php
/**
 * Comprehensive Report Generator
 * Combines all verification reports into one comprehensive document
 */

// Timezone GMT+8 (WITA)
date_default_timezone_set('Asia/Makassar');

define('BASEPATH', __DIR__ . '/');
define('APPPATH', __DIR__ . '/application/');

$report = "# Laporan Komprehensif Verifikasi Aplikasi Apotek\n\n";
$report .= "**Tanggal:** " . date('Y-m-d H:i:s') . "\n";
$report .= "**Versi:** CodeIgniter 3\n\n";

$report .= "---\n\n";

// 1. CRUD Verification Report
$report .= "## 1. Verifikasi CRUD Operations\n\n";

if (file_exists(__DIR__ . '/CRUD_VERIFICATION_REPORT.md')) {
    $crudReport = file_get_contents(__DIR__ . '/CRUD_VERIFICATION_REPORT.md');
    // Extract summary from CRUD report
    if (preg_match('/## Ringkasan\n\n(.*?)\n\n## Detail/', $crudReport, $matches)) {
        $report .= $matches[1] . "\n\n";
    }
    $report .= "**Detail lengkap:** Lihat `CRUD_VERIFICATION_REPORT.md`\n\n";
} else {
    $report .= "⚠️ Laporan CRUD belum di-generate. Jalankan `php verify_crud.php` terlebih dahulu.\n\n";
}

$report .= "---\n\n";

// 2. AJAX Verification Report
$report .= "## 2. Verifikasi AJAX/JavaScript\n\n";

if (file_exists(__DIR__ . '/AJAX_VERIFICATION_REPORT.md')) {
    $ajaxReport = file_get_contents(__DIR__ . '/AJAX_VERIFICATION_REPORT.md');
    // Extract summary from AJAX report
    if (preg_match('/## Ringkasan\n\n(.*?)\n\n## Detail/', $ajaxReport, $matches)) {
        $report .= $matches[1] . "\n\n";
    }
    $report .= "**Detail lengkap:** Lihat `AJAX_VERIFICATION_REPORT.md`\n\n";
} else {
    $report .= "⚠️ Laporan AJAX belum di-generate. Jalankan `php verify_ajax.php` terlebih dahulu.\n\n";
}

$report .= "---\n\n";

// 3. Menu Testing Report
$report .= "## 3. Testing Menu URLs\n\n";

if (file_exists(__DIR__ . '/MENU_TESTING_REPORT.md')) {
    $menuReport = file_get_contents(__DIR__ . '/MENU_TESTING_REPORT.md');
    // Extract summary from Menu report
    if (preg_match('/## Ringkasan\n\n(.*?)\n\n## Detail/', $menuReport, $matches)) {
        $report .= $matches[1] . "\n\n";
    }
    $report .= "**Detail lengkap:** Lihat `MENU_TESTING_REPORT.md`\n\n";
} else {
    $report .= "⚠️ Laporan Menu Testing belum di-generate. Jalankan `php test_menu_urls.php` atau gunakan interface web `test_menu_interface.php`.\n\n";
}

$report .= "---\n\n";

// 4. Summary and Recommendations
$report .= "## 4. Ringkasan dan Rekomendasi\n\n";

// Read routes file to check URL consistency
$routesFile = APPPATH . 'config/routes.php';
$routesContent = file_exists($routesFile) ? file_get_contents($routesFile) : '';

// Count routes
$routeCount = substr_count($routesContent, '$route[');

$report .= "### Statistik Aplikasi\n\n";
$report .= "- **Total Routes:** $routeCount\n";
$report .= "- **Total Menu (dari sidebar):** 91\n";
$report .= "- **Base URL:** " . (file_exists(APPPATH . 'config/config.php') ? 
    (preg_match("/base_url['\"]\s*=\s*['\"]([^'\"]+)['\"]/", file_get_contents(APPPATH . 'config/config.php'), $m) ? $m[1] : 'N/A') : 'N/A') . "\n\n";

$report .= "### Rekomendasi\n\n";

$report .= "1. **CRUD Operations:**\n";
$report .= "   - Pastikan semua controller yang memerlukan CRUD memiliki method lengkap (ajax_list, ajax_get, ajax_save, ajax_delete)\n";
$report .= "   - Tambahkan permission check di setiap method\n";
$report .= "   - Tambahkan validasi form di ajax_save\n\n";

$report .= "2. **AJAX/JavaScript:**\n";
$report .= "   - Pastikan semua view yang menggunakan DataTable memiliki error handling\n";
$report .= "   - Verifikasi semua URL AJAX sesuai dengan routes\n";
$report .= "   - Pastikan baseUrl didefinisikan dengan benar di semua view\n\n";

$report .= "3. **Menu URLs:**\n";
$report .= "   - Perbaiki URL mismatch antara sidebar dan routes\n";
$report .= "   - Buat controller untuk menu yang belum ada (61 menu)\n";
$report .= "   - Pastikan semua menu memiliki route yang valid\n\n";

$report .= "4. **Testing:**\n";
$report .= "   - Gunakan `test_menu_interface.php` untuk testing interaktif\n";
$report .= "   - Jalankan `test_menu_urls.php` untuk batch testing\n";
$report .= "   - Lakukan testing secara berkala setelah perubahan\n\n";

$report .= "---\n\n";

$report .= "## 5. File Laporan\n\n";
$report .= "- `CRUD_VERIFICATION_REPORT.md` - Laporan verifikasi CRUD\n";
$report .= "- `AJAX_VERIFICATION_REPORT.md` - Laporan verifikasi AJAX/JavaScript\n";
$report .= "- `MENU_TESTING_REPORT.md` - Laporan testing menu URLs\n";
$report .= "- `menu_test_results.json` - Hasil testing dalam format JSON\n";
$report .= "- `COMPREHENSIVE_REPORT.md` - Laporan komprehensif ini\n\n";

$report .= "---\n\n";

$report .= "## 6. Cara Menggunakan Tools\n\n";

$report .= "### Verifikasi CRUD\n";
$report .= "```bash\n";
$report .= "php verify_crud.php\n";
$report .= "```\n\n";

$report .= "### Verifikasi AJAX/JavaScript\n";
$report .= "```bash\n";
$report .= "php verify_ajax.php\n";
$report .= "```\n\n";

$report .= "### Testing Menu URLs (CLI)\n";
$report .= "```bash\n";
$report .= "php test_menu_urls.php\n";
$report .= "```\n\n";

$report .= "### Testing Menu URLs (Web Interface)\n";
$report .= "Buka browser dan akses: `test_menu_interface.php`\n\n";

$report .= "### Generate Laporan Komprehensif\n";
$report .= "```bash\n";
$report .= "php generate_comprehensive_report.php\n";
$report .= "```\n\n";

// Save report
file_put_contents(__DIR__ . '/COMPREHENSIVE_REPORT.md', $report);

echo "Comprehensive Report Generated!\n";
echo "Report saved to: COMPREHENSIVE_REPORT.md\n";
echo "\n";
echo $report;


<?php
/**
 * Verify Master Data modules: files, routes, PHP syntax
 */
define('ENVIRONMENT', 'development');
define('BASEPATH', __DIR__ . '/../system/');
define('APPPATH', __DIR__ . '/../application/');

$modules = array(
    array('route' => 'masterdata/masterproduk', 'controller' => 'MasterData/MasterProduk', 'model' => 'Master_produk_model', 'view' => 'produk'),
    array('route' => 'masterdata/masterkategori', 'controller' => 'MasterData/MasterKategori', 'model' => 'Master_kategori_model', 'view' => 'kategori'),
    array('route' => 'masterdata/mastersatuan', 'controller' => 'MasterData/MasterSatuan', 'model' => 'Master_satuan_model', 'view' => 'satuan'),
    array('route' => 'masterdata/masterrak', 'controller' => 'MasterData/MasterRak', 'model' => 'Master_rak_model', 'view' => 'rak'),
    array('route' => 'masterdata/mastergudang', 'controller' => 'MasterData/MasterGudang', 'model' => 'Master_gudang_model', 'view' => 'gudang'),
    array('route' => 'masterdata/masterpajak', 'controller' => 'MasterData/MasterPajak', 'model' => 'Master_pajak_model', 'view' => 'pajak'),
    array('route' => 'masterdata/mastershift', 'controller' => 'MasterData/MasterShift', 'model' => 'Master_shift_model', 'view' => 'shift'),
    array('route' => 'masterdata/mastermembership', 'controller' => 'MasterData/MasterMembership', 'model' => 'Master_membership_model', 'view' => 'membership'),
    array('route' => 'masterdata/masterpelayanan', 'controller' => 'MasterData/MasterJenisPelayanan', 'model' => 'Jenis_pelayanan_model', 'view' => 'jenis_pelayanan'),
    array('route' => 'masterdata/masterantrian', 'controller' => 'MasterData/MasterJenisAntrian', 'model' => 'Jenis_antrian_model', 'view' => 'jenis_antrian'),
    array('route' => 'masterdata/masterproduklab', 'controller' => 'MasterData/MasterProdukLab', 'model' => 'Produk_lab_model', 'view' => 'produk_lab'),
    array('route' => 'masterdata/masteritempemeriksaan', 'controller' => 'MasterData/MasterItemPemeriksaan', 'model' => 'Item_pemeriksaan_model', 'view' => 'item_pemeriksaan'),
);

$view_pages = array('index', 'create', 'edit', 'view');
$route = array();
require APPPATH . 'config/routes.php';

function resolveUri($uri, $routes) {
    $uri = trim($uri, '/');
    if (isset($routes[$uri])) {
        return $routes[$uri];
    }
    foreach ($routes as $pattern => $target) {
        $regex = '#^' . str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), preg_quote($pattern, '#')) . '$#';
        if (preg_match($regex, $uri)) {
            return $target;
        }
    }
    return $uri . '/index';
}

function routeTargetToControllerFile($target) {
    $parts = explode('/', trim($target, '/'));
    if (count($parts) < 2) {
        return NULL;
    }
    $method = array_pop($parts);
    $controller = array_pop($parts);
    $directory = count($parts) ? implode('/', $parts) . '/' : '';
    return APPPATH . 'controllers/' . $directory . $controller . '.php';
}

function verifyRouteTargetFile($label, $uri, $routes, &$ok, &$fail, &$errors) {
    $resolved = resolveUri($uri, $routes);
    $controller_file = routeTargetToControllerFile($resolved);
    if ($controller_file && file_exists($controller_file)) {
        echo "  [OK] Route target file: {$uri} => {$resolved}\n";
        $ok++;
        return true;
    }
    echo "  [FAIL] Route target missing controller: {$uri} => {$resolved}\n";
    $fail++;
    $errors[] = "Route target missing controller: {$uri} => {$resolved}";
    return false;
}

$ok = 0;
$fail = 0;
$errors = array();

echo "Master Data Verification\n" . str_repeat('=', 70) . "\n";

foreach ($modules as $mod) {
    $label = $mod['view'];
    echo "\n[{$label}]\n";

    $controller_file = APPPATH . 'controllers/' . $mod['controller'] . '.php';
    if (file_exists($controller_file)) {
        echo "  [OK] Controller: {$mod['controller']}\n";
        $ok++;
    } else {
        echo "  [FAIL] Controller missing: {$mod['controller']}\n";
        $fail++;
        $errors[] = "Controller missing: {$mod['controller']}";
    }

    $model_file = APPPATH . 'models/' . $mod['model'] . '.php';
    if (file_exists($model_file)) {
        echo "  [OK] Model: {$mod['model']}\n";
        $ok++;
    } else {
        echo "  [FAIL] Model missing: {$mod['model']}\n";
        $fail++;
        $errors[] = "Model missing: {$mod['model']}";
    }

    foreach ($view_pages as $page) {
        $view_file = APPPATH . 'views/master_data/' . $mod['view'] . '/' . $page . '.php';
        if (file_exists($view_file)) {
            echo "  [OK] View: master_data/{$mod['view']}/{$page}.php\n";
            $ok++;
        } else {
            echo "  [FAIL] View missing: master_data/{$mod['view']}/{$page}.php\n";
            $fail++;
            $errors[] = "View missing: {$mod['view']}/{$page}.php";
        }
    }

    $resolved = resolveUri($mod['route'], $route);
    $expected_target = $mod['controller'] . '/index';
    if ($resolved === $expected_target) {
        echo "  [OK] Route: {$mod['route']} => {$resolved}\n";
        $ok++;
    } else {
        echo "  [FAIL] Route: {$mod['route']} => {$resolved} (expected {$expected_target})\n";
        $fail++;
        $errors[] = "Route mismatch: {$mod['route']}";
    }

    verifyRouteTargetFile($label, $mod['route'] . '/create', $route, $ok, $fail, $errors);
    verifyRouteTargetFile($label, $mod['route'] . '/ajax_list', $route, $ok, $fail, $errors);
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "Syntax Check (new/changed PHP files)\n" . str_repeat('-', 70) . "\n";

$php_files = array_merge(
    glob(APPPATH . 'controllers/MasterData/MasterJenis*.php'),
    glob(APPPATH . 'controllers/MasterData/MasterProdukLab.php'),
    glob(APPPATH . 'controllers/MasterData/MasterItemPemeriksaan.php'),
    glob(APPPATH . 'models/Jenis_*.php'),
    glob(APPPATH . 'models/Produk_lab_model.php'),
    glob(APPPATH . 'models/Item_pemeriksaan_model.php'),
    array(APPPATH . 'helpers/dropdown_helper.php')
);

foreach ($php_files as $file) {
    $output = array();
    $code = 0;
    exec('php -l ' . escapeshellarg($file) . ' 2>&1', $output, $code);
    if ($code === 0) {
        echo "  [OK] " . basename($file) . "\n";
        $ok++;
    } else {
        echo "  [FAIL] " . basename($file) . ": " . implode(' ', $output) . "\n";
        $fail++;
        $errors[] = "Syntax error: " . basename($file);
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "Relation helpers check\n" . str_repeat('-', 70) . "\n";

$relation_checks = array(
    array('Master_kategori_model', 'is_in_use'),
    array('Master_gudang_model', 'is_in_use'),
    array('Master_rak_model', 'get_by_id_with_relations'),
    array('Master_produk_model', 'datatable'),
);

foreach ($relation_checks as $check) {
    $file = APPPATH . 'models/' . $check[0] . '.php';
    $content = file_get_contents($file);
    if (strpos($content, 'function ' . $check[1]) !== false) {
        echo "  [OK] {$check[0]}::{$check[1]}()\n";
        $ok++;
    } else {
        echo "  [FAIL] {$check[0]}::{$check[1]}() missing\n";
        $fail++;
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "Summary: OK={$ok} | FAIL={$fail}\n";
if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $e) {
        echo "  - {$e}\n";
    }
}
exit($fail > 0 ? 1 : 0);

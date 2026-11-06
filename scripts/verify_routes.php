<?php
/**
 * Verify sidebar menu routes resolve to controllers
 */
define('ENVIRONMENT', 'development');
define('BASEPATH', __DIR__ . '/../system/');
define('APPPATH', __DIR__ . '/../application/');

$route = [];
require APPPATH . 'config/routes.php';

$sidebar = file_get_contents(APPPATH . 'views/layouts/sidebar.php');
preg_match_all("/base_url\(['\"]([^'\"]+)['\"]\)/", $sidebar, $matches);
$urls = array_unique($matches[1]);
$urls[] = 'dashboard';
sort($urls);

function findControllerFile($target) {
    $segments = explode('/', $target);
    $method = array_pop($segments);
    $path = APPPATH . 'controllers/';

    foreach ($segments as $segment) {
        $ucFolder = $path . ucfirst(strtolower($segment));
        if (is_dir($ucFolder)) {
            $path = $ucFolder . '/';
            continue;
        }
        $candidates = [
            $path . ucfirst($segment) . '.php',
            $path . $segment . '.php',
            $path . ucfirst(strtolower($segment)) . '.php',
        ];
        foreach ($candidates as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }
        return null;
    }

    $last = end($segments);
    if ($last) {
        $candidates = [
            $path . $last . '.php',
            $path . ucfirst($last) . '.php',
        ];
        foreach ($candidates as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }
    }
    return null;
}

function resolveUri($uri, $routes) {
    $uri = trim($uri, '/');
    if (isset($routes[$uri])) {
        return $routes[$uri];
    }
    $best = null;
    foreach ($routes as $pattern => $target) {
        $regex = '#^' . str_replace([':any', ':num'], ['[^/]+', '[0-9]+'], preg_quote($pattern, '#')) . '$#';
        if (preg_match($regex, $uri)) {
            $best = $target;
        }
    }
    if ($best) {
        return $best;
    }
    return $uri . '/index';
}

$ok = 0;
$fail = 0;
echo "Sidebar Route Verification\n" . str_repeat('=', 70) . "\n";

foreach ($urls as $url) {
    $resolved = resolveUri($url, $route);
    $controllerPath = findControllerFile($resolved);
    $isPlaceholder = strpos($resolved, 'placeholder/') === 0;
    $pass = $controllerPath !== null || $isPlaceholder;
    if ($pass) {
        $ok++;
        $status = 'OK';
    } else {
        $fail++;
        $status = 'FAIL';
    }
    echo sprintf("[%s] %-38s => %-35s %s\n", $status, $url, $resolved, $controllerPath ? basename($controllerPath) : ($isPlaceholder ? 'Placeholder' : '-'));
}

echo str_repeat('=', 70) . "\n";
echo "Total: " . count($urls) . " | OK: $ok | FAIL: $fail\n";
exit($fail > 0 ? 1 : 0);

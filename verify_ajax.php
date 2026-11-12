<?php
/**
 * AJAX/JavaScript Verification Script
 * Verifies AJAX endpoints and JavaScript in views
 */

// Timezone GMT+8 (WITA)
date_default_timezone_set('Asia/Makassar');

// No need to load CodeIgniter for file scanning
define('APPPATH', __DIR__ . '/application/');

class AJAXVerifier {
    
    private $views = [];
    private $results = [];
    
    public function __construct() {
        $this->scanViews();
    }
    
    private function scanViews() {
        $viewDir = __DIR__ . '/application/views/';
        $this->scanDirectory($viewDir, '');
    }
    
    private function scanDirectory($dir, $namespace) {
        if (!is_dir($dir)) return;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || $file == 'index.html' || $file == 'layouts') continue;
            
            $path = $dir . $file;
            if (is_dir($path)) {
                $this->scanDirectory($path . '/', $namespace . $file . '/');
            } elseif (preg_match('/\.php$/', $file)) {
                $this->views[] = [
                    'path' => $path,
                    'namespace' => $namespace,
                    'file' => $file
                ];
            }
        }
    }
    
    public function verifyAll() {
        foreach ($this->views as $view) {
            $this->verifyView($view);
        }
        return $this->results;
    }
    
    private function verifyView($view) {
        $file = file_get_contents($view['path']);
        
        $result = [
            'view' => $view['namespace'] . $view['file'],
            'path' => $view['path'],
            'has_datatable' => false,
            'has_form_handler' => false,
            'has_ajax_calls' => false,
            'ajax_urls' => [],
            'has_jquery' => false,
            'has_error_handling' => false,
            'issues' => []
        ];
        
        // Check for DataTable initialization
        $result['has_datatable'] = preg_match('/initDataTable\s*\(/', $file) || 
                                   preg_match('/\.DataTable\s*\(/', $file) ||
                                   preg_match('/\$\([\'"]\#\w+[\'"]\)\.DataTable/', $file);
        
        // Check for form handler
        $result['has_form_handler'] = preg_match('/handleFormSubmit\s*\(/', $file) ||
                                      preg_match('/\.ajax\s*\(/', $file) ||
                                      preg_match('/\.post\s*\(/', $file) ||
                                      preg_match('/\.get\s*\(/', $file);
        
        // Extract AJAX URLs
        preg_match_all('/(?:url|action)\s*[:=]\s*[\'"]([^\'"]+ajax[^\'"]*)[\'"]/', $file, $urlMatches);
        preg_match_all('/baseUrl\s*\+\s*[\'"]([^\'"]+ajax[^\'"]*)[\'"]/', $file, $baseUrlMatches);
        preg_match_all('/[\'"]([^\'"]+ajax[^\'"]*)[\'"]/', $file, $directMatches);
        
        $ajaxUrls = array_merge(
            isset($urlMatches[1]) ? $urlMatches[1] : [],
            isset($baseUrlMatches[1]) ? $baseUrlMatches[1] : [],
            isset($directMatches[1]) ? $directMatches[1] : []
        );
        
        $result['ajax_urls'] = array_unique($ajaxUrls);
        $result['has_ajax_calls'] = !empty($result['ajax_urls']);
        
        // Check for jQuery
        $result['has_jquery'] = preg_match('/jQuery|\\$\(/', $file);
        
        // Check for error handling
        $result['has_error_handling'] = preg_match('/\.error\s*\(/', $file) ||
                                       preg_match('/catch\s*\(/', $file) ||
                                       preg_match('/\.fail\s*\(/', $file);
        
        // Check for common issues
        if ($result['has_ajax_calls'] && !$result['has_error_handling']) {
            $result['issues'][] = 'AJAX calls tanpa error handling';
        }
        
        if ($result['has_datatable'] && !$result['has_jquery']) {
            $result['issues'][] = 'DataTable digunakan tapi jQuery tidak terdeteksi';
        }
        
        // Check for baseUrl variable
        if ($result['has_ajax_calls'] && !preg_match('/baseUrl|base_url/', $file)) {
            $result['issues'][] = 'AJAX calls mungkin tidak menggunakan baseUrl';
        }
        
        $this->results[] = $result;
    }
    
    public function generateReport() {
        $report = "# Laporan Verifikasi AJAX/JavaScript\n\n";
        $report .= "**Tanggal:** " . date('Y-m-d H:i:s') . "\n\n";
        
        $withDatatable = 0;
        $withFormHandler = 0;
        $withAjax = 0;
        $withErrorHandling = 0;
        $withIssues = 0;
        
        foreach ($this->results as $result) {
            if ($result['has_datatable']) $withDatatable++;
            if ($result['has_form_handler']) $withFormHandler++;
            if ($result['has_ajax_calls']) $withAjax++;
            if ($result['has_error_handling']) $withErrorHandling++;
            if (!empty($result['issues'])) $withIssues++;
        }
        
        $report .= "## Ringkasan\n\n";
        $report .= "- **Total View:** " . count($this->results) . "\n";
        $report .= "- **Menggunakan DataTable:** $withDatatable\n";
        $report .= "- **Menggunakan Form Handler:** $withFormHandler\n";
        $report .= "- **Menggunakan AJAX:** $withAjax\n";
        $report .= "- **Memiliki Error Handling:** $withErrorHandling\n";
        $report .= "- **Memiliki Issues:** $withIssues\n\n";
        
        $report .= "## Detail View\n\n";
        
        foreach ($this->results as $result) {
            if (empty($result['ajax_urls']) && !$result['has_datatable'] && !$result['has_form_handler']) {
                continue; // Skip views without AJAX/JS
            }
            
            $report .= "### {$result['view']}\n\n";
            $report .= "- **DataTable:** " . ($result['has_datatable'] ? '✅' : '❌') . "\n";
            $report .= "- **Form Handler:** " . ($result['has_form_handler'] ? '✅' : '❌') . "\n";
            $report .= "- **AJAX Calls:** " . ($result['has_ajax_calls'] ? '✅' : '❌') . "\n";
            $report .= "- **jQuery:** " . ($result['has_jquery'] ? '✅' : '❌') . "\n";
            $report .= "- **Error Handling:** " . ($result['has_error_handling'] ? '✅' : '❌') . "\n";
            
            if (!empty($result['ajax_urls'])) {
                $report .= "- **AJAX URLs:**\n";
                foreach ($result['ajax_urls'] as $url) {
                    $report .= "  - `$url`\n";
                }
            }
            
            if (!empty($result['issues'])) {
                $report .= "- **⚠️ Issues:**\n";
                foreach ($result['issues'] as $issue) {
                    $report .= "  - $issue\n";
                }
            }
            
            $report .= "\n";
        }
        
        return $report;
    }
}

// Run verification
$verifier = new AJAXVerifier();
$results = $verifier->verifyAll();
$report = $verifier->generateReport();

// Save report
file_put_contents(__DIR__ . '/AJAX_VERIFICATION_REPORT.md', $report);

echo "AJAX/JavaScript Verification Complete!\n";
echo "Report saved to: AJAX_VERIFICATION_REPORT.md\n";
echo "\nSummary:\n";
echo $report;


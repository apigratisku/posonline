<?php
/**
 * CRUD Verification Script
 * Verifies all CRUD operations on existing controllers
 */

define('BASEPATH', __DIR__ . '/');
define('APPPATH', __DIR__ . '/application/');

class CRUDVerifier {
    
    private $controllers = [];
    private $results = [];
    
    public function __construct() {
        $this->scanControllers();
    }
    
    private function scanControllers() {
        $controllerDir = APPPATH . 'controllers/';
        $this->scanDirectory($controllerDir, '');
    }
    
    private function scanDirectory($dir, $namespace) {
        if (!is_dir($dir)) return;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || $file == 'index.html') continue;
            
            $path = $dir . $file;
            if (is_dir($path)) {
                $this->scanDirectory($path . '/', $namespace . $file . '/');
            } elseif (preg_match('/\.php$/', $file)) {
                $className = str_replace('.php', '', $file);
                $fullPath = $namespace . $className;
                $this->controllers[] = [
                    'class' => $className,
                    'namespace' => $namespace,
                    'path' => $path,
                    'full_path' => $fullPath
                ];
            }
        }
    }
    
    public function verifyAll() {
        foreach ($this->controllers as $controller) {
            $this->verifyController($controller);
        }
        return $this->results;
    }
    
    private function verifyController($controller) {
        $file = file_get_contents($controller['path']);
        $className = $controller['class'];
        
        $result = [
            'controller' => $controller['full_path'],
            'class' => $className,
            'methods' => [],
            'has_index' => false,
            'has_ajax_list' => false,
            'has_ajax_get' => false,
            'has_ajax_save' => false,
            'has_ajax_delete' => false,
            'has_permission_check' => false,
            'has_validation' => false,
            'status' => 'unknown'
        ];
        
        // Check for index method
        $result['has_index'] = preg_match('/public\s+function\s+index\s*\(/', $file);
        
        // Check for CRUD methods
        $result['has_ajax_list'] = preg_match('/public\s+function\s+ajax_list\s*\(/', $file);
        $result['has_ajax_get'] = preg_match('/public\s+function\s+ajax_get\s*\([^)]*\)/', $file);
        $result['has_ajax_save'] = preg_match('/public\s+function\s+ajax_save\s*\(/', $file);
        $result['has_ajax_delete'] = preg_match('/public\s+function\s+ajax_delete\s*\([^)]*\)/', $file);
        
        // Check for permission checks
        $result['has_permission_check'] = preg_match('/check_permission\s*\(/', $file) || 
                                         preg_match('/\$this->auth->has_permission\s*\(/', $file);
        
        // Check for validation
        $result['has_validation'] = preg_match('/form_validation->set_rules\s*\(/', $file);
        
        // Check for ajax_response usage
        $result['uses_ajax_response'] = preg_match('/ajax_response\s*\(/', $file);
        
        // Determine status
        $crudCount = 0;
        if ($result['has_ajax_list']) $crudCount++;
        if ($result['has_ajax_get']) $crudCount++;
        if ($result['has_ajax_save']) $crudCount++;
        if ($result['has_ajax_delete']) $crudCount++;
        
        if ($crudCount == 4) {
            $result['status'] = 'complete';
        } elseif ($crudCount >= 2) {
            $result['status'] = 'partial';
        } elseif ($result['has_index']) {
            $result['status'] = 'basic';
        } else {
            $result['status'] = 'empty';
        }
        
        // Extract all public methods
        preg_match_all('/public\s+function\s+(\w+)\s*\([^)]*\)/', $file, $matches);
        if (isset($matches[1])) {
            $result['methods'] = $matches[1];
        }
        
        $this->results[] = $result;
    }
    
    public function generateReport() {
        $report = "# Laporan Verifikasi CRUD\n\n";
        $report .= "**Tanggal:** " . date('Y-m-d H:i:s') . "\n\n";
        
        $complete = 0;
        $partial = 0;
        $basic = 0;
        $empty = 0;
        
        foreach ($this->results as $result) {
            if ($result['status'] == 'complete') $complete++;
            elseif ($result['status'] == 'partial') $partial++;
            elseif ($result['status'] == 'basic') $basic++;
            else $empty++;
        }
        
        $report .= "## Ringkasan\n\n";
        $report .= "- **Total Controller:** " . count($this->results) . "\n";
        $report .= "- **CRUD Lengkap:** $complete\n";
        $report .= "- **CRUD Sebagian:** $partial\n";
        $report .= "- **Basic (Index saja):** $basic\n";
        $report .= "- **Kosong:** $empty\n\n";
        
        $report .= "## Detail Controller\n\n";
        
        foreach ($this->results as $result) {
            $statusIcon = $result['status'] == 'complete' ? '✅' : 
                         ($result['status'] == 'partial' ? '⚠️' : 
                         ($result['status'] == 'basic' ? 'ℹ️' : '❌'));
            
            $report .= "### {$statusIcon} {$result['controller']}\n\n";
            $report .= "- **Class:** `{$result['class']}`\n";
            $report .= "- **Status:** {$result['status']}\n";
            $report .= "- **Index:** " . ($result['has_index'] ? '✅' : '❌') . "\n";
            $report .= "- **ajax_list:** " . ($result['has_ajax_list'] ? '✅' : '❌') . "\n";
            $report .= "- **ajax_get:** " . ($result['has_ajax_get'] ? '✅' : '❌') . "\n";
            $report .= "- **ajax_save:** " . ($result['has_ajax_save'] ? '✅' : '❌') . "\n";
            $report .= "- **ajax_delete:** " . ($result['has_ajax_delete'] ? '✅' : '❌') . "\n";
            $report .= "- **Permission Check:** " . ($result['has_permission_check'] ? '✅' : '❌') . "\n";
            $report .= "- **Validation:** " . ($result['has_validation'] ? '✅' : '❌') . "\n";
            $report .= "- **Uses ajax_response:** " . (isset($result['uses_ajax_response']) && $result['uses_ajax_response'] ? '✅' : '❌') . "\n";
            
            if (!empty($result['methods'])) {
                $report .= "- **Methods:** " . implode(', ', $result['methods']) . "\n";
            }
            $report .= "\n";
        }
        
        return $report;
    }
}

// Run verification
$verifier = new CRUDVerifier();
$results = $verifier->verifyAll();
$report = $verifier->generateReport();

// Save report
file_put_contents(APPPATH . '../CRUD_VERIFICATION_REPORT.md', $report);

echo "CRUD Verification Complete!\n";
echo "Report saved to: CRUD_VERIFICATION_REPORT.md\n";
echo "\nSummary:\n";
echo $report;


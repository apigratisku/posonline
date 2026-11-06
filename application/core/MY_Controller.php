<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/Datatable_trait.php';

class MY_Controller extends CI_Controller {
    
    use Datatable_trait;
    
    protected $data = array();
    protected $user = null;
    protected $peran = null;
    
    public function __construct() {
        parent::__construct();
        
        // Load helpers
        $this->load->helper(array('url', 'form', 'permission', 'ajax', 'flash'));
        
        // Load libraries
        $this->load->library('auth_lib', NULL, 'auth');
        $this->load->library('form_validation');
        
        // Check authentication (except for auth controller)
        if ($this->router->class != 'auth') {
            if (is_cli()) {
                // Mock user and role for CLI testing
                $this->user = (object) [
                    'id' => 1,
                    'username' => 'admin',
                    'nama_lengkap' => 'Administrator CLI',
                    'email' => 'admin@pos.local',
                    'peran_id' => 1
                ];
                $this->peran = (object) [
                    'id' => 1,
                    'kode' => 'admin',
                    'nama' => 'Administrator'
                ];
                $this->data['user'] = $this->user;
                $this->data['peran'] = $this->peran;
                
                $this->load->model('profil_apotek_model');
                $profil_data = $this->profil_apotek_model->get_singleton();
                $this->data['profil_apotek'] = $profil_data;
                $this->data['profil_toko'] = $profil_data;
            } else {
                if (!$this->auth->is_logged_in()) {
                    if ($this->datatable_is_ajax_request()) {
                        $this->datatable_json($this->datatable_empty_response('Sesi berakhir. Silakan login kembali.'));
                        exit;
                    }
                    redirect('auth/login');
                }
                
                // Get current user
                $this->user = $this->auth->get_user();
                $this->peran = $this->auth->get_peran();
                
                // Set user data for views
                $this->data['user'] = $this->user;
                $this->data['peran'] = $this->peran;

                $this->load->model('profil_apotek_model');
                $profil_data = $this->profil_apotek_model->get_singleton();
                $this->data['profil_apotek'] = $profil_data;
                $this->data['profil_toko'] = $profil_data; // alias generik POS
            }
        }
        
        // Set base URL
        $this->data['base_url'] = base_url();
        $this->data['site_url'] = site_url();
    }
    
    /**
     * Check permission
     */
    protected function check_permission($modul, $aksi = 'view') {
        if (is_cli()) {
            return; // Bypass for CLI testing
        }
        if (!$this->auth->has_permission($modul, $aksi)) {
            $peran = $this->auth->get_peran();
            $peran_kode = $peran ? $peran->kode : 'Unknown';
            
            // Build informative message
            $message = 'Anda tidak memiliki akses untuk melakukan aksi ini.';
            if (ENVIRONMENT === 'development') {
                $message .= '<br><br>';
                $message .= '<strong>Debug Info:</strong><br>';
                $message .= 'Modul: ' . htmlspecialchars($modul) . '<br>';
                $message .= 'Aksi: ' . htmlspecialchars($aksi) . '<br>';
                $message .= 'Role: ' . htmlspecialchars($peran_kode) . '<br>';
                $message .= '<br>Silakan hubungi administrator untuk mendapatkan akses.';
            }
            
            if ($this->datatable_is_ajax_request()) {
                if ($this->router->fetch_method() === 'ajax_list') {
                    log_message('error', 'Permission denied ajax_list: ' . $modul . '/' . $aksi);
                    $this->datatable_json($this->datatable_empty_response(strip_tags($message)));
                } else {
                    $this->ajax_response(false, strip_tags($message));
                }
                exit;
            } else {
                show_error($message, 403, 'Akses Ditolak');
            }
        }
    }
    
    /**
     * DataTables JSON response
     */
    protected function datatable_json($result) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    protected function datatable_json_rows($rows) {
        $count = is_array($rows) ? count($rows) : 0;
        $this->datatable_json(array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => $count,
            'recordsFiltered' => $count,
            'data' => $rows,
        ));
    }
    
    /**
     * AJAX Response
     */
    protected function ajax_response($status = true, $message = '', $data = null, $errors = null) {
        $response = array(
            'status' => $status,
            'message' => $message
        );
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
    
    /**
     * Load view with layout
     */
    protected function load_view($view, $data = array(), $return = false) {
        $this->data = array_merge($this->data, $data);
        
        if ($return) {
            return $this->load->view($view, $this->data, true);
        } else {
            $this->load->view($view, $this->data);
        }
    }
    
    /**
     * Render page with layout
     */
    protected function render($view, $data = array()) {
        $this->data = array_merge($this->data, $data);
        $html = $this->load->view($view, $this->data, true);
        $parts = apotik_defer_scripts($html);
        $this->data['content'] = $parts['content'];
        $this->data['page_scripts'] = $parts['scripts'];
        $this->load->view('layouts/main', $this->data);
    }
    
    /**
     * Set flash message
     */
    protected function set_flash($type, $message) {
        $this->session->set_flashdata('flash_type', $type);
        $this->session->set_flashdata('flash_message', $message);
    }
}


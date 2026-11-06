<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('auth_lib', NULL, 'auth');
        $this->load->helper('form');
        $this->load->model('profil_apotek_model');
    }
    
    /**
     * Login page
     */
    public function login() {
        // If already logged in, redirect based on role
        if ($this->auth->is_logged_in()) {
            $this->_redirect_after_login();
        }
        
        if ($this->input->post()) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            
            // Validate input
            if (empty($username) || empty($password)) {
                if ($this->input->is_ajax_request()) {
                    $this->ajax_response(false, 'Username dan password harus diisi');
                } else {
                    $this->session->set_flashdata('error', 'Username dan password harus diisi');
                    redirect('auth/login');
                }
                return;
            }
            
            if ($this->auth->login($username, $password)) {
                if ($this->input->is_ajax_request()) {
                    // Build redirect URL according to role
                    $redirect_url = $this->_get_redirect_url_after_login();
                    $this->ajax_response(true, 'Login berhasil', array('redirect' => $redirect_url));
                } else {
                    $this->_redirect_after_login();
                }
            } else {
                if ($this->input->is_ajax_request()) {
                    $this->ajax_response(false, 'Username atau password salah');
                } else {
                    $this->session->set_flashdata('error', 'Username atau password salah');
                    redirect('auth/login');
                }
            }
        } else {
            $profil_data = $this->profil_apotek_model->get_singleton();
            $this->load->view('auth/login', array(
                'profil_apotek' => $profil_data,
                'profil_toko' => $profil_data
            ));
        }
    }
    
    /**
     * Logout
     */
    public function logout() {
        $this->auth->logout();
        redirect('auth/login');
    }
    
    /**
     * AJAX Response helper
     */
    private function ajax_response($status = true, $message = '', $data = null) {
        $response = array(
            'status' => $status,
            'message' => $message
        );
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Determine redirect URL after successful login based on role
     */
    private function _get_redirect_url_after_login() {
        // Get role code from session (set in Auth_lib::login)
        $peran_kode = $this->session->userdata('peran_kode');

        // Kasir: langsung ke halaman kasir penjualan
        if ($peran_kode === 'kasir') {
            return site_url('penjualan/kasir');
        }

        // Manager/owner atau role lain: arahkan ke dashboard umum
        return site_url('dashboard');
    }

    /**
     * Redirect helper after successful login (non-AJAX)
     */
    private function _redirect_after_login() {
        $redirect_url = $this->_get_redirect_url_after_login();
        redirect($redirect_url);
    }
}


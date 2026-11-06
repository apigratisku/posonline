<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_lib {
    
    protected $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
        // Session sudah di-autoload di config/autoload.php
        $this->CI->load->helper('url');
    }
    
    /**
     * Login user
     * Supports bcrypt format $2y$10$ and other password hash formats
     */
    public function login($username, $password) {
        // Validate input
        if (empty($username) || empty($password)) {
            return false;
        }
        
        $user = $this->CI->db
            ->select('u.*, p.nama as peran_nama, p.kode as peran_kode')
            ->from('users u')
            ->join('peran p', 'p.id = u.peran_id', 'left')
            ->where('u.username', $username)
            ->where('u.is_active', 1)
            ->get()
            ->row();
        
        if (!$user || empty($user->password)) {
            return false;
        }
        
        // Get password hash and trim whitespace
        // Ensure we get the full hash from database (varchar(255) should be enough for bcrypt)
        $password_hash = trim($user->password);
        
        // Ensure password hash is not empty
        if (empty($password_hash)) {
            return false;
        }
        
        // Verify password using password_verify
        // password_verify() automatically supports bcrypt format including $2y$10$
        // It handles both full 60-character bcrypt hashes and any valid bcrypt hash format
        // The function will return true if password matches the hash, false otherwise
        if (password_verify($password, $password_hash)) {
            // Set session
            $session_data = array(
                'user_id' => $user->id,
                'username' => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'peran_id' => $user->peran_id,
                'peran_nama' => $user->peran_nama,
                'peran_kode' => $user->peran_kode,
                'gudang_id' => isset($user->gudang_id) ? $user->gudang_id : null,
                'logged_in' => true
            );
            
            $this->CI->session->set_userdata($session_data);
            
            // Update last login
            $this->CI->db->where('id', $user->id)
                ->update('users', array('last_login' => date('Y-m-d H:i:s')));
            
            // Log activity
            $this->log_activity('Login', 'User berhasil login');
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Log activity
        $this->log_activity('Logout', 'User berhasil logout');
        
        // Destroy session
        $this->CI->session->sess_destroy();
    }
    
    /**
     * Check if user is logged in
     */
    public function is_logged_in() {
        return $this->CI->session->userdata('logged_in') === true;
    }
    
    /**
     * Get current user
     */
    public function get_user() {
        if (!$this->is_logged_in()) {
            return null;
        }
        
        $user_id = $this->CI->session->userdata('user_id');
        return $this->CI->db->where('id', $user_id)->get('users')->row();
    }
    
    /**
     * Get current user ID
     */
    public function get_user_id() {
        return $this->CI->session->userdata('user_id');
    }
    
    /**
     * Get current peran
     */
    public function get_peran() {
        if (!$this->is_logged_in()) {
            return null;
        }
        
        $peran_id = $this->CI->session->userdata('peran_id');
        if ($peran_id) {
            return $this->CI->db->where('id', $peran_id)->get('peran')->row();
        }
        
        return null;
    }
    
    /**
     * Check permission
     */
    public function has_permission($modul, $aksi = 'view') {
        if (!$this->is_logged_in()) {
            return false;
        }
        
        $peran_id = $this->CI->session->userdata('peran_id');
        $peran_kode = $this->CI->session->userdata('peran_kode');
        
        // If user has no role, deny access
        if (!$peran_id) {
            return false;
        }
        
        // Admin role always has full access (fallback for development)
        if ($peran_kode == 'admin') {
            return true;
        }

        // Kasir role: selalu boleh melakukan penjualan (create) di modul penjualan
        // tanpa memberi akses ke daftar/laporan penjualan atau modul lain.
        if ($peran_kode === 'kasir' && $modul === 'penjualan' && $aksi === 'create') {
            return true;
        }
        
        // Get modul
        $modul_data = $this->CI->db->where('kode', $modul)->get('modul')->row();
        if (!$modul_data) {
            // If modul doesn't exist, allow access for admin (fallback)
            if ($peran_kode == 'admin') {
                return true;
            }
            return false;
        }
        
        // Get permission
        $permission = $this->CI->db
            ->where('peran_id', $peran_id)
            ->where('modul_id', $modul_data->id)
            ->get('peran_hak_akses')
            ->row();
        
        // If permission doesn't exist, allow access for admin (fallback)
        if (!$permission) {
            if ($peran_kode == 'admin') {
                return true;
            }
            return false;
        }
        
        // Check action
        switch ($aksi) {
            case 'view':
                return $permission->can_view == 1;
            case 'create':
                return $permission->can_create == 1;
            case 'update':
                return $permission->can_update == 1;
            case 'delete':
                return $permission->can_delete == 1;
            default:
                return false;
        }
    }
    
    /**
     * Log activity
     */
    public function log_activity($aksi, $deskripsi = '') {
        if (!$this->CI->db->table_exists('log_aktivitas')) {
            return;
        }

        $user_id = $this->get_user_id();
        if (!empty($user_id)) {
            $user_exists = $this->CI->db->where('id', $user_id)->count_all_results('users');
            if (!$user_exists) {
                $user_id = null;
            }
        } else {
            $user_id = null;
        }

        $user_agent = (string) $this->CI->input->user_agent();
        if (strlen($user_agent) > 255) {
            $user_agent = substr($user_agent, 0, 255);
        }

        $data = array(
            'user_id' => $user_id,
            'modul' => $this->CI->router->class,
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'ip_address' => $this->CI->input->ip_address(),
            'user_agent' => $user_agent,
            'created_at' => date('Y-m-d H:i:s')
        );

        @$this->CI->db->insert('log_aktivitas', $data);
    }
}


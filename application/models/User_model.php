<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {
    
    protected $table = 'users';
    protected $primary_key = 'id';
    protected $fillable = array('username', 'email', 'password', 'nama_lengkap', 'telepon', 'alamat', 'peran_id', 'gudang_id', 'foto', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function username_exists($username, $exclude_id = null) {
        $this->db->where('username', $username);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    public function email_exists($email, $exclude_id = null) {
        $this->db->where('email', $email);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    /**
     * Get user with role
     */
    public function get_by_id_with_role($id) {
        $this->db->select('u.*, p.nama as peran_nama, p.kode as peran_kode, mg.nama as gudang_nama');
        $this->db->from($this->table . ' u');
        $this->db->join('peran p', 'p.id = u.peran_id', 'left');
        $this->db->join('master_gudang mg', 'mg.id = u.gudang_id', 'left');
        $this->db->where('u.id', $id);
        return $this->db->get()->row();
    }
    
    /**
     * Insert with password hashing
     * Supports bcrypt format $2y$10$ and automatically hashes plain passwords
     */
    public function insert($data) {
        if (isset($data['password']) && !empty($data['password'])) {
            // If password is already a bcrypt hash (starts with $2y$ or $2a$), use it directly
            // Otherwise, hash it using bcrypt
            $password = trim($data['password']);
            if (!preg_match('/^\$2[ay]\$\d{2}\$/', $password)) {
                // Password is plain text, hash it
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            } else {
                // Password is already hashed, use it as is
                $data['password'] = $password;
            }
        }
        return parent::insert($data);
    }
    
    /**
     * Update with password hashing
     * Supports bcrypt format $2y$10$ and automatically hashes plain passwords
     */
    public function update($id, $data) {
        $user = $this->get_by_id($id);
        if ($user && strtolower($user->username) === 'demo') {
            unset($data['password']);
        }

        if (isset($data['password']) && !empty($data['password'])) {
            // If password is already a bcrypt hash (starts with $2y$ or $2a$), use it directly
            // Otherwise, hash it using bcrypt
            $password = trim($data['password']);
            if (!preg_match('/^\$2[ay]\$\d{2}\$/', $password)) {
                // Password is plain text, hash it
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            } else {
                // Password is already hashed, use it as is
                $data['password'] = $password;
            }
        } else {
            unset($data['password']);
        }
        return parent::update($id, $data);
    }
    
    public function delete($id) {
        $user = $this->get_by_id($id);
        if ($user && in_array(strtolower($user->username), array('demo', 'admin', 'adhit'))) {
            return false;
        }
        return parent::delete($id);
    }

    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'u.created_at';
        }
        $params['build_query'] = function () {
            $this->db->select('u.*, p.nama as peran_nama');
            $this->db->from($this->table . ' u');
            $this->db->join('peran p', 'p.id = u.peran_id', 'left');
        };
        return $this->datatable_execute($params);
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_kategori_model extends MY_Model {
    
    protected $table = 'master_kategori';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'deskripsi', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get all with filters
     */
    public function get_all_filtered($search = '', $is_active = null) {
        $this->db->from($this->table);
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode', $search);
            $this->db->or_like('nama', $search);
            $this->db->or_like('deskripsi', $search);
            $this->db->group_end();
        }
        
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        
        return $this->db->get()->result();
    }
    
    /**
     * Check if kode exists
     */
    public function kode_exists($kode, $exclude_id = null) {
        $this->db->where('kode', $kode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }

    public function is_in_use($id) {
        return $this->db->where('kategori_id', $id)->count_all_results('master_produk') > 0;
    }
}


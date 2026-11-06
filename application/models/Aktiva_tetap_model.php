<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aktiva_tetap_model extends MY_Model {
    
    protected $table = 'aktiva_tetap';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'kategori', 'tanggal_beli', 'harga_beli', 'nilai_residu', 'umur_ekonomis', 'metode_depresiasi', 'is_active', 'deskripsi');
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
            $this->db->or_like('kategori', $search);
            $this->db->or_like('deskripsi', $search);
            $this->db->group_end();
        }
        
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        
        $this->db->order_by('kode', 'ASC');
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
}


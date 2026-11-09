<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_golongan_model extends MY_Model {
    
    protected $table = 'master_golongan';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'deskripsi', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
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
        return $this->db->where('golongan_id', $id)->count_all_results('master_produk') > 0;
    }
}

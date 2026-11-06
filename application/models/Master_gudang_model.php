<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_gudang_model extends MY_Model {
    
    protected $table = 'master_gudang';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'alamat', 'telepon', 'email', 'is_utama', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function kode_exists($kode, $exclude_id = null) {
        $this->db->where('kode', $kode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }

    public function is_in_use($id) {
        $rak_count = $this->db->where('gudang_id', $id)->count_all_results('master_rak');
        $produk_count = $this->db->where('gudang_id', $id)->count_all_results('master_produk');
        return ($rak_count + $produk_count) > 0;
    }
}


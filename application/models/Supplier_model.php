<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends MY_Model {
    
    protected $table = 'supplier';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'alamat', 'telepon', 'email', 'npwp', 'rekening', 'is_active');
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
}


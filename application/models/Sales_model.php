<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends MY_Model {
    
    protected $table = 'sales';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'alamat', 'telepon', 'email', 'komisi_persen', 'is_active');
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


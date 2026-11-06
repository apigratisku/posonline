<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_shift_model extends MY_Model {
    
    protected $table = 'master_shift';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'jam_mulai', 'jam_selesai', 'is_active');
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


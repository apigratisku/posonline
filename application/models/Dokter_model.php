<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dokter_model extends MY_Model {
    
    protected $table = 'dokter';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'spesialisasi', 'no_sip', 'alamat', 'telepon', 'email', 'is_active');
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
    
    public function no_sip_exists($no_sip, $exclude_id = null) {
        if (empty($no_sip)) return false;
        
        $this->db->where('no_sip', $no_sip);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
}


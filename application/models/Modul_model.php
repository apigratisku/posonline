<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modul_model extends MY_Model {
    
    protected $table = 'modul';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'controller', 'icon', 'urutan', 'is_active');
    protected $timestamps = false;
    
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
    
    /**
     * Get active modules ordered by urutan
     */
    public function get_active_modules() {
        $this->db->where('is_active', 1);
        $this->db->order_by('urutan', 'ASC');
        return $this->db->get($this->table)->result();
    }
}


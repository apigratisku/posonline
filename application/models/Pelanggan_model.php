<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan_model extends MY_Model {
    
    protected $table = 'pelanggan';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'alamat', 'telepon', 'email', 'tanggal_lahir', 'jenis_kelamin', 'membership_id', 'poin', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_all_with_relations($where = array()) {
        $this->db->select('p.*, mm.nama as membership_nama');
        $this->db->from($this->table . ' p');
        $this->db->join('master_membership mm', 'mm.id = p.membership_id', 'left');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        return $this->db->get()->result();
    }
    
    public function kode_exists($kode, $exclude_id = null) {
        $this->db->where('kode', $kode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'p.id';
        }
        $params['build_query'] = function () {
            $this->db->select('p.*, mm.nama as membership_nama');
            $this->db->from($this->table . ' p');
            $this->db->join('master_membership mm', 'mm.id = p.membership_id', 'left');
        };
        return $this->datatable_execute($params);
    }
}


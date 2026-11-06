<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_rak_model extends MY_Model {
    
    protected $table = 'master_rak';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'gudang_id', 'deskripsi', 'is_active');
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

    public function get_by_id_with_relations($id) {
        $this->db->select('mr.*, mg.nama as gudang_nama, mg.kode as gudang_kode');
        $this->db->from($this->table . ' mr');
        $this->db->join('master_gudang mg', 'mg.id = mr.gudang_id', 'left');
        $this->db->where('mr.id', $id);
        return $this->db->get()->row();
    }

    public function count_by_gudang($gudang_id) {
        return $this->db->where('gudang_id', $gudang_id)->count_all_results($this->table);
    }

    public function is_in_use($id) {
        return $this->db->where('rak_id', $id)->count_all_results('master_produk') > 0;
    }

    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'mr.created_at';
        }
        $params['build_query'] = function () {
            $this->db->select('mr.*, mg.nama as gudang_nama');
            $this->db->from($this->table . ' mr');
            $this->db->join('master_gudang mg', 'mg.id = mr.gudang_id', 'left');
        };
        if (empty($params['search_fields'])) {
            $params['search_fields'] = array('mr.kode', 'mr.nama', 'mg.nama');
        }
        return $this->datatable_execute($params);
    }
}


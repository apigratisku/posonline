<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konsinyasi_masuk_model extends MY_Model {
    
    protected $table = 'konsinyasi_masuk';
    protected $primary_key = 'id';
    protected $fillable = array('no_konsinyasi', 'supplier_id', 'gudang_id', 'tanggal', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function generate_no_konsinyasi() {
        $date = date('Ymd');
        $row = $this->db->select_max('id')->get($this->table)->row();
        $max_id = $row ? $row->id : 0;
        return 'KONS-' . $date . '-' . str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);
    }
    
    public function get_by_id_with_relations($id) {
        $this->db->select('km.*, s.nama as supplier_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' km');
        $this->db->join('supplier s', 's.id = km.supplier_id');
        $this->db->join('master_gudang mg', 'mg.id = km.gudang_id');
        $this->db->where('km.id', $id);
        return $this->db->get()->row();
    }
}


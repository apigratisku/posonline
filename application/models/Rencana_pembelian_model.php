<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rencana_pembelian_model extends MY_Model {
    
    protected $table = 'rencana_pembelian';
    protected $primary_key = 'id';
    protected $fillable = array('no_rencana', 'supplier_id', 'gudang_id', 'tanggal', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function generate_no_rencana() {
        $prefix = 'RENC-' . date('Ymd') . '-';
        $this->db->select('no_rencana');
        $this->db->from($this->table);
        $this->db->like('no_rencana', $prefix, 'after');
        $this->db->order_by('no_rencana', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_rencana, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    public function get_by_id_with_relations($id) {
        $this->db->select('rp.*, s.nama as supplier_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' rp');
        $this->db->join('supplier s', 's.id = rp.supplier_id', 'left');
        $this->db->join('master_gudang mg', 'mg.id = rp.gudang_id');
        $this->db->where('rp.id', $id);
        return $this->db->get()->row();
    }
    
    public function get_details($rencana_id) {
        $this->db->select('rpd.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from('rencana_pembelian_detail rpd');
        $this->db->join('master_produk mp', 'mp.id = rpd.produk_id');
        $this->db->where('rpd.rencana_id', $rencana_id);
        return $this->db->get()->result();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'rp.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('rp.*, s.nama as supplier_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' rp');
            $this->db->join('supplier s', 's.id = rp.supplier_id', 'left');
            $this->db->join('master_gudang mg', 'mg.id = rp.gudang_id');
        };
        return $this->datatable_execute($params);
    }
}


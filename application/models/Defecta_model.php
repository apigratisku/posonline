<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Defecta_model extends MY_Model {
    
    protected $table = 'defecta';
    protected $primary_key = 'id';
    protected $fillable = array('produk_id', 'gudang_id', 'tanggal', 'qty', 'keterangan', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get all with relations
     */
    public function get_all_with_relations($where = array()) {
        $this->db->select('d.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' d');
        $this->db->join('master_produk mp', 'mp.id = d.produk_id');
        $this->db->join('master_gudang mg', 'mg.id = d.gudang_id');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $this->db->order_by('d.tanggal', 'DESC');
        $this->db->order_by('d.id', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Approve defecta - update stock
     */
    public function approve($id) {
        $defecta = $this->get_by_id($id);
        if (!$defecta || $defecta->status != 'pending') {
            return false;
        }
        
        // Update status
        $this->update($id, array('status' => 'approved'));
        
        // Update kartu stok
        $this->load->model('kartu_stok_model');
        $kartu_data = array(
            'produk_id' => $defecta->produk_id,
            'gudang_id' => $defecta->gudang_id,
            'tanggal' => $defecta->tanggal,
            'referensi' => 'DEFECTA-' . $id,
            'tipe' => 'keluar',
            'qty_masuk' => 0,
            'qty_keluar' => $defecta->qty,
            'keterangan' => 'Defecta: ' . $defecta->keterangan
        );
        $this->kartu_stok_model->insert_kartu_stok($kartu_data);
        
        return true;
    }
    
    /**
     * DataTables
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'd.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('d.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' d');
            $this->db->join('master_produk mp', 'mp.id = d.produk_id');
            $this->db->join('master_gudang mg', 'mg.id = d.gudang_id');
        };
        return $this->datatable_execute($params);
    }
}


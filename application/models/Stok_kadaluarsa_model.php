<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_kadaluarsa_model extends MY_Model {
    
    protected $table = 'stok_kadaluarsa';
    protected $primary_key = 'id';
    protected $fillable = array('produk_id', 'gudang_id', 'batch_no', 'expired_date', 'qty', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get all with relations
     */
    public function get_all_with_relations($where = array()) {
        $this->db->select('sk.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' sk');
        $this->db->join('master_produk mp', 'mp.id = sk.produk_id');
        $this->db->join('master_gudang mg', 'mg.id = sk.gudang_id');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        $this->db->order_by('sk.expired_date', 'ASC');
        $this->db->order_by('sk.id', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Get expired products (expired_date <= today)
     */
    public function get_expired($gudang_id = null) {
        $this->db->select('sk.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' sk');
        $this->db->join('master_produk mp', 'mp.id = sk.produk_id');
        $this->db->join('master_gudang mg', 'mg.id = sk.gudang_id');
        $this->db->where('sk.expired_date <=', date('Y-m-d'));
        $this->db->where('sk.status !=', 'disposed');
        
        if ($gudang_id) {
            $this->db->where('sk.gudang_id', $gudang_id);
        }
        
        return $this->db->get()->result();
    }
    
    /**
     * Get warning products (expired_date <= today + 30 days)
     */
    public function get_warning($days = 30, $gudang_id = null) {
        $warning_date = date('Y-m-d', strtotime('+' . $days . ' days'));
        
        $this->db->select('sk.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' sk');
        $this->db->join('master_produk mp', 'mp.id = sk.produk_id');
        $this->db->join('master_gudang mg', 'mg.id = sk.gudang_id');
        $this->db->where('sk.expired_date <=', $warning_date);
        $this->db->where('sk.expired_date >', date('Y-m-d'));
        $this->db->where('sk.status', 'warning');
        
        if ($gudang_id) {
            $this->db->where('sk.gudang_id', $gudang_id);
        }
        
        return $this->db->get()->result();
    }
    
    /**
     * Mark as disposed
     */
    public function dispose($id) {
        return $this->update($id, array('status' => 'disposed'));
    }
    
    /**
     * DataTables
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'sk.expired_date';
        }
        $params['build_query'] = function () {
            $this->db->select('sk.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' sk');
            $this->db->join('master_produk mp', 'mp.id = sk.produk_id');
            $this->db->join('master_gudang mg', 'mg.id = sk.gudang_id');
        };
        return $this->datatable_execute($params);
    }
}


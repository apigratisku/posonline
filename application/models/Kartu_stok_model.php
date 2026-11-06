<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kartu_stok_model extends MY_Model {
    
    protected $table = 'kartu_stok';
    protected $primary_key = 'id';
    protected $fillable = array('produk_id', 'gudang_id', 'tanggal', 'referensi', 'tipe', 'qty_masuk', 'qty_keluar', 'qty_saldo', 'harga', 'keterangan');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Insert kartu stok and update saldo
     */
    public function insert_kartu_stok($data) {
        // Get last saldo
        $last_saldo = $this->get_last_saldo($data['produk_id'], $data['gudang_id']);
        
        // Calculate new saldo
        $qty_masuk = isset($data['qty_masuk']) ? $data['qty_masuk'] : 0;
        $qty_keluar = isset($data['qty_keluar']) ? $data['qty_keluar'] : 0;
        $data['qty_saldo'] = $last_saldo + $qty_masuk - $qty_keluar;
        
        // Add created_by
        $CI =& get_instance();
        $CI->load->library('auth_lib', NULL, 'auth');
        if ($CI->auth->is_logged_in()) {
            $data['created_by'] = $CI->auth->get_user_id();
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->insert($data);
    }
    
    /**
     * Get last saldo
     */
    public function get_last_saldo($produk_id, $gudang_id) {
        $this->db->select('qty_saldo');
        $this->db->from($this->table);
        $this->db->where('produk_id', $produk_id);
        $this->db->where('gudang_id', $gudang_id);
        $this->db->order_by('tanggal', 'DESC');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $result = $this->db->get()->row();
        
        return $result ? $result->qty_saldo : 0;
    }
    
    /**
     * Get kartu stok with relations
     */
    public function get_all_with_relations($produk_id, $gudang_id = null) {
        $this->db->select('ks.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' ks');
        $this->db->join('master_produk mp', 'mp.id = ks.produk_id');
        $this->db->join('master_gudang mg', 'mg.id = ks.gudang_id');
        $this->db->where('ks.produk_id', $produk_id);
        
        if ($gudang_id) {
            $this->db->where('ks.gudang_id', $gudang_id);
        }
        
        $this->db->order_by('ks.tanggal', 'DESC');
        $this->db->order_by('ks.id', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * DataTables
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'ks.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('ks.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' ks');
            $this->db->join('master_produk mp', 'mp.id = ks.produk_id');
            $this->db->join('master_gudang mg', 'mg.id = ks.gudang_id');
        };
        $params['apply_filters'] = function () use ($params) {
            if (isset($params['produk_id'])) {
            $this->db->where('ks.produk_id', $params['produk_id']);
            }
            if (isset($params['gudang_id'])) {
            $this->db->where('ks.gudang_id', $params['gudang_id']);
            }
        };
        return $this->datatable_execute($params);
    }
}


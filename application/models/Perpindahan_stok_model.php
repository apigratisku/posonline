<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perpindahan_stok_model extends MY_Model {
    
    protected $table = 'perpindahan_stok';
    protected $primary_key = 'id';
    protected $fillable = array('no_mutasi', 'gudang_asal_id', 'gudang_tujuan_id', 'tanggal', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('ps.*, mga.nama as gudang_asal_nama, mgt.nama as gudang_tujuan_nama');
        $this->db->from($this->table . ' ps');
        $this->db->join('master_gudang mga', 'mga.id = ps.gudang_asal_id');
        $this->db->join('master_gudang mgt', 'mgt.id = ps.gudang_tujuan_id');
        $this->db->where('ps.id', $id);
        return $this->db->get()->row();
    }
    
    /**
     * Get details
     */
    public function get_details($mutasi_id) {
        $this->db->select('psd.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from('perpindahan_stok_detail psd');
        $this->db->join('master_produk mp', 'mp.id = psd.produk_id');
        $this->db->where('psd.mutasi_id', $mutasi_id);
        return $this->db->get()->result();
    }
    
    /**
     * Generate no_mutasi
     */
    public function generate_no_mutasi() {
        $prefix = 'MUT-' . date('Ymd') . '-';
        $this->db->select('no_mutasi');
        $this->db->from($this->table);
        $this->db->like('no_mutasi', $prefix, 'after');
        $this->db->order_by('no_mutasi', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_mutasi, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Complete mutasi - update stock
     */
    public function complete($id) {
        $mutasi = $this->get_by_id($id);
        if (!$mutasi || $mutasi->status != 'process') {
            return false;
        }
        
        // Get details
        $details = $this->get_details($id);
        
        // Load models
        $this->load->model('kartu_stok_model');
        
        foreach ($details as $detail) {
            // Decrease stock from source warehouse
            $kartu_keluar = array(
                'produk_id' => $detail->produk_id,
                'gudang_id' => $mutasi->gudang_asal_id,
                'tanggal' => $mutasi->tanggal,
                'referensi' => $mutasi->no_mutasi,
                'tipe' => 'keluar',
                'qty_masuk' => 0,
                'qty_keluar' => $detail->qty,
                'keterangan' => 'Mutasi ke ' . $mutasi->gudang_tujuan_id . ': ' . $mutasi->keterangan
            );
            $this->kartu_stok_model->insert_kartu_stok($kartu_keluar);
            
            // Increase stock to destination warehouse
            $kartu_masuk = array(
                'produk_id' => $detail->produk_id,
                'gudang_id' => $mutasi->gudang_tujuan_id,
                'tanggal' => $mutasi->tanggal,
                'referensi' => $mutasi->no_mutasi,
                'tipe' => 'masuk',
                'qty_masuk' => $detail->qty,
                'qty_keluar' => 0,
                'keterangan' => 'Mutasi dari ' . $mutasi->gudang_asal_id . ': ' . $mutasi->keterangan
            );
            $this->kartu_stok_model->insert_kartu_stok($kartu_masuk);
        }
        
        // Update status
        return $this->update($id, array('status' => 'completed'));
    }
    
    /**
     * DataTables
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'ps.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('ps.*, mga.nama as gudang_asal_nama, mgt.nama as gudang_tujuan_nama');
            $this->db->from($this->table . ' ps');
            $this->db->join('master_gudang mga', 'mga.id = ps.gudang_asal_id');
            $this->db->join('master_gudang mgt', 'mgt.id = ps.gudang_tujuan_id');
        };
        return $this->datatable_execute($params);
    }
}


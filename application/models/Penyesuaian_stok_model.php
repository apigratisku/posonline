<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyesuaian_stok_model extends MY_Model {
    
    protected $table = 'penyesuaian_stok';
    protected $primary_key = 'id';
    protected $fillable = array('no_penyesuaian', 'produk_id', 'gudang_id', 'tanggal', 'qty_awal', 'qty_penyesuaian', 'qty_akhir', 'alasan', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('ps.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' ps');
        $this->db->join('master_produk mp', 'mp.id = ps.produk_id');
        $this->db->join('master_gudang mg', 'mg.id = ps.gudang_id');
        $this->db->where('ps.id', $id);
        return $this->db->get()->row();
    }
    
    /**
     * Generate no_penyesuaian
     */
    public function generate_no_penyesuaian() {
        $prefix = 'ADJ-' . date('Ymd') . '-';
        $this->db->select('no_penyesuaian');
        $this->db->from($this->table);
        $this->db->like('no_penyesuaian', $prefix, 'after');
        $this->db->order_by('no_penyesuaian', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_penyesuaian, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Insert with auto calculate
     */
    public function insert($data) {
        // Calculate qty_akhir
        $data['qty_akhir'] = (isset($data['qty_awal']) ? $data['qty_awal'] : 0) + (isset($data['qty_penyesuaian']) ? $data['qty_penyesuaian'] : 0);
        
        return parent::insert($data);
    }
    
    /**
     * Update with auto calculate
     */
    public function update($id, $data) {
        // Calculate qty_akhir if qty_awal or qty_penyesuaian changed
        if (isset($data['qty_awal']) || isset($data['qty_penyesuaian'])) {
            $current = $this->get_by_id($id);
            $qty_awal = isset($data['qty_awal']) ? $data['qty_awal'] : $current->qty_awal;
            $qty_penyesuaian = isset($data['qty_penyesuaian']) ? $data['qty_penyesuaian'] : $current->qty_penyesuaian;
            $data['qty_akhir'] = $qty_awal + $qty_penyesuaian;
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Approve penyesuaian - update stock
     */
    public function approve($id) {
        $penyesuaian = $this->get_by_id($id);
        if (!$penyesuaian || $penyesuaian->status != 'draft') {
            return false;
        }
        
        // Update status
        $this->update($id, array('status' => 'approved'));
        
        // Update kartu stok
        $this->load->model('kartu_stok_model');
        $selisih = $penyesuaian->qty_penyesuaian;
        
        $kartu_data = array(
            'produk_id' => $penyesuaian->produk_id,
            'gudang_id' => $penyesuaian->gudang_id,
            'tanggal' => $penyesuaian->tanggal,
            'referensi' => $penyesuaian->no_penyesuaian,
            'tipe' => 'penyesuaian',
            'qty_masuk' => $selisih > 0 ? $selisih : 0,
            'qty_keluar' => $selisih < 0 ? abs($selisih) : 0,
            'keterangan' => 'Penyesuaian Stok: ' . $penyesuaian->alasan
        );
        $this->kartu_stok_model->insert_kartu_stok($kartu_data);
        
        return true;
    }
    
    /**
     * DataTables
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'ps.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('ps.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' ps');
            $this->db->join('master_produk mp', 'mp.id = ps.produk_id');
            $this->db->join('master_gudang mg', 'mg.id = ps.gudang_id');
        };
        return $this->datatable_execute($params);
    }
}


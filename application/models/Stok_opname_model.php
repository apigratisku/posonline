<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_opname_model extends MY_Model {
    
    protected $table = 'stok_opname';
    protected $primary_key = 'id';
    protected $fillable = array('no_opname', 'gudang_id', 'tanggal', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('so.*, mg.nama as gudang_nama');
        $this->db->from($this->table . ' so');
        $this->db->join('master_gudang mg', 'mg.id = so.gudang_id');
        $this->db->where('so.id', $id);
        return $this->db->get()->row();
    }
    
    /**
     * Get details
     */
    public function get_details($opname_id) {
        $this->db->select('sod.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from('stok_opname_detail sod');
        $this->db->join('master_produk mp', 'mp.id = sod.produk_id');
        $this->db->where('sod.opname_id', $opname_id);
        return $this->db->get()->result();
    }
    
    /**
     * Generate no_opname
     */
    public function generate_no_opname() {
        $prefix = 'OPN-' . date('Ymd') . '-';
        $this->db->select('no_opname');
        $this->db->from($this->table);
        $this->db->like('no_opname', $prefix, 'after');
        $this->db->order_by('no_opname', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_opname, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Complete opname - update stock
     */
    public function complete($id) {
        $opname = $this->get_by_id($id);
        if (!$opname || $opname->status != 'process') {
            return false;
        }
        
        // Get details
        $details = $this->get_details($id);
        
        // Load models
        $this->load->model('kartu_stok_model');
        
        foreach ($details as $detail) {
            $selisih = $detail->stok_fisik - $detail->stok_sistem;
            
            if ($selisih != 0) {
                // Update kartu stok
                $kartu_data = array(
                    'produk_id' => $detail->produk_id,
                    'gudang_id' => $opname->gudang_id,
                    'tanggal' => $opname->tanggal,
                    'referensi' => $opname->no_opname,
                    'tipe' => 'opname',
                    'qty_masuk' => $selisih > 0 ? $selisih : 0,
                    'qty_keluar' => $selisih < 0 ? abs($selisih) : 0,
                    'keterangan' => 'Stok Opname: ' . $opname->keterangan
                );
                $this->kartu_stok_model->insert_kartu_stok($kartu_data);
            }
        }
        
        // Update status
        return $this->update($id, array('status' => 'completed'));
    }
    
    /**
     * DataTables
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'so.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('so.*, mg.nama as gudang_nama');
            $this->db->from($this->table . ' so');
            $this->db->join('master_gudang mg', 'mg.id = so.gudang_id');
        };
        return $this->datatable_execute($params);
    }
}


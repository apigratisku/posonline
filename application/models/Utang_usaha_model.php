<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utang_usaha_model extends MY_Model {
    
    protected $table = 'utang_usaha';
    protected $primary_key = 'id';
    protected $fillable = array('faktur_pembelian_id', 'supplier_id', 'tanggal', 'jatuh_tempo', 'nominal', 'terbayar', 'sisa', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Insert utang usaha
     */
    public function insert($data) {
        $data['sisa'] = (isset($data['nominal']) ? $data['nominal'] : 0) - (isset($data['terbayar']) ? $data['terbayar'] : 0);
        $data['status'] = $data['sisa'] <= 0 ? 'paid' : ($data['terbayar'] > 0 ? 'partial' : 'unpaid');
        return parent::insert($data);
    }
    
    /**
     * Update payment
     */
    public function add_payment($id, $nominal) {
        $utang = $this->get_by_id($id);
        if (!$utang) return false;
        
        $terbayar = $utang->terbayar + $nominal;
        $sisa = $utang->nominal - $terbayar;
        $status = $sisa <= 0 ? 'paid' : ($terbayar > 0 ? 'partial' : 'unpaid');
        
        return $this->update($id, array(
            'terbayar' => $terbayar,
            'sisa' => $sisa,
            'status' => $status
        ));
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('uu.*, fp.no_faktur, s.nama as supplier_nama, pp.no_po');
        $this->db->from($this->table . ' uu');
        $this->db->join('faktur_pembelian fp', 'fp.id = uu.faktur_pembelian_id', 'left');
        $this->db->join('supplier s', 's.id = uu.supplier_id');
        $this->db->join('pesanan_pembelian pp', 'pp.id = fp.po_id', 'left');
        $this->db->where('uu.id', $id);
        return $this->db->get()->row();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'uu.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('uu.*, fp.no_faktur, s.nama as supplier_nama');
            $this->db->from($this->table . ' uu');
            $this->db->join('faktur_pembelian fp', 'fp.id = uu.faktur_pembelian_id', 'left');
            $this->db->join('supplier s', 's.id = uu.supplier_id');
        };
        return $this->datatable_execute($params);
    }
}

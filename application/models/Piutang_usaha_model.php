<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Piutang_usaha_model extends MY_Model {
    
    protected $table = 'piutang_usaha';
    protected $primary_key = 'id';
    protected $fillable = array('faktur_penjualan_id', 'pelanggan_id', 'tanggal', 'jatuh_tempo', 'nominal', 'terbayar', 'sisa', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Insert piutang usaha
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
        $piutang = $this->get_by_id($id);
        if (!$piutang) return false;
        
        $terbayar = $piutang->terbayar + $nominal;
        $sisa = $piutang->nominal - $terbayar;
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
        $this->db->select('pu.*, fp.no_faktur, pl.nama as pelanggan_nama');
        $this->db->from($this->table . ' pu');
        $this->db->join('faktur_penjualan fp', 'fp.id = pu.faktur_penjualan_id', 'left');
        $this->db->join('pelanggan pl', 'pl.id = pu.pelanggan_id');
        $this->db->where('pu.id', $id);
        return $this->db->get()->row();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'pu.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('pu.*, fp.no_faktur, pl.nama as pelanggan_nama');
            $this->db->from($this->table . ' pu');
            $this->db->join('faktur_penjualan fp', 'fp.id = pu.faktur_penjualan_id', 'left');
            $this->db->join('pelanggan pl', 'pl.id = pu.pelanggan_id');
        };
        return $this->datatable_execute($params);
    }
}

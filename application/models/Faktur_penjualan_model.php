<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faktur_penjualan_model extends MY_Model {
    
    protected $table = 'faktur_penjualan';
    protected $primary_key = 'id';
    protected $fillable = array('no_faktur', 'penjualan_id', 'pelanggan_id', 'tanggal', 'jatuh_tempo', 'subtotal', 'diskon', 'pajak', 'total', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function generate_no_faktur() {
        $prefix = 'FAK-PJL-' . date('Ymd') . '-';
        $this->db->select('no_faktur');
        $this->db->from($this->table);
        $this->db->like('no_faktur', $prefix, 'after');
        $this->db->order_by('no_faktur', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_faktur, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    public function get_by_id_with_relations($id) {
        $this->db->select('fp.*, p.nama as pelanggan_nama, pj.no_penjualan');
        $this->db->from($this->table . ' fp');
        $this->db->join('pelanggan p', 'p.id = fp.pelanggan_id', 'left');
        $this->db->join('penjualan pj', 'pj.id = fp.penjualan_id', 'left');
        $this->db->where('fp.id', $id);
        return $this->db->get()->row();
    }
    
    public function get_details($faktur_id) {
        $this->db->select('fpd.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from('faktur_penjualan_detail fpd');
        $this->db->join('master_produk mp', 'mp.id = fpd.produk_id');
        $this->db->where('fpd.faktur_id', $faktur_id);
        return $this->db->get()->result();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'fp.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('fp.*, p.nama as pelanggan_nama');
            $this->db->from($this->table . ' fp');
            $this->db->join('pelanggan p', 'p.id = fp.pelanggan_id', 'left');
        };
        return $this->datatable_execute($params);
    }
}

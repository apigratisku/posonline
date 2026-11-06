<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pesanan_penjualan_model extends MY_Model {
    
    protected $table = 'pesanan_penjualan';
    protected $primary_key = 'id';
    protected $fillable = array('no_pesanan', 'pelanggan_id', 'sales_id', 'tanggal', 'tanggal_kirim', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function generate_no_pesanan() {
        $prefix = 'PO-PJL-' . date('Ymd') . '-';
        $this->db->select('no_pesanan');
        $this->db->from($this->table);
        $this->db->like('no_pesanan', $prefix, 'after');
        $this->db->order_by('no_pesanan', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_pesanan, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    public function get_by_id_with_relations($id) {
        $this->db->select('pp.*, pl.nama as pelanggan_nama, s.nama as sales_nama');
        $this->db->from($this->table . ' pp');
        $this->db->join('pelanggan pl', 'pl.id = pp.pelanggan_id', 'left');
        $this->db->join('sales s', 's.id = pp.sales_id', 'left');
        $this->db->where('pp.id', $id);
        return $this->db->get()->row();
    }
    
    public function get_details($pesanan_id) {
        $this->db->select('ppd.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from('pesanan_penjualan_detail ppd');
        $this->db->join('master_produk mp', 'mp.id = ppd.produk_id');
        $this->db->where('ppd.pesanan_id', $pesanan_id);
        return $this->db->get()->result();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'pp.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('pp.*, pl.nama as pelanggan_nama');
            $this->db->from($this->table . ' pp');
            $this->db->join('pelanggan pl', 'pl.id = pp.pelanggan_id', 'left');
        };
        return $this->datatable_execute($params);
    }
}


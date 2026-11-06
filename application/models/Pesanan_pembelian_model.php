<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pesanan_pembelian_model extends MY_Model {
    
    protected $table = 'pesanan_pembelian';
    protected $primary_key = 'id';
    protected $fillable = array('no_po', 'supplier_id', 'gudang_id', 'tanggal', 'tanggal_kirim', 'subtotal', 'diskon', 'pajak', 'total', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function generate_no_po() {
        $prefix = 'PO-' . date('Ymd') . '-';
        $this->db->select('no_po');
        $this->db->from($this->table);
        $this->db->like('no_po', $prefix, 'after');
        $this->db->order_by('no_po', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_po, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    public function get_by_id_with_relations($id) {
        $this->db->select('pp.*, s.nama as supplier_nama, mg.nama as gudang_nama');
        $this->db->from($this->table . ' pp');
        $this->db->join('supplier s', 's.id = pp.supplier_id');
        $this->db->join('master_gudang mg', 'mg.id = pp.gudang_id');
        $this->db->where('pp.id', $id);
        return $this->db->get()->row();
    }
    
    public function get_details($po_id) {
        $this->db->select('ppd.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from('pesanan_pembelian_detail ppd');
        $this->db->join('master_produk mp', 'mp.id = ppd.produk_id');
        $this->db->where('ppd.po_id', $po_id);
        return $this->db->get()->result();
    }
    
    /**
     * Kirim PO (draft -> sent)
     */
    public function send($id) {
        $po = $this->get_by_id($id);
        if (!$po || $po->status != 'draft') {
            return false;
        }
        return $this->update($id, array('status' => 'sent'));
    }
    
    /**
     * Receive PO - update stock
     */
    public function receive($id) {
        $po = $this->get_by_id($id);
        if (!$po || $po->status != 'sent') {
            return false;
        }
        
        $details = $this->get_details($id);
        $this->load->model('kartu_stok_model');
        
        $this->db->trans_start();
        
        foreach ($details as $detail) {
            $kartu_data = array(
                'produk_id' => $detail->produk_id,
                'gudang_id' => $po->gudang_id,
                'tanggal' => date('Y-m-d'),
                'referensi' => $po->no_po,
                'tipe' => 'masuk',
                'qty_masuk' => $detail->qty,
                'qty_keluar' => 0,
                'harga' => $detail->harga,
                'keterangan' => 'Pembelian: ' . $po->no_po
            );
            $this->kartu_stok_model->insert_kartu_stok($kartu_data);
            
            $this->db->where('id', $detail->produk_id);
            $this->db->update('master_produk', array('harga_beli' => $detail->harga));
        }
        
        $this->update($id, array('status' => 'received'));
        
        $this->db->trans_complete();
        
        return $this->db->trans_status() !== FALSE;
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'pp.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('pp.*, s.nama as supplier_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' pp');
            $this->db->join('supplier s', 's.id = pp.supplier_id');
            $this->db->join('master_gudang mg', 'mg.id = pp.gudang_id');
        };
        return $this->datatable_execute($params);
    }
}

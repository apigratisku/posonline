<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_pembelian_model extends MY_Model {
    
    protected $table = 'retur_pembelian';
    protected $primary_key = 'id';
    protected $fillable = array('no_retur', 'faktur_pembelian_id', 'supplier_id', 'tanggal', 'subtotal', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function generate_no_retur() {
        $prefix = 'RET-PBL-' . date('Ymd') . '-';
        $this->db->select('no_retur');
        $this->db->from($this->table);
        $this->db->like('no_retur', $prefix, 'after');
        $this->db->order_by('no_retur', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_retur, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    public function get_by_id_with_relations($id) {
        $this->db->select('rp.*, s.nama as supplier_nama, fp.no_faktur');
        $this->db->from($this->table . ' rp');
        $this->db->join('supplier s', 's.id = rp.supplier_id');
        $this->db->join('faktur_pembelian fp', 'fp.id = rp.faktur_pembelian_id', 'left');
        $this->db->where('rp.id', $id);
        return $this->db->get()->row();
    }
    
    public function get_details($retur_id) {
        $this->db->select('rpd.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from('retur_pembelian_detail rpd');
        $this->db->join('master_produk mp', 'mp.id = rpd.produk_id');
        $this->db->where('rpd.retur_id', $retur_id);
        return $this->db->get()->result();
    }
    
    public function send($id) {
        $retur = $this->get_by_id($id);
        if (!$retur || $retur->status != 'draft') {
            return false;
        }
        
        $details = $this->get_details($id);
        $this->load->model('kartu_stok_model');
        $this->load->model('faktur_pembelian_model');
        $this->load->model('pesanan_pembelian_model');
        
        // Find gudang_id from Faktur -> PO
        $gudang_id = 1; // Default
        if ($retur->faktur_pembelian_id) {
            $faktur = $this->faktur_pembelian_model->get_by_id($retur->faktur_pembelian_id);
            if ($faktur && $faktur->po_id) {
                $po = $this->pesanan_pembelian_model->get_by_id($faktur->po_id);
                if ($po && $po->gudang_id) {
                    $gudang_id = $po->gudang_id;
                }
            }
        }
        
        $this->db->trans_start();
        
        foreach ($details as $detail) {
            $kartu_data = array(
                'produk_id' => $detail->produk_id,
                'gudang_id' => $gudang_id,
                'tanggal' => date('Y-m-d'),
                'referensi' => $retur->no_retur,
                'tipe' => 'keluar',
                'qty_masuk' => 0,
                'qty_keluar' => $detail->qty,
                'harga' => $detail->harga,
                'keterangan' => 'Retur Pembelian: ' . $retur->no_retur
            );
            $this->kartu_stok_model->insert_kartu_stok($kartu_data);
        }
        
        $this->update($id, array('status' => 'sent'));
        
        $this->db->trans_complete();
        
        return $this->db->trans_status() !== FALSE;
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'rp.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('rp.*, s.nama as supplier_nama, fp.no_faktur');
            $this->db->from($this->table . ' rp');
            $this->db->join('supplier s', 's.id = rp.supplier_id');
            $this->db->join('faktur_pembelian fp', 'fp.id = rp.faktur_pembelian_id', 'left');
        };
        return $this->datatable_execute($params);
    }
}

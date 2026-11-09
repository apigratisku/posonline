<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faktur_pembelian_model extends MY_Model {
    
    protected $table = 'faktur_pembelian';
    protected $primary_key = 'id';
    protected $fillable = array('no_faktur', 'po_id', 'supplier_id', 'jenis_pembayaran_id', 'tanggal', 'jatuh_tempo', 'subtotal', 'diskon', 'pajak', 'total', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function generate_no_faktur() {
        $prefix = 'FAK-PBL-' . date('Ymd') . '-';
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
        $this->db->select('fp.*, s.nama as supplier_nama, pp.no_po, mby.nama as jenis_pembayaran_nama');
        $this->db->from($this->table . ' fp');
        $this->db->join('supplier s', 's.id = fp.supplier_id');
        $this->db->join('pesanan_pembelian pp', 'pp.id = fp.po_id', 'left');
        $this->db->join('metode_pembayaran mby', 'mby.id = fp.jenis_pembayaran_id', 'left');
        $this->db->where('fp.id', $id);
        return $this->db->get()->row();
    }
    
    public function get_details($faktur_id) {
        $this->db->select('fpd.*, mp.kode as produk_kode, mp.nama as produk_nama, ms.nama as satuan_beli_nama');
        $this->db->from('faktur_pembelian_detail fpd');
        $this->db->join('master_produk mp', 'mp.id = fpd.produk_id');
        $this->db->join('master_satuan ms', 'ms.id = fpd.satuan_beli_id', 'left');
        $this->db->where('fpd.faktur_id', $faktur_id);
        return $this->db->get()->result();
    }
    
    /**
     * Apply period filter for completed purchases
     */
    protected function apply_period_filter($start_date, $end_date) {
        $start = date('Y-m-d', strtotime($start_date));
        $end = date('Y-m-d', strtotime($end_date));
        $this->db->where('tanggal >=', $start);
        $this->db->where('tanggal <=', $end);
        $this->db->where('status', 'completed');
    }

    /**
     * Sum total pembelian dalam periode
     */
    public function get_total_by_period($start_date, $end_date) {
        $this->db->reset_query();
        $this->db->select_sum('total');
        $this->apply_period_filter($start_date, $end_date);
        $row = $this->db->get($this->table)->row();
        return floatval(isset($row->total) ? $row->total : 0);
    }

    /**
     * Total pembelian per hari untuk grafik
     */
    public function get_daily_totals($start_date, $end_date) {
        $this->db->reset_query();
        $this->db->select('tanggal, SUM(total) as total', false);
        $this->apply_period_filter($start_date, $end_date);
        $this->db->group_by('tanggal');
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'fp.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('fp.*, s.nama as supplier_nama');
            $this->db->from($this->table . ' fp');
            $this->db->join('supplier s', 's.id = fp.supplier_id');
        };
        return $this->datatable_execute($params);
    }
}


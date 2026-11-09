<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_model extends MY_Model {
    
    protected $table = 'penjualan';
    protected $primary_key = 'id';
    protected $fillable = array('no_penjualan', 'pelanggan_id', 'sales_id', 'shift_id', 'gudang_id', 'metode_pembayaran_id', 'tanggal', 'subtotal', 'diskon', 'pajak', 'total', 'bayar', 'kembalian', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Generate no_penjualan
     */
    public function generate_no_penjualan() {
        $prefix = 'PJL-' . date('Ymd') . '-';
        $this->db->select('no_penjualan');
        $this->db->from($this->table);
        $this->db->like('no_penjualan', $prefix, 'after');
        $this->db->order_by('no_penjualan', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        if ($last) {
            $last_num = intval(substr($last->no_penjualan, -4));
            $new_num = $last_num + 1;
        } else {
            $new_num = 1;
        }
        
        return $prefix . str_pad($new_num, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('p.*, 
            pl.nama as pelanggan_nama, 
            pl.alamat as pelanggan_alamat,
            pl.telepon as pelanggan_telepon,
            s.nama as sales_nama, 
            s.kode as sales_kode,
            ms.nama as shift_nama, 
            ms.kode as shift_kode,
            mg.nama as gudang_nama,
            mg.kode as gudang_kode,
            mp.nama as metode_pembayaran_nama,
            mp.kode as metode_pembayaran_kode,
            mp.tipe as metode_pembayaran_tipe');
        $this->db->from($this->table . ' p');
        $this->db->join('pelanggan pl', 'pl.id = p.pelanggan_id', 'left');
        $this->db->join('sales s', 's.id = p.sales_id', 'left');
        $this->db->join('master_shift ms', 'ms.id = p.shift_id', 'left');
        $this->db->join('master_gudang mg', 'mg.id = p.gudang_id', 'left');
        $this->db->join('metode_pembayaran mp', 'mp.id = p.metode_pembayaran_id', 'left');
        $this->db->where('p.id', $id);
        $result = $this->db->get()->row();
        
        // Set default values for null relations
        if ($result) {
            if (empty($result->pelanggan_nama)) {
                $result->pelanggan_nama = 'Umum';
            }
            if (empty($result->sales_nama)) {
                $result->sales_nama = null;
            }
            if (empty($result->shift_nama)) {
                $result->shift_nama = null;
            }
        }
        
        return $result;
    }
    
    /**
     * Get details
     */
    public function get_details($penjualan_id) {
        $this->db->select('pd.*, mp.kode as produk_kode, mp.nama as produk_nama, mp.satuan_id as satuan_jual_id, mp.satuan_beli_id, mp.satuan_konversi, ms.nama as satuan_nama');
        $this->db->from('penjualan_detail pd');
        $this->db->join('master_produk mp', 'mp.id = pd.produk_id');
        $this->db->join('master_satuan ms', 'ms.id = COALESCE(pd.satuan_id, mp.satuan_id)', 'left', false);
        $this->db->where('pd.penjualan_id', $penjualan_id);
        return $this->db->get()->result();
    }
    
    /**
     * Complete penjualan - update stock
     */
    public function complete($id) {
        $penjualan = $this->get_by_id($id);
        if (!$penjualan || $penjualan->status != 'draft') {
            return false;
        }
        
        // Get details
        $details = $this->get_details($id);
        
        // Load models
        $this->load->model('kartu_stok_model');
        
        foreach ($details as $detail) {
            // Update kartu stok
            $kartu_data = array(
                'produk_id' => $detail->produk_id,
                'gudang_id' => $penjualan->gudang_id,
                'tanggal' => date('Y-m-d', strtotime($penjualan->tanggal)),
                'referensi' => $penjualan->no_penjualan,
                'tipe' => 'keluar',
                'qty_masuk' => 0,
                'qty_keluar' => $detail->qty,
                'harga' => $detail->harga,
                'keterangan' => 'Penjualan: ' . $penjualan->no_penjualan
            );
            $this->kartu_stok_model->insert_kartu_stok($kartu_data);
        }
        
        // Update status
        return $this->update($id, array('status' => 'completed'));
    }
    
    /**
     * Apply period and optional kasir filter
     */
    protected function apply_period_filter($start_date, $end_date, $user_id = null) {
        $this->db->where('tanggal >=', $start_date);
        $this->db->where('tanggal <=', $end_date);
        $this->db->where('status', 'completed');
        if ($user_id) {
            $this->db->where('created_by', $user_id);
        }
    }

    /**
     * Sum total penjualan dalam periode
     */
    public function get_total_by_period($start_date, $end_date, $user_id = null) {
        $this->db->reset_query();
        $this->db->select_sum('total');
        $this->apply_period_filter($start_date, $end_date, $user_id);
        $row = $this->db->get($this->table)->row();
        return floatval(isset($row->total) ? $row->total : 0);
    }

    /**
     * Hitung jumlah transaksi dalam periode
     */
    public function count_by_period($start_date, $end_date, $user_id = null) {
        $this->db->reset_query();
        $this->apply_period_filter($start_date, $end_date, $user_id);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Total penjualan per hari untuk grafik
     */
    public function get_daily_totals($start_date, $end_date, $user_id = null) {
        $this->db->reset_query();
        $this->db->select('DATE(tanggal) as tanggal, SUM(total) as total', false);
        $this->apply_period_filter($start_date, $end_date, $user_id);
        $this->db->group_by('DATE(tanggal)', false);
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * DataTables
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'p.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('p.*, pl.nama as pelanggan_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' p');
            $this->db->join('pelanggan pl', 'pl.id = p.pelanggan_id', 'left');
            $this->db->join('master_gudang mg', 'mg.id = p.gudang_id');
        };
        return $this->datatable_execute($params);
    }
}


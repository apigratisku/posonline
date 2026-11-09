<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_produk_model extends MY_Model {
    
    protected $table = 'master_produk';
    protected $primary_key = 'id';
    protected $fillable = array(
        'kode', 'barcode', 'nama', 'pabrik', 'kategori_id', 'golongan_id',
        'satuan_id', 'satuan_beli_id', 'satuan_konversi', 'indikasi',
        'rak_id', 'gudang_id', 'pajak_id',
        'harga_beli', 'persentase_keuntungan', 'harga_jual',
        'margin_resep', 'harga_resep', 'margin_pelanggan', 'harga_pelanggan',
        'stok_minimal', 'stok_maksimal', 'stok_awal', 'stok_akhir',
        'expired_date', 'gambar', 'deskripsi', 'is_resep', 'is_active'
    );
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get all with joins
     */
    public function get_all_with_relations($where = array(), $order_by = null, $limit = null, $offset = null) {
        $this->db->select('mp.*, mk.nama as kategori_nama, mgol.nama as golongan_nama, ms.nama as satuan_nama, msb.nama as satuan_beli_nama, mr.nama as rak_nama, mg.nama as gudang_nama, mpaj.nama as pajak_nama');
        $this->db->from($this->table . ' mp');
        $this->db->join('master_kategori mk', 'mk.id = mp.kategori_id', 'left');
        $this->db->join('master_golongan mgol', 'mgol.id = mp.golongan_id', 'left');
        $this->db->join('master_satuan ms', 'ms.id = mp.satuan_id', 'left');
        $this->db->join('master_satuan msb', 'msb.id = mp.satuan_beli_id', 'left');
        $this->db->join('master_rak mr', 'mr.id = mp.rak_id', 'left');
        $this->db->join('master_gudang mg', 'mg.id = mp.gudang_id', 'left');
        $this->db->join('master_pajak mpaj', 'mpaj.id = mp.pajak_id', 'left');
        
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }
    
    /**
     * Get by ID with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('mp.*, mk.nama as kategori_nama, mgol.nama as golongan_nama, ms.nama as satuan_nama, msb.nama as satuan_beli_nama, mr.nama as rak_nama, mg.nama as gudang_nama, mpaj.nama as pajak_nama');
        $this->db->from($this->table . ' mp');
        $this->db->join('master_kategori mk', 'mk.id = mp.kategori_id', 'left');
        $this->db->join('master_golongan mgol', 'mgol.id = mp.golongan_id', 'left');
        $this->db->join('master_satuan ms', 'ms.id = mp.satuan_id', 'left');
        $this->db->join('master_satuan msb', 'msb.id = mp.satuan_beli_id', 'left');
        $this->db->join('master_rak mr', 'mr.id = mp.rak_id', 'left');
        $this->db->join('master_gudang mg', 'mg.id = mp.gudang_id', 'left');
        $this->db->join('master_pajak mpaj', 'mpaj.id = mp.pajak_id', 'left');
        $this->db->where('mp.id', $id);
        return $this->db->get()->row();
    }
    
    /**
     * Get current stock by product and warehouse
     */
    public function get_stok($produk_id, $gudang_id = null) {
        $this->db->select_sum('qty_masuk', 'total_masuk');
        $this->db->select_sum('qty_keluar', 'total_keluar');
        $this->db->from('kartu_stok');
        $this->db->where('produk_id', $produk_id);
        
        if ($gudang_id) {
            $this->db->where('gudang_id', $gudang_id);
        }
        
        $result = $this->db->get()->row();
        $total_masuk = isset($result->total_masuk) ? $result->total_masuk : 0;
        $total_keluar = isset($result->total_keluar) ? $result->total_keluar : 0;
        $stok = $total_masuk - $total_keluar;
        
        return $stok;
    }
    
    /**
     * Check if kode exists
     */
    public function kode_exists($kode, $exclude_id = null) {
        $this->db->where('kode', $kode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    /**
     * Check if barcode exists
     */
    public function barcode_exists($barcode, $exclude_id = null) {
        if (empty($barcode)) return false;
        
        $this->db->where('barcode', $barcode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    /**
     * Search products
     */
    public function search($term, $limit = 20) {
        $this->db->select('mp.*, mk.nama as kategori_nama, ms.nama as satuan_nama, msb.nama as satuan_beli_nama');
        $this->db->from($this->table . ' mp');
        $this->db->join('master_kategori mk', 'mk.id = mp.kategori_id', 'left');
        $this->db->join('master_satuan ms', 'ms.id = mp.satuan_id', 'left');
        $this->db->join('master_satuan msb', 'msb.id = mp.satuan_beli_id', 'left');
        $this->db->where('mp.is_active', 1);
        $this->db->group_start();
        $this->db->like('mp.kode', $term);
        $this->db->or_like('mp.barcode', $term);
        $this->db->or_like('mp.nama', $term);
        $this->db->group_end();
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
    
    /**
     * Tabel transaksi yang mereferensi produk_id.
     * Dipakai untuk mencegah hapus produk yang masih punya riwayat.
     */
    private $reference_tables = array(
        'defecta',
        'faktur_pembelian_detail',
        'faktur_penjualan_detail',
        'kartu_stok',
        'konsinyasi_masuk_detail',
        'mutasi_antar_outlet_detail',
        'penerimaan_resep_detail',
        'penjualan_detail',
        'penyesuaian_stok',
        'permintaan_mutasi_detail',
        'perpindahan_stok_detail',
        'pesanan_pembelian_detail',
        'pesanan_penjualan_detail',
        'promo_bundel_detail',
        'rencana_pembelian_detail',
        'retur_pembelian_detail',
        'retur_penjualan_detail',
        'stok_kadaluarsa',
        'stok_opname_detail',
        'template_racikan_detail'
    );

    /**
     * Cek apakah produk masih direferensikan tabel transaksi
     */
    public function is_in_use($id) {
        foreach ($this->reference_tables as $tbl) {
            if ($this->db->where('produk_id', $id)->count_all_results($tbl) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * DataTables with relations
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'mp.id';
        }
        $params['build_query'] = function () {
            $this->db->select('mp.*, mk.nama as kategori_nama, ms.nama as satuan_nama, mg.nama as gudang_nama');
            $this->db->from($this->table . ' mp');
            $this->db->join('master_kategori mk', 'mk.id = mp.kategori_id', 'left');
            $this->db->join('master_satuan ms', 'ms.id = mp.satuan_id', 'left');
            $this->db->join('master_gudang mg', 'mg.id = mp.gudang_id', 'left');
        };
        $params['apply_filters'] = function () use ($params) {
            $kategori_id = $this->input->post('kategori_id');
            if (!empty($kategori_id)) {
            $this->db->where('mp.kategori_id', intval($kategori_id));
            }
            $gudang_id = $this->input->post('gudang_id');
            if (!empty($gudang_id)) {
            $this->db->where('mp.gudang_id', intval($gudang_id));
            }
        };
        return $this->datatable_execute($params);
    }
}


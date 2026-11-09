<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasir extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penjualan_model');
        $this->load->model('penjualan_detail_model');
        $this->load->model('master_produk_model');
        $this->load->model('pelanggan_model');
        $this->load->model('sales_model');
        $this->load->model('master_shift_model');
        $this->load->model('master_gudang_model');
        $this->load->model('metode_pembayaran_model');
        $this->check_permission('penjualan', 'create');
    }
    
    /**
     * Resolve gudang assigned to current user (fallback: gudang utama)
     */
    private function resolve_user_gudang() {
        $gudang_id = isset($this->user->gudang_id) ? $this->user->gudang_id : null;
        if (!$gudang_id) {
            $utama = $this->master_gudang_model->get_all(array('is_utama' => 1, 'is_active' => 1));
            $gudang_id = !empty($utama) ? $utama[0]->id : null;
        }
        if (!$gudang_id) {
            return null;
        }
        return $this->master_gudang_model->get_by_id($gudang_id);
    }

    /**
     * Konversi qty dari satuan terpilih ke satuan jual terkecil.
     * Stok & kartu_stok selalu disimpan dalam satuan jual terkecil.
     */
    private function resolve_konversi_satuan($produk, $satuan_id) {
        $satuan_id = intval($satuan_id);
        $satuan_beli_id = isset($produk->satuan_beli_id) ? intval($produk->satuan_beli_id) : 0;
        $konversi = isset($produk->satuan_konversi) ? intval($produk->satuan_konversi) : 1;
        if ($konversi < 1) {
            $konversi = 1;
        }
        if ($satuan_id && $satuan_beli_id && $satuan_id === $satuan_beli_id) {
            return $konversi;
        }
        return 1;
    }

    /**
     * Validate gudang_id matches user's assigned gudang
     */
    private function assert_user_gudang($gudang_id) {
        $allowed = $this->resolve_user_gudang();
        if (!$allowed || intval($gudang_id) !== intval($allowed->id)) {
            return false;
        }
        return $allowed->is_active == 1;
    }

    /**
     * Index - Kasir page
     */
    public function index() {
        $assigned_gudang = $this->resolve_user_gudang();
        $data = array(
            'title' => 'Kasir',
            'breadcrumb' => array(
                array('label' => 'Penjualan', 'url' => '#'),
                array('label' => 'Kasir')
            ),
            'body_class' => 'has-mobile-nav apotek-app apotek-pos-page',
            'page_css' => array('pos.css'),
            'page_js' => array('kasir.js'),
            'sales' => $this->sales_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1)),
            'metode_pembayarans' => $this->metode_pembayaran_model->get_all(array('is_active' => 1)),
            'assigned_gudang' => $assigned_gudang,
            'assigned_gudang_id' => $assigned_gudang ? $assigned_gudang->id : null
        );
        
        $this->render('penjualan/kasir/index', $data);
    }
    
    /**
     * AJAX Search Product
     */
    public function ajax_search_produk() {
        $term = $this->input->get('q');
        $gudang_id = $this->input->get('gudang_id');
        
        if (!$this->assert_user_gudang($gudang_id)) {
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('results' => array(), 'error' => 'Gudang tidak diizinkan untuk user ini')));
            return;
        }
        
        if (empty($term)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('results' => array())));
            return;
        }
        
        $produks = $this->master_produk_model->search($term, 20);
        
        $results = array();
        foreach ($produks as $produk) {
            $stok = $this->master_produk_model->get_stok($produk->id, $gudang_id);
            $satuan_nama = isset($produk->satuan_nama) && !empty($produk->satuan_nama) ? $produk->satuan_nama : '-';
            $satuan_beli_nama = isset($produk->satuan_beli_nama) && !empty($produk->satuan_beli_nama) ? $produk->satuan_beli_nama : '-';
            $konversi = isset($produk->satuan_konversi) ? intval($produk->satuan_konversi) : 1;
            if ($konversi < 1) {
                $konversi = 1;
            }
            
            $results[] = array(
                'id' => $produk->id,
                'kode' => $produk->kode,
                'barcode' => isset($produk->barcode) ? $produk->barcode : '',
                'nama' => $produk->nama,
                'harga_jual' => floatval($produk->harga_jual),
                'harga_resep' => floatval($produk->harga_resep),
                'harga_pelanggan' => floatval($produk->harga_pelanggan),
                'stok' => $stok,
                'satuan' => $satuan_nama,
                'satuan_nama' => $satuan_nama,
                'satuan_id' => isset($produk->satuan_id) && $produk->satuan_id ? intval($produk->satuan_id) : null,
                'satuan_beli_id' => isset($produk->satuan_beli_id) && $produk->satuan_beli_id ? intval($produk->satuan_beli_id) : null,
                'satuan_beli_nama' => $satuan_beli_nama,
                'satuan_konversi' => $konversi,
                'text' => $produk->nama . ' (' . $produk->kode . ') - Stok: ' . $stok . ' ' . $satuan_nama
            );
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('results' => $results)));
    }
    
    /**
     * AJAX Get Product by Kode (Barcode scan uses kode barang)
     */
    public function ajax_get_produk_by_barcode() {
        $barcode = $this->input->post('barcode');
        $gudang_id = $this->input->post('gudang_id');
        
        if (!$this->assert_user_gudang($gudang_id)) {
            $this->ajax_response(false, 'Gudang tidak diizinkan untuk user ini');
            return;
        }
        
        if (empty($barcode)) {
            $this->ajax_response(false, 'Kode tidak boleh kosong');
            return;
        }
        
        // Search by kode barang (primary - no new DB field needed)
        $this->db->select('mp.*, ms.nama as satuan_nama, msb.nama as satuan_beli_nama');
        $this->db->from('master_produk mp');
        $this->db->join('master_satuan ms', 'ms.id = mp.satuan_id', 'left');
        $this->db->join('master_satuan msb', 'msb.id = mp.satuan_beli_id', 'left');
        $this->db->where('mp.kode', $barcode);
        $this->db->where('mp.is_active', 1);
        $produk = $this->db->get()->row();
        
        if (!$produk) {
            // Fallback: also try the nama (fuzzy not needed, exact kode only)
            // No additional DB field search needed
            $produk = null;
        }
        
        if ($produk) {
            $stok = $this->master_produk_model->get_stok($produk->id, $gudang_id);
            $satuan_nama = isset($produk->satuan_nama) && !empty($produk->satuan_nama) ? $produk->satuan_nama : '-';
            $satuan_beli_nama = isset($produk->satuan_beli_nama) && !empty($produk->satuan_beli_nama) ? $produk->satuan_beli_nama : '-';
            $konversi = isset($produk->satuan_konversi) ? intval($produk->satuan_konversi) : 1;
            if ($konversi < 1) {
                $konversi = 1;
            }
            
            $this->ajax_response(true, 'Produk ditemukan', array(
                'id' => $produk->id,
                'kode' => $produk->kode,
                'nama' => $produk->nama,
                'harga_jual' => floatval($produk->harga_jual),
                'harga_resep' => floatval($produk->harga_resep),
                'harga_pelanggan' => floatval($produk->harga_pelanggan),
                'stok' => $stok,
                'satuan' => $satuan_nama,
                'satuan_nama' => $satuan_nama,
                'satuan_id' => isset($produk->satuan_id) && $produk->satuan_id ? intval($produk->satuan_id) : null,
                'satuan_beli_id' => isset($produk->satuan_beli_id) && $produk->satuan_beli_id ? intval($produk->satuan_beli_id) : null,
                'satuan_beli_nama' => $satuan_beli_nama,
                'satuan_konversi' => $konversi
            ));
        } else {
            $this->ajax_response(false, 'Produk tidak ditemukan untuk kode: ' . $barcode);
        }
    }

    
    /**
     * AJAX Save Penjualan
     */
    public function ajax_save() {
        $this->check_permission('penjualan', 'create');
        
        $this->form_validation->set_rules('gudang_id', 'Gudang', 'required|integer');
        $this->form_validation->set_rules('metode_pembayaran_id', 'Metode Pembayaran', 'required|integer');
        $this->form_validation->set_rules('items', 'Items', 'required');
        $this->form_validation->set_rules('bayar', 'Bayar', 'required|numeric|greater_than_equal_to[0]');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $items = json_decode($this->input->post('items'), true);
        if (empty($items) || !is_array($items)) {
            $this->ajax_response(false, 'Item penjualan tidak boleh kosong');
            return;
        }
        
        $gudang_id = intval($this->input->post('gudang_id'));
        
        if (!$this->assert_user_gudang($gudang_id)) {
            $this->ajax_response(false, 'Gudang tidak diizinkan untuk user ini');
            return;
        }
        
        // Validate gudang exists
        $gudang = $this->master_gudang_model->get_by_id($gudang_id);
        if (!$gudang || !$gudang->is_active) {
            $this->ajax_response(false, 'Gudang tidak ditemukan atau tidak aktif');
            return;
        }
        
        // Validate metode pembayaran exists
        $metode_pembayaran_id = intval($this->input->post('metode_pembayaran_id'));
        $metode_pembayaran = $this->metode_pembayaran_model->get_by_id($metode_pembayaran_id);
        if (!$metode_pembayaran || !$metode_pembayaran->is_active) {
            $this->ajax_response(false, 'Metode pembayaran tidak ditemukan atau tidak aktif');
            return;
        }
        
        // Validate all items and check stock BEFORE transaction
        $this->load->model('kartu_stok_model');
        foreach ($items as $index => $item) {
            // Validate item structure
            if (!isset($item['produk_id']) || !isset($item['qty']) || !isset($item['harga'])) {
                $this->ajax_response(false, 'Data item tidak lengkap pada item ke-' . ($index + 1));
                return;
            }
            
            // Validate product exists
            $produk = $this->master_produk_model->get_by_id($item['produk_id']);
            if (!$produk || !$produk->is_active) {
                $this->ajax_response(false, 'Produk tidak ditemukan atau tidak aktif: ' . (isset($item['nama']) ? $item['nama'] : 'Item ke-' . ($index + 1)));
                return;
            }
            
            // Validate quantities and prices
            $qty = floatval($item['qty']);
            $harga = floatval($item['harga']);
            $diskon = isset($item['diskon']) ? floatval($item['diskon']) : 0;
            
            if ($qty <= 0) {
                $this->ajax_response(false, 'Quantity harus lebih dari 0 untuk produk: ' . (isset($item['nama']) ? $item['nama'] : 'Item ke-' . ($index + 1)));
                return;
            }
            
            if ($harga < 0) {
                $this->ajax_response(false, 'Harga tidak boleh negatif untuk produk: ' . (isset($item['nama']) ? $item['nama'] : 'Item ke-' . ($index + 1)));
                return;
            }
            
            if ($diskon < 0) {
                $this->ajax_response(false, 'Diskon tidak boleh negatif untuk produk: ' . (isset($item['nama']) ? $item['nama'] : 'Item ke-' . ($index + 1)));
                return;
            }
            
            // Resolve satuan yang dipilih kasir (satuan pembelian dikonversi ke satuan jual terkecil)
            $satuan_id = !empty($item['satuan_id']) ? intval($item['satuan_id']) : intval($produk->satuan_id);
            $konversi = $this->resolve_konversi_satuan($produk, $satuan_id);
            $qty_terkecil = $qty * $konversi;
            
            // Check stock availability BEFORE transaction (stok selalu dalam satuan jual terkecil)
            $stok = $this->master_produk_model->get_stok($item['produk_id'], $gudang_id);
            if ($stok < $qty_terkecil) {
                $produk_nama = isset($item['nama']) ? $item['nama'] : $produk->nama;
                $this->ajax_response(false, 'Stok produk "' . $produk_nama . '" tidak mencukupi. Stok tersedia: ' . $stok . ', dibutuhkan: ' . $qty_terkecil);
                return;
            }
        }
        
        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $qty = floatval($item['qty']);
            $harga = floatval($item['harga']);
            $diskon = isset($item['diskon']) ? floatval($item['diskon']) : 0;
            $subtotal += ($qty * $harga) - $diskon;
        }
        
        $diskon = floatval($this->input->post('diskon')) ?: 0;
        $pajak = floatval($this->input->post('pajak')) ?: 0;
        $total = $subtotal - $diskon + $pajak;
        $bayar = floatval($this->input->post('bayar')) ?: 0;
        $kembalian = $bayar - $total;
        
        // Validate payment
        if ($bayar < $total) {
            $this->ajax_response(false, 'Jumlah pembayaran kurang. Total: ' . number_format($total, 0, ',', '.') . ', Bayar: ' . number_format($bayar, 0, ',', '.'));
            return;
        }
        
        // Start transaction
        $this->db->trans_start();
        
        // Insert penjualan
        $penjualan_data = array(
            'no_penjualan' => $this->penjualan_model->generate_no_penjualan(),
            'pelanggan_id' => $this->input->post('pelanggan_id') ? intval($this->input->post('pelanggan_id')) : null,
            'sales_id' => $this->input->post('sales_id') ? intval($this->input->post('sales_id')) : null,
            'shift_id' => $this->input->post('shift_id') ? intval($this->input->post('shift_id')) : null,
            'gudang_id' => $gudang_id,
            'metode_pembayaran_id' => $metode_pembayaran_id,
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d H:i:s'),
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'pajak' => $pajak,
            'total' => $total,
            'bayar' => $bayar,
            'kembalian' => $kembalian,
            'status' => 'completed',
            'keterangan' => $this->input->post('keterangan') ?: null
        );
        
        $penjualan_id = $this->penjualan_model->insert($penjualan_data);
        
        if (!$penjualan_id) {
            $this->db->trans_rollback();
            $this->ajax_response(false, 'Gagal menyimpan penjualan');
            return;
        }
        
        // Insert details and update stock
        foreach ($items as $item) {
            $qty = floatval($item['qty']);
            $harga = floatval($item['harga']);
            $diskon = isset($item['diskon']) ? floatval($item['diskon']) : 0;

            // Resolve satuan terpilih (sama seperti validasi di atas)
            $produk = $this->master_produk_model->get_by_id($item['produk_id']);
            $satuan_id = !empty($item['satuan_id']) ? intval($item['satuan_id']) : intval($produk->satuan_id);
            $konversi = $this->resolve_konversi_satuan($produk, $satuan_id);
            $qty_terkecil = $qty * $konversi;
            $harga_terkecil = $konversi > 0 ? ($harga / $konversi) : $harga;

            // Double check stock (in case of concurrent transactions)
            $stok = $this->master_produk_model->get_stok($item['produk_id'], $gudang_id);
            if ($stok < $qty_terkecil) {
                $this->db->trans_rollback();
                $produk_nama = isset($item['nama']) ? $item['nama'] : 'Produk ID ' . $item['produk_id'];
                $this->ajax_response(false, 'Stok produk "' . $produk_nama . '" tidak mencukupi. Stok tersedia: ' . $stok);
                return;
            }
            
            // Insert detail (qty & harga disimpan sesuai satuan yang dipilih kasir)
            $detail_data = array(
                'penjualan_id' => $penjualan_id,
                'produk_id' => intval($item['produk_id']),
                'satuan_id' => $satuan_id ? $satuan_id : null,
                'qty' => $qty,
                'harga' => $harga,
                'diskon' => $diskon,
                'subtotal' => ($qty * $harga) - $diskon
            );
            
            $detail_id = $this->penjualan_detail_model->insert($detail_data);
            if (!$detail_id) {
                $this->db->trans_rollback();
                $this->ajax_response(false, 'Gagal menyimpan detail penjualan');
                return;
            }
            
            // Update kartu stok (selalu dalam satuan jual terkecil)
            $kartu_data = array(
                'produk_id' => intval($item['produk_id']),
                'gudang_id' => $gudang_id,
                'tanggal' => date('Y-m-d', strtotime($penjualan_data['tanggal'])),
                'referensi' => $penjualan_data['no_penjualan'],
                'tipe' => 'keluar',
                'qty_masuk' => 0,
                'qty_keluar' => $qty_terkecil,
                'harga' => $harga_terkecil,
                'keterangan' => 'Penjualan: ' . $penjualan_data['no_penjualan']
            );
            
            $kartu_id = $this->kartu_stok_model->insert_kartu_stok($kartu_data);
            if (!$kartu_id) {
                $this->db->trans_rollback();
                $this->ajax_response(false, 'Gagal menyimpan kartu stok');
                return;
            }
        }
        
        // Update member points if pelanggan has membership
        if ($penjualan_data['pelanggan_id']) {
            $pelanggan = $this->pelanggan_model->get_by_id($penjualan_data['pelanggan_id']);
            if ($pelanggan && isset($pelanggan->membership_id) && $pelanggan->membership_id) {
                // Calculate points (example: 1 point per 1000 rupiah)
                $poin = floor($total / 1000);
                $poin_sekarang = isset($pelanggan->poin) ? intval($pelanggan->poin) : 0;
                $this->pelanggan_model->update($pelanggan->id, array(
                    'poin' => $poin_sekarang + $poin
                ));
            }
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->ajax_response(false, 'Gagal menyimpan penjualan. Silakan coba lagi.');
        } else {
            $penjualan = $this->penjualan_model->get_by_id($penjualan_id);
            $this->ajax_response(true, 'Penjualan berhasil disimpan', array(
                'id' => $penjualan_id,
                'no_penjualan' => $penjualan->no_penjualan
            ));
        }
    }
    
    /**
     * AJAX Get Penjualan for Print
     */
    public function ajax_get_print($id) {
        $id = intval($id);
        if ($id <= 0) {
            $this->ajax_response(false, 'ID penjualan tidak valid');
            return;
        }
        
        $penjualan = $this->penjualan_model->get_by_id_with_relations($id);
        if (!$penjualan) {
            $this->ajax_response(false, 'Data penjualan tidak ditemukan');
            return;
        }
        
        $details = $this->penjualan_model->get_details($id);
        if (!$details) {
            $details = array();
        }
        
        // Format data for response
        $penjualan_data = array(
            'id' => $penjualan->id,
            'no_penjualan' => $penjualan->no_penjualan,
            'tanggal' => $penjualan->tanggal,
            'pelanggan_nama' => isset($penjualan->pelanggan_nama) ? $penjualan->pelanggan_nama : 'Umum',
            'pelanggan_alamat' => isset($penjualan->pelanggan_alamat) ? $penjualan->pelanggan_alamat : '',
            'pelanggan_telepon' => isset($penjualan->pelanggan_telepon) ? $penjualan->pelanggan_telepon : '',
            'sales_nama' => isset($penjualan->sales_nama) ? $penjualan->sales_nama : '',
            'shift_nama' => isset($penjualan->shift_nama) ? $penjualan->shift_nama : '',
            'gudang_nama' => isset($penjualan->gudang_nama) ? $penjualan->gudang_nama : '',
            'metode_pembayaran_nama' => isset($penjualan->metode_pembayaran_nama) ? $penjualan->metode_pembayaran_nama : '',
            'subtotal' => floatval($penjualan->subtotal),
            'diskon' => floatval($penjualan->diskon),
            'pajak' => floatval($penjualan->pajak),
            'total' => floatval($penjualan->total),
            'bayar' => floatval($penjualan->bayar),
            'kembalian' => floatval($penjualan->kembalian),
            'status' => $penjualan->status,
            'keterangan' => isset($penjualan->keterangan) ? $penjualan->keterangan : ''
        );
        
        $details_data = array();
        foreach ($details as $detail) {
            $details_data[] = array(
                'id' => $detail->id,
                'produk_kode' => isset($detail->produk_kode) ? $detail->produk_kode : '',
                'produk_nama' => isset($detail->produk_nama) ? $detail->produk_nama : '',
                'satuan_nama' => isset($detail->satuan_nama) ? $detail->satuan_nama : '',
                'qty' => floatval($detail->qty),
                'harga' => floatval($detail->harga),
                'diskon' => floatval($detail->diskon),
                'subtotal' => floatval($detail->subtotal)
            );
        }
        
        $this->ajax_response(true, 'Data berhasil diambil', array(
            'penjualan' => $penjualan_data,
            'details' => $details_data
        ));
    }
}


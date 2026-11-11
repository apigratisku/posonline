<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FakturPembelian extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('faktur_pembelian_model');
        $this->load->model('faktur_pembelian_detail_model');
        $this->load->model('pesanan_pembelian_model');
        $this->load->model('supplier_model');
        $this->load->model('master_produk_model');
        $this->load->model('master_satuan_model');
        $this->load->model('master_kategori_model');
        $this->load->model('master_golongan_model');
        $this->load->model('master_pajak_model');
        $this->load->model('metode_pembayaran_model');
        $this->check_permission('pembelian', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Faktur Pembelian',
            'breadcrumb' => array(
                array('label' => 'Pembelian', 'url' => '#'),
                array('label' => 'Faktur Pembelian')
            ),
            'suppliers' => $this->supplier_model->get_all(array('is_active' => 1)),
            'pos' => $this->pesanan_pembelian_model->get_all("status IN ('sent', 'received')"),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1)),
            'metode_pembayarans' => $this->metode_pembayaran_model->get_all(array('is_active' => 1)),
            'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
            'kategoris' => $this->master_kategori_model->get_all(array('is_active' => 1)),
            'golongs' => $this->master_golongan_model->get_all(array('is_active' => 1)),
            'pajaks' => $this->master_pajak_model->get_all(array('is_active' => 1))
        );
        
        $this->render('pembelian/faktur_pembelian/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('pembelian', 'view');
        
        $params = array(
            'columns' => array('fp.no_faktur', 'fp.tanggal', 'fp.total', 'fp.status'),
            'search_fields' => array('fp.no_faktur', 's.nama')
        );
        
        $result = $this->faktur_pembelian_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'paid' ? 'success' : ($row->status == 'partial' ? 'warning' : 'danger')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_faktur,
                $row->supplier_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                $row->jatuh_tempo ? date('d/m/Y', strtotime($row->jatuh_tempo)) : '-',
                number_format($row->total, 0, ',', '.'),
                $status_badge,
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($this->auth->has_permission('pembelian', 'view')) {
            $buttons .= '<button class="btn btn-sm btn-info" onclick="viewData(' . $id . ')" title="Lihat"><i class="fas fa-eye"></i></button> ';
        }
        if ($status == 'unpaid' && $this->auth->has_permission('pembelian', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="payInvoice(' . $id . ')" title="Tandai Lunas"><i class="fas fa-check-circle"></i></button> ';
        }
        return $buttons;
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('pembelian', 'update');
        } else {
            $this->check_permission('pembelian', 'create');
        }
        
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('no_faktur', 'No. Faktur', 'trim|max_length[50]');
        $this->form_validation->set_rules('diskon_tipe', 'Tipe Diskon', 'trim|in_list[persen,nominal]');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $items = json_decode($this->input->post('items'), true);
        if (empty($items)) {
            $this->ajax_response(false, 'Item faktur tidak boleh kosong');
            return;
        }
        
        // No. Faktur: isian manual, kalau kosong generate otomatis. Tidak boleh duplikat.
        $no_faktur = trim($this->input->post('no_faktur'));
        if ($no_faktur === '') {
            $no_faktur = $this->faktur_pembelian_model->generate_no_faktur();
        } elseif ($this->faktur_pembelian_model->no_faktur_exists($no_faktur, $id)) {
            $this->ajax_response(false, 'No. Faktur "' . $no_faktur . '" sudah digunakan');
            return;
        }
        
        // Diskon item: JS mengirim nominal (sudah dikonversi) + persen sebagai info.
        // Server tetap hitung ulang dari persen kalau dikirim, supaya aman dari manipulasi.
        $subtotal = 0;
        foreach ($items as $key => $item) {
            $qty   = floatval($item['qty']);
            $harga = floatval($item['harga']);
            $diskon = isset($item['diskon_persen']) && floatval($item['diskon_persen']) > 0
                ? ($qty * $harga) * (min(max(floatval($item['diskon_persen']), 0), 100) / 100)
                : max(floatval(isset($item['diskon']) ? $item['diskon'] : 0), 0);
            $items[$key]['diskon'] = $diskon;
            $subtotal += ($qty * $harga) - $diskon;
        }
        
        // Diskon global: input bisa persen atau nominal, dikonversi ke nominal
        $diskonInput = floatval($this->input->post('diskon')) ?: 0;
        $diskonTipe  = $this->input->post('diskon_tipe') === 'persen' ? 'persen' : 'nominal';
        $diskon = $diskonTipe === 'persen'
            ? $subtotal * (min(max($diskonInput, 0), 100) / 100)
            : max($diskonInput, 0);
        $pajak = floatval($this->input->post('pajak')) ?: 0;
        $total = $subtotal - $diskon + $pajak;
        
        $data = array(
            'no_faktur' => $no_faktur,
            'po_id' => $this->input->post('po_id') ?: null,
            'supplier_id' => $this->input->post('supplier_id'),
            'jenis_pembayaran_id' => $this->input->post('jenis_pembayaran_id') ?: null,
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'jatuh_tempo' => $this->input->post('jatuh_tempo') ?: null,
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'pajak' => $pajak,
            'total' => $total,
            'status' => 'unpaid'
        );
        
        $this->db->trans_start();
        
        if ($id) {
            $this->faktur_pembelian_model->update($id, $data);
            $this->db->where('faktur_id', $id)->delete('faktur_pembelian_detail');
        } else {
            $id = $this->faktur_pembelian_model->insert($data);
        }
        
        // Insert details and add stock if po_id is empty
        $this->load->model('kartu_stok_model');
        foreach ($items as $item) {
            $detail_data = array(
                'faktur_id' => $id,
                'produk_id' => $item['produk_id'],
                'satuan_beli_id' => !empty($item['satuan_beli_id']) ? $item['satuan_beli_id'] : null,
                'qty' => $item['qty'],
                'no_batch' => !empty($item['no_batch']) ? $item['no_batch'] : null,
                'expired_date' => !empty($item['expired_date']) ? $item['expired_date'] : null,
                'harga' => $item['harga'],
                'diskon' => isset($item['diskon']) ? $item['diskon'] : 0,
                'subtotal' => ($item['qty'] * $item['harga']) - (isset($item['diskon']) ? $item['diskon'] : 0)
            );
            $this->faktur_pembelian_detail_model->insert($detail_data);
            
            // Sync stock if no PO
            if (empty($data['po_id']) && !$this->input->post('id')) { // only add stock on insert, not update (simplification)
                $produk = $this->master_produk_model->get_by_id($item['produk_id']);
                $konversi = ($produk && (int) $produk->satuan_konversi > 0) ? (int) $produk->satuan_konversi : 1;
                $qty_terkecil = (float) $item['qty'] * $konversi;
                $harga_terkecil = $konversi > 0 ? ((float) $item['harga'] / $konversi) : (float) $item['harga'];

                $kartu_data = array(
                    'produk_id' => $item['produk_id'],
                    'gudang_id' => 1, // Default gudang
                    'tanggal' => date('Y-m-d'),
                    'referensi' => $data['no_faktur'],
                    'tipe' => 'masuk',
                    'qty_masuk' => $qty_terkecil,
                    'qty_keluar' => 0,
                    'harga' => $harga_terkecil,
                    'keterangan' => 'Faktur Pembelian: ' . $data['no_faktur']
                );
                $this->kartu_stok_model->insert_kartu_stok($kartu_data);
                
                // Update harga beli (per satuan jual terkecil)
                $this->db->where('id', $item['produk_id']);
                $this->db->update('master_produk', array('harga_beli' => round($harga_terkecil, 2)));
            }
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->ajax_response(false, 'Gagal menyimpan data');
        } else {
            $this->ajax_response(true, 'Data berhasil disimpan', array('id' => $id));
        }
    }
    
    /**
     * Tambah obat/produk baru langsung dari form faktur pembelian
     */
    public function ajax_add_produk() {
        $this->check_permission('pembelian', 'create');

        $this->form_validation->set_rules('kode', 'Kode Obat', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama Obat', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }

        $kode = $this->input->post('kode');
        if ($this->master_produk_model->kode_exists($kode)) {
            $this->ajax_response(false, 'Kode obat sudah digunakan');
            return;
        }

        $harga_beli = floatval($this->input->post('harga_beli')) ?: 0;
        $persentase = floatval($this->input->post('persentase_keuntungan')) ?: 0;
        $margin_resep = $this->input->post('margin_resep') !== null && $this->input->post('margin_resep') !== ''
            ? floatval($this->input->post('margin_resep')) : $persentase;
        $margin_pelanggan = $this->input->post('margin_pelanggan') !== null && $this->input->post('margin_pelanggan') !== ''
            ? floatval($this->input->post('margin_pelanggan')) : $persentase;

        $data = array(
            'kode' => $kode,
            'nama' => $this->input->post('nama'),
            'pabrik' => $this->input->post('pabrik'),
            'kategori_id' => $this->input->post('kategori_id') ?: null,
            'golongan_id' => $this->input->post('golongan_id') ?: null,
            'satuan_id' => $this->input->post('satuan_id') ?: null,
            'satuan_beli_id' => $this->input->post('satuan_beli_id') ?: null,
            'satuan_konversi' => intval($this->input->post('satuan_konversi')) ?: 1,
            'indikasi' => $this->input->post('indikasi'),
            'pajak_id' => $this->input->post('pajak_id') ?: null,
            'harga_beli' => $harga_beli,
            'persentase_keuntungan' => $persentase,
            'harga_jual' => round($harga_beli + ($harga_beli * $persentase / 100), 0),
            'margin_resep' => $margin_resep,
            'harga_resep' => round($harga_beli + ($harga_beli * $margin_resep / 100), 0),
            'margin_pelanggan' => $margin_pelanggan,
            'harga_pelanggan' => round($harga_beli + ($harga_beli * $margin_pelanggan / 100), 0),
            'stok_minimal' => 0,
            'stok_maksimal' => 0,
            'stok_awal' => 0,
            'stok_akhir' => 0,
            'is_active' => 1
        );

        $insert_id = $this->master_produk_model->insert($data);
        if (!$insert_id) {
            $this->ajax_response(false, 'Gagal menambahkan obat baru');
            return;
        }

        $this->ajax_response(true, 'Obat baru berhasil ditambahkan', array(
            'id' => (int) $insert_id,
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'harga_beli' => (float) $data['harga_beli'],
            'satuan_beli_id' => $data['satuan_beli_id'],
            'satuan_konversi' => (int) $data['satuan_konversi']
        ));
    }

    public function ajax_get($id) {
        $this->check_permission('pembelian', 'view');
        $faktur = $this->faktur_pembelian_model->get_by_id_with_relations($id);
        if ($faktur) {
            $details = $this->faktur_pembelian_model->get_details($id);
            $this->ajax_response(true, 'Data berhasil diambil', array(
                'faktur' => $faktur,
                'details' => $details
            ));
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }

    public function ajax_pay($id) {
        $this->check_permission('pembelian', 'update');
        $faktur = $this->faktur_pembelian_model->get_by_id($id);
        if (!$faktur || $faktur->status != 'unpaid') {
            $this->ajax_response(false, 'Hanya faktur belum lunas yang dapat dibayarkan');
            return;
        }
        
        $this->db->trans_start();
        
        $this->faktur_pembelian_model->update($id, array('status' => 'paid'));
        
        // Sinkronisasi status PO ke completed jika ada
        if ($faktur->po_id) {
            $this->load->model('pesanan_pembelian_model');
            $this->pesanan_pembelian_model->update($faktur->po_id, array('status' => 'completed'));
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->ajax_response(false, 'Gagal memproses pembayaran');
        } else {
            $this->ajax_response(true, 'Faktur berhasil ditandai sebagai Lunas');
        }
    }
}


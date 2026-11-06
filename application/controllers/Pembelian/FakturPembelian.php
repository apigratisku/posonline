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
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
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
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $items = json_decode($this->input->post('items'), true);
        if (empty($items)) {
            $this->ajax_response(false, 'Item faktur tidak boleh kosong');
            return;
        }
        
        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['qty'] * $item['harga']) - (isset($item['diskon']) ? $item['diskon'] : 0);
        }
        $diskon = floatval($this->input->post('diskon')) ?: 0;
        $pajak = floatval($this->input->post('pajak')) ?: 0;
        $total = $subtotal - $diskon + $pajak;
        
        $data = array(
            'no_faktur' => $id ? $this->input->post('no_faktur') : $this->faktur_pembelian_model->generate_no_faktur(),
            'po_id' => $this->input->post('po_id') ?: null,
            'supplier_id' => $this->input->post('supplier_id'),
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
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'diskon' => isset($item['diskon']) ? $item['diskon'] : 0,
                'subtotal' => ($item['qty'] * $item['harga']) - (isset($item['diskon']) ? $item['diskon'] : 0)
            );
            $this->faktur_pembelian_detail_model->insert($detail_data);
            
            // Sync stock if no PO
            if (empty($data['po_id']) && !$this->input->post('id')) { // only add stock on insert, not update (simplification)
                $kartu_data = array(
                    'produk_id' => $item['produk_id'],
                    'gudang_id' => 1, // Default gudang
                    'tanggal' => date('Y-m-d'),
                    'referensi' => $data['no_faktur'],
                    'tipe' => 'masuk',
                    'qty_masuk' => $item['qty'],
                    'qty_keluar' => 0,
                    'harga' => $item['harga'],
                    'keterangan' => 'Faktur Pembelian: ' . $data['no_faktur']
                );
                $this->kartu_stok_model->insert_kartu_stok($kartu_data);
                
                // Update harga beli
                $this->db->where('id', $item['produk_id']);
                $this->db->update('master_produk', array('harga_beli' => $item['harga']));
            }
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->ajax_response(false, 'Gagal menyimpan data');
        } else {
            $this->ajax_response(true, 'Data berhasil disimpan', array('id' => $id));
        }
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


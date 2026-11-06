<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PesananPembelian extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('pesanan_pembelian_model');
        $this->load->model('pesanan_pembelian_detail_model');
        $this->load->model('supplier_model');
        $this->load->model('master_gudang_model');
        $this->load->model('master_produk_model');
        $this->check_permission('pembelian', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Pesanan Pembelian',
            'breadcrumb' => array(
                array('label' => 'Pembelian', 'url' => '#'),
                array('label' => 'Pesanan Pembelian')
            ),
            'suppliers' => $this->supplier_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1)),
            'page_js' => array('pembelian-pesanan.js')
        );
        
        $this->render('pembelian/pesanan_pembelian/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('pembelian', 'view');
        
        $params = array(
            'columns' => array('pp.no_po', 'pp.tanggal', 'pp.total', 'pp.status'),
            'search_fields' => array('pp.no_po', 's.nama', 'mg.nama')
        );
        
        $result = $this->pesanan_pembelian_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'cancelled' ? 'danger' : ($row->status == 'received' ? 'info' : ($row->status == 'sent' ? 'warning' : 'secondary')))) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_po,
                $row->supplier_nama,
                $row->gudang_nama,
                date('d/m/Y', strtotime($row->tanggal)),
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
        if ($status == 'draft' && $this->auth->has_permission('pembelian', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-primary" onclick="editData(' . $id . ')" title="Edit"><i class="fas fa-edit"></i></button> ';
            $buttons .= '<button class="btn btn-sm btn-warning" onclick="sendData(' . $id . ')" title="Kirim"><i class="fas fa-paper-plane"></i></button> ';
        }
        if ($status == 'sent' && $this->auth->has_permission('pembelian', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="receiveData(' . $id . ')" title="Terima Barang"><i class="fas fa-check"></i> Terima</button>';
        }
        return $buttons;
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('pembelian', 'update');
            $existing = $this->pesanan_pembelian_model->get_by_id($id);
            if (!$existing || $existing->status != 'draft') {
                $this->ajax_response(false, 'Hanya pesanan berstatus draft yang dapat diubah');
                return;
            }
        } else {
            $this->check_permission('pembelian', 'create');
        }
        
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('gudang_id', 'Gudang', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $items = json_decode($this->input->post('items'), true);
        if (empty($items)) {
            $this->ajax_response(false, 'Item pembelian tidak boleh kosong');
            return;
        }
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['qty'] * $item['harga']) - (isset($item['diskon']) ? $item['diskon'] : 0);
        }
        $diskon = floatval($this->input->post('diskon')) ?: 0;
        $pajak = floatval($this->input->post('pajak')) ?: 0;
        $total = $subtotal - $diskon + $pajak;
        
        $data = array(
            'no_po' => $id ? $this->input->post('no_po') : $this->pesanan_pembelian_model->generate_no_po(),
            'supplier_id' => $this->input->post('supplier_id'),
            'gudang_id' => $this->input->post('gudang_id'),
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'tanggal_kirim' => $this->input->post('tanggal_kirim') ?: null,
            'subtotal' => $subtotal,
            'diskon' => $diskon,
            'pajak' => $pajak,
            'total' => $total,
            'status' => $this->input->post('status') ?: 'draft',
            'keterangan' => $this->input->post('keterangan')
        );
        
        $this->db->trans_start();
        
        if ($id) {
            $this->pesanan_pembelian_model->update($id, $data);
            $this->db->where('po_id', $id)->delete('pesanan_pembelian_detail');
        } else {
            $id = $this->pesanan_pembelian_model->insert($data);
        }
        
        foreach ($items as $item) {
            $detail_data = array(
                'po_id' => $id,
                'produk_id' => $item['produk_id'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'diskon' => isset($item['diskon']) ? $item['diskon'] : 0,
                'subtotal' => ($item['qty'] * $item['harga']) - (isset($item['diskon']) ? $item['diskon'] : 0)
            );
            $this->pesanan_pembelian_detail_model->insert($detail_data);
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
        $po = $this->pesanan_pembelian_model->get_by_id_with_relations($id);
        if ($po) {
            $details = $this->pesanan_pembelian_model->get_details($id);
            $this->ajax_response(true, 'Data berhasil diambil', array(
                'po' => $po,
                'details' => $details
            ));
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_send($id) {
        $this->check_permission('pembelian', 'update');
        if ($this->pesanan_pembelian_model->send($id)) {
            $this->ajax_response(true, 'Pesanan pembelian berhasil dikirim');
        } else {
            $this->ajax_response(false, 'Gagal mengirim pesanan pembelian');
        }
    }
    
    public function ajax_receive($id) {
        $this->check_permission('pembelian', 'update');
        if ($this->pesanan_pembelian_model->receive($id)) {
            $this->ajax_response(true, 'Pesanan pembelian berhasil diterima dan stok telah diperbarui');
        } else {
            $this->ajax_response(false, 'Gagal menerima pesanan pembelian');
        }
    }
}

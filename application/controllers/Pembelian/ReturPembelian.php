<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReturPembelian extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('retur_pembelian_model');
        $this->load->model('retur_pembelian_detail_model');
        $this->load->model('supplier_model');
        $this->load->model('faktur_pembelian_model');
        $this->load->model('master_produk_model');
        $this->check_permission('pembelian', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Retur Pembelian',
            'breadcrumb' => array(
                array('label' => 'Pembelian', 'url' => '#'),
                array('label' => 'Retur Pembelian')
            ),
            'suppliers' => $this->supplier_model->get_all(array('is_active' => 1)),
            'fakturs' => $this->faktur_pembelian_model->get_all(),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
        );
        
        $this->render('pembelian/retur_pembelian/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('pembelian', 'view');
        
        $params = array(
            'columns' => array('rp.no_retur', 'rp.tanggal', 'rp.subtotal', 'rp.status'),
            'search_fields' => array('rp.no_retur', 's.nama', 'fp.no_faktur')
        );
        
        $result = $this->retur_pembelian_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'sent' ? 'success' : ($row->status == 'cancelled' ? 'danger' : 'secondary')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_retur,
                $row->no_faktur ? $row->no_faktur : '-',
                $row->supplier_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                number_format($row->subtotal, 0, ',', '.'),
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
            $buttons .= '<button class="btn btn-sm btn-warning" onclick="sendData(' . $id . ')" title="Konfirmasi & Kembalikan"><i class="fas fa-paper-plane"></i></button> ';
        }
        return $buttons;
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('pembelian', 'update');
            $existing = $this->retur_pembelian_model->get_by_id($id);
            if (!$existing || $existing->status != 'draft') {
                $this->ajax_response(false, 'Hanya retur berstatus draft yang dapat diubah');
                return;
            }
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
            $this->ajax_response(false, 'Item retur tidak boleh kosong');
            return;
        }
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ($item['qty'] * $item['harga']);
        }
        
        $data = array(
            'no_retur' => $id ? $this->input->post('no_retur') : $this->retur_pembelian_model->generate_no_retur(),
            'faktur_pembelian_id' => $this->input->post('faktur_pembelian_id') ?: null,
            'supplier_id' => $this->input->post('supplier_id'),
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'subtotal' => $subtotal,
            'status' => $this->input->post('status') ?: 'draft',
            'keterangan' => $this->input->post('keterangan')
        );
        
        $this->db->trans_start();
        
        if ($id) {
            $this->retur_pembelian_model->update($id, $data);
            $this->db->where('retur_id', $id)->delete('retur_pembelian_detail');
        } else {
            $id = $this->retur_pembelian_model->insert($data);
        }
        
        foreach ($items as $item) {
            $detail_data = array(
                'retur_id' => $id,
                'produk_id' => $item['produk_id'],
                'qty' => $item['qty'],
                'harga' => $item['harga'],
                'subtotal' => ($item['qty'] * $item['harga'])
            );
            $this->retur_pembelian_detail_model->insert($detail_data);
        }
        
        // If status is saved as sent, deduct stock right away
        if (isset($data['status']) && $data['status'] == 'sent') {
            $this->retur_pembelian_model->send($id);
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
        $retur = $this->retur_pembelian_model->get_by_id_with_relations($id);
        if ($retur) {
            $details = $this->retur_pembelian_model->get_details($id);
            $this->ajax_response(true, 'Data berhasil diambil', array(
                'retur' => $retur,
                'details' => $details
            ));
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_send($id) {
        $this->check_permission('pembelian', 'update');
        if ($this->retur_pembelian_model->send($id)) {
            $this->ajax_response(true, 'Retur pembelian berhasil dikonfirmasi dan stok telah dikurangi');
        } else {
            $this->ajax_response(false, 'Gagal mengonfirmasi retur pembelian');
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PesananPenjualan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('pesanan_penjualan_model');
        $this->load->model('pesanan_penjualan_detail_model');
        $this->load->model('pelanggan_model');
        $this->load->model('sales_model');
        $this->load->model('master_produk_model');
        $this->check_permission('penjualan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Pesanan Penjualan',
            'breadcrumb' => array(
                array('label' => 'Penjualan', 'url' => '#'),
                array('label' => 'Pesanan Penjualan')
            ),
            'pelanggans' => $this->pelanggan_model->get_all(array('is_active' => 1)),
            'sales' => $this->sales_model->get_all(array('is_active' => 1))
        );
        
        $this->render('penjualan/pesanan_penjualan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('penjualan', 'view');
        
        $params = array(
            'columns' => array('pp.no_pesanan', 'pp.tanggal', 'pp.status'),
            'search_fields' => array('pp.no_pesanan', 'pl.nama')
        );
        
        $result = $this->pesanan_penjualan_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'cancelled' ? 'danger' : ($row->status == 'process' ? 'warning' : ($row->status == 'confirmed' ? 'info' : 'secondary')))) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_pesanan,
                $row->pelanggan_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                $row->tanggal_kirim ? date('d/m/Y', strtotime($row->tanggal_kirim)) : '-',
                $status_badge,
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($this->auth->has_permission('penjualan', 'view')) {
            $buttons .= '<button class="btn btn-sm btn-info" onclick="viewData(' . $id . ')"><i class="fas fa-eye"></i></button> ';
        }
        if ($status == 'draft' && $this->auth->has_permission('penjualan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-warning" onclick="editData(' . $id . ')"><i class="fas fa-edit"></i></button> ';
            $buttons .= '<button class="btn btn-sm btn-success" onclick="confirmData(' . $id . ')"><i class="fas fa-check"></i></button>';
        }
        return $buttons;
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('penjualan', 'update');
        } else {
            $this->check_permission('penjualan', 'create');
        }
        
        $this->form_validation->set_rules('pelanggan_id', 'Pelanggan', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'no_pesanan' => $id ? $this->input->post('no_pesanan') : $this->pesanan_penjualan_model->generate_no_pesanan(),
            'pelanggan_id' => $this->input->post('pelanggan_id'),
            'sales_id' => $this->input->post('sales_id') ?: null,
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'tanggal_kirim' => $this->input->post('tanggal_kirim') ?: null,
            'status' => $this->input->post('status') ?: 'draft',
            'keterangan' => $this->input->post('keterangan')
        );
        
        if ($id) {
            if ($this->pesanan_penjualan_model->update($id, $data)) {
                // Update details
                $items = json_decode($this->input->post('items'), true);
                if ($items) {
                    // Delete old details
                    $this->db->where('pesanan_id', $id)->delete('pesanan_penjualan_detail');
                    // Insert new details
                    foreach ($items as $item) {
                        $detail_data = array(
                            'pesanan_id' => $id,
                            'produk_id' => $item['produk_id'],
                            'qty' => $item['qty'],
                            'harga' => $item['harga'],
                            'subtotal' => $item['qty'] * $item['harga']
                        );
                        $this->pesanan_penjualan_detail_model->insert($detail_data);
                    }
                }
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->pesanan_penjualan_model->insert($data);
            if ($insert_id) {
                // Insert details
                $items = json_decode($this->input->post('items'), true);
                if ($items) {
                    foreach ($items as $item) {
                        $detail_data = array(
                            'pesanan_id' => $insert_id,
                            'produk_id' => $item['produk_id'],
                            'qty' => $item['qty'],
                            'harga' => $item['harga'],
                            'subtotal' => $item['qty'] * $item['harga']
                        );
                        $this->pesanan_penjualan_detail_model->insert($detail_data);
                    }
                }
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_get($id) {
        $this->check_permission('penjualan', 'view');
        $pesanan = $this->pesanan_penjualan_model->get_by_id_with_relations($id);
        if ($pesanan) {
            $details = $this->pesanan_penjualan_model->get_details($id);
            $this->ajax_response(true, 'Data berhasil diambil', array(
                'pesanan' => $pesanan,
                'details' => $details
            ));
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_confirm($id) {
        $this->check_permission('penjualan', 'update');
        if ($this->pesanan_penjualan_model->update($id, array('status' => 'confirmed'))) {
            $this->ajax_response(true, 'Pesanan berhasil dikonfirmasi');
        } else {
            $this->ajax_response(false, 'Gagal mengkonfirmasi pesanan');
        }
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PerpindahanStok extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('perpindahan_stok_model');
        $this->load->model('perpindahan_stok_detail_model');
        $this->load->model('master_gudang_model');
        $this->load->model('master_produk_model');
        $this->check_permission('persediaan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Perpindahan Stok',
            'breadcrumb' => array(
                array('label' => 'Persediaan', 'url' => '#'),
                array('label' => 'Perpindahan Stok')
            ),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('persediaan/perpindahan_stok/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('persediaan', 'view');
        
        $params = array(
            'columns' => array('ps.tanggal', 'ps.status'),
            'search_fields' => array('ps.no_mutasi', 'mga.nama', 'mgt.nama')
        );
        
        $result = $this->perpindahan_stok_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'cancelled' ? 'danger' : ($row->status == 'process' ? 'warning' : 'secondary'))) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_mutasi,
                $row->gudang_asal_nama,
                $row->gudang_tujuan_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                $status_badge,
                isset($row->keterangan) ? $row->keterangan : '-',
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($status == 'process' && $this->auth->has_permission('persediaan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="completeMutasi(' . $id . ')"><i class="fas fa-check"></i> Selesai</button>';
        }
        return $buttons;
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('persediaan', 'update');
        } else {
            $this->check_permission('persediaan', 'create');
        }
        
        $this->form_validation->set_rules('gudang_asal_id', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('gudang_tujuan_id', 'Gudang Tujuan', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'no_mutasi' => $id ? $this->input->post('no_mutasi') : $this->perpindahan_stok_model->generate_no_mutasi(),
            'gudang_asal_id' => $this->input->post('gudang_asal_id'),
            'gudang_tujuan_id' => $this->input->post('gudang_tujuan_id'),
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'status' => $this->input->post('status') ?: 'draft',
            'keterangan' => $this->input->post('keterangan')
        );
        
        if ($id) {
            if ($this->perpindahan_stok_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->perpindahan_stok_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_complete($id) {
        $this->check_permission('persediaan', 'update');
        
        if ($this->perpindahan_stok_model->complete($id)) {
            $this->ajax_response(true, 'Perpindahan stok berhasil diselesaikan');
        } else {
            $this->ajax_response(false, 'Gagal menyelesaikan perpindahan stok');
        }
    }
}


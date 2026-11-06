<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Defecta extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('defecta_model');
        $this->load->model('master_produk_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('persediaan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Defecta',
            'breadcrumb' => array(
                array('label' => 'Persediaan', 'url' => '#'),
                array('label' => 'Defecta')
            ),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('persediaan/defecta/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('persediaan', 'view');
        
        $params = array(
            'columns' => array('d.tanggal', 'd.qty', 'd.status', 'd.keterangan'),
            'search_fields' => array('mp.nama', 'mp.kode', 'd.keterangan')
        );
        
        $result = $this->defecta_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'approved' ? 'success' : ($row->status == 'rejected' ? 'danger' : 'warning')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                date('d/m/Y', strtotime($row->tanggal)),
                $row->produk_kode . ' - ' . $row->produk_nama,
                $row->gudang_nama,
                number_format($row->qty, 0, ',', '.'),
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
        if ($status == 'pending' && $this->auth->has_permission('persediaan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="approveData(' . $id . ')"><i class="fas fa-check"></i></button> ';
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="rejectData(' . $id . ')"><i class="fas fa-times"></i></button>';
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
        
        $this->form_validation->set_rules('produk_id', 'Produk', 'required');
        $this->form_validation->set_rules('gudang_id', 'Gudang', 'required');
        $this->form_validation->set_rules('qty', 'Qty', 'required|numeric|greater_than[0]');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'produk_id' => $this->input->post('produk_id'),
            'gudang_id' => $this->input->post('gudang_id'),
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'qty' => $this->input->post('qty'),
            'keterangan' => $this->input->post('keterangan'),
            'status' => 'pending'
        );
        
        if ($id) {
            if ($this->defecta_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->defecta_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_approve($id) {
        $this->check_permission('persediaan', 'update');
        
        if ($this->defecta_model->approve($id)) {
            $this->ajax_response(true, 'Defecta berhasil disetujui');
        } else {
            $this->ajax_response(false, 'Gagal menyetujui defecta');
        }
    }
}


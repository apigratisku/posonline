<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PenerimaanResep extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penerimaan_resep_model');
        $this->load->model('penerimaan_resep_detail_model');
        $this->load->model('kunjungan_pasien_model');
        $this->load->model('dokter_model');
        $this->load->model('master_produk_model');
        $this->check_permission('pelayanan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Penerimaan Resep',
            'breadcrumb' => array(
                array('label' => 'Pelayanan', 'url' => '#'),
                array('label' => 'Penerimaan Resep')
            ),
            'dokter_list' => $this->dokter_model->get_all(array('is_active' => 1))
        );
        
        $this->render('pelayanan/penerimaan_resep/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('pelayanan', 'view');
        
        $params = array(
            'columns' => array('pr.no_resep', 'pr.tanggal', 'pr.status'),
            'search_fields' => array('pr.no_resep', 'pl.nama', 'd.nama')
        );
        
        $result = $this->penerimaan_resep_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'process' ? 'warning' : ($row->status == 'cancelled' ? 'danger' : 'secondary'))) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_resep,
                $row->pelanggan_nama,
                $row->dokter_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                $status_badge,
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($this->auth->has_permission('pelayanan', 'view')) {
            $buttons .= '<button class="btn btn-sm btn-info" onclick="viewData(' . $id . ')"><i class="fas fa-eye"></i></button> ';
        }
        if ($status != 'completed' && $this->auth->has_permission('pelayanan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-warning" onclick="editData(' . $id . ')"><i class="fas fa-edit"></i></button> ';
        }
        return $buttons;
    }
    
    public function ajax_get($id) {
        $this->check_permission('pelayanan', 'view');
        $data = $this->penerimaan_resep_model->get_by_id_with_relations($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('pelayanan', 'update');
        } else {
            $this->check_permission('pelayanan', 'create');
        }
        
        $this->form_validation->set_rules('kunjungan_id', 'Kunjungan', 'required');
        $this->form_validation->set_rules('dokter_id', 'Dokter', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $this->db->trans_start();
        
        $data = array(
            'kunjungan_id' => $this->input->post('kunjungan_id'),
            'dokter_id' => $this->input->post('dokter_id'),
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'status' => $this->input->post('status') ?: 'pending',
            'keterangan' => $this->input->post('keterangan')
        );
        
        if (!$id) {
            $data['no_resep'] = $this->penerimaan_resep_model->generate_no_resep();
        }
        
        if ($id) {
            $this->penerimaan_resep_model->update($id, $data);
            $resep_id = $id;
        } else {
            $resep_id = $this->penerimaan_resep_model->insert($data);
        }
        
        // Save details
        $details = json_decode($this->input->post('details'), true);
        if ($details) {
            $this->penerimaan_resep_detail_model->save_details($resep_id, $details);
        }
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->ajax_response(false, 'Gagal menyimpan data');
        } else {
            $this->ajax_response(true, 'Data berhasil disimpan', array('id' => $resep_id));
        }
    }
}


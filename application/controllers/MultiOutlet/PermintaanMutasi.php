<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PermintaanMutasi extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('permintaan_mutasi_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('outlet', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Permintaan Mutasi',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Permintaan Mutasi')
            ),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        $this->render('multi_outlet/permintaan_mutasi/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('outlet', 'view');
        $params = array(
            'columns' => array('id', 'no_permintaan', 'gudang_asal', 'gudang_tujuan', 'tanggal', 'status', 'created_at'),
            'search_fields' => array('no_permintaan')
        );
        $result = $this->permintaan_mutasi_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = 'secondary';
            switch($row->status) {
                case 'draft': $status_badge = 'secondary'; break;
                case 'diproses': $status_badge = 'warning'; break;
                case 'selesai': $status_badge = 'success'; break;
                case 'ditolak': $status_badge = 'danger'; break;
            }
            $data[] = array(
                $row->id,
                $row->no_permintaan,
                $row->gudang_asal,
                $row->gudang_tujuan,
                date('d/m/Y', strtotime($row->tanggal)),
                '<span class="badge bg-' . $status_badge . '">' . ucfirst($row->status) . '</span>',
                date('d/m/Y H:i', strtotime($row->created_at ?: 'now')),
                $this->get_action_buttons($row->id)
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('outlet', 'view')) {
            $buttons .= '<a href="' . base_url('outlet/permintaan/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'update')) {
            $buttons .= '<a href="' . base_url('outlet/permintaan/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('outlet', 'create');
        $data = array(
            'title' => 'Tambah Permintaan Mutasi',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Permintaan Mutasi', 'url' => base_url('outlet/permintaan')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Permintaan Mutasi',
            'form_action' => base_url('outlet/permintaan/store'),
            'back_url' => 'outlet/permintaan',
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        $this->render('multi_outlet/permintaan_mutasi/create', $data);
    }
    
    public function store() {
        $this->check_permission('outlet', 'create');
        $this->form_validation->set_rules('gudang_asal_id', 'Gudang Asal', 'required');
        $this->form_validation->set_rules('gudang_tujuan_id', 'Gudang Tujuan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Permintaan Mutasi',
                'breadcrumb' => array(
                    array('label' => 'Multi Outlet', 'url' => '#'),
                    array('label' => 'Permintaan Mutasi', 'url' => base_url('outlet/permintaan')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Permintaan Mutasi',
                'form_action' => base_url('outlet/permintaan/store'),
                'back_url' => 'outlet/permintaan',
                'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
            );
            $this->render('multi_outlet/permintaan_mutasi/create', $data);
            return;
        }
        
        // Generate no_permintaan
        $no_permintaan = 'PM-' . date('Ymd') . '-' . str_pad($this->permintaan_mutasi_model->get_next_number(), 4, '0', STR_PAD_LEFT);
        
        $data = array(
            'no_permintaan' => $no_permintaan,
            'gudang_asal_id' => $this->input->post('gudang_asal_id'),
            'gudang_tujuan_id' => $this->input->post('gudang_tujuan_id'),
            'tanggal' => $this->input->post('tanggal'),
            'keterangan' => $this->input->post('keterangan'),
            'status' => 'draft'
        );
        
        $insert_id = $this->permintaan_mutasi_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('outlet/permintaan');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('outlet/permintaan/create');
        }
    }
    
    public function view($id) {
        $this->check_permission('outlet', 'view');
        $data = $this->permintaan_mutasi_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('outlet/permintaan');
            return;
        }
        $view_data = array(
            'title' => 'Detail Permintaan Mutasi',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Permintaan Mutasi', 'url' => base_url('outlet/permintaan')),
                array('label' => 'Detail')
            ),
            'back_url' => 'outlet/permintaan',
            'data' => $data
        );
        $this->render('multi_outlet/permintaan_mutasi/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('outlet', 'view');
        $data = $this->permintaan_mutasi_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('outlet', 'delete');
        if ($this->permintaan_mutasi_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


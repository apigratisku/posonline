<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OutletMitra extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('outlet_mitra_model');
        $this->check_permission('outlet', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Outlet Mitra',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Outlet Mitra')
            )
        );
        $this->render('multi_outlet/outlet_mitra/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('outlet', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'alamat', 'telepon', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'alamat')
        );
        $result = $this->outlet_mitra_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->alamat ?: '-',
                $row->telepon ?: '-',
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
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
            $buttons .= '<a href="' . base_url('outlet/outlet-mitra/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'update')) {
            $buttons .= '<a href="' . base_url('outlet/outlet-mitra/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('outlet', 'create');
        $data = array(
            'title' => 'Tambah Outlet Mitra',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Outlet Mitra', 'url' => base_url('outlet/outlet-mitra')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Outlet Mitra',
            'form_action' => base_url('outlet/outlet-mitra/store'),
            'back_url' => 'outlet/outlet-mitra'
        );
        $this->render('multi_outlet/outlet_mitra/create', $data);
    }
    
    public function store() {
        $this->check_permission('outlet', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Outlet Mitra',
                'breadcrumb' => array(
                    array('label' => 'Multi Outlet', 'url' => '#'),
                    array('label' => 'Outlet Mitra', 'url' => base_url('outlet/outlet-mitra')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Outlet Mitra',
                'form_action' => base_url('outlet/outlet-mitra/store'),
                'back_url' => 'outlet/outlet-mitra'
            );
            $this->render('multi_outlet/outlet_mitra/create', $data);
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->outlet_mitra_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('outlet/outlet-mitra');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('outlet/outlet-mitra/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('outlet', 'update');
        $data = $this->outlet_mitra_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('outlet/outlet-mitra');
            return;
        }
        $view_data = array(
            'title' => 'Edit Outlet Mitra',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Outlet Mitra', 'url' => base_url('outlet/outlet-mitra')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Outlet Mitra',
            'form_action' => base_url('outlet/outlet-mitra/update/' . $id),
            'back_url' => 'outlet/outlet-mitra',
            'data' => $data
        );
        $this->render('multi_outlet/outlet_mitra/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('outlet', 'update');
        $data = $this->outlet_mitra_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('outlet/outlet-mitra');
            return;
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Outlet Mitra',
                'breadcrumb' => array(
                    array('label' => 'Multi Outlet', 'url' => '#'),
                    array('label' => 'Outlet Mitra', 'url' => base_url('outlet/outlet-mitra')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Outlet Mitra',
                'form_action' => base_url('outlet/outlet-mitra/update/' . $id),
                'back_url' => 'outlet/outlet-mitra',
                'data' => $data
            );
            $this->render('multi_outlet/outlet_mitra/edit', $view_data);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->outlet_mitra_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('outlet/outlet-mitra');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('outlet/outlet-mitra/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('outlet', 'view');
        $data = $this->outlet_mitra_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('outlet/outlet-mitra');
            return;
        }
        $view_data = array(
            'title' => 'Detail Outlet Mitra',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Outlet Mitra', 'url' => base_url('outlet/outlet-mitra')),
                array('label' => 'Detail')
            ),
            'back_url' => 'outlet/outlet-mitra',
            'data' => $data
        );
        $this->render('multi_outlet/outlet_mitra/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('outlet', 'view');
        $data = $this->outlet_mitra_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('outlet', 'update');
        } else {
            $this->check_permission('outlet', 'create');
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->outlet_mitra_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->outlet_mitra_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('outlet', 'delete');
        if ($this->outlet_mitra_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


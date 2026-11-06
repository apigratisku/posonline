<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MemberPoin extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('member_poin_model');
        $this->check_permission('promo', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Program Member Poin',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Member Poin')
            )
        );
        $this->render('program_promo/member_poin/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('promo', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'poin_per_rupiah', 'rupiah_per_poin', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama')
        );
        $result = $this->member_poin_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                number_format($row->poin_per_rupiah ?: 0, 0, ',', '.'),
                'Rp ' . number_format($row->rupiah_per_poin ?: 0, 0, ',', '.'),
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
        if ($this->auth->has_permission('promo', 'view')) {
            $buttons .= '<a href="' . base_url('promo/memberpoin/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('promo', 'update')) {
            $buttons .= '<a href="' . base_url('promo/memberpoin/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('promo', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('promo', 'create');
        $data = array(
            'title' => 'Tambah Program Member Poin',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Member Poin', 'url' => base_url('promo/memberpoin')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Program Member Poin',
            'form_action' => base_url('promo/memberpoin/store'),
            'back_url' => 'promo/memberpoin'
        );
        $this->render('program_promo/member_poin/create', $data);
    }
    
    public function store() {
        $this->check_permission('promo', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Program Member Poin',
                'breadcrumb' => array(
                    array('label' => 'Program Promo', 'url' => '#'),
                    array('label' => 'Member Poin', 'url' => base_url('promo/memberpoin')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Program Member Poin',
                'form_action' => base_url('promo/memberpoin/store'),
                'back_url' => 'promo/memberpoin'
            );
            $this->render('program_promo/member_poin/create', $data);
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'poin_per_rupiah' => $this->input->post('poin_per_rupiah') ?: 0,
            'rupiah_per_poin' => $this->input->post('rupiah_per_poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->member_poin_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('promo/memberpoin');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('promo/memberpoin/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('promo', 'update');
        $data = $this->member_poin_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/memberpoin');
            return;
        }
        $view_data = array(
            'title' => 'Edit Program Member Poin',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Member Poin', 'url' => base_url('promo/memberpoin')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Program Member Poin',
            'form_action' => base_url('promo/memberpoin/update/' . $id),
            'back_url' => 'promo/memberpoin',
            'data' => $data
        );
        $this->render('program_promo/member_poin/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('promo', 'update');
        $data = $this->member_poin_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/memberpoin');
            return;
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Program Member Poin',
                'breadcrumb' => array(
                    array('label' => 'Program Promo', 'url' => '#'),
                    array('label' => 'Member Poin', 'url' => base_url('promo/memberpoin')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Program Member Poin',
                'form_action' => base_url('promo/memberpoin/update/' . $id),
                'back_url' => 'promo/memberpoin',
                'data' => $data
            );
            $this->render('program_promo/member_poin/edit', $view_data);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'poin_per_rupiah' => $this->input->post('poin_per_rupiah') ?: 0,
            'rupiah_per_poin' => $this->input->post('rupiah_per_poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->member_poin_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('promo/memberpoin');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('promo/memberpoin/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('promo', 'view');
        $data = $this->member_poin_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/memberpoin');
            return;
        }
        $view_data = array(
            'title' => 'Detail Program Member Poin',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Member Poin', 'url' => base_url('promo/memberpoin')),
                array('label' => 'Detail')
            ),
            'back_url' => 'promo/memberpoin',
            'data' => $data
        );
        $this->render('program_promo/member_poin/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('promo', 'view');
        $data = $this->member_poin_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('promo', 'update');
        } else {
            $this->check_permission('promo', 'create');
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
            'poin_per_rupiah' => $this->input->post('poin_per_rupiah') ?: 0,
            'rupiah_per_poin' => $this->input->post('rupiah_per_poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->member_poin_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->member_poin_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('promo', 'delete');
        if ($this->member_poin_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


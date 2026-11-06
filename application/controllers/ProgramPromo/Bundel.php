<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bundel extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('promo_bundel_model');
        $this->load->model('master_produk_model');
        $this->check_permission('promo', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Program Promo Bundel',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Bundel')
            )
        );
        $this->render('program_promo/bundel/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('promo', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'tanggal_mulai', 'tanggal_selesai', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'deskripsi')
        );
        $result = $this->promo_bundel_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                date('d/m/Y', strtotime($row->tanggal_mulai)),
                date('d/m/Y', strtotime($row->tanggal_selesai)),
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
            $buttons .= '<a href="' . base_url('promo/bundel/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('promo', 'update')) {
            $buttons .= '<a href="' . base_url('promo/bundel/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('promo', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('promo', 'create');
        $data = array(
            'title' => 'Tambah Promo Bundel',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Bundel', 'url' => base_url('promo/bundel')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Promo Bundel',
            'form_action' => base_url('promo/bundel/store'),
            'back_url' => 'promo/bundel',
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
        );
        $this->render('program_promo/bundel/create', $data);
    }
    
    public function store() {
        $this->check_permission('promo', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'required');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Promo Bundel',
                'breadcrumb' => array(
                    array('label' => 'Program Promo', 'url' => '#'),
                    array('label' => 'Bundel', 'url' => base_url('promo/bundel')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Promo Bundel',
                'form_action' => base_url('promo/bundel/store'),
                'back_url' => 'promo/bundel',
                'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
            );
            $this->render('program_promo/bundel/create', $data);
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'harga_bundel' => $this->input->post('harga_bundel') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->promo_bundel_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('promo/bundel');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('promo/bundel/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('promo', 'update');
        $data = $this->promo_bundel_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/bundel');
            return;
        }
        $view_data = array(
            'title' => 'Edit Promo Bundel',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Bundel', 'url' => base_url('promo/bundel')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Promo Bundel',
            'form_action' => base_url('promo/bundel/update/' . $id),
            'back_url' => 'promo/bundel',
            'data' => $data,
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
        );
        $this->render('program_promo/bundel/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('promo', 'update');
        $data = $this->promo_bundel_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/bundel');
            return;
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'required');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Promo Bundel',
                'breadcrumb' => array(
                    array('label' => 'Program Promo', 'url' => '#'),
                    array('label' => 'Bundel', 'url' => base_url('promo/bundel')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Promo Bundel',
                'form_action' => base_url('promo/bundel/update/' . $id),
                'back_url' => 'promo/bundel',
                'data' => $data,
                'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
            );
            $this->render('program_promo/bundel/edit', $view_data);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'harga_bundel' => $this->input->post('harga_bundel') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->promo_bundel_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('promo/bundel');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('promo/bundel/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('promo', 'view');
        $data = $this->promo_bundel_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/bundel');
            return;
        }
        $view_data = array(
            'title' => 'Detail Promo Bundel',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Bundel', 'url' => base_url('promo/bundel')),
                array('label' => 'Detail')
            ),
            'back_url' => 'promo/bundel',
            'data' => $data
        );
        $this->render('program_promo/bundel/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('promo', 'view');
        $data = $this->promo_bundel_model->get_by_id($id);
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
            'deskripsi' => $this->input->post('deskripsi'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'harga_bundel' => $this->input->post('harga_bundel') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->promo_bundel_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->promo_bundel_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('promo', 'delete');
        if ($this->promo_bundel_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


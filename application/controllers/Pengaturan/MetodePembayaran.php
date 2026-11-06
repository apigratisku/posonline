<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MetodePembayaran extends MY_Controller {

    private $url_prefix = 'pengaturan/metode-pembayaran';
    
    public function __construct() {
        parent::__construct();
        $this->load->model('metode_pembayaran_model');
        $this->check_permission('pengaturan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Metode Pembayaran',
            'breadcrumb' => array(
                array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                array('label' => 'Metode Pembayaran')
            )
        );
        $this->render('pengaturan/metode_pembayaran/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('pengaturan', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'tipe', 'is_active'),
            'search_fields' => array('kode', 'nama', 'tipe')
        );
        $result = $this->metode_pembayaran_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                htmlspecialchars($row->tipe ?: '-'),
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                $this->get_action_buttons($row->id)
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('pengaturan', 'view')) {
            $buttons .= '<a href="' . base_url($this->url_prefix . '/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('pengaturan', 'update')) {
            $buttons .= '<a href="' . base_url($this->url_prefix . '/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('pengaturan', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('pengaturan', 'create');
        $data = array(
            'title' => 'Tambah Metode Pembayaran',
            'breadcrumb' => array(
                array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                array('label' => 'Metode Pembayaran', 'url' => base_url($this->url_prefix)),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Metode Pembayaran',
            'form_action' => base_url($this->url_prefix . '/store'),
            'back_url' => $this->url_prefix
        );
        $this->render('pengaturan/metode_pembayaran/create', $data);
    }
    
    public function store() {
        $this->check_permission('pengaturan', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Metode Pembayaran',
                'breadcrumb' => array(
                    array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                    array('label' => 'Metode Pembayaran', 'url' => base_url($this->url_prefix)),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Metode Pembayaran',
                'form_action' => base_url($this->url_prefix . '/store'),
                'back_url' => $this->url_prefix
            );
            $this->render('pengaturan/metode_pembayaran/create', $data);
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->metode_pembayaran_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect($this->url_prefix);
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect($this->url_prefix . '/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('pengaturan', 'update');
        $data = $this->metode_pembayaran_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect($this->url_prefix);
            return;
        }
        $view_data = array(
            'title' => 'Edit Metode Pembayaran',
            'breadcrumb' => array(
                array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                array('label' => 'Metode Pembayaran', 'url' => base_url($this->url_prefix)),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Metode Pembayaran',
            'form_action' => base_url($this->url_prefix . '/update/' . $id),
            'back_url' => $this->url_prefix,
            'data' => $data
        );
        $this->render('pengaturan/metode_pembayaran/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('pengaturan', 'update');
        $data = $this->metode_pembayaran_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect($this->url_prefix);
            return;
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Metode Pembayaran',
                'breadcrumb' => array(
                    array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                    array('label' => 'Metode Pembayaran', 'url' => base_url($this->url_prefix)),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Metode Pembayaran',
                'form_action' => base_url($this->url_prefix . '/update/' . $id),
                'back_url' => $this->url_prefix,
                'data' => $data
            );
            $this->render('pengaturan/metode_pembayaran/edit', $view_data);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->metode_pembayaran_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect($this->url_prefix);
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect($this->url_prefix . '/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('pengaturan', 'view');
        $data = $this->metode_pembayaran_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect($this->url_prefix);
            return;
        }
        $view_data = array(
            'title' => 'Detail Metode Pembayaran',
            'breadcrumb' => array(
                array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                array('label' => 'Metode Pembayaran', 'url' => base_url($this->url_prefix)),
                array('label' => 'Detail')
            ),
            'back_url' => $this->url_prefix,
            'data' => $data
        );
        $this->render('pengaturan/metode_pembayaran/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('pengaturan', 'view');
        $data = $this->metode_pembayaran_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('pengaturan', 'update');
        } else {
            $this->check_permission('pengaturan', 'create');
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
            'tipe' => $this->input->post('tipe'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->metode_pembayaran_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->metode_pembayaran_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('pengaturan', 'delete');
        if ($this->metode_pembayaran_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dokter extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('dokter_model');
        $this->check_permission('kontak', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Dokter',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Dokter')
            )
        );
        
        $this->render('kontak/dokter/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('kontak', 'view');
        
        $params = array(
            'columns' => array('kode', 'nama', 'spesialisasi', 'no_sip', 'telepon', 'is_active'),
            'search_fields' => array('kode', 'nama', 'spesialisasi', 'no_sip')
        );
        
        $result = $this->dokter_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->kode,
                $row->nama,
                isset($row->spesialisasi) ? $row->spesialisasi : '-',
                isset($row->no_sip) ? $row->no_sip : '-',
                isset($row->telepon) ? $row->telepon : '-',
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('kontak', 'view')) {
            $buttons .= '<a href="' . base_url('kontak/dokter/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('kontak', 'update')) {
            $buttons .= '<a href="' . base_url('kontak/dokter/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('kontak', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('kontak', 'create');
        $data = array(
            'title' => 'Tambah Dokter',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Dokter', 'url' => base_url('kontak/dokter')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Dokter',
            'form_action' => base_url('kontak/dokter/store'),
            'back_url' => 'kontak/dokter'
        );
        $this->render('kontak/dokter/create', $data);
    }
    
    public function store() {
        $this->check_permission('kontak', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Dokter',
                'breadcrumb' => array(
                    array('label' => 'Kontak', 'url' => '#'),
                    array('label' => 'Dokter', 'url' => base_url('kontak/dokter')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Dokter',
                'form_action' => base_url('kontak/dokter/store'),
                'back_url' => 'kontak/dokter'
            );
            $this->render('kontak/dokter/create', $data);
            return;
        }
        if ($this->dokter_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('kontak/dokter/create');
            return;
        }
        $no_sip = $this->input->post('no_sip');
        if (!empty($no_sip) && $this->dokter_model->no_sip_exists($no_sip)) {
            $this->set_flash('error', 'No SIP sudah digunakan');
            redirect('kontak/dokter/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'spesialisasi' => $this->input->post('spesialisasi'),
            'no_sip' => $no_sip,
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->dokter_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('kontak/dokter');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('kontak/dokter/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('kontak', 'update');
        $data = $this->dokter_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/dokter');
            return;
        }
        $view_data = array(
            'title' => 'Edit Dokter',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Dokter', 'url' => base_url('kontak/dokter')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Dokter',
            'form_action' => base_url('kontak/dokter/update/' . $id),
            'back_url' => 'kontak/dokter',
            'data' => $data
        );
        $this->render('kontak/dokter/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('kontak', 'update');
        $data = $this->dokter_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/dokter');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Dokter',
                'breadcrumb' => array(
                    array('label' => 'Kontak', 'url' => '#'),
                    array('label' => 'Dokter', 'url' => base_url('kontak/dokter')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Dokter',
                'form_action' => base_url('kontak/dokter/update/' . $id),
                'back_url' => 'kontak/dokter',
                'data' => $data
            );
            $this->render('kontak/dokter/edit', $view_data);
            return;
        }
        if ($this->dokter_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('kontak/dokter/edit/' . $id);
            return;
        }
        $no_sip = $this->input->post('no_sip');
        if (!empty($no_sip) && $this->dokter_model->no_sip_exists($no_sip, $id)) {
            $this->set_flash('error', 'No SIP sudah digunakan');
            redirect('kontak/dokter/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'spesialisasi' => $this->input->post('spesialisasi'),
            'no_sip' => $no_sip,
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->dokter_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('kontak/dokter');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('kontak/dokter/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('kontak', 'view');
        $data = $this->dokter_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/dokter');
            return;
        }
        $view_data = array(
            'title' => 'Detail Dokter',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Dokter', 'url' => base_url('kontak/dokter')),
                array('label' => 'Detail')
            ),
            'back_url' => 'kontak/dokter',
            'data' => $data
        );
        $this->render('kontak/dokter/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('kontak', 'view');
        $data = $this->dokter_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('kontak', 'update');
        } else {
            $this->check_permission('kontak', 'create');
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        if ($this->dokter_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $no_sip = $this->input->post('no_sip');
        if (!empty($no_sip) && $this->dokter_model->no_sip_exists($no_sip, $id)) {
            $this->ajax_response(false, 'No SIP sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'spesialisasi' => $this->input->post('spesialisasi'),
            'no_sip' => $no_sip,
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->dokter_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->dokter_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('kontak', 'delete');
        if ($this->dokter_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


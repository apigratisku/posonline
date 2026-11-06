<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pelanggan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('pelanggan_model');
        $this->load->model('master_membership_model');
        $this->check_permission('kontak', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Pelanggan',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Pelanggan')
            ),
            'memberships' => $this->master_membership_model->get_all(array('is_active' => 1))
        );
        
        $this->render('kontak/pelanggan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('kontak', 'view');
        
        $params = array(
            'columns' => array('p.kode', 'p.nama', 'p.telepon', 'p.email', 'p.poin', 'p.is_active'),
            'search_fields' => array('p.kode', 'p.nama', 'p.telepon', 'p.email')
        );
        
        $result = $this->pelanggan_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->kode,
                $row->nama,
                isset($row->telepon) ? $row->telepon : '-',
                isset($row->email) ? $row->email : '-',
                isset($row->membership_nama) ? $row->membership_nama : '-',
                number_format($row->poin, 0, ',', '.'),
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
            $buttons .= '<a href="' . base_url('kontak/pelanggan/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('kontak', 'update')) {
            $buttons .= '<a href="' . base_url('kontak/pelanggan/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('kontak', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    /**
     * Create - Show create form
     */
    public function create() {
        $this->check_permission('kontak', 'create');
        
        $data = array(
            'title' => 'Tambah Pelanggan',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Pelanggan', 'url' => base_url('kontak/pelanggan')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Pelanggan',
            'form_action' => base_url('kontak/pelanggan/store'),
            'back_url' => 'kontak/pelanggan',
            'memberships' => $this->master_membership_model->get_all(array('is_active' => 1))
        );
        
        $this->render('kontak/pelanggan/create', $data);
    }
    
    /**
     * Store - Save new record
     */
    public function store() {
        $this->check_permission('kontak', 'create');
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Pelanggan',
                'breadcrumb' => array(
                    array('label' => 'Kontak', 'url' => '#'),
                    array('label' => 'Pelanggan', 'url' => base_url('kontak/pelanggan')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Pelanggan',
                'form_action' => base_url('kontak/pelanggan/store'),
                'back_url' => 'kontak/pelanggan',
                'memberships' => $this->master_membership_model->get_all(array('is_active' => 1))
            );
            $this->render('kontak/pelanggan/create', $data);
            return;
        }
        
        if ($this->pelanggan_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('kontak/pelanggan/create');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'tanggal_lahir' => $this->input->post('tanggal_lahir') ?: null,
            'jenis_kelamin' => $this->input->post('jenis_kelamin') ?: null,
            'membership_id' => $this->input->post('membership_id') ?: null,
            'poin' => $this->input->post('poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->pelanggan_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('kontak/pelanggan');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('kontak/pelanggan/create');
        }
    }
    
    /**
     * Edit - Show edit form
     */
    public function edit($id) {
        $this->check_permission('kontak', 'update');
        
        $data = $this->pelanggan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/pelanggan');
            return;
        }
        
        $view_data = array(
            'title' => 'Edit Pelanggan',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Pelanggan', 'url' => base_url('kontak/pelanggan')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Pelanggan',
            'form_action' => base_url('kontak/pelanggan/update/' . $id),
            'back_url' => 'kontak/pelanggan',
            'data' => $data,
            'memberships' => $this->master_membership_model->get_all(array('is_active' => 1))
        );
        
        $this->render('kontak/pelanggan/edit', $view_data);
    }
    
    /**
     * Update - Update existing record
     */
    public function update($id) {
        $this->check_permission('kontak', 'update');
        
        $data = $this->pelanggan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/pelanggan');
            return;
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Pelanggan',
                'breadcrumb' => array(
                    array('label' => 'Kontak', 'url' => '#'),
                    array('label' => 'Pelanggan', 'url' => base_url('kontak/pelanggan')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Pelanggan',
                'form_action' => base_url('kontak/pelanggan/update/' . $id),
                'back_url' => 'kontak/pelanggan',
                'data' => $data,
                'memberships' => $this->master_membership_model->get_all(array('is_active' => 1))
            );
            $this->render('kontak/pelanggan/edit', $view_data);
            return;
        }
        
        if ($this->pelanggan_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('kontak/pelanggan/edit/' . $id);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'tanggal_lahir' => $this->input->post('tanggal_lahir') ?: null,
            'jenis_kelamin' => $this->input->post('jenis_kelamin') ?: null,
            'membership_id' => $this->input->post('membership_id') ?: null,
            'poin' => $this->input->post('poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->pelanggan_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('kontak/pelanggan');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('kontak/pelanggan/edit/' . $id);
        }
    }
    
    /**
     * View - Show single record
     */
    public function view($id) {
        $this->check_permission('kontak', 'view');
        
        $data = $this->pelanggan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/pelanggan');
            return;
        }
        
        $view_data = array(
            'title' => 'Detail Pelanggan',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Pelanggan', 'url' => base_url('kontak/pelanggan')),
                array('label' => 'Detail')
            ),
            'back_url' => 'kontak/pelanggan',
            'data' => $data
        );
        
        $this->render('kontak/pelanggan/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('kontak', 'view');
        $data = $this->pelanggan_model->get_by_id($id);
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
        
        if ($this->pelanggan_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'tanggal_lahir' => $this->input->post('tanggal_lahir') ?: null,
            'jenis_kelamin' => $this->input->post('jenis_kelamin') ?: null,
            'membership_id' => $this->input->post('membership_id') ?: null,
            'poin' => $this->input->post('poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->pelanggan_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->pelanggan_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('kontak', 'delete');
        if ($this->pelanggan_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


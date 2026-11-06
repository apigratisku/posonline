<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DaftarPengguna extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('peran_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('manajemen', 'view');
    }
    
    private function get_gudang_list() {
        return $this->master_gudang_model->get_all(array('is_active' => 1));
    }
    
    private function validate_gudang_for_role() {
        $peran_id = $this->input->post('peran_id');
        if (empty($peran_id)) {
            return true;
        }
        $peran = $this->peran_model->get_by_id($peran_id);
        if ($peran && $peran->kode === 'kasir' && empty($this->input->post('gudang_id'))) {
            $this->form_validation->set_rules('gudang_id', 'Gudang Transaksi POS', 'required|integer');
            return false;
        }
        return true;
    }
    
    public function index() {
        $data = array(
            'title' => 'Daftar Pengguna',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Daftar Pengguna')
            ),
            'peran_list' => $this->peran_model->get_all(array('is_active' => 1))
        );
        
        $this->render('manajemen_pengguna/daftar_pengguna/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('manajemen', 'view');
        
        $params = array(
            'columns' => array(
                'u.username',
                'u.nama_lengkap',
                'u.email',
                'p.nama',
                'u.is_active',
                'u.created_at',
            ),
            'search_fields' => array('u.username', 'u.nama_lengkap', 'u.email'),
            'default_order' => 'u.created_at',
        );
        
        $result = $this->user_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->username,
                $row->nama_lengkap,
                $row->email,
                isset($row->peran_nama) ? $row->peran_nama : '-',
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                date('d/m/Y H:i', strtotime($row->created_at ?: 'now')),
                $this->get_action_buttons($row->id, $row->username)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $username = '') {
        $buttons = '';
        if ($this->auth->has_permission('manajemen', 'view')) {
            $buttons .= '<a href="' . base_url('manajemen/pengguna/view/' . $id) . '" class="btn btn-sm btn-info" title="Lihat"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'update')) {
            $buttons .= '<a href="' . base_url('manajemen/pengguna/edit/' . $id) . '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'delete')) {
            if (in_array(strtolower($username), array('demo', 'admin', 'adhit'))) {
                $buttons .= '<button class="btn btn-sm btn-secondary" disabled title="User dilindungi sistem"><i class="fas fa-lock"></i></button>';
            } else {
                $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')" title="Hapus"><i class="fas fa-trash"></i></button>';
            }
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('manajemen', 'create');
        $data = array(
            'title' => 'Tambah Pengguna',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Daftar Pengguna', 'url' => base_url('manajemen/pengguna')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Pengguna',
            'form_action' => base_url('manajemen/pengguna/store'),
            'back_url' => 'manajemen/pengguna',
            'peran_list' => $this->peran_model->get_all(array('is_active' => 1)),
            'gudang_list' => $this->get_gudang_list()
        );
        $this->render('manajemen_pengguna/daftar_pengguna/create', $data);
    }
    
    public function store() {
        $this->check_permission('manajemen', 'create');
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->validate_gudang_for_role();
        if ($this->form_validation->run() == false) {
            $this->set_flash('error', 'Validasi gagal. Periksa kembali form.');
            $data = array(
                'title' => 'Tambah Pengguna',
                'breadcrumb' => array(
                    array('label' => 'Manajemen Pengguna', 'url' => '#'),
                    array('label' => 'Daftar Pengguna', 'url' => base_url('manajemen/pengguna')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Pengguna',
                'form_action' => base_url('manajemen/pengguna/store'),
                'back_url' => 'manajemen/pengguna',
                'peran_list' => $this->peran_model->get_all(array('is_active' => 1)),
                'gudang_list' => $this->get_gudang_list()
            );
            $this->render('manajemen_pengguna/daftar_pengguna/create', $data);
            return;
        }
        if ($this->user_model->username_exists($this->input->post('username'))) {
            $this->set_flash('error', 'Username sudah digunakan');
            redirect('manajemen/pengguna/create');
            return;
        }
        if ($this->user_model->email_exists($this->input->post('email'))) {
            $this->set_flash('error', 'Email sudah digunakan');
            redirect('manajemen/pengguna/create');
            return;
        }
        $data = array(
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'nama_lengkap' => $this->input->post('nama_lengkap'),
            'telepon' => $this->input->post('telepon'),
            'alamat' => $this->input->post('alamat'),
            'peran_id' => $this->input->post('peran_id') ?: null,
            'gudang_id' => $this->input->post('gudang_id') ?: null,
            'password' => $this->input->post('password'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->user_model->insert($data);
        $saved = $insert_id ? $this->user_model->get_by_id($insert_id) : null;
        if ($saved) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('manajemen/pengguna');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data. Periksa log aplikasi untuk detail.');
            redirect('manajemen/pengguna/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->user_model->get_by_id_with_role($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/pengguna');
            return;
        }
        unset($data->password);
        $view_data = array(
            'title' => 'Edit Pengguna',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Daftar Pengguna', 'url' => base_url('manajemen/pengguna')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Pengguna',
            'form_action' => base_url('manajemen/pengguna/update/' . $id),
            'back_url' => 'manajemen/pengguna',
            'data' => $data,
            'peran_list' => $this->peran_model->get_all(array('is_active' => 1)),
            'gudang_list' => $this->get_gudang_list()
        );
        $this->render('manajemen_pengguna/daftar_pengguna/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->user_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/pengguna');
            return;
        }
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        $this->validate_gudang_for_role();
        if ($this->form_validation->run() == false) {
            $data = $this->user_model->get_by_id_with_role($id);
            unset($data->password);
            $view_data = array(
                'title' => 'Edit Pengguna',
                'breadcrumb' => array(
                    array('label' => 'Manajemen Pengguna', 'url' => '#'),
                    array('label' => 'Daftar Pengguna', 'url' => base_url('manajemen/pengguna')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Pengguna',
                'form_action' => base_url('manajemen/pengguna/update/' . $id),
                'back_url' => 'manajemen/pengguna',
                'data' => $data,
                'peran_list' => $this->peran_model->get_all(array('is_active' => 1)),
                'gudang_list' => $this->get_gudang_list()
            );
            $this->render('manajemen_pengguna/daftar_pengguna/edit', $view_data);
            return;
        }
        if ($this->user_model->username_exists($this->input->post('username'), $id)) {
            $this->set_flash('error', 'Username sudah digunakan');
            redirect('manajemen/pengguna/edit/' . $id);
            return;
        }
        if ($this->user_model->email_exists($this->input->post('email'), $id)) {
            $this->set_flash('error', 'Email sudah digunakan');
            redirect('manajemen/pengguna/edit/' . $id);
            return;
        }
        $user_to_update = $this->user_model->get_by_id($id);
        $is_demo = ($user_to_update && strtolower($user_to_update->username) === 'demo');

        $update_data = array(
            'username' => $is_demo ? 'demo' : $this->input->post('username'),
            'email' => $this->input->post('email'),
            'nama_lengkap' => $this->input->post('nama_lengkap'),
            'telepon' => $this->input->post('telepon'),
            'alamat' => $this->input->post('alamat'),
            'peran_id' => $is_demo ? $user_to_update->peran_id : ($this->input->post('peran_id') ?: null),
            'gudang_id' => $this->input->post('gudang_id') ?: null,
            'is_active' => $is_demo ? 1 : ($this->input->post('is_active') ? 1 : 0)
        );
        if ($this->input->post('password') && !$is_demo) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
            if ($this->form_validation->run() == false) {
                $data = $this->user_model->get_by_id_with_role($id);
                unset($data->password);
                $view_data = array(
                    'title' => 'Edit Pengguna',
                    'breadcrumb' => array(
                        array('label' => 'Manajemen Pengguna', 'url' => '#'),
                        array('label' => 'Daftar Pengguna', 'url' => base_url('manajemen/pengguna')),
                        array('label' => 'Edit')
                    ),
                    'form_title' => 'Edit Pengguna',
                    'form_action' => base_url('manajemen/pengguna/update/' . $id),
                    'back_url' => 'manajemen/pengguna',
                    'data' => $data,
                    'peran_list' => $this->peran_model->get_all(array('is_active' => 1)),
                    'gudang_list' => $this->get_gudang_list()
                );
                $this->render('manajemen_pengguna/daftar_pengguna/edit', $view_data);
                return;
            }
            $update_data['password'] = $this->input->post('password');
        }
        if ($this->user_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('manajemen/pengguna');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('manajemen/pengguna/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->user_model->get_by_id_with_role($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/pengguna');
            return;
        }
        unset($data->password);
        $view_data = array(
            'title' => 'Detail Pengguna',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Daftar Pengguna', 'url' => base_url('manajemen/pengguna')),
                array('label' => 'Detail')
            ),
            'back_url' => 'manajemen/pengguna',
            'data' => $data
        );
        $this->render('manajemen_pengguna/daftar_pengguna/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->user_model->get_by_id_with_role($id);
        if ($data) {
            unset($data->password); // Don't send password
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('manajemen', 'update');
        } else {
            $this->check_permission('manajemen', 'create');
        }
        
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        if ($this->user_model->username_exists($this->input->post('username'), $id)) {
            $this->ajax_response(false, 'Username sudah digunakan');
            return;
        }
        
        if ($this->user_model->email_exists($this->input->post('email'), $id)) {
            $this->ajax_response(false, 'Email sudah digunakan');
            return;
        }
        
        $user_to_update = $id ? $this->user_model->get_by_id($id) : null;
        $is_demo = ($user_to_update && strtolower($user_to_update->username) === 'demo');

        $data = array(
            'username' => $is_demo ? 'demo' : $this->input->post('username'),
            'email' => $this->input->post('email'),
            'nama_lengkap' => $this->input->post('nama_lengkap'),
            'telepon' => $this->input->post('telepon'),
            'alamat' => $this->input->post('alamat'),
            'peran_id' => $is_demo ? $user_to_update->peran_id : ($this->input->post('peran_id') ?: null),
            'gudang_id' => $this->input->post('gudang_id') ?: null,
            'is_active' => $is_demo ? 1 : ($this->input->post('is_active') ? 1 : 0)
        );
        
        // Password only on create or if provided (and not demo user)
        if (!$is_demo && (!$id || $this->input->post('password'))) {
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            if ($this->form_validation->run() == false) {
                $this->ajax_response(false, 'Password minimal 6 karakter', null, $this->form_validation->error_array());
                return;
            }
            $data['password'] = $this->input->post('password');
        }
        
        if ($id) {
            if ($this->user_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->user_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('manajemen', 'delete');
        $user = $this->user_model->get_by_id($id);
        if ($user && in_array(strtolower($user->username), array('demo', 'admin', 'adhit'))) {
            $this->ajax_response(false, 'Pengguna ini dilindungi dan tidak dapat dihapus');
            return;
        }
        if ($this->user_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


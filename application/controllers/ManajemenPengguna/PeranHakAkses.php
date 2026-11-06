<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeranHakAkses extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('peran_model');
        $this->load->model('peran_hak_akses_model');
        $this->load->model('modul_model');
        $this->check_permission('manajemen', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Peran & Hak Akses',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Peran & Hak Akses')
            ),
            'peran_list' => $this->peran_model->get_all(),
            'modul_list' => $this->modul_model->get_all(array('is_active' => 1))
        );
        
        $this->render('manajemen/peran_hak_akses/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('manajemen', 'view');
        
        $params = array(
            'columns' => array('kode', 'nama', 'created_at'),
            'search_fields' => array('kode', 'nama')
        );
        
        $result = $this->peran_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->kode,
                $row->nama,
                isset($row->deskripsi) ? $row->deskripsi : '-',
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                date('d/m/Y', strtotime($row->created_at ?: 'now')),
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('manajemen', 'view')) {
            $buttons .= '<a href="' . base_url('manajemen-pengguna/peran-hak-akses/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-secondary" onclick="managePermissions(' . $id . ')"><i class="fas fa-key"></i> Kelola Akses</button> ';
            $buttons .= '<a href="' . base_url('manajemen-pengguna/peran-hak-akses/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('manajemen', 'create');
        $data = array(
            'title' => 'Tambah Peran',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Peran & Hak Akses', 'url' => base_url('manajemen-pengguna/peran-hak-akses')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Peran',
            'form_action' => base_url('manajemen-pengguna/peran-hak-akses/store'),
            'back_url' => 'manajemen-pengguna/peran-hak-akses'
        );
        $this->render('manajemen/peran_hak_akses/create', $data);
    }
    
    public function store() {
        $this->check_permission('manajemen', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Peran',
                'breadcrumb' => array(
                    array('label' => 'Manajemen Pengguna', 'url' => '#'),
                    array('label' => 'Peran & Hak Akses', 'url' => base_url('manajemen-pengguna/peran-hak-akses')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Peran',
                'form_action' => base_url('manajemen-pengguna/peran-hak-akses/store'),
                'back_url' => 'manajemen-pengguna/peran-hak-akses'
            );
            $this->render('manajemen/peran_hak_akses/create', $data);
            return;
        }
        if ($this->peran_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('manajemen-pengguna/peran-hak-akses/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->peran_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('manajemen-pengguna/peran-hak-akses');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('manajemen-pengguna/peran-hak-akses/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->peran_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen-pengguna/peran-hak-akses');
            return;
        }
        $view_data = array(
            'title' => 'Edit Peran',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Peran & Hak Akses', 'url' => base_url('manajemen-pengguna/peran-hak-akses')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Peran',
            'form_action' => base_url('manajemen-pengguna/peran-hak-akses/update/' . $id),
            'back_url' => 'manajemen-pengguna/peran-hak-akses',
            'data' => $data
        );
        $this->render('manajemen/peran_hak_akses/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->peran_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen-pengguna/peran-hak-akses');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Peran',
                'breadcrumb' => array(
                    array('label' => 'Manajemen Pengguna', 'url' => '#'),
                    array('label' => 'Peran & Hak Akses', 'url' => base_url('manajemen-pengguna/peran-hak-akses')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Peran',
                'form_action' => base_url('manajemen-pengguna/peran-hak-akses/update/' . $id),
                'back_url' => 'manajemen-pengguna/peran-hak-akses',
                'data' => $data
            );
            $this->render('manajemen/peran_hak_akses/edit', $view_data);
            return;
        }
        if ($this->peran_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('manajemen-pengguna/peran-hak-akses/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->peran_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('manajemen-pengguna/peran-hak-akses');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('manajemen-pengguna/peran-hak-akses/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->peran_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen-pengguna/peran-hak-akses');
            return;
        }
        $permissions = $this->peran_hak_akses_model->get_by_peran($id);
        $view_data = array(
            'title' => 'Detail Peran',
            'breadcrumb' => array(
                array('label' => 'Manajemen Pengguna', 'url' => '#'),
                array('label' => 'Peran & Hak Akses', 'url' => base_url('manajemen-pengguna/peran-hak-akses')),
                array('label' => 'Detail')
            ),
            'back_url' => 'manajemen-pengguna/peran-hak-akses',
            'data' => $data,
            'permissions' => $permissions,
            'modul_list' => $this->modul_model->get_all(array('is_active' => 1))
        );
        $this->render('manajemen/peran_hak_akses/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->peran_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_get_permissions($peran_id) {
        $this->check_permission('manajemen', 'view');
        $permissions = $this->peran_hak_akses_model->get_by_peran($peran_id);
        $this->ajax_response(true, 'Data berhasil diambil', $permissions);
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('manajemen', 'update');
        } else {
            $this->check_permission('manajemen', 'create');
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        if ($this->peran_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->peran_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->peran_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_save_permissions($peran_id) {
        $this->check_permission('manajemen', 'update');
        $permissions = $this->input->post('permissions');
        
        if ($this->peran_hak_akses_model->save_permissions($peran_id, $permissions)) {
            $this->ajax_response(true, 'Hak akses berhasil disimpan');
        } else {
            $this->ajax_response(false, 'Gagal menyimpan hak akses');
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('manajemen', 'delete');
        if ($this->peran_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


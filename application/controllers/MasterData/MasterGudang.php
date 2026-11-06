<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterGudang extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_gudang_model');
        $this->check_permission('master', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Master Gudang',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Gudang')
            )
        );
        $this->render('master_data/gudang/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('master', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'alamat', 'is_utama', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'alamat', 'telepon', 'email')
        );
        $result = $this->master_gudang_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->alamat ?: '-',
                $row->is_utama ? '<span class="badge bg-primary">Utama</span>' : '-',
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
        if ($this->auth->has_permission('master', 'view')) {
            $buttons .= '<a href="' . base_url('masterdata/mastergudang/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'update')) {
            $buttons .= '<a href="' . base_url('masterdata/mastergudang/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('master', 'create');
        $data = array(
            'title' => 'Tambah Master Gudang',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Gudang', 'url' => base_url('masterdata/mastergudang')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Gudang',
            'form_action' => base_url('masterdata/mastergudang/store'),
            'back_url' => 'masterdata/mastergudang'
        );
        $this->render('master_data/gudang/create', $data);
    }
    
    public function store() {
        $this->check_permission('master', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Master Gudang',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Gudang', 'url' => base_url('masterdata/mastergudang')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Gudang',
                'form_action' => base_url('masterdata/mastergudang/store'),
                'back_url' => 'masterdata/mastergudang'
            );
            $this->render('master_data/gudang/create', $data);
            return;
        }
        if ($this->master_gudang_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastergudang/create');
            return;
        }
        // If this is set as utama, unset others
        if ($this->input->post('is_utama')) {
            $this->db->update('master_gudang', array('is_utama' => 0));
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_utama' => $this->input->post('is_utama') ? 1 : 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->master_gudang_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/mastergudang');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('masterdata/mastergudang/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_gudang_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastergudang');
            return;
        }
        $view_data = array(
            'title' => 'Edit Master Gudang',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Gudang', 'url' => base_url('masterdata/mastergudang')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Gudang',
            'form_action' => base_url('masterdata/mastergudang/update/' . $id),
            'back_url' => 'masterdata/mastergudang',
            'data' => $data
        );
        $this->render('master_data/gudang/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_gudang_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastergudang');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Master Gudang',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Gudang', 'url' => base_url('masterdata/mastergudang')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Gudang',
                'form_action' => base_url('masterdata/mastergudang/update/' . $id),
                'back_url' => 'masterdata/mastergudang',
                'data' => $data
            );
            $this->render('master_data/gudang/edit', $view_data);
            return;
        }
        if ($this->master_gudang_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastergudang/edit/' . $id);
            return;
        }
        // If this is set as utama, unset others
        if ($this->input->post('is_utama')) {
            $this->db->where('id !=', $id);
            $this->db->update('master_gudang', array('is_utama' => 0));
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_utama' => $this->input->post('is_utama') ? 1 : 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->master_gudang_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/mastergudang');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/mastergudang/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_gudang_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastergudang');
            return;
        }
        $view_data = array(
            'title' => 'Detail Master Gudang',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Gudang', 'url' => base_url('masterdata/mastergudang')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/mastergudang',
            'data' => $data
        );
        $this->render('master_data/gudang/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_gudang_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('master', 'update');
        } else {
            $this->check_permission('master', 'create');
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        if ($this->master_gudang_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        if ($this->input->post('is_utama') && !$id) {
            $this->db->update('master_gudang', array('is_utama' => 0));
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'is_utama' => $this->input->post('is_utama') ? 1 : 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($id) {
            if ($this->input->post('is_utama')) {
                $this->db->where('id !=', $id);
                $this->db->update('master_gudang', array('is_utama' => 0));
            }
            if ($this->master_gudang_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->master_gudang_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('master', 'delete');
        if ($this->master_gudang_model->is_in_use($id)) {
            $this->ajax_response(false, 'Data tidak dapat dihapus karena masih digunakan');
            return;
        }
        if ($this->master_gudang_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    public function ajax_search() {
        $search = $this->input->get('q');
        $this->db->from('master_gudang');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode', $search);
            $this->db->or_like('nama', $search);
            $this->db->group_end();
        }
        $this->db->where('is_active', 1);
        $data = $this->db->get()->result();
        $results = array();
        foreach ($data as $row) {
            $results[] = array(
                'id' => $row->id,
                'text' => $row->nama . ' (' . $row->kode . ')'
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('results' => $results)));
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterShift extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_shift_model');
        $this->check_permission('master', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Master Shift',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Shift')
            )
        );
        $this->render('master_data/shift/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('master', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'jam_mulai', 'jam_selesai', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama')
        );
        $result = $this->master_shift_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->jam_mulai ?: '-',
                $row->jam_selesai ?: '-',
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
            $buttons .= '<a href="' . base_url('masterdata/mastershift/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'update')) {
            $buttons .= '<a href="' . base_url('masterdata/mastershift/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('master', 'create');
        $data = array(
            'title' => 'Tambah Master Shift',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Shift', 'url' => base_url('masterdata/mastershift')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Shift',
            'form_action' => base_url('masterdata/mastershift/store'),
            'back_url' => 'masterdata/mastershift'
        );
        $this->render('master_data/shift/create', $data);
    }
    
    public function store() {
        $this->check_permission('master', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Master Shift',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Shift', 'url' => base_url('masterdata/mastershift')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Shift',
                'form_action' => base_url('masterdata/mastershift/store'),
                'back_url' => 'masterdata/mastershift'
            );
            $this->render('master_data/shift/create', $data);
            return;
        }
        if ($this->master_shift_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastershift/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'jam_mulai' => $this->input->post('jam_mulai'),
            'jam_selesai' => $this->input->post('jam_selesai'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->master_shift_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/mastershift');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('masterdata/mastershift/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_shift_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastershift');
            return;
        }
        $view_data = array(
            'title' => 'Edit Master Shift',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Shift', 'url' => base_url('masterdata/mastershift')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Shift',
            'form_action' => base_url('masterdata/mastershift/update/' . $id),
            'back_url' => 'masterdata/mastershift',
            'data' => $data
        );
        $this->render('master_data/shift/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_shift_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastershift');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Master Shift',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Shift', 'url' => base_url('masterdata/mastershift')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Shift',
                'form_action' => base_url('masterdata/mastershift/update/' . $id),
                'back_url' => 'masterdata/mastershift',
                'data' => $data
            );
            $this->render('master_data/shift/edit', $view_data);
            return;
        }
        if ($this->master_shift_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastershift/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'jam_mulai' => $this->input->post('jam_mulai'),
            'jam_selesai' => $this->input->post('jam_selesai'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->master_shift_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/mastershift');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/mastershift/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_shift_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastershift');
            return;
        }
        $view_data = array(
            'title' => 'Detail Master Shift',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Shift', 'url' => base_url('masterdata/mastershift')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/mastershift',
            'data' => $data
        );
        $this->render('master_data/shift/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_shift_model->get_by_id($id);
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
        if ($this->master_shift_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'jam_mulai' => $this->input->post('jam_mulai'),
            'jam_selesai' => $this->input->post('jam_selesai'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($id) {
            if ($this->master_shift_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->master_shift_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('master', 'delete');
        if ($this->master_shift_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    public function ajax_search() {
        $search = $this->input->get('q');
        $this->db->from('master_shift');
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


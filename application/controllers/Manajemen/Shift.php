<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('riwayat_shift_model');
        $this->load->model('user_model');
        $this->load->model('master_shift_model');
        $this->check_permission('manajemen', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Riwayat Shift',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Riwayat Shift')
            ),
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        
        $this->render('manajemen/shift/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('manajemen', 'view');
        
        $params = array(
            'columns' => array('rs.tanggal', 'u.nama_lengkap', 'ms.nama', 'rs.jam_mulai', 'rs.jam_selesai'),
            'search_fields' => array('u.nama_lengkap', 'ms.nama', 'rs.keterangan')
        );
        
        if ($this->input->post('user_id')) {
            $params['user_id'] = $this->input->post('user_id');
        }
        
        $result = $this->riwayat_shift_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                date('d/m/Y', strtotime($row->tanggal)),
                $row->nama_lengkap,
                $row->shift_nama ?: '-',
                $row->jam_mulai ?: '-',
                $row->jam_selesai ?: '-',
                $row->keterangan ?: '-',
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('manajemen', 'view')) {
            $buttons .= '<a href="' . base_url('manajemen/shift/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'update')) {
            $buttons .= '<a href="' . base_url('manajemen/shift/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('manajemen', 'create');
        $data = array(
            'title' => 'Tambah Riwayat Shift',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Riwayat Shift', 'url' => base_url('manajemen/shift')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Riwayat Shift',
            'form_action' => base_url('manajemen/shift/store'),
            'back_url' => 'manajemen/shift',
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        $this->render('manajemen/shift/create', $data);
    }
    
    public function store() {
        $this->check_permission('manajemen', 'create');
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('shift_id', 'Shift', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Riwayat Shift',
                'breadcrumb' => array(
                    array('label' => 'Manajemen', 'url' => '#'),
                    array('label' => 'Riwayat Shift', 'url' => base_url('manajemen/shift')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Riwayat Shift',
                'form_action' => base_url('manajemen/shift/store'),
                'back_url' => 'manajemen/shift',
                'users' => $this->user_model->get_all(array('is_active' => 1)),
                'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
            );
            $this->render('manajemen/shift/create', $data);
            return;
        }
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'shift_id' => $this->input->post('shift_id'),
            'tanggal' => $this->input->post('tanggal'),
            'jam_mulai' => $this->input->post('jam_mulai') ?: null,
            'jam_selesai' => $this->input->post('jam_selesai') ?: null,
            'keterangan' => $this->input->post('keterangan')
        );
        $insert_id = $this->riwayat_shift_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('manajemen/shift');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('manajemen/shift/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->riwayat_shift_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/shift');
            return;
        }
        $view_data = array(
            'title' => 'Edit Riwayat Shift',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Riwayat Shift', 'url' => base_url('manajemen/shift')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Riwayat Shift',
            'form_action' => base_url('manajemen/shift/update/' . $id),
            'back_url' => 'manajemen/shift',
            'data' => $data,
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        $this->render('manajemen/shift/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->riwayat_shift_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/shift');
            return;
        }
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('shift_id', 'Shift', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Riwayat Shift',
                'breadcrumb' => array(
                    array('label' => 'Manajemen', 'url' => '#'),
                    array('label' => 'Riwayat Shift', 'url' => base_url('manajemen/shift')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Riwayat Shift',
                'form_action' => base_url('manajemen/shift/update/' . $id),
                'back_url' => 'manajemen/shift',
                'data' => $data,
                'users' => $this->user_model->get_all(array('is_active' => 1)),
                'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
            );
            $this->render('manajemen/shift/edit', $view_data);
            return;
        }
        $update_data = array(
            'user_id' => $this->input->post('user_id'),
            'shift_id' => $this->input->post('shift_id'),
            'tanggal' => $this->input->post('tanggal'),
            'jam_mulai' => $this->input->post('jam_mulai') ?: null,
            'jam_selesai' => $this->input->post('jam_selesai') ?: null,
            'keterangan' => $this->input->post('keterangan')
        );
        if ($this->riwayat_shift_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('manajemen/shift');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('manajemen/shift/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->riwayat_shift_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/shift');
            return;
        }
        $view_data = array(
            'title' => 'Detail Riwayat Shift',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Riwayat Shift', 'url' => base_url('manajemen/shift')),
                array('label' => 'Detail')
            ),
            'back_url' => 'manajemen/shift',
            'data' => $data
        );
        $this->render('manajemen/shift/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->riwayat_shift_model->get_by_id($id);
        if ($data) {
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
        
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('shift_id', 'Shift', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'shift_id' => $this->input->post('shift_id'),
            'tanggal' => $this->input->post('tanggal'),
            'jam_mulai' => $this->input->post('jam_mulai') ?: null,
            'jam_selesai' => $this->input->post('jam_selesai') ?: null,
            'keterangan' => $this->input->post('keterangan')
        );
        
        if ($id) {
            if ($this->riwayat_shift_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->riwayat_shift_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('manajemen', 'delete');
        if ($this->riwayat_shift_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


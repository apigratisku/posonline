<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Presensi extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('presensi_karyawan_model');
        $this->load->model('user_model');
        $this->load->model('master_shift_model');
        $this->check_permission('manajemen', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Presensi Karyawan',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Presensi Karyawan')
            ),
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        
        $this->render('manajemen/presensi/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('manajemen', 'view');
        
        $params = array(
            'columns' => array('pk.tanggal', 'u.nama_lengkap', 'ms.nama', 'pk.jam_masuk', 'pk.jam_keluar', 'pk.status'),
            'search_fields' => array('u.nama_lengkap', 'ms.nama', 'pk.status')
        );
        
        if ($this->input->post('user_id')) {
            $params['user_id'] = $this->input->post('user_id');
        }
        
        $result = $this->presensi_karyawan_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-';
            if ($row->status == 'hadir') {
                $status_badge .= 'success';
            } elseif ($row->status == 'terlambat') {
                $status_badge .= 'warning';
            } elseif ($row->status == 'tidak_hadir') {
                $status_badge .= 'danger';
            } else {
                $status_badge .= 'secondary';
            }
            $status_badge .= '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
            
            $data[] = array(
                date('d/m/Y', strtotime($row->tanggal)),
                $row->nama_lengkap,
                $row->shift_nama ?: '-',
                $row->jam_masuk ?: '-',
                $row->jam_keluar ?: '-',
                $status_badge,
                $row->keterangan ?: '-',
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($this->auth->has_permission('manajemen', 'view')) {
            $buttons .= '<a href="' . base_url('manajemen/presensi/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'update')) {
            if (!$status || $status == 'tidak_hadir') {
                $buttons .= '<button class="btn btn-sm btn-success" onclick="checkIn(' . $id . ')"><i class="fas fa-sign-in-alt"></i> Check In</button> ';
            }
            if ($status == 'hadir' || $status == 'terlambat') {
                $buttons .= '<button class="btn btn-sm btn-info" onclick="checkOut(' . $id . ')"><i class="fas fa-sign-out-alt"></i> Check Out</button> ';
            }
            $buttons .= '<a href="' . base_url('manajemen/presensi/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('manajemen', 'create');
        $data = array(
            'title' => 'Tambah Presensi',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Presensi Karyawan', 'url' => base_url('manajemen/presensi')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Presensi',
            'form_action' => base_url('manajemen/presensi/store'),
            'back_url' => 'manajemen/presensi',
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        $this->render('manajemen/presensi/create', $data);
    }
    
    public function store() {
        $this->check_permission('manajemen', 'create');
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Presensi',
                'breadcrumb' => array(
                    array('label' => 'Manajemen', 'url' => '#'),
                    array('label' => 'Presensi Karyawan', 'url' => base_url('manajemen/presensi')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Presensi',
                'form_action' => base_url('manajemen/presensi/store'),
                'back_url' => 'manajemen/presensi',
                'users' => $this->user_model->get_all(array('is_active' => 1)),
                'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
            );
            $this->render('manajemen/presensi/create', $data);
            return;
        }
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'shift_id' => $this->input->post('shift_id') ?: null,
            'tanggal' => $this->input->post('tanggal'),
            'jam_masuk' => $this->input->post('jam_masuk') ?: null,
            'jam_keluar' => $this->input->post('jam_keluar') ?: null,
            'status' => $this->input->post('status') ?: 'tidak_hadir',
            'keterangan' => $this->input->post('keterangan')
        );
        $insert_id = $this->presensi_karyawan_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('manajemen/presensi');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('manajemen/presensi/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->presensi_karyawan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/presensi');
            return;
        }
        $view_data = array(
            'title' => 'Edit Presensi',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Presensi Karyawan', 'url' => base_url('manajemen/presensi')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Presensi',
            'form_action' => base_url('manajemen/presensi/update/' . $id),
            'back_url' => 'manajemen/presensi',
            'data' => $data,
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        $this->render('manajemen/presensi/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('manajemen', 'update');
        $data = $this->presensi_karyawan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/presensi');
            return;
        }
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Presensi',
                'breadcrumb' => array(
                    array('label' => 'Manajemen', 'url' => '#'),
                    array('label' => 'Presensi Karyawan', 'url' => base_url('manajemen/presensi')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Presensi',
                'form_action' => base_url('manajemen/presensi/update/' . $id),
                'back_url' => 'manajemen/presensi',
                'data' => $data,
                'users' => $this->user_model->get_all(array('is_active' => 1)),
                'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
            );
            $this->render('manajemen/presensi/edit', $view_data);
            return;
        }
        $update_data = array(
            'user_id' => $this->input->post('user_id'),
            'shift_id' => $this->input->post('shift_id') ?: null,
            'tanggal' => $this->input->post('tanggal'),
            'jam_masuk' => $this->input->post('jam_masuk') ?: null,
            'jam_keluar' => $this->input->post('jam_keluar') ?: null,
            'status' => $this->input->post('status') ?: 'tidak_hadir',
            'keterangan' => $this->input->post('keterangan')
        );
        if ($this->presensi_karyawan_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('manajemen/presensi');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('manajemen/presensi/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->presensi_karyawan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('manajemen/presensi');
            return;
        }
        $view_data = array(
            'title' => 'Detail Presensi',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Presensi Karyawan', 'url' => base_url('manajemen/presensi')),
                array('label' => 'Detail')
            ),
            'back_url' => 'manajemen/presensi',
            'data' => $data
        );
        $this->render('manajemen/presensi/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->presensi_karyawan_model->get_by_id($id);
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
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'shift_id' => $this->input->post('shift_id') ?: null,
            'tanggal' => $this->input->post('tanggal'),
            'jam_masuk' => $this->input->post('jam_masuk') ?: null,
            'jam_keluar' => $this->input->post('jam_keluar') ?: null,
            'status' => $this->input->post('status') ?: 'tidak_hadir',
            'keterangan' => $this->input->post('keterangan')
        );
        
        if ($id) {
            if ($this->presensi_karyawan_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->presensi_karyawan_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_checkin() {
        $this->check_permission('manajemen', 'create');
        
        $user_id = $this->input->post('user_id');
        $tanggal = $this->input->post('tanggal') ?: date('Y-m-d');
        
        if (!$user_id) {
            $this->ajax_response(false, 'User ID harus diisi');
            return;
        }
        
        // Check if already checked in today
        $existing = $this->presensi_karyawan_model->get_all(array(
            'user_id' => $user_id,
            'tanggal' => $tanggal
        ));
        
        if (!empty($existing)) {
            $this->ajax_response(false, 'Sudah check in hari ini');
            return;
        }
        
        $data = array(
            'user_id' => $user_id,
            'tanggal' => $tanggal,
            'jam_masuk' => date('H:i:s'),
            'status' => 'hadir'
        );
        
        $insert_id = $this->presensi_karyawan_model->insert($data);
        if ($insert_id) {
            $this->ajax_response(true, 'Check in berhasil', array('id' => $insert_id));
        } else {
            $this->ajax_response(false, 'Gagal check in');
        }
    }
    
    public function ajax_checkout($id) {
        $this->check_permission('manajemen', 'update');
        
        $data = array(
            'jam_keluar' => date('H:i:s')
        );
        
        if ($this->presensi_karyawan_model->update($id, $data)) {
            $this->ajax_response(true, 'Check out berhasil');
        } else {
            $this->ajax_response(false, 'Gagal check out');
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('manajemen', 'delete');
        if ($this->presensi_karyawan_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('jadwal_kerja_model');
        $this->load->model('user_model');
        $this->load->model('master_shift_model');
        $this->check_permission('manajemen', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Jadwal Kerja',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Jadwal Kerja')
            ),
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        
        $this->render('manajemen/jadwal/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('manajemen', 'view');
        
        $users = $this->user_model->get_all(array('is_active' => 1));
        $data = array();
        
        foreach ($users as $user) {
            $jadwal = $this->jadwal_kerja_model->get_by_user($user->id);
            $jadwal_map = array();
            foreach ($jadwal as $j) {
                $jadwal_map[$j->hari] = $j;
            }
            
            $hari_list = array('senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu');
            $jadwal_str = '';
            foreach ($hari_list as $hari) {
                if (isset($jadwal_map[$hari]) && $jadwal_map[$hari]->shift_nama) {
                    $jadwal_str .= ucfirst($hari) . ': ' . $jadwal_map[$hari]->shift_nama . '<br>';
                }
            }
            
            $data[] = array(
                $user->nama_lengkap,
                $user->username,
                $jadwal_str ?: '-',
                $this->get_action_buttons($user->id)
            );
        }
        
        $result = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => count($users),
            'recordsFiltered' => count($users),
            'data' => $data
        );
        
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($user_id) {
        $buttons = '';
        if ($this->auth->has_permission('manajemen', 'view')) {
            $buttons .= '<a href="' . base_url('manajemen/jadwal/view/' . $user_id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('manajemen', 'update')) {
            $buttons .= '<a href="' . base_url('manajemen/jadwal/edit/' . $user_id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a> ';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('manajemen', 'create');
        $data = array(
            'title' => 'Tambah Jadwal Kerja',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Jadwal Kerja', 'url' => base_url('manajemen/jadwal')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Jadwal Kerja',
            'form_action' => base_url('manajemen/jadwal/store'),
            'back_url' => 'manajemen/jadwal',
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        $this->render('manajemen/jadwal/create', $data);
    }
    
    public function store() {
        $this->check_permission('manajemen', 'create');
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Jadwal Kerja',
                'breadcrumb' => array(
                    array('label' => 'Manajemen', 'url' => '#'),
                    array('label' => 'Jadwal Kerja', 'url' => base_url('manajemen/jadwal')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Jadwal Kerja',
                'form_action' => base_url('manajemen/jadwal/store'),
                'back_url' => 'manajemen/jadwal',
                'users' => $this->user_model->get_all(array('is_active' => 1)),
                'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
            );
            $this->render('manajemen/jadwal/create', $data);
            return;
        }
        $user_id = $this->input->post('user_id');
        $schedule = $this->input->post('schedule');
        if ($this->jadwal_kerja_model->save_schedule($user_id, $schedule)) {
            $this->set_flash('success', 'Jadwal berhasil disimpan');
            redirect('manajemen/jadwal');
        } else {
            $this->set_flash('error', 'Gagal menyimpan jadwal');
            redirect('manajemen/jadwal/create');
        }
    }
    
    public function edit($user_id) {
        $this->check_permission('manajemen', 'update');
        $user = $this->user_model->get_by_id($user_id);
        if (!$user) {
            $this->set_flash('error', 'User tidak ditemukan');
            redirect('manajemen/jadwal');
            return;
        }
        $jadwal = $this->jadwal_kerja_model->get_by_user($user_id);
        $jadwal_map = array();
        foreach ($jadwal as $j) {
            $jadwal_map[$j->hari] = array(
                'shift_id' => $j->shift_id,
                'is_active' => $j->is_active
            );
        }
        $view_data = array(
            'title' => 'Edit Jadwal Kerja',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Jadwal Kerja', 'url' => base_url('manajemen/jadwal')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Jadwal Kerja',
            'form_action' => base_url('manajemen/jadwal/update/' . $user_id),
            'back_url' => 'manajemen/jadwal',
            'user' => $user,
            'jadwal' => $jadwal_map,
            'shifts' => $this->master_shift_model->get_all(array('is_active' => 1))
        );
        $this->render('manajemen/jadwal/edit', $view_data);
    }
    
    public function update($user_id) {
        $this->check_permission('manajemen', 'update');
        $user = $this->user_model->get_by_id($user_id);
        if (!$user) {
            $this->set_flash('error', 'User tidak ditemukan');
            redirect('manajemen/jadwal');
            return;
        }
        $schedule = $this->input->post('schedule');
        if ($this->jadwal_kerja_model->save_schedule($user_id, $schedule)) {
            $this->set_flash('success', 'Jadwal berhasil diupdate');
            redirect('manajemen/jadwal');
        } else {
            $this->set_flash('error', 'Gagal mengupdate jadwal');
            redirect('manajemen/jadwal/edit/' . $user_id);
        }
    }
    
    public function view($user_id) {
        $this->check_permission('manajemen', 'view');
        $user = $this->user_model->get_by_id($user_id);
        if (!$user) {
            $this->set_flash('error', 'User tidak ditemukan');
            redirect('manajemen/jadwal');
            return;
        }
        $jadwal = $this->jadwal_kerja_model->get_by_user($user_id);
        $view_data = array(
            'title' => 'Detail Jadwal Kerja',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Jadwal Kerja', 'url' => base_url('manajemen/jadwal')),
                array('label' => 'Detail')
            ),
            'back_url' => 'manajemen/jadwal',
            'user' => $user,
            'jadwal' => $jadwal
        );
        $this->render('manajemen/jadwal/view', $view_data);
    }
    
    public function ajax_get($user_id) {
        $this->check_permission('manajemen', 'view');
        $jadwal = $this->jadwal_kerja_model->get_by_user($user_id);
        $jadwal_map = array();
        
        foreach ($jadwal as $j) {
            $jadwal_map[$j->hari] = array(
                'shift_id' => $j->shift_id,
                'is_active' => $j->is_active
            );
        }
        
        $this->ajax_response(true, 'Data berhasil diambil', array(
            'user_id' => $user_id,
            'jadwal' => $jadwal_map
        ));
    }
    
    public function ajax_save() {
        $this->check_permission('manajemen', 'update');
        
        $user_id = $this->input->post('user_id');
        if (!$user_id) {
            $this->ajax_response(false, 'User ID harus diisi');
            return;
        }
        
        $schedule = $this->input->post('schedule');
        if ($this->jadwal_kerja_model->save_schedule($user_id, $schedule)) {
            $this->ajax_response(true, 'Jadwal berhasil disimpan');
        } else {
            $this->ajax_response(false, 'Gagal menyimpan jadwal');
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('manajemen', 'delete');
        if ($this->jadwal_kerja_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


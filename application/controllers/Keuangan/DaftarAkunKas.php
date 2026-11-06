<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DaftarAkunKas extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('akun_kas_model');
        $this->check_permission('keuangan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Daftar Akun Kas',
            'breadcrumb' => array(
                array('label' => 'Keuangan', 'url' => '#'),
                array('label' => 'Daftar Akun Kas')
            )
        );
        
        $this->render('keuangan/akun_kas/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('keuangan', 'view');
        
        $params = array(
            'columns' => array('kode', 'nama', 'tipe', 'saldo_akhir', 'is_active'),
            'search_fields' => array('kode', 'nama')
        );
        
        $result = $this->akun_kas_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $saldo = $this->akun_kas_model->get_saldo($row->id);
            $data[] = array(
                $row->kode,
                $row->nama,
                '<span class="badge bg-' . ($row->tipe == 'kas' ? 'primary' : 'info') . '">' . strtoupper($row->tipe) . '</span>',
                number_format($saldo, 0, ',', '.'),
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('keuangan', 'view')) {
            $buttons .= '<a href="' . base_url('keuangan/akun-kas/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
            $buttons .= '<button class="btn btn-sm btn-secondary" onclick="viewBukuKas(' . $id . ')"><i class="fas fa-book"></i> Buku Kas</button> ';
        }
        if ($this->auth->has_permission('keuangan', 'update')) {
            $buttons .= '<a href="' . base_url('keuangan/akun-kas/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('keuangan', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('keuangan', 'create');
        $data = array(
            'title' => 'Tambah Akun Kas',
            'breadcrumb' => array(
                array('label' => 'Keuangan', 'url' => '#'),
                array('label' => 'Daftar Akun Kas', 'url' => base_url('keuangan/akun-kas')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Akun Kas',
            'form_action' => base_url('keuangan/akun-kas/store'),
            'back_url' => 'keuangan/akun-kas'
        );
        $this->render('keuangan/akun_kas/create', $data);
    }
    
    public function store() {
        $this->check_permission('keuangan', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Akun Kas',
                'breadcrumb' => array(
                    array('label' => 'Keuangan', 'url' => '#'),
                    array('label' => 'Daftar Akun Kas', 'url' => base_url('keuangan/akun-kas')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Akun Kas',
                'form_action' => base_url('keuangan/akun-kas/store'),
                'back_url' => 'keuangan/akun-kas'
            );
            $this->render('keuangan/akun_kas/create', $data);
            return;
        }
        if ($this->akun_kas_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('keuangan/akun-kas/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe') ?: 'kas',
            'saldo_awal' => $this->input->post('saldo_awal') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $data['saldo_akhir'] = $data['saldo_awal'];
        $insert_id = $this->akun_kas_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('keuangan/akun-kas');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('keuangan/akun-kas/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('keuangan', 'update');
        $data = $this->akun_kas_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('keuangan/akun-kas');
            return;
        }
        $data->saldo = $this->akun_kas_model->get_saldo($id);
        $view_data = array(
            'title' => 'Edit Akun Kas',
            'breadcrumb' => array(
                array('label' => 'Keuangan', 'url' => '#'),
                array('label' => 'Daftar Akun Kas', 'url' => base_url('keuangan/akun-kas')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Akun Kas',
            'form_action' => base_url('keuangan/akun-kas/update/' . $id),
            'back_url' => 'keuangan/akun-kas',
            'data' => $data
        );
        $this->render('keuangan/akun_kas/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('keuangan', 'update');
        $data = $this->akun_kas_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('keuangan/akun-kas');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data->saldo = $this->akun_kas_model->get_saldo($id);
            $view_data = array(
                'title' => 'Edit Akun Kas',
                'breadcrumb' => array(
                    array('label' => 'Keuangan', 'url' => '#'),
                    array('label' => 'Daftar Akun Kas', 'url' => base_url('keuangan/akun-kas')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Akun Kas',
                'form_action' => base_url('keuangan/akun-kas/update/' . $id),
                'back_url' => 'keuangan/akun-kas',
                'data' => $data
            );
            $this->render('keuangan/akun_kas/edit', $view_data);
            return;
        }
        if ($this->akun_kas_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('keuangan/akun-kas/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe') ?: 'kas',
            'saldo_awal' => $this->input->post('saldo_awal') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->akun_kas_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('keuangan/akun-kas');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('keuangan/akun-kas/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('keuangan', 'view');
        $data = $this->akun_kas_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('keuangan/akun-kas');
            return;
        }
        $data->saldo = $this->akun_kas_model->get_saldo($id);
        $view_data = array(
            'title' => 'Detail Akun Kas',
            'breadcrumb' => array(
                array('label' => 'Keuangan', 'url' => '#'),
                array('label' => 'Daftar Akun Kas', 'url' => base_url('keuangan/akun-kas')),
                array('label' => 'Detail')
            ),
            'back_url' => 'keuangan/akun-kas',
            'data' => $data
        );
        $this->render('keuangan/akun_kas/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('keuangan', 'view');
        $data = $this->akun_kas_model->get_by_id($id);
        if ($data) {
            $data->saldo = $this->akun_kas_model->get_saldo($id);
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('keuangan', 'update');
        } else {
            $this->check_permission('keuangan', 'create');
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        if ($this->akun_kas_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe') ?: 'kas',
            'saldo_awal' => $this->input->post('saldo_awal') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->akun_kas_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $data['saldo_akhir'] = $data['saldo_awal'];
            $insert_id = $this->akun_kas_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('keuangan', 'delete');
        if ($this->akun_kas_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


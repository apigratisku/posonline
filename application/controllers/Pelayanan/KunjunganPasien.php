<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KunjunganPasien extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('kunjungan_pasien_model');
        $this->load->model('pelanggan_model');
        $this->load->model('dokter_model');
        $this->check_permission('pelayanan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Kunjungan Pasien',
            'breadcrumb' => array(
                array('label' => 'Pelayanan', 'url' => '#'),
                array('label' => 'Kunjungan Pasien')
            ),
            'pelanggan_list' => $this->pelanggan_model->get_all(array('is_active' => 1)),
            'dokter_list' => $this->dokter_model->get_all(array('is_active' => 1))
        );
        
        $this->render('pelayanan/kunjungan_pasien/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('pelayanan', 'view');
        
        $params = array(
            'columns' => array('kp.no_kunjungan', 'kp.tanggal', 'kp.status'),
            'search_fields' => array('kp.no_kunjungan', 'pl.nama', 'd.nama')
        );
        
        $result = $this->kunjungan_pasien_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'process' ? 'warning' : ($row->status == 'cancelled' ? 'danger' : 'secondary'))) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_kunjungan,
                $row->pelanggan_nama,
                isset($row->dokter_nama) ? $row->dokter_nama : '-',
                date('d/m/Y H:i', strtotime($row->tanggal)),
                $status_badge,
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($this->auth->has_permission('pelayanan', 'view')) {
            $buttons .= '<a href="' . base_url('pelayanan/kunjungan-pasien/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($status != 'completed' && $this->auth->has_permission('pelayanan', 'update')) {
            $buttons .= '<a href="' . base_url('pelayanan/kunjungan-pasien/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($status != 'completed' && $this->auth->has_permission('pelayanan', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('pelayanan', 'create');
        $data = array(
            'title' => 'Tambah Kunjungan Pasien',
            'breadcrumb' => array(
                array('label' => 'Pelayanan', 'url' => '#'),
                array('label' => 'Kunjungan Pasien', 'url' => base_url('pelayanan/kunjungan-pasien')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Kunjungan Pasien',
            'form_action' => base_url('pelayanan/kunjungan-pasien/store'),
            'back_url' => 'pelayanan/kunjungan-pasien',
            'pelanggan_list' => $this->pelanggan_model->get_all(array('is_active' => 1)),
            'dokter_list' => $this->dokter_model->get_all(array('is_active' => 1))
        );
        $this->render('pelayanan/kunjungan_pasien/create', $data);
    }
    
    public function store() {
        $this->check_permission('pelayanan', 'create');
        $this->form_validation->set_rules('pelanggan_id', 'Pelanggan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Kunjungan Pasien',
                'breadcrumb' => array(
                    array('label' => 'Pelayanan', 'url' => '#'),
                    array('label' => 'Kunjungan Pasien', 'url' => base_url('pelayanan/kunjungan-pasien')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Kunjungan Pasien',
                'form_action' => base_url('pelayanan/kunjungan-pasien/store'),
                'back_url' => 'pelayanan/kunjungan-pasien',
                'pelanggan_list' => $this->pelanggan_model->get_all(array('is_active' => 1)),
                'dokter_list' => $this->dokter_model->get_all(array('is_active' => 1))
            );
            $this->render('pelayanan/kunjungan_pasien/create', $data);
            return;
        }
        $data = array(
            'pelanggan_id' => $this->input->post('pelanggan_id'),
            'dokter_id' => $this->input->post('dokter_id') ?: null,
            'jenis_pelayanan_id' => $this->input->post('jenis_pelayanan_id') ?: null,
            'jenis_antrian_id' => $this->input->post('jenis_antrian_id') ?: null,
            'tanggal' => $this->input->post('tanggal'),
            'keluhan' => $this->input->post('keluhan'),
            'status' => $this->input->post('status') ?: 'pending',
            'no_kunjungan' => $this->kunjungan_pasien_model->generate_no_kunjungan()
        );
        $insert_id = $this->kunjungan_pasien_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('pelayanan/kunjungan-pasien');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('pelayanan/kunjungan-pasien/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('pelayanan', 'update');
        $data = $this->kunjungan_pasien_model->get_by_id_with_relations($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('pelayanan/kunjungan-pasien');
            return;
        }
        if ($data->status == 'completed') {
            $this->set_flash('error', 'Kunjungan yang sudah selesai tidak dapat diedit');
            redirect('pelayanan/kunjungan-pasien');
            return;
        }
        $view_data = array(
            'title' => 'Edit Kunjungan Pasien',
            'breadcrumb' => array(
                array('label' => 'Pelayanan', 'url' => '#'),
                array('label' => 'Kunjungan Pasien', 'url' => base_url('pelayanan/kunjungan-pasien')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Kunjungan Pasien',
            'form_action' => base_url('pelayanan/kunjungan-pasien/update/' . $id),
            'back_url' => 'pelayanan/kunjungan-pasien',
            'data' => $data,
            'pelanggan_list' => $this->pelanggan_model->get_all(array('is_active' => 1)),
            'dokter_list' => $this->dokter_model->get_all(array('is_active' => 1))
        );
        $this->render('pelayanan/kunjungan_pasien/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('pelayanan', 'update');
        $data = $this->kunjungan_pasien_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('pelayanan/kunjungan-pasien');
            return;
        }
        if ($data->status == 'completed') {
            $this->set_flash('error', 'Kunjungan yang sudah selesai tidak dapat diedit');
            redirect('pelayanan/kunjungan-pasien');
            return;
        }
        $this->form_validation->set_rules('pelanggan_id', 'Pelanggan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        if ($this->form_validation->run() == false) {
            $data = $this->kunjungan_pasien_model->get_by_id_with_relations($id);
            $view_data = array(
                'title' => 'Edit Kunjungan Pasien',
                'breadcrumb' => array(
                    array('label' => 'Pelayanan', 'url' => '#'),
                    array('label' => 'Kunjungan Pasien', 'url' => base_url('pelayanan/kunjungan-pasien')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Kunjungan Pasien',
                'form_action' => base_url('pelayanan/kunjungan-pasien/update/' . $id),
                'back_url' => 'pelayanan/kunjungan-pasien',
                'data' => $data,
                'pelanggan_list' => $this->pelanggan_model->get_all(array('is_active' => 1)),
                'dokter_list' => $this->dokter_model->get_all(array('is_active' => 1))
            );
            $this->render('pelayanan/kunjungan_pasien/edit', $view_data);
            return;
        }
        $update_data = array(
            'pelanggan_id' => $this->input->post('pelanggan_id'),
            'dokter_id' => $this->input->post('dokter_id') ?: null,
            'jenis_pelayanan_id' => $this->input->post('jenis_pelayanan_id') ?: null,
            'jenis_antrian_id' => $this->input->post('jenis_antrian_id') ?: null,
            'tanggal' => $this->input->post('tanggal'),
            'keluhan' => $this->input->post('keluhan'),
            'status' => $this->input->post('status') ?: 'pending'
        );
        if ($this->kunjungan_pasien_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('pelayanan/kunjungan-pasien');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('pelayanan/kunjungan-pasien/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('pelayanan', 'view');
        $data = $this->kunjungan_pasien_model->get_by_id_with_relations($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('pelayanan/kunjungan-pasien');
            return;
        }
        $view_data = array(
            'title' => 'Detail Kunjungan Pasien',
            'breadcrumb' => array(
                array('label' => 'Pelayanan', 'url' => '#'),
                array('label' => 'Kunjungan Pasien', 'url' => base_url('pelayanan/kunjungan-pasien')),
                array('label' => 'Detail')
            ),
            'back_url' => 'pelayanan/kunjungan-pasien',
            'data' => $data
        );
        $this->render('pelayanan/kunjungan_pasien/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('pelayanan', 'view');
        $data = $this->kunjungan_pasien_model->get_by_id_with_relations($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('pelayanan', 'update');
        } else {
            $this->check_permission('pelayanan', 'create');
        }
        
        $this->form_validation->set_rules('pelanggan_id', 'Pelanggan', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'pelanggan_id' => $this->input->post('pelanggan_id'),
            'dokter_id' => $this->input->post('dokter_id') ?: null,
            'jenis_pelayanan_id' => $this->input->post('jenis_pelayanan_id') ?: null,
            'jenis_antrian_id' => $this->input->post('jenis_antrian_id') ?: null,
            'tanggal' => $this->input->post('tanggal'),
            'keluhan' => $this->input->post('keluhan'),
            'status' => $this->input->post('status') ?: 'pending'
        );
        
        if (!$id) {
            $data['no_kunjungan'] = $this->kunjungan_pasien_model->generate_no_kunjungan();
        }
        
        if ($id) {
            if ($this->kunjungan_pasien_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->kunjungan_pasien_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('pelayanan', 'delete');
        if ($this->kunjungan_pasien_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


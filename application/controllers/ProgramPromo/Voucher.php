<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('voucher_model');
        $this->check_permission('promo', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Program Voucher',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Voucher')
            )
        );
        $this->render('program_promo/voucher/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('promo', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'nilai', 'tanggal_mulai', 'tanggal_selesai', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama')
        );
        $result = $this->voucher_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                'Rp ' . number_format($row->nilai ?: 0, 0, ',', '.'),
                date('d/m/Y', strtotime($row->tanggal_mulai)),
                date('d/m/Y', strtotime($row->tanggal_selesai)),
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
        if ($this->auth->has_permission('promo', 'view')) {
            $buttons .= '<a href="' . base_url('promo/voucher/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('promo', 'update')) {
            $buttons .= '<a href="' . base_url('promo/voucher/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('promo', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('promo', 'create');
        $data = array(
            'title' => 'Tambah Voucher',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Voucher', 'url' => base_url('promo/voucher')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Voucher',
            'form_action' => base_url('promo/voucher/store'),
            'back_url' => 'promo/voucher'
        );
        $this->render('program_promo/voucher/create', $data);
    }
    
    public function store() {
        $this->check_permission('promo', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('nilai', 'Nilai', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Voucher',
                'breadcrumb' => array(
                    array('label' => 'Program Promo', 'url' => '#'),
                    array('label' => 'Voucher', 'url' => base_url('promo/voucher')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Voucher',
                'form_action' => base_url('promo/voucher/store'),
                'back_url' => 'promo/voucher'
            );
            $this->render('program_promo/voucher/create', $data);
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'nilai' => $this->input->post('nilai'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'minimal_pembelian' => $this->input->post('minimal_pembelian') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->voucher_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('promo/voucher');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('promo/voucher/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('promo', 'update');
        $data = $this->voucher_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/voucher');
            return;
        }
        $view_data = array(
            'title' => 'Edit Voucher',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Voucher', 'url' => base_url('promo/voucher')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Voucher',
            'form_action' => base_url('promo/voucher/update/' . $id),
            'back_url' => 'promo/voucher',
            'data' => $data
        );
        $this->render('program_promo/voucher/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('promo', 'update');
        $data = $this->voucher_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/voucher');
            return;
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('nilai', 'Nilai', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Voucher',
                'breadcrumb' => array(
                    array('label' => 'Program Promo', 'url' => '#'),
                    array('label' => 'Voucher', 'url' => base_url('promo/voucher')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Voucher',
                'form_action' => base_url('promo/voucher/update/' . $id),
                'back_url' => 'promo/voucher',
                'data' => $data
            );
            $this->render('program_promo/voucher/edit', $view_data);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'nilai' => $this->input->post('nilai'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'minimal_pembelian' => $this->input->post('minimal_pembelian') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->voucher_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('promo/voucher');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('promo/voucher/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('promo', 'view');
        $data = $this->voucher_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('promo/voucher');
            return;
        }
        $view_data = array(
            'title' => 'Detail Voucher',
            'breadcrumb' => array(
                array('label' => 'Program Promo', 'url' => '#'),
                array('label' => 'Voucher', 'url' => base_url('promo/voucher')),
                array('label' => 'Detail')
            ),
            'back_url' => 'promo/voucher',
            'data' => $data
        );
        $this->render('program_promo/voucher/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('promo', 'view');
        $data = $this->voucher_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('promo', 'update');
        } else {
            $this->check_permission('promo', 'create');
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('nilai', 'Nilai', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'nilai' => $this->input->post('nilai'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'minimal_pembelian' => $this->input->post('minimal_pembelian') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->voucher_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->voucher_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('promo', 'delete');
        if ($this->voucher_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('sales_model');
        $this->check_permission('kontak', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Sales / Pelayan',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Sales')
            )
        );
        
        $this->render('kontak/sales/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('kontak', 'view');
        
        $params = array(
            'columns' => array('kode', 'nama', 'telepon', 'email', 'komisi_persen', 'is_active'),
            'search_fields' => array('kode', 'nama', 'telepon', 'email')
        );
        
        $result = $this->sales_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->kode,
                $row->nama,
                isset($row->telepon) ? $row->telepon : '-',
                isset($row->email) ? $row->email : '-',
                number_format($row->komisi_persen, 2) . '%',
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('kontak', 'view')) {
            $buttons .= '<a href="' . base_url('kontak/sales/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('kontak', 'update')) {
            $buttons .= '<a href="' . base_url('kontak/sales/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('kontak', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('kontak', 'create');
        $data = array(
            'title' => 'Tambah Sales',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Sales', 'url' => base_url('kontak/sales')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Sales',
            'form_action' => base_url('kontak/sales/store'),
            'back_url' => 'kontak/sales'
        );
        $this->render('kontak/sales/create', $data);
    }
    
    public function store() {
        $this->check_permission('kontak', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Sales',
                'breadcrumb' => array(
                    array('label' => 'Kontak', 'url' => '#'),
                    array('label' => 'Sales', 'url' => base_url('kontak/sales')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Sales',
                'form_action' => base_url('kontak/sales/store'),
                'back_url' => 'kontak/sales'
            );
            $this->render('kontak/sales/create', $data);
            return;
        }
        if ($this->sales_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('kontak/sales/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'komisi_persen' => $this->input->post('komisi_persen') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->sales_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('kontak/sales');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('kontak/sales/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('kontak', 'update');
        $data = $this->sales_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/sales');
            return;
        }
        $view_data = array(
            'title' => 'Edit Sales',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Sales', 'url' => base_url('kontak/sales')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Sales',
            'form_action' => base_url('kontak/sales/update/' . $id),
            'back_url' => 'kontak/sales',
            'data' => $data
        );
        $this->render('kontak/sales/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('kontak', 'update');
        $data = $this->sales_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/sales');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Sales',
                'breadcrumb' => array(
                    array('label' => 'Kontak', 'url' => '#'),
                    array('label' => 'Sales', 'url' => base_url('kontak/sales')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Sales',
                'form_action' => base_url('kontak/sales/update/' . $id),
                'back_url' => 'kontak/sales',
                'data' => $data
            );
            $this->render('kontak/sales/edit', $view_data);
            return;
        }
        if ($this->sales_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('kontak/sales/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'komisi_persen' => $this->input->post('komisi_persen') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->sales_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('kontak/sales');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('kontak/sales/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('kontak', 'view');
        $data = $this->sales_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kontak/sales');
            return;
        }
        $view_data = array(
            'title' => 'Detail Sales',
            'breadcrumb' => array(
                array('label' => 'Kontak', 'url' => '#'),
                array('label' => 'Sales', 'url' => base_url('kontak/sales')),
                array('label' => 'Detail')
            ),
            'back_url' => 'kontak/sales',
            'data' => $data
        );
        $this->render('kontak/sales/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('kontak', 'view');
        $data = $this->sales_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('kontak', 'update');
        } else {
            $this->check_permission('kontak', 'create');
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        if ($this->sales_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email'),
            'komisi_persen' => $this->input->post('komisi_persen') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->sales_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->sales_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('kontak', 'delete');
        if ($this->sales_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


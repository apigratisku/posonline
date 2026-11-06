<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProdukMitra extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('produk_mitra_model');
        $this->load->model('outlet_mitra_model');
        $this->check_permission('outlet', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Produk Mitra',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Produk Mitra')
            ),
            'outlets' => $this->outlet_mitra_model->get_all(array('is_active' => 1))
        );
        $this->render('multi_outlet/produk_mitra/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('outlet', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'outlet_mitra', 'harga', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama')
        );
        $result = $this->produk_mitra_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->outlet_mitra ?: '-',
                'Rp ' . number_format($row->harga ?: 0, 0, ',', '.'),
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
        if ($this->auth->has_permission('outlet', 'view')) {
            $buttons .= '<a href="' . base_url('outlet/produk-mitra/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'update')) {
            $buttons .= '<a href="' . base_url('outlet/produk-mitra/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function ajax_get($id) {
        $this->check_permission('outlet', 'view');
        $data = $this->produk_mitra_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('outlet', 'update');
        } else {
            $this->check_permission('outlet', 'create');
        }
        
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'outlet_mitra_id' => $this->input->post('outlet_mitra_id') ?: null,
            'harga' => $this->input->post('harga') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            if ($this->produk_mitra_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->produk_mitra_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('outlet', 'delete');
        if ($this->produk_mitra_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


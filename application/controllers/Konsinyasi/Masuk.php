<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Masuk extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('konsinyasi_masuk_model');
        $this->load->model('supplier_model');
        $this->load->model('master_gudang_model');
        $this->load->model('master_produk_model');
        $this->check_permission('konsinyasi', 'view');
    }
    
    /**
     * Index - List page
     */
    public function index() {
        $data = array(
            'title' => 'Konsinyasi Masuk',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Konsinyasi Masuk')
            ),
            'suppliers' => $this->supplier_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('konsinyasi/masuk/index', $data);
    }
    
    /**
     * AJAX List - DataTables server-side
     */
    public function ajax_list() {
        $this->check_permission('konsinyasi', 'view');
        $params = array(
            'columns' => array('km.id', 'km.no_konsinyasi', 's.nama', 'mg.nama', 'km.tanggal', 'km.status', 'km.keterangan', 'km.created_at'),
            'search_fields' => array('km.no_konsinyasi', 's.nama', 'mg.nama', 'km.keterangan'),
            'default_order' => 'km.tanggal',
            'build_query' => function () {
                $this->db->select('km.*, s.nama as supplier_nama, mg.nama as gudang_nama');
                $this->db->from('konsinyasi_masuk km');
                $this->db->join('supplier s', 's.id = km.supplier_id', 'left');
                $this->db->join('master_gudang mg', 'mg.id = km.gudang_id', 'left');
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-';
            switch($row->status) {
                case 'draft': $status_badge .= 'secondary'; break;
                case 'diterima': $status_badge .= 'success'; break;
                case 'ditolak': $status_badge .= 'danger'; break;
                default: $status_badge .= 'info';
            }
            $status_badge .= '">' . ucfirst($row->status ?: 'draft') . '</span>';
            $data[] = array(
                $row->id,
                $row->no_konsinyasi,
                $row->supplier_nama ?: '-',
                $row->gudang_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                $status_badge,
                $row->keterangan ?: '-',
                date('d/m/Y H:i', strtotime($row->created_at ?: 'now')),
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    /**
     * Get action buttons
     */
    private function get_action_buttons($id, $status) {
        $buttons = '';
        
        if ($this->auth->has_permission('konsinyasi', 'view')) {
            $buttons .= '<a href="' . base_url('konsinyasi/masuk/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        
        if ($this->auth->has_permission('konsinyasi', 'update') && $status == 'draft') {
            $buttons .= '<a href="' . base_url('konsinyasi/masuk/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        
        if ($this->auth->has_permission('konsinyasi', 'delete') && $status == 'draft') {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        
        return $buttons;
    }
    
    /**
     * Create - Show create form
     */
    public function create() {
        $this->check_permission('konsinyasi', 'create');
        
        $data = array(
            'title' => 'Tambah Konsinyasi Masuk',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Konsinyasi Masuk', 'url' => base_url('konsinyasi/masuk')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Konsinyasi Masuk',
            'form_action' => base_url('konsinyasi/masuk/store'),
            'back_url' => 'konsinyasi/masuk',
            'suppliers' => $this->supplier_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
        );
        
        $this->render('konsinyasi/masuk/create', $data);
    }
    
    /**
     * Store - Save new record
     */
    public function store() {
        $this->check_permission('konsinyasi', 'create');
        
        // Validation
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('gudang_id', 'Gudang', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Konsinyasi Masuk',
                'breadcrumb' => array(
                    array('label' => 'Konsinyasi', 'url' => '#'),
                    array('label' => 'Konsinyasi Masuk', 'url' => base_url('konsinyasi/masuk')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Konsinyasi Masuk',
                'form_action' => base_url('konsinyasi/masuk/store'),
                'back_url' => 'konsinyasi/masuk',
                'suppliers' => $this->supplier_model->get_all(array('is_active' => 1)),
                'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
                'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
            );
            $this->render('konsinyasi/masuk/create', $data);
            return;
        }
        
        $no_konsinyasi = $this->konsinyasi_masuk_model->generate_no_konsinyasi();
        
        $data = array(
            'no_konsinyasi' => $no_konsinyasi,
            'supplier_id' => $this->input->post('supplier_id'),
            'gudang_id' => $this->input->post('gudang_id'),
            'tanggal' => $this->input->post('tanggal'),
            'status' => 'draft',
            'keterangan' => $this->input->post('keterangan')
        );
        
        $insert_id = $this->konsinyasi_masuk_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('konsinyasi/masuk');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('konsinyasi/masuk/create');
        }
    }
    
    /**
     * View - Show single record
     */
    public function view($id) {
        $this->check_permission('konsinyasi', 'view');
        
        $data = $this->konsinyasi_masuk_model->get_by_id_with_relations($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('konsinyasi/masuk');
            return;
        }
        
        $view_data = array(
            'title' => 'Detail Konsinyasi Masuk',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Konsinyasi Masuk', 'url' => base_url('konsinyasi/masuk')),
                array('label' => 'Detail')
            ),
            'back_url' => 'konsinyasi/masuk',
            'data' => $data
        );
        
        $this->render('konsinyasi/masuk/view', $view_data);
    }
    
    /**
     * AJAX Get - Get single record
     */
    public function ajax_get($id) {
        $this->check_permission('konsinyasi', 'view');
        
        $data = $this->konsinyasi_masuk_model->get_by_id_with_relations($id);
        
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    /**
     * AJAX Save - Create or Update
     */
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('konsinyasi', 'update');
        } else {
            $this->check_permission('konsinyasi', 'create');
        }
        
        // Validation
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('gudang_id', 'Gudang', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        if ($id) {
            $data = array(
                'supplier_id' => $this->input->post('supplier_id'),
                'gudang_id' => $this->input->post('gudang_id'),
                'tanggal' => $this->input->post('tanggal'),
                'keterangan' => $this->input->post('keterangan')
            );
            
            if ($this->konsinyasi_masuk_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $no_konsinyasi = $this->konsinyasi_masuk_model->generate_no_konsinyasi();
            
            $data = array(
                'no_konsinyasi' => $no_konsinyasi,
                'supplier_id' => $this->input->post('supplier_id'),
                'gudang_id' => $this->input->post('gudang_id'),
                'tanggal' => $this->input->post('tanggal'),
                'status' => 'draft',
                'keterangan' => $this->input->post('keterangan')
            );
            
            $insert_id = $this->konsinyasi_masuk_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    /**
     * AJAX Delete
     */
    public function ajax_delete($id) {
        $this->check_permission('konsinyasi', 'delete');
        
        $data = $this->konsinyasi_masuk_model->get_by_id($id);
        if ($data && $data->status == 'draft') {
            if ($this->konsinyasi_masuk_model->delete($id)) {
                $this->ajax_response(true, 'Data berhasil dihapus');
            } else {
                $this->ajax_response(false, 'Gagal menghapus data');
            }
        } else {
            $this->ajax_response(false, 'Data tidak dapat dihapus karena sudah diproses');
        }
    }
}


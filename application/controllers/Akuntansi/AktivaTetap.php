<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AktivaTetap extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('aktiva_tetap_model');
        $this->check_permission('akuntansi', 'view');
    }
    
    /**
     * Index - List page
     */
    public function index() {
        $data = array(
            'title' => 'Aktiva Tetap',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Aktiva Tetap')
            )
        );
        
        $this->render('akuntansi/aktiva_tetap/index', $data);
    }
    
    /**
     * AJAX List - DataTables server-side
     */
    public function ajax_list() {
        $this->check_permission('akuntansi', 'view');
        
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'kategori', 'tanggal_beli', 'harga_beli', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'kategori', 'deskripsi'),
            'where' => array()
        );
        
        $result = $this->aktiva_tetap_model->datatable($params);
        
        // Format data
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->kategori ?: '-',
                $row->tanggal_beli ? date('d/m/Y', strtotime($row->tanggal_beli)) : '-',
                'Rp ' . number_format($row->harga_beli ?: 0, 0, ',', '.'),
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                date('d/m/Y H:i', strtotime($row->created_at ?: 'now')),
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        
        $this->datatable_json($result);
    }
    
    /**
     * Get action buttons
     */
    private function get_action_buttons($id) {
        $buttons = '';
        
        if ($this->auth->has_permission('akuntansi', 'view')) {
            $buttons .= '<a href="' . base_url('akuntansi/aktivatetap/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        
        if ($this->auth->has_permission('akuntansi', 'update')) {
            $buttons .= '<a href="' . base_url('akuntansi/aktivatetap/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        
        if ($this->auth->has_permission('akuntansi', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        
        return $buttons;
    }
    
    /**
     * Create - Show create form
     */
    public function create() {
        $this->check_permission('akuntansi', 'create');
        
        $data = array(
            'title' => 'Tambah Aktiva Tetap',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Aktiva Tetap', 'url' => base_url('akuntansi/aktivatetap')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Aktiva Tetap',
            'form_action' => base_url('akuntansi/aktivatetap/store'),
            'back_url' => 'akuntansi/aktivatetap'
        );
        
        $this->render('akuntansi/aktiva_tetap/create', $data);
    }
    
    /**
     * Store - Save new record
     */
    public function store() {
        $this->check_permission('akuntansi', 'create');
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|numeric');
        $this->form_validation->set_rules('tanggal_beli', 'Tanggal Beli', 'required');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Aktiva Tetap',
                'breadcrumb' => array(
                    array('label' => 'Akuntansi', 'url' => '#'),
                    array('label' => 'Aktiva Tetap', 'url' => base_url('akuntansi/aktivatetap')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Aktiva Tetap',
                'form_action' => base_url('akuntansi/aktivatetap/store'),
                'back_url' => 'akuntansi/aktivatetap'
            );
            $this->render('akuntansi/aktiva_tetap/create', $data);
            return;
        }
        
        // Check kode uniqueness
        if ($this->aktiva_tetap_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('akuntansi/aktivatetap/create');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'kategori' => $this->input->post('kategori'),
            'tanggal_beli' => $this->input->post('tanggal_beli'),
            'harga_beli' => $this->input->post('harga_beli'),
            'nilai_residu' => $this->input->post('nilai_residu') ?: 0,
            'umur_ekonomis' => $this->input->post('umur_ekonomis') ?: 0,
            'metode_depresiasi' => $this->input->post('metode_depresiasi') ?: 'garis_lurus',
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->aktiva_tetap_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('akuntansi/aktivatetap');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('akuntansi/aktivatetap/create');
        }
    }
    
    /**
     * Edit - Show edit form
     */
    public function edit($id) {
        $this->check_permission('akuntansi', 'update');
        
        $data = $this->aktiva_tetap_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('akuntansi/aktivatetap');
            return;
        }
        
        $view_data = array(
            'title' => 'Edit Aktiva Tetap',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Aktiva Tetap', 'url' => base_url('akuntansi/aktivatetap')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Aktiva Tetap',
            'form_action' => base_url('akuntansi/aktivatetap/update/' . $id),
            'back_url' => 'akuntansi/aktivatetap',
            'data' => $data
        );
        
        $this->render('akuntansi/aktiva_tetap/edit', $view_data);
    }
    
    /**
     * Update - Update existing record
     */
    public function update($id) {
        $this->check_permission('akuntansi', 'update');
        
        $data = $this->aktiva_tetap_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('akuntansi/aktivatetap');
            return;
        }
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|numeric');
        $this->form_validation->set_rules('tanggal_beli', 'Tanggal Beli', 'required');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Aktiva Tetap',
                'breadcrumb' => array(
                    array('label' => 'Akuntansi', 'url' => '#'),
                    array('label' => 'Aktiva Tetap', 'url' => base_url('akuntansi/aktivatetap')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Aktiva Tetap',
                'form_action' => base_url('akuntansi/aktivatetap/update/' . $id),
                'back_url' => 'akuntansi/aktivatetap',
                'data' => $data
            );
            $this->render('akuntansi/aktiva_tetap/edit', $view_data);
            return;
        }
        
        // Check kode uniqueness
        if ($this->aktiva_tetap_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('akuntansi/aktivatetap/edit/' . $id);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'kategori' => $this->input->post('kategori'),
            'tanggal_beli' => $this->input->post('tanggal_beli'),
            'harga_beli' => $this->input->post('harga_beli'),
            'nilai_residu' => $this->input->post('nilai_residu') ?: 0,
            'umur_ekonomis' => $this->input->post('umur_ekonomis') ?: 0,
            'metode_depresiasi' => $this->input->post('metode_depresiasi') ?: 'garis_lurus',
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->aktiva_tetap_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('akuntansi/aktivatetap');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('akuntansi/aktivatetap/edit/' . $id);
        }
    }
    
    /**
     * View - Show single record
     */
    public function view($id) {
        $this->check_permission('akuntansi', 'view');
        
        $data = $this->aktiva_tetap_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('akuntansi/aktivatetap');
            return;
        }
        
        $view_data = array(
            'title' => 'Detail Aktiva Tetap',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Aktiva Tetap', 'url' => base_url('akuntansi/aktivatetap')),
                array('label' => 'Detail')
            ),
            'back_url' => 'akuntansi/aktivatetap',
            'data' => $data
        );
        
        $this->render('akuntansi/aktiva_tetap/view', $view_data);
    }
    
    /**
     * AJAX Get - Get single record
     */
    public function ajax_get($id) {
        $this->check_permission('akuntansi', 'view');
        
        $data = $this->aktiva_tetap_model->get_by_id($id);
        
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
            $this->check_permission('akuntansi', 'update');
        } else {
            $this->check_permission('akuntansi', 'create');
        }
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|numeric');
        $this->form_validation->set_rules('tanggal_beli', 'Tanggal Beli', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        // Check kode uniqueness
        if ($this->aktiva_tetap_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'kategori' => $this->input->post('kategori'),
            'tanggal_beli' => $this->input->post('tanggal_beli'),
            'harga_beli' => $this->input->post('harga_beli'),
            'nilai_residu' => $this->input->post('nilai_residu') ?: 0,
            'umur_ekonomis' => $this->input->post('umur_ekonomis') ?: 0,
            'metode_depresiasi' => $this->input->post('metode_depresiasi') ?: 'garis_lurus',
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            // Update
            if ($this->aktiva_tetap_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            // Insert
            $insert_id = $this->aktiva_tetap_model->insert($data);
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
        $this->check_permission('akuntansi', 'delete');
        
        if ($this->aktiva_tetap_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


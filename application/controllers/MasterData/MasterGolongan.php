<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterGolongan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_golongan_model');
        $this->check_permission('master', 'view');
    }
    
    /**
     * Index - List page
     */
    public function index() {
        $data = array(
            'title' => 'Master Golongan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Golongan')
            )
        );
        
        $this->render('master_data/golongan/index', $data);
    }
    
    /**
     * AJAX List - DataTables server-side
     */
    public function ajax_list() {
        $this->check_permission('master', 'view');
        
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'deskripsi', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'deskripsi'),
            'where' => array()
        );
        
        $result = $this->master_golongan_model->datatable($params);
        
        // Format data
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->deskripsi,
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
        
        if ($this->auth->has_permission('master', 'view')) {
            $buttons .= '<a href="' . base_url('masterdata/mastergolongan/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        
        if ($this->auth->has_permission('master', 'update')) {
            $buttons .= '<a href="' . base_url('masterdata/mastergolongan/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        
        if ($this->auth->has_permission('master', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        
        return $buttons;
    }
    
    /**
     * Create - Show create form
     */
    public function create() {
        $this->check_permission('master', 'create');
        
        $data = array(
            'title' => 'Tambah Master Golongan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Golongan', 'url' => base_url('masterdata/mastergolongan')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Golongan',
            'form_action' => base_url('masterdata/mastergolongan/store'),
            'back_url' => 'masterdata/mastergolongan'
        );
        
        $this->render('master_data/golongan/create', $data);
    }
    
    /**
     * Store - Save new record
     */
    public function store() {
        $this->check_permission('master', 'create');
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Master Golongan',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Golongan', 'url' => base_url('masterdata/mastergolongan')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Golongan',
                'form_action' => base_url('masterdata/mastergolongan/store'),
                'back_url' => 'masterdata/mastergolongan'
            );
            $this->render('master_data/golongan/create', $data);
            return;
        }
        
        // Check kode uniqueness
        if ($this->master_golongan_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastergolongan/create');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->master_golongan_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/mastergolongan');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('masterdata/mastergolongan/create');
        }
    }
    
    /**
     * Edit - Show edit form
     */
    public function edit($id) {
        $this->check_permission('master', 'update');
        
        $data = $this->master_golongan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastergolongan');
            return;
        }
        
        $view_data = array(
            'title' => 'Edit Master Golongan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Golongan', 'url' => base_url('masterdata/mastergolongan')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Golongan',
            'form_action' => base_url('masterdata/mastergolongan/update/' . $id),
            'back_url' => 'masterdata/mastergolongan',
            'data' => $data
        );
        
        $this->render('master_data/golongan/edit', $view_data);
    }
    
    /**
     * Update - Update existing record
     */
    public function update($id) {
        $this->check_permission('master', 'update');
        
        $data = $this->master_golongan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastergolongan');
            return;
        }
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Master Golongan',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Golongan', 'url' => base_url('masterdata/mastergolongan')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Golongan',
                'form_action' => base_url('masterdata/mastergolongan/update/' . $id),
                'back_url' => 'masterdata/mastergolongan',
                'data' => $data
            );
            $this->render('master_data/golongan/edit', $view_data);
            return;
        }
        
        // Check kode uniqueness
        if ($this->master_golongan_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastergolongan/edit/' . $id);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->master_golongan_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/mastergolongan');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/mastergolongan/edit/' . $id);
        }
    }
    
    /**
     * View - Show single record
     */
    public function view($id) {
        $this->check_permission('master', 'view');
        
        $data = $this->master_golongan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastergolongan');
            return;
        }
        
        $view_data = array(
            'title' => 'Detail Master Golongan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Golongan', 'url' => base_url('masterdata/mastergolongan')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/mastergolongan',
            'data' => $data
        );
        
        $this->render('master_data/golongan/view', $view_data);
    }
    
    /**
     * AJAX Get - Get single record
     */
    public function ajax_get($id) {
        $this->check_permission('master', 'view');
        
        $data = $this->master_golongan_model->get_by_id($id);
        
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
            $this->check_permission('master', 'update');
        } else {
            $this->check_permission('master', 'create');
        }
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        // Check kode uniqueness
        if ($this->master_golongan_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            // Update
            if ($this->master_golongan_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            // Insert
            $insert_id = $this->master_golongan_model->insert($data);
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
        $this->check_permission('master', 'delete');

        if ($this->master_golongan_model->is_in_use($id)) {
            $this->ajax_response(false, 'Data tidak dapat dihapus karena masih digunakan');
            return;
        }
        
        if ($this->master_golongan_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    /**
     * AJAX Search - For autocomplete
     */
    public function ajax_search() {
        $search = $this->input->get('q');
        $data = $this->master_golongan_model->get_all_filtered($search, 1);
        
        $results = array();
        foreach ($data as $row) {
            $results[] = array(
                'id' => $row->id,
                'text' => $row->nama . ' (' . $row->kode . ')'
            );
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('results' => $results)));
    }
}

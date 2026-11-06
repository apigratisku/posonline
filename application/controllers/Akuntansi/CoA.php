<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CoA extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('coa_model');
        $this->check_permission('akuntansi', 'view');
    }
    
    /**
     * Index - List page
     */
    public function index() {
        $data = array(
            'title' => 'Master CoA',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Master CoA')
            )
        );
        
        $this->render('akuntansi/coa/index', $data);
    }
    
    /**
     * AJAX List - DataTables server-side
     */
    public function ajax_list() {
        $this->check_permission('akuntansi', 'view');
        
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'tipe', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'deskripsi', 'tipe'),
            'where' => array()
        );
        
        $result = $this->coa_model->datatable($params);
        
        // Format data
        $data = array();
        foreach ($result['data'] as $row) {
            $tipe_badge = '<span class="badge bg-';
            switch($row->tipe) {
                case 'aktiva':
                    $tipe_badge .= 'primary';
                    break;
                case 'pasiva':
                    $tipe_badge .= 'warning';
                    break;
                case 'modal':
                    $tipe_badge .= 'success';
                    break;
                case 'pendapatan':
                    $tipe_badge .= 'info';
                    break;
                case 'beban':
                    $tipe_badge .= 'danger';
                    break;
                default:
                    $tipe_badge .= 'secondary';
            }
            $tipe_badge .= '">' . ucfirst($row->tipe ?: '-') . '</span>';
            
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $tipe_badge,
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
            $buttons .= '<a href="' . base_url('akuntansi/coa/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        
        if ($this->auth->has_permission('akuntansi', 'update')) {
            $buttons .= '<a href="' . base_url('akuntansi/coa/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
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
            'title' => 'Tambah Master CoA',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Master CoA', 'url' => base_url('akuntansi/coa')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah CoA',
            'form_action' => base_url('akuntansi/coa/store'),
            'back_url' => 'akuntansi/coa',
            'coa_list' => $this->coa_model->get_all(array('is_active' => 1))
        );
        
        $this->render('akuntansi/coa/create', $data);
    }
    
    /**
     * Store - Save new record
     */
    public function store() {
        $this->check_permission('akuntansi', 'create');
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('tipe', 'Tipe', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Master CoA',
                'breadcrumb' => array(
                    array('label' => 'Akuntansi', 'url' => '#'),
                    array('label' => 'Master CoA', 'url' => base_url('akuntansi/coa')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah CoA',
                'form_action' => base_url('akuntansi/coa/store'),
                'back_url' => 'akuntansi/coa',
                'coa_list' => $this->coa_model->get_all(array('is_active' => 1))
            );
            $this->render('akuntansi/coa/create', $data);
            return;
        }
        
        // Check kode uniqueness
        if ($this->coa_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('akuntansi/coa/create');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe'),
            'parent_id' => $this->input->post('parent_id') ?: null,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        $insert_id = $this->coa_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('akuntansi/coa');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('akuntansi/coa/create');
        }
    }
    
    /**
     * Edit - Show edit form
     */
    public function edit($id) {
        $this->check_permission('akuntansi', 'update');
        
        $data = $this->coa_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('akuntansi/coa');
            return;
        }
        
        $view_data = array(
            'title' => 'Edit Master CoA',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Master CoA', 'url' => base_url('akuntansi/coa')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit CoA',
            'form_action' => base_url('akuntansi/coa/update/' . $id),
            'back_url' => 'akuntansi/coa',
            'data' => $data,
            'coa_list' => $this->coa_model->get_all(array('is_active' => 1, 'id !=' => $id))
        );
        
        $this->render('akuntansi/coa/edit', $view_data);
    }
    
    /**
     * Update - Update existing record
     */
    public function update($id) {
        $this->check_permission('akuntansi', 'update');
        
        $data = $this->coa_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('akuntansi/coa');
            return;
        }
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('tipe', 'Tipe', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Master CoA',
                'breadcrumb' => array(
                    array('label' => 'Akuntansi', 'url' => '#'),
                    array('label' => 'Master CoA', 'url' => base_url('akuntansi/coa')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit CoA',
                'form_action' => base_url('akuntansi/coa/update/' . $id),
                'back_url' => 'akuntansi/coa',
                'data' => $data,
                'coa_list' => $this->coa_model->get_all(array('is_active' => 1, 'id !=' => $id))
            );
            $this->render('akuntansi/coa/edit', $view_data);
            return;
        }
        
        // Check kode uniqueness
        if ($this->coa_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('akuntansi/coa/edit/' . $id);
            return;
        }
        
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe'),
            'parent_id' => $this->input->post('parent_id') ?: null,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($this->coa_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('akuntansi/coa');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('akuntansi/coa/edit/' . $id);
        }
    }
    
    /**
     * View - Show single record
     */
    public function view($id) {
        $this->check_permission('akuntansi', 'view');
        
        $data = $this->coa_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('akuntansi/coa');
            return;
        }
        
        $view_data = array(
            'title' => 'Detail Master CoA',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Master CoA', 'url' => base_url('akuntansi/coa')),
                array('label' => 'Detail')
            ),
            'back_url' => 'akuntansi/coa',
            'data' => $data
        );
        
        $this->render('akuntansi/coa/view', $view_data);
    }
    
    /**
     * AJAX Get - Get single record
     */
    public function ajax_get($id) {
        $this->check_permission('akuntansi', 'view');
        
        $data = $this->coa_model->get_by_id($id);
        
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
        $this->form_validation->set_rules('tipe', 'Tipe', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        // Check kode uniqueness
        if ($this->coa_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'tipe' => $this->input->post('tipe'),
            'parent_id' => $this->input->post('parent_id') ?: null,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            // Update
            if ($this->coa_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            // Insert
            $insert_id = $this->coa_model->insert($data);
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
        
        if ($this->coa_model->delete($id)) {
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
        $data = $this->coa_model->get_all_filtered($search, 1);
        
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


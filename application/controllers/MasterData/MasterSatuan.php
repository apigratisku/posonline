<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterSatuan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_satuan_model');
        $this->check_permission('master', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Master Satuan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Satuan')
            )
        );
        $this->render('master_data/satuan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('master', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'deskripsi', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'deskripsi')
        );
        $result = $this->master_satuan_model->datatable($params);
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
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('master', 'view')) {
            $buttons .= '<a href="' . base_url('masterdata/mastersatuan/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'update')) {
            $buttons .= '<a href="' . base_url('masterdata/mastersatuan/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('master', 'create');
        $data = array(
            'title' => 'Tambah Master Satuan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Satuan', 'url' => base_url('masterdata/mastersatuan')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Satuan',
            'form_action' => base_url('masterdata/mastersatuan/store'),
            'back_url' => 'masterdata/mastersatuan'
        );
        $this->render('master_data/satuan/create', $data);
    }
    
    public function store() {
        $this->check_permission('master', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $this->set_flash('error', 'Validasi gagal. Periksa kembali form.');
            $data = array(
                'title' => 'Tambah Master Satuan',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Satuan', 'url' => base_url('masterdata/mastersatuan')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Satuan',
                'form_action' => base_url('masterdata/mastersatuan/store'),
                'back_url' => 'masterdata/mastersatuan'
            );
            $this->render('master_data/satuan/create', $data);
            return;
        }
        if ($this->master_satuan_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastersatuan/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->master_satuan_model->insert($data);
        $saved = $insert_id ? $this->master_satuan_model->get_by_id($insert_id) : null;
        if ($saved) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/mastersatuan');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data. Periksa log aplikasi untuk detail.');
            redirect('masterdata/mastersatuan/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_satuan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastersatuan');
            return;
        }
        $view_data = array(
            'title' => 'Edit Master Satuan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Satuan', 'url' => base_url('masterdata/mastersatuan')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Satuan',
            'form_action' => base_url('masterdata/mastersatuan/update/' . $id),
            'back_url' => 'masterdata/mastersatuan',
            'data' => $data
        );
        $this->render('master_data/satuan/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_satuan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastersatuan');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Master Satuan',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Satuan', 'url' => base_url('masterdata/mastersatuan')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Satuan',
                'form_action' => base_url('masterdata/mastersatuan/update/' . $id),
                'back_url' => 'masterdata/mastersatuan',
                'data' => $data
            );
            $this->render('master_data/satuan/edit', $view_data);
            return;
        }
        if ($this->master_satuan_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastersatuan/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->master_satuan_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/mastersatuan');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/mastersatuan/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_satuan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastersatuan');
            return;
        }
        $view_data = array(
            'title' => 'Detail Master Satuan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Satuan', 'url' => base_url('masterdata/mastersatuan')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/mastersatuan',
            'data' => $data
        );
        $this->render('master_data/satuan/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_satuan_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('master', 'update');
        } else {
            $this->check_permission('master', 'create');
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        if ($this->master_satuan_model->kode_exists($this->input->post('kode'), $id)) {
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
            if ($this->master_satuan_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->master_satuan_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('master', 'delete');
        if ($this->master_satuan_model->is_in_use($id)) {
            $this->ajax_response(false, 'Data tidak dapat dihapus karena masih digunakan');
            return;
        }
        if ($this->master_satuan_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    public function ajax_search() {
        $search = $this->input->get('q');
        $data = $this->master_satuan_model->get_all_filtered($search, 1);
        $results = array();
        foreach ($data as $row) {
            $results[] = array(
                'id' => $row->id,
                'text' => $row->nama . ' (' . $row->kode . ')'
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('results' => $results)));
    }
}


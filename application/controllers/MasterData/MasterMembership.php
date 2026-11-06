<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterMembership extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_membership_model');
        $this->check_permission('master', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Master Membership',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Membership')
            )
        );
        $this->render('master_data/membership/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('master', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'diskon_persen', 'minimal_poin', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama')
        );
        $result = $this->master_membership_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                number_format($row->diskon_persen ?: 0, 2, ',', '.') . '%',
                number_format($row->minimal_poin ?: 0, 0, ',', '.'),
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
            $buttons .= '<a href="' . base_url('masterdata/mastermembership/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'update')) {
            $buttons .= '<a href="' . base_url('masterdata/mastermembership/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('master', 'create');
        $data = array(
            'title' => 'Tambah Master Membership',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Membership', 'url' => base_url('masterdata/mastermembership')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Membership',
            'form_action' => base_url('masterdata/mastermembership/store'),
            'back_url' => 'masterdata/mastermembership'
        );
        $this->render('master_data/membership/create', $data);
    }
    
    public function store() {
        $this->check_permission('master', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Master Membership',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Membership', 'url' => base_url('masterdata/mastermembership')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Membership',
                'form_action' => base_url('masterdata/mastermembership/store'),
                'back_url' => 'masterdata/mastermembership'
            );
            $this->render('master_data/membership/create', $data);
            return;
        }
        if ($this->master_membership_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastermembership/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'diskon_persen' => $this->input->post('diskon_persen') ?: 0,
            'minimal_poin' => $this->input->post('minimal_poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->master_membership_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/mastermembership');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('masterdata/mastermembership/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_membership_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastermembership');
            return;
        }
        $view_data = array(
            'title' => 'Edit Master Membership',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Membership', 'url' => base_url('masterdata/mastermembership')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Membership',
            'form_action' => base_url('masterdata/mastermembership/update/' . $id),
            'back_url' => 'masterdata/mastermembership',
            'data' => $data
        );
        $this->render('master_data/membership/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_membership_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastermembership');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Master Membership',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Membership', 'url' => base_url('masterdata/mastermembership')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Membership',
                'form_action' => base_url('masterdata/mastermembership/update/' . $id),
                'back_url' => 'masterdata/mastermembership',
                'data' => $data
            );
            $this->render('master_data/membership/edit', $view_data);
            return;
        }
        if ($this->master_membership_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/mastermembership/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'diskon_persen' => $this->input->post('diskon_persen') ?: 0,
            'minimal_poin' => $this->input->post('minimal_poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->master_membership_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/mastermembership');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/mastermembership/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_membership_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/mastermembership');
            return;
        }
        $view_data = array(
            'title' => 'Detail Master Membership',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Membership', 'url' => base_url('masterdata/mastermembership')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/mastermembership',
            'data' => $data
        );
        $this->render('master_data/membership/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_membership_model->get_by_id($id);
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
        if ($this->master_membership_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'diskon_persen' => $this->input->post('diskon_persen') ?: 0,
            'minimal_poin' => $this->input->post('minimal_poin') ?: 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($id) {
            if ($this->master_membership_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->master_membership_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('master', 'delete');
        if ($this->master_membership_model->is_in_use($id)) {
            $this->ajax_response(false, 'Data tidak dapat dihapus karena masih digunakan');
            return;
        }
        if ($this->master_membership_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    public function ajax_search() {
        $search = $this->input->get('q');
        $this->db->from('master_membership');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode', $search);
            $this->db->or_like('nama', $search);
            $this->db->group_end();
        }
        $this->db->where('is_active', 1);
        $data = $this->db->get()->result();
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


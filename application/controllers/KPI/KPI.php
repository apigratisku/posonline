<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KPI extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('kpi_model');
        $this->load->model('user_model');
        $this->check_permission('kpi', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'KPI Karyawan',
            'breadcrumb' => array(
                array('label' => 'KPI', 'url' => '#'),
                array('label' => 'KPI Karyawan')
            ),
            'users' => $this->user_model->get_all(array('is_active' => 1))
        );
        $this->render('kpi/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('kpi', 'view');
        $params = array(
            'columns' => array('k.id', 'u.nama_lengkap', 'k.bulan', 'k.target', 'k.pencapaian', 'k.persen', 'k.status'),
            'search_fields' => array('u.nama_lengkap', 'k.keterangan'),
            'default_order' => 'k.bulan',
            'build_query' => function () {
                $this->db->select('k.*, u.nama_lengkap as user_nama');
                $this->db->from('kpi k');
                $this->db->join('users u', 'u.id = k.user_id', 'left');
            },
            'apply_filters' => function () {
                $bulan = $this->input->post('bulan');
                $tahun = $this->input->post('tahun');
                if ($bulan) {
                    $this->db->where('MONTH(k.bulan)', $bulan);
                }
                if ($tahun) {
                    $this->db->where('YEAR(k.bulan)', $tahun);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->user_nama ?: '-',
                date('F Y', strtotime($row->bulan ?: 'now')),
                number_format($row->target ?: 0, 0, ',', '.'),
                number_format($row->pencapaian ?: 0, 0, ',', '.'),
                number_format($row->target > 0 ? ($row->pencapaian / $row->target * 100) : 0, 2) . '%',
                $row->status == 'selesai' ? '<span class="badge bg-success">Selesai</span>' : '<span class="badge bg-warning">Proses</span>',
                $this->get_action_buttons($row->id)
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('kpi', 'view')) {
            $buttons .= '<a href="' . base_url('kpi/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('kpi', 'update')) {
            $buttons .= '<a href="' . base_url('kpi/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('kpi', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('kpi', 'create');
        $data = array(
            'title' => 'Tambah KPI',
            'breadcrumb' => array(
                array('label' => 'KPI', 'url' => '#'),
                array('label' => 'KPI Karyawan', 'url' => base_url('kpi')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah KPI',
            'form_action' => base_url('kpi/store'),
            'back_url' => 'kpi',
            'users' => $this->user_model->get_all(array('is_active' => 1))
        );
        $this->render('kpi/create', $data);
    }
    
    public function store() {
        $this->check_permission('kpi', 'create');
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('target', 'Target', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah KPI',
                'breadcrumb' => array(
                    array('label' => 'KPI', 'url' => '#'),
                    array('label' => 'KPI Karyawan', 'url' => base_url('kpi')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah KPI',
                'form_action' => base_url('kpi/store'),
                'back_url' => 'kpi',
                'users' => $this->user_model->get_all(array('is_active' => 1))
            );
            $this->render('kpi/create', $data);
            return;
        }
        
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'bulan' => $this->input->post('bulan'),
            'target' => $this->input->post('target') ?: 0,
            'pencapaian' => $this->input->post('pencapaian') ?: 0,
            'keterangan' => $this->input->post('keterangan'),
            'status' => $this->input->post('status') ?: 'proses'
        );
        
        $insert_id = $this->kpi_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('kpi');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('kpi/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('kpi', 'update');
        $data = $this->kpi_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kpi');
            return;
        }
        $view_data = array(
            'title' => 'Edit KPI',
            'breadcrumb' => array(
                array('label' => 'KPI', 'url' => '#'),
                array('label' => 'KPI Karyawan', 'url' => base_url('kpi')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit KPI',
            'form_action' => base_url('kpi/update/' . $id),
            'back_url' => 'kpi',
            'data' => $data,
            'users' => $this->user_model->get_all(array('is_active' => 1))
        );
        $this->render('kpi/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('kpi', 'update');
        $data = $this->kpi_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kpi');
            return;
        }
        
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('target', 'Target', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit KPI',
                'breadcrumb' => array(
                    array('label' => 'KPI', 'url' => '#'),
                    array('label' => 'KPI Karyawan', 'url' => base_url('kpi')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit KPI',
                'form_action' => base_url('kpi/update/' . $id),
                'back_url' => 'kpi',
                'data' => $data,
                'users' => $this->user_model->get_all(array('is_active' => 1))
            );
            $this->render('kpi/edit', $view_data);
            return;
        }
        
        $update_data = array(
            'user_id' => $this->input->post('user_id'),
            'bulan' => $this->input->post('bulan'),
            'target' => $this->input->post('target') ?: 0,
            'pencapaian' => $this->input->post('pencapaian') ?: 0,
            'keterangan' => $this->input->post('keterangan'),
            'status' => $this->input->post('status') ?: 'proses'
        );
        
        if ($this->kpi_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('kpi');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('kpi/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('kpi', 'view');
        $data = $this->kpi_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('kpi');
            return;
        }
        $user = $this->user_model->get_by_id($data->user_id);
        $view_data = array(
            'title' => 'Detail KPI',
            'breadcrumb' => array(
                array('label' => 'KPI', 'url' => '#'),
                array('label' => 'KPI Karyawan', 'url' => base_url('kpi')),
                array('label' => 'Detail')
            ),
            'back_url' => 'kpi',
            'data' => $data,
            'user' => $user
        );
        $this->render('kpi/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('kpi', 'view');
        $data = $this->kpi_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('kpi', 'update');
        } else {
            $this->check_permission('kpi', 'create');
        }
        
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('target', 'Target', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'bulan' => $this->input->post('bulan'),
            'target' => $this->input->post('target') ?: 0,
            'pencapaian' => $this->input->post('pencapaian') ?: 0,
            'keterangan' => $this->input->post('keterangan'),
            'status' => $this->input->post('status') ?: 'proses'
        );
        
        if ($id) {
            if ($this->kpi_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->kpi_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('kpi', 'delete');
        if ($this->kpi_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


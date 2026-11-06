<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gaji extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('gaji_model');
        $this->load->model('user_model');
        $this->check_permission('gaji', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Gaji Karyawan',
            'breadcrumb' => array(
                array('label' => 'Gaji', 'url' => '#'),
                array('label' => 'Gaji Karyawan')
            ),
            'users' => $this->user_model->get_all(array('is_active' => 1))
        );
        $this->render('gaji/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('gaji', 'view');
        $params = array(
            'columns' => array('g.id', 'u.nama_lengkap', 'g.bulan', 'g.gaji_pokok', 'g.tunjangan', 'g.potongan', 'g.total', 'g.status'),
            'search_fields' => array('u.nama_lengkap', 'g.keterangan'),
            'default_order' => 'g.bulan',
            'build_query' => function () {
                $this->db->select('g.*, u.nama_lengkap as user_nama');
                $this->db->from('gaji g');
                $this->db->join('users u', 'u.id = g.user_id', 'left');
            },
            'apply_filters' => function () {
                $bulan = $this->input->post('bulan');
                $tahun = $this->input->post('tahun');
                if ($bulan) {
                    $this->db->where('MONTH(g.bulan)', $bulan);
                }
                if ($tahun) {
                    $this->db->where('YEAR(g.bulan)', $tahun);
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
                'Rp ' . number_format($row->gaji_pokok ?: 0, 0, ',', '.'),
                'Rp ' . number_format($row->tunjangan ?: 0, 0, ',', '.'),
                'Rp ' . number_format($row->potongan ?: 0, 0, ',', '.'),
                'Rp ' . number_format(($row->gaji_pokok ?: 0) + ($row->tunjangan ?: 0) - ($row->potongan ?: 0), 0, ',', '.'),
                $row->status == 'dibayar' ? '<span class="badge bg-success">Dibayar</span>' : '<span class="badge bg-warning">Belum Dibayar</span>',
                $this->get_action_buttons($row->id)
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('gaji', 'view')) {
            $buttons .= '<a href="' . base_url('gaji/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('gaji', 'update')) {
            $buttons .= '<a href="' . base_url('gaji/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('gaji', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function create() {
        $this->check_permission('gaji', 'create');
        $data = array(
            'title' => 'Tambah Gaji',
            'breadcrumb' => array(
                array('label' => 'Gaji', 'url' => '#'),
                array('label' => 'Gaji Karyawan', 'url' => base_url('gaji')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Gaji',
            'form_action' => base_url('gaji/store'),
            'back_url' => 'gaji',
            'users' => $this->user_model->get_all(array('is_active' => 1))
        );
        $this->render('gaji/create', $data);
    }
    
    public function store() {
        $this->check_permission('gaji', 'create');
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Gaji',
                'breadcrumb' => array(
                    array('label' => 'Gaji', 'url' => '#'),
                    array('label' => 'Gaji Karyawan', 'url' => base_url('gaji')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Gaji',
                'form_action' => base_url('gaji/store'),
                'back_url' => 'gaji',
                'users' => $this->user_model->get_all(array('is_active' => 1))
            );
            $this->render('gaji/create', $data);
            return;
        }
        
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'bulan' => $this->input->post('bulan'),
            'gaji_pokok' => $this->input->post('gaji_pokok') ?: 0,
            'tunjangan' => $this->input->post('tunjangan') ?: 0,
            'potongan' => $this->input->post('potongan') ?: 0,
            'keterangan' => $this->input->post('keterangan'),
            'status' => $this->input->post('status') ?: 'belum_dibayar'
        );
        
        $insert_id = $this->gaji_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('gaji');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('gaji/create');
        }
    }
    
    public function edit($id) {
        $this->check_permission('gaji', 'update');
        $data = $this->gaji_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('gaji');
            return;
        }
        $view_data = array(
            'title' => 'Edit Gaji',
            'breadcrumb' => array(
                array('label' => 'Gaji', 'url' => '#'),
                array('label' => 'Gaji Karyawan', 'url' => base_url('gaji')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Gaji',
            'form_action' => base_url('gaji/update/' . $id),
            'back_url' => 'gaji',
            'data' => $data,
            'users' => $this->user_model->get_all(array('is_active' => 1))
        );
        $this->render('gaji/edit', $view_data);
    }
    
    public function update($id) {
        $this->check_permission('gaji', 'update');
        $data = $this->gaji_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('gaji');
            return;
        }
        
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $view_data = array(
                'title' => 'Edit Gaji',
                'breadcrumb' => array(
                    array('label' => 'Gaji', 'url' => '#'),
                    array('label' => 'Gaji Karyawan', 'url' => base_url('gaji')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Gaji',
                'form_action' => base_url('gaji/update/' . $id),
                'back_url' => 'gaji',
                'data' => $data,
                'users' => $this->user_model->get_all(array('is_active' => 1))
            );
            $this->render('gaji/edit', $view_data);
            return;
        }
        
        $update_data = array(
            'user_id' => $this->input->post('user_id'),
            'bulan' => $this->input->post('bulan'),
            'gaji_pokok' => $this->input->post('gaji_pokok') ?: 0,
            'tunjangan' => $this->input->post('tunjangan') ?: 0,
            'potongan' => $this->input->post('potongan') ?: 0,
            'keterangan' => $this->input->post('keterangan'),
            'status' => $this->input->post('status') ?: 'belum_dibayar'
        );
        
        if ($this->gaji_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('gaji');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('gaji/edit/' . $id);
        }
    }
    
    public function view($id) {
        $this->check_permission('gaji', 'view');
        $data = $this->gaji_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('gaji');
            return;
        }
        $user = $this->user_model->get_by_id($data->user_id);
        $view_data = array(
            'title' => 'Detail Gaji',
            'breadcrumb' => array(
                array('label' => 'Gaji', 'url' => '#'),
                array('label' => 'Gaji Karyawan', 'url' => base_url('gaji')),
                array('label' => 'Detail')
            ),
            'back_url' => 'gaji',
            'data' => $data,
            'user' => $user
        );
        $this->render('gaji/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('gaji', 'view');
        $data = $this->gaji_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        if ($id) {
            $this->check_permission('gaji', 'update');
        } else {
            $this->check_permission('gaji', 'create');
        }
        
        $this->form_validation->set_rules('user_id', 'Karyawan', 'required');
        $this->form_validation->set_rules('bulan', 'Bulan', 'required');
        $this->form_validation->set_rules('gaji_pokok', 'Gaji Pokok', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'user_id' => $this->input->post('user_id'),
            'bulan' => $this->input->post('bulan'),
            'gaji_pokok' => $this->input->post('gaji_pokok') ?: 0,
            'tunjangan' => $this->input->post('tunjangan') ?: 0,
            'potongan' => $this->input->post('potongan') ?: 0,
            'keterangan' => $this->input->post('keterangan'),
            'status' => $this->input->post('status') ?: 'belum_dibayar'
        );
        
        if ($id) {
            if ($this->gaji_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->gaji_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('gaji', 'delete');
        if ($this->gaji_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


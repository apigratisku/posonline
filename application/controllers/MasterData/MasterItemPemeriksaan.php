<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterItemPemeriksaan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('item_pemeriksaan_model');
        $this->load->model('master_satuan_model');
        $this->check_permission('master', 'view');
    }

    public function index() {
        $data = array(
            'title' => 'Item Pemeriksaan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Item Pemeriksaan')
            )
        );
        $this->render('master_data/item_pemeriksaan/index', $data);
    }

    public function ajax_list() {
        $this->check_permission('master', 'view');
        $params = array(
            'columns' => array('id', 'kode', 'nama', 'satuan', 'harga', 'deskripsi', 'is_active', 'created_at'),
            'search_fields' => array('kode', 'nama', 'deskripsi'),
            'where' => array()
        );
        $result = $this->item_pemeriksaan_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->satuan,
                number_format($row->harga, 0, ',', '.'),
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
            $buttons .= '<a href="' . base_url('masterdata/masteritempemeriksaan/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'update')) {
            $buttons .= '<a href="' . base_url('masterdata/masteritempemeriksaan/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('master', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }

    public function create() {
        $this->check_permission('master', 'create');
        $data = array(
            'title' => 'Tambah Item Pemeriksaan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Item Pemeriksaan', 'url' => base_url('masterdata/masteritempemeriksaan')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Item Pemeriksaan',
            'form_action' => base_url('masterdata/masteritempemeriksaan/store'),
            'back_url' => 'masterdata/masteritempemeriksaan',
            'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
        );
        $this->render('master_data/item_pemeriksaan/create', $data);
    }

    public function store() {
        $this->check_permission('master', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            redirect('masterdata/masteritempemeriksaan/create');
            return;
        }
        if ($this->item_pemeriksaan_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/masteritempemeriksaan/create');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'satuan' => $this->input->post('satuan'),
            'harga' => $this->input->post('harga') ?: 0,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $insert_id = $this->item_pemeriksaan_model->insert($data);
        if ($insert_id) {
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/masteritempemeriksaan');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('masterdata/masteritempemeriksaan/create');
        }
    }

    public function edit($id) {
        $this->check_permission('master', 'update');
        $data = $this->item_pemeriksaan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/masteritempemeriksaan');
            return;
        }
        $view_data = array(
            'title' => 'Edit Item Pemeriksaan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Item Pemeriksaan', 'url' => base_url('masterdata/masteritempemeriksaan')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Item Pemeriksaan',
            'form_action' => base_url('masterdata/masteritempemeriksaan/update/' . $id),
            'back_url' => 'masterdata/masteritempemeriksaan',
            'data' => $data,
            'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
        );
        $this->render('master_data/item_pemeriksaan/edit', $view_data);
    }

    public function update($id) {
        $this->check_permission('master', 'update');
        $data = $this->item_pemeriksaan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/masteritempemeriksaan');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            redirect('masterdata/masteritempemeriksaan/edit/' . $id);
            return;
        }
        if ($this->item_pemeriksaan_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/masteritempemeriksaan/edit/' . $id);
            return;
        }
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'satuan' => $this->input->post('satuan'),
            'harga' => $this->input->post('harga') ?: 0,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->item_pemeriksaan_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/masteritempemeriksaan');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/masteritempemeriksaan/edit/' . $id);
        }
    }

    public function view($id) {
        $this->check_permission('master', 'view');
        $data = $this->item_pemeriksaan_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/masteritempemeriksaan');
            return;
        }
        $view_data = array(
            'title' => 'Detail Item Pemeriksaan',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Item Pemeriksaan', 'url' => base_url('masterdata/masteritempemeriksaan')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/masteritempemeriksaan',
            'data' => $data
        );
        $this->render('master_data/item_pemeriksaan/view', $view_data);
    }

    public function ajax_get($id) {
        $this->check_permission('master', 'view');
        $data = $this->item_pemeriksaan_model->get_by_id($id);
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
        if ($this->item_pemeriksaan_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        $data = array(
            'kode' => $this->input->post('kode'),
            'nama' => $this->input->post('nama'),
            'satuan' => $this->input->post('satuan'),
            'harga' => $this->input->post('harga') ?: 0,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($id) {
            if ($this->item_pemeriksaan_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->item_pemeriksaan_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }

    public function ajax_delete($id) {
        $this->check_permission('master', 'delete');
        if ($this->item_pemeriksaan_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }

    public function ajax_search() {
        $search = $this->input->get('q');
        $data = $this->item_pemeriksaan_model->get_all_filtered($search, 1);
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

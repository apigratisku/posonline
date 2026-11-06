<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MutasiAntarOutlet extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('mutasi_antar_outlet_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('outlet', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Mutasi Antar Outlet',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Mutasi Antar Outlet')
            ),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        $this->render('multi_outlet/mutasi_antar_outlet/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('outlet', 'view');
        $params = array(
            'columns' => array('id', 'no_mutasi', 'gudang_asal', 'gudang_tujuan', 'tanggal', 'status', 'created_at'),
            'search_fields' => array('no_mutasi')
        );
        $result = $this->mutasi_antar_outlet_model->datatable($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = 'secondary';
            switch($row->status) {
                case 'draft': $status_badge = 'secondary'; break;
                case 'diproses': $status_badge = 'warning'; break;
                case 'selesai': $status_badge = 'success'; break;
                case 'ditolak': $status_badge = 'danger'; break;
            }
            $data[] = array(
                $row->id,
                $row->no_mutasi,
                $row->gudang_asal,
                $row->gudang_tujuan,
                date('d/m/Y', strtotime($row->tanggal)),
                '<span class="badge bg-' . $status_badge . '">' . ucfirst($row->status) . '</span>',
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
            $buttons .= '<a href="' . base_url('outlet/mutasi/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'update')) {
            $buttons .= '<a href="' . base_url('outlet/mutasi/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        if ($this->auth->has_permission('outlet', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function view($id) {
        $this->check_permission('outlet', 'view');
        $data = $this->mutasi_antar_outlet_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('outlet/mutasi');
            return;
        }
        $view_data = array(
            'title' => 'Detail Mutasi Antar Outlet',
            'breadcrumb' => array(
                array('label' => 'Multi Outlet', 'url' => '#'),
                array('label' => 'Mutasi Antar Outlet', 'url' => base_url('outlet/mutasi')),
                array('label' => 'Detail')
            ),
            'back_url' => 'outlet/mutasi',
            'data' => $data
        );
        $this->render('multi_outlet/mutasi_antar_outlet/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('outlet', 'view');
        $data = $this->mutasi_antar_outlet_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('outlet', 'delete');
        if ($this->mutasi_antar_outlet_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


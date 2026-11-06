<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('konsinyasi_masuk_model');
        $this->check_permission('konsinyasi', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Retur Konsinyasi',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Retur Konsinyasi')
            )
        );
        
        $this->render('konsinyasi/retur/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('konsinyasi', 'view');
        $params = array(
            'columns' => array('km.id', 'km.no_konsinyasi', 's.nama', 'km.tanggal', 'km.keterangan', 'km.created_at'),
            'search_fields' => array('km.no_konsinyasi', 's.nama'),
            'default_order' => 'km.tanggal',
            'build_query' => function () {
                $this->db->select('km.*, s.nama as supplier_nama');
                $this->db->from('konsinyasi_masuk km');
                $this->db->join('supplier s', 's.id = km.supplier_id', 'left');
                $this->db->where('km.status', 'retur');
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->no_konsinyasi,
                $row->supplier_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                $row->keterangan ?: '-',
                date('d/m/Y H:i', strtotime($row->created_at ?: 'now')),
                $this->get_action_buttons($row->id)
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('konsinyasi', 'view')) {
            $buttons .= '<a href="' . base_url('konsinyasi/retur/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
        }
        return $buttons;
    }
    
    public function view($id) {
        $this->check_permission('konsinyasi', 'view');
        $data = $this->konsinyasi_masuk_model->get_by_id_with_relations($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('konsinyasi/retur');
            return;
        }
        $view_data = array(
            'title' => 'Detail Retur Konsinyasi',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Retur Konsinyasi', 'url' => base_url('konsinyasi/retur')),
                array('label' => 'Detail')
            ),
            'back_url' => 'konsinyasi/retur',
            'data' => $data
        );
        $this->render('konsinyasi/retur/view', $view_data);
    }
    
    public function ajax_get($id) {
        $this->check_permission('konsinyasi', 'view');
        $data = $this->konsinyasi_masuk_model->get_by_id_with_relations($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('konsinyasi', 'delete');
        if ($this->konsinyasi_masuk_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


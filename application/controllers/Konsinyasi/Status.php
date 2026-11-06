<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('konsinyasi_masuk_model');
        $this->check_permission('konsinyasi', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Status Konsinyasi',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Status Konsinyasi')
            )
        );
        
        $this->render('konsinyasi/status/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('konsinyasi', 'view');
        $params = array(
            'columns' => array('km.id', 'km.no_konsinyasi', 's.nama', 'mg.nama', 'km.tanggal', 'km.status'),
            'search_fields' => array('km.no_konsinyasi', 's.nama'),
            'default_order' => 'km.tanggal',
            'build_query' => function () {
                $this->db->select('km.*, s.nama as supplier_nama, mg.nama as gudang_nama');
                $this->db->from('konsinyasi_masuk km');
                $this->db->join('supplier s', 's.id = km.supplier_id', 'left');
                $this->db->join('master_gudang mg', 'mg.id = km.gudang_id', 'left');
            },
            'apply_filters' => function () {
                $status_filter = $this->input->post('status');
                if ($status_filter) {
                    $this->db->where('km.status', $status_filter);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-';
            switch($row->status) {
                case 'draft': $status_badge .= 'secondary'; break;
                case 'diterima': $status_badge .= 'success'; break;
                case 'retur': $status_badge .= 'warning'; break;
                case 'ditolak': $status_badge .= 'danger'; break;
                default: $status_badge .= 'info';
            }
            $status_badge .= '">' . ucfirst($row->status ?: 'draft') . '</span>';
            $data[] = array(
                $row->id,
                $row->no_konsinyasi,
                $row->supplier_nama ?: '-',
                $row->gudang_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                $status_badge,
                $this->get_action_buttons($row->id)
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        if ($this->auth->has_permission('konsinyasi', 'view')) {
            $buttons .= '<a href="' . base_url('konsinyasi/status/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
        }
        return $buttons;
    }
    
    public function view($id) {
        $this->check_permission('konsinyasi', 'view');
        $data = $this->konsinyasi_masuk_model->get_by_id_with_relations($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('konsinyasi/status');
            return;
        }
        $view_data = array(
            'title' => 'Detail Status Konsinyasi',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Status Konsinyasi', 'url' => base_url('konsinyasi/status')),
                array('label' => 'Detail')
            ),
            'back_url' => 'konsinyasi/status',
            'data' => $data
        );
        $this->render('konsinyasi/status/view', $view_data);
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
}


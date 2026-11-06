<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DaftarPenjualan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penjualan_model');
        $this->check_permission('penjualan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Daftar Penjualan',
            'breadcrumb' => array(
                array('label' => 'Penjualan', 'url' => '#'),
                array('label' => 'Daftar Penjualan')
            )
        );
        
        $this->render('penjualan/daftar_penjualan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('penjualan', 'view');
        
        $params = array(
            'columns' => array('p.no_penjualan', 'p.tanggal', 'p.total', 'p.status'),
            'search_fields' => array('p.no_penjualan', 'pl.nama', 'mg.nama')
        );
        
        $result = $this->penjualan_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'cancelled' ? 'danger' : 'warning')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_penjualan,
                isset($row->pelanggan_nama) ? $row->pelanggan_nama : 'Umum',
                $row->gudang_nama,
                date('d/m/Y H:i', strtotime($row->tanggal)),
                number_format($row->total, 0, ',', '.'),
                $status_badge,
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($this->auth->has_permission('penjualan', 'view')) {
            $buttons .= '<button class="btn btn-sm btn-info" onclick="viewData(' . $id . ')"><i class="fas fa-eye"></i></button> ';
            $buttons .= '<button class="btn btn-sm btn-primary" onclick="printData(' . $id . ')"><i class="fas fa-print"></i></button> ';
        }
        if ($status == 'draft' && $this->auth->has_permission('penjualan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="completeData(' . $id . ')"><i class="fas fa-check"></i></button> ';
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="cancelData(' . $id . ')"><i class="fas fa-times"></i></button>';
        }
        return $buttons;
    }
    
    public function ajax_get($id) {
        $this->check_permission('penjualan', 'view');
        $penjualan = $this->penjualan_model->get_by_id_with_relations($id);
        if ($penjualan) {
            $details = $this->penjualan_model->get_details($id);
            $this->ajax_response(true, 'Data berhasil diambil', array(
                'penjualan' => $penjualan,
                'details' => $details
            ));
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_complete($id) {
        $this->check_permission('penjualan', 'update');
        if ($this->penjualan_model->complete($id)) {
            $this->ajax_response(true, 'Penjualan berhasil diselesaikan');
        } else {
            $this->ajax_response(false, 'Gagal menyelesaikan penjualan');
        }
    }
    
    public function ajax_cancel($id) {
        $this->check_permission('penjualan', 'update');
        if ($this->penjualan_model->update($id, array('status' => 'cancelled'))) {
            $this->ajax_response(true, 'Penjualan berhasil dibatalkan');
        } else {
            $this->ajax_response(false, 'Gagal membatalkan penjualan');
        }
    }

    public function print_receipt($id) {
        $this->check_permission('penjualan', 'view');
        $penjualan = $this->penjualan_model->get_by_id_with_relations($id);
        if (!$penjualan) {
            show_404();
            return;
        }
        $data = array(
            'penjualan' => $penjualan,
            'details' => $this->penjualan_model->get_details($id),
            'profil_apotek' => $this->data['profil_apotek']
        );
        $this->load->view('penjualan/daftar_penjualan/print', $data);
    }
}


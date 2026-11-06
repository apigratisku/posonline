<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StokKadaluarsa extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('stok_kadaluarsa_model');
        $this->check_permission('persediaan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Stok Kadaluarsa',
            'breadcrumb' => array(
                array('label' => 'Persediaan', 'url' => '#'),
                array('label' => 'Stok Kadaluarsa')
            )
        );
        
        $this->render('persediaan/stok_kadaluarsa/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('persediaan', 'view');
        
        $params = array(
            'columns' => array('sk.expired_date', 'sk.qty', 'sk.status'),
            'search_fields' => array('mp.nama', 'mp.kode', 'sk.batch_no')
        );
        
        $result = $this->stok_kadaluarsa_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $days_left = (strtotime($row->expired_date) - time()) / 86400;
            $status_badge = '<span class="badge bg-' . ($days_left < 0 ? 'danger' : ($days_left <= 30 ? 'warning' : 'success')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                date('d/m/Y', strtotime($row->expired_date)),
                $row->produk_kode . ' - ' . $row->produk_nama,
                $row->gudang_nama,
                isset($row->batch_no) ? $row->batch_no : '-',
                number_format($row->qty, 0, ',', '.'),
                $status_badge,
                $days_left < 0 ? '<span class="text-danger">Kadaluarsa</span>' : ($days_left <= 30 ? '<span class="text-warning">' . ceil($days_left) . ' hari</span>' : '<span class="text-success">' . ceil($days_left) . ' hari</span>')
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
}


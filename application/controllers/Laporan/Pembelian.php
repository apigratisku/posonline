<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('faktur_pembelian_model');
        $this->check_permission('laporan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Laporan Pembelian',
            'breadcrumb' => array(
                array('label' => 'Laporan', 'url' => '#'),
                array('label' => 'Laporan Pembelian')
            )
        );
        $this->render('laporan/pembelian/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('laporan', 'view');
        $params = array(
            'columns' => array('fp.id', 'fp.no_faktur', 's.nama', 'fp.tanggal', 'fp.total'),
            'search_fields' => array('fp.no_faktur', 's.nama'),
            'default_order' => 'fp.tanggal',
            'build_query' => function () {
                $this->db->select('fp.*, s.nama as supplier_nama');
                $this->db->from('faktur_pembelian fp');
                $this->db->join('supplier s', 's.id = fp.supplier_id', 'left');
            },
            'apply_filters' => function () {
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($start_date) {
                    $this->db->where('fp.tanggal >=', $start_date);
                }
                if ($end_date) {
                    $this->db->where('fp.tanggal <=', $end_date);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->no_faktur,
                $row->supplier_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                'Rp ' . number_format($row->total ?: 0, 0, ',', '.')
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    public function ajax_export() {
        $this->check_permission('laporan', 'view');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        $this->db->select('fp.*, s.nama as supplier_nama');
        $this->db->from('faktur_pembelian fp');
        $this->db->join('supplier s', 's.id = fp.supplier_id', 'left');
        
        if ($start_date) {
            $this->db->where('fp.tanggal >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('fp.tanggal <=', $end_date);
        }
        
        $this->db->order_by('fp.tanggal', 'DESC');
        $records = $this->db->get()->result();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=laporan_pembelian_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, array('No. Faktur', 'Supplier', 'Tanggal', 'Total'));
        
        foreach ($records as $row) {
            fputcsv($output, array(
                $row->no_faktur,
                $row->supplier_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal)),
                number_format($row->total ?: 0, 0, ',', '.')
            ));
        }
        
        fclose($output);
        exit;
    }
}


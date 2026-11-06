<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penjualan_model');
        $this->check_permission('laporan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Laporan Penjualan',
            'breadcrumb' => array(
                array('label' => 'Laporan', 'url' => '#'),
                array('label' => 'Laporan Penjualan')
            )
        );
        $this->render('laporan/penjualan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('laporan', 'view');
        $params = array(
            'columns' => array('p.id', 'p.no_penjualan', 'pl.nama', 'p.tanggal', 'p.total', 'u.nama_lengkap'),
            'search_fields' => array('p.no_penjualan', 'pl.nama'),
            'default_order' => 'p.tanggal',
            'build_query' => function () {
                $this->db->select('p.*, pl.nama as pelanggan_nama, u.nama_lengkap as kasir_nama');
                $this->db->from('penjualan p');
                $this->db->join('pelanggan pl', 'pl.id = p.pelanggan_id', 'left');
                $this->db->join('users u', 'u.id = p.created_by', 'left');
            },
            'apply_filters' => function () {
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($start_date) {
                    $this->db->where('p.tanggal >=', $start_date);
                }
                if ($end_date) {
                    $this->db->where('p.tanggal <=', $end_date);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->no_penjualan,
                $row->pelanggan_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                'Rp ' . number_format($row->total ?: 0, 0, ',', '.'),
                $row->kasir_nama ?: '-'
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    public function ajax_export() {
        $this->check_permission('laporan', 'view');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        $this->db->select('p.*, pl.nama as pelanggan_nama, u.nama_lengkap as kasir_nama');
        $this->db->from('penjualan p');
        $this->db->join('pelanggan pl', 'pl.id = p.pelanggan_id', 'left');
        $this->db->join('users u', 'u.id = p.created_by', 'left');
        
        if ($start_date) {
            $this->db->where('p.tanggal >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('p.tanggal <=', $end_date);
        }
        
        $this->db->order_by('p.tanggal', 'DESC');
        $records = $this->db->get()->result();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=laporan_penjualan_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, array('No. Penjualan', 'Pelanggan', 'Tanggal', 'Total', 'Kasir'));
        
        foreach ($records as $row) {
            fputcsv($output, array(
                $row->no_penjualan,
                $row->pelanggan_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal)),
                number_format($row->total ?: 0, 0, ',', '.'),
                $row->kasir_nama ?: '-'
            ));
        }
        
        fclose($output);
        exit;
    }
}


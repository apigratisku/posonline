<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('faktur_pembelian_model');
        $this->load->model('faktur_pembelian_detail_model');
        $this->load->model('supplier_model');
        $this->check_permission('analisis', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Analisis Pembelian',
            'breadcrumb' => array(
                array('label' => 'Analisis', 'url' => '#'),
                array('label' => 'Analisis Pembelian')
            )
        );
        $this->render('analisis/pembelian/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('analisis', 'view');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $this->db->select('fp.supplier_id, s.nama as supplier_nama, COUNT(fp.id) as jumlah_transaksi, SUM(fp.total) as total_pembelian');
        $this->db->from('faktur_pembelian fp');
        $this->db->join('supplier s', 's.id = fp.supplier_id');
        
        if ($start_date) {
            $this->db->where('fp.tanggal >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('fp.tanggal <=', $end_date);
        }
        
        $this->db->group_by('fp.supplier_id');
        $this->db->order_by('total_pembelian', 'DESC');
        
        $records = $this->db->get()->result();
        $data = array();
        foreach ($records as $row) {
            $data[] = array(
                $row->supplier_nama,
                $row->jumlah_transaksi,
                'Rp ' . number_format($row->total_pembelian ?: 0, 0, ',', '.'),
                'Rp ' . number_format(($row->total_pembelian ?: 0) / ($row->jumlah_transaksi ?: 1), 0, ',', '.')
            );
        }
        
        $this->datatable_json_rows($data);
    }
}


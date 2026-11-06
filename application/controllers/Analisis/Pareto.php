<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pareto extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penjualan_detail_model');
        $this->load->model('master_produk_model');
        $this->check_permission('analisis', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Analisis Pareto',
            'breadcrumb' => array(
                array('label' => 'Analisis', 'url' => '#'),
                array('label' => 'Analisis Pareto')
            )
        );
        $this->render('analisis/pareto/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('analisis', 'view');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $this->db->select('pd.produk_id, mp.kode, mp.nama, SUM(pd.qty) as total_qty, SUM(pd.subtotal) as total_value');
        $this->db->from('penjualan_detail pd');
        $this->db->join('penjualan p', 'p.id = pd.penjualan_id');
        $this->db->join('master_produk mp', 'mp.id = pd.produk_id');
        
        if ($start_date) {
            $this->db->where('p.tanggal >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('p.tanggal <=', $end_date);
        }
        
        $this->db->where('p.status', 'completed');
        $this->db->group_by('pd.produk_id');
        $this->db->order_by('total_value', 'DESC');
        $this->db->limit(20);
        
        $records = $this->db->get()->result();
        
        $total_value = 0;
        foreach ($records as $row) {
            $total_value += $row->total_value;
        }
        
        $data = array();
        $cumulative = 0;
        foreach ($records as $row) {
            $cumulative += $row->total_value;
            $percentage = $total_value > 0 ? ($row->total_value / $total_value * 100) : 0;
            $cumulative_percentage = $total_value > 0 ? ($cumulative / $total_value * 100) : 0;
            
            $data[] = array(
                $row->kode,
                $row->nama,
                number_format($row->total_qty, 0, ',', '.'),
                'Rp ' . number_format($row->total_value, 0, ',', '.'),
                number_format($percentage, 2) . '%',
                number_format($cumulative_percentage, 2) . '%'
            );
        }
        
        $this->datatable_json_rows($data);
    }
}


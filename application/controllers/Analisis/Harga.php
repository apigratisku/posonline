<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Harga extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_produk_model');
        $this->load->model('faktur_pembelian_detail_model');
        $this->load->model('penjualan_detail_model');
        $this->check_permission('analisis', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Analisis Harga',
            'breadcrumb' => array(
                array('label' => 'Analisis', 'url' => '#'),
                array('label' => 'Analisis Harga')
            ),
            'produk_list' => $this->master_produk_model->get_all(array('is_active' => 1), 'nama ASC')
        );
        $this->render('analisis/harga/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('analisis', 'view');
        $produk_id = $this->input->post('produk_id');
        
        if (!$produk_id) {
            $this->datatable_json_rows(array());
            return;
        }
        
        // Get average purchase price
        $this->db->select_avg('harga', 'avg_harga_beli');
        $this->db->from('faktur_pembelian_detail');
        $this->db->where('produk_id', $produk_id);
        $avg_beli = $this->db->get()->row();
        
        // Get average selling price
        $this->db->select_avg('harga', 'avg_harga_jual');
        $this->db->from('penjualan_detail');
        $this->db->where('produk_id', $produk_id);
        $avg_jual = $this->db->get()->row();
        
        // Get current product price
        $produk = $this->master_produk_model->get_by_id($produk_id);
        
        $data = array();
        if ($produk) {
            $margin = ($produk->harga_jual ?: 0) - ($produk->harga_beli ?: 0);
            $margin_persen = ($produk->harga_beli > 0) ? ($margin / $produk->harga_beli * 100) : 0;
            
            $data[] = array(
                $produk->kode,
                $produk->nama,
                'Rp ' . number_format($produk->harga_beli ?: 0, 0, ',', '.'),
                'Rp ' . number_format($produk->harga_jual ?: 0, 0, ',', '.'),
                'Rp ' . number_format($avg_beli->avg_harga_beli ?: 0, 0, ',', '.'),
                'Rp ' . number_format($avg_jual->avg_harga_jual ?: 0, 0, ',', '.'),
                'Rp ' . number_format($margin, 0, ',', '.'),
                number_format($margin_persen, 2) . '%'
            );
        }
        
        $this->datatable_json_rows($data);
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persediaan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('kartu_stok_model');
        $this->load->model('master_produk_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('laporan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Laporan Persediaan',
            'breadcrumb' => array(
                array('label' => 'Laporan', 'url' => '#'),
                array('label' => 'Laporan Persediaan')
            ),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        $this->render('laporan/persediaan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('laporan', 'view');
        $params = array(
            'columns' => array('mp.id', 'mp.kode', 'mp.nama', 'mg.nama', 'total_masuk', 'total_keluar', 'saldo'),
            'search_fields' => array('mp.kode', 'mp.nama'),
            'default_order' => 'mp.kode',
            'build_query' => function () {
                $this->db->select('mp.id, mp.kode, mp.nama, mg.nama as gudang_nama');
                $this->db->select_sum('ks.qty_masuk', 'total_masuk');
                $this->db->select_sum('ks.qty_keluar', 'total_keluar');
                $this->db->from('master_produk mp');
                $this->db->join('kartu_stok ks', 'ks.produk_id = mp.id', 'left');
                $this->db->join('master_gudang mg', 'mg.id = ks.gudang_id', 'left');
                $this->db->group_by('mp.id, mg.id');
            },
            'apply_filters' => function () {
                $produk_id = $this->input->post('produk_id');
                $gudang_id = $this->input->post('gudang_id');
                if ($produk_id) {
                    $this->db->where('mp.id', $produk_id);
                }
                if ($gudang_id) {
                    $this->db->where('mg.id', $gudang_id);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $saldo = ($row->total_masuk ?: 0) - ($row->total_keluar ?: 0);
            $data[] = array(
                $row->id,
                $row->kode,
                $row->nama,
                $row->gudang_nama ?: '-',
                number_format($row->total_masuk ?: 0, 0, ',', '.'),
                number_format($row->total_keluar ?: 0, 0, ',', '.'),
                number_format($saldo, 0, ',', '.')
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    public function ajax_export() {
        $this->check_permission('laporan', 'view');
        $produk_id = $this->input->get('produk_id');
        $gudang_id = $this->input->get('gudang_id');
        
        $this->db->select('mp.id, mp.kode, mp.nama, mg.nama as gudang_nama');
        $this->db->select_sum('ks.qty_masuk', 'total_masuk');
        $this->db->select_sum('ks.qty_keluar', 'total_keluar');
        $this->db->from('master_produk mp');
        $this->db->join('kartu_stok ks', 'ks.produk_id = mp.id', 'left');
        $this->db->join('master_gudang mg', 'mg.id = ks.gudang_id', 'left');
        $this->db->group_by('mp.id, mg.id');
        
        if ($produk_id) {
            $this->db->where('mp.id', $produk_id);
        }
        if ($gudang_id) {
            $this->db->where('mg.id', $gudang_id);
        }
        
        $this->db->order_by('mp.kode', 'ASC');
        $records = $this->db->get()->result();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=laporan_persediaan_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, array('Kode', 'Nama Produk', 'Gudang', 'Total Masuk', 'Total Keluar', 'Saldo'));
        
        foreach ($records as $row) {
            $saldo = ($row->total_masuk ?: 0) - ($row->total_keluar ?: 0);
            fputcsv($output, array(
                $row->kode,
                $row->nama,
                $row->gudang_nama ?: '-',
                number_format($row->total_masuk ?: 0, 0, ',', '.'),
                number_format($row->total_keluar ?: 0, 0, ',', '.'),
                number_format($saldo, 0, ',', '.')
            ));
        }
        
        fclose($output);
        exit;
    }
}


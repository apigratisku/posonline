<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('kartu_stok_model');
        $this->load->model('master_produk_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('konsinyasi', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Stok Konsinyasi',
            'breadcrumb' => array(
                array('label' => 'Konsinyasi', 'url' => '#'),
                array('label' => 'Stok Konsinyasi')
            ),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('konsinyasi/stok/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('konsinyasi', 'view');
        $params = array(
            'columns' => array('ks.id', 'mp.kode', 'mg.nama', 'ks.tanggal', 'ks.referensi', 'ks.qty_masuk', 'ks.qty_keluar', 'ks.qty_saldo', 'ks.keterangan'),
            'search_fields' => array('mp.kode', 'mp.nama', 'ks.referensi'),
            'default_order' => 'ks.tanggal',
            'build_query' => function () {
                $this->db->select('ks.*, mp.kode as produk_kode, mp.nama as produk_nama, mg.nama as gudang_nama');
                $this->db->from('kartu_stok ks');
                $this->db->join('master_produk mp', 'mp.id = ks.produk_id', 'left');
                $this->db->join('master_gudang mg', 'mg.id = ks.gudang_id', 'left');
                $this->db->like('ks.referensi', 'KONS-', 'after');
            },
            'apply_filters' => function () {
                $produk_id = $this->input->post('produk_id');
                $gudang_id = $this->input->post('gudang_id');
                if ($produk_id) {
                    $this->db->where('ks.produk_id', $produk_id);
                }
                if ($gudang_id) {
                    $this->db->where('ks.gudang_id', $gudang_id);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                $row->produk_kode . ' - ' . $row->produk_nama,
                $row->gudang_nama ?: '-',
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                $row->referensi,
                number_format($row->qty_masuk ?: 0, 0, ',', '.'),
                number_format($row->qty_keluar ?: 0, 0, ',', '.'),
                number_format($row->qty_saldo ?: 0, 0, ',', '.'),
                $row->keterangan ?: '-'
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    public function ajax_get($id) {
        $this->check_permission('konsinyasi', 'view');
        $data = $this->kartu_stok_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_produk_model');
        $this->load->model('master_kategori_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('persediaan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Daftar Produk',
            'breadcrumb' => array(
                array('label' => 'Persediaan', 'url' => '#'),
                array('label' => 'Daftar Produk')
            ),
            'kategoris' => $this->master_kategori_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('persediaan/produk/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('persediaan', 'view');
        
        $params = array(
            'columns' => array('mp.id', 'mp.kode', 'mp.barcode', 'mp.nama', 'mk.nama', 'ms.nama', 'mg.nama', 'mp.harga_jual', 'mp.stok_akhir', 'mp.is_active'),
            'search_fields' => array('mp.kode', 'mp.barcode', 'mp.nama', 'mk.nama', 'ms.nama')
        );
        
        $result = $this->master_produk_model->datatable($params);
        
        // Format data
        $data = array();
        foreach ($result['data'] as $row) {
            $stok = $this->master_produk_model->get_stok($row->id, $row->gudang_id);
            $data[] = array(
                $row->kode,
                isset($row->barcode) ? $row->barcode : '-',
                $row->nama,
                isset($row->kategori_nama) ? $row->kategori_nama : '-',
                isset($row->satuan_nama) ? $row->satuan_nama : '-',
                isset($row->gudang_nama) ? $row->gudang_nama : '-',
                number_format($row->harga_jual, 0, ',', '.'),
                '<span class="badge ' . ($stok < $row->stok_minimal ? 'bg-danger' : 'bg-success') . '">' . $stok . '</span>',
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        
        if ($this->auth->has_permission('persediaan', 'view')) {
            $buttons .= '<a href="' . base_url('masterdata/masterproduk/view/' . $id) . '" class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></a> ';
        }
        
        if ($this->auth->has_permission('persediaan', 'view')) {
            $buttons .= '<a href="' . base_url('masterdata/masterproduk/kartu_stok/' . $id) . '" class="btn btn-sm btn-secondary" title="Kartu Stok"><i class="fas fa-list"></i></a>';
        }
        
        return $buttons;
    }
}


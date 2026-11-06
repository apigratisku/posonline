<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PenyesuaianStok extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penyesuaian_stok_model');
        $this->load->model('master_produk_model');
        $this->load->model('master_gudang_model');
        $this->check_permission('persediaan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Penyesuaian Stok',
            'breadcrumb' => array(
                array('label' => 'Persediaan', 'url' => '#'),
                array('label' => 'Penyesuaian Stok')
            ),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1)),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('persediaan/penyesuaian_stok/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('persediaan', 'view');
        
        $params = array(
            'columns' => array('ps.tanggal', 'ps.qty_penyesuaian', 'ps.status'),
            'search_fields' => array('ps.no_penyesuaian', 'mp.nama', 'mp.kode')
        );
        
        $result = $this->penyesuaian_stok_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'approved' ? 'success' : ($row->status == 'rejected' ? 'danger' : 'warning')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_penyesuaian,
                $row->produk_kode . ' - ' . $row->produk_nama,
                $row->gudang_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                number_format($row->qty_awal, 0, ',', '.'),
                '<span class="' . ($row->qty_penyesuaian >= 0 ? 'text-success' : 'text-danger') . '">' . ($row->qty_penyesuaian >= 0 ? '+' : '') . number_format($row->qty_penyesuaian, 0, ',', '.') . '</span>',
                number_format($row->qty_akhir, 0, ',', '.'),
                $status_badge,
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($status == 'draft' && $this->auth->has_permission('persediaan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="approveData(' . $id . ')"><i class="fas fa-check"></i></button>';
        }
        return $buttons;
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('persediaan', 'update');
        } else {
            $this->check_permission('persediaan', 'create');
        }
        
        $this->form_validation->set_rules('produk_id', 'Produk', 'required');
        $this->form_validation->set_rules('gudang_id', 'Gudang', 'required');
        $this->form_validation->set_rules('qty_penyesuaian', 'Qty Penyesuaian', 'required|numeric');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        // Get current stock
        $produk_id = $this->input->post('produk_id');
        $gudang_id = $this->input->post('gudang_id');
        $qty_awal = $this->master_produk_model->get_stok($produk_id, $gudang_id);
        $qty_penyesuaian = $this->input->post('qty_penyesuaian');
        
        $data = array(
            'no_penyesuaian' => $id ? $this->input->post('no_penyesuaian') : $this->penyesuaian_stok_model->generate_no_penyesuaian(),
            'produk_id' => $produk_id,
            'gudang_id' => $gudang_id,
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'qty_awal' => $qty_awal,
            'qty_penyesuaian' => $qty_penyesuaian,
            'alasan' => $this->input->post('alasan'),
            'status' => 'draft'
        );
        
        if ($id) {
            if ($this->penyesuaian_stok_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->penyesuaian_stok_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_approve($id) {
        $this->check_permission('persediaan', 'update');
        
        if ($this->penyesuaian_stok_model->approve($id)) {
            $this->ajax_response(true, 'Penyesuaian stok berhasil disetujui');
        } else {
            $this->ajax_response(false, 'Gagal menyetujui penyesuaian stok');
        }
    }
}


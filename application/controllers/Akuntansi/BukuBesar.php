<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BukuBesar extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('buku_besar_model');
        $this->load->model('coa_model');
        $this->check_permission('akuntansi', 'view');
    }
    
    /**
     * Index - List page
     */
    public function index() {
        $data = array(
            'title' => 'Buku Besar',
            'breadcrumb' => array(
                array('label' => 'Akuntansi', 'url' => '#'),
                array('label' => 'Buku Besar')
            ),
            'coa_list' => $this->coa_model->get_all(array('is_active' => 1))
        );
        
        $this->render('akuntansi/buku_besar/index', $data);
    }
    
    /**
     * AJAX List - DataTables server-side
     */
    public function ajax_list() {
        $this->check_permission('akuntansi', 'view');
        $params = array(
            'columns' => array('bb.tanggal', 'bb.no_bukti', 'coa.kode', 'bb.keterangan', 'bb.debit', 'bb.kredit', 'bb.saldo'),
            'search_fields' => array('bb.no_bukti', 'bb.keterangan', 'coa.kode', 'coa.nama'),
            'default_order' => 'bb.tanggal',
            'build_query' => function () {
                $this->db->select('bb.*, coa.kode as coa_kode, coa.nama as coa_nama');
                $this->db->from('buku_besar bb');
                $this->db->join('coa', 'coa.id = bb.coa_id', 'left');
            },
            'apply_filters' => function () {
                $coa_id = $this->input->post('coa_id');
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($coa_id) {
                    $this->db->where('bb.coa_id', $coa_id);
                }
                if ($start_date) {
                    $this->db->where('bb.tanggal >=', $start_date);
                }
                if ($end_date) {
                    $this->db->where('bb.tanggal <=', $end_date);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                $row->no_bukti ?: '-',
                $row->coa_kode . ' - ' . $row->coa_nama,
                $row->keterangan ?: '-',
                'Rp ' . number_format($row->debit ?: 0, 0, ',', '.'),
                'Rp ' . number_format($row->kredit ?: 0, 0, ',', '.'),
                'Rp ' . number_format($row->saldo ?: 0, 0, ',', '.')
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    /**
     * AJAX Get - Get single record
     */
    public function ajax_get($id) {
        $this->check_permission('akuntansi', 'view');
        
        $data = $this->buku_besar_model->get_by_id($id);
        
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    /**
     * AJAX Save - Create or Update
     */
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('akuntansi', 'update');
        } else {
            $this->check_permission('akuntansi', 'create');
        }
        
        // Validation
        $this->form_validation->set_rules('coa_id', 'CoA', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $debit = $this->input->post('debit') ?: 0;
        $kredit = $this->input->post('kredit') ?: 0;
        
        // Calculate saldo
        $saldo_sebelum = $this->buku_besar_model->get_saldo_by_coa($this->input->post('coa_id'), $this->input->post('tanggal'));
        $saldo = $saldo_sebelum + $debit - $kredit;
        
        $data = array(
            'coa_id' => $this->input->post('coa_id'),
            'tanggal' => $this->input->post('tanggal'),
            'no_bukti' => $this->input->post('no_bukti'),
            'keterangan' => $this->input->post('keterangan'),
            'debit' => $debit,
            'kredit' => $kredit,
            'saldo' => $saldo,
            'is_active' => 1
        );
        
        if ($id) {
            // Update
            if ($this->buku_besar_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            // Insert
            $insert_id = $this->buku_besar_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    /**
     * AJAX Delete
     */
    public function ajax_delete($id) {
        $this->check_permission('akuntansi', 'delete');
        
        if ($this->buku_besar_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


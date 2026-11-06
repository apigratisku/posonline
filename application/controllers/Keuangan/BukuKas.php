<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BukuKas extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('buku_kas_model');
        $this->load->model('akun_kas_model');
        $this->check_permission('keuangan', 'view');
    }
    
    public function index($akun_kas_id = null) {
        $data = array(
            'title' => 'Buku Kas',
            'breadcrumb' => array(
                array('label' => 'Keuangan', 'url' => '#'),
                array('label' => 'Buku Kas')
            ),
            'akun_kas' => $akun_kas_id ? $this->akun_kas_model->get_by_id($akun_kas_id) : null,
            'akun_kas_list' => $this->akun_kas_model->get_all(array('is_active' => 1))
        );
        
        $this->render('keuangan/buku_kas/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('keuangan', 'view');
        
        $akun_kas_id = $this->input->post('akun_kas_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $params = array(
            'columns' => array('bk.tanggal', 'bk.tipe', 'bk.nominal', 'bk.keterangan'),
            'search_fields' => array('bk.keterangan', 'bk.referensi', 'ak.nama')
        );
        
        if ($akun_kas_id) {
            $params['akun_kas_id'] = $akun_kas_id;
        }
        
        $result = $this->buku_kas_model->datatable($params);
        
        $saldo_berjalan = 0;
        if ($akun_kas_id) {
            $akun = $this->akun_kas_model->get_by_id($akun_kas_id);
            $saldo_berjalan = isset($akun->saldo_awal) ? $akun->saldo_awal : 0;
        }
        
        $data = array();
        foreach ($result['data'] as $row) {
            if ($row->tipe == 'debit') {
                $saldo_berjalan += $row->nominal;
            } else {
                $saldo_berjalan -= $row->nominal;
            }
            
            $buttons = '';
            if ($this->auth->has_permission('keuangan', 'delete')) {
                $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $row->id . ')"><i class="fas fa-trash"></i></button>';
            }
            
            $tipe_badge = '<span class="badge bg-' . ($row->tipe == 'debit' ? 'success' : 'danger') . '">' . strtoupper($row->tipe) . '</span>';
            $data[] = array(
                date('d/m/Y', strtotime($row->tanggal)),
                $row->akun_nama,
                $tipe_badge,
                number_format($row->nominal, 0, ',', '.'),
                isset($row->keterangan) ? $row->keterangan : '-',
                isset($row->referensi) ? $row->referensi : '-',
                number_format($saldo_berjalan, 0, ',', '.'),
                $buttons
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('keuangan', 'update');
        } else {
            $this->check_permission('keuangan', 'create');
        }
        
        $this->form_validation->set_rules('akun_kas_id', 'Akun Kas', 'required');
        $this->form_validation->set_rules('tipe', 'Tipe', 'required');
        $this->form_validation->set_rules('nominal', 'Nominal', 'required|numeric|greater_than[0]');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'akun_kas_id' => $this->input->post('akun_kas_id'),
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'tipe' => $this->input->post('tipe'),
            'nominal' => $this->input->post('nominal'),
            'keterangan' => $this->input->post('keterangan'),
            'referensi' => $this->input->post('referensi')
        );
        
        if ($id) {
            if ($this->buku_kas_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->buku_kas_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('keuangan', 'delete');
        if ($this->buku_kas_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
}


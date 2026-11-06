<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UtangUsaha extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('utang_usaha_model');
        $this->load->model('faktur_pembelian_model');
        $this->load->model('buku_kas_model');
        $this->load->model('akun_kas_model');
        $this->check_permission('keuangan', 'view');
    }
    
    public function index() {
        $this->load->model('supplier_model');
        $data = array(
            'title' => 'Utang Usaha',
            'breadcrumb' => array(
                array('label' => 'Keuangan', 'url' => '#'),
                array('label' => 'Utang Usaha')
            ),
            'suppliers' => $this->supplier_model->get_all(array('is_active' => 1)),
            'fakturs' => $this->faktur_pembelian_model->get_all(),
            'akun_kas_list' => $this->akun_kas_model->get_all(array('is_active' => 1))
        );
        
        $this->render('keuangan/utang_usaha/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('keuangan', 'view');
        
        $params = array(
            'columns' => array('uu.tanggal', 'uu.nominal', 'uu.status'),
            'search_fields' => array('fp.no_faktur', 's.nama')
        );
        
        $result = $this->utang_usaha_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'paid' ? 'success' : ($row->status == 'partial' ? 'warning' : 'danger')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_faktur ? $row->no_faktur : 'Manual',
                $row->supplier_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                $row->jatuh_tempo ? date('d/m/Y', strtotime($row->jatuh_tempo)) : '-',
                number_format($row->nominal, 0, ',', '.'),
                number_format($row->terbayar, 0, ',', '.'),
                number_format($row->sisa, 0, ',', '.'),
                $status_badge,
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        if ($this->auth->has_permission('keuangan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-primary" onclick="editData(' . $id . ')" title="Edit"><i class="fas fa-edit"></i></button> ';
        }
        if ($status != 'paid' && $this->auth->has_permission('keuangan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="bayarUtang(' . $id . ')" title="Bayar"><i class="fas fa-money-bill"></i> Bayar</button> ';
        }
        if ($this->auth->has_permission('keuangan', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')" title="Hapus"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function ajax_get($id) {
        $this->check_permission('keuangan', 'view');
        $data = $this->utang_usaha_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('keuangan', 'update');
        } else {
            $this->check_permission('keuangan', 'create');
        }
        
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('nominal', 'Nominal', 'required|numeric|greater_than[0]');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'supplier_id' => $this->input->post('supplier_id'),
            'faktur_pembelian_id' => $this->input->post('faktur_pembelian_id') ?: null,
            'tanggal' => $this->input->post('tanggal'),
            'jatuh_tempo' => $this->input->post('jatuh_tempo') ?: null,
            'nominal' => floatval($this->input->post('nominal'))
        );
        
        if ($id) {
            $existing = $this->utang_usaha_model->get_by_id($id);
            if ($existing) {
                $data['sisa'] = $data['nominal'] - $existing->terbayar;
                $data['status'] = $data['sisa'] <= 0 ? 'paid' : ($existing->terbayar > 0 ? 'partial' : 'unpaid');
                $this->utang_usaha_model->update($id, $data);
                $this->ajax_response(true, 'Data berhasil diubah');
            } else {
                $this->ajax_response(false, 'Data tidak ditemukan');
            }
        } else {
            $data['terbayar'] = 0;
            $data['sisa'] = $data['nominal'];
            $data['status'] = 'unpaid';
            $insert_id = $this->utang_usaha_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil disimpan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menyimpan data');
            }
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('keuangan', 'delete');
        if ($this->utang_usaha_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    public function ajax_bayar($id) {
        $this->check_permission('keuangan', 'update');
        
        $this->form_validation->set_rules('nominal', 'Nominal', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('akun_kas_id', 'Akun Kas', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $utang = $this->utang_usaha_model->get_by_id_with_relations($id);
        if (!$utang) {
            $this->ajax_response(false, 'Data tidak ditemukan');
            return;
        }
        
        $nominal = floatval($this->input->post('nominal'));
        if ($nominal > $utang->sisa) {
            $this->ajax_response(false, 'Nominal pembayaran melebihi sisa utang');
            return;
        }
        
        $this->db->trans_start();
        
        // Update utang
        $this->utang_usaha_model->add_payment($id, $nominal);
        
        // Insert buku kas (kredit)
        $sisa_baru = $utang->sisa - $nominal;
        $keterangan = 'Pembayaran utang - Supplier: ' . $utang->supplier_nama;
        $details = array();
        if ($utang->no_po) {
            $details[] = 'PO: ' . $utang->no_po;
        }
        if ($utang->no_faktur) {
            $details[] = 'Faktur: ' . $utang->no_faktur;
        }
        if (!empty($details)) {
            $keterangan .= ' (' . implode(', ', $details) . ')';
        }
        if ($sisa_baru > 0) {
            $keterangan .= ' | Sisa: Rp ' . number_format($sisa_baru, 0, ',', '.');
        } else {
            $keterangan .= ' | Lunas';
        }
        
        $buku_kas_data = array(
            'akun_kas_id' => $this->input->post('akun_kas_id'),
            'tanggal' => date('Y-m-d'),
            'tipe' => 'kredit',
            'nominal' => $nominal,
            'keterangan' => $keterangan,
            'referensi' => 'UTANG-' . $id
        );
        $this->buku_kas_model->insert($buku_kas_data);
        
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            $this->ajax_response(false, 'Gagal memproses pembayaran');
        } else {
            $this->ajax_response(true, 'Pembayaran berhasil diproses');
        }
    }
}

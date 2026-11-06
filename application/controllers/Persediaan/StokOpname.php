<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StokOpname extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('stok_opname_model');
        $this->load->model('stok_opname_detail_model');
        $this->load->model('master_gudang_model');
        $this->load->model('master_produk_model');
        $this->check_permission('persediaan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Stok Opname',
            'breadcrumb' => array(
                array('label' => 'Persediaan', 'url' => '#'),
                array('label' => 'Stok Opname')
            ),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('persediaan/stok_opname/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('persediaan', 'view');
        
        $params = array(
            'columns' => array('so.tanggal', 'so.status'),
            'search_fields' => array('so.no_opname', 'mg.nama')
        );
        
        $result = $this->stok_opname_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'cancelled' ? 'danger' : ($row->status == 'process' ? 'warning' : 'secondary'))) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_opname,
                $row->gudang_nama,
                date('d/m/Y', strtotime($row->tanggal)),
                $status_badge,
                isset($row->keterangan) ? $row->keterangan : '-',
                $this->get_action_buttons($row->id, $row->status)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        $buttons = '';
        $buttons .= '<a href="' . base_url('persediaan/stok_opname/view/' . $id) . '" class="btn btn-sm btn-info" title="Detail"><i class="fas fa-eye"></i> Detail</a> ';
        if ($status == 'process' && $this->auth->has_permission('persediaan', 'update')) {
            $buttons .= '<button class="btn btn-sm btn-success" onclick="completeOpname(' . $id . ')" title="Selesaikan"><i class="fas fa-check"></i> Selesai</button> ';
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="cancelOpname(' . $id . ')" title="Batalkan"><i class="fas fa-times"></i> Batal</button>';
        }
        return $buttons;
    }
    
    public function view($id) {
        $this->check_permission('persediaan', 'view');
        $opname = $this->stok_opname_model->get_by_id_with_relations($id);
        if (!$opname) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('persediaan/opname');
            return;
        }
        
        $data = array(
            'title' => 'Detail Stok Opname',
            'breadcrumb' => array(
                array('label' => 'Persediaan', 'url' => base_url('persediaan/opname')),
                array('label' => 'Detail Stok Opname')
            ),
            'opname' => $opname,
            'details' => $this->stok_opname_model->get_details($id),
            'produks' => $this->master_produk_model->get_all(array('is_active' => 1))
        );
        
        $this->render('persediaan/stok_opname/detail', $data);
    }
    
    public function ajax_save() {
        $id = $this->input->post('id');
        
        if ($id) {
            $this->check_permission('persediaan', 'update');
        } else {
            $this->check_permission('persediaan', 'create');
        }
        
        $this->form_validation->set_rules('gudang_id', 'Gudang', 'required');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        $data = array(
            'no_opname' => $id ? $this->input->post('no_opname') : $this->stok_opname_model->generate_no_opname(),
            'gudang_id' => $this->input->post('gudang_id'),
            'tanggal' => $this->input->post('tanggal') ?: date('Y-m-d'),
            'status' => $id ? ($this->input->post('status') ?: 'process') : 'process',
            'keterangan' => $this->input->post('keterangan')
        );
        
        if ($id) {
            if ($this->stok_opname_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate', array('id' => $id));
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            $insert_id = $this->stok_opname_model->insert($data);
            if ($insert_id) {
                $this->ajax_response(true, 'Data berhasil ditambahkan', array('id' => $insert_id));
            } else {
                $this->ajax_response(false, 'Gagal menambahkan data');
            }
        }
    }
    
    public function ajax_complete($id) {
        $this->check_permission('persediaan', 'update');
        
        if ($this->stok_opname_model->complete($id)) {
            $this->ajax_response(true, 'Stok opname berhasil diselesaikan');
        } else {
            $this->ajax_response(false, 'Gagal menyelesaikan stok opname');
        }
    }
    
    public function ajax_cancel($id) {
        $this->check_permission('persediaan', 'update');
        $opname = $this->stok_opname_model->get_by_id($id);
        if (!$opname || $opname->status != 'process') {
            $this->ajax_response(false, 'Status opname tidak valid');
            return;
        }
        
        if ($this->stok_opname_model->update($id, array('status' => 'cancelled'))) {
            $this->ajax_response(true, 'Stok opname berhasil dibatalkan');
        } else {
            $this->ajax_response(false, 'Gagal membatalkan stok opname');
        }
    }
    
    public function ajax_add_item() {
        $this->check_permission('persediaan', 'update');
        $opname_id = $this->input->post('opname_id');
        $produk_id = $this->input->post('produk_id');
        
        $opname = $this->stok_opname_model->get_by_id($opname_id);
        if (!$opname || $opname->status != 'process') {
            $this->ajax_response(false, 'Status opname tidak valid');
            return;
        }
        
        $this->db->where('opname_id', $opname_id);
        $this->db->where('produk_id', $produk_id);
        $exists = $this->db->get('stok_opname_detail')->row();
        if ($exists) {
            $this->ajax_response(false, 'Produk sudah ada dalam daftar');
            return;
        }
        
        $stok_sistem = $this->master_produk_model->get_stok($produk_id, $opname->gudang_id);
        
        $detail_data = array(
            'opname_id' => $opname_id,
            'produk_id' => $produk_id,
            'stok_sistem' => $stok_sistem,
            'stok_fisik' => 0,
            'selisih' => - $stok_sistem,
            'keterangan' => ''
        );
        
        $insert_id = $this->stok_opname_detail_model->insert($detail_data);
        if ($insert_id) {
            $this->ajax_response(true, 'Produk berhasil ditambahkan');
        } else {
            $this->ajax_response(false, 'Gagal menambahkan produk');
        }
    }

    public function ajax_load_all_products() {
        $this->check_permission('persediaan', 'update');
        $opname_id = $this->input->post('opname_id');
        
        $opname = $this->stok_opname_model->get_by_id($opname_id);
        if (!$opname || $opname->status != 'process') {
            $this->ajax_response(false, 'Status opname tidak valid');
            return;
        }
        
        // Get all active products
        $produks = $this->master_produk_model->get_all(array('is_active' => 1));
        
        $this->db->trans_start();
        $added_count = 0;
        foreach ($produks as $produk) {
            $this->db->where('opname_id', $opname_id);
            $this->db->where('produk_id', $produk->id);
            $exists = $this->db->get('stok_opname_detail')->row();
            if ($exists) {
                continue;
            }
            
            $stok_sistem = $this->master_produk_model->get_stok($produk->id, $opname->gudang_id);
            
            // Set stok_fisik = stok_sistem so they are already matching
            $detail_data = array(
                'opname_id' => $opname_id,
                'produk_id' => $produk->id,
                'stok_sistem' => $stok_sistem,
                'stok_fisik' => $stok_sistem,
                'selisih' => 0,
                'keterangan' => 'Sesuai'
            );
            $this->stok_opname_detail_model->insert($detail_data);
            $added_count++;
        }
        $this->db->trans_complete();
        
        if ($this->db->trans_status() !== FALSE) {
            $this->ajax_response(true, $added_count . ' produk berhasil dimuat dengan stok yang sesuai.');
        } else {
            $this->ajax_response(false, 'Gagal memuat produk dari gudang');
        }
    }
    
    public function ajax_save_details() {
        $this->check_permission('persediaan', 'update');
        $opname_id = $this->input->post('opname_id');
        
        $opname = $this->stok_opname_model->get_by_id($opname_id);
        if (!$opname || $opname->status != 'process') {
            $this->ajax_response(false, 'Status opname tidak valid');
            return;
        }
        
        $items = $this->input->post('items');
        if (empty($items)) {
            $this->ajax_response(false, 'Tidak ada item untuk disimpan');
            return;
        }
        
        $this->db->trans_start();
        foreach ($items as $detail_id => $item) {
            $detail = $this->stok_opname_detail_model->get_by_id($detail_id);
            if ($detail && $detail->opname_id == $opname_id) {
                $stok_fisik = floatval($item['stok_fisik']);
                $selisih = $stok_fisik - $detail->stok_sistem;
                
                $update_data = array(
                    'stok_fisik' => $stok_fisik,
                    'selisih' => $selisih,
                    'keterangan' => $item['keterangan']
                );
                $this->stok_opname_detail_model->update($detail_id, $update_data);
            }
        }
        $this->db->trans_complete();
        
        if ($this->db->trans_status() !== FALSE) {
            $this->ajax_response(true, 'Draft opname berhasil disimpan');
        } else {
            $this->ajax_response(false, 'Gagal menyimpan draft');
        }
    }
    
    public function ajax_delete_item($id) {
        $this->check_permission('persediaan', 'update');
        $detail = $this->stok_opname_detail_model->get_by_id($id);
        if (!$detail) {
            $this->ajax_response(false, 'Item tidak ditemukan');
            return;
        }
        
        $opname = $this->stok_opname_model->get_by_id($detail->opname_id);
        if (!$opname || $opname->status != 'process') {
            $this->ajax_response(false, 'Status opname tidak valid');
            return;
        }
        
        if ($this->stok_opname_detail_model->delete($id)) {
            $this->ajax_response(true, 'Item berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus item');
        }
    }
}


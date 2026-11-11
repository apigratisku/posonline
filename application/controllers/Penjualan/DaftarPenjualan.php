<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DaftarPenjualan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penjualan_model');
        $this->check_permission('penjualan', 'view');
    }
    
    public function index() {
        $peran_kode = $this->peran ? $this->peran->kode : $this->session->userdata('peran_kode');
        $is_admin = strtolower(trim((string) $peran_kode)) === 'admin';

        $data = array(
            'title' => 'Daftar Penjualan',
            'is_admin' => $is_admin,
            'breadcrumb' => array(
                array('label' => 'Penjualan', 'url' => '#'),
                array('label' => 'Daftar Penjualan')
            )
        );
        
        $this->render('penjualan/daftar_penjualan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('penjualan', 'view');
        
        $params = array(
            'columns' => array('p.no_penjualan', 'p.tanggal', 'p.total', 'p.status'),
            'search_fields' => array('p.no_penjualan', 'pl.nama', 'mg.nama')
        );
        
        $result = $this->penjualan_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $status_badge = '<span class="badge bg-' . ($row->status == 'completed' ? 'success' : ($row->status == 'cancelled' ? 'danger' : 'warning')) . '">' . strtoupper($row->status) . '</span>';
            $data[] = array(
                $row->no_penjualan,
                isset($row->pelanggan_nama) ? $row->pelanggan_nama : 'Umum',
                $row->gudang_nama,
                date('d/m/Y H:i', strtotime($row->tanggal)),
                number_format($row->total, 0, ',', '.'),
                $status_badge,
                $this->get_action_buttons($row->id, $row->status),
                $row->id
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id, $status) {
        // Pembatalan transaksi (dengan pengembalian stok) hanya untuk role admin
        $peran_kode = $this->peran ? $this->peran->kode : $this->session->userdata('peran_kode');
        $is_admin = strtolower(trim((string) $peran_kode)) === 'admin';

        $buttons = '';
        if ($this->auth->has_permission('penjualan', 'view')) {
            $buttons .= '<button class="btn btn-sm btn-info" onclick="viewData(' . $id . ')"><i class="fas fa-eye"></i></button> ';
            $buttons .= '<button class="btn btn-sm btn-primary" onclick="printData(' . $id . ')"><i class="fas fa-print"></i></button> ';
        }
        if ($status != 'cancelled' && $this->auth->has_permission('penjualan', 'update')) {
            if ($status == 'draft') {
                $buttons .= '<button class="btn btn-sm btn-success" onclick="completeData(' . $id . ')"><i class="fas fa-check"></i></button> ';
            }
            if ($is_admin) {
                $label = $status == 'completed' ? 'Batalkan & Kembalikan Stok' : 'Batalkan';
                $buttons .= '<button class="btn btn-sm btn-danger" title="' . $label . '" onclick="cancelData(' . $id . ', ' . ($status == 'completed' ? '1' : '0') . ')"><i class="fas fa-times"></i></button>';
            }
        }
        return $buttons;
    }
    
    public function ajax_get($id) {
        $this->check_permission('penjualan', 'view');
        $penjualan = $this->penjualan_model->get_by_id_with_relations($id);
        if ($penjualan) {
            $details = $this->penjualan_model->get_details($id);
            $this->ajax_response(true, 'Data berhasil diambil', array(
                'penjualan' => $penjualan,
                'details' => $details
            ));
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_complete($id) {
        $this->check_permission('penjualan', 'update');
        if ($this->penjualan_model->complete($id)) {
            $this->ajax_response(true, 'Penjualan berhasil diselesaikan');
        } else {
            $this->ajax_response(false, 'Gagal menyelesaikan penjualan');
        }
    }
    
    /**
     * Pastikan user adalah admin.
     * Pembatalan transaksi (dengan pengembalian stok) adalah aksi sensitif —
     * hanya role admin yang boleh.
     */
    private function ensure_admin_can_cancel() {
        $peran_kode = $this->peran ? $this->peran->kode : $this->session->userdata('peran_kode');
        if (strtolower(trim((string) $peran_kode)) !== 'admin') {
            $this->ajax_response(false, 'Hanya role admin yang dapat membatalkan transaksi');
            return false;
        }
        return true;
    }

    /**
     * Aksi cancel untuk 1 transaksi penjualan (dipakai oleh aksi baris DataTables).
     * Pembatalan juga mengembalikan stok bila transaksi sudah completed.
     */
    public function ajax_cancel($id) {
        $this->check_permission('penjualan', 'update');
        if (!$this->ensure_admin_can_cancel()) {
            return;
        }

        $id = intval($id);
        $penjualan = $this->penjualan_model->get_by_id($id);
        if (!$penjualan) {
            $this->ajax_response(false, 'Data penjualan tidak ditemukan');
            return;
        }
        if ($penjualan->status == 'cancelled') {
            $this->ajax_response(false, 'Penjualan sudah dibatalkan sebelumnya');
            return;
        }

        $alasan = trim($this->input->post('alasan'));

        // Transaksi: kalau salah satu langkah gagal, semua batal (tidak ada stok setengah kembali)
        $this->db->trans_start();
        $ok = $this->penjualan_model->cancel_with_stock_return($id, $alasan);
        $this->db->trans_complete();

        if (!$ok || $this->db->trans_status() === FALSE) {
            $this->ajax_response(false, 'Gagal membatalkan penjualan');
            return;
        }

        $message = 'Penjualan berhasil dibatalkan';
        if ($penjualan->status == 'completed') {
            $message .= ' dan stok barang telah dikembalikan';
        }
        $this->ajax_response(true, $message);
    }

    /**
     * Aksi massal (checkbox di halaman daftar).
     */
    public function ajax_cancel_bulk() {
        $this->check_permission('penjualan', 'update');
        if (!$this->ensure_admin_can_cancel()) {
            return;
        }

        $ids = $this->input->post('ids');
        if (!is_array($ids) || empty($ids)) {
            $this->ajax_response(false, 'Tidak ada transaksi yang dipilih');
            return;
        }

        $alasan = trim($this->input->post('alasan'));
        $sukses = 0;
        $gagal = 0;
        $stok_kembali = false;

        foreach ($ids as $id) {
            $id = intval($id);
            $penjualan = $this->penjualan_model->get_by_id($id);
            if (!$penjualan || $penjualan->status == 'cancelled') {
                $gagal++;
                continue;
            }

            $this->db->trans_start();
            $ok = $this->penjualan_model->cancel_with_stock_return($id, $alasan);
            $this->db->trans_complete();

            if ($ok && $this->db->trans_status() !== FALSE) {
                $sukses++;
                if ($penjualan->status == 'completed') {
                    $stok_kembali = true;
                }
            } else {
                $gagal++;
            }
        }

        if ($sukses == 0) {
            $this->ajax_response(false, 'Tidak ada penjualan yang berhasil dibatalkan');
            return;
        }

        $message = $sukses . ' penjualan berhasil dibatalkan';
        if ($stok_kembali) {
            $message .= ' dan stok barang telah dikembalikan';
        }
        if ($gagal > 0) {
            $message .= ' (' . $gagal . ' gagal/dilewati)';
        }
        $this->ajax_response(true, $message);
    }

    public function print_receipt($id) {
        $this->check_permission('penjualan', 'view');
        $penjualan = $this->penjualan_model->get_by_id_with_relations($id);
        if (!$penjualan) {
            show_404();
            return;
        }
        $data = array(
            'penjualan' => $penjualan,
            'details' => $this->penjualan_model->get_details($id),
            'profil_apotek' => $this->data['profil_apotek']
        );
        $this->load->view('penjualan/daftar_penjualan/print', $data);
    }
}


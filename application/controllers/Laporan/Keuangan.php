<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keuangan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('buku_kas_model');
        $this->load->model('akun_kas_model');
        $this->check_permission('laporan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Laporan Keuangan',
            'breadcrumb' => array(
                array('label' => 'Laporan', 'url' => '#'),
                array('label' => 'Laporan Keuangan')
            ),
            'akun_kas_list' => $this->akun_kas_model->get_all(array('is_active' => 1))
        );
        $this->render('laporan/keuangan/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('laporan', 'view');
        $params = array(
            'columns' => array('bk.id', 'bk.tanggal', 'ak.nama', 'bk.keterangan', 'bk.nominal', 'bk.nominal'),
            'search_fields' => array('bk.keterangan', 'ak.nama'),
            'default_order' => 'bk.tanggal',
            'build_query' => function () {
                $this->db->select('bk.*, ak.nama as akun_kas_nama');
                $this->db->from('buku_kas bk');
                $this->db->join('akun_kas ak', 'ak.id = bk.akun_kas_id', 'left');
            },
            'apply_filters' => function () {
                $akun_kas_id = $this->input->post('akun_kas_id');
                $start_date = $this->input->post('start_date');
                $end_date = $this->input->post('end_date');
                if ($akun_kas_id) {
                    $this->db->where('bk.akun_kas_id', $akun_kas_id);
                }
                if ($start_date) {
                    $this->db->where('bk.tanggal >=', $start_date);
                }
                if ($end_date) {
                    $this->db->where('bk.tanggal <=', $end_date);
                }
            },
        );
        $result = $this->datatable_execute($params);
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                $row->id,
                date('d/m/Y', strtotime($row->tanggal ?: 'now')),
                $row->akun_kas_nama ?: '-',
                $row->keterangan ?: '-',
                $row->tipe == 'debit' ? 'Rp ' . number_format($row->nominal ?: 0, 0, ',', '.') : '-',
                $row->tipe == 'kredit' ? 'Rp ' . number_format($row->nominal ?: 0, 0, ',', '.') : '-'
            );
        }
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    public function ajax_export() {
        $this->check_permission('laporan', 'view');
        $akun_kas_id = $this->input->get('akun_kas_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        $this->db->select('bk.*, ak.nama as akun_kas_nama');
        $this->db->from('buku_kas bk');
        $this->db->join('akun_kas ak', 'ak.id = bk.akun_kas_id', 'left');
        
        if ($akun_kas_id) {
            $this->db->where('bk.akun_kas_id', $akun_kas_id);
        }
        if ($start_date) {
            $this->db->where('bk.tanggal >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('bk.tanggal <=', $end_date);
        }
        
        $this->db->order_by('bk.tanggal', 'DESC');
        $records = $this->db->get()->result();
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=laporan_keuangan_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, array('Tanggal', 'Akun Kas', 'Keterangan', 'Debit', 'Kredit'));
        
        foreach ($records as $row) {
            fputcsv($output, array(
                date('d/m/Y', strtotime($row->tanggal)),
                $row->akun_kas_nama ?: '-',
                $row->keterangan ?: '-',
                $row->tipe == 'debit' ? number_format($row->nominal ?: 0, 0, ',', '.') : '0',
                $row->tipe == 'kredit' ? number_format($row->nominal ?: 0, 0, ',', '.') : '0'
            ));
        }
        
        fclose($output);
        exit;
    }
}


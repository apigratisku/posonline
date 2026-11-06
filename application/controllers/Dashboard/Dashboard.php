<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('dashboard', 'view');
        $this->load->model('penjualan_model');
        $this->load->model('faktur_pembelian_model');
        $this->load->model('master_produk_model');
        $this->load->model('akun_kas_model');
    }
    
    public function index() {
        $data = array(
            'title' => 'Dashboard Umum',
            'breadcrumb' => array(
                array('label' => 'Dashboard')
            ),
            'is_kasir_role' => ($this->session->userdata('peran_kode') === 'kasir')
        );
        
        $this->render('dashboard/umum', $data);
    }
    
    public function penjualan() {
        $data = array(
            'title' => 'Dashboard Penjualan',
            'breadcrumb' => array(
                array('label' => 'Dashboard', 'url' => 'dashboard'),
                array('label' => 'Penjualan')
            )
        );
        
        $this->render('dashboard/penjualan', $data);
    }
    
    public function pembelian() {
        $data = array(
            'title' => 'Dashboard Pembelian',
            'breadcrumb' => array(
                array('label' => 'Dashboard', 'url' => 'dashboard'),
                array('label' => 'Pembelian')
            )
        );
        
        $this->render('dashboard/pembelian', $data);
    }
    
    public function persediaan() {
        $data = array(
            'title' => 'Dashboard Persediaan',
            'breadcrumb' => array(
                array('label' => 'Dashboard', 'url' => 'dashboard'),
                array('label' => 'Persediaan')
            )
        );
        
        $this->render('dashboard/persediaan', $data);
    }
    
    public function keuangan() {
        $data = array(
            'title' => 'Dashboard Keuangan',
            'breadcrumb' => array(
                array('label' => 'Dashboard', 'url' => 'dashboard'),
                array('label' => 'Keuangan')
            )
        );
        
        $this->render('dashboard/keuangan', $data);
    }
    
    public function ajax_statistik() {
        $peran_kode = $this->session->userdata('peran_kode');
        $is_kasir = ($peran_kode === 'kasir');
        $user_id = $is_kasir ? $this->auth->get_user_id() : null;

        $start_date = $this->input->post('start_date') ?: date('Y-m-01');
        $end_date = $this->input->post('end_date') ?: date('Y-m-d 23:59:59');
        if (strlen($end_date) === 10) {
            $end_date .= ' 23:59:59';
        }

        $penjualan_total = $this->penjualan_model->get_total_by_period($start_date, $end_date, $user_id);
        $jumlah_transaksi = $this->penjualan_model->count_by_period($start_date, $end_date, $user_id);
        $chart_penjualan = $this->_build_chart_series(
            $this->penjualan_model->get_daily_totals($start_date, $end_date, $user_id),
            $start_date,
            $end_date
        );

        $pembelian_total = 0;
        $chart_pembelian = array('labels' => array(), 'values' => array());
        $kas_total = 0;

        if (!$is_kasir) {
            $pembelian_total = $this->faktur_pembelian_model->get_total_by_period($start_date, $end_date);
            $chart_pembelian = $this->_build_chart_series(
                $this->faktur_pembelian_model->get_daily_totals($start_date, $end_date),
                $start_date,
                $end_date
            );

            $kas_row = $this->db->select_sum('saldo_akhir')
                ->where('is_active', 1)
                ->get('akun_kas')->row();
            $kas_total = floatval(isset($kas_row->saldo_akhir) ? $kas_row->saldo_akhir : 0);
        }

        $produk_total = $this->db->where('is_active', 1)->count_all_results('master_produk');
        $produk_low_stock = $this->db->where('is_active', 1)
            ->where('stok_akhir <= stok_minimal')
            ->count_all_results('master_produk');

        $this->ajax_response(true, 'Data berhasil diambil', array(
            'scope' => $is_kasir ? 'kasir' : 'global',
            'penjualan' => $penjualan_total,
            'jumlah_transaksi' => $jumlah_transaksi,
            'pembelian' => $pembelian_total,
            'produk_total' => $produk_total,
            'produk_low_stock' => $produk_low_stock,
            'kas_total' => $kas_total,
            'chart_penjualan' => $chart_penjualan,
            'chart_pembelian' => $chart_pembelian,
            'period_label' => 'Bulan ini'
        ));
    }

    /**
     * Build chart labels/values for each day in period
     */
    private function _build_chart_series($rows, $start_date, $end_date) {
        $start = new DateTime(date('Y-m-d', strtotime($start_date)));
        $end = new DateTime(date('Y-m-d', strtotime($end_date)));
        $end->modify('+1 day');

        $totals = array();
        foreach ($rows as $row) {
            $key = date('Y-m-d', strtotime($row->tanggal));
            $totals[$key] = floatval($row->total);
        }

        $labels = array();
        $values = array();
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        foreach ($period as $day) {
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('j');
            $values[] = isset($totals[$key]) ? $totals[$key] : 0;
        }

        return array('labels' => $labels, 'values' => $values);
    }
}


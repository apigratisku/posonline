<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterProduk extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('master_produk_model');
        $this->load->model('master_kategori_model');
        $this->load->model('master_golongan_model');
        $this->load->model('master_satuan_model');
        $this->load->model('master_rak_model');
        $this->load->model('master_gudang_model');
        $this->load->model('master_pajak_model');
        $this->load->model('kartu_stok_model');
        $this->check_permission('master', 'view');
    }
    
    /**
     * Index - List page
     */
    public function index() {
        $data = array(
            'title' => 'Master Produk',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Produk')
            ),
            'kategoris' => $this->master_kategori_model->get_all(array('is_active' => 1)),
            'golongs' => $this->master_golongan_model->get_all(array('is_active' => 1)),
            'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
            'raks' => array(),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
            'pajaks' => $this->master_pajak_model->get_all(array('is_active' => 1))
        );
        
        $this->render('master_data/produk/index', $data);
    }
    
    /**
     * AJAX List - DataTables server-side
     */
    public function ajax_list() {
        $this->check_permission('master', 'view');
        
        $params = array(
            'columns' => array('mp.id', 'mp.kode', 'mp.barcode', 'mp.nama', 'mk.nama', 'ms.nama', 'mg.nama', 'mp.harga_beli', 'mp.persentase_keuntungan', 'mp.harga_jual', 'mp.stok_akhir', 'mp.is_active'),
            'search_fields' => array('mp.kode', 'mp.barcode', 'mp.nama', 'mk.nama', 'ms.nama')
        );
        
        $result = $this->master_produk_model->datatable($params);
        
        // Format data
        $data = array();
        foreach ($result['data'] as $row) {
            $stok = $this->master_produk_model->get_stok($row->id, $row->gudang_id);
            $data[] = array(
                $row->id,
                $row->kode,
                isset($row->barcode) ? $row->barcode : '-',
                $row->nama,
                isset($row->kategori_nama) ? $row->kategori_nama : '-',
                isset($row->satuan_nama) ? $row->satuan_nama : '-',
                isset($row->gudang_nama) ? $row->gudang_nama : '-',
                number_format($row->harga_beli, 0, ',', '.'),
                number_format($row->persentase_keuntungan, 2, ',', '.') . '%',
                number_format($row->harga_jual, 0, ',', '.'),
                '<span class="badge ' . ($stok < $row->stok_minimal ? 'bg-danger' : 'bg-success') . '">' . $stok . '</span>',
                $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>',
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        
        $this->datatable_json($result);
    }
    
    /**
     * Get action buttons
     */
    private function get_action_buttons($id) {
        $buttons = '';
        
        if ($this->auth->has_permission('master', 'view')) {
            $buttons .= '<a href="' . base_url('masterdata/masterproduk/view/' . $id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a> ';
            $buttons .= '<a href="' . base_url('masterdata/masterproduk/print_barcode/' . $id) . '" class="btn btn-sm btn-dark" title="Cetak Barcode" target="_blank"><i class="fas fa-barcode"></i></a> ';
            $buttons .= '<button class="btn btn-sm btn-secondary" onclick="viewKartuStok(' . $id . ')"><i class="fas fa-list"></i> Kartu Stok</button> ';
        }
        
        if ($this->auth->has_permission('master', 'update')) {
            $buttons .= '<a href="' . base_url('masterdata/masterproduk/edit/' . $id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a> ';
        }
        
        if ($this->auth->has_permission('master', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        
        return $buttons;
    }
    
    /**
     * Create - Show create form
     */
    public function create() {
        $this->check_permission('master', 'create');
        $data = array(
            'title' => 'Tambah Master Produk',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Produk', 'url' => base_url('masterdata/masterproduk')),
                array('label' => 'Tambah')
            ),
            'form_title' => 'Tambah Produk',
            'form_action' => base_url('masterdata/masterproduk/store'),
            'back_url' => 'masterdata/masterproduk',
            'kategoris' => $this->master_kategori_model->get_all(array('is_active' => 1)),
            'golongs' => $this->master_golongan_model->get_all(array('is_active' => 1)),
            'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
            'raks' => array(),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
            'pajaks' => $this->master_pajak_model->get_all(array('is_active' => 1))
        );
        $this->render('master_data/produk/create', $data);
    }
    
    /**
     * Store - Save new record
     */
    public function store() {
        $this->check_permission('master', 'create');
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = array(
                'title' => 'Tambah Master Produk',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Produk', 'url' => base_url('masterdata/masterproduk')),
                    array('label' => 'Tambah')
                ),
                'form_title' => 'Tambah Produk',
                'form_action' => base_url('masterdata/masterproduk/store'),
                'back_url' => 'masterdata/masterproduk',
                'kategoris' => $this->master_kategori_model->get_all(array('is_active' => 1)),
                'golongs' => $this->master_golongan_model->get_all(array('is_active' => 1)),
                'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
                'raks' => array(),
                'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
                'pajaks' => $this->master_pajak_model->get_all(array('is_active' => 1))
            );
            $this->render('master_data/produk/create', $data);
            return;
        }
        if ($this->master_produk_model->kode_exists($this->input->post('kode'))) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/masterproduk/create');
            return;
        }
        $barcode = $this->input->post('barcode');
        if (!empty($barcode) && $this->master_produk_model->barcode_exists($barcode)) {
            $this->set_flash('error', 'Barcode sudah digunakan');
            redirect('masterdata/masterproduk/create');
            return;
        }
        $rak_error = $this->validate_rak_gudang_relation(
            $this->input->post('rak_id'),
            $this->input->post('gudang_id')
        );
        if ($rak_error) {
            $this->set_flash('error', $rak_error);
            redirect('masterdata/masterproduk/create');
            return;
        }
        $harga_beli = floatval($this->input->post('harga_beli')) ?: 0;
        $persentase = floatval($this->input->post('persentase_keuntungan')) ?: 0;
        if ($persentase < 0) {
            $persentase = 0;
        }
        $harga_jual = $this->hitung_harga_jual($harga_beli, $persentase);
        $margin_resep = floatval($this->input->post('margin_resep')) ?: 0;
        $harga_resep = $this->hitung_harga_margin($harga_beli, $margin_resep, $this->input->post('harga_resep'));
        $margin_pelanggan = floatval($this->input->post('margin_pelanggan')) ?: 0;
        $harga_pelanggan = $this->hitung_harga_margin($harga_beli, $margin_pelanggan, $this->input->post('harga_pelanggan'));
        $data = array(
            'kode' => $this->input->post('kode'),
            'barcode' => $barcode,
            'nama' => $this->input->post('nama'),
            'pabrik' => $this->input->post('pabrik'),
            'kategori_id' => $this->input->post('kategori_id') ?: null,
            'golongan_id' => $this->input->post('golongan_id') ?: null,
            'satuan_id' => $this->input->post('satuan_id') ?: null,
            'satuan_beli_id' => $this->input->post('satuan_beli_id') ?: null,
            'satuan_konversi' => intval($this->input->post('satuan_konversi')) ?: 1,
            'indikasi' => $this->input->post('indikasi'),
            'rak_id' => $this->input->post('rak_id') ?: null,
            'gudang_id' => $this->input->post('gudang_id') ?: null,
            'pajak_id' => $this->input->post('pajak_id') ?: null,
            'harga_beli' => $harga_beli,
            'persentase_keuntungan' => $persentase,
            'harga_jual' => $harga_jual,
            'margin_resep' => $margin_resep,
            'harga_resep' => $harga_resep,
            'margin_pelanggan' => $margin_pelanggan,
            'harga_pelanggan' => $harga_pelanggan,
            'stok_minimal' => $this->input->post('stok_minimal') ?: 0,
            'stok_maksimal' => $this->input->post('stok_maksimal') ?: 0,
            'stok_awal' => $this->input->post('stok_awal') ?: 0,
            'expired_date' => $this->input->post('expired_date') ?: null,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_resep' => $this->input->post('is_resep') ? 1 : 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        $data['stok_akhir'] = $data['stok_awal'];
        $insert_id = $this->master_produk_model->insert($data);
        if ($insert_id) {
            if ($data['stok_awal'] > 0 && $data['gudang_id']) {
                $kartu_data = array(
                    'produk_id' => $insert_id,
                    'gudang_id' => $data['gudang_id'],
                    'tanggal' => date('Y-m-d'),
                    'referensi' => 'STOK-AWAL',
                    'tipe' => 'masuk',
                    'qty_masuk' => $data['stok_awal'],
                    'qty_keluar' => 0,
                    'keterangan' => 'Stok awal produk'
                );
                $this->kartu_stok_model->insert_kartu_stok($kartu_data);
            }
            $this->set_flash('success', 'Data berhasil ditambahkan');
            redirect('masterdata/masterproduk');
        } else {
            $this->set_flash('error', 'Gagal menambahkan data');
            redirect('masterdata/masterproduk/create');
        }
    }
    
    /**
     * Edit - Show edit form
     */
    public function edit($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_produk_model->get_by_id_with_relations($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/masterproduk');
            return;
        }
        $view_data = array(
            'title' => 'Edit Master Produk',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Produk', 'url' => base_url('masterdata/masterproduk')),
                array('label' => 'Edit')
            ),
            'form_title' => 'Edit Produk',
            'form_action' => base_url('masterdata/masterproduk/update/' . $id),
            'back_url' => 'masterdata/masterproduk',
            'data' => $data,
            'kategoris' => $this->master_kategori_model->get_all(array('is_active' => 1)),
            'golongs' => $this->master_golongan_model->get_all(array('is_active' => 1)),
            'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
            'raks' => $this->get_raks_for_gudang($data->gudang_id),
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
            'pajaks' => $this->master_pajak_model->get_all(array('is_active' => 1))
        );
        $this->render('master_data/produk/edit', $view_data);
    }
    
    /**
     * Update - Update existing record
     */
    public function update($id) {
        $this->check_permission('master', 'update');
        $data = $this->master_produk_model->get_by_id($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/masterproduk');
            return;
        }
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        if ($this->form_validation->run() == false) {
            $data = $this->master_produk_model->get_by_id_with_relations($id);
            $view_data = array(
                'title' => 'Edit Master Produk',
                'breadcrumb' => array(
                    array('label' => 'Master Data', 'url' => '#'),
                    array('label' => 'Master Produk', 'url' => base_url('masterdata/masterproduk')),
                    array('label' => 'Edit')
                ),
                'form_title' => 'Edit Produk',
                'form_action' => base_url('masterdata/masterproduk/update/' . $id),
                'back_url' => 'masterdata/masterproduk',
                'data' => $data,
                'kategoris' => $this->master_kategori_model->get_all(array('is_active' => 1)),
                'golongs' => $this->master_golongan_model->get_all(array('is_active' => 1)),
                'satuans' => $this->master_satuan_model->get_all(array('is_active' => 1)),
                'raks' => $this->get_raks_for_gudang($data->gudang_id),
                'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1)),
                'pajaks' => $this->master_pajak_model->get_all(array('is_active' => 1))
            );
            $this->render('master_data/produk/edit', $view_data);
            return;
        }
        if ($this->master_produk_model->kode_exists($this->input->post('kode'), $id)) {
            $this->set_flash('error', 'Kode sudah digunakan');
            redirect('masterdata/masterproduk/edit/' . $id);
            return;
        }
        $barcode = $this->input->post('barcode');
        if (!empty($barcode) && $this->master_produk_model->barcode_exists($barcode, $id)) {
            $this->set_flash('error', 'Barcode sudah digunakan');
            redirect('masterdata/masterproduk/edit/' . $id);
            return;
        }
        $rak_error = $this->validate_rak_gudang_relation(
            $this->input->post('rak_id'),
            $this->input->post('gudang_id')
        );
        if ($rak_error) {
            $this->set_flash('error', $rak_error);
            redirect('masterdata/masterproduk/edit/' . $id);
            return;
        }
        $harga_beli = floatval($this->input->post('harga_beli')) ?: 0;
        $persentase = floatval($this->input->post('persentase_keuntungan')) ?: 0;
        if ($persentase < 0) {
            $persentase = 0;
        }
        $harga_jual = $this->hitung_harga_jual($harga_beli, $persentase);
        $margin_resep = floatval($this->input->post('margin_resep')) ?: 0;
        $harga_resep = $this->hitung_harga_margin($harga_beli, $margin_resep, $this->input->post('harga_resep'));
        $margin_pelanggan = floatval($this->input->post('margin_pelanggan')) ?: 0;
        $harga_pelanggan = $this->hitung_harga_margin($harga_beli, $margin_pelanggan, $this->input->post('harga_pelanggan'));
        $update_data = array(
            'kode' => $this->input->post('kode'),
            'barcode' => $barcode,
            'nama' => $this->input->post('nama'),
            'pabrik' => $this->input->post('pabrik'),
            'kategori_id' => $this->input->post('kategori_id') ?: null,
            'golongan_id' => $this->input->post('golongan_id') ?: null,
            'satuan_id' => $this->input->post('satuan_id') ?: null,
            'satuan_beli_id' => $this->input->post('satuan_beli_id') ?: null,
            'satuan_konversi' => intval($this->input->post('satuan_konversi')) ?: 1,
            'indikasi' => $this->input->post('indikasi'),
            'rak_id' => $this->input->post('rak_id') ?: null,
            'gudang_id' => $this->input->post('gudang_id') ?: null,
            'pajak_id' => $this->input->post('pajak_id') ?: null,
            'harga_beli' => $harga_beli,
            'persentase_keuntungan' => $persentase,
            'harga_jual' => $harga_jual,
            'margin_resep' => $margin_resep,
            'harga_resep' => $harga_resep,
            'margin_pelanggan' => $margin_pelanggan,
            'harga_pelanggan' => $harga_pelanggan,
            'stok_minimal' => $this->input->post('stok_minimal') ?: 0,
            'stok_maksimal' => $this->input->post('stok_maksimal') ?: 0,
            'expired_date' => $this->input->post('expired_date') ?: null,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_resep' => $this->input->post('is_resep') ? 1 : 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        if ($this->master_produk_model->update($id, $update_data)) {
            $this->set_flash('success', 'Data berhasil diupdate');
            redirect('masterdata/masterproduk');
        } else {
            $this->set_flash('error', 'Gagal mengupdate data');
            redirect('masterdata/masterproduk/edit/' . $id);
        }
    }
    
    /**
     * Hitung harga jual dari harga beli + persentase keuntungan (dibulatkan ke rupiah)
     */
    private function hitung_harga_jual($harga_beli, $persentase) {
        $harga_beli = floatval($harga_beli);
        $persentase = floatval($persentase);
        if ($harga_beli <= 0 || $persentase <= 0) {
            return round($harga_beli, 0);
        }
        return round($harga_beli + ($harga_beli * $persentase / 100), 0);
    }

    /**
     * Hitung harga resep / pelanggan.
     * Jika harga diinput manual, pakai nilai tersebut; jika kosong, hitung dari margin.
     */
    private function hitung_harga_margin($harga_beli, $margin, $harga_manual = null) {
        $harga_manual = floatval($harga_manual);
        $harga_beli = floatval($harga_beli);
        $margin = floatval($margin);
        if ($harga_manual > 0) {
            return round($harga_manual, 0);
        }
        if ($harga_beli <= 0 || $margin <= 0) {
            return round($harga_beli, 0);
        }
        return round($harga_beli + ($harga_beli * $margin / 100), 0);
    }

    /**
     * View - Show single record
     */
    public function view($id) {
        $this->check_permission('master', 'view');
        $data = $this->master_produk_model->get_by_id_with_relations($id);
        if (!$data) {
            $this->set_flash('error', 'Data tidak ditemukan');
            redirect('masterdata/masterproduk');
            return;
        }
        $view_data = array(
            'title' => 'Detail Master Produk',
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Produk', 'url' => base_url('masterdata/masterproduk')),
                array('label' => 'Detail')
            ),
            'back_url' => 'masterdata/masterproduk',
            'data' => $data
        );
        $this->render('master_data/produk/view', $view_data);
    }

    /**
     * Print Barcode - Standalone barcode label page
     */
    public function print_barcode($id) {
        $this->check_permission('master', 'view');
        $produk = $this->master_produk_model->get_by_id_with_relations($id);
        if (!$produk) {
            show_404();
            return;
        }
        $this->load->view('master_data/produk/print_barcode', array('produk' => $produk));
    }
    
    /**
     * AJAX Get - Get single record
     */
    public function ajax_get($id) {
        $this->check_permission('master', 'view');
        
        $data = $this->master_produk_model->get_by_id_with_relations($id);
        
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
            $this->check_permission('master', 'update');
        } else {
            $this->check_permission('master', 'create');
        }
        
        // Validation
        $this->form_validation->set_rules('kode', 'Kode', 'required|trim');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        
        if ($this->form_validation->run() == false) {
            $this->ajax_response(false, 'Validasi gagal', null, $this->form_validation->error_array());
            return;
        }
        
        // Check kode uniqueness
        if ($this->master_produk_model->kode_exists($this->input->post('kode'), $id)) {
            $this->ajax_response(false, 'Kode sudah digunakan');
            return;
        }
        
        // Check barcode uniqueness if provided
        $barcode = $this->input->post('barcode');
        if (!empty($barcode) && $this->master_produk_model->barcode_exists($barcode, $id)) {
            $this->ajax_response(false, 'Barcode sudah digunakan');
            return;
        }

        $rak_error = $this->validate_rak_gudang_relation(
            $this->input->post('rak_id'),
            $this->input->post('gudang_id')
        );
        if ($rak_error) {
            $this->ajax_response(false, $rak_error);
            return;
        }
        
        $harga_beli = floatval($this->input->post('harga_beli')) ?: 0;
        $persentase = floatval($this->input->post('persentase_keuntungan')) ?: 0;
        if ($persentase < 0) {
            $persentase = 0;
        }
        $harga_jual = $this->hitung_harga_jual($harga_beli, $persentase);
        $margin_resep = floatval($this->input->post('margin_resep')) ?: 0;
        $harga_resep = $this->hitung_harga_margin($harga_beli, $margin_resep, $this->input->post('harga_resep'));
        $margin_pelanggan = floatval($this->input->post('margin_pelanggan')) ?: 0;
        $harga_pelanggan = $this->hitung_harga_margin($harga_beli, $margin_pelanggan, $this->input->post('harga_pelanggan'));
        $data = array(
            'kode' => $this->input->post('kode'),
            'barcode' => $barcode,
            'nama' => $this->input->post('nama'),
            'pabrik' => $this->input->post('pabrik'),
            'kategori_id' => $this->input->post('kategori_id') ?: null,
            'golongan_id' => $this->input->post('golongan_id') ?: null,
            'satuan_id' => $this->input->post('satuan_id') ?: null,
            'satuan_beli_id' => $this->input->post('satuan_beli_id') ?: null,
            'satuan_konversi' => intval($this->input->post('satuan_konversi')) ?: 1,
            'indikasi' => $this->input->post('indikasi'),
            'rak_id' => $this->input->post('rak_id') ?: null,
            'gudang_id' => $this->input->post('gudang_id') ?: null,
            'pajak_id' => $this->input->post('pajak_id') ?: null,
            'harga_beli' => $harga_beli,
            'persentase_keuntungan' => $persentase,
            'harga_jual' => $harga_jual,
            'margin_resep' => $margin_resep,
            'harga_resep' => $harga_resep,
            'margin_pelanggan' => $margin_pelanggan,
            'harga_pelanggan' => $harga_pelanggan,
            'stok_minimal' => $this->input->post('stok_minimal') ?: 0,
            'stok_maksimal' => $this->input->post('stok_maksimal') ?: 0,
            'stok_awal' => $this->input->post('stok_awal') ?: 0,
            'expired_date' => $this->input->post('expired_date') ?: null,
            'deskripsi' => $this->input->post('deskripsi'),
            'is_resep' => $this->input->post('is_resep') ? 1 : 0,
            'is_active' => $this->input->post('is_active') ? 1 : 0
        );
        
        if ($id) {
            // Update
            if ($this->master_produk_model->update($id, $data)) {
                $this->ajax_response(true, 'Data berhasil diupdate');
            } else {
                $this->ajax_response(false, 'Gagal mengupdate data');
            }
        } else {
            // Insert
            $data['stok_akhir'] = $data['stok_awal'];
            $insert_id = $this->master_produk_model->insert($data);
            if ($insert_id) {
                // Create initial stock card entry if stok_awal > 0
                if ($data['stok_awal'] > 0 && $data['gudang_id']) {
                    $kartu_data = array(
                        'produk_id' => $insert_id,
                        'gudang_id' => $data['gudang_id'],
                        'tanggal' => date('Y-m-d'),
                        'referensi' => 'STOK-AWAL',
                        'tipe' => 'masuk',
                        'qty_masuk' => $data['stok_awal'],
                        'qty_keluar' => 0,
                        'keterangan' => 'Stok awal produk'
                    );
                    $this->kartu_stok_model->insert_kartu_stok($kartu_data);
                }
                
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
        $this->check_permission('master', 'delete');

        $produk = $this->master_produk_model->get_by_id($id);
        if (!$produk) {
            $this->ajax_response(false, 'Data tidak ditemukan');
            return;
        }

        // Blokir hapus jika produk masih direferensikan transaksi
        if ($this->master_produk_model->is_in_use($id)) {
            $this->ajax_response(false, 'Data tidak dapat dihapus karena masih digunakan pada transaksi. Nonaktifkan produk bila sudah tidak dijual.');
            return;
        }

        if ($this->master_produk_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    /**
     * AJAX Search - For autocomplete
     */
    public function ajax_search() {
        $search = $this->input->get('q');
        $gudang_id = $this->input->get('gudang_id');
        $data = $this->master_produk_model->search($search, 20);
        
        $results = array();
        foreach ($data as $row) {
            $stok = $this->master_produk_model->get_stok($row->id, $gudang_id);
            $results[] = array(
                'id' => $row->id,
                'text' => $row->nama . ' (' . $row->kode . ') - Stok: ' . $stok,
                'kode' => $row->kode,
                'nama' => $row->nama,
                'harga_jual' => $row->harga_jual,
                'stok' => $stok
            );
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('results' => $results)));
    }
    
    /**
     * View Kartu Stok
     */
    public function kartu_stok($produk_id) {
        $produk = $this->master_produk_model->get_by_id_with_relations($produk_id);
        if (!$produk) {
            show_404();
        }
        
        $data = array(
            'title' => 'Kartu Stok - ' . $produk->nama,
            'breadcrumb' => array(
                array('label' => 'Master Data', 'url' => '#'),
                array('label' => 'Master Produk', 'url' => site_url('masterdata/masterproduk')),
                array('label' => 'Kartu Stok')
            ),
            'produk' => $produk,
            'gudangs' => $this->master_gudang_model->get_all(array('is_active' => 1))
        );
        
        $this->render('master_data/produk/kartu_stok', $data);
    }
    
    /**
     * AJAX List Kartu Stok
     */
    public function ajax_list_kartu_stok() {
        $produk_id = $this->input->post('produk_id');
        $gudang_id = $this->input->post('gudang_id');
        
        $this->load->model('kartu_stok_model');
        
        $params = array(
            'columns' => array('ks.tanggal', 'ks.referensi', 'ks.tipe', 'ks.qty_masuk', 'ks.qty_keluar', 'ks.qty_saldo', 'ks.keterangan'),
            'search_fields' => array('ks.referensi', 'ks.keterangan'),
            'produk_id' => $produk_id
        );
        
        if ($gudang_id) {
            $params['gudang_id'] = $gudang_id;
        }
        
        $result = $this->kartu_stok_model->datatable($params);
        
        // Format data
        $data = array();
        foreach ($result['data'] as $row) {
            $data[] = array(
                date('d/m/Y', strtotime($row->tanggal)),
                isset($row->referensi) ? $row->referensi : '-',
                '<span class="badge bg-' . ($row->tipe == 'masuk' ? 'success' : ($row->tipe == 'keluar' ? 'danger' : 'warning')) . '">' . strtoupper($row->tipe) . '</span>',
                number_format($row->qty_masuk, 0, ',', '.'),
                number_format($row->qty_keluar, 0, ',', '.'),
                '<strong>' . number_format($row->qty_saldo, 0, ',', '.') . '</strong>',
                isset($row->keterangan) ? $row->keterangan : '-'
            );
        }
        
        $result['data'] = $data;
        
        $this->datatable_json($result);
    }

    /**
     * AJAX - Get rak list by gudang for cascade dropdown
     */
    public function ajax_rak_by_gudang($gudang_id = null) {
        $this->check_permission('master', 'view');
        $gudang_id = $gudang_id ?: $this->input->get('gudang_id');
        $raks = $this->get_raks_for_gudang($gudang_id);
        $results = array();
        foreach ($raks as $rak) {
            $results[] = array(
                'id' => $rak->id,
                'nama' => $rak->nama
            );
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array('status' => true, 'data' => $results)));
    }
    
    private function get_raks_for_gudang($gudang_id) {
        if (empty($gudang_id)) {
            return array();
        }
        return $this->master_rak_model->get_all(array(
            'is_active' => 1,
            'gudang_id' => $gudang_id
        ));
    }

    private function validate_rak_gudang_relation($rak_id, $gudang_id) {
        if (empty($rak_id)) {
            return null;
        }
        $rak = $this->master_rak_model->get_by_id($rak_id);
        if (!$rak) {
            return 'Rak tidak ditemukan';
        }
        if (!empty($gudang_id) && intval($rak->gudang_id) !== intval($gudang_id)) {
            return 'Rak yang dipilih tidak berada di gudang yang dipilih';
        }
        return null;
    }
}


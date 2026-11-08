<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reset data transaksi (admin only).
 *
 * Menghapus data penjualan, stok, produk, pembelian, dan keuangan.
 * Konfigurasi toko (profil_toko, konfigurasi, metode_pembayaran, master_*)
 * TIDAK disentuh.
 */
class ResetData extends MY_Controller {

    private $url_prefix = 'pengaturan/reset-data';

    /**
     * Daftar tabel per grup. Urutan: tabel anak dulu, lalu induk.
     */
    private $groups = array(
        'penjualan' => array(
            'label'       => 'Data Penjualan',
            'description' => 'Transaksi penjualan, faktur, pesanan, retur, resep, kunjungan pasien, dan rekam medis.',
            'tables'      => array(
                'penjualan_detail',
                'penjualan',
                'faktur_penjualan_detail',
                'faktur_penjualan',
                'pesanan_penjualan_detail',
                'pesanan_penjualan',
                'retur_penjualan_detail',
                'retur_penjualan',
                'penerimaan_resep_detail',
                'penerimaan_resep',
                'pemeriksaan_lab',
                'rekam_medis',
                'kunjungan_pasien',
            ),
        ),
        'stok' => array(
            'label'       => 'Data Stok',
            'description' => 'Kartu stok, stok opname, penyesuaian, perpindahan, stok kadaluarsa, dan defecta.',
            'tables'      => array(
                'kartu_stok',
                'stok_opname_detail',
                'stok_opname',
                'stok_kadaluarsa',
                'penyesuaian_stok',
                'perpindahan_stok_detail',
                'perpindahan_stok',
                'defecta',
            ),
        ),
        'produk' => array(
            'label'       => 'Daftar Produk',
            'description' => 'Master produk beserta data yang bergantung padanya (template racikan dan promo bundel).',
            'tables'      => array(
                'master_produk',
                'template_racikan_detail',
                'template_racikan',
                'promo_bundel_detail',
                'promo_bundel',
            ),
        ),
        'pembelian' => array(
            'label'       => 'Data Pembelian',
            'description' => 'Pesanan, faktur, retur, rencana pembelian, konsinyasi, dan mutasi antar outlet.',
            'tables'      => array(
                'faktur_pembelian_detail',
                'faktur_pembelian',
                'retur_pembelian_detail',
                'retur_pembelian',
                'pesanan_pembelian_detail',
                'pesanan_pembelian',
                'rencana_pembelian_detail',
                'rencana_pembelian',
                'konsinyasi_masuk_detail',
                'konsinyasi_masuk',
                'konsinyasi_retur',
                'mutasi_antar_outlet_detail',
                'mutasi_antar_outlet',
                'permintaan_mutasi_detail',
                'permintaan_mutasi',
            ),
        ),
        'keuangan' => array(
            'label'       => 'Data Keuangan',
            'description' => 'Buku kas, buku besar, piutang, dan utang usaha.',
            'tables'      => array(
                'piutang_usaha',
                'utang_usaha',
                'buku_besar',
                'buku_kas',
            ),
        ),
    );

    /**
     * Tabel yang sengaja dipertahankan (informasi untuk UI).
     */
    private $protected_tables = array(
        'profil_toko', 'profil_apotek', 'konfigurasi', 'metode_pembayaran',
        'master_pajak', 'master_kategori', 'master_satuan', 'master_rak',
        'master_gudang', 'master_membership', 'master_shift', 'users', 'peran',
        'modul', 'supplier', 'pelanggan', 'coa', 'akun_kas', 'log_aktivitas',
    );

    public function __construct() {
        parent::__construct();
        $this->check_permission('pengaturan', 'update');

        if (!$this->is_admin()) {
            $message = 'Fitur reset data hanya dapat diakses oleh Administrator.';
            if ($this->input->is_ajax_request()) {
                $this->ajax_response(false, $message);
                exit;
            }
            show_error($message, 403, 'Akses Ditolak');
        }
    }

    public function index() {
        $token = bin2hex(random_bytes(16));
        $this->session->set_userdata('reset_data_token', $token);

        $this->render('pengaturan/reset_data/index', array(
            'title'            => 'Reset Data',
            'breadcrumb'       => array(
                array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                array('label' => 'Reset Data'),
            ),
            'groups'           => $this->groups_with_stats(),
            'protected_tables' => $this->protected_tables,
            'token'            => $token,
        ));
    }

    public function execute() {
        if ($this->input->method() !== 'post') {
            redirect($this->url_prefix);
            return;
        }

        // Validasi token sekali pakai (pengganti CSRF yang nonaktif di config).
        $session_token = (string) $this->session->userdata('reset_data_token');
        $post_token    = (string) $this->input->post('token');
        $this->session->unset_userdata('reset_data_token');

        if ($session_token === '' || !hash_equals($session_token, $post_token)) {
            $this->set_flash('error', 'Sesi formulir kedaluwarsa. Silakan muat ulang halaman reset data.');
            redirect($this->url_prefix);
            return;
        }

        // Validasi konfirmasi di sisi server.
        if (strtoupper(trim((string) $this->input->post('konfirmasi'))) !== 'RESET') {
            $this->set_flash('error', 'Konfirmasi tidak valid. Ketik RESET (huruf besar) untuk melanjutkan.');
            redirect($this->url_prefix);
            return;
        }

        $tables = array();
        foreach ($this->groups as $group) {
            $tables = array_merge($tables, $group['tables']);
        }

        $result = $this->truncate_tables($tables);

        if (!empty($result['errors'])) {
            $this->set_flash('error', 'Reset data selesai dengan kegagalan pada ' . count($result['errors'])
                . ' tabel: ' . implode(' | ', $result['errors']));
        } else {
            $this->set_flash('success', 'Reset data berhasil. ' . number_format($result['total'], 0, ',', '.')
                . ' baris dihapus dari ' . count($result['cleared']) . ' tabel. Konfigurasi toko tidak diubah.');
        }

        $this->auth->log_activity('reset_data', 'Reset data penjualan, stok, produk, pembelian, dan keuangan. '
            . 'Baris dihapus: ' . $result['total']
            . '. Gagal: ' . (empty($result['errors']) ? '-' : implode('; ', $result['errors'])));

        redirect($this->url_prefix);
    }

    private function is_admin() {
        $kode = $this->peran ? $this->peran->kode : $this->session->userdata('peran_kode');
        return strtolower(trim((string) $kode)) === 'admin';
    }

    private function groups_with_stats() {
        $groups = array();

        foreach ($this->groups as $key => $group) {
            $tables = array();
            $total  = 0;

            foreach ($group['tables'] as $table) {
                if (!$this->db->table_exists($table)) {
                    $tables[] = array('name' => $table, 'rows' => null);
                    continue;
                }

                $rows   = (int) $this->db->count_all_results($table);
                $total += $rows;
                $tables[] = array('name' => $table, 'rows' => $rows);
            }

            $groups[$key] = array(
                'label'       => $group['label'],
                'description' => $group['description'],
                'tables'      => $tables,
                'total'       => $total,
            );
        }

        return $groups;
    }

    /**
     * Kosongkan tabel. FOREIGN_KEY_CHECKS dimatikan sementara agar urutan
     * tabel tidak menjadi masalah, lalu dikembalikan seperti semula.
     */
    private function truncate_tables($tables) {
        $cleared = array();
        $errors  = array();
        $total   = 0;

        $db_debug = $this->db->db_debug;
        $this->db->db_debug = false;

        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tables as $table) {
            if (!$this->db->table_exists($table)) {
                continue;
            }

            $rows = (int) $this->db->count_all_results($table);

            if ($this->db->query('TRUNCATE TABLE ' . $this->db->escape_identifiers($table))) {
                $cleared[$table] = $rows;
                $total += $rows;
            } else {
                $error = $this->db->error();
                $errors[] = $table . ': ' . $error['message'];
                log_message('error', 'Reset data gagal TRUNCATE ' . $table . ': ' . $error['message']);
            }
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        $this->db->db_debug = $db_debug;

        return array('cleared' => $cleared, 'errors' => $errors, 'total' => $total);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_kas_model extends MY_Model {
    
    protected $table = 'buku_kas';
    protected $primary_key = 'id';
    protected $fillable = array('akun_kas_id', 'tanggal', 'tipe', 'nominal', 'keterangan', 'referensi');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Insert buku kas and update saldo akhir
     */
    public function insert($data) {
        $CI =& get_instance();
        $CI->load->library('auth_lib', NULL, 'auth');
        if ($CI->auth->is_logged_in()) {
            $data['created_by'] = $CI->auth->get_user_id();
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $result = parent::insert($data);
        
        if ($result) {
            // Update saldo akhir akun kas
            $CI->load->model('akun_kas_model');
            $akun_kas_id = $data['akun_kas_id'];
            $saldo = $CI->akun_kas_model->get_saldo($akun_kas_id);
            $CI->akun_kas_model->update($akun_kas_id, array('saldo_akhir' => $saldo));
        }
        
        return $result;
    }
    
    /**
     * Get with relations
     */
    public function get_all_with_relations($akun_kas_id = null, $start_date = null, $end_date = null) {
        $this->db->select('bk.*, ak.kode as akun_kode, ak.nama as akun_nama, ak.tipe as akun_tipe');
        $this->db->from($this->table . ' bk');
        $this->db->join('akun_kas ak', 'ak.id = bk.akun_kas_id');
        
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
        $this->db->order_by('bk.id', 'DESC');
        
        return $this->db->get()->result();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'bk.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('bk.*, ak.kode as akun_kode, ak.nama as akun_nama');
            $this->db->from($this->table . ' bk');
            $this->db->join('akun_kas ak', 'ak.id = bk.akun_kas_id');
        };
        $params['apply_filters'] = function () use ($params) {
            if (isset($params['akun_kas_id'])) {
            $this->db->where('bk.akun_kas_id', $params['akun_kas_id']);
            }
        };
        return $this->datatable_execute($params);
    }
}


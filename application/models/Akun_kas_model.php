<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Akun_kas_model extends MY_Model {
    
    protected $table = 'akun_kas';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'tipe', 'saldo_awal', 'saldo_akhir', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function kode_exists($kode, $exclude_id = null) {
        $this->db->where('kode', $kode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    /**
     * Get current saldo
     */
    public function get_saldo($akun_kas_id) {
        $akun = $this->get_by_id($akun_kas_id);
        if (!$akun) return 0;
        
        $this->db->select_sum('nominal', 'total_debit');
        $this->db->from('buku_kas');
        $this->db->where('akun_kas_id', $akun_kas_id);
        $this->db->where('tipe', 'debit');
        $debit_row = $this->db->get()->row();
        $debit = isset($debit_row->total_debit) ? $debit_row->total_debit : 0;
        
        $this->db->select_sum('nominal', 'total_kredit');
        $this->db->from('buku_kas');
        $this->db->where('akun_kas_id', $akun_kas_id);
        $this->db->where('tipe', 'kredit');
        $kredit_row = $this->db->get()->row();
        $kredit = isset($kredit_row->total_kredit) ? $kredit_row->total_kredit : 0;
        
        return $akun->saldo_awal + $debit - $kredit;
    }
}


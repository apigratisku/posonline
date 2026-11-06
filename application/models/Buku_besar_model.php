<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku_besar_model extends MY_Model {
    
    protected $table = 'buku_besar';
    protected $primary_key = 'id';
    protected $fillable = array('coa_id', 'tanggal', 'no_bukti', 'keterangan', 'debit', 'kredit', 'saldo', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get all with filters
     */
    public function get_all_filtered($search = '', $coa_id = null, $start_date = null, $end_date = null) {
        $this->db->select('bb.*, coa.kode as coa_kode, coa.nama as coa_nama');
        $this->db->from($this->table . ' bb');
        $this->db->join('coa', 'coa.id = bb.coa_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('bb.no_bukti', $search);
            $this->db->or_like('bb.keterangan', $search);
            $this->db->or_like('coa.kode', $search);
            $this->db->or_like('coa.nama', $search);
            $this->db->group_end();
        }
        
        if ($coa_id) {
            $this->db->where('bb.coa_id', $coa_id);
        }
        
        if ($start_date) {
            $this->db->where('bb.tanggal >=', $start_date);
        }
        
        if ($end_date) {
            $this->db->where('bb.tanggal <=', $end_date);
        }
        
        $this->db->order_by('bb.tanggal', 'DESC');
        $this->db->order_by('bb.id', 'DESC');
        return $this->db->get()->result();
    }
    
    /**
     * Get saldo by coa
     */
    public function get_saldo_by_coa($coa_id, $tanggal = null) {
        $this->db->select_sum('debit');
        $this->db->select_sum('kredit');
        $this->db->where('coa_id', $coa_id);
        if ($tanggal) {
            $this->db->where('tanggal <=', $tanggal);
        }
        $result = $this->db->get($this->table)->row();
        return ($result->debit ?: 0) - ($result->kredit ?: 0);
    }
}


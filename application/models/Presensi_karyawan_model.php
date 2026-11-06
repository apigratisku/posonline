<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Presensi_karyawan_model extends MY_Model {
    
    protected $table = 'presensi_karyawan';
    protected $primary_key = 'id';
    protected $fillable = array('user_id', 'shift_id', 'tanggal', 'jam_masuk', 'jam_keluar', 'status', 'keterangan');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get attendance with relations
     */
    public function get_with_relations($user_id = null, $start_date = null, $end_date = null) {
        $this->db->select('pk.*, u.nama_lengkap, ms.nama as shift_nama');
        $this->db->from($this->table . ' pk');
        $this->db->join('users u', 'u.id = pk.user_id');
        $this->db->join('master_shift ms', 'ms.id = pk.shift_id', 'left');
        
        if ($user_id) {
            $this->db->where('pk.user_id', $user_id);
        }
        
        if ($start_date) {
            $this->db->where('pk.tanggal >=', $start_date);
        }
        
        if ($end_date) {
            $this->db->where('pk.tanggal <=', $end_date);
        }
        
        $this->db->order_by('pk.tanggal', 'DESC');
        return $this->db->get()->result();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'pk.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('pk.*, u.nama_lengkap, ms.nama as shift_nama');
            $this->db->from($this->table . ' pk');
            $this->db->join('users u', 'u.id = pk.user_id');
            $this->db->join('master_shift ms', 'ms.id = pk.shift_id', 'left');
        };
        $params['apply_filters'] = function () use ($params) {
            if (isset($params['user_id'])) {
            $this->db->where('pk.user_id', $params['user_id']);
            }
        };
        return $this->datatable_execute($params);
    }
}


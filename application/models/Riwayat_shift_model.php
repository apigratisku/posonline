<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat_shift_model extends MY_Model {
    
    protected $table = 'riwayat_shift';
    protected $primary_key = 'id';
    protected $fillable = array('user_id', 'shift_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'keterangan');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get with relations
     */
    public function get_with_relations($user_id = null, $start_date = null, $end_date = null) {
        $this->db->select('rs.*, u.nama_lengkap, ms.nama as shift_nama, ms.jam_mulai as shift_jam_mulai, ms.jam_selesai as shift_jam_selesai');
        $this->db->from($this->table . ' rs');
        $this->db->join('users u', 'u.id = rs.user_id');
        $this->db->join('master_shift ms', 'ms.id = rs.shift_id');
        
        if ($user_id) {
            $this->db->where('rs.user_id', $user_id);
        }
        
        if ($start_date) {
            $this->db->where('rs.tanggal >=', $start_date);
        }
        
        if ($end_date) {
            $this->db->where('rs.tanggal <=', $end_date);
        }
        
        $this->db->order_by('rs.tanggal', 'DESC');
        $this->db->order_by('rs.jam_mulai', 'DESC');
        return $this->db->get()->result();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'rs.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('rs.*, u.nama_lengkap, ms.nama as shift_nama');
            $this->db->from($this->table . ' rs');
            $this->db->join('users u', 'u.id = rs.user_id');
            $this->db->join('master_shift ms', 'ms.id = rs.shift_id');
        };
        $params['apply_filters'] = function () use ($params) {
            if (isset($params['user_id'])) {
            $this->db->where('rs.user_id', $params['user_id']);
            }
        };
        return $this->datatable_execute($params);
    }
}


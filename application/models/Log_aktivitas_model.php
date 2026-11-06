<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_aktivitas_model extends MY_Model {
    
    protected $table = 'log_aktivitas';
    protected $primary_key = 'id';
    protected $fillable = array('user_id', 'modul', 'aksi', 'deskripsi', 'ip_address', 'user_agent');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Insert log
     */
    public function insert($data) {
        if (!isset($data['ip_address'])) {
            $data['ip_address'] = $this->input->ip_address();
        }
        if (!isset($data['user_agent'])) {
            $data['user_agent'] = $this->input->user_agent();
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        return parent::insert($data);
    }
    
    /**
     * Get with relations
     */
    public function get_with_relations($user_id = null, $modul = null, $start_date = null, $end_date = null) {
        $this->db->select('la.*, u.nama_lengkap, u.username');
        $this->db->from($this->table . ' la');
        $this->db->join('users u', 'u.id = la.user_id', 'left');
        
        if ($user_id) {
            $this->db->where('la.user_id', $user_id);
        }
        
        if ($modul) {
            $this->db->where('la.modul', $modul);
        }
        
        if ($start_date) {
            $this->db->where('la.created_at >=', $start_date);
        }
        
        if ($end_date) {
            $this->db->where('la.created_at <=', $end_date);
        }
        
        $this->db->order_by('la.created_at', 'DESC');
        return $this->db->get()->result();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'la.created_at';
        }
        $params['build_query'] = function () {
            $this->db->select('la.*, u.nama_lengkap, u.username');
            $this->db->from($this->table . ' la');
            $this->db->join('users u', 'u.id = la.user_id', 'left');
        };
        $params['apply_filters'] = function () use ($params) {
            if (isset($params['user_id'])) {
            $this->db->where('la.user_id', $params['user_id']);
            }
            if (isset($params['modul'])) {
            $this->db->where('la.modul', $params['modul']);
            }
        };
        return $this->datatable_execute($params);
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_kerja_model extends MY_Model {
    
    protected $table = 'jadwal_kerja';
    protected $primary_key = 'id';
    protected $fillable = array('user_id', 'hari', 'shift_id', 'is_active');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get schedule by user
     */
    public function get_by_user($user_id) {
        $this->db->select('jk.*, ms.nama as shift_nama, ms.jam_mulai, ms.jam_selesai');
        $this->db->from($this->table . ' jk');
        $this->db->join('master_shift ms', 'ms.id = jk.shift_id', 'left');
        $this->db->where('jk.user_id', $user_id);
        $this->db->where('jk.is_active', 1);
        return $this->db->get()->result();
    }
    
    /**
     * Save schedule for user
     */
    public function save_schedule($user_id, $schedule) {
        $this->db->trans_start();
        
        // Delete existing schedule
        $this->db->where('user_id', $user_id);
        $this->db->delete($this->table);
        
        // Insert new schedule
        if (!empty($schedule)) {
            foreach ($schedule as $hari => $data) {
                $insert_data = array(
                    'user_id' => $user_id,
                    'hari' => $hari,
                    'shift_id' => isset($data['shift_id']) ? $data['shift_id'] : null,
                    'is_active' => isset($data['is_active']) ? 1 : 0
                );
                $this->db->insert($this->table, $insert_data);
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peran_hak_akses_model extends MY_Model {
    
    protected $table = 'peran_hak_akses';
    protected $primary_key = 'id';
    protected $fillable = array('peran_id', 'modul_id', 'can_view', 'can_create', 'can_update', 'can_delete');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get permissions by role
     */
    public function get_by_peran($peran_id) {
        $this->db->select('pha.*, m.nama as modul_nama, m.kode as modul_kode');
        $this->db->from($this->table . ' pha');
        $this->db->join('modul m', 'm.id = pha.modul_id');
        $this->db->where('pha.peran_id', $peran_id);
        return $this->db->get()->result();
    }
    
    /**
     * Normalisasi nilai centang dari form/JS.
     * JS mengirim 0 untuk checkbox yang tidak dicentang, jadi isset() tidak boleh dipakai.
     */
    private function flag($perms, $key) {
        return !empty($perms[$key]) ? 1 : 0;
    }

    /**
     * Save permissions for a role
     */
    public function save_permissions($peran_id, $permissions) {
        $this->db->trans_start();
        
        // Delete existing permissions
        $this->db->where('peran_id', $peran_id);
        $this->db->delete($this->table);
        
        // Insert new permissions
        if (!empty($permissions)) {
            foreach ($permissions as $modul_id => $perms) {
                $data = array(
                    'peran_id' => $peran_id,
                    'modul_id' => $modul_id,
                    'can_view' => $this->flag($perms, 'view'),
                    'can_create' => $this->flag($perms, 'create'),
                    'can_update' => $this->flag($perms, 'update'),
                    'can_delete' => $this->flag($perms, 'delete')
                );
                $this->db->insert($this->table, $data);
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}


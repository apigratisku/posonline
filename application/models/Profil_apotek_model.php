<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Profil_apotek_model - Backward compatibility alias untuk Profil_toko_model
 * Gunakan Profil_toko_model untuk kode baru.
 */
class Profil_apotek_model extends MY_Model {

    protected $table = 'profil_toko';
    protected $primary_key = 'id';
    protected $fillable = array('nama', 'alamat', 'telepon', 'email', 'npwp', 'siup', 'logo');
    protected $timestamps = true;

    /**
     * Ambil profil singleton dengan fallback default
     */
    public function get_singleton() {
        $data = $this->get_by_id(1);
        if (!$data) {
            return (object) array(
                'id' => null,
                'nama' => 'Toko POS',
                'alamat' => '',
                'telepon' => '',
                'email' => '',
                'npwp' => '',
                'siup' => '',
                'logo' => ''
            );
        }
        if (empty($data->nama)) {
            $data->nama = 'Toko POS';
        }
        return $data;
    }

    /**
     * Simpan profil singleton (selalu id=1)
     */
    public function save_singleton($id, $data) {
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }

        $existing = $this->get_by_id($id);
        if ($existing) {
            return $this->update($id, $data);
        }

        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $CI =& get_instance();
            $CI->load->library('auth_lib', NULL, 'auth');
            if ($CI->auth->is_logged_in()) {
                $data['created_by'] = $CI->auth->get_user_id();
            }
        }

        $data[$this->primary_key] = $id;
        if (!$this->db->insert($this->table, $data)) {
            $this->log_db_error('save_singleton insert');
            return false;
        }

        return $this->get_by_id($id) ? $id : false;
    }
}

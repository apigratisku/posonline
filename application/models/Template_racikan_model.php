<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template_racikan_model extends MY_Model {
    
    protected $table = 'template_racikan';
    protected $primary_key = 'id';
    protected $fillable = array('kode', 'nama', 'deskripsi', 'is_active');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('template_racikan_detail_model');
    }
    
    public function kode_exists($kode, $exclude_id = null) {
        $this->db->where('kode', $kode);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() > 0;
    }
    
    /**
     * Get with details
     */
    public function get_by_id_with_details($id) {
        $template = $this->get_by_id($id);
        if ($template) {
            $template->detail = $this->template_racikan_detail_model->get_by_template($id);
        }
        return $template;
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template_racikan_detail_model extends MY_Model {
    
    protected $table = 'template_racikan_detail';
    protected $primary_key = 'id';
    protected $fillable = array('template_id', 'produk_id', 'qty', 'keterangan');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get by template ID
     */
    public function get_by_template($template_id) {
        $this->db->select('trd.*, mp.kode as produk_kode, mp.nama as produk_nama');
        $this->db->from($this->table . ' trd');
        $this->db->join('master_produk mp', 'mp.id = trd.produk_id');
        $this->db->where('trd.template_id', $template_id);
        return $this->db->get()->result();
    }
    
    /**
     * Save details
     */
    public function save_details($template_id, $details) {
        $this->db->trans_start();
        
        // Delete existing details
        $this->db->where('template_id', $template_id);
        $this->db->delete($this->table);
        
        // Insert new details
        if (!empty($details)) {
            foreach ($details as $detail) {
                $data = array(
                    'template_id' => $template_id,
                    'produk_id' => $detail['produk_id'],
                    'qty' => $detail['qty'],
                    'keterangan' => isset($detail['keterangan']) ? $detail['keterangan'] : ''
                );
                $this->db->insert($this->table, $data);
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}


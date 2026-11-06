<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penerimaan_resep_detail_model extends MY_Model {
    
    protected $table = 'penerimaan_resep_detail';
    protected $primary_key = 'id';
    protected $fillable = array('resep_id', 'produk_id', 'qty', 'aturan_pakai', 'keterangan');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Get by resep ID
     */
    public function get_by_resep($resep_id) {
        $this->db->select('prd.*, mp.kode as produk_kode, mp.nama as produk_nama, mp.satuan_id');
        $this->db->from($this->table . ' prd');
        $this->db->join('master_produk mp', 'mp.id = prd.produk_id');
        $this->db->where('prd.resep_id', $resep_id);
        return $this->db->get()->result();
    }
    
    /**
     * Save details
     */
    public function save_details($resep_id, $details) {
        $this->db->trans_start();
        
        // Delete existing details
        $this->db->where('resep_id', $resep_id);
        $this->db->delete($this->table);
        
        // Insert new details
        if (!empty($details)) {
            foreach ($details as $detail) {
                $data = array(
                    'resep_id' => $resep_id,
                    'produk_id' => $detail['produk_id'],
                    'qty' => $detail['qty'],
                    'aturan_pakai' => isset($detail['aturan_pakai']) ? $detail['aturan_pakai'] : '',
                    'keterangan' => isset($detail['keterangan']) ? $detail['keterangan'] : ''
                );
                $this->db->insert($this->table, $data);
            }
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penerimaan_resep_model extends MY_Model {
    
    protected $table = 'penerimaan_resep';
    protected $primary_key = 'id';
    protected $fillable = array('no_resep', 'kunjungan_id', 'dokter_id', 'tanggal', 'status', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('penerimaan_resep_detail_model');
    }
    
    /**
     * Generate nomor resep
     */
    public function generate_no_resep() {
        $date = date('Ymd');
        $this->db->select_max('id');
        $row = $this->db->get($this->table)->row();
        $max_id = $row ? $row->id : 0;
        $next_id = $max_id + 1;
        return 'RESEP-' . $date . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('pr.*, kp.no_kunjungan, pl.nama as pelanggan_nama, d.nama as dokter_nama');
        $this->db->from($this->table . ' pr');
        $this->db->join('kunjungan_pasien kp', 'kp.id = pr.kunjungan_id');
        $this->db->join('pelanggan pl', 'pl.id = kp.pelanggan_id');
        $this->db->join('dokter d', 'd.id = pr.dokter_id');
        $this->db->where('pr.id', $id);
        $resep = $this->db->get()->row();
        
        if ($resep) {
            $resep->detail = $this->penerimaan_resep_detail_model->get_by_resep($id);
        }
        
        return $resep;
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'pr.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('pr.*, kp.no_kunjungan, pl.nama as pelanggan_nama, d.nama as dokter_nama');
            $this->db->from($this->table . ' pr');
            $this->db->join('kunjungan_pasien kp', 'kp.id = pr.kunjungan_id');
            $this->db->join('pelanggan pl', 'pl.id = kp.pelanggan_id');
            $this->db->join('dokter d', 'd.id = pr.dokter_id');
        };
        $params['apply_filters'] = function () use ($params) {
            if (isset($params['status'])) {
            $this->db->where('pr.status', $params['status']);
            }
        };
        return $this->datatable_execute($params);
    }
}


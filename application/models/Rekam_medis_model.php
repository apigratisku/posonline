<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekam_medis_model extends MY_Model {
    
    protected $table = 'rekam_medis';
    protected $primary_key = 'id';
    protected $fillable = array('no_rekam_medis', 'kunjungan_id', 'pelanggan_id', 'tanggal', 'diagnosa', 'tindakan', 'resep', 'keterangan');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Generate nomor rekam medis
     */
    public function generate_no_rekam_medis() {
        $date = date('Ymd');
        $this->db->select_max('id');
        $row = $this->db->get($this->table)->row();
        $max_id = $row ? $row->id : 0;
        $next_id = $max_id + 1;
        return 'RM-' . $date . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('rm.*, pl.nama as pelanggan_nama, kp.no_kunjungan');
        $this->db->from($this->table . ' rm');
        $this->db->join('pelanggan pl', 'pl.id = rm.pelanggan_id');
        $this->db->join('kunjungan_pasien kp', 'kp.id = rm.kunjungan_id');
        $this->db->where('rm.id', $id);
        return $this->db->get()->row();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'rm.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('rm.*, pl.nama as pelanggan_nama, kp.no_kunjungan');
            $this->db->from($this->table . ' rm');
            $this->db->join('pelanggan pl', 'pl.id = rm.pelanggan_id');
            $this->db->join('kunjungan_pasien kp', 'kp.id = rm.kunjungan_id');
        };
        $params['apply_filters'] = function () use ($params) {
            if (isset($params['pelanggan_id'])) {
            $this->db->where('rm.pelanggan_id', $params['pelanggan_id']);
            }
        };
        return $this->datatable_execute($params);
    }
}


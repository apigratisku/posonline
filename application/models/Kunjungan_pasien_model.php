<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kunjungan_pasien_model extends MY_Model {
    
    protected $table = 'kunjungan_pasien';
    protected $primary_key = 'id';
    protected $fillable = array('no_kunjungan', 'pelanggan_id', 'dokter_id', 'jenis_pelayanan_id', 'jenis_antrian_id', 'tanggal', 'keluhan', 'status');
    protected $timestamps = true;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Generate nomor kunjungan
     */
    public function generate_no_kunjungan() {
        $date = date('Ymd');
        $this->db->select_max('id');
        $row = $this->db->get($this->table)->row();
        $max_id = $row ? $row->id : 0;
        $next_id = $max_id + 1;
        return 'KUNJ-' . $date . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get with relations
     */
    public function get_by_id_with_relations($id) {
        $this->db->select('kp.*, pl.nama as pelanggan_nama, pl.telepon as pelanggan_telepon, d.nama as dokter_nama, jp.nama as jenis_pelayanan_nama, ja.nama as jenis_antrian_nama');
        $this->db->from($this->table . ' kp');
        $this->db->join('pelanggan pl', 'pl.id = kp.pelanggan_id');
        $this->db->join('dokter d', 'd.id = kp.dokter_id', 'left');
        $this->db->join('jenis_pelayanan jp', 'jp.id = kp.jenis_pelayanan_id', 'left');
        $this->db->join('jenis_antrian ja', 'ja.id = kp.jenis_antrian_id', 'left');
        $this->db->where('kp.id', $id);
        return $this->db->get()->row();
    }
    
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = 'kp.tanggal';
        }
        $params['build_query'] = function () {
            $this->db->select('kp.*, pl.nama as pelanggan_nama, d.nama as dokter_nama');
            $this->db->from($this->table . ' kp');
            $this->db->join('pelanggan pl', 'pl.id = kp.pelanggan_id');
            $this->db->join('dokter d', 'd.id = kp.dokter_id', 'left');
        };
        return $this->datatable_execute($params);
    }
}


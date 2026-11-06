<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konfigurasi_model extends MY_Model {

    protected $table = 'konfigurasi';
    protected $primary_key = 'id';
    protected $fillable = array('key', 'value');
    protected $timestamps = true;

    public function get_all($where = array(), $order_by = null, $limit = null, $offset = null) {
        if (!$order_by) {
            $order_by = '`key` ASC';
        }
        return parent::get_all($where, $order_by, $limit, $offset);
    }

    public function get_by_key($key) {
        return $this->db->where('`key`', $key)->get($this->table)->row();
    }
}

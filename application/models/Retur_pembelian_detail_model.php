<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retur_pembelian_detail_model extends MY_Model {
    
    protected $table = 'retur_pembelian_detail';
    protected $primary_key = 'id';
    protected $fillable = array('retur_id', 'produk_id', 'qty', 'harga', 'subtotal');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function insert($data) {
        $qty = isset($data['qty']) ? $data['qty'] : 0;
        $harga = isset($data['harga']) ? $data['harga'] : 0;
        $data['subtotal'] = $qty * $harga;
        return parent::insert($data);
    }
}

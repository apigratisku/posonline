<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rencana_pembelian_detail_model extends MY_Model {
    
    protected $table = 'rencana_pembelian_detail';
    protected $primary_key = 'id';
    protected $fillable = array('rencana_id', 'produk_id', 'qty', 'harga', 'subtotal');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function insert($data) {
        $data['subtotal'] = (isset($data['qty']) ? $data['qty'] : 0) * (isset($data['harga']) ? $data['harga'] : 0);
        return parent::insert($data);
    }
}


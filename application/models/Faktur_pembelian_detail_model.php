<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faktur_pembelian_detail_model extends MY_Model {
    
    protected $table = 'faktur_pembelian_detail';
    protected $primary_key = 'id';
    protected $fillable = array('faktur_id', 'produk_id', 'qty', 'harga', 'diskon', 'subtotal');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function insert($data) {
        $qty = isset($data['qty']) ? $data['qty'] : 0;
        $harga = isset($data['harga']) ? $data['harga'] : 0;
        $diskon = isset($data['diskon']) ? $data['diskon'] : 0;
        $data['subtotal'] = ($qty * $harga) - $diskon;
        return parent::insert($data);
    }
}


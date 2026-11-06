<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pesanan_penjualan_detail_model extends MY_Model {
    
    protected $table = 'pesanan_penjualan_detail';
    protected $primary_key = 'id';
    protected $fillable = array('pesanan_id', 'produk_id', 'qty', 'harga', 'subtotal');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function insert($data) {
        $data['subtotal'] = (isset($data['qty']) ? $data['qty'] : 0) * (isset($data['harga']) ? $data['harga'] : 0);
        return parent::insert($data);
    }
    
    public function update($id, $data) {
        if (isset($data['qty']) || isset($data['harga'])) {
            $current = $this->get_by_id($id);
            $qty = isset($data['qty']) ? $data['qty'] : $current->qty;
            $harga = isset($data['harga']) ? $data['harga'] : $current->harga;
            $data['subtotal'] = $qty * $harga;
        }
        return parent::update($id, $data);
    }
}


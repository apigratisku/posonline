<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pesanan_pembelian_detail_model extends MY_Model {
    
    protected $table = 'pesanan_pembelian_detail';
    protected $primary_key = 'id';
    protected $fillable = array('po_id', 'produk_id', 'qty', 'harga', 'diskon', 'subtotal');
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
    
    public function update($id, $data) {
        if (isset($data['qty']) || isset($data['harga']) || isset($data['diskon'])) {
            $current = $this->get_by_id($id);
            $qty = isset($data['qty']) ? $data['qty'] : $current->qty;
            $harga = isset($data['harga']) ? $data['harga'] : $current->harga;
            $diskon = isset($data['diskon']) ? $data['diskon'] : $current->diskon;
            $data['subtotal'] = ($qty * $harga) - $diskon;
        }
        return parent::update($id, $data);
    }
}


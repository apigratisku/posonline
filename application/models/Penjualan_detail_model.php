<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_detail_model extends MY_Model {
    
    protected $table = 'penjualan_detail';
    protected $primary_key = 'id';
    protected $fillable = array('penjualan_id', 'produk_id', 'qty', 'harga', 'diskon', 'subtotal');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Insert detail with auto calculate subtotal
     */
    public function insert($data) {
        // Validate required fields
        if (!isset($data['penjualan_id']) || empty($data['penjualan_id'])) {
            log_message('error', 'Penjualan_detail_model::insert - penjualan_id is required');
            return false;
        }
        
        if (!isset($data['produk_id']) || empty($data['produk_id'])) {
            log_message('error', 'Penjualan_detail_model::insert - produk_id is required');
            return false;
        }
        
        // Validate and set default values
        $qty = isset($data['qty']) ? floatval($data['qty']) : 0;
        $harga = isset($data['harga']) ? floatval($data['harga']) : 0;
        $diskon = isset($data['diskon']) ? floatval($data['diskon']) : 0;
        
        // Validate values
        if ($qty <= 0) {
            log_message('error', 'Penjualan_detail_model::insert - qty must be greater than 0');
            return false;
        }
        
        if ($harga < 0) {
            log_message('error', 'Penjualan_detail_model::insert - harga cannot be negative');
            return false;
        }
        
        if ($diskon < 0) {
            log_message('error', 'Penjualan_detail_model::insert - diskon cannot be negative');
            return false;
        }
        
        // Calculate subtotal: (qty * harga) - diskon
        $data['qty'] = $qty;
        $data['harga'] = $harga;
        $data['diskon'] = $diskon;
        $data['subtotal'] = ($qty * $harga) - $diskon;
        
        // Ensure subtotal is not negative
        if ($data['subtotal'] < 0) {
            $data['subtotal'] = 0;
        }
        
        return parent::insert($data);
    }
    
    /**
     * Update detail with auto calculate subtotal
     */
    public function update($id, $data) {
        // Validate ID exists
        $current = $this->get_by_id($id);
        if (!$current) {
            log_message('error', 'Penjualan_detail_model::update - Record not found: ' . $id);
            return false;
        }
        
        // Get current values or use new values
        $qty = isset($data['qty']) ? floatval($data['qty']) : floatval($current->qty);
        $harga = isset($data['harga']) ? floatval($data['harga']) : floatval($current->harga);
        $diskon = isset($data['diskon']) ? floatval($data['diskon']) : floatval($current->diskon);
        
        // Validate values
        if ($qty <= 0) {
            log_message('error', 'Penjualan_detail_model::update - qty must be greater than 0');
            return false;
        }
        
        if ($harga < 0) {
            log_message('error', 'Penjualan_detail_model::update - harga cannot be negative');
            return false;
        }
        
        if ($diskon < 0) {
            log_message('error', 'Penjualan_detail_model::update - diskon cannot be negative');
            return false;
        }
        
        // Calculate subtotal: (qty * harga) - diskon
        $data['qty'] = $qty;
        $data['harga'] = $harga;
        $data['diskon'] = $diskon;
        $data['subtotal'] = ($qty * $harga) - $diskon;
        
        // Ensure subtotal is not negative
        if ($data['subtotal'] < 0) {
            $data['subtotal'] = 0;
        }
        
        return parent::update($id, $data);
    }
}


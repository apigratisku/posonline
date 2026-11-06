<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perpindahan_stok_detail_model extends MY_Model {
    
    protected $table = 'perpindahan_stok_detail';
    protected $primary_key = 'id';
    protected $fillable = array('mutasi_id', 'produk_id', 'qty', 'keterangan');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
}


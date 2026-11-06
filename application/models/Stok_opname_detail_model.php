<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stok_opname_detail_model extends MY_Model {
    
    protected $table = 'stok_opname_detail';
    protected $primary_key = 'id';
    protected $fillable = array('opname_id', 'produk_id', 'stok_sistem', 'stok_fisik', 'selisih', 'keterangan');
    protected $timestamps = false;
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Insert detail with auto calculate selisih
     */
    public function insert($data) {
        // Calculate selisih
        $data['selisih'] = (isset($data['stok_fisik']) ? $data['stok_fisik'] : 0) - (isset($data['stok_sistem']) ? $data['stok_sistem'] : 0);
        
        return parent::insert($data);
    }
    
    /**
     * Update detail with auto calculate selisih
     */
    public function update($id, $data) {
        // Calculate selisih if stok_fisik or stok_sistem changed
        if (isset($data['stok_fisik']) || isset($data['stok_sistem'])) {
            $current = $this->get_by_id($id);
            $stok_fisik = isset($data['stok_fisik']) ? $data['stok_fisik'] : $current->stok_fisik;
            $stok_sistem = isset($data['stok_sistem']) ? $data['stok_sistem'] : $current->stok_sistem;
            $data['selisih'] = $stok_fisik - $stok_sistem;
        }
        
        return parent::update($id, $data);
    }
}


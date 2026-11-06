<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReturPenjualan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('penjualan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Retur Penjualan',
            'breadcrumb' => array(
                array('label' => 'Penjualan', 'url' => '#'),
                array('label' => 'Retur Penjualan')
            )
        );
        
        $this->render('penjualan/retur_penjualan/index', $data);
    }
}


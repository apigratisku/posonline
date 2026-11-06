<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FakturPenjualan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('penjualan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Faktur Penjualan',
            'breadcrumb' => array(
                array('label' => 'Penjualan', 'url' => '#'),
                array('label' => 'Faktur Penjualan')
            )
        );
        
        $this->render('penjualan/faktur_penjualan/index', $data);
    }
}


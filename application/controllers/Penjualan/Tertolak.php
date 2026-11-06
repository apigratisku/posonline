<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tertolak extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('penjualan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Penjualan Tertolak',
            'breadcrumb' => array(
                array('label' => 'Penjualan', 'url' => '#'),
                array('label' => 'Penjualan Tertolak')
            )
        );
        
        $this->render('penjualan/tertolak/index', $data);
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('pengaturan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Pengaturan',
            'breadcrumb' => array(
                array('label' => 'Pengaturan')
            )
        );
        
        $this->render('pengaturan/index', $data);
    }
}


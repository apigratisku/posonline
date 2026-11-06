<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manajemen extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('manajemen', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Manajemen',
            'breadcrumb' => array(
                array('label' => 'Manajemen')
            )
        );
        
        $this->render('manajemen/index', $data);
    }
}


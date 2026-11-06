<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gaji extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('gaji', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Gaji',
            'breadcrumb' => array(
                array('label' => 'Gaji')
            )
        );
        
        $this->render('gaji/index', $data);
    }
}


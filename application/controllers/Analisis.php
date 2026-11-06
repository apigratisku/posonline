<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analisis extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('analisis', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Analisis',
            'breadcrumb' => array(
                array('label' => 'Analisis')
            )
        );
        
        $this->render('analisis/index', $data);
    }
}


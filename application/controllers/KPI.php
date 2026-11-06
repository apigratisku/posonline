<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class KPI extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_permission('kpi', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'KPI',
            'breadcrumb' => array(
                array('label' => 'KPI')
            )
        );
        
        $this->render('kpi/index', $data);
    }
}


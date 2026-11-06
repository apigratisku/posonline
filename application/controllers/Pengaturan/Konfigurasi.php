<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konfigurasi extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('konfigurasi_model');
        $this->check_permission('pengaturan', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Konfigurasi Sistem',
            'breadcrumb' => array(
                array('label' => 'Pengaturan', 'url' => '#'),
                array('label' => 'Konfigurasi')
            ),
            'configs' => $this->konfigurasi_model->get_all()
        );
        $this->render('pengaturan/konfigurasi/index', $data);
    }
    
    public function update() {
        $this->check_permission('pengaturan', 'update');
        $configs = $this->input->post('config');
        
        if ($configs && is_array($configs)) {
            foreach ($configs as $key => $value) {
                $existing = $this->konfigurasi_model->get_by_key($key);
                if ($existing) {
                    $this->konfigurasi_model->update($existing->id, array('value' => $value));
                } else {
                    $this->konfigurasi_model->insert(array('key' => $key, 'value' => $value));
                }
            }
            $this->set_flash('success', 'Konfigurasi berhasil diupdate');
        } else {
            $this->set_flash('error', 'Gagal mengupdate konfigurasi');
        }
        
        redirect('pengaturan/konfigurasi');
    }
}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProfilApotek extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('profil_apotek_model');
        $this->load->model('profil_toko_model');
        $this->check_permission('pengaturan', 'view');
    }
    
    private function empty_profil() {
        return (object) array(
            'id' => null,
            'nama' => '',
            'alamat' => '',
            'telepon' => '',
            'email' => '',
            'npwp' => '',
            'siup' => '',
            'logo' => ''
        );
    }
    
    private function view_data($data) {
        return array(
            'title' => 'Profil Toko',
            'breadcrumb' => array(
                array('label' => 'Pengaturan', 'url' => base_url('pengaturan')),
                array('label' => 'Profil Toko')
            ),
            'data' => $data
        );
    }
    
    public function index() {
        $data = $this->profil_apotek_model->get_by_id(1);
        if (!$data) {
            $data = $this->empty_profil();
        }
        $this->render('pengaturan/profil_apotek/index', $this->view_data($data));
    }
    
    public function update() {
        $this->check_permission('pengaturan', 'update');
        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('telepon', 'Telepon', 'trim|max_length[30]');
        $this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[500]');
        
        $existing = $this->profil_apotek_model->get_by_id(1);
        if (!$existing) {
            $existing = $this->empty_profil();
        }
        
        if ($this->form_validation->run() == false) {
            $this->render('pengaturan/profil_apotek/index', $this->view_data($existing));
            return;
        }
        
        $update_data = array(
            'nama' => $this->input->post('nama'),
            'alamat' => trim($this->input->post('alamat')),
            'telepon' => trim($this->input->post('telepon')),
            'email' => $this->input->post('email'),
            'npwp' => $this->input->post('npwp'),
            'siup' => $this->input->post('siup')
        );
        
        if (!empty($_FILES['logo']['name'])) {
            $upload_path = FCPATH . 'assets/uploads/logo/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }
            
            $config['upload_path'] = './assets/uploads/logo/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = true;
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('logo')) {
                $upload_data = $this->upload->data();
                $update_data['logo'] = $upload_data['file_name'];
                
                if (!empty($existing->logo)) {
                    $old_file = $upload_path . $existing->logo;
                    if (is_file($old_file)) {
                        @unlink($old_file);
                    }
                }
            } else {
                $this->set_flash('error', 'Upload logo gagal: ' . strip_tags($this->upload->display_errors()));
                redirect('pengaturan/profil');
                return;
            }
        }
        
        if ($existing->id) {
            if ($this->profil_apotek_model->update(1, $update_data)) {
                $this->set_flash('success', 'Profil toko berhasil diupdate');
            } else {
                $this->set_flash('error', 'Gagal mengupdate profil toko');
            }
        } else {
            if ($this->profil_apotek_model->save_singleton(1, $update_data)) {
                $this->set_flash('success', 'Profil toko berhasil disimpan');
            } else {
                $this->set_flash('error', 'Gagal menyimpan profil toko');
            }
        }
        
        redirect('pengaturan/profil');
    }
}

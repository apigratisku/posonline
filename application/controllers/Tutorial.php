<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tutorial / Cara Penggunaan
 * Panduan singkat penggunaan aplikasi + tombol export PDF
 */
class Tutorial extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('tutorial');
    }

    public function index() {
        $data = array(
            'title' => 'Cara Penggunaan',
            'breadcrumb' => array(
                array('label' => 'Dashboard', 'url' => 'dashboard'),
                array('label' => 'Cara Penggunaan')
            ),
            'steps' => tutorial_steps()
        );

        $this->render('tutorial/index', $data);
    }

    /**
     * Export panduan ke PDF.
     * Menggunakan view khusus (tanpa layout aplikasi) lalu print via browser.
     */
    public function export_pdf() {
        $this->load->helper('tutorial');

        $html = $this->load->view('tutorial/pdf', array(
            'steps' => tutorial_steps(),
            'profil_toko' => $this->data['profil_toko'],
            'printed_by' => $this->user,
        ), true);

        $this->output
            ->set_content_type('text/html', 'utf-8')
            ->set_output($html);
    }
}

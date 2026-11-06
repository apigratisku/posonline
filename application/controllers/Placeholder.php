<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Placeholder extends MY_Controller {

    private $pages = array(
        'pelayanan_rekam-medis' => array('title' => 'Rekam Medis', 'group' => 'Pelayanan', 'permission' => 'pelayanan'),
        'pelayanan_pemeriksaan-awal' => array('title' => 'Pemeriksaan Awal', 'group' => 'Pelayanan', 'permission' => 'pelayanan'),
        'pelayanan_pemeriksaan-dokter' => array('title' => 'Pemeriksaan Dokter', 'group' => 'Pelayanan', 'permission' => 'pelayanan'),
        'pelayanan_penebusan' => array('title' => 'Penebusan Resep', 'group' => 'Pelayanan', 'permission' => 'pelayanan'),
        'pelayanan_racikan' => array('title' => 'Template Racikan', 'group' => 'Pelayanan', 'permission' => 'pelayanan'),
        'pelayanan_lab' => array('title' => 'Pemeriksaan Lab', 'group' => 'Pelayanan', 'permission' => 'pelayanan'),
        'pembelian_rencana' => array('title' => 'Rencana Pembelian', 'group' => 'Pembelian', 'permission' => 'pembelian'),
        'pembelian_retur' => array('title' => 'Retur Pembelian', 'group' => 'Pembelian', 'permission' => 'pembelian'),
        'pembelian_faktur-ai' => array('title' => 'Faktur AI', 'group' => 'Pembelian', 'permission' => 'pembelian'),
        'kpi_parameter' => array('title' => 'Parameter KPI', 'group' => 'KPI', 'permission' => 'kpi'),
        'kpi_pencapaian' => array('title' => 'Pencapaian KPI', 'group' => 'KPI', 'permission' => 'kpi'),
        'kpi_laporan' => array('title' => 'Laporan KPI', 'group' => 'KPI', 'permission' => 'kpi'),
        'gaji_pokok' => array('title' => 'Gaji Pokok', 'group' => 'Gaji', 'permission' => 'gaji'),
        'gaji_tunjangan' => array('title' => 'Tunjangan', 'group' => 'Gaji', 'permission' => 'gaji'),
        'gaji_potongan' => array('title' => 'Potongan Gaji', 'group' => 'Gaji', 'permission' => 'gaji'),
        'gaji_pembayaran' => array('title' => 'Pembayaran Gaji', 'group' => 'Gaji', 'permission' => 'gaji'),
        'pengaturan_integrasi' => array('title' => 'Integrasi Platform', 'group' => 'Pengaturan', 'permission' => 'pengaturan'),
        'pengaturan_online' => array('title' => 'Apotek Online', 'group' => 'Pengaturan', 'permission' => 'pengaturan'),
        'pengaturan_website' => array('title' => 'Website', 'group' => 'Pengaturan', 'permission' => 'pengaturan'),
        'pengaturan_katalog' => array('title' => 'Katalog Online', 'group' => 'Pengaturan', 'permission' => 'pengaturan'),
        'pengaturan_whatsapp' => array('title' => 'WhatsApp Tools', 'group' => 'Pengaturan', 'permission' => 'pengaturan'),
        'master_pelayanan' => array('title' => 'Jenis Pelayanan', 'group' => 'Master Data', 'permission' => 'master'),
        'master_antrian' => array('title' => 'Jenis Antrian', 'group' => 'Master Data', 'permission' => 'master'),
        'master_produk-lab' => array('title' => 'Produk Lab', 'group' => 'Master Data', 'permission' => 'master'),
        'master_item-pemeriksaan' => array('title' => 'Item Pemeriksaan', 'group' => 'Master Data', 'permission' => 'master'),
    );

    public function page($key = '') {
        if (!isset($this->pages[$key])) {
            show_404();
            return;
        }

        $page = $this->pages[$key];
        $this->check_permission($page['permission'], 'view');

        $data = array(
            'title' => $page['title'],
            'module_title' => $page['title'],
            'module_group' => $page['group'],
            'breadcrumb' => array(
                array('label' => $page['group'], 'url' => '#'),
                array('label' => $page['title'])
            )
        );

        $this->render('common/coming_soon', $data);
    }
}

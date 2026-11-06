<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends MY_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('log_aktivitas_model');
        $this->load->model('user_model');
        $this->load->model('modul_model');
        $this->check_permission('manajemen', 'view');
    }
    
    public function index() {
        $data = array(
            'title' => 'Log Aktivitas',
            'breadcrumb' => array(
                array('label' => 'Manajemen', 'url' => '#'),
                array('label' => 'Log Aktivitas')
            ),
            'users' => $this->user_model->get_all(array('is_active' => 1)),
            'moduls' => $this->modul_model->get_all(array('is_active' => 1))
        );
        
        $this->render('manajemen/log/index', $data);
    }
    
    public function ajax_list() {
        $this->check_permission('manajemen', 'view');
        
        $params = array(
            'columns' => array('la.created_at', 'u.nama_lengkap', 'la.modul', 'la.aksi', 'la.deskripsi'),
            'search_fields' => array('u.nama_lengkap', 'u.username', 'la.modul', 'la.aksi', 'la.deskripsi')
        );
        
        if ($this->input->post('user_id')) {
            $params['user_id'] = $this->input->post('user_id');
        }
        
        if ($this->input->post('modul')) {
            $params['modul'] = $this->input->post('modul');
        }
        
        $result = $this->log_aktivitas_model->datatable($params);
        
        $data = array();
        foreach ($result['data'] as $row) {
            $aksi_badge = '<span class="badge bg-';
            if ($row->aksi == 'create') {
                $aksi_badge .= 'success';
            } elseif ($row->aksi == 'update') {
                $aksi_badge .= 'warning';
            } elseif ($row->aksi == 'delete') {
                $aksi_badge .= 'danger';
            } else {
                $aksi_badge .= 'info';
            }
            $aksi_badge .= '">' . ucfirst($row->aksi) . '</span>';
            
            $data[] = array(
                date('d/m/Y H:i:s', strtotime($row->created_at ?: 'now')),
                $row->nama_lengkap ?: ($row->username ?: 'System'),
                ucfirst($row->modul),
                $aksi_badge,
                $row->deskripsi ?: '-',
                $row->ip_address ?: '-',
                $this->get_action_buttons($row->id)
            );
        }
        
        $result['data'] = $data;
        $this->datatable_json($result);
    }
    
    private function get_action_buttons($id) {
        $buttons = '';
        $buttons .= '<button class="btn btn-sm btn-info" onclick="viewDetail(' . $id . ')"><i class="fas fa-eye"></i></button> ';
        if ($this->auth->has_permission('manajemen', 'delete')) {
            $buttons .= '<button class="btn btn-sm btn-danger" onclick="deleteData(' . $id . ')"><i class="fas fa-trash"></i></button>';
        }
        return $buttons;
    }
    
    public function ajax_get($id) {
        $this->check_permission('manajemen', 'view');
        $data = $this->log_aktivitas_model->get_by_id($id);
        if ($data) {
            $this->ajax_response(true, 'Data berhasil diambil', $data);
        } else {
            $this->ajax_response(false, 'Data tidak ditemukan');
        }
    }
    
    public function ajax_delete($id) {
        $this->check_permission('manajemen', 'delete');
        if ($this->log_aktivitas_model->delete($id)) {
            $this->ajax_response(true, 'Data berhasil dihapus');
        } else {
            $this->ajax_response(false, 'Gagal menghapus data');
        }
    }
    
    public function ajax_export() {
        $this->check_permission('manajemen', 'view');
        
        $params = array();
        if ($this->input->get('user_id')) {
            $params['user_id'] = $this->input->get('user_id');
        }
        if ($this->input->get('modul')) {
            $params['modul'] = $this->input->get('modul');
        }
        if ($this->input->get('start_date')) {
            $params['start_date'] = $this->input->get('start_date');
        }
        if ($this->input->get('end_date')) {
            $params['end_date'] = $this->input->get('end_date');
        }
        
        $logs = $this->log_aktivitas_model->get_with_relations(
            isset($params['user_id']) ? $params['user_id'] : null,
            isset($params['modul']) ? $params['modul'] : null,
            isset($params['start_date']) ? $params['start_date'] : null,
            isset($params['end_date']) ? $params['end_date'] : null
        );
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=log_aktivitas_' . date('Y-m-d') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($output, array('Tanggal/Waktu', 'User', 'Modul', 'Aksi', 'Deskripsi', 'IP Address'));
        
        // Data
        foreach ($logs as $log) {
            fputcsv($output, array(
                date('d/m/Y H:i:s', strtotime($log->created_at)),
                $log->nama_lengkap ?: ($log->username ?: 'System'),
                $log->modul,
                $log->aksi,
                $log->deskripsi,
                $log->ip_address
            ));
        }
        
        fclose($output);
        exit;
    }
}


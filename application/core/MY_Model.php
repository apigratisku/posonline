<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/Datatable_trait.php';

class MY_Model extends CI_Model {
    
    use Datatable_trait;
    
    protected $table = '';
    protected $primary_key = 'id';
    protected $fillable = array();
    protected $timestamps = true;
    protected $soft_deletes = false;
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Log database error with query context
     */
    protected function log_db_error($action) {
        $error = $this->db->error();
        if (!empty($error['code'])) {
            log_message('error', sprintf(
                'MY_Model %s failed on table %s: [%s] %s | Query: %s',
                $action,
                $this->table,
                $error['code'],
                $error['message'],
                $this->db->last_query()
            ));
        }
    }
    
    /**
     * Get all records
     */
    public function get_all($where = array(), $order_by = null, $limit = null, $offset = null) {
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get($this->table)->result();
    }
    
    /**
     * Get single record by ID
     */
    public function get_by_id($id) {
        return $this->db->where($this->primary_key, $id)->get($this->table)->row();
    }
    
    /**
     * Get single record by condition
     */
    public function get_one($where = array()) {
        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->get($this->table)->row();
    }
    
    /**
     * Count records
     */
    public function count($where = array()) {
        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->count_all_results($this->table);
    }
    
    /**
     * Insert record
     */
    public function insert($data) {
        // Filter fillable fields
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        // Add timestamps
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $CI =& get_instance();
            $CI->load->library('auth_lib', NULL, 'auth');
            if ($CI->auth->is_logged_in()) {
                $data['created_by'] = $CI->auth->get_user_id();
            }
        }
        
        if (!$this->db->insert($this->table, $data)) {
            $this->log_db_error('insert');
            return false;
        }

        $insert_id = $this->db->insert_id();
        if (!$insert_id) {
            $this->log_db_error('insert (no insert_id)');
            return false;
        }

        if (!$this->get_by_id($insert_id)) {
            $this->log_db_error('insert (row not found after insert)');
            return false;
        }

        return $insert_id;
    }
    
    /**
     * Update record
     */
    public function update($id, $data) {
        // Filter fillable fields
        if (!empty($this->fillable)) {
            $data = array_intersect_key($data, array_flip($this->fillable));
        }
        
        // Add timestamps
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $CI =& get_instance();
            $CI->load->library('auth_lib', NULL, 'auth');
            if ($CI->auth->is_logged_in()) {
                $data['updated_by'] = $CI->auth->get_user_id();
            }
        }
        
        $result = $this->db->where($this->primary_key, $id)->update($this->table, $data);
        if (!$result) {
            $this->log_db_error('update');
        }
        return $result;
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        if ($this->soft_deletes) {
            $data = array('deleted_at' => date('Y-m-d H:i:s'));
            $CI =& get_instance();
            $CI->load->library('auth_lib', NULL, 'auth');
            if ($CI->auth->is_logged_in()) {
                $data['deleted_by'] = $CI->auth->get_user_id();
            }
            $result = $this->db->where($this->primary_key, $id)->update($this->table, $data);
            if (!$result) {
                $this->log_db_error('soft delete');
            }
            return $result;
        }

        $result = $this->db->where($this->primary_key, $id)->delete($this->table);
        if (!$result) {
            $this->log_db_error('delete');
        }
        return $result;
    }
    
    /**
     * Batch insert
     */
    public function insert_batch($data) {
        $result = $this->db->insert_batch($this->table, $data);
        if ($result === false) {
            $this->log_db_error('insert_batch');
        }
        return $result;
    }
    
    /**
     * Batch update
     */
    public function update_batch($data, $key) {
        return $this->db->update_batch($this->table, $data, $key);
    }
    
    /**
     * DataTables server-side processing
     */
    public function datatable($params = array()) {
        if (!isset($params['default_order'])) {
            $params['default_order'] = $this->primary_key;
        }
        return $this->datatable_execute($params);
    }
}


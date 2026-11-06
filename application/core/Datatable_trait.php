<?php
defined('BASEPATH') OR exit('No direct script access allowed');

trait Datatable_trait {

    protected function datatable_is_ajax_request() {
        return $this->input->is_ajax_request()
            || $this->router->fetch_method() === 'ajax_list'
            || $this->input->post('draw') !== null;
    }

    protected function datatable_empty_response($error = '') {
        $result = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => array(),
        );
        if ($error !== '') {
            $result['error'] = $error;
        }
        return $result;
    }

    protected function datatable_parse_request() {
        $search_post = $this->input->post('search');
        $order_post = $this->input->post('order');

        $order_dir = 'desc';
        if (is_array($order_post) && isset($order_post[0]['dir'])) {
            $dir = strtolower($order_post[0]['dir']);
            if (in_array($dir, array('asc', 'desc'), true)) {
                $order_dir = $dir;
            }
        }

        return array(
            'draw' => intval($this->input->post('draw')),
            'start' => intval($this->input->post('start')),
            'length' => intval($this->input->post('length')),
            'search' => is_array($search_post) && isset($search_post['value']) ? $search_post['value'] : '',
            'order_column' => is_array($order_post) && isset($order_post[0]['column']) ? intval($order_post[0]['column']) : 0,
            'order_dir' => $order_dir,
        );
    }

    protected function datatable_resolve_order($columns, $order_column, $default_field) {
        if (isset($columns[$order_column]) && $columns[$order_column] !== '') {
            return $this->datatable_strip_order_suffix($columns[$order_column]);
        }
        return $this->datatable_strip_order_suffix($default_field);
    }

    protected function datatable_strip_order_suffix($field) {
        $field = trim(preg_replace('/\s+(ASC|DESC)$/i', '', $field));
        if (strpos($field, ',') !== false) {
            $parts = explode(',', $field);
            $field = trim($parts[0]);
        }
        return $field;
    }

    protected function datatable_apply_search($params, $search) {
        if ($search !== '' && !empty($params['search_fields'])) {
            $this->db->group_start();
            foreach ($params['search_fields'] as $field) {
                $this->db->or_like($field, $search);
            }
            $this->db->group_end();
        }
    }

    protected function datatable_count_results($params) {
        if (!empty($params['count_key'])) {
            $this->db->select('COUNT(DISTINCT ' . $params['count_key'] . ') AS datatable_count', false);
            $row = $this->db->get()->row();
            return $row ? (int) $row->datatable_count : 0;
        }
        return (int) $this->db->count_all_results('', true);
    }

    protected function datatable_log_db_error($context) {
        $error = $this->db->error();
        if (!empty($error['code'])) {
            log_message('error', sprintf(
                'Datatable %s failed: [%s] %s | Query: %s',
                $context,
                $error['code'],
                $error['message'],
                $this->db->last_query()
            ));
        }
    }

    protected function datatable_format_db_error($prefix) {
        $error = $this->db->error();
        if (!empty($error['message'])) {
            if (ENVIRONMENT === 'development') {
                return $prefix . ': ' . $error['message'];
            }
            return $prefix . '. Silakan hubungi administrator.';
        }
        return $prefix . '.';
    }

    protected function datatable_infer_count_key($params) {
        if (!empty($params['count_key'])) {
            return $params['count_key'];
        }
        if (empty($params['build_query'])) {
            return null;
        }
        $default = isset($params['default_order']) ? $params['default_order'] : '';
        if (strpos($default, '.') === false) {
            return null;
        }
        $alias = explode('.', $default)[0];
        $pk = (property_exists($this, 'primary_key') && !empty($this->primary_key)) ? $this->primary_key : 'id';
        if (strpos($pk, '.') !== false) {
            return $pk;
        }
        return $alias . '.' . $pk;
    }

    protected function datatable_execute($params = array()) {
        $req = $this->datatable_parse_request();
        $inferred_count_key = $this->datatable_infer_count_key($params);
        if ($inferred_count_key !== null) {
            $params['count_key'] = $inferred_count_key;
        }
        $columns = isset($params['columns']) ? $params['columns'] : array();
        if (isset($params['default_order'])) {
            $default_order = $params['default_order'];
        } elseif (property_exists($this, 'primary_key') && !empty($this->primary_key)) {
            $default_order = $this->primary_key;
        } else {
            $default_order = 'id';
        }
        $order_by = $this->datatable_resolve_order($columns, $req['order_column'], $default_order);

        $build = function () use ($params) {
            if (isset($params['build_query']) && is_callable($params['build_query'])) {
                call_user_func($params['build_query']);
            } elseif (property_exists($this, 'table') && !empty($this->table)) {
                $this->db->from($this->table);
            }
            if (isset($params['where']) && !empty($params['where'])) {
                $this->db->where($params['where']);
            }
            if (isset($params['apply_filters']) && is_callable($params['apply_filters'])) {
                call_user_func($params['apply_filters'], $this->db);
            }
        };

        $build();
        $recordsTotal = $this->datatable_count_results($params);

        $build();
        $this->datatable_apply_search($params, $req['search']);
        $recordsFiltered = $this->datatable_count_results($params);

        $build();
        $this->datatable_apply_search($params, $req['search']);
        $this->db->order_by($order_by, $req['order_dir']);
        if ($req['length'] > 0) {
            $this->db->limit($req['length'], $req['start']);
        }
        $query = $this->db->get();
        $error_message = '';
        if ($query === false) {
            $this->datatable_log_db_error('get rows');
            $records = array();
            $error_message = $this->datatable_format_db_error('Gagal memuat data tabel');
        } else {
            $records = $query->result();
            if ($recordsFiltered > 0 && empty($records)) {
                $db_error = $this->db->error();
                if (!empty($db_error['code'])) {
                    $this->datatable_log_db_error('get rows (empty result)');
                    $error_message = $this->datatable_format_db_error('Data tidak dapat dimuat');
                }
            }
        }

        $result = array(
            'draw' => $req['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $records,
        );
        if ($error_message !== '') {
            $result['error'] = $error_message;
        }
        return $result;
    }
}

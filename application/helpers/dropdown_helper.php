<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('rows_to_dropdown')) {
    /**
     * Convert array of objects/arrays to id => label map for form_dropdown.
     *
     * @param array $rows
     * @param string $value_key
     * @param string $label_key
     * @return array
     */
    function rows_to_dropdown($rows, $value_key = 'id', $label_key = 'nama') {
        $options = array();
        if (empty($rows)) {
            return $options;
        }
        foreach ($rows as $row) {
            if (is_object($row)) {
                $value = isset($row->{$value_key}) ? $row->{$value_key} : null;
                $label = isset($row->{$label_key}) ? $row->{$label_key} : '';
            } else {
                $value = isset($row[$value_key]) ? $row[$value_key] : null;
                $label = isset($row[$label_key]) ? $row[$label_key] : '';
            }
            if ($value !== null) {
                $options[$value] = $label;
            }
        }
        return $options;
    }
}

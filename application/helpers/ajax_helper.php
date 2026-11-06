<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('ajax_response')) {
    /**
     * Send AJAX response
     */
    function ajax_response($status = true, $message = '', $data = null, $errors = null) {
        $CI =& get_instance();
        
        $response = array(
            'status' => $status,
            'message' => $message
        );
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        $CI->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}

if (!function_exists('ajax_success')) {
    /**
     * Send AJAX success response
     */
    function ajax_success($message = 'Berhasil', $data = null) {
        ajax_response(true, $message, $data);
    }
}

if (!function_exists('ajax_error')) {
    /**
     * Send AJAX error response
     */
    function ajax_error($message = 'Terjadi kesalahan', $errors = null) {
        ajax_response(false, $message, null, $errors);
    }
}

if (!function_exists('ajax_validation_error')) {
    /**
     * Send AJAX validation error response
     */
    function ajax_validation_error($errors) {
        ajax_response(false, 'Validasi gagal', null, $errors);
    }
}


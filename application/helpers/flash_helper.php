<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Flash Message Helper
 * 
 * Helper functions for setting and displaying flash messages
 */

if (!function_exists('set_flash_message')) {
    /**
     * Set flash message
     * 
     * @param string $type Type of message (success, error, warning, info)
     * @param string $message Message content
     */
    function set_flash_message($type, $message) {
        $CI =& get_instance();
        $CI->session->set_flashdata('flash_type', $type);
        $CI->session->set_flashdata('flash_message', $message);
    }
}

if (!function_exists('get_flash_message')) {
    /**
     * Get flash message
     * 
     * @return array|null Array with 'type' and 'message' keys, or null if no message
     */
    function get_flash_message() {
        $CI =& get_instance();
        $type = $CI->session->flashdata('flash_type');
        $message = $CI->session->flashdata('flash_message');
        
        if ($type && $message) {
            return array(
                'type' => $type,
                'message' => $message
            );
        }
        
        return null;
    }
}

if (!function_exists('display_flash_message')) {
    /**
     * Display flash message HTML
     * 
     * @return string HTML for flash message or empty string
     */
    function display_flash_message() {
        $flash = get_flash_message();
        
        if (!$flash) {
            return '';
        }
        
        $alert_class = 'alert-' . $flash['type'];
        if ($flash['type'] == 'error') {
            $alert_class = 'alert-danger';
        }
        
        $icon = '';
        switch ($flash['type']) {
            case 'success':
                $icon = '<i class="fas fa-check-circle"></i> ';
                break;
            case 'error':
            case 'danger':
                $icon = '<i class="fas fa-exclamation-circle"></i> ';
                break;
            case 'warning':
                $icon = '<i class="fas fa-exclamation-triangle"></i> ';
                break;
            case 'info':
                $icon = '<i class="fas fa-info-circle"></i> ';
                break;
        }
        
        return '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">' .
               $icon . htmlspecialchars($flash['message']) .
               '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' .
               '</div>';
    }
}


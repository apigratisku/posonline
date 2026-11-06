<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('has_permission')) {
    /**
     * Check if user has permission
     */
    function has_permission($modul, $aksi = 'view') {
        $CI =& get_instance();
        $CI->load->library('auth_lib', NULL, 'auth');
        return $CI->auth->has_permission($modul, $aksi);
    }
}

if (!function_exists('can_view')) {
    /**
     * Check if user can view
     */
    function can_view($modul) {
        return has_permission($modul, 'view');
    }
}

if (!function_exists('can_create')) {
    /**
     * Check if user can create
     */
    function can_create($modul) {
        return has_permission($modul, 'create');
    }
}

if (!function_exists('can_update')) {
    /**
     * Check if user can update
     */
    function can_update($modul) {
        return has_permission($modul, 'update');
    }
}

if (!function_exists('can_delete')) {
    /**
     * Check if user can delete
     */
    function can_delete($modul) {
        return has_permission($modul, 'delete');
    }
}

if (!function_exists('menu_visible')) {
    /**
     * Check if menu modul should be shown in navigation
     */
    function menu_visible($modul) {
        $CI =& get_instance();
        $hidden = $CI->config->item('hidden_modules');
        if (is_array($hidden) && in_array($modul, $hidden, true)) {
            return false;
        }
        return can_view($modul);
    }
}


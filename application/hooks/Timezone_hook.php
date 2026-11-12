<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Timezone Hook
 *
 * Memastikan seluruh aplikasi berjalan pada GMT+8 (Asia/Makassar, WITA),
 * termasuk proses simpan data ke database.
 */
class Timezone_hook
{
    public function set_timezone()
    {
        // Timezone aplikasi (PHP): date(), time(), DateTime, Carbon, dll.
        date_default_timezone_set('Asia/Makassar');

        // Timezone session MySQL: NOW(), CURRENT_TIMESTAMP, DATE_*, TIMESTAMPDIFF
        // Hanya dijalankan jika koneksi database sudah tersedia (mis. request web).
        $CI =& get_instance();
        if (isset($CI->db) && is_object($CI->db) && ! empty($CI->db->conn_id)) {
            @$CI->db->query("SET time_zone = '+08:00'");
        }
    }
}

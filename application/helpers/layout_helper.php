<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('apotik_defer_scripts')) {
    /**
     * Pisahkan blok <script> dari HTML view agar bisa dirender setelah jQuery.
     *
     * @param string $html
     * @return array{content: string, scripts: string}
     */
    function apotik_defer_scripts($html) {
        $scripts = '';

        $content = preg_replace_callback('/<script\b[^>]*>.*?<\/script>/is', function ($matches) use (&$scripts) {
            $scripts .= $matches[0];
            return '';
        }, $html);

        return array(
            'content' => $content,
            'scripts' => $scripts,
        );
    }
}

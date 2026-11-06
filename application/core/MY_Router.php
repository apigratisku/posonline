<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Router extends CI_Router {

    /**
     * Validate request with case-insensitive controller directory matching.
     * Fixes routing on Linux where folder names use PascalCase (e.g. Dashboard)
     * but routes use lowercase (e.g. dashboard/dashboard/index).
     */
    protected function _validate_request($segments)
    {
        $c = count($segments);
        $directory_override = isset($this->directory);

        while ($c-- > 0)
        {
            $segment = $this->translate_uri_dashes === TRUE
                ? str_replace('-', '_', $segments[0])
                : $segments[0];

            // If there's a subdirectory and a controller inside it, prioritize the subdirectory controller
            // to avoid conflicts when a directory and a controller file have the same name (e.g. Pengaturan/ and Pengaturan.php)
            $is_sub_controller = FALSE;
            if ($directory_override === FALSE && count($segments) >= 2)
            {
                $next_segment = $this->translate_uri_dashes === TRUE
                    ? str_replace('-', '_', $segments[1])
                    : $segments[1];
                
                $matched_dir = $this->_match_controller_directory($segment);
                if ($matched_dir !== NULL)
                {
                    $sub_test = $matched_dir . '/' . ucfirst($next_segment);
                    if (file_exists(APPPATH.'controllers/'.$sub_test.'.php'))
                    {
                        $is_sub_controller = TRUE;
                    }
                }
            }

            $test = $this->directory.ucfirst($segment);

            if ($is_sub_controller === FALSE && file_exists(APPPATH.'controllers/'.$test.'.php'))
            {
                return $segments;
            }

            if ($directory_override === FALSE)
            {
                $matched_dir = $this->_match_controller_directory($segment);
                if ($matched_dir !== NULL)
                {
                    $this->set_directory($matched_dir, TRUE);
                    array_shift($segments);
                    continue;
                }
            }

            return $segments;
        }

        return $segments;
    }

    /**
     * Find controller subdirectory by exact or case-insensitive name.
     */
    protected function _match_controller_directory($segment)
    {
        $base = APPPATH.'controllers/'.$this->directory;

        if (is_dir($base.$segment))
        {
            return $segment;
        }

        if ( ! is_dir($base))
        {
            return NULL;
        }

        foreach (scandir($base) as $item)
        {
            if ($item[0] === '.' || ! is_dir($base.$item))
            {
                continue;
            }

            if (strcasecmp($item, $segment) === 0)
            {
                return $item;
            }
        }

        return NULL;
    }
}

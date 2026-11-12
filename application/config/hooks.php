<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$hook['pre_controller'][] = array(
    'class'    => 'Timezone_hook',
    'function' => 'set_timezone',
    'filename' => 'Timezone_hook.php',
    'filepath' => 'hooks'
);

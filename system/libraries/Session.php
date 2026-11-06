<?php
/**
 * CodeIgniter Session Library Wrapper
 * 
 * This file exists to maintain compatibility with load_class() function
 * which expects Session.php in libraries/ directory.
 * 
 * The actual Session library is in libraries/Session/Session.php
 */
defined('BASEPATH') OR exit('No direct script access allowed');

// Load the actual Session library from subdirectory
require_once BASEPATH.'libraries/Session/Session.php';

// The CI_Session class is already defined in Session/Session.php
// This file just ensures load_class() can find it


<?php

/**
 * @package Mido Framework
 * @version 1.0

* Plugin Name: Mido Framework
* Plugin URI: http://www.zalo.nyc
* Description: Mido Framework
* Author: Zalo Lorza
* Version: 1.0
* Author URI: http://www.zalo.nyc/
*/

define( 'MIDO_DIR', plugin_dir_url( __FILE__ ) );



/*
*
* Autoload dependencies
*
*/
require 'functions.php';
require 'vendor/autoload.php';

/*
*
* Class names in theme
*
*/
 \Mido\Mido::set_class_alias();


/*
 *
 * Run Framework
 *
 */
// \Mido\Mido::run();





/*
 *
 * Activation hook - Build Initial Theme Structure
 *
 */

//register_activation_hook( __FILE__, array('\Mido\ThemeBuilder','buildTheme') );

<?php


/**
* 
* Running Mido
*
*/

	   
if (class_exists('Mido')){

	Mido::run();

} else if(!is_admin()) {

	wp_die('This theme needs MidoFramework. Please contact hi@zalo.nyc for more information');

}

<?php

class Bootstrap extends MidoBootstrap {


	function _init(){

		define('INIT_DIR', BOOTSTRAP_DIR.'/init');
      	define('CONTROLLERS_DIR', BOOTSTRAP_DIR.'/controllers');
      	define('MANAGERS_DIR', BOOTSTRAP_DIR.'/managers');
		define('VIEWS_DIR', 'php/views');
		  

		add_theme_support( 'post-thumbnails');   



		if( function_exists('acf_add_options_page') ) {
	
			acf_add_options_page(array(
				'page_title' 	=> 'General Settings',
				'menu_title'	=> 'General Settings',
				'menu_slug' 	=> 'theme-general-settings',
				'capability'	=> 'edit_posts',
				'redirect'		=> false
			));
		}
	}

	function action_init(){
		unregister_taxonomy_for_object_type( 'post_tag', 'post' );
	}


};


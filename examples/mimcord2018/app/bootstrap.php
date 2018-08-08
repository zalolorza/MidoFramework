<?php

class Bootstrap extends MidoBootstrap {



	function _init(){

		define('INIT_DIR', BOOTSTRAP_DIR.'/init');
      	define('CONTROLLERS_DIR', BOOTSTRAP_DIR.'/controllers');
      	define('MANAGERS_DIR', BOOTSTRAP_DIR.'/managers');
		define('VIEWS_DIR', 'views');
		
		
		add_theme_support( 'post-thumbnails', array( 'page', 'noticia' ) );

		add_action( 'load-themes.php', array($this,'add_theme_caps'));

		if( get_role('author') ){
			remove_role( 'author' );
		  }
		  
		if( get_role('contributor') ){
			remove_role( 'contributor' );
		}
		  

		  

	}

	function add_theme_caps(){
		
		$role = get_role( 'editor' );
		$role->add_cap( 'edit_users' ); 
		$role->add_cap( 'delete_users' ); 
		$role->add_cap( 'create_users' ); 
		$role->add_cap( 'list_users' ); 
		$role->add_cap( 'promote_users' ); 
		
	}


};

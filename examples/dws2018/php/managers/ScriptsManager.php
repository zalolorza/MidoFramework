<?php

class ScriptsManager extends MidoManager {

	function _init(){
		
	
	}

	public static function add_google_maps(){

		add_action('wp_enqueue_scripts', function(){

			wp_register_script('google_maps',"https://maps.googleapis.com/maps/api/js?key=".MidoACF::get_maps_api_key(),null,null,true);
			wp_enqueue_script('google_maps');

		});

	}

	function action_wp_enqueue_scripts_20(){

				if(is_admin()) return;

				$site_url = get_site_url();
				if(!LangManager::is_default()){
					wp_localize_script( 'build_js', 'ICL_LANGUAGE_CODE', ICL_LANGUAGE_CODE );
					$site_url .= '/'. ICL_LANGUAGE_CODE;
				}
				$base = parse_url($site_url, PHP_URL_PATH);
			
				//expose vars
				wp_localize_script( 'build_js', 'THEME_URI', THEME_URI );
				wp_localize_script( 'build_js', 'SITE_URL', $site_url );
				
				wp_localize_script( 'build_js', 'BASE_PATH', $base );
				wp_localize_script( 'build_js', 'texts', LangManager::get_short_texts() );

				//CustomEase.js
				wp_register_script('customEase',THEME_URI.'/src/js/vendor/greensock/easing/CustomEase.js',null,null,true);
				wp_enqueue_script('customEase');

	}

	

}

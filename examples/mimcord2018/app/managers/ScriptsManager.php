<?php

define('SCRIPTS_VERSION', 3.2);

class ScriptsManager extends MidoManager {


	function _init(){
		self::remove_wp_emoji();
		
	}

	function remove_wp_emoji(){

		// REMOVE WP EMOJI
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');

		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}


	// REMOVE EMBED JS
	function action_init_999() {

					// Remove the REST API endpoint.
					remove_action( 'rest_api_init', 'wp_oembed_register_route' );

					// Turn off oEmbed auto discovery.
					add_filter( 'embed_oembed_discover', '__return_false' );

					// Don't filter oEmbed results.
					remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

					// Remove oEmbed discovery links.
					remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

					// Remove oEmbed-specific JavaScript from the front-end and back-end.
					remove_action( 'wp_head', 'wp_oembed_add_host_js' );

					// Remove filter of the oEmbed result before any HTTP requests are made.
					remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
	}

	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	function filter_tiny_mce_plugins($plugins){
				return array_diff($plugins, array('wpembed'));
	}

	// Remove all embeds rewrite rules.
	function filter_rewrite_rules_array($rules) {
			foreach($rules as $rule => $rewrite) {
					if(false !== strpos($rewrite, 'embed=true')) {
					unset($rules[$rule]);
					}
			}
			return $rules;
	}

	function action_wp_enqueue_scripts_10(){

				if(is_admin()) return;

				wp_deregister_script('jquery');

				if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
				    $gzip = true;
				} else {
					$gzip = false;
				}


				$version = SCRIPTS_VERSION;
				//compress vendor.js
				$vendor = THEME_URI.'/dist/vendor.js';
				if($gzip){
					$vendor = $vendor.'.gz';
				}
				$vendor = $vendor.'?v='.$version;

				//vendor.js // it's called jquery to inform pluguins that jquery is active. Jquery is actually embeded inside
				wp_register_script('jquery',$vendor,null,null,true);
				wp_enqueue_script('jquery');

				//bundle.js
				wp_register_script('app_js',THEME_URI.'/dist/bundle.js?v='.$version,null,null,true);
				wp_enqueue_script('app_js');

				//bundle.css
				wp_register_style('app_css',THEME_URI.'/dist/bundle.css?v='.$version,null,null,'all');
				wp_enqueue_style('app_css');

				//expose vars
				ScriptsManager::expose_vars();

	}



	public static function expose_vars(){

		wp_localize_script( 'app_js', 'THEME_URI', THEME_URI );
		wp_localize_script( 'app_js', 'SITE_URL', get_site_url() );
		wp_localize_script( 'app_js', 'ICL_LANGUAGE_CODE', ICL_LANGUAGE_CODE );

		do_action( 'scripts_vars');
	}

	public static function add_google_maps(){

		add_action('wp_enqueue_scripts', function(){
			wp_register_script('google_maps',"https://maps.googleapis.com/maps/api/js?key=".MidoACF::get_maps_api_key(),null,null,true);
			wp_enqueue_script('google_maps');

		});

	}

	public static function add_script($url,$version=0){

		add_action('wp_enqueue_scripts', function() use ($url,$version){

					wp_register_script($url,THEME_URI.'/dist/'.$url.'?v='.$version,null,null,true);
					wp_enqueue_script($url);
		},99);

	}

	public static function hydrate_google_maps(){
		add_action('scripts_vars', function(){
			wp_localize_script( 'app_js', 'GOOGLE_MAPS_URL', "https://maps.googleapis.com/maps/api/js?key=".MidoACF::get_maps_api_key() );
		});
	}
}

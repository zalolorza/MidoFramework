<?php

namespace Mido;

class Scripts extends Manager {


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

				//expose vars
				do_action( 'scripts_vars');

	}

	public static function deregister_jquery(){

		if(is_admin()) return;

		add_action('wp_enqueue_scripts', function(){
			wp_deregister_script('jquery');
		},10);
	}


	public static function add_google_maps(){

		add_action('wp_enqueue_scripts', function(){
			wp_register_script('google_maps',"https://maps.googleapis.com/maps/api/js?key=".MidoACF::get_maps_api_key(),null,null,true);
			wp_enqueue_script('google_maps');
		});

		add_action('scripts_vars', function(){
			wp_localize_script( 'app_js', 'GOOGLE_MAPS_URL', "https://maps.googleapis.com/maps/api/js?key=".MidoACF::get_maps_api_key() );
		});

	}


	public static function add_script($name,$url,$version=0,$priority=10){
		
		add_action('wp_enqueue_scripts', function() use ($name,$url,$version){

					wp_register_script($name,THEME_URI.$url,null,$version,true);
					wp_enqueue_script($name);
		},$priority);

	}

	public static function add_style($name,$url,$version=0,$priority=10){
		
		add_action('wp_enqueue_scripts', function() use ($name,$url,$version){

					wp_register_style($name,THEME_URI.$url,null,$version);
					wp_enqueue_style($name);

		},$priority);

	}

	public static function get_bundle($bundle){

		$scripts = ThemeInitializers::get_scripts();

		if(!$scripts) return false;
		
		if(!isset($scripts[$bundle]) || !is_array($scripts[$bundle])) return false;

		return $scripts[$bundle];

	}

	public static function add_styles_bundle($bundle,$version = 0){

		$bundle = self::get_bundle($bundle);

		if(!$bundle) return;

		if(isset($bundle['version'])){
				$version = $bundle['version'];
				unset($bundle['version']);
		}

		foreach($bundle as $script => $url){
            self::add_style($script,$url,$version);
        }
	}
	
	public static function add_scripts_bundle($bundle,$version = 0){

		$bundle = self::get_bundle($bundle);

		if(!$bundle) return;

		if(isset($bundle['version'])){
			$version = $bundle['version'];
			unset($bundle['version']);
		}

		foreach($bundle as $script => $url){
            self::add_script($script,$url,$version);
        };
	}
	
}

<?php

class WebManager extends MidoManager {


	function _init(){
		WebManager::set_footer(true);	
	}

	public static function get_texts($context = array()){

		$texts = array(
			'news' => __('Notícies','mimcord'),
			'tagline' => __('Paper Cord & Yarn Manufacturer', 'mimcord'),
			'menutrigger' => __('Menú','mimcord'),
			'nanses' => __('Nanses','mimcord'),
			'address' => __('Passeig del Ter, sn<br>Can LLanas<br>08560 Manlleu<br>Barcelona','mimcord'),
			'copyright' => __('Copyright © Mimcord SA','mimcord'),
			'telf' => __('T +34 938510766','mimcord'),
			'email' => __('mimcord@mimcord.com','mimcord'),
			'language' => __('Idioma','mimcord'),
			'download' => __('Descarregar','mimcord'),
			'back' => __('Tornar','mimcord'),
			'prev' => __('Anterior','mimcord'),
			'next' => __('Següent','mimcord'),
			'form_contacte' => __('Formulari de contacte','mimcord'),
			'gmaps' => __('Google Maps','mimcord'),
			'mimcordSA' => __('Mimcord SA','mimcord'),
			'datasheet' => __('Fitxa tècnica','mimcord'),
			'rememberme' => __("Recorda'm",'mimcord'),
		);
		
		if(isset($context['texts'])){
			$texts = array_merge($context['texts'],$texts);
		}

		return $texts;
	}

	function filter_render($context){

		$context['current_year'] = date("Y");
		$context['lang_switcher'] = self::get_languages();
		$context['texts'] = self::get_texts($context);
		$context['ICL_LANGUAGE_CODE'] = ICL_LANGUAGE_CODE;

		if(!isset($context['header'])){
			WebManager::hydrate_header($context);
		}
		
		return $context;

	}

	public static function hydrate_header(&$context, $options = array()){
		
		$class = '';
		$menu_class = '';
		$body_class = '';
		
		
		if ( !isset($options['display']) && empty($options['display']) ) $options['display'] = true;
		if ( !isset($options['background']) && empty($options['background']) ) $options['background'] = 'white';
		if ( !isset($options['format']) && empty($options['format']) ) $options['format'] = 'regular';
		if ( !isset($options['add_line']) && empty($options['add_line']) ) $options['add_line'] = false;
		if ( !isset($options['parallax']) && empty($options['parallax'])) $options['parallax'] = true;

		if($options['parallax']) {
			$options['parallaxClass'] = ' parallax ';
			if(is_string($options['parallax']) and $options['parallax']=="opacity"){
				$options['parallaxClass'] .= ' parallax-opacity ';
			}
		}
		
		if($options['add_line']) {
			$class .= 'has-line line-'.$options['add_line'];
		}

		$class .= ' format-'.$options['format'];
		$body_class .= ' header-format-'.$options['format'];

		if($options['format'] == 'slider' || ImagesManager::is_image($options['background']) || $options['background']=='thumbnail'){
			if($options['format'] == 'slider'){
				$options['display'] = false; 
			}
			if($options['background']=='thumbnail'){
				$options['background'] = get_post_thumbnail_id();
			}

			$options['has_image'] = true;
			$options['text-color'] = 'white';
			$class .= ' background-has-image background-blue ';
		} else {

			$class .= ' background-'.$options['background'];
			if($options['background'] == 'white'){
				$options['text-color'] = 'black';
			} else {
				$options['text-color'] = 'white';
			}
		}

		if($options['format'] == 'overlap'){
			$class .= ' format-overlap '; 
		}

		$class .= ' text-'.$options['text-color'];
		$menu_class .= ' color-'.$options['text-color'];
		$body_class .= ' header-text-color-'.$options['text-color'];
		

		if($options['display']){
			if ( !isset($options['title']) && empty($options['title']) ) $options['title'] = get_the_title();
			if ( empty($options['tagline']) ) $options['tagline'] = false;
			if($options['tagline']) {
				$class .= ' has-tagline';
			}
			if($options['title']) {
				$class .= ' has-title';
			}
		}


		$options['class'] .= $class;
		$options['menu_class'] .= $menu_class;
		$options['body_class'] .= $body_class;

		$context['header'] = $options;

		return $context;
	}

	public static function set_header($options){
	
		add_filter('render', function($context) use ($options){
			
			WebManager::hydrate_header($context, $options);

			return $context;

		},1);

	}

	public static function set_footer($show){
	
		add_filter('render', function($context) use ($show){
			
			$context['show_footer'] = $show;

			return $context;

		},1);

	}

	public static function get_languages(){

			if(!function_exists( 'icl_get_languages' )) return false;
			
			
			$langs = icl_get_languages('skip_missing=0');
				/*$langs = array_filter($langs,function($lan){
					if(!$lan['active']) return $lan;
				});*/
			
			foreach($langs as &$lan){
					$lan = array(
							url => $lan['url'],
							name => substr($lan['native_name'], 0, 3)
					);
				}
			return $langs;
	}

	public static function __filter_wp_get_attachment_url($url, $post_id) {

			//Skip file attachments
			if ( ! wp_attachment_is_image( $post_id ) ) {
				return $url;
			}
		
			//Correct protocol for https connections
			list( $protocol, $uri ) = explode( '://', $url, 2 );
		
			if ( is_ssl() ) {
				if ( 'http' == $protocol ) {
					$protocol = 'https';
				}
			} else {
				if ( 'https' == $protocol ) {
					$protocol = 'http';
				}
			}
		
			return $protocol . '://' . $uri;
	}


}

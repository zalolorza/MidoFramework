<?php
namespace Mido;
/**
 * Mido Admin class
 *
 * @package Mido
 */

final class ACF {



	use Singleton;
	
	/**
 	* SET ACF field as featured image
 	*/

	public static function update_featured_image($field){

		add_filter('acf/update_value/name='.$field, function($value, $post_id, $field ){

			if($value != ''){
	        //Add the value which is the image ID to the _thumbnail_id meta data for the current post
				update_post_meta($post_id, '_thumbnail_id', $value);
			}

			return $value;

		}, 10, 3);


	}
	


	public static function update_featured_image_from_gallery($field){

		add_filter('acf/update_value/name='.$field, function($value, $post_id, $field){

			if($value != ''){
	        //Add the value which is the image ID to the _thumbnail_id meta data for the current post
				update_post_meta($post_id, '_thumbnail_id', $value[0]);   
			}

			return $value;

		}, 10, 3);

	}



	/**
	 * Save ACF field as custom meta
	 */

	public static function update_post($post_meta,$acf_field){


		add_action('acf/update_value/name='.$acf_field, function($value, $post_id, $field) use ($post_meta){

				// Update post
				$my_post = array(
					'ID'           => $post_id,
					$post_meta => $value
					);

			    // Update the post into the database


				wp_update_post( $my_post );

				return false;

		}, 10,3);

		if(is_admin()){

			add_action("acf/load_field/name=".$acf_field, function( $field) use ($post_meta){

				global $post;
				$field['value'] = $post->$post_meta;
				return $field;

			});

		}
		
	}


	/**
	 * Add Options Page
	 */


	public static function add_options_page($title,$position){
		if(function_exists('acf_add_options_page')){
			if(is_int($position)){
				$parent = false;
			} else {
				$parent = $position;
				$position = 0;
			}
			$option_page = acf_add_options_page(array(
				'page_title'    => $title,
				'menu_title'    => $title,
				'menu_slug'     => sanitize_title($title),
				'redirect'  => false,
				'parent_slug' => $parent,
				'position' => $position
				));
		}
		return $this;
	}

	/**
	 * Add Toolbar
	 */

	public static function set_toolbars($custom_toolbars = array()){

		add_filter('acf/fields/wysiwyg/toolbars', function($toolbars) use ($custom_toolbars){

		unset($toolbars['Full']);
	    	unset($toolbars['Basic']);

	    	foreach($custom_toolbars as $custom_toolbar => $buttons ){
	    		
	    		$toolbars[$custom_toolbar] = array();
    			$toolbars[$custom_toolbar][1] = $buttons;

	    	}
	    	
	    	return $toolbars;

		});

	}

	/**
	 * API maps
	 */

	public static function update_maps_api_key($key) {

		//define('GOOGLE_API_KEY',$key);

		add_filter('acf/settings/google_api_key', function () use ($key) {
    				return $key;
		});

		/*add_filter('render', function ($context) use ($key) {
    				$context['google_api_key'] = $key;
		});*/

	}

	/**
	 * get API maps key
	 */

	public static function get_maps_api_key(){

		return ThemeInitializers::get_val('admin','acf','google_api_key');

	}

	

}
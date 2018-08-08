<?php 
namespace Mido;

final class Pages
{
	use Singleton;


	protected $templates = [];
	
	public static function get_page($name = null){
		
		$page =  get_page_by_path( $name );

		
		if(!$page){
			$page = get_page_by_title( $name ) ;
		}


		return $page;
	}

	public static function get_id($name){

		$page = self::get_page($name);

		if (function_exists('icl_object_id')) {
			$id = apply_filters('wpml_object_id', $page -> ID, 'page', TRUE, ICL_LANGUAGE_CODE);
			if(!$id){
				$id = $page -> ID;
			}
		} else{
			$id = $page -> ID;
		}

		return $id;
	}

	public static function get_title($name){

		$pageId = self::get_id($name);
		
		return get_the_title($pageId);
	}

	public static function get_link($name){

		$if = self::get_id($name);

		if($id){
			
			$permalink = get_permalink($id);

			return $permalink;
		} else {
			return false;
		}
	}


	public static function add_page($params){

		$_this = self::getInstance();

		$pageID = self::get_id($params['title']);

		if (!$pageID) {

			if(!$params['parent']) { 
				$params['parent'] = false;
			} else {
				$params['parent'] = self::get_id($params['parent']);
			}

			if(!$params['slug']) $params['slug'] = null;

			$page = array(
				'comment_status' =>'closed', 
				'ping_status' => 'closed',
				'post_name' => $params['slug'],
				'post_parent' => $params['parent'],
				'post_status' => 'publish',
				'post_title' => $params['title'],
				'post_type' =>  'page'
			); 

			$pageID = Post::add($page);

		
		}

		if($params['template']){

			update_post_meta($pageID, "_wp_page_template", $params['template']);
			update_post_meta($pageID, "is_framework_page", true);
		}

		return $_this;

	}

}
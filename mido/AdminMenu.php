<?php
namespace Mido;
/**
 * Mido Admin Menu class
 *
 * @package Mido
 */

class AdminMenu {
	/*
    *
    * Construct
    *
    */
	use Singleton;

	private $submenu_pages = [];
	private $delete_submenu_pages = [];
	private $pages_on_menu = [];


	private function __construct(){

		//add_filter('parent_file', array($this,'set_current_menu'));

		//add_filter('custom_admin_submenu', array($this,'add_separator_class_submenu'));

		//add_action('admin_menu', array($this,'add_admin_menus'));
	}


	public static function add_options_page_acf(){
		ACF::add_options_page();
	}


	public static function add_menu_page($page_slug_or_title, $title = null, $position = 0, $icon = 'dashicons-clipboard',$capability = 'edit_pages', $function = ''){


			$_this = self::getInstance();

			$id = Pages::get_id($page_slug_or_title);

			if(!$title){
				$title = get_the_title($id);
			}




			$page_title = $title;
			$menu_title = $title;
		    	$menu_slug = 'post.php?post=' . $id . '&action=edit';

			$hook_suffix = add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon, $position);

			global $menu;
			ksort($menu);


			add_filter('parent_file', function($parent_file) use($id){

					global $current_screen, $pagenow, $post;
				    $post_type = $current_screen->post_type;

				    if ($post_type == 'page' && isset($post) && $post->ID == $id){
			                $custom_submenu_file = 'post.php?post=' . $id . '&action=edit';
			                $parent_file = 'post.php?post=' . $id . '&action=edit';
			            }
			    
				    return $parent_file;

			});
		   
			$_this->last_slug = $menu_slug;
		    return $menu_slug;

	}



	public static function delete_submenu_page($menu_slug){

		$_this = self::getInstance();

		array_push($_this->delete_submenu_pages, $menu_slug);

	}

	public static function add_submenu_page($page_slug_or_title, $title = null, $parent_slug = null, $capability='edit_pages', $function=''){
		

		$_this = self::getInstance();

		if($parent_slug == null){
			$parent_slug = $_this->last_slug;
		};

		$id = Pages::get_id($page_slug_or_title);

		if(!$title){
				$title = get_the_title($id);
			}


		$page_title = $title;
		$menu_title = $title;
		$menu_slug = 'post.php?post=' . $id . '&action=edit';

	

		add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function
		            );


	}




	/*
	*
 	* Add menu separator
 	*
 	*/


	public static function add_menu_separator ($position){

		global $menu;
		  $index = 0;
		  foreach($menu as $offset => $section) {
		    if (substr($section[2],0,9)=='separator')
		      $index++;
		    if ($offset>=$position) {
		      $menu[$position] = array('','read',"separator{$index}",'','wp-menu-separator');
		      break;
		    }
		  }
		  ksort( $menu );

	}

	public static function add_separator_class_submenu ($item){
		if ($item["defaults"]["page_title"] == 'wp-menu-separator') {
		        $item['css_class'] = 'wp-menu-separator';
		    }
		    return $item;
	}

}
<?php

class MenusManager extends MidoManager {


	function filter_render($context){

		//$context['social_networks'] = self::get_menu('social_networks');
		
		$context['nav'] = array(
			'main' => self::get_menu(),
			'footer_sitemap' => self::get_menu('footer_sitemap'),
			'footer_forms' => self::get_menu('footer_forms'),
		);

		return $context;

	}

	


	public static function get_menu($menu = 'main'){

		if(is_object($menu)){

			$menu_name=$menu->get_param( 'name' );

		} else {

			$menu_name = $menu;
		}




		if($menu_name == 'social_networks'){

			global $sitepress;
			$current_language = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();
			$sitepress->switch_lang($default_language);
		}



		$locations = get_nav_menu_locations();
			$menu_id = $locations[ $menu_name ];

			$items = wp_get_nav_menu_items($menu_id);

			if(!$items) return false;
			
			foreach($items as &$item){

				
				$newItem = (array) $item;


				$newItem['ID'] = $item->ID;
				$newItem['id'] = $item->ID;
				$newItem['title'] = $item->title;
				$newItem['url'] = $item->url;
				$classes = '';


				foreach($item->classes as $class){
					$classes = $class.' ';
				}

				if(get_the_ID() == $item->object_id || wp_get_post_parent_id(get_the_ID()) == $item->object_id) {
					$classes .= ' selected ';
				}
				$newItem['classes'] = $classes;
				$newItem['target'] = $item->target;
				if(!$newItem['target']){
					$newItem['target'] ='_self';
				}

				switch($menu_name){


						case 'main':
							$newItem['hashtag'] = sanitize_title(get_field('hashtag',  $item->ID));
							if(!$newItem['hashtag'] && !$newItem['url']){
									$newItem['hashtag'] = sanitize_title($newItem['title']);
							}
							if($newItem['hashtag']) {
									$newItem['url'] = '#'.$newItem['hashtag'];
									if(!is_front_page()){
										$newItem['url'] = get_home_url().$newItem['url'];
									}
							}
							break;


						case 'social_networks':

								$newItem['svg'] = get_field('svg',  icl_object_id($item->ID, 'nav_menu_item', false, 'ca'));
								break;
				}

				$item = $newItem;
			}

			if($menu_name == 'social_networks'){
				$sitepress->switch_lang($current_language);
			}


		return $items;

	}





}

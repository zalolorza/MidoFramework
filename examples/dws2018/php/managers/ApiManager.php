<?php

class ApiManager extends MidoManager {

	function action_rest_api_init(){

		self::register_routes();
		self::register_fields();

	}

	public static function get_namespace(){

		$version = '1';
    		$namespace = 'theme/v' . $version;

    		return $namespace;

	}

	public static function register_routes(){
	 	

			register_rest_route( 'wp/v2', '/room-values/(?P<id>\d+)', array(
				'methods' => 'GET',
				'callback' => function(WP_REST_Request $request ){
								$args = array('post_type' => 'added-value',
								'posts_per_page'  => -1,
								//'fields' => 'ids',
								'tax_query' => array(
									array(
										'taxonomy' => 'room',
										'field' => 'id',
										'terms' =>   $request['id'],
									),
								),
							);
							
							$query = new WP_Query($args);
							$posts = array();

							
							

							foreach($query->posts as $key => $post){

								$id = $post->ID;
								
								$image_blur = get_field('image_blur',$id);
								$image_design_elements = get_field('image_design_elements',$id);
								
								$belongs_to = get_field('belongs_to',$id);

								
								$in_tax = $belongs_to[array_search($request['id'], array_column($belongs_to, 'tax'))];

							
								$is_showed_in_any = array(
									'main' => false,
									'design' => false
								);

								foreach(array_column($belongs_to, 'show_in') as $showed_in){
									if(in_array('main',$showed_in)){
										$is_showed_in_any['main'] = true;
									}
									if(in_array('design',$showed_in)){
										$is_showed_in_any['design'] = true;
									}
								};
								

								$parsed_post = array(
									'ID' => $post->ID,
									'post_name' => $post->post_name,
									'show_in'=> $in_tax['show_in']
								);

								if(in_array('main',$in_tax['show_in'])){
									$parsed_post['coord'] = array(
										'x' => $in_tax['x'],
										'y' => $in_tax['y']
									);
								};
								
								if($is_showed_in_any['main'] && $image_blur){

									$parsed_post['img_blur'] = \Timber\ImageHelper::resize($image_blur,80,80,'center',false);
								}

								if($is_showed_in_any['design'] && $image_design_elements){
									$parsed_post['img_design_elements'] = $image_design_elements['sizes']['medium'];
									$parsed_post['post_title'] = $post->post_title;
									$parsed_post['post_description'] = $post->post_content;
								}

								$posts[] = $parsed_post;

								
							}

							return $posts;
				},
			  ) );

	}

	public static function register_fields(){


		register_rest_field( 'added-value',
			    'featured_media', 
			    array(
			        'get_callback'    => function($object, $field_name, $request){
						$featured = wp_get_attachment_image_src($object['featured_media'],'full')[0];
						if($object['acf']['video_mp4']){
							$prop = $object['acf']['video_mp4']['height'] / $object['acf']['video_mp4']['width'];
						} else {
							$prop = 1;
						}
						$featured_crop = \Timber\ImageHelper::resize($featured,1000,1000*$prop,'center',false);
						return $featured_crop;
			        },
			        'update_callback' => null,
			        'schema'          => null,
			         )
				);
		
		register_rest_field( 'story',
			    'featured_on_related', 
			    array(
			        'get_callback'    => function($object, $field_name, $request){
						if($object['acf']['image_related']){
							$image = $object['acf']['image_related'];
						} else {
							$image = $object['featured_media'];
						}
						$featured = wp_get_attachment_image_src($image,'full')[0];
						$featured_crop = \Timber\ImageHelper::resize($featured,355,128,'center',false);
						return $featured_crop;
			        },
			        'update_callback' => null,
			        'schema'          => null,
			         )
				);
	
		
		register_rest_field( 'story',
			    'video_src', 
			    array(
			        'get_callback'    => function($object, $field_name, $request){
						return StoriesManager::get_video($object['acf']['video']);
					},
			        'update_callback' => null,
			        'schema'          => null,
			         )
				);
	
	}


}
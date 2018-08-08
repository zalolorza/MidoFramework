<?php

namespace Mido;

class Post extends \Timber\Post {


	public function add($post){


			if($post['post_parent'] != null && $post['post_type'] == 'page' && !is_integer($post['post_parent'])){

					$parent = Pages::get_id($post['post_parent']);
					$post['post_parent'] = $parent->ID;
				}


			$postID = wp_insert_post( $post );

			
			return $postID;
			
	}

	public static function get_posts($post_type = 'post', $ppp = -1, $offset = 0){




		$args = array(
			'offset' => $offset,
		     'post_type' => $post_type,
		     'post_status' => 'publish',
		     'posts_per_page' => $ppp
		    // 'orderby' => 'date',
		    // 'suppress_filters' => false
	    );

	    $posts = Mido::get_posts($args);

	  	return $posts;
	}

	public function get_last($post_type = 'post'){
		$posts_array = self::getPosts($post_type, 1);
	    $last_post = $posts_array[0];
	    if (sizeof($posts_array) > 0) {
	        $last_post->url = get_permalink($last_post->ID);
	    }
	    return $last_post;
	}

    
}

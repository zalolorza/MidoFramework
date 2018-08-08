<?php

class HeaderManager extends MidoManager {


	function _init(){

		
		
	}

	function filter_render($context){

		if(isset($context['header'])) return $context;


		if($context['router']['view_type'] == 'archive'){
			$post = get_post(PostsManager::get_archive_id());
		} else {
			global $post;
		}
		

		
		$context['header'] = array(
			'image' => Timber\ImageHelper::resize(wp_get_attachment_image_src($post->_thumbnail_id,'full')[0], '1440', '860'),
			'video_loop' => VideoManager::get_background(1,'',$post->ID),
			'vimeo' => VideoManager::get_video_field('video', 1,$post->ID),
			'headline' => get_field('tagline',$post->ID)
		);
		
	
		return $context;

	}
 
	


}

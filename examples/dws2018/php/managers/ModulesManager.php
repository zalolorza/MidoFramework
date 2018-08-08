<?php

class ModulesManager {

	public static function get(){

        $modules = get_field('modules');

        $i = 0;

        while( have_rows('modules') ) : the_row();
		
                // get layout
                $layout = get_row_layout();
                
                switch($layout){
                        case 'video_gallery':
                            foreach($modules[$i]['video_gallery'] as &$post){
                                $video = get_field('video',$post->ID,false);
                                if($video == '') $video = false;
                                $post->video = $video;
                                $post->image = get_the_post_thumbnail_url($post,'full');
                            }
                            break;

                        case '3cols_text':
                            $cols = array();
                            for ($c = 1; $c <= 3; $c++) {
                                $cols[] = array(
                                    'title' => $modules[$i]['title_'.$c],
                                    'text' => $modules[$i]['text_'.$c]
                                );
                            }
                            $modules[$i]['cols'] = $cols;
                           
                            break;
                        case 'gallery':
                            foreach($modules[$i]['gallery'] as &$image){
                                $video = get_field('video',$image['ID'],false);
                                if($video == '') $video = false;
                                $image['video'] = $video;
                            }
                            break;
                        case 'video':
                            VideoManager::hydrate_module_big_video($modules[$i]);
                            break;
                 }

                 $i++;

        endwhile;

        
        return $modules;

	}
 
}

<?php

class VideoManager {

	function get_background($depth = 1,$prefix = '',$id = false){

        if(!$id) $id = get_the_ID();

        if($depth == 1){
            $get_field = 'get_field';
        } else {
            $get_field = 'get_sub_field';
        }

        $mp4 = $get_field($prefix . 'video_mp4',$id);

        if(!$mp4) return false;

        $webm = $get_field($prefix . 'video_webm',$id);
                    
        return array(
                        'width' => $mp4['width'],
                        'height' => $mp4['height'],
                        'mp4' => $mp4['url'],
                        'webm' => $webm,
                    );
     }

     function hydrate_module_big_video(&$module){

        if($module['loop']['video_mp4']){
            $module['video_loop'] = array(
                'width' => $module['loop']['video_mp4']['width'],
                'height' => $module['loop']['video_mp4']['height'],
                'mp4' => $module['loop']['video_mp4']['url'],
                'webm' => $module['loop']['video_webm']
            );
        } else {
            $module['video_loop'] = false;
        }

        unset($module['loop']);


        if($module['video']){
            $module['video'] = get_sub_field('video',false);
        }

        return $module;

     }


     public static function get_video_field($fieldname = 'video', $depth = 1, $id = false){


        if($depth == 1){
            $get_field = 'get_field';
            return  $get_field($fieldname, $id, false);
        } else if($depth == 2) {
            $get_field = 'get_sub_field';
            return  $get_field($fieldname, false);
        }

     }

     public static function get_video_img($ID){

        return self::get_video(get_field('video',$ID));

     }


     public static function get_video($iframe){

        if(!$iframe) {
            return false;
        }

        // use preg_match to find iframe src
        preg_match('/src="(.+?)"/', $iframe, $matches);

        if(sizeof($matches) == 0){
            preg_match('/href="(.+?)"/', $iframe, $matches);
        }

        $src = $matches[1];

        return $src;
    }
}
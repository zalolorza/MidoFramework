<?php

class PostsManager {


    function get_archive_id(){

        return get_option( 'page_for_posts' );

    }


    function get_featured($layout = 'footer'){

        $archive_id = self::get_archive_id();

        if(is_bool($layout)){
            $layout_modifier = 'diagonal';
        } else if(strpos($layout, 'simple') !== false) {
            $layout_modifier = 'simple';
        } else if(strpos($layout, 'diagonal') !== false) {
            $layout_modifier = 'diagonal';
        }
       

        if(is_bool($layout) || strpos($layout, 'footer') !== false){
            $layout = 'footer';
        } else {
            $layout = $layout;
        }

        return array(
            'title' => get_field('featured_title',$archive_id),
            'text' => get_field('featured_text',$archive_id),
            'link' => get_the_permalink($archive_id),
            'posts' => self::get_featured_posts(),
            'layout' => $layout,
            'layout_modifier' => $layout_modifier
        );

    }

    function get_featured_posts(){

        $posts = Mido::get_posts(array('post_type' => 'post', 'posts_per_page' => 3,  'post__not_in' => array(get_the_ID())));

        foreach($posts as &$post){
            $post->image = wp_get_attachment_image_src($post->_thumbnail_id,'full')[0];
        };

        return $posts;
    }



}
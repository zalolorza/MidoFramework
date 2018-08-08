<?php

class CategoriesManager  {

    public static function get_all($hydrate = true){

        $categories = get_terms( array(
			'taxonomy' => 'category',
			'hide_empty' => true
        ) );
        
        if($hydrate){
            foreach($categories as &$category){
                self::hydrate_one($category);
            }
        }
        
        return $categories;
		
    }

	public static function get_current(){
        return self::hydrate_one(get_category(get_query_var('cat')));
    }

    public static function hydrate_one(&$category){
            $category->image = get_field('featured_image',$category);
			$category->title = get_field('long_title',$category);
			if(!$category->title){
				$category->title = $category->name;
            }
            $category->link = get_category_link($category->term_id);
            return $category;
    }

}
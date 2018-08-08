<?php

class ProductsManager  {

	public static function get_current(){
        
    }

    public static function get_from_category($category, $setup = true){

        $args = array( 'category' => $category->term_id, 'post_type' =>  'producte' ); 
        $postslist = get_posts( $args );    
        foreach ($postslist as &$post) {
            if($setup){
                setup_postdata($post);
                $post->slider = get_field('slider',$post->ID);
            }

            $post->technical_sheet = get_field('data_sheet',$post->ID);
     
           
        }

        return $postslist;
            
    }

    public static function get_technical_sheets(){
        $categories = CategoriesManager::get_all(false);
        foreach ($categories as $key => &$category) {
            $products = self::get_from_category($category, false);
            $category_sheets = get_field('technical_sheets', $category);
            if($category_sheets) {
                $technical_sheets = array_column($category_sheets,'technical_sheet');
            } else {
                $technical_sheets = array();
            }
            foreach($products as $product){
                
                if($product->technical_sheet){
                    $title = $product->technical_sheet['title'];
                    array_push($technical_sheets,array(
                        'title' => $title,
                        'url' => $product->technical_sheet['url'],
                    ));
                }
            };
            
            if(sizeof($technical_sheets) > 0) {
                $category->downloads = $technical_sheets;
                $category->title = $category->name;
            } else {
                unset($categories[$key]);
            }
        }
        return $categories;
    }
}
<?php
namespace Mido;

final class CustomPostTypes
{
    
    use Singleton;
    
    public $post_types;
    public $taxonomies;

    private function __construct () { 
        global $wp_post_types;
        $this->post_types = array_keys($wp_post_types);
        $this->taxonomies = get_taxonomies();
    }

    public static function remove($type){

            
            $_this = self::getInstance();

            if ($type == 'post'){
     
                    //self::remove_taxonomy('category');
                    //self::remove_taxonomy('post_tag');
                    //register_taxonomy('category', array());
                    //register_taxonomy('post_tag', array());

                    global $wp_post_types;
                    $wp_post_types['post']->show_ui = false;
                    $wp_post_types['post']->show_in_menu = false;
                    $wp_post_types['post']->show_in_nav_menus = false;
                    $wp_post_types['post']->show_in_admin_bar = false;
                    $wp_post_types['post']->show_in_rest = false;

             
                    add_action('admin_menu', function(){
                        global $submenu;
                        unset($submenu['post-new.php?post_type=post'][10]);
                    });

                    add_action( 'wp_before_admin_bar_render', function() {
                        global $wp_admin_bar;
                        $wp_admin_bar->remove_menu('new-post');
                    } );

                    add_action( 'load-post-new.php', function() {
                    if ( get_current_screen()->post_type == 'my_post_type' )
                        wp_die( "You ain't allowed to do that!" );
                    } );

                    add_action( 'admin_menu', function(){
                        remove_menu_page( 'edit.php' );
                    } );

                    unset($_this->post_types[array_search('post', $_this->post_types)]);
            
                    return $_this;
    
            };

            unset($_this->post_types[array_search($type, $_this->post_types)]);

            add_action('init',function() use ($type){
                unregister_post_type($type);
                global $wp_post_types;
                unset($wp_post_types[$type]);
            }); 

            return $_this;           
    }


    public static function add($name, $properties){

        $_this = self::getInstance();

        if(in_array($name,$_this->post_types)) {

            Hooks::add_action('init',array($_this,'edit_register_post_type'),array($name,$properties));

        } else {

            $_this->post_types[] = $name;
            
           Hooks::add_action('init','register_post_type',array($name,$properties));

        } 

        return $_this;
    }

    public function edit_register_post_type($name,$properties){

        if ($name == 'post'){
                 //register_taxonomy('category', array());
                 //register_taxonomy('post_tag', array());

                 add_action('admin_menu',function() use($properties){
                      global $menu;

                      foreach($menu as &$item){
                            if(isset($item[5]) && $item[5] == 'menu-posts'){
                                $item[0] = $properties['labels']['name'];
                            }
                      }
                     
                 });
            };

         global $wp_post_types;

         foreach($properties as $key=>$val){

            if($key != 'labels'){

                $wp_post_types[$name]->$key = $val;

            }

         }

         foreach($properties['labels'] as $key=>$val){

            $wp_post_types[$name]->labels->$key = $val;

         }
        
    }

 
    public static function add_taxonomy($name,$post_types,$properties){

        $_this = self::getInstance();

        if(in_array($name,$_this->taxonomies)) return;

        Hooks::add_action('init','register_taxonomy',array($name, $post_types, $properties));
    

    }

    public static function remove_taxonomy($tax){

        $_this = self::getInstance();

         unregister_taxonomy($tax);
         unset($_this->taxonomies[array_search($tax, $_this->taxonomies)]);

    }
}


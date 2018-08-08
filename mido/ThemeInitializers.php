<?php
namespace Mido;
/**
 * Mido Botstrap class
 *
 * @package Mido
 */

class ThemeInitializers {


    use Singleton;

	/*
   	*
    * Init theme
    *
    */

	public static function initTheme(){

        self::init_theme();
        self::init_admin();

	}


    /********************************************
    *                                           *
    *              THEME SETUP                  *
    *                                           *
    *********************************************/


    private static function init_theme(){

        self::set_cpt();
        self::set_tax();
        self::set_routes();
        self::set_page_templates();
        self::set_images();
        self::set_menus();
        self::set_scripts();

    }

    /*
    *
    * styles
    *
    */
    public static function store_scripts($scripts) {
        $_this = self::getInstance();
        $_this->scripts = $scripts;
    }
    
    public static function get_scripts() {
        $_this = self::getInstance();
        return $_this->scripts;
	}

    private static function set_scripts(){
        if(!file_exists(INIT_DIR."/scripts.ini")) return false;
        $scripts = parse_ini_file(INIT_DIR."/scripts.ini", true);

        ThemeInitializers::store_scripts($scripts);

        if(isset($scripts['config']['version'])){
            $version = $scripts['config']['version'];
        } else {
            $version = 0.0;
        }

        if(isset($scripts['config']['jquery']) && $scripts['config']['jquery'] == 'false'){
                Scripts::deregister_jquery();
        }

        if(isset($scripts['config']['google_maps']) && $scripts['config']['google_maps'] == 'true'){
            Scripts::add_google_maps();
        }

        Scripts::add_scripts_bundle('js',$version);
        Scripts::add_styles_bundle('css',$version);
      
        
    }

    

    /*
    *
    * Menus
    *
    */

    private static function set_menus(){

        if(!file_exists(INIT_DIR."/menus.ini")) return false;

        $menus = parse_ini_file(INIT_DIR."/menus.ini", false);

        foreach($menus as &$menu){
            $menu = __($menu,'mido-admin');
        };

        add_action( 'init', function() use ($menus){

            register_nav_menus($menus);

        });

    }


    /*
    *
    * Custom Post Types
    *
    */

	private static function set_cpt(){

		if(!file_exists(INIT_DIR."/cpt.ini")) return false;

        $post_types = parse_ini_file(INIT_DIR."/cpt.ini", true);

        foreach($post_types as $post_type_name => $post_type){


            if(isset($post_type['remove']) and $post_type['remove']){


                CustomPostTypes::remove($post_type_name);

            } else {

                $post_type_params = array('labels' => array(
                            'name' => self::format_label($post_type['name']),
                            'singular_name' => self::format_label($post_type['singular_name']),
                            'all_items' => self::format_label($post_type['all_items']),
                            'add_new' => self::format_label($post_type['add_new']),
                            'add_new_item' => self::format_label($post_type['add_new_item']),
                            'edit' => self::format_label($post_type['edit']),
                            'edit_item' => self::format_label($post_type['edit_item']),
                            'new_item' => self::format_label($post_type['new_item']),
                            'view_item' => self::format_label($post_type['view_item']),
                            'search_items' => self::format_label($post_type['search_items']),
                            'not_found' => self::format_label($post_type['not_found']),
                            'not_found_in_trash' => self::format_label($post_type['not_found_in_trash']),
                            'parent_item_colon' => self::format_label($post_type['parent_item_colon']),
                            ),
                        'description' => self::format_label($post_type['description']),
                        'public' => self::format_param($post_type['public']),
                        'publicly_queryable' =>self::format_param($post_type['publicly_queryable']),
                        'exclude_from_search' => self::format_param($post_type['exclude_from_search']),
                        'show_ui' => self::format_param($post_type['show_ui']),
                        'show_in_menu' => self::format_param($post_type['show_in_menu']),
                        'query_var' => self::format_param($post_type['query_var']),
                        'menu_icon' => self::format_param($post_type['menu_icon']),
                        'rewrite' => self::format_param($post_type['rewrite']),
                        'has_archive' => self::format_param($post_type['has_archive']),
                        'capability_type' => self::format_param($post_type['capability_type']),
                        'hierarchical' => self::format_param($post_type['hierarchical']),
                        'supports' => self::format_param($post_type['supports']),
                        'show_in_rest' => self::format_param($post_type['show_in_rest']),
                        'label' => $post_type_name,
                        );

                if(isset($post_type['menu_position'])){
                    $post_type_params['menu_position'] = self::format_param($post_type['menu_position']);
                }


                CustomPostTypes::add($post_type_name, $post_type_params);

            }

        }

    }

    /*
    *
    * Custom Taxonomies
    *
    */


    private static function set_tax(){

    	if(!file_exists(INIT_DIR."/taxonomies.ini")) return false;

        $taxonomies = parse_ini_file(INIT_DIR."/taxonomies.ini", true);


        foreach($taxonomies as $tax_name => $tax){

            if(isset($tax['remove']) and $tax['remove']){


            } else {


                $tax_post_types= $tax['post_types'];

    
 
                $tax_params = array(
                                            'hierarchical'      => self::format_param($tax['hierarchical']),
                                            'labels'            => array(
                                                'name'              => self::format_label($tax['name']),
                                                'singular_name'     => self::format_label($tax['singular_name']),
                                                'search_items'      => self::format_label($tax['search_items']),
                                                'all_items'         => self::format_label($tax['all_items']),
                                                'parent_item'       => self::format_label($tax['parent_item']),
                                                'parent_item_colon' => self::format_label($tax['parent_item_colon']),
                                                'edit_item'         => self::format_label($tax['edit_item']),
                                                'update_item'       => self::format_label($tax['update_item']),
                                                'add_new_item'      => self::format_label($tax['add_new_item']),
                                                'new_item_name'     => self::format_label($tax['new_item_name']),
                                                'menu_name'         => self::format_label($tax['menu_name']),
                                                ),
                                            'show_ui'           => self::format_param($tax['show_ui']),
                                            'show_in_menu'      => self::format_param($tax['show_in_menu']),
                                            'show_in_nav_menus' => self::format_param($tax['show_in_nav_menus']),
                                            'show_admin_column' => self::format_param($tax['show_admin_column']),
                                            'query_var'         => self::format_param($tax['query_var']),
                                            'show_in_rest'      => self::format_param($tax['show_in_rest'])
                                            );
                                            
                if(isset($tax['rewrite']) && self::format_param($tax['rewrite'])){
                    $tax_params['rewrite'] = $tax['rewrite'];
                }


                CustomPostTypes::add_taxonomy($tax_name,$tax_post_types,$tax_params);
            }
        }
   }



    /*
    *
    *   Custom routers
    *
    */

    private static function set_routes(){

    	if(!file_exists(INIT_DIR."/routes.ini")) return false;

        $routes = parse_ini_file(INIT_DIR."/routes.ini", true);

        $equal_routes = array();

        foreach($routes as $route => $args){

                if(!$args){

                    $equal_routes[] = $route;

                } else {

                    if($equal_routes){

                        foreach($equal_routes as $equal_route){

                            Router::map_ini($equal_route, $args);

                        }

                        $equal_routes = array();
                    }

                    Router::map_ini($route,$args);
                }


        }



    }


    /*
    *
    *   Page TWIG templates
    *
    */

    private static function set_page_templates(){


            if(!file_exists(INIT_DIR."/page_templates.ini")) return false;

            $templates = parse_ini_file(INIT_DIR."/page_templates.ini", false);


            foreach ( $templates as $name => $file){
                        PageTemplater::add_template($name, $file);
                  }


    }


    /*
    *
    *   Image sizes and quality
    *
    */

    static private $images_ini;

    private static function set_images(){

        if(!file_exists(INIT_DIR."/images.ini")) return false;

        $images = parse_ini_file(INIT_DIR."/images.ini", true);
        self::$images_ini = $images;


        //add sizes
        $size_names = array();
        $protected_names = array("thumb", "thumbnail", "medium", "large", "post-thumbnail");

        foreach ( $images['sizes'] as $name => $val){

                        $val = self::explode(",",$val);

                        if(in_array($name,$protected_names)){
                            $crop = self::format_param($val[2]);
                            if($crop){
                                $crop = 1;
                            } else {
                                $crop = 0;
                            }

                            add_action('after_switch_theme',function() use ($name, $val, $crop){
                                update_option( $name.'_size_w', $val[0] );
                                update_option( $name.'_size_h', $val[1] );
                                update_option( $name.'_crop', $crop );
                            });
                        
                        } else {

                            add_image_size( $name, $val[0], $val[1], self::format_param($val[2]));

                            if(isset($val[3])){

                                $size_names[$name] = __($val[3], 'mido-admin');

                            }

                        }


        }


        add_filter( 'image_size_names_choose', function( $sizes ) use ($size_names) {

            return array_merge( $sizes, $size_names);

        } );



        //quality

       if($images['quality']['jpeg']){

            add_filter( 'jpeg_quality', create_function( '', 'return '.$images['quality']['jpeg'].';' ) );

       }

    }





    /********************************************
    *                                           *
    *           ADMIN INITIALIZERS              *
    *                                           *
    *********************************************/


    /*
    *
    *   init admin
    *
    */

    static private $admin_ini;

    private static function init_admin(){

        self::mime_types();


        // Only if admin.ini exists

        if(!file_exists(INIT_DIR."/admin.ini")) return false;

        self::$admin_ini = parse_ini_file(INIT_DIR."/admin.ini", true);


        self::admin_editor();
        self::admin_layout();
        self::admin_scripts();



        Admin::default_modifications();

        self::admin_acf();

    }


    /*
    *
    *   Add admin scripts
    *
    */

    private static function admin_scripts(){

        if(!self::$admin_ini['scripts']) return;

         $scripts = self::$admin_ini['scripts'];


        if(isset($scripts['login_css'])){
            Admin::add_login_css($scripts['login_css']);
        }

        if(!is_admin()) return;


        if(isset($scripts['editor_css'])){
            Admin::add_editor_css($scripts['editor_css']);
        }


        if(isset($scripts['admin_css'])){
            Admin::add_admin_css($scripts['admin_css']);
        }

        if(isset($scripts['admin_js'])){
            Admin::add_admin_js($scripts['admin_js']);
        }

    }

    /*
    *
    *   basic settings admin (a bunch of custom settings)
    *
    */

    private static function admin_layout(){

        if(!self::$admin_ini['layout']) return;

        $settings = self::$admin_ini['layout'];


        if(isset($settings['show_admin_bar']) && !$settings['show_admin_bar']) add_filter('show_admin_bar','__return_false');

        if(!is_admin()) return;

        if($settings['disable_new_pages'])

            Admin::disable_new_pages(self::explode(",",$settings['disable_new_pages']));

        if($settings['remove_admin_bar_nodes'])

            Admin::remove_admin_bar_nodes(self::explode(",",$settings['remove_admin_bar_nodes']));

        if($settings['unset_meta_box']) {
            $metaboxes = [];
            foreach($settings['unset_meta_box'] as $metabox){
                 $metaboxes[] = self::explode(",",$metabox);
            }

            Admin::unset_meta_boxes($metaboxes);

        }

        if($settings['custom_footer']) Admin::custom_footer($settings['custom_footer']);

        if($settings['remove_editor_in_pages']) {

            if($settings['remove_editor_in_pages'] == '1'){
                Admin::remove_editor_in_pages();
            } else {
                Admin::remove_editor_in_pages(self::explode(",",$settings['remove_editor_in_pages']));
            }

        }


    }


    private static function mime_types(){

        if(!isset(self::$images_ini['MIME_types'])) return;

        $mime_types = self::$images_ini['MIME_types'];

        if($mime_types['svg']){

            Admin::allow_svg();
        }

    }


    private static function admin_editor(){



        if(!self::$admin_ini['editor']) return;

        $editor = self::$admin_ini['editor'];

        if(isset($editor['autop']) && !$editor['autop']) Admin::remove_autop();

        if(isset($editor['html_editor']) && !$editor['html_editor']) Admin::disable_html_editor();


        if($editor['toolbar']){

            $toolbar = [];
            $toolbar['paste_as_text'] = self::format_param($editor['paste_as_text']);
            $toolbar['toolbar'] = $editor['toolbar'];
            $toolbar['style_formats'] = [];

            for ($i = 1; $i <= 99; $i++) {

                if(!isset($editor['style_format_'.$i])) break;

                $style = [];

                foreach($editor['style_format_'.$i] as $key => $val){

                            if($key =="title"){

                                $val = __($val,'mido-admin');

                            } else {

                                $val = self::format_param($val);

                            }

                            $style[$key] = $val;

                        }

                $toolbar['style_formats'][] = $style;

            }

            Admin::custom_editor_toolbar($toolbar);

        } else {

            if($editor['paste_as_text']) Admin::paste_as_text();

        }


    }


    private static function admin_acf(){

        if(!self::$admin_ini['acf']) return;

        $acf = self::$admin_ini['acf'];

        if(isset($acf['set_featured_image_as'])) ACF::update_featured_image($acf['set_featured_image_as']);

        if(isset($acf['set_featured_image_as_gallery'])) ACF::update_featured_image_from_gallery($acf['set_featured_image_as_gallery']);

        if(isset($acf['meta'])){
            foreach($acf['meta'] as $meta => $val){
                ACF::update_post($meta,$val);
            }
        }

        if(isset($acf['toolbar'])){
            foreach($acf['toolbar'] as $toolbar => $buttons){
                $acf['toolbar'][$toolbar] = self::explode(",",$buttons);
            }
            ACF::set_toolbars($acf['toolbar']);
        }

        if(isset($acf['google_api_key'])){

            ACF::update_maps_api_key($acf['google_api_key']);

        }
    }





    /********************************************
    *                                           *
    *                  HELPERS                  *
    *                                           *
    *********************************************/


    /*
    *
    * Domain labels
    *
    */


    private static function format_label($label){

        if($label){

            $label = __($label, 'mido-admin');

        } else {

            $label = '';
        }

        return $label;

    }


    /*
    *
    * Format .ini to php
    *
    */

    private static function format_param($param){

        if(!isset($param)) return false;

        switch ($param){
            case '':
            case ' ':
            case 'false':
                return false;
                break;
            case '1':
            case 'true':
                return true;
                break;
            default:
                return $param;
                break;
        }

    }


    private static function explode($needle,$string){
        $string = preg_replace('/\s+/', '', $string);
        $string = explode($needle,$string);
        return $string;
    }

    public static function get_val($ini_name, $field = false, $subfield = false){

        $ini = null;
        switch($ini_name){
            case 'admin':
                $ini = self::$admin_ini;
                break;
        }

        if(!$ini){
            $ini = parse_ini_file(INIT_DIR."/".$ini_name.".ini", true);
        }

        $val = $ini;

        if($field){

            if($subfield){

                $val = $ini[$field][$subfield];

            } else {

                $val = $ini[$field];

            }
        }

        return $val;

    }




}

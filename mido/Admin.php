<?php
namespace Mido;
/**
 * Mido Admin class
 *
 * @package Mido
 */

class Admin {


    /*
    *
    * Default admin modifications
    *
    */

    public static function default_modifications(){

        /**
         * WP logo link login 
         */
        add_filter('login_headerurl', function(){
                return home_url();
        });

        /**
         * changing the alt text on the logo to show the site name
         */

        add_filter('login_headertitle', function(){
                return get_option('blogname');
        });


    }

    /*
    *
    * Custom login CSS
    *
    */


    public static function add_login_css($script){

        add_action('login_enqueue_scripts', function() use ($script){

            $script = get_stylesheet_directory_uri().'/'.$script;

            wp_enqueue_style('custom_login_css', $script, false);

        }, 10);

    }


    /*
    *
    * Custom TinyMCE style
    *
    */

    public static function add_editor_css($script) {

        add_action('admin_init', function() use ($script){

            $script = get_stylesheet_directory_uri().'/'.$script;

            add_editor_style($script);
        });
    }


    /*
    *
    * Custom admin style
    *
    */

    public static function add_admin_css($script) {

        add_action('admin_enqueue_scripts', function() use ($script){

            $script = get_stylesheet_directory_uri().'/'.$script;

            wp_enqueue_style('admin-styles', $script);

        });
    }

    /*
    *
    * Custom admin JS
    *
    */

    public static function add_admin_js($script) {

        add_action('admin_enqueue_scripts', function() use ($script){

            $script = get_stylesheet_directory_uri().'/'.$script;

            wp_enqueue_script('admin-custom-js', $script);

        });
    }


    /*
    *
    *   Remove metaboxes dashboard widgets (used in bones theme)
    *
    */

    public static function unset_meta_boxes($metaboxes){

        global $wp_meta_boxes;

        foreach((array) $metaboxes as $metabox){

                unset($wp_meta_boxes[$metabox[0]][$metabox[1]][$metabox[2]][$metabox[3]]);
        }

    }



    /*
    *
    *   Remove admin bar nodes
    *
    */

    public static function remove_admin_bar_nodes($nodes){

    	add_action('admin_bar_menu',function($wp_admin_bar)use($nodes){

	    	   foreach ((array) $nodes as $node){
                            $wp_admin_bar->remove_node($node);
	                }

    	},999);

          
    }


    /*
    *
    *   Set custom dashboard columns
    *
    */

    public static function dashboard_columns($num_columns){

        add_action('admin_head-index.php', function() use ($num_columns){

                add_screen_option(
                    'layout_columns', array(
                        'default' => $num_columns
                        )
                    );


        });


        add_filter('screen_layout_columns', function() use ($num_columns){

               $columns['dashboard'] = $num_columns;
               return $columns;


        });

    }


    /*
    *
    * Disable new pages to non admin 
    *
    */


    public static  function disable_new_pages($toRoles = array()) {

        if(current_user_can('administrator')) return false;

        $user = wp_get_current_user();

        if ( !in_array( $user->roles, (array) $toRoles ) ) {
            return false;
        }

        //disable new pages
        add_action('admin_menu', function(){
                global $submenu;
                unset($submenu['edit.php?post_type=page'][10]);
                if (isset($_GET['post_type']) && $_GET['post_type'] == 'page') {
                    echo '<style type="text/css">
                .page-title-action:not(.page-title-action-avoidremove) { display:none; }
                </style>';
                }
        });

        //hide page buttons
        add_action('admin_head', function(){
                global $current_screen;
                if ($current_screen->id == 'page') {
                    echo '<style>.page-title-action:not(.page-title-action-avoidremove){display: none;}</style>';
                }        
        });

    }


    /*
    *
    * Remove suport for post types
    *
    */


    public static function remove_support($post_type,$support){

        add_action('admin_init', function() use ($post_type,$support){

           remove_post_type_support($post_type, $support);

        });

    }


    /*
    *
    * Remove editor suport for pages except templates
    *
    */

    public static function remove_editor_in_pages($onTemplates = false) {

        add_action( 'admin_init', function() use ($onTemplates){


            // Get the post ID on edit post with filter_input super global inspection.
            $current_post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
            // Get the post ID on update post with filter_input super global inspection.
            $update_post_id = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT );

            // Check to see if the post ID is set, else return.
            if ( isset( $current_post_id ) ) {
               $post_id = absint( $current_post_id );
            } else if ( isset( $update_post_id ) ) {
               $post_id = absint( $update_post_id );
            } else {
               $post_id = false;
            }

            if ( !isset( $post_id ) || !$post_id ) {

            	if(!$onTemplates ){

            		remove_post_type_support( 'page', 'editor' );

            	}
            }
            
        
            // Don't do anything unless there is a post_id.
            if ( isset( $post_id ) ) {
               // Get the template of the current post.
               $template_file = get_post_meta( $post_id, '_wp_page_template', true );

               // Example of removing page editor for page-your-template.php template.
               if (!$onTemplates || in_array($template_file, (array) $onTemplates) ) {

               	   remove_post_type_support( 'page', 'editor' );
               }
            }

        } );

    }


    /*
    *
    * Disable HTML editor
    *
    */

    public static function disable_html_editor() {
        
        add_action('admin_head', function(){
            global $pagenow;
            if (!( 'post.php' == $pagenow || 'post-new.php' == $pagenow )) {
                return;
            }
            echo '<style>.wp-editor-tabs { display: none; } </style>';
        });
    }



    /*
    *
    * Allow SVG Mime
    *
    */

    public static function allow_svg(){

        add_filter('upload_mimes', function(){

            $mimes['svg'] = 'image/svg+xml';
            return $mimes;

        });

    }


    /*
    *
    * Custom footer
    *
    */

    public static function custom_footer($custom_footer){

	        add_action('admin_footer_text', function($footer) use ($custom_footer){

	             return '<style>#footer-left{display:none !important}</style>'.__($custom_footer);

			},99999999);

    }


    /*
    *
    * Admin menu
    *
    */
    
    public static function add_menu_page(){

        call_user_func_array(array('\Mido\AdminMenu','add_menu_page'), func_get_args());


    }


    /*
    *
    * Admin submenu
    *
    */

    public static function add_submenu_page(){

        call_user_func_array(array('\Mido\AdminMenu','add_submenu_page'), func_get_args());


    }



    /*
    *
    * Remove autop
    *
    */

    public static function remove_autop(){

        add_action('init', function(){
           // remove_filter( 'the_content', 'wpautop' );
        });

        /*add_action('acf/init', function(){

            remove_filter('acf_the_content', 'wpautop' );
            add_filter('acf_the_content', function ($pee) { return wpautop($pee, false); } );

        });*/

    }


    /*
    *
    * Editor paste as text (avoid strange paste)
    *
    */

    public static function paste_as_text() {

    	add_filter('tiny_mce_before_init',function($init){

    		    $init['paste_as_text'] = true;
   				return $init;

    	});
	}	


	/*
    *
    * Custom editor toolbar
    *
    */

    public static function custom_editor_toolbar($toolbar){

        add_action('init', function() use ($toolbar){

            if (!current_user_can('edit_posts') && !current_user_can('edit_pages') || get_user_option('rich_editing') != 'true')
                    return;

                add_filter('tiny_mce_before_init', function($init) use ($toolbar){

                    $init['paste_as_text'] = $toolbar['paste_as_text'];

                    unset($init["toolbar2"]);
                    
                    $init["toolbar1"] = $toolbar['toolbar'];
                    
                    $init['style_formats'] = json_encode($toolbar['style_formats']);
                    return $init;

                });

        });

    }




}
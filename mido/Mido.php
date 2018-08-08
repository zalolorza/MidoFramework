<?php

namespace Mido;
/**
 * Mido Class
 *
 * @package Mido
 */

final class Mido extends \Timber\Timber
{


    private static $_instance = null;

    public static $twig_templates = [];


    /*
    *
    * Run Mido Framework
    *
    */


    public static function run(){

        
        
        /*if(!is_admin()){
            ob_start();
            $cache_folder = dirname(__DIR__).'/cache';
            $actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $file = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $actual_link);
            $file = mb_ereg_replace("([\.]{2,})", '', $file).'.twig';
            $fileUrl = $cache_folder.'/'.$file;
            
            if(file_exists($fileUrl)){
                //readfile($fileUrl);
                Mido::render($fileUrl);
                die();
            };

            add_action('after_render', function() use ($fileUrl){
            
                $content = ob_get_contents();
                $myfile = fopen($fileUrl, "w");
                fwrite($myfile, $content);
                fclose($myfile);
            
            });
        };*/
       
      

        /*
        *
        * Avoid multiple instances for Mido without breaking inheritance with Timber
        *
        */

        if (self::$_instance) throw new \Exception('Mido already running');
        self::$_instance = new self();

        $_this = self::$_instance;


        /*
         *
         * Switch theme hook - Build Initial Theme Structure
         *
         */

        Hooks::add_action("after_switch_theme",array('\Mido\ThemeBuilder','buildTheme'));



        /*
        *
        * Start Router
        *
        */
        Router::getInstance();

        /*
        *
        * Class names in theme
        *
        */
        //self::set_class_alias();


        
        $_this->bootstrap();




    }


    /*
    *
    * Bootstrap
    *
    */

    private function bootstrap(){
    
       
        define( 'THEME_DIR',  get_stylesheet_directory());
        define( 'THEME_URI',  get_stylesheet_directory_uri());
        define( 'BOOTSTRAP_DIR', rsearch('bootstrap.php'));
        
        //Bootstrap Theme
        
        include_once BOOTSTRAP_DIR.'/bootstrap.php';

        \Bootstrap::getInstance();

        self::define_constants();

        if(MANAGERS_DIR){
            ThemeBuilder::autoload_directory_php(array(
                MANAGERS_DIR
            ));
        }

        foreach(get_declared_classes() as $class){

                if(is_subclass_of($class,"MidoManager")){
                    new $class();
                }
            }


        //Bootstrap Admin
        if(is_admin()){

            include_once BOOTSTRAP_DIR.'/bootstrapAdmin.php';
            \BootstrapAdmin::getInstance();

            include_once(plugin_dir_path( __DIR__ ).'fancy-admin-ui/fancy-admin-ui.php');


        }

        //Twig views
        if(!is_admin()){

            ThemeBuilder::autoload_directories_twig(VIEWS_DIR);

        }


        new Scripts();
        ThemeBuilder::initTheme();
        RestAPI::init();
        //\Mido\Mail::init();


    }

    public static function filterPagesByTemplate (){
      if(is_admin()){
        new FilterPagesByTemplate();
      }
    }

    private static function define_constants(){

        if(!defined('INIT_DIR')) define('INIT_DIR', BOOTSTRAP_DIR.'/init');
        if(!defined('CONTROLLERS_DIR')) define('CONTROLLERS_DIR', BOOTSTRAP_DIR.'/controllers');
        if(!defined('VIEWS_DIR')) define('VIEWS_DIR', 'views');
        if(!defined('MANAGERS_DIR')) define('MANAGERS_DIR', false);
    }





    /*
    *
    * Set Classnames
    *
    */

    public static function set_class_alias(){


            /*
            *
            * Mido
            *
            */
            class_alias(__NAMESPACE__.'\Mido','Mido');
            class_alias(__NAMESPACE__.'\Router','MidoRouter');
            class_alias(__NAMESPACE__.'\Post','MidoPost');
            class_alias(__NAMESPACE__.'\Pages','MidoPages');
            class_alias(__NAMESPACE__.'\Bootstrap','MidoBootstrap');
            class_alias(__NAMESPACE__.'\Controller','MidoController');
            class_alias(__NAMESPACE__.'\ACF','MidoACF');
            class_alias(__NAMESPACE__.'\Admin','MidoAdmin');
            class_alias(__NAMESPACE__.'\Manager','MidoManager');
            class_alias(__NAMESPACE__.'\Scripts','MidoScripts');
            class_alias(__NAMESPACE__.'\CustomPostTypes','MidoCPT');
           // class_alias(__NAMESPACE__.'\Mail','MidoMail');


            /*
            *
            * Timber
            *
            */
            class_alias('\Timber\Archives','MidoArchives');
            class_alias('\Timber\Comment','MidoComment');
            class_alias('\Timber\Image','MidoImage');
            class_alias('\Timber\Menu','MidoMenu');
            class_alias('\Timber\MenuItem','MidoMenuItem');
            //class_alias('\Timber\Post','MidoPost');
            class_alias('\Timber\PostPreview','MidoPostPreview');
            class_alias('\Timber\PostQuery','MidoPostQuery');
            class_alias('\Timber\Site','MidoSite');
            class_alias('\Timber\Term','MidoTerm');
            class_alias('\Timber\Theme','MidoTheme');
            class_alias('\Timber\User','MidoUser');
            class_alias('\Timber\Helper','MidoHelper');
            class_alias('\Timber\ImageHelper','MidoImageHelper');
            class_alias('\Timber\TextHelper','MidoTextHelper');
            //class_alias('\Timber\URLHelper','MidoURLHelper');
            class_alias('\Timber\FunctionWrapper','MidoWrapper');

    }





    /*
    *
    * Get all posts
    *
    */


    public static function get_all_posts($post_type = 'post'){


        $posts = Mido::get_posts(array('post_type' => $post_type, 'posts_per_page' => -1));

       return $posts;


    }



    public static function get_page_template(){

        global $post;

        $template = get_post_meta($post->ID,'_wp_page_template');

        if(isset($template[0])){
            $page_template = $template[0];
        } else {
            $page_template = false;
        }

        return $page_template;
    }


}

<?php

namespace Mido;
/**
 * Building Mido 
 *
 * @package Mido
 */

final class ThemeBuilder
{

    /*
    *
    *  Init theme
    *
    */


    public static function initTheme(){

        ThemeInitializers::initTheme();
    }

    /*
    *
    *  Build theme
    *
    */


    public static function buildTheme(){


            /*
            *
            *   Flush rewrite rules
            *
            */


            flush_rewrite_rules();

            /*
            *
            *   Copy initial theme
            *
            */

            //self::copy_directory_files(self::get_framework_directory()."/initial-template", get_stylesheet_directory());


            /*
            *
            * Create pages
            *
            */

            //self::create_pages();

            /*
            *
            *   Write functions.php
            *
            */

            //self::write_functions_php();
            
    }


    /*
    *
    *  Write functions.php 
    *
    */

    private function write_functions_php(){

        $file = get_stylesheet_directory().'/functions.php';
            $content = substr(file_get_contents($file),5);
            $comment = '/**'.PHP_EOL.'* Running Mido'.PHP_EOL.'*'.PHP_EOL.'*/'.PHP_EOL.PHP_EOL;
            $function = "if (class_exists('Mido'))".PHP_EOL.PHP_EOL."{Mido::run();".PHP_EOL."return;".PHP_EOL."}".PHP_EOL.PHP_EOL;
            $newContent = $comment.PHP_EOL.$function;
            
            if (!strpos($content, $newContent)) {
                file_put_contents($file,'<?php '.PHP_EOL.
                        $newContent.PHP_EOL.
                        $content);
            } 

    }


    /*
    *
    *  Create theme pages
    *
    */

    private function create_pages(){


        if(!file_exists(INIT_DIR."/theme_builder.ini")) return false;

        //get theme_builder.ini ---> rsearch() // for pages
        // get pages.ini -----> rsearch(); //for templates

            if($pages['pages']){

                add_action('after_switch_theme',function() use($pages){

                    foreach ( $pages['pages'] as $title => $params){

                        if(!is_array($params)){
                            $params['template'] = $params;
                        }

                        if($pages['templates'][$params['template']]){
                            $params['template'] = $pages['templates'][$params['template']];
                        }

                       if(!$params['title']) $params['title'] = $title;

                       Pages::add_page($params);

                  }

                });
            
            }

    }


    /*
    *
    * Copy files and directories from a Source to a Destiny
    *
    */


    private function copy_directory_files($src,$dst) { 
        if(!$dst) return;
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    self::copy_directory_files($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    if (!file_exists($dst . '/' . $file)) {
                       copy($src . '/' . $file,$dst . '/' . $file);
                   }
                     
                } 
            } 
        } 
        closedir($dir); 
    } 


    /*
    *
    *  Get directories 
    *
    */
    

    private function get_framework_directory(){
         return dirname( dirname(__FILE__) );
    }

    private function get_wp_root(){

            $base = dirname(__FILE__);
            $path = false;

            if (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php"))
            {
                $path = dirname(dirname(dirname($base)));
            }
            else
            if (@file_exists(dirname(dirname(dirname(dirname($base))))."/wp-config.php"))
            {
                $path = dirname(dirname(dirname(dirname($base))));
            }
            else
            $path = false;

            if ($path != false)
            {
                $path = str_replace("\\", "/", $path);
            }
            return $path;
    }



    /*
    *
    * Autoload PHP scripts
    *
    */

    public static function autoload_directory_php($directories){



        if(!is_array($directories)){
            $directories = array($directories);
        }


        foreach($directories as $directory){

            foreach (glob($directory."/*.php") as $filename)
                            {
                                include_once $filename;
                            }

            
        }

        
        
        
    }

    /*
    *
    * Autoload Twig folders & subfolders
    *
    */
  
     public static function autoload_directories_twig($directories){

      
        if(!is_array(Mido::$dirname)){
                    Mido::$dirname= array('views');
         } 
        
        if(!is_array($directories)){
            $directories = array($directories);
        }


        
        foreach($directories as $directory){

            $abs_directory = THEME_DIR.'/'.$directory;

            if(!in_array($directory, Mido::$dirname)){
                Mido::$dirname[]=$directory;
            }
           
            $dir = opendir($abs_directory); 
            while(false !== ( $file = readdir($dir)) ) { 
                if (( $file != '.' ) && ( $file != '..' )) { 

                    if ( is_dir($abs_directory . '/' . $file) ) { 

                        $dirname = $directory.'/'.$file;

                        if(!in_array($dirname, Mido::$dirname)){
                                 Mido::$dirname[]=$dirname;
                        }
                        
                        self::autoload_directories_twig($dirname); 
                    } else {

                       if(pathinfo($file, PATHINFO_EXTENSION) == 'twig'){
                                Mido::$twig_templates[] = $file;
                        }
                    }
                } 
            } 
            closedir($dir); 

        }

        

    }

   
   
}



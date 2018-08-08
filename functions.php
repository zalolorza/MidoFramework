<?php

/*
*
* Check if Twig file exists
*
*/

function twig_exists($template){

        if(!$template) return false;

        if(in_array($template, \Mido::$twig_templates)) return true;

        
        return rsearch($template,THEME_DIR.'/'.VIEWS_DIR);

    }

/*
*
* Recursive file search
*
*/

function rsearch($pattern, $dir = THEME_DIR) {
               
                $iti = new \RecursiveDirectoryIterator($dir);
                foreach(new \RecursiveIteratorIterator($iti) as $file){
                     if(strpos($file , $pattern) !== false){
                        return $file->getPath();
                     }
                }
                return false;
            }

/*
*
* Custom Dump
*
*/

function dump($var, $die = true){


    echo '<pre>';
        highlight_string("<?php\n\$ =\n" . var_export($var, true) . ";\n?>");
    echo '</pre>';

    if($die){

        die();
        
    }

}



<?php

namespace Mido;

class EnqueueScripts {
	
	use Singleton;

	protected $scripts=[];
	protected $scriptsMobile=[];

	private static $_instance = [];
	
	private function __construct () { 
       
    }

	public static function getInstance()
    {
        $class= get_called_class();
        if (!self::$_instance[$class]) {
            self::$_instance[$class] = new $class;
            self::$_instance[$class]->class=$class;

            Hooks::add_action('wp_enqueue_scripts',array($class,'enqueue'));
        }

        return self::$_instance[$class];
    }

	public static function enqueue(){
		 if (!is_admin()) {
				$_this = self::getInstance();
				
				foreach($_this->scripts as $script){
					if(!Enviroment::isMobile() && $script['onlyMobile']) continue;
					switch ($script[type]){
							case 'js':
								wp_enqueue_script($script[name],$script[file],$script[dependencies],$script[version],$script[addToFooter]);
								break;
							case 'style':
								wp_enqueue_style($script[name],$script[file],$script[dependencies],$script[version],$script[media]);
								break;
						}
				}
		}

	}

	public function add($name,$file,$dependencies = array(),$version = null, $lastArgument = false, $isMobile = false){

		$_this = self::getInstance();

		if(!$lastArgument){
			switch ($_this->class){
				case __NAMESPACE__.'\Scripts':
					$lastArgument = false;
					$key = addToFooter;
					$directory='js';
					$type = 'js';
					break;
				case __NAMESPACE__.'\Styles':
					$lastArgument = 'all';
					$key = media;
					$directory='css';
					$type = 'style';
					break;
			}
		}

		$file = get_stylesheet_directory().'/'.$file;

        $script = array(
			name => $name,
			file => $file,
			dependencies => $dependencies,
			version => $version,
			$key => $addToFooter,
			onlyMobile => $isMobile,
			type => $type
			);

        $_this->scripts[] = $script;

		return $_this;
	}

	public function addMobile($name,$file,$dependencies = array(),$version = null, $lastArgument = false){
		self::add($name,$file,$dependencies,$version,$lastArgument,true);
		return $_this;
	}

	public function getScripts(){
		$_this = self::getInstance();
		return $_this->scripts;
	}

}
<?php

namespace Mido;

trait Singleton {

	static $instances = array();
    
    private function __construct () { 
       
    }

    private final function __clone() { 

    }


    public static function getInstance()
    {
        $calledClass = get_called_class();

        if (!isset(self::$instances[$calledClass]))
        {
            self::$instances[$calledClass] = new $calledClass();
      
        }



        return self::$instances[$calledClass];
    }

    

}
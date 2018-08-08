<?php
namespace Mido;
/**
 * Mido Manager class
 *
 * @package Mido
 */

class Manager {

	public function __construct(){

        $this->_init();
        $this->add_actions_and_filters();
     
    }

    public function _init(){

    }


    private function add_actions_and_filters(){

        $reflectionClass = new \ReflectionClass(get_called_class());
       
        $methods = $reflectionClass->getMethods();

        $magicMethods = array('__construct', '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup', '__toString', '__invoke', '__set_state', '__clone', '__debugInfo', '__activate');

     
        foreach($methods as $method){

            $method = $method->name;
            
            if(substr($method,0,7) == 'action_'){

                $action = substr($method,7);

                $index = explode("_", $action);
                $index = end($index);

                if(is_numeric($index)){
                    $action = str_replace("_".$index,"",$action);
                } else {
                    $index = 999;
                }

                add_action($action,array($this,$method),$index);

            } else if(substr($method,0,7) == 'filter_'){

                    $filter = substr($method,7);

                add_filter($filter,array($this,$method));

            }
        }

    }

}
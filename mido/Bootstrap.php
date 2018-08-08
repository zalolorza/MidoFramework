<?php
namespace Mido;
/**
 * Mido Botstrap class
 *
 * @package Mido
 */

class Bootstrap {

	/*
    *
    * Construct
    *
    */

    use Singleton;

    private function __construct(){

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




    /*
    *
    * Users Roles
    *
    */

     public function add_user_roles($roles,$singlerole=null){
        if($singlerole){
            UserRoles::add_role($roles,$singlerole);
        } else {
            UserRoles::add_roles($roles);
        }
    }


    public function remove_user_roles($roles){
        UserRoles::remove_role($roles);  
    }


    /*
    *
    * Add / remove user capabilites
    *
    */


    public function add_user_capabilities($role, $capabilities){

        UserRoles::add_capabilities($role, $capabilities);

    }

    public function remove_user_capabilities($role, $capabilities){

        UserRoles::add_capabilities($role, $capabilities);

    }


    

}
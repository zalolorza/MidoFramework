<?php

namespace Mido;

trait Invoke {

    private static $methods;
    private static $magicMethods;

    private function invoke($methodName,$add_pattern_before = false,$add_pattern_after=false){

        self::get_instance_invoke();


        $methodName = str_replace(__CLASS__."::","",$methodName);


        if($add_pattern_before) $methodName = $add_pattern_before.$methodName;
        if($add_pattern_after)  $methodName = $methodName.$add_pattern_after;



       foreach(self::$methods as $method) {
            $method = $method->name;

            if($method == $methodName  || in_array($method, self::$magicMethods)) continue;

            if (substr($method, 0, strlen($methodName)) === $methodName) {
                $this->$method();
            } 
        };
       
    }

    private function get_instance_invoke(){

        if(!isset(self::$methods)){
                $reflectionClass = new \ReflectionClass($this);
                self::$methods = $reflectionClass->getMethods();
                self::$magicMethods = array(__construct, __destruct, __call, __callStatic, __get, __set, __isset, __unset, __sleep, __wakeup, __toString, __invoke, __set_state, __clone, __debugInfo, __activate);
        }

    }

    private function add_actions_and_filters(){

        $this->get_instance_invoke();

        dump($this->$methods,false);

        foreach(self::$methods as $method){

            $method = $method->name;
            
            if(substr($method,0,7) == 'action_'){

                $action = substr($method,7);

                $index = end(explode("_", $action));

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
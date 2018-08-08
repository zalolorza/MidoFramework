<?php

namespace Mido;

final class Hooks {

    use Singleton;

    protected $actions;
    protected $filters;
    private static $_isHooked = false;

    public function __construct() {
        $this->actions = array();
        $this->filters = array();
    }

    public function enqueue_action( $hook, $function, $args = null) {
        $instance = self::getInstance();
        $instance->actions = $instance->add($instance->actions, $hook, $function, $args);
    }

    public function enqueue_filter( $hook, $function, $args = null) {
        $instance = self::getInstance();
        $instance->filters = $instance->add($instance->filters, $hook, $function, $args);
    }

    public static function add_action( $hook, $function, $args = null) {
        $hookArray = array(
            'hook'      => $hook,
            'function' => $function,
            'args' => $args
            );

        add_action($hook,  self::getFunction($hookArray));


    }

    public static function add_filter( $hook, $function, $args = null) {
        $hookArray = array(
            'hook'      => $hook,
            'function' => $function,
            'args' => $args
            );

        add_filter($hook,  self::getFunction($hookArray));
    }

    public function do_action($name) {
        do_action($name);
    }

    private function add( $hooks, $hook, $function, $args = null) {
       
        $hooks[] = array(
            'hook'      => $hook,
            'function' => $function,
            'args' => $args
            );

    }

    public function run_queue() {

        $instance = self::getInstance();

        foreach ( $instance->filters as $hook ) {
            add_filter( $hook['hook'],  self::getFunction($hook));
        }


        foreach ( $instance->actions as $hook ) {
            add_action( $hook['hook'], self::getFunction($hook) );

        }

        $instance->filters = array();
        $instance->actions = array();

    }

    private static function getFunction (array $hook){

        $function = $hook['function'];

        

        if(is_array($hook['args'])){
            if(!array_key_exists(0,$hook['args'])){
                $args = array($hook['args']);
            } else {
                $args = $hook['args'];
            }
        } else {
            $args = array($hook['args']);
        }
        
        $callFunction = function() use ($function, $args) {
                call_user_func_array($function, $args);
            };
        return $callFunction;
    }

}

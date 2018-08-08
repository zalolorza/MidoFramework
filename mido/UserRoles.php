<?php

namespace Mido;
 
/**
 * Mido Users class
 *
 * @package Mido
 */

final class UserRoles {

    use Singleton;
    
    public function add_role($name,$role=null){

        if(!$role) $role = sanitize_title($name);

        Hooks::add_action('admin_init','add_role',array($role,$name));

    }

    public function add_roles($roles){

        if(!is_array($roles)){
            $roles = array($roles);
        } 
    
        foreach ($roles as $role){
            self::add_role($role);
        }

    }

    public function remove_role($role){
        Hooks::add_action('admin_init','remove_role',$role);

    }

    public function remove_roles($roles){
        if(!is_array($roles)){
            $roles = array($roles);
        } 
        foreach ($roles as $role){
            self::remove_role($role);
        }

    }

    public function add_capabilities($role, $capabilities){

        $user = get_role($role);

            foreach ($capabilities as $cap) {
                    $user ->add_cap($cap);
            }

    }

    public function remove_capabilities($role, $capabilities){

        $user = get_role($role);



            foreach ($capabilities as $cap) {
                    $user->remove_cap($cap);
            }

    }

}
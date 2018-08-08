<?php
namespace Mido;
/**
 * Mido Admin page class
 *
 * @package Mido
 */

class AdminPage {

    function init(){




    }

    /**
     * Add menu.
     * @return void
     */
    function settings_page() {
        add_menu_page('Mido Framework settings', 'MidoFramework', 'administrator', __FILE__, 'mido-framework', "dashicons-media-code");

    }



}

//add_action('admin_menu', array('\Mido\AdminPage','settings_page'));

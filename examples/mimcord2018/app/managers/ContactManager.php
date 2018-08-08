<?php

class ContactManager {

	public static function hydrate(&$context){

		self::hydrate_form($context);

		return $context;

	}

	public static function hydrate_form(&$context){

		$context->form = array(

		                'method' => 'POST',
		                'inputs' => get_field('form_fields'),
		                'action' => ContactManager::get_form_action()

		                  );

		return $context->form;
		

	}

	public static function get_form_action(){

		$route = 'contact/send';

		return get_home_url().'/'.$route;

	}

}
<?php

class WebManager extends MidoManager {


	function _init(){

		
		
	}

	function filter_render($context){

		if(!isset($context['footerFormat'])){
			$context['footerFormat'] = 'diagonal';
		}

		if($context['featured']){
			$context['featured'] = PostsManager::get_featured($context['featured']);
			$context['footerFormat'] = 'simple';
		} else {
			$context['featured'] = false;
		}

		

		$context['current_year'] = date("Y");
	
		$context['lang'] = LangManager::get_other();
		
		$context['options']  = get_fields('options');

		
		
		return $context;

	}
 
	


}

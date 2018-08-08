<?php


class PostController extends MidoController {

	function _init(){
		
	}

	function index(){


		$this->render();
		
	}

	function single(){

		
		$this->featured = true;
		$this->footerFormat = 'diagonal-only-mobile';
		$this->modules = ModulesManager::get();
		
		$this->render();
		
	}

	function category(){

		$this->render();

	}


}

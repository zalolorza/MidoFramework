<?php

class ExceptionController extends MidoController {

	function _init(){


	}

	function error404(){

		$this->error = array(
				"title" => __('Error 404', 'dws'),
				"text" => __('Sorry about that...','dws')
		);

		$this->header = false;

		$this->render('404.twig');

	}

}

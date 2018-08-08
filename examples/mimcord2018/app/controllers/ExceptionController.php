<?php

class ExceptionController extends MidoController {

	function _init(){


	}

	function error404(){

		
		$this->error = array(
				title => __('Error 404','mimcord'),
				text => __('No hem trobat el que buscaves. Pots seguir navegant pel nostre web','mimcord').
						' <a href="'.get_home_url().'">'.
						__('aquí','mimcord').
						'<a>'
		);

		WebManager::set_header(array(
			'background' => 'gray',
			'tagline' => false,
			'title' => $this->error['title'],
			'parallax' => false
		));


		$this->render('404.twig');

	}

}

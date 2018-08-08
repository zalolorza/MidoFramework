<?php


class VideoController extends MidoController {

	function _init(){
			
	}

	function single(){
		
		$this->featured = true;
		
		//$this->footerFormat = 'simple';
		//$this->headerClass = 'headerFullHeight';
		$this->render();
		
	}

}

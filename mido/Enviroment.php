<?php

namespace Mido;

final class Enviroment {
	
	use Singleton;

	private function __construct(){
		
	}

	public function isMobile(){
		return wp_is_mobile();
	}

}
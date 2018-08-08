<?php

class AjaxController extends MidoController  {

	function _init(){

		global $sitepress;
		$sitepress->switch_lang($_GET['lan']);

	}

	function menuCart(){

				$response = array(
					'cart'=> MenusManager::get_cart_nav()
				);
				echo json_encode($response);

  }

}

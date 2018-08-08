<?php

class AjaxController extends MidoController  {

	function _init(){

		global $sitepress;
		if(isset($_GET['lan']) && $_GET['lan'] != 'ICL_LANGUAGE_CODE'){
			$sitepress->switch_lang($_GET['lan']);
		}

	}

}

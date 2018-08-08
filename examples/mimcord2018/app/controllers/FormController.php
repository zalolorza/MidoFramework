<?php

class FormController extends MidoController  {

	function _init(){

		add_filter( 'wp_mail_content_type',function(){
			return "text/html";
		} );

	}


	function send(){

		$data = $_POST;

		$footer = preg_replace("/<p[^>]*?>/", "", get_field('map_text',$data['id']));
		$data['footer'] = str_replace("</p>", "<br />", $footer);

		$inputs = get_field('form_fields',$data['id']);
		$data_admin = array('inputs' => $inputs, 'data' => $data);
		$admin_mail = get_field('contact_admin_mail',$data['id']);
		$headers = array('Content-Type: text/html; charset=UTF-8');

		if(!isset($admin_mail) || $admin_mail == null || $admin_mail == ''){
			$admin_mail = 'mimcord@mimcord.com';
		}
	
		$content = Mido::compile('mail-contact-admin.twig',$data_admin);
	
		$MAIL_ADMIN = wp_mail( $admin_mail , 'Nou contacte desde el web', $content, $headers);

		wp_redirect(get_permalink($data['id']));

		if($MAIL_ADMIN){

			echo true;

		}



	}

}

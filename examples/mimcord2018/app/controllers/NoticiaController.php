<?php


class NoticiaController extends MidoController {

	function _init(){
			
	}

	function index(){

		WebManager::set_header(array(
			'title' => __('News'),
			'background' => 'blue',
			'parallax' => 'opacity'
		));

		$this->render();

	}

	function single(){

		ScriptsManager::add_script('news_view.js',0.1);

		WebManager::set_header(array(
			'parallax' => 'opacity',
			'add_line' => true
		));

		$this->archiveLink = get_post_type_archive_link( 'noticia' );
		$this->post->download = get_field('download');
		
		$this->render();

	}

}

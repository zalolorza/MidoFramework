<?php


class PagesController extends MidoController {

	function _init(){
			
	}

	function page(){

		$this->render();
		
	}


	function home(){

		//$this->featured = PostsManager::get_featured('middle');

		$this->modules = ModulesManager::get();

		$this->featured = 'footer_simple';
		
		$this->gallery = array();

	

		while( have_rows('gallery') ) : the_row();
				
			$img = get_sub_field('img');

			$this->gallery[] = array(
				'img' => $img['url'],
				'video_loop' => VideoManager::get_background(2),
				'vimeo' => VideoManager::get_video_field('video',2)
			);	
		
		endwhile;
		

		$this->render();

	}

	function about(){

		$this->modules = ModulesManager::get();

		$this->featured = true;

		$this->render();

	}

	function capabilities(){

		$this->featured = true;
		
		$this->render();

	}


	function contact(){

		$this->featured = true;

		ScriptsManager::add_google_maps();
		$this->render();

	}


	function page_modules(){

		$this->featured = true;

		$this->modules = ModulesManager::get();
		$this->render();

	}



}

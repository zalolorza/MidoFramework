<?php


class PagesController extends MidoController {

	function _init(){
		
	}

	function page(){


		$this->render();

	}

	function home(){
		
		ScriptsManager::add_script('home_view.js',0.1);

		$this->news = Mido::get_posts(array('post_type' => 'noticia', 'posts_per_page' => 6));
		$this->footerClass = 'footer-complete';
		
		WebManager::set_header(array(
			'format' => 'slider'
		));

		$this->render();

	}

	function privateArea(){
		
		$this->technical_sheets_groups = ProductsManager::get_technical_sheets();
		
		WebManager::set_header(array(
			'background' => 'gray',
			'tagline' => $this->page->content,
			'title' => __("Benvingut a l’àrea privada",'mimcord'),
			'parallax' => 'opacity'
		));

		LoginManager::render_private($this);
		
	}

	function aboutUs(){

		WebManager::set_header(array(
			'background' => 'thumbnail'
		));

		$milestones = $this->page->get_field('milestones');
		$i = 0;
		foreach($milestones as $key => &$milestone){
			$milestone['textLength'] = strlen ( $milestone['text'] );
			if($milestone['join_with_previous']){
				array_push($milestones[$i]['extra'],$milestone);
				unset($milestones[$key]);
			} else {
				$milestone['extra'] = array();
				$i = $key;
			}		
		}
		
		$this->page->milestones = $milestones;
		$this->page->img_3 = $this->page->get_field('img_3');


		ScriptsManager::add_script('about_view.js',2.0);

		$this->render();

	}

	function contact(){

		WebManager::set_header(array(
			'title' => __('Som Mimcord, estem aquí.','mimcord'),
			'parallax' => false
		));
		ScriptsManager::add_script('contact_form.js',1.0);
		ContactManager::hydrate_form($this);
		$this->render();

	}
	
	
	function legalNotes(){

		WebManager::set_header(array(
			'background' => 'gray',
			'tagline' => false,
			'parallax' => false
		));

	
		$this->render();

	}

}

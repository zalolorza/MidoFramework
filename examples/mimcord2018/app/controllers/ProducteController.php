<?php


class ProducteController extends MidoController {

	function _init(){
			
	}

	function index(){

		WebManager::set_header(array(
			'format' => 'overlap',
			'title' => false,
			'background' => 'blue',
			'parallax' => false
		));

		$this->categories = CategoriesManager::get_all();

	
		$this->render();

	}

	function single(){
		
		$this->render();

	}

	function category(){
	
		$this->category = CategoriesManager::get_current();
		$this->products = ProductsManager::get_from_category($this->category);

		ScriptsManager::add_script('product_category_view.js',0.1);
	
		WebManager::set_header(array(
			'add_line' =>  true,
			'title' => $this->category->title,
			'tagline' => $this->category->description,
			'parallax' => 'opacity'
		));

		
		
		$this->render('category_producte.twig');

	}

}

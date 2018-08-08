<?php

namespace Mido;

class Controller {

	/*
    *
    * Construct controller
    *
    */


	public function __construct($router){

		$this->router = $router;

		$this->hydrate($router);

		$this->do_action('controller', $this);

		$this->update_router();

		$this->_init();

		$this->run();

	}


	private function update_router(){

		if($this->router['method'] != $this->router['persistent_method']){
			$this->router['method_name'] = $this->router['method'];
		};

	}

	public function switch($controllerClass, $actionName){

		$controllerClass = Router::slugToController($controllerClass);
		$actionName = Router::slugToMethod($actionName);
		
		$file = CONTROLLERS_DIR.'/'.substr($controllerClass,1).'.php';
		include_once($file);

		$this->router['controller'] = $controllerClass;
		$this->router['controller_name'] = $controllerClass;
		$this->router['method'] = $actionName;
		$this->router['method_name'] = $actionName;

		new $controllerClass($this->router);
		exit();
		
	}


	private function run(){

		$this->update_router();

		$method = $this->router['method'];


		if($method) {
			$this-> $method();
		} else {
			$this -> render();
		}

	}



	/*
    *
    * Public init()
    *
    */

	public function _init(){

	}


	private function hydrate(){


		foreach(\Mido::get_context() as $name => $val){
			$this->$name = $val;
		};


		$router = $this->router;
		if(!isset($router['view_type'])) return;

		switch($router['view_type']):

			case 'single':

					//$this->post = \Mido::get_post();
					$this->post =  new \MidoPost();

					break;

			case 'archive':

					$this->posts = new \Timber\PostQuery();
					//$this->posts = \Mido::get_posts();

					break;

			case 'taxonomy':

					$this->posts = \Mido::get_posts();

					break;


			case 'page':

					$this->page = \Mido::get_post();

				     break;

			case 'author':


				       break;


			case 'search':


				       break;

			case 'error404':


				       break;

			default:


						break;


		endswitch;


	}

	/*
    *
    * Render view (string or template)
    *
    */

	public function render($template = null, $context = null, bool $expires=false, string $cache_mode="default"){



		$context = $this->get_context($context);

		$context = $this->apply_filter('render', $context);

		$template = $this->get_template($template);

		if($template['is_string']) {

			Mido::render_string($template['string'], $context);

		} else if($template['extension']=='twig'){

			Mido::render($template['file'], $context, $expires, $cache_mode);

		} else if($template['extension']=='php') {

			include_once($template['file']);

		}

		do_action('after_render');

	}


	/*
    *
    * Compile view (template or string)
    *
    */

	public function compile($template = null, $context = null,  bool $expires=false, string $cache_mode="default", bool $via_render=false){

		$context = $this->get_context($context);

		$context = $this->apply_filter('compile', $context);

		$template = $this->get_template($template);

		if($template['is_string']) {

			return Mido::compile_string($template['string'], $context);

		} else if($template['extension']=='twig'){

			return Mido::compile($template['file'], $context, $expires, $cache_mode, $via_render);

		} else {

			return false;

		}

	}

	/*
    *
    * get context from controller
    *
    */

    private function get_context($context = null){

    	if(!$context){

    		$this->update_router();

    		$context = get_object_vars($this);

    	}
    	return $context;
    }


	/*
    *
    * Get template
    *
    */

	private function get_template($template = null){

		if(!$template){
			$template = $this->get_default_template_twig();
		} else {
			$this->router['template_twig'] = $template;
		}

		$path_parts = pathinfo($template);
		$extension = $path_parts['extension'];

		$template_array = array();

		if(!$extension) {

			$template_array['is_string']= true;
			$template_array['string'] = $template;

		} else {
			$template_array['is_string'] = false;
			$template_array['file'] = $template;
			$template_array['extension'] = $extension;

		}

		return $template_array;
	}


	/*
    *
    * Get default template twig
    *
    */


	private function get_default_template_twig(){


		switch($this->router['view_type']):

			case 'single':

						/*
				        *
				        *	single_postType.twig > single.twig
				        *
				        */

				        $post_type = $this->router['post_type'];


						$template = $this->get_twig_hierarchy(array(
				        			'single_'.$post_type.'.twig',
				        			'single.twig'
				        	));

						break;

			case 'archive':

						/*
				        *
				        *	archive_postType.twig > index_postType.twig > archive.twig > index.twig
				        *
				        *
				        */
				        $post_type = $this->router['post_type'];

				        $template = $this->get_twig_hierarchy(array(
				        			'archive_'.$post_type.'.twig',
				        			'index_'.$post_type.'.twig',
				        			'archive.twig',
				        			'index.twig'
				        	));

						break;

			case 'taxonomy':


						/*
				        *
				        *	taxName.twig > tax_taxName.twig > archive_taxName.twig > index_taxName.twig > archive.twig > index.twig
				        *
				        */

				        $taxName = get_queried_object()->taxonomy;


				        $template = $this->get_twig_hierarchy(array(
				        			$taxName.'.twig',
				        			'tax_'.$taxName.'.twig',
				        			'archive_'.$taxName.'.twig',
				        			'index_'.$taxName.'.twig',
				        			'archive.twig',
				        			'index.twig'
				        	));


						break;


			case 'page':

					   $template = $this->get_twig_hierarchy('page.twig');

				       break;

			case 'author':

					   $template = $this->get_twig_hierarchy(array(
					   	 	'author.twig',
					   	 	'user.twig'
					   	 	));

				       break;


			case 'search':

					   $template = $this->get_twig_hierarchy('search.twig');

				       break;

			case 'error404':

					   $template = $this->get_twig_hierarchy('404.twig');

				       break;

			default:

						$template = $this->get_twig_hierarchy();

						break;


		endswitch;

		return $template;

	}


	/*
    *
    * Run hirerarchy array
    *
    */

	private function get_twig_hierarchy($templates = 'index.twig'){


		if(!is_array($templates)) $templates = array($templates);




		$templates = array_merge(array($this->router['template']),$templates);

		foreach($templates as $template){

			if(twig_exists($template)) {
				$this->router['template_twig'] = $template;
				return $template;
			}


			$last_template = $template;

		}

		$this->router['template_twig'] = $last_template;
		return $last_template;
	}


	/*
	*
	*
	*	Nice name
	*
	*/

	private function nice_name($name, $replace = "Controller"){

		switch($name){
			case 'controller';
				$name = $this->router['controller'];
				break;
			case 'controller_name';
				$name = $this->router['controller_name'];
				break;
		}

		return strtolower(str_replace($replace,"",$name));

	}

	/*
	*
	*
	*	Do action
	*
	*/

	private function do_action($actionName, $param){

		$controller = '_'.$this->nice_name('controller');
		$controller_name = '_'.$this->nice_name('controller_name');
		$method = '_'.$this->router['method'];
		$method_name = '_'.$this->router['method_name'];
		$persistent_method = '_'.$this->router['persistent_method'];


		do_action($actionName, $param);

		if($controller_name != $controller){

			do_action($actionName.$controller, $param);
			do_action($actionName.$controller.$method, $param);

		};


		do_action($actionName.$controller_name, $param);

		if($method != $method_name && $controller == $controller_name){

			do_action($actionName.$controller_name.$method, $param);

		}


		if($method_name != $persistent_method){

			do_action($actionName.$controller_name.$persistent_method, $param);

		}

		do_action($actionName.$controller_name.$method_name, $param);

	}

	/*
	*
	*
	*	Apply filter
	*
	*/

	private function apply_filter($filterName, $param){

		$controller = '_'.$this->nice_name('controller');
		$controller_name = '_'.$this->nice_name('controller_name');
		$method = '_'.$this->router['method'];
		$method_name = '_'.$this->router['method_name'];
		$persistent_method = '_'.$this->router['persistent_method'];


		$param = apply_filters($filterName, $param);

		if($controller_name != $controller){

			$param  = apply_filters($filterName.$controller, $param);
			$param  = apply_filters($filterName.$controller.$method, $param);

		};


		$param  = apply_filters($filterName.$controller_name, $param);

		if($method != $method_name && $controller == $controller_name){

			$param  = apply_filters($filterName.$controller_name.$method, $param);

		}


		if($method_name != $persistent_method){

			$param  = apply_filters($filterName.$controller_name.$persistent_method, $param);

		}

		$param  = apply_filters($filterName.$controller_name.$method_name, $param);



		return $param;

	}


	/*
	*
	*
	*	Exception
	*
	*/

	public function exception($exception = 404){

		if (is_string($exception)){

			$exceptionAction = $exception;

		} else {

			$exceptionAction = 'error'.$exception;

		}



		$file = CONTROLLERS_DIR.'/ExceptionController.php';

		include_once($file);

		$this->router['method'] = $exceptionAction;

		$controller = new \ExceptionController($this->router);

		die();


	}

}

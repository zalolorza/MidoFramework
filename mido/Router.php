<?php

namespace Mido;

class Router {

	use Singleton;

	private $action = false;

	private $custom_routes = []; 


    /*
    *
    * Init Router
    *
    */


	private function __construct(){


		/*
        *
        * Page templater init
        *
        */

        Hooks::add_action( 'plugins_loaded', array( __NAMESPACE__.'\PageTemplater', 'getInstance' ) );



        /*
        *
        * Page, Archives, Taxonomies...
        *
        */

        Hooks::add_filter('template_include',array($this, 'template_router_switcher'));


		/*
        *
        * Single Router
        *
        */

		
		Hooks::add_filter('single_template',array($this, 'single_router'));


		/*
        *
        * Taxonomies Router
        *
        */

		//Hooks::add_filter('taxonomy_template',array($this, 'taxonomy_router'));

		
	}


	public function template_router_switcher($template){


		global $post;
	
		
		if(function_exists('is_shop') && is_shop()){

			$this ->wc_router();

		} else if(is_tax() || is_category() || is_tag()){

			$this ->taxonomy_router();

		} else if(is_archive() || is_home()){

			$this ->archive_router();

		} else if(is_page()){

			$this ->page_router();

		} else if(is_404()){

			$this ->exception_router(404);

		}
		
	}

	

    /*
    *
    * Page Router
    *
    */


	public function page_router(){
		
		

		if(class_exists( 'WooCommerce' )){
			if(is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url()){
				$this ->wc_router();
			} 
		}

		
		// View Type
		$this->view_type = 'page';

		
		


		// Get global post
		global $post;


		/*
        *
        * Get Page template & Set template
        *
        */

		$templates = PageTemplater::get_templates();

		$page_template =  get_post_meta( 
				$post->ID, '_wp_page_template', true
			);

		if ( ! isset( $templates[$page_template] ) ) {

			$page_template = "page.twig";
			$action = "page";

		} else {

			$path_parts = pathinfo($page_template);
			$action = self::slugToMethod($path_parts['filename']);

		}


		$this->template = $page_template;

		/*
        *
        * Controller: PagesController
        * Action: Name of page template
        *
        */

		$this->controller = '\PagesController';
		

		$this->action = $action;


		/*
        *
        * Run router
        *
        */

		$this->run();
	}



    /*
    *
    * WooCommerce Router
    *
    */


	public function wc_router(){

		$this->view_type = 'page';
		
		if(is_shop()){

			$template = "shop.twig";
			$action = "shop";

		} if(is_cart()){

			$template = "cart.twig";
			$action = "cart";

		} else if (is_checkout()) {
			
			$template = "checkout.twig";
			$action = "checkout";

		} else if (is_account_page()) {

			$template = "account.twig";
			$action = "account";
			
		} else if (is_wc_endpoint_url()) {
			
		}
	
		
		$this->template = $template;

		/*
        *
        * Controller: WooController
        * Action: WC Action
        *
        */

		$this->controller = '\WooController';
		$this->action = $action;


		/*
        *
        * Run router
        *
        */

		$this->run();
	}



    /*
    *
    * Single Router
    *
    */

	public function single_router($template){


		// View Type
		$this->view_type = 'single';

		// WP default template
		$this->template = false;

	

		/*
        *
        * Controller: PostType
        * Action: Single
        *
        */
		
		$post_type = get_queried_object()->post_type;

		if($post_type == 'post'){

			$post_object = get_post_type_object('post');

			if(isset($post_object->rewrite['slug'])){

				$post_type = $post_object->rewrite['slug'];

			} else {

				$post_type = 'post';

			}

		} 
		
		
		$this->post_type =$post_type;

		if($post_type == 'product'){
			$this->controller = '\WooController';
			$this->action = "product";
		} else {
			$this->controller = self::slugToController($post_type);
			$this->action = "single";
		}
		


		/*
        *
        * Run router
        *
        */

		$this->run();	

	}





    /*
    *
    * Archive Router
    *
    */

	public function archive_router(){

		if(is_home()){

			$post_object = get_post_type_object('post');

			if(isset($post_object->rewrite['slug'])){

				$post_type = $post_object->rewrite['slug'];

			} else {

				$post_type = 'post';

			}

		} else {

			$post_type = get_queried_object()->name;

		}

		// Post Type
		$this->post_type =$post_type;


		// View Type
		$this->view_type = 'archive';


		//Template
		$this->template = 'archive_'.$post_type.'.twig';

		// WP default template
		//$this->template = $template;

		/*
        *
        * Controller: PostType
        * Action: Index
        *
        */

		$this->controller = self::slugToController($post_type);
		$this->action = "index";


		/*
        *
        * Run router
        *
        */

		$this->run();

		
	}





    /*
    *
    * Tax Router
    *
    */

	public function taxonomy_router(){



		$this->controller = 'TaxonomiesController';

		$this->view_type = 'taxonomy';

		//$this->template = $template;

		if(get_query_var('taxonomy')){

			$this->action = self::slugToMethod(get_query_var('taxonomy'));

		} else if(is_category()) {

			$this->action = 'category';

		} else if(is_tag()){

			$this->action = 'tag';

		}


		/*
        *
        * Run router
        *
        */

		$this->run();
		

	}


	/*
    *
    * User, Author,...
    *
    */

	public function user_router($type){


		die($type);
		

	}


	/*
    *
    * Error Router
    *
    */

	public function exception_router($error){

		$errorName = 'error'.$error;

		$this->view_type = $errorName; 
		$this->controller = '\MidoController';
		$this->action = 'render';

		status_header( $error );

		if(file_exists(CONTROLLERS_DIR.'/ExceptionController.php')){

					$this->controller = '\ExceptionController';

					$this->action = $errorName;
		};

		/*
       	 *
        	* Run router
        	*
        	*/

		$this->run();
		

	}


	/*
    *
    * Custom router
    * Args:
    *	- controller
    *	- action
    *	- default_template
    *	- custom query
    *	- ... (custom by dev)
    *
    */

	public function map($route, $callback){

		\Routes::map($route, function($params) use ($callback) {

		 		return call_user_func_array($callback, func_get_args());

		 });

	}

	public static function map_ini($route, $args){

		\Routes::map($route, function($params) use ($args) {

		 	return call_user_func_array(array('\Mido\Router','map_ini_callback'), array($params,$args));

		 });

	}

	private static function map_ini_callback($params,$args){

			if (isset($args['redirect'])) {
				self::redirect($args['redirect']);
				return;
			}
			
			$_this = self::getInstance();



			$_this->query = self::map_query($params,$args);
			if($_this->query) self::add_query($_this->query);

			$_this->controller = self::custom_router_set_arg($args['controller'], $params, 'controller');

			

			if(isset($args['action'])){
				$_this->action = self::custom_router_set_arg($args['action'], $params, 'action');
			} else {
				$_this->action = 'index';
			}

			if(isset($args['template'])) $_this->template = $args['template'];
			if(isset($args['view_type'])) $_this->view_type = $args['view_type'];

			$_this->params = $params;

			
			if(isset($args['callback'])){

				$callback = explode('->',$args['callback']);

				if (count($callback) === 1) $callback = $callback[0];

				add_filter('template_include',function() use ($_this, $callback){

					$args = get_object_vars($_this);
					call_user_func($callback, $args);
					exit();

				});

			} else {

				$_this->run();
				//add_filter('template_include',array($_this, 'run'));

			}

	}


	private static function redirect($redirect){

			if(is_array($redirect)){

				reset($redirect);
				$status = key($redirect);
				$url = $redirect[$status];

			} else {

				$status = 302;
				$url = $redirect;

			}

			wp_redirect( $url, $status );
			exit;

	}


	public static function map_query($params,$args){

		if(!isset($args['query'])) return false;

		$query = $args['query'];

		if(is_string($query)){

			$query_parts = explode(":",$query);

			if(isset($query_parts[2])){
				$key_parts = array();
				$param_parts = array();
				$full = array(&$key_parts, &$param_parts);
				array_walk($query_parts, function($v, $k) use ($full) { $full[$k % 2][] = $v; });
				
				foreach($param_parts as $array_key => $param_key){
					
					$query_parts[($array_key*2)+1] = $params[$param_key];
				
				}

				$query = implode($query_parts);

			}

		} else if(is_array($query)){

			foreach($query as $key => $val){
				if(substr( $val, 0, 1 ) == ':'){
					$query[$key] = $params[substr($val,1)];
				}
			}
		}

		return $query;

	}

	public static function add_query($query){

		add_action('do_parse_request', function() use ($query) {
				global $wp;

				if ( is_callable($query) )
					$query = call_user_func($query);

				if ( is_array($query) )
					$wp->query_vars = $query;
				elseif ( !empty($query) )
					parse_str($query, $wp->query_vars);
				else
					return true; // Could not interpret query. Let WP try.

				return false;
			});

	}



	private static function custom_router_set_arg($arg, $params, $type = 'controller'){
			if(substr( $arg, 0, 1 ) == ':'){
				$arg = $params[substr($arg,1)];
			} 

			if($type == 'controller'){
					$arg = self::slugToController($arg);
			} else if($type == 'action') {
					$arg = self::slugToMethod($arg);
			}

			return $arg;
	}



    /*
    *
    * Run Router
    *
    */

	public function run($custom = false){


		if($custom){
			$_this->controller = self::slugToController($custom['controller']);
			$_this->action = self::slugToMethod($custom['method']);
			$_this->template = $custom['template'];
			$_this->view_type = $custom['view_type'];
			$_this->params = $custom['params'];
		} else{
			$_this = $this;
		}
		
		$_this->run_controller();
		
	}

	private function run_controller(){

		$controllerName = substr($this->controller,1);
		$actionName =  $this->action;

		$this->validate();

		$controllerClass = $this->controller;

		$action = $this->action;

		if(!isset($this->params)){
			$this->params = null;
		}


		$router = array(
			'controller' => $controllerClass,
			'controller_name' => $controllerName,
			'method' => $action,
			'method_name' => $actionName,
			'persistent_method' => $actionName,
			'params' => $this->params
		);



		if(isset($this->view_type)) $router['view_type']=$this->view_type;

		if(isset($this->template)) $router['template']=$this->template;

		if(isset($this->post_type)) $router['post_type']=$this->post_type;
		
			
		Scripts::add_scripts_bundle($router['controller_name'].'_js');
		Scripts::add_scripts_bundle($router['controller_name'].'_'.$router['method_name'].'_js');
			
		Scripts::add_styles_bundle($router['controller_name'].'_css');
		Scripts::add_styles_bundle($router['controller_name'].'_'.$router['method_name'].'_css');
		
        
		

		$controller = new $controllerClass($router);

		
		exit();
	}



	/*
    *
    * Slug
    *
    */


	static function slugToFunction($slug){

		return str_replace(' ', '', ucwords(str_replace('-', ' ', $slug)));

	}


	static function slugToController($slug){

			$slug = ucfirst(self::slugToFunction($slug));
			$slugLength =  strlen( $slug );
			$controllerWordLength = 11; // 'Controller';

			$hasControllerWord = false;

			if($slugLength>$controllerWordLength){
				if(substr($slug,$slugLength-$controllerWordLength+1) == 'Controller'){
							$hasControllerWord = true;
					}
			}

			if(!$hasControllerWord){
				$slug = $slug.'Controller';
			}

			if(substr($slug,0,1) != '\\'){
				$slug = '\\'.$slug;
			}
			
	        return $slug;

	}

	static function slugToMethod($slug){

			$slug = self::slugToFunction($slug);
			$slug[0] = strtolower($slug[0]);
			return $slug;

	}



    /*
    *
    * Errors
    *
    */

    private function validate(){
    	
    	$error = false;

    	$file = CONTROLLERS_DIR.'/'.substr($this->controller,1).'.php';


    	if(file_exists($file)){

    		include_once($file);

    	} else {
    		$this->controller = 'MidoController';
    		$this->action = 'render';
    	}

    	

		if (!class_exists($this->controller)) {
			$error = 'controllerNotFound';
		} else if($this->action && !method_exists($this->controller, $this->action)){
			$error = 'methodNotFound';
		}


		if($error && !current_user_can('administrator')){
			 $this->exception_router(404);
		} else if($error){
			 $this->$error();
		} 
		 
    }

	private function controllerNotFound(){
			throw new \Exception('Controller "'.$this->controller.'" not found');
	}

	private function methodNotFound(){
			throw new \Exception('Method "'.$this->action.'" not found in "'.$this->controller.'"');
	}
	
}
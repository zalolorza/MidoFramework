<?php
namespace Mido;

class PageTemplater {

	
	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;

	/**
	 * Returns an instance of this class. 
	 */
	use Singleton;

	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {

		$this->templates = array();


		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}

		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_project_templates' ) 
		);


		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path

		// NOW ON ROUTER

		/*add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);*/

	} 



	public static function add_template($name = null, $file = null){
		$_this = self::getInstance();

		if(!$file) $file = $name;
		if(!$name || is_int($name)) $name = $file;

		/*$path_parts = pathinfo($file);
		
		$views_path = get_stylesheet_directory().'/views/';


		if($path_parts['dirname'] != '.'){
			foreach(explode('/',$path_parts['dirname']) as $directory){
				$dirname = sanitize_title($directory);
				$views_path .= $dirname.'/';
				$Timberdirname .= '/'.$dirname;
				if(!file_exists($views_path)){
	                @mkdir($views_path, 0777, true);
	            }
	        }
		}

		$file_name = sanitize_title($path_parts['basename']);

		
		$path = $views_path.$file_name.'.twig';

		if(is_admin()){

			if(!file_exists($path)){
                $path = fopen($path, "w");
                fwrite($path,'New TWIG template: ' .$name.'<br><br>Controller: {{controller.name}} <br>Method: {{controller.action}} <br><br>Template: {{controller.template}} <br>');
            }   

		}

		$_this->templates[$path] = $name;

		*/


	 	$_this->templates[$file] = $name;

		
	}

	public static function get_templates(){
		$_this = self::getInstance();
		return $_this->templates;
	}



	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {

		$posts_templates = array_merge( $posts_templates, $this->templates );

		return $posts_templates;
	}

	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;

	} 

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		

		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		$file =  get_post_meta( 
			$post->ID, '_wp_page_template', true
		);


		//Router::page($file);
	}

} 



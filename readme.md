# MidoFramework - A MVC framework for WordPress
WordPress plugin that runs an MVC framework the top of WordPress. It uses [Timber](https://timber.github.io/) to render [Twig](https://twig.symfony.com/) views.

## Installation
Just install it like a regular plugin.

### functions.php
In order to use the Mido in yourr theme, add the following in your `functions.php`:

````
if (class_exists('Mido')){

	Mido::run();

} else if(!is_admin()) {

	wp_die('This theme needs MidoFramework. Please contact hi@zalo.nyc for more information');

}

````

### Theme directory structure

Mido will start the php app from the directory where `bootstrap.php` is allocated. Within this directory, the basic structure should be something like (you can change that):

```
/app/ (can be any directory inside the theme)
|-- /controllers/
|-- /init/
|-- /managers/
|-- /views/
|-- bootstrap.php
|-- bootstrapAdmin.php
````




# Bootstrap & Init

* `bootstrap.php` is always called when the framework is initialized. Here you can set some globals, like Mido Globals:

````
class Bootstrap extends MidoBootstrap {

	function _init(){

		define('INIT_DIR', BOOTSTRAP_DIR.'/init');
		define('CONTROLLERS_DIR', BOOTSTRAP_DIR.'/controllers');
		define('MANAGERS_DIR', BOOTSTRAP_DIR.'/managers');
		define('VIEWS_DIR', 'php/views');
	}
}
		  
		  
````

* `bootstrapAdmin.php` is only called when the admin is initialized.

````
class BootstrapAdmin extends MidoBootstrap {

	function _init(){

		// any admin setup
	}
}
`````

## action_  and  filter_

Any method inside the classes `Bootstrap` and `BootstrapAdmin` that starts with `action_` equals to: `add_action()` WordPress function. The same with WordPress filters. For example:

````
class Bootstrap extends MidoBootstrap {

	function action_admin_init(){

		/* 	This equals to 
		*
		*	add_action('admin_init',function(){
		*		
		*	});
		*
		*/
	}
}
`````

The cool thing is that the scope inside the action will be the parent class.

If you add a number at the end of the action name, like `action_admin_init_99` it'll be interpretated as the priority value.

This also applies for `Managers` that extend `MidoManager`.




## Init

Basic configuration of the theme


#### admin.ini

Basic admin configurations. Mido sets a completely custom admin, with custom styles, editors, toolbars,...

#### cpt.ini

Custom Post Types setup

#### taxonomies.ini

Taxonomies setup

#### images.ini

Images setup. Here you can set custom sizes or edit the default ones, set the crop quality, and add new MIME types.

#### menus.ini

Theme menus setup

#### pages_templates.ini

Page templater. In order to avoid php templates, twig templates are set here. Example:

````
Home = home.twig
About = about.twig
Contact = contact.twig
````

This will be used in the PagesController and as a page template selector.

#### routes.ini

Custom router that overrides WordPress endpoints (if you want). It stablishes a relationship between routes and controllers. Any route set here will override WordPress router. It's possible to use dynamic segments.

Example:

````
[contact/send/]
controller = FormController
action = send
`````

In that case it'll set a route like `www.domain.com/contact/send` that will be controlled by:

````
class FormController extends MidoController  {

	function send(){

	
		}
} 
`````

You could also pass an argument, for example: `[contact/send/:formId]`


#### scripts.ini

Here you can:

* Set the scripts version in order to bust cache
* Set 'false' to default WordPress jquery script
* Set custom JS and CSS scripts that'll be included in the theme automatically:

````
[js]

build_js = '/dist/build.js'
other_js_file = '/dist/other.js'

[css]

build_css = '/dist/build.css'
`````

* And you can also set specific scripts for specific controllers and/or actions. 

````
[ControllerName_js]

version = 0.1
scriptnameforacontroller = '/dist/scriptname_controller.js'

[ControllerName_ActionName_js]

version = 0.1
scriptnameforaspecificaction = '/dist/scriptname_action.js'

`````




# Controllers

## Basic controller sintaxis
````
class ControllerName extends MidoController  {

	function _init(){
	
	}

	function action(){

	
	}
} 
````

Like other MVC PHP frameworks, Mido uses a Controller->Action architecture. Anytime a controller is called, there are global actions that also run besides the action endpoint. For example, anytime a controller is called, it first runs a `_init()` action, before calling any other action. Here you can set global functionalities for the controller.

### Render
````
class ControllerName extends MidoController  {

	function _init(){
	
	}

	function action(){
	
		$this->var_name = 'Hello dolly';
		$this->render();
	
	}
} 
````
1) Set any var as a variable of the controller to make it available in the Twig view. 

2) Use `$this->render()` to render the default Twig view. Mido uses a template hierarchy system that mimics and improves [WordPress template hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)

### Posts and CPT
### Pages
### Taxonomies

## Managers
1. This is

## Views
1. This is

# Examples
1. This is

# MidoFramework - A MVC framework for WordPress
WordPress plugin that runs an MVC framework the top of WordPress. It uses [Timber](https://timber.github.io/) to render [Twig](https://twig.symfony.com/) views.

**This plugin is still experimental, DON'T use it in your sites (I do because I know my code)**

## 0. Contents

1. [Installation](#1-installation)
2. [Bootstrap and initializaton](#2-bootstrap-and-initialization)
3. [Controllers](#3-controllers)
4. [Managers](#4-managers)
5. [Views](#5-views)
6. [Actions and filters](#6-actions-and-filters)
7. [Others](#7-others)
8. [Examples](#8-examples)



## 1. Installation
Just install it like a regular plugin.

### functions.php
In order to use the Mido in yourr theme, add the following in your `functions.php`:

```php
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

---


# 2. Bootstrap and initialization

* `bootstrap.php` is always called when the framework is initialized. Here you can set some globals, like Mido Globals:

```php
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

```php
class BootstrapAdmin extends MidoBootstrap {

	function _init(){

		// any admin setup
	}
}
`````

## action_  and  filter_

Any method inside the classes `Bootstrap` and `BootstrapAdmin` that starts with `action_` equals to: `add_action()` WordPress function. The same with WordPress filters. For example:

```php
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

```ini
Home = home.twig
About = about.twig
Contact = contact.twig
````

This will be used in the PagesController and as a page template selector.

#### routes.ini

Custom router that overrides WordPress endpoints (if you want). It stablishes a relationship between routes and controllers. Any route set here will override WordPress router. It's possible to use dynamic segments.

Example:

```ini
[contact/send/]
controller = FormController
action = send
`````

In that case it'll set a route like `www.domain.com/contact/send` that will be controlled by:

```php
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

```ini
[js]

build_js = '/dist/build.js'
other_js_file = '/dist/other.js'

[css]

build_css = '/dist/build.css'
`````

* And you can also set specific scripts for specific controllers and/or actions. 

````ini
[ControllerName_js]

version = 0.1
scriptnameforacontroller = '/dist/scriptname_controller.js'

[ControllerName_ActionName_js]

version = 0.1
scriptnameforaspecificaction = '/dist/scriptname_action.js'

`````


---

# 3. Controllers

## Basic controller sintaxis

```php
class NameController extends MidoController  {

	function _init(){
	
	}

	function action(){

	
	}
} 
````

Like other MVC PHP frameworks, Mido uses a Controller->Action architecture. Anytime a controller is called, there are global actions that also run besides the action endpoint. For example, anytime a controller is called, it first runs a `_init()` action, before calling any other action. Here you can set global functionalities for the controller.

### Render

```php
class NameController extends MidoController  {

	function _init(){
	
	}

	function action(){
	
		$this->var_name = 'Hello dolly';
		$this->render();
	
	}
} 
````
1) Set any var as a variable within the scope of the controller to make it available in the Twig view. The controller comes already hydratated with the variables that you'd expect. For example, in the single view it comes hydratated with all post information (title, date, content, metadata,...) as `$this->post`, and the archive comes with a complete list of posts as `$this->posts`.

2) Use `$this->render()` to render the default Twig view. Mido uses a template hierarchy system that mimics and extends [WordPress template hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/). 

3) You can skip Mido's hierarchy by specifying your own template: `$this->render('your-better-template.twig')`. Directory is not requiered, only the filename. Mido will render the first twig file that maches the filename and it's located in any subdirectory inside of `/views`.

## Posts and CPT

Each post type has its own controller. For example, the "post" post type will have a controller named `PostController.php` and will look like:


```php
class PostController extends MidoController {

	function _init(){
		// Anytime the controller is called
	}

	function index(){

		// Archive
		$this->render();
		
	}

	function single(){

		// Single
		$this->render();
		
	}

	function category(){

		// Categories
		$this->render();

	}
	
	function tag(){

		// tags
		$this->render();

	}


}
````

For custom post types is the same: a CPT named `custom-post-type` will be controlled by `CustomPostTypeController` in `CustomPostTypeController.php`.

Taxonomies are actions inside of the post type controller if they are referenced to a single post type. If those taxonomies are used in more than one post type, then we need a new controller for each taxonomy.


## Pages

Pages are all fired from a single controller `PagesController.php`. Each action refers to a specific page template. For example, if we have `home.twig`, `about.twig` and `contact.twig` as page templates, the controller will look like:

```php

class PagesController extends MidoController {

	function _init(){
			
	}

	function page(){
		//default template (no template)
		$this->render();
		
	}


	function home(){

		//it renders 'home.twig'
		$this->render();

	}

	function about(){

		$this->render();

	}


	function contact(){

		$this->render();

	}


}


```


## Other controllers

Other controllers include endpoints like: 
* Taxonomies 
* Authors & Users
* WooCommerce
* Exeptions
* API REST
* Ajax
* Mail

---

# 4. Managers
Managers are allways available from any controller (in other words, all php files inside of `/managers` will be included before calling the controller). 

Managers can extend `MidoManager` class, so they can benefit of the [actions and filters Mido's functionality](#action_--and--filter_).

---

# 5. Views
1. This is

## Herarchy

### Post Type Single
```
single_{{post_type}}.twig
single.twig
````

### Post Type Archive
```
archive_{{post_type}}.twig
index_{{post_type}}.twig
archive.twig
index.twig
````

### Taxonomies
```
{{tax_name}}.twig
tax_{{tax_name}}.twig
archive_{{tax_name}}.twig
index_{{tax_name}}.twig
archive.twig
index.twig
````

### Search
```
search.twig
````

### Error 404
```
404.twig
````

---

# 6. Actions and filters
1. This is

---

---

# 7. Others
1. This is

---

# 8. Examples
1. This is

**This plugin is still experimental, DON'T use it in your sites (I do because I know my code)**

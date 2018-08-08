# MidoFramework - An MVC framework for WordPress
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


# The MVC Framework

## Theme directory structure

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

## Init

Basic configuration of the theme

### admin.ini

Basic admin configurations. Mido sets a completely custom admin, with custom styles, editors, toolbars,...

### cpt.ini

Custom Post Types setup

### taxonomies.ini

Taxonomies setup

### images.ini

Images setup. Here you can set custom sizes or edit the default ones, set the crop quality, and add new MIME types.

### menus.ini

Theme menus setup

### pages_templates.ini

Page templater. In order to avoid php templates, twig templates are set here. Example:

````
Home = home.twig
About = about.twig
Contact = contact.twig
````

This will be used in the PagesController and as a page template selector.

### routes.ini

Custom router that overrides WordPress endpoints (if you want). It stablishes a relationship between routes and controllers. Any route set here will override WordPress router. It's possible to use dynamic segments.

Example:

````
[contact/send/]
controller = FormController
action = send
`````

In that case it'll set a route like www.domain.com/contact/send that will be controlled by:

````
class FormController extends MidoController  {

	function send(){

	
		}
} 
```


### scripts.ini


## Controllers
### Posts and CPT
### Pages
### Taxonomies

## Managers
1. This is

## Views
1. This is

# Examples
1. This is

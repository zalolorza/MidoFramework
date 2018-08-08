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


## MVC structure
1. This is

### Controllers
#### Posts and CPT
#### Pages
#### Taxonomies

### Managers
1. This is

### Views
1. This is

## Examples
1. This is

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
|-- controllers
|-- init
|-- managers
|-- views
bootstrap.php
bootstrapAdmin.php
````


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

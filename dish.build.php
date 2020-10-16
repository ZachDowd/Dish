<?php

use \Dish\CLI;
use \Dish\Config;
use \Dish\Drive\Folder;
use \Dish\Build;

Config::setMany($params);
Config::load(Config::get('paths.config'));

Build::makeDestination(Config::get('paths.dest'));

Build::move(Config::get('paths.public'), Config::get('paths.dest'));

if(Config::get('minify'))
{
	Build::minify();
}

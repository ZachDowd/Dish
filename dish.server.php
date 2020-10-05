<?php

require "vendor/autoload.php";

use \Dish\Config;
use \Dish\Dish;

Config::setMany(json_decode($_ENV['DISH_PARAMS']));
Config::load(Config::get('paths.config') . '/config.json');

if(Dish::isAssetRequest())
{
	return false;
}
elseif(Dish::isDocsRequest())
{
	//Dish::serveDocs();
}
elseif(Dish::isValidRequest())
{
	Dish::serve();
}
else
{
	Dish::serve404();
}

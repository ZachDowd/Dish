<?php

$DIR = isset($DIR) ? $DIR : $_SERVER['PWD'];

require $DIR . '/vendor/autoload.php';

use \Dish\CLI;

// Merge parameters with default values
$params = CLI::parameters($_SERVER['argv'], [
	'addr'			=> '127.0.0.1:8080',
	'dest'			=> 'build',
	'indexify'		=> false,
	'minify'		=> false,
	'timestamp'		=> date('Y-m-d_His'),
	'parse'			=> true,
	'env'			=> 'dev'
]);

// Build out the path to be used for loading content
$params['paths'] = [
	'root'			=> $DIR,
	'dest'			=> $DIR . '/' . $params['dest'],
	'src'			=> $DIR . '/src',
	'components'	=> $DIR . '/src/components',
	'config'		=> $DIR . '/src/config',
	'public'		=> $DIR . '/src/public',
	'assets'		=> $DIR . '/src/public/site'
];

// `dish build` -> run build
if(count($params['cmds']) > 1 && $params['cmds'][1] == 'build')
{
	require 'dish.build.php';
	exit;
}

// `dish deploy` -> setup files and folders
if(count($params['cmds']) > 1 && $params['cmds'][1] == 'deploy')
{
	require 'dish.deploy.php';
	exit;
}

// `dish directory-name/` -> serve content from that directory with no parsing
if(count($params['cmds']) > 1 && preg_match('/\/$/', $params['cmds'][1]))
{
	$params['parse'] = false;
	$params['paths']['public'] = $DIR . '/' . $params['cmds'][1];
	$params['paths']['pages'] = $DIR . '/' . $params['cmds'][1];
}

CLI::newline();
CLI::good('Dished out on http://' . $params['addr']);
CLI::warn('Press CTRL+C to close');
CLI::newline();

$json = addslashes(json_encode($params));

exec('DISH_PARAMS="' . $json . '" php -S ' . $params['addr'] . ' -t ' . $params['paths']['public'] . ' -c src/config/php.ini ' .  $DIR . '/vendor/dish/dish/dish.server.php');

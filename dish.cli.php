<?php

require $_SERVER['PWD'] . '/vendor/autoload.php';

use \Dish\CLI;

// Merge parameters with default values
$params = CLI::parameters($_SERVER['argv'], [
	'addr'			=> '127.0.0.1:8080',
	'dest'			=> 'build',
	'host-assets'	=> false,
	'minify'		=> false,
	'timestamp'		=> date('Y-m-d_His'),
	'parse'			=> true,
]);

// Build out the path to be used for loading content
$params['paths'] = [
	'root'			=> $_SERVER['PWD'],
	'dest'			=> $_SERVER['PWD'] . '/' . $params['dest'],
	'src'			=> $_SERVER['PWD'] . '/src',
	'components'	=> $_SERVER['PWD'] . '/src/components',
	'config'		=> $_SERVER['PWD'] . '/src/config',
	'pages'			=> $_SERVER['PWD'] . '/src/pages',
	'public'		=> $_SERVER['PWD'] . '/src/public'
];

// `dish build` -> run build
if(count($params['cmds']) > 1 && $params['cmds'][1] == 'build')
{
	require 'dish.build.php';
	exit;
}

// `dish directory-name/` -> serve content from that directory with no parsing
if(count($params['cmds']) > 1 && preg_match('/\/$/', $params['cmds'][1]))
{
	$params['parse'] = false;
	$params['paths']['public'] = $_SERVER['PWD'] . '/' . $params['cmds'][1];
	$params['paths']['pages'] = $_SERVER['PWD'] . '/' . $params['cmds'][1];
}

CLI::newline();
CLI::good('Dished out on http://' . $params['addr']);
CLI::warn('Press CTRL+C to close');
CLI::newline();

$json = addslashes(json_encode($params));

exec('DISH_PARAMS="' . $json . '" php -S ' . $params['addr'] . ' -t ' . $params['paths']['public'] . ' -c src/config/php.ini ' .  $_SERVER['PWD'] . '/vendor/dish/dish/dish.server.php');

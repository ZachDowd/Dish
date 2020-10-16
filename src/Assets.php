<?php namespace Dish;

class Assets {

	/**
	 * CSS
	 *
	 * List of CSS <link>
	 *
	 * @return	string
	 */

	public static function css()
	{
		$html = [];

		$assetsPath = str_replace(Config::get('paths.public'), '', Config::get('paths.assets'));

		if(Config::get('minify'))
		{
			$html[] = '<link rel="stylesheet" type="text/css" href="' . $assetsPath . '/' . Config::get('timestamp') . '.css" />';
		}
		else
		{
			$files = Config::get('assets.css');
			foreach($files as $file)
			{
				$html[] = '<link rel="stylesheet" type="text/css" href="' . $file . '" />';
			}
		}

		return implode("\n", $html) . "\n";
	}

	/**
	 * JS
	 *
	 * List of JS <script>
	 *
	 * @return	string
	 */

	public static function js()
	{
		$html = [];

		$assetsPath = str_replace(Config::get('paths.public'), '', Config::get('paths.assets'));

		if(Config::get('minify'))
		{
			$html[] = '<script src="' . $assetsPath . '/' . Config::get('timestamp') . '.js"></script>';
		}
		else
		{
			$files = Config::get('assets.js');

			foreach($files as $file)
			{
				$html[] = '<script src="' . $file . '"></script>';
			}
		}

		return implode("\n", $html);
	}

}

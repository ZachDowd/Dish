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

		$host = Config::get('host-assets') ? Config::get('host') : '';

		if(Config::get('minify'))
		{
			$html[] = '<link rel="stylesheet" type="text/css" href="' . $host . '/css/' . Config::get('timestamp') . '.css" />';
		}
		else
		{
			$files = Config::get('css');
			foreach($files as $file)
			{
				$html[] = '<link rel="stylesheet" type="text/css" href="' . $host . $file . '" />';
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

		$host = Config::get('host-assets') ? Config::get('host') : '';

		if(Config::get('minify'))
		{
			$html[] = '<script src="' . $host . '/js/' . Config::get('timestamp') . '.js"></script>';
		}
		else
		{
			$files = Config::get('js');

			foreach($files as $file)
			{
				$html[] = '<script src="' . $host . $file . '"></script>';
			}
		}

		return implode("\n", $html);
	}

}

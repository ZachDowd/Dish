<?php namespace Dish;

class Dish {

	/**
	 * isAssetRequest
	 *
	 * Should PHP built-in server treat this URL as an asset?
	 *
	 * @return	bool
	 */

	public static function isAssetRequest()
	{
		$types = Config::get('assets');

		if(preg_match('/\.(?:' . implode('|', $types) . ')$/', $_SERVER["REQUEST_URI"]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * isDocsRequest
	 *
	 * Can this page be resolved to a file?
	 *
	 * @return	bool
	 */

	public static function isDocsRequest()
	{
		return preg_match('/^\/docs/', $_SERVER['REQUEST_URI']);
	}

	/**
	 * isValidRequest
	 *
	 * Can this page be resolved to a file?
	 *
	 * @return	bool
	 */

	public static function isValidRequest()
	{
		return file_exists(self::pagePath());
	}

	/**
	 * pagePath
	 *
	 * Asbolute path to the requested page
	 *
	 * @return	string
	 */

	public static function pagePath()
	{
		$file = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);

		// Correct the filename for the homepage
		if($file == '/')
		{
			$file = '/index';
		}

		return Config::get('paths.pages') . $file . '.html';
	}

	/**
	 * serve
	 *
	 * Serve the content
	 *
	 * @return	string
	 * @return	void
	 */

	public static function serve($page = null)
	{
		if(Config::get('parse'))
		{
			ob_start();

			include($page ? $page : self::pagePath());

			$doc = new View;
			$html = $doc->parse(ob_get_clean());
			$html = $doc->parseReplacements($html, Config::get('replacements'));

			echo $html;
		}
		else
		{
			include(self::pagePath());
		}
	}

	/**
	 * serve404
	 *
	 * Serve the 404 content
	 *
	 * @return	void
	 */

	public static function serve404()
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		self::serve(Config::get('paths.pages') . '/404.html');
	}

	/**
	 * serveDocs
	 *
	 * Serve the build content
	 *
	 * @return	string
	 * @return	void
	 */

	public static function serveDocs()
	{
		self::serve('app/Docs/docs.html');
	}

}

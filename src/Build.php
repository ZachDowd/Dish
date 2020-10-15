<?php namespace Dish;

use \Dish\Drive\File;
use \Dish\Drive\Folder;
use \MatthiasMullie\Minify;

class Build {

	/**
	 * makeDestination
	 *
	 * Make a clean destination folder
	 *
	 * @param	$dest	string
	 * @return	void
	 */

	public static function makeDestination($dest)
	{
		$dest = new Folder($dest);

		if($dest->exists())
		{
			$dest->delete();
		}

		// Make empty folder
		$dest->make();
	}

	/**
	 * move
	 *
	 * Build $src into $dest
	 *
	 * @param	$src	string
	 * @param	$dest	string
	 * @return	void
	 */

	public static function move($src, $dest)
	{
		$src = new Folder($src);
		$dest = new Folder($dest);

		self::processFolder($src, $dest, $src);
	}

	/**
	 * processFolder
	 *
	 * Loop through and build the folder
	 *
	 * @param	$from Drive/Folder
	 * @param	$dest Drive/Folder
	 * @param	$src Drive/Folder
	 * @return	void
	 */

	public static function processFolder($from, $dest, $src)
	{
		// Get child files and folders
		$files = $from->files();
		$folders = $from->folders();

		$path = self::pathify($src->path(), $dest->path(), $from->path());
		$f = new Folder($path);
		$f->make();

		foreach($folders as $folder)
		{
			$f = new Folder($folder->path());
			self::processFolder($f, $dest, $src);
		}

		foreach($files as $file)
		{
			self::processFile($file, $dest, $src);
		}
	}

	/**
	 * processFile
	 *
	 * Build the file
	 *
	 * @param	$file	Drive/File
	 * @param	$dest	Drive/Folder
	 * @param	$src	Drive/Folder
	 * @return	void
	 */

	public static function processFile($file, $dest, $src)
	{
		if(preg_match('/\.html$/', $file->path()))
		{
			ob_start();

			$globals = Config::get('globals');
	
			foreach($globals as $key => $value)
			{
				$$key = $value;
			}
	
			include($file->path());
	
			$contents = ob_get_clean();

			// Parse the file
			$doc = new View;
			$contents = $doc->parse($contents);
			$contents = $doc->parseReplacements($contents, Config::get('replacements'));
		}
		else
		{
			$contents = $file->contents();
		}

		// Set the path name
		$path = self::pathify($src->path(), $dest->path(), $file->path());

		if(Config::get('indexify') && preg_match('/\.html$/', $path) && !preg_match('/(index|[0-9]{3})\.html$/', $path))
		{
			$path = str_replace('.html', '', $path);
			$builtFolder = new Folder($path);

			if(!$builtFolder->exists())
			{
				$builtFolder->make();
			}

			$path .= '/index.html';
		}

		// Save the file
		$built = new File($path);
		$built->make();
		$built->contents($contents);
	}

	/**
	 * pathify
	 *
	 * Update path name to work in build folder
	 *
	 * @param	$find		string
	 * @param	$replace	string
	 * @param	$str		string
	 * @return	string
	 */

	public static function pathify($find, $replace, $str)
	{
		return str_replace($find, $replace, $str);
	}

	/**
	 * minify
	 *
	 * Minify CSS and JS
	 *
	 * @return	void
	 */

	public static function minify()
	{
		$public = Config::get('paths.public');

		$assetsPath = Config::get('paths.dest') . str_replace(Config::get('paths.public'), '', Config::get('paths.assets'));
		$assetsPath = str_replace('//', '/', $assetsPath);

		$minifier = new Minify\CSS();
		$files = Config::get('css');

		foreach($files as $file)
		{
			$minifier->add($public . $file);
		}

		$minifier->minify($assetsPath . '/' . Config::get('timestamp') . '.css');

		$minifier = new Minify\JS();
		$files = Config::get('js');

		foreach($files as $file)
		{
			$minifier->add($public . $file);
		}

		$minifier->minify($assetsPath . '/' . Config::get('timestamp') . '.js');

	}

}

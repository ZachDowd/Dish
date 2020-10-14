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
	 * @param	$parse	bool
	 * @return	void
	 */

	public static function move($src, $dest, $parse = false)
	{
		$src = new Folder($src);
		$dest = new Folder($dest);

		self::processFolder($src, $dest, $src, $parse);
	}

	/**
	 * processFolder
	 *
	 * Loop through and build the folder
	 *
	 * @param	$from Drive/Folder
	 * @param	$dest Drive/Folder
	 * @param	$src Drive/Folder
	 * @param	$parse	bool
	 * @return	void
	 */

	public static function processFolder($from, $dest, $src, $parse)
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
			self::processFolder($f, $dest, $src, $parse);
		}

		foreach($files as $file)
		{
			self::processFile($file, $dest, $src, $parse);
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
	 * @param	$parse	bool
	 * @return	void
	 */

	public static function processFile($file, $dest, $src, $parse)
	{
		$contents = $file->contents();

		if($parse)
		{
			// Parse the file
			$doc = new View;
			$contents = $doc->parse($contents);
			$contents = $doc->parseReplacements($contents, Config::get('replacements'));

			if(Config::get('host-assets'))
			{
				$contents = self::htmlFixImages($contents);
			}
		}

		// Set the path name
		$path = self::pathify($src->path(), $dest->path(), $file->path());

		if(Config::get('indexify') && !preg_match('/index\.html$/', $path) && !preg_match('/[0-9]\.html$/', $path))
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
	 * htmlFixImages
	 *
	 * Replace images with correct URLs
	 *
	 * @param	$html	string
	 * @return	string
	 */

	public static function htmlFixImages($html)
	{
		// Replace images with hosted versions
		return preg_replace('/(\()?(\'|\")?\/images\//', "$1$2" . Config::get('host') . "/images/", $html);
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
		$dest = Config::get('paths.dest');
		$public = Config::get('paths.public');

		$css = new Folder($dest . '/css');
		$css->delete();
		$css->make();

		$minifier = new Minify\CSS();
		$files = Config::get('css');

		foreach($files as $file)
		{
			$minifier->add($public . $file);
		}

		$minifier->minify($dest . '/css/' . Config::get('timestamp') . '.css');

		$js = new Folder($dest . '/js');
		$js->delete();
		$js->make();

		$minifier = new Minify\JS();
		$files = Config::get('js');

		foreach($files as $file)
		{
			$minifier->add($public . $file);
		}

		$minifier->minify($dest . '/js/'  . Config::get('timestamp') . '.js');
	}

}

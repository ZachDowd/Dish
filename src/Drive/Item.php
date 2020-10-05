<?php namespace Dish\Drive;

class Item {

	/**
	 * Item absolute path
	 *
	 * @var string
	 */

	protected $path;


	/**
	 * Construct
	 *
	 * @param	string	$parent_path_name	Named path to prepend.
	 * @return	void
	 */

	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Path
	 *
	 * Get or set the absolute path for the item. You can pass a second path
	 * as the parameter to return a relative path between the two.
	 *
	 * @param	string	$relative	If specified, the return value will be a relative path from this parameter.
	 * @return	string				An absolute or relative path.
	 */

	public function path($relative = null)
	{
		$path = $this->path;

		if(!is_null($relative))
		{
			$path = str_replace($relative, '', $path);

			if($path[0] != DIRECTORY_SEPARATOR)
			{
				$path = DIRECTORY_SEPARATOR . $path;
			}
		}

		return $path;
	}

	/**
	 * Delete
	 *
	 * Delete the file or folder from the drive.
	 *
	 * @return	void
	 */

	public function delete()
	{
		if($this->is_folder())
		{
			$this->deleteFolder($this->path());
		}
		else
		{
			unlink($this->path());
		}
	}

	/**
	 * Delete
	 *
	 * Delete the folder recursively
	 *
	 * @param	$dir	string
	 * @return	void
	 */

	public function deleteFolder($dir)
	{
		if(!file_exists($dir))
		{
			return true;
		}

		if(!is_dir($dir))
		{
			return unlink($dir);
		}

		foreach(scandir($dir) as $item)
		{
			if($item == '.' || $item == '..')
			{
		    	continue;
			}

			if(!$this->deleteFolder($dir . DIRECTORY_SEPARATOR . $item))
			{
		    	return false;
			}
		}

		return rmdir($dir);
	}

	/**
	 * Exists
	 *
	 * Returns true if the file or folder exists on the drive.
	 *
	 * @return	bool
	 */

	public function exists()
	{
		return $this->is_folder() or $this->is_file();
	}

	/**
	 * Extension
	 *
	 * Returns the string following the last dot in the filename.
	 *
	 * @example new File('hello.jpg')->extension(); // "jpg"
	 * @example new File('private.tar.gz')->extension(); // "gz"
	 * @example new File('.gitignore')->extension(); // "gitignore"
	 * @return string
	 */

	public function extension()
	{
		return pathinfo($this->path(), PATHINFO_EXTENSION);
	}

	/**
	 * Is file?
	 *
	 * @return	bool
	 */

	public function is_file()
	{
		return is_file($this->path());
	}

	/**
	 * Is directory?
	 *
	 * @return	bool
	 */

	public function is_folder()
	{
		return is_dir($this->path());
	}

	/**
	 * Label
	 *
	 * Get the filename without the extension.
	 *
	 * @example	// "/images/hello.jpg" -> "hello"
	 * @return string
	 */

	public function label()
	{
		return pathinfo($this->path(), PATHINFO_FILENAME);
	}

	/**
	 * Name
	 *
	 * @return	string	Returns the filename, e.g. "hello.jpg"
	 */

	public function name()
	{
		return pathinfo($this->path(), PATHINFO_BASENAME);
	}
}

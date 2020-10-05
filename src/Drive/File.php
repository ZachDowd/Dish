<?php namespace Dish\Drive;

class File extends Item {

	/**
	 * Contents
	 *
	 * Get or set the contents of the file using <code>file_get_contents</code> and <code>file_put_contents</code>.
	 *
	 * @param	string	$value	Pass the contents to be written to the file.
	 * @return	string	Returns the contents of the file, or false if the content could not be read.
	 */

	public function contents($value = null)
	{
		if(!is_null($value))
		{
			file_put_contents($this->path(), $value);
		}

		return @file_get_contents($this->path());
	}

	/**
	 * Make
	 *
	 * Creates an empty file.
	 *
	 * @return	bool	Returns true if the file exists (or already existed) or false if there was an issue.
	 */

	public function make()
	{
		if(!$this->exists())
		{
			file_put_contents($this->path(), '');
		}

		return $this->exists();
	}

	/**
	 * URL
	 *
	 * Returns an absolute URL that is publicly accessible.
	 * This method strips away the paths for <code>storage</code>, <code>cache</code>, and <code>public</code>.
	 * If the end result matches the original path string then <code>null</code> is returned to avoid exposes
	 * the absolute path of the files on the server.
	 *
	 * @example	// ".../site.com/storage/files/hello.jpg" -> "/files/hello.jpg"
	 * @return	string	Absolute URL that is publicly accessible.
	 */

	public function url()
	{
		$pathnames = ['storage', 'cache', 'public'];

		$return = $this->path();

		foreach($pathnames as $pathname)
		{
			$return = str_replace(static::$manager->path($pathname), '', $return);
		}

		if($return[0] != DIRECTORY_SEPARATOR)
		{
			$return = DIRECTORY_SEPARATOR . $return;
		}

		if($this->path() == $return)
		{
			return null;
		}
		else
		{
			return $return;
		}
	}

}

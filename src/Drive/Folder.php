<?php namespace Dish\Drive;

class Folder extends Item {

	/**
	 * fldr
	 *
	 * Get a \Drive\Folder object.
	 *
	 * @param	string	$value	The name of the folder.
	 * @return	\Drive\Folder
	 */

	public function fldr($value)
	{
		return new Folder($this->path() . $value);
	}

	/**
	 * File
	 *
	 * Get a \Drive\File object.
	 *
	 * @param	string	$value	The name of the file.
	 * @return	\Drive\File
	 */

	public function file($value)
	{
		return new File($this->path() . '/' . $value);
	}

	/**
	 * Glob
	 *
	 * Find all files or folders in a folder.
	 *
	 * @param	string	$type The filetype to filter by.
	 * @return	array	An array of \Drive\Item objects.
	 */

	private function glob($type = null)
	{
		$glob = glob(preg_replace('/\/$/', '',$this->path()) . '/*');

		if ($glob === false) return [];

		$results = [];

		foreach($glob as $item)
		{
			if(is_null($type) || filetype($item) == $type)
			{
				if(filetype($item) == 'file')
				{
					$results[] = new File($item);
				}
				else
				{
					$results[] = new Folder($item);
				}
			}
		}

		return $results;
	}

	/**
	 * Folders
	 *
	 * Get an array of all folders in the folder using <code>$folder->glob('files')</code>.
	 *
	 * @return	array	An array of all folders in the folder.
	 */

	public function files()
	{
		return $this->glob('file');
	}

	/**
	 * Files
	 *
	 * Get an array of all files in the folder using <code>$folder->glob('dir')</code>.
	 *
	 * @return	array	An array of all files in the folder.
	 */

	public function folders()
	{
		return $this->glob('dir');
	}

	/**
	 * All
	 *
	 * Get an array of all files and folders in the folder using <code>$folder->glob()</code>.
	 *
	 * @return	array	An array of all files and folders.
	 */

	public function all()
	{
		return $this->glob();
	}

	/**
	 * Make
	 *
	 * Creates an empty folder.
	 *
	 * @param	bool	$recursive	Create all missing folders within the path.
	 * @return	bool	Returns true if the folder exists (or already existed) or false if there was an issue.
	 */

	public function make($recursive = false)
	{
		if(!$this->exists())
		{
			return mkdir($this->path(), 0755, $recursive);
		}

		return true;
	}

	/**
	 * Make
	 *
	 * Copy a folder recursively
	 *
	 * @param	string	$dst
	 * @param	string	$src
	 * @return	void
	 */

	function copy($dst, $src = null)
	{
		if(is_null($src))
		{
			$src = $this->path();
		}

	    $dir = opendir($src);
	    @mkdir($dst);

	    while(false !== ( $file = readdir($dir)) )
		{
	        if(($file != '.') && ($file != '..'))
			{
	            if(is_dir($src . '/' . $file))
				{
	                $this->copy($dst . '/' . $file, $src . '/' . $file);
	            }
	            else
				{
	                copy($src . '/' . $file,$dst . '/' . $file);
	            }
	        }
	    }

	    closedir($dir);
	}
}

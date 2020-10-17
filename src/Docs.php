<?php namespace Dish;

use Drive\File;
use Drive\Folder;

class Docs {

	/**
	 * Evaluate
	 *
	 * Evaluate a components folder and return documentation variables
	 *
	 * @param	$parentFolder	Drive\Folder	The folder to search within
	 * @return	array
	 */

	public function evaluate($parentFolder, $tag = [])
	{
		$data = [];

		$files = $parentFolder->files();
		$folders = $parentFolder->folders();

		foreach($files as $file)
		{
			$fileTag = $this->makeTag($file, $tag);
			$data[implode(':', $fileTag)] = $this->evaluateFile($file, $fileTag);
		}

		foreach($folders as $folder)
		{
			$folderTag = $tag;
			$folderTag[] = $folder->label();
			$folderFiles = $this->evaluate($folder, $folderTag);

			foreach($folderFiles as $fileTag => $file)
			{
				$data[$fileTag] = $file;
			}
		}

		return $data;
	}

	/**
	 * evaluateFile
	 *
	 * Evaluate a component
	 *
	 * @param	$file	Drive\File		The file to evaluate
	 * @param	$tag	array
	 * @return	array
	 */

	public function evaluateFile($file, $tag)
	{
		$vars = $this->findProps($file);
		$props = $vars['props'];
		$globals = $vars['globals'];

		return [
			'props'			=> $props,
			'globals'		=> $globals,
			'example'		=> $this->makeExample($file, $props, $globals, $tag),
		];
	}

	/**
	 * findProps
	 *
	 * Evaluate a component
	 *
	 * @param	$file	Drive\File		The file to evaluate
	 * @return	array
	 */

	public function findProps($file)
	{
		$html = $file->contents();
			
		preg_match_all('/\$[a-zA-Z]{1}[a-zA-Z0-9\_]+/', $html, $matches);

		$vars = [];
		$globals = [];

		foreach($matches[0] as $match)
		{
			$name = preg_replace('/^\$/', '', $match);

			$doc = Config::get('docs.' . $name);
			$global = Config::get('globals.' . $name);

			if($global)
			{
				$globals[$match] = $name;
			}
			else if($doc)
			{
				$vars[$match] = [];

				foreach($doc as $key => $value)
				{
					$vars[$match][$key] = $value;
				}

				$vars[$match]['name'] = $name;
				$vars[$match]['attrs'] = $this->makePropAttrs($doc);
			}
			else
			{
				$vars[$match] = [
					"name"	=> $name
				];
			}
		}

		return [
			'props'		=> $vars,	
			'globals'	=> $globals,	
		];
	}

	/**
	 * makeExample
	 *
	 * Evaluate a component
	 *
	 * @param	$file		Drive\File		The file to evaluate
	 * @param	$props		array
	 * @param	$globals	array
	 * @param	$tag		array
	 * @return	array
	 */

	public function makeExample($file, $props, $globals, $tag)
	{
		$lines = [];

		$attrs = [''];
		$sets = [];

		foreach($props as $key => $options)
		{
			$name = preg_replace('/^\$/', '', $key);

			if($options['propPreference'] == 'set')
			{
				$sets[$name] = $options['placeholder'];
			}
			else if($options['propPreference'] != 'ignore')
			{
				$attrs[] = $name . '="' . $options['placeholder'] . '"';
			}
		}

		$lines[] = '<' . implode(':', $tag) . implode(' ', $attrs) .'>';

		if(count($sets) > 0)
		{
			foreach($sets as $key => $value)
			{
				$lines[] = '	<Set name="' . $key . '" value="' . $value . '" />';
			}
		}

		if(isset($props['$html']))
		{
			$lines[] = '	' . $props['$html']['placeholder'];
		}

		if(count($lines) == 1)
		{
			// Close single-line tags
			$lines[0] = preg_replace('/\>$/', ' />', $lines[0]);
		}
		else
		{
			// Add a closing tag
			$lines[] = '</' . implode(':', $tag) .'>';
		}

		return implode("\n", $lines);
	}

	/**
	 * makeTag
	 *
	 * Make a tag
	 *
	 * @param	$file	Drive\File		The file to add to the tag
	 * @param	$tag	array			Current tag array
	 * @return	array
	 */

	public function makeTag($file, $tag)
	{
		if(count($tag) == 1 && $tag[0] == $file->label())
		{
			// Do nothing, it's a main tag
		}
		else
		{
			$tag[] = $file->label();
		}

		return $tag;
	}

	/**
	 * makePropAttrs
	 *
	 * Make a attributes for the doc prop tag
	 *
	 * @param	$doc	array
	 * @return	string
	 */

	public function makePropAttrs($doc)
	{
		$attrs = [];
		
		foreach($doc as $key => $value)
		{
			if(is_bool($value))
			{
				$val = $value ? 'true' : 'false';
			}
			else if(is_array($value))
			{
				$val = implode(', ', $value);
			}
			else if(is_string($value))
			{
				$val = $value;
			}

			if($val)
			{
				$attrs[] = $key . '="' . $val . '"';
			}
		}

		return implode(' ', $attrs);
	}

}